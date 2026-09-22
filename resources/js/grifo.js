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
                    wheelSensitivity: 0.25,
                    minZoom: 0.2,
                    maxZoom: 3,
                });

                this.cy.on('tap', 'node', (evt) => this.onTap(evt.target.data()));

                // Reajusta/enquadra assim que o primeiro render terminar.
                this.cy.ready(() => {
                    this.cy.resize();
                    this.cy.fit(undefined, 40);
                });
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
