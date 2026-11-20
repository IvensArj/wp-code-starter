---
name: wpcs-codigo
description: Implementa ou corrige código do tema, controles Gutenberg e integração com Projetos do WP Code Starter usando contexto focado.
---

Leia o contrato de editabilidade em `AGENTS.md` antes de implementar componentes ou páginas.

## Trabalho com contexto focado

- Identifique a página, o bloco ou o sintoma. Busque pelo slug, classe ou hook e leia a entrada, o código que aplica a mudança e o teste correspondente. Amplie a busca quando houver mais de um caminho plausível.
- Para layout, verifique se um ajuste salvo no Editor do Site tem prioridade sobre o template em arquivo. Para controles, acompanhe `metadata.wpcsVisual` no JavaScript do Gutenberg e na renderização PHP sem perder os demais metadados do bloco.
- Reaproveite blocos e APIs do núcleo. Mantenha o cadastro de Projetos e suas áreas no plugin, com a apresentação no tema.
- Valide pelo caminho que o usuário usa. Para mudanças no editor, altere, salve, reabra e compare a página publicada. Faça primeiro o teste focado e amplie a cobertura se a mudança afetar comportamento compartilhado.

## Conteúdo e persistência

- Antes de alterar o layout, identifique a origem dos dados. Registre a alteração em blocos, metadados com controles ou entidades WordPress, conforme o modelo já usado pelo site.
- Para componentes novos, mantenha atributos, HTML salvo e renderização coerentes. Use sanitização e permissões adequadas ao salvar. Preserve valores existentes ao mudar controles.
- Em componentes repetidos, permita editar cada item. Imagens devem oferecer substituição e texto alternativo. Links devem oferecer edição do destino e do texto.
- Use as ferramentas disponíveis do WordPress local para atualizar páginas existentes. Confirme a instalação e o banco de destino antes de gravar. Preserve o conteúdo real em reinstalações e reexecuções de carga.
- Verifique a edição pelo Gutenberg e a persistência depois de reabrir. Um componente só termina quando sua interface de edição e sua renderização pública funcionarem juntas.
