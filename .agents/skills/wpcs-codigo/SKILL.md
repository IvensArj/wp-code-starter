---
name: wpcs-codigo
description: Implementa ou corrige código do tema, controles Gutenberg e integração com Projetos do WP Code Starter usando contexto focado.
---

## Trabalho com contexto focado

- Identifique a página, o bloco ou o sintoma. Busque pelo slug, classe ou hook e leia a entrada, o código que aplica a mudança e o teste correspondente. Amplie a busca quando houver mais de um caminho plausível.
- Para layout, verifique se um ajuste salvo no Editor do Site tem prioridade sobre o template em arquivo. Para controles, acompanhe `metadata.wpcsVisual` no JavaScript do Gutenberg e na renderização PHP sem perder os demais metadados do bloco.
- Reaproveite blocos e APIs do núcleo. Mantenha o cadastro de Projetos e suas áreas no plugin, com a apresentação no tema.
- Valide pelo caminho que o usuário usa. Para mudanças no editor, altere, salve, reabra e compare a página publicada. Faça primeiro o teste focado e amplie a cobertura se a mudança afetar comportamento compartilhado.
