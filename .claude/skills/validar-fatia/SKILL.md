---
name: validar-fatia
description: Protocolo de portão de qualidade entre fatias de um projeto Laravel/TALL. Use ao concluir uma fatia/etapa de implementação, antes de commitar ou de começar a próxima fatia, e quando o usuário disser "fatia pronta", "valida a fatia", "posso seguir?", "revisa antes do commit". Monta o dossiê, aciona o agente validador-fatia em contexto isolado, lê o veredito e conduz o loop de correção com limite de ciclos.
---

# Portão de qualidade entre fatias

Esta skill **orquestra**. Quem julga é o subagente `validador-fatia`, em contexto isolado e sem permissão de escrita. Nunca julgue a fatia você mesmo nesta sessão: você escreveu o código e vai inflar a nota.

## 1. Monte o dossiê

Antes de chamar o validador, reúna:

- **Fatia**: número e nome.
- **Critério de aceite**: os itens verificáveis que esta fatia prometeu. Busque em `docs/`, no plano de fatias do projeto, ou no que foi combinado na conversa. **Se não existir critério de aceite escrito, escreva-o agora e confirme com o usuário antes de validar** — validar sem aceite é medir a coisa errada com precisão.
- **Escopo**: `git diff --stat <base>...HEAD` para a ref onde a fatia começou; se não houver git, a lista de arquivos tocados.
- **Ciclo**: 1 na primeira vez, +1 a cada revalidação.
- **Achados anteriores**: cole o JSON do ciclo anterior, se houver.

## 2. Acione o validador

Chame a ferramenta Agent com `subagent_type: "validador-fatia"`, passando o dossiê completo no prompt. Rode em foreground (`run_in_background: false`) — o próximo passo depende do veredito.

O validador roda os portões (Pint, PHPStan, `php artisan test`) por conta própria. Não rode antes só para "adiantar": ele precisa medir o estado real.

## 3. Aja pelo veredito

Leia o bloco JSON. O campo `acao` manda:

**`seguir`** — mostre ao usuário veredito, nota e as ressalvas. Registre em `docs/validacao/fatia-XX.md` (crie a pasta se preciso). Se veio `APROVADO_COM_RESSALVAS`, acrescente cada ressalva a `docs/debito-tecnico.md` antes de seguir — ressalva não registrada é ressalva esquecida. Só então avance para a próxima fatia.

**`retomar_fatia`** — corrija **exatamente** os blockers e achados listados, na ordem dada. Regras da correção:
- Não redesenhe o que não foi apontado. Escopo da correção = lista do validador.
- Não "aproveite para" melhorar outra coisa: isso polui o diff e faz o próximo ciclo avaliar código novo em vez do corrigido.
- Se você **discorda** de um achado, não o implemente calado nem o ignore calado — responda ao usuário com o contra-argumento técnico e deixe ele decidir. Validador erra; validador que não pode ser contestado vira burocracia.
- Terminadas as correções, volte ao passo 1 com `ciclo + 1`, em **nova instância** do agente (contexto novo, sem a nota anterior na cabeça).

**`escalar_humano`** ou **ciclo 3 alcançado** — pare o loop. Apresente ao usuário: o que continua reprovando, o que já foi tentado em cada ciclo e as duas ou três saídas possíveis com o custo de cada uma. Três ciclos sem convergir quase nunca é falta de esforço: é critério de aceite ambíguo, decisão de arquitetura pendente ou requisito que mudou. Isso é decisão de humano, não de mais uma rodada.

**`INCONCLUSIVO`** — o validador pediu informação que faltou. Complete o dossiê e repita.

## 4. Limites do loop

- **Máximo 2 ciclos de correção automática** (validação inicial + até 2 retomadas). Depois, escale.
- Nunca altere as faixas de aprovação para fazer a fatia passar. Se a faixa está errada para o contexto, mude no arquivo do agente, de forma explícita e conversada — não no meio de um julgamento.
- Nunca peça ao validador para "considerar que isso é aceitável". Se é aceitável, vira débito técnico registrado; se não é, corrige.

## 5. Registro

Cada validação vira uma entrada em `docs/validacao/fatia-XX.md`: data, ciclo, veredito, nota, blockers e como foram resolvidos. É o histórico que mostra onde o projeto trava com frequência — e a base para ajustar os pesos da rubrica depois de algumas fatias.
