import cytoscape from 'cytoscape';

/**
 * Grafo societário interativo ("grifo") — estilo Obsidian.
 * Componente Alpine que renderiza o grafo com Cytoscape: layout de força,
 * zoom/pan só na área do canvas, rótulo dentro das bolhas, cores por papel
 * (empresa × sócio) e por situação, adaptadas ao tema claro/escuro. Clicar num
 * nó recentra o grafo (chama a ação Livewire correspondente).
 */
// Espelha o conjunto negativo de App\Support\SituacaoCadastral (tudo exceto
// ATIVA). Ao adicionar um código negativo lá, atualize esta lista (cor vermelha).
const NEGATIVE = ['BAIXADA', 'INAPTA', 'SUSPENSA', 'NULA'];

function themeColors() {
    const s = getComputedStyle(document.documentElement);
    const v = (name, fallback) => (s.getPropertyValue(name).trim() || fallback);

    return {
        company: v('--graph-company', '#1d4ed8'),
        person: v('--graph-person', '#b45309'),
        negative: v('--graph-negative', '#dc2626'),
        label: v('--graph-label', '#ffffff'),
        ring: v('--graph-center-ring', '#f2c744'),
        edge: v('--graph-edge', '#cbd5e1'),
    };
}

function fillFor(ele, c) {
    if (ele.data('role') === 'person') {
        return c.person;
    }
    const situacao = ele.data('situacao');
    if (situacao && NEGATIVE.includes(situacao)) {
        return c.negative;
    }
    return c.company;
}

// Formata 14 dígitos como CNPJ (XX.XXX.XXX/XXXX-XX); devolve o original se não for.
function formatCnpj(value) {
    const d = String(value || '').replace(/\D/g, '');
    if (d.length !== 14) {
        return String(value || '');
    }

    return `${d.slice(0, 2)}.${d.slice(2, 5)}.${d.slice(5, 8)}/${d.slice(8, 12)}-${d.slice(12)}`;
}

// Escapa texto antes de ir para innerHTML do tooltip (nome/documento vêm da base).
function escapeHtml(value) {
    return String(value ?? '').replace(/[&<>"']/g, (ch) => ({
        '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;',
    }[ch]));
}

function styleSheet(c) {
    return [
        {
            selector: 'node',
            style: {
                'background-color': (ele) => fillFor(ele, c),
                label: 'data(label)',
                color: c.label,
                'text-valign': 'center',
                'text-halign': 'center',
                'text-wrap': 'wrap',
                'text-max-width': (ele) => (ele.data('isCenter') ? 96 : 72),
                'font-size': 10,
                'font-weight': 600,
                'line-height': 1.1,
                width: (ele) => (ele.data('isCenter') ? 112 : 84),
                height: (ele) => (ele.data('isCenter') ? 112 : 84),
                'border-width': (ele) => (ele.data('isCenter') ? 4 : 0),
                'border-color': c.ring,
                'min-zoomed-font-size': 5,
                'transition-property': 'background-color, border-width',
                'transition-duration': '150ms',
            },
        },
        {
            selector: 'edge',
            style: {
                width: 1.5,
                'line-color': c.edge,
                'curve-style': 'bezier',
                opacity: 0.85,
            },
        },
        {
            // Ligação provável (só CPF mascarado, nome divergente): tracejada e
            // mais apagada, sinalizando menor confiança (possível xará).
            selector: 'edge[?probable]',
            style: {
                'line-style': 'dashed',
                'line-dash-pattern': [6, 4],
                opacity: 0.45,
            },
        },
    ];
}

function layoutOptions() {
    // 'cose' é o layout de força embutido no Cytoscape (sem plugin extra).
    return {
        name: 'cose',
        animate: false,
        padding: 30,
        nodeRepulsion: 9000,
        idealEdgeLength: 130,
        nodeOverlap: 24,
        gravity: 0.6,
        componentSpacing: 120,
    };
}

export default function registerGrifo() {
    const define = (Alpine) => Alpine.data('grifo', (initial) => ({
            cy: null,
            observer: null,
            onResize: null,
            tip: null,
            expanded: false,

            init() {
                // $nextTick: só inicializa o Cytoscape quando o container já tem
                // tamanho (evita canvas em branco por medir 0×0 cedo demais).
                this.$nextTick(() => this.boot(initial));

                // O Cytoscape não reajusta sozinho ao redimensionar a janela.
                this.onResize = () => {
                    if (this.cy) {
                        this.cy.resize();
                        this.cy.fit(undefined, 40);
                    }
                };
                window.addEventListener('resize', this.onResize);

                // Re-aplica as cores quando o tema (data-theme) muda.
                this.observer = new MutationObserver(() => this.applyColors());
                this.observer.observe(document.documentElement, {
                    attributes: true,
                    attributeFilter: ['data-theme'],
                });

                // Limpa ao sair (navegação SPA do Livewire).
                this.$el.addEventListener('livewire:navigating', () => this.teardown(), { once: true });
            },

            boot(data) {
                this.destroy();

                this.cy = cytoscape({
                    container: this.$refs.canvas,
                    elements: this.elements(data),
                    style: styleSheet(themeColors()),
                    layout: layoutOptions(),
                    // Zoom por roda mais rápido/fluido (padrão do Cytoscape é 1;
                    // 0.25 ficava lento demais).
                    wheelSensitivity: 0.6,
                    minZoom: 0.2,
                    maxZoom: 3,
                });

                this.cy.on('tap', 'node', (evt) => this.onTap(evt.target.data()));

                // Tooltip ao passar o mouse: empresa → CNPJ; pessoa → nome/CPF.
                this.cy.on('mouseover', 'node', (evt) => this.showTip(evt.target));
                this.cy.on('mouseout', 'node', () => this.hideTip());
                // Some ao mexer no grafo (evita tooltip "grudado" fora do lugar).
                this.cy.on('pan zoom drag tapstart', () => this.hideTip());

                // Reajusta/enquadra assim que o primeiro render terminar.
                this.cy.ready(() => {
                    this.cy.resize();
                    this.cy.fit(undefined, 40);
                });
            },

            // Alterna entre altura padrão e expandida (mais espaço para muitas
            // bolhas). Após o layout mudar, o Cytoscape precisa de resize + fit.
            toggleExpand() {
                this.expanded = !this.expanded;
                this.$nextTick(() => {
                    if (this.cy) {
                        this.cy.resize();
                        this.cy.fit(undefined, 40);
                    }
                });
            },

            showTip(node) {
                const d = node.data();
                const lines = [];

                if (d.role === 'company') {
                    if (d.title) {
                        lines.push(`<strong>${escapeHtml(d.title)}</strong>`);
                    }
                    if (d.cnpj) {
                        lines.push(escapeHtml(formatCnpj(d.cnpj)));
                    }
                } else {
                    if (d.title) {
                        lines.push(`<strong>${escapeHtml(d.title)}</strong>`);
                    }
                    if (d.doc) {
                        lines.push(`CPF ${escapeHtml(d.doc)}`);
                    }
                }

                if (lines.length === 0) {
                    return;
                }

                const el = this.tipEl();
                el.innerHTML = lines.join('<br>');
                el.style.display = 'block';

                // Ancorar acima da bolha (renderedPosition = px relativos ao canvas).
                const pos = node.renderedPosition();
                const r = (node.renderedHeight ? node.renderedHeight() : 84) / 2;
                el.style.left = `${pos.x}px`;
                el.style.top = `${pos.y - r - 8}px`;
            },

            hideTip() {
                if (this.tip) {
                    this.tip.style.display = 'none';
                }
            },

            // Cria (uma vez) o elemento de tooltip dentro do wrapper posicionado.
            tipEl() {
                if (this.tip) {
                    return this.tip;
                }

                const el = document.createElement('div');
                el.className = 'grifo-tip';
                el.style.cssText = [
                    'position:absolute', 'z-index:20', 'display:none',
                    'transform:translate(-50%,-100%)', 'pointer-events:none',
                    'max-width:260px', 'padding:6px 9px', 'border-radius:8px',
                    'font-size:12px', 'line-height:1.35', 'white-space:normal',
                    'background:var(--ink,#171712)', 'color:var(--surface,#fff)',
                    'box-shadow:0 4px 14px rgba(0,0,0,.22)',
                ].join(';');
                this.$refs.canvas.parentElement.appendChild(el);
                this.tip = el;

                return el;
            },

            refresh(data) {
                if (!this.cy) {
                    this.boot(data);
                    return;
                }

                this.cy.elements().remove();
                this.cy.add(this.elements(data));
                this.cy.layout(layoutOptions()).run();
                this.cy.fit(undefined, 40);
            },

            elements(data) {
                const nodes = (data.nodes || []).map((n) => ({ data: n.data }));
                const edges = (data.edges || []).map((e) => ({ data: e.data }));
                return [...nodes, ...edges];
            },

            onTap(d) {
                if (d.isCenter) {
                    return;
                }
                if (d.role === 'company' && d.cnpj) {
                    this.$wire.focusOn(d.cnpj);
                } else if (d.role === 'person' && d.doc) {
                    this.$wire.focusPerson(d.doc, d.name || '');
                }
            },

            applyColors() {
                if (this.cy) {
                    this.cy.style(styleSheet(themeColors())).update();
                }
            },

            zoomIn() {
                if (this.cy) {
                    this.cy.zoom({ level: this.cy.zoom() * 1.25, renderedPosition: this.center() });
                }
            },

            zoomOut() {
                if (this.cy) {
                    this.cy.zoom({ level: this.cy.zoom() / 1.25, renderedPosition: this.center() });
                }
            },

            fit() {
                if (this.cy) {
                    this.cy.fit(undefined, 40);
                }
            },

            center() {
                const box = this.$refs.canvas.getBoundingClientRect();
                return { x: box.width / 2, y: box.height / 2 };
            },

            destroy() {
                if (this.cy) {
                    this.cy.destroy();
                    this.cy = null;
                }
            },

            teardown() {
                if (this.onResize) {
                    window.removeEventListener('resize', this.onResize);
                }
                if (this.observer) {
                    this.observer.disconnect();
                }
                if (this.tip) {
                    this.tip.remove();
                    this.tip = null;
                }
                this.destroy();
            },
        }));

    // Registra já se o Alpine (via Livewire) iniciou; senão, no evento de init.
    if (window.Alpine) {
        define(window.Alpine);
    } else {
        document.addEventListener('alpine:init', () => define(window.Alpine));
    }
}
