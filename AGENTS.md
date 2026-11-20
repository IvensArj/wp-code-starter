# Trabalhar no WP Code Starter

No repositório, o tema fica em `themes/wp-code-starter`. Se esta pasta já contiver `theme.json`, ela é a raiz do tema. Dentro do tema, procure `templates/` para modelos, `parts/` para regiões compartilhadas, `patterns/` para seções reutilizáveis e `assets/` para estilos e scripts.

Use blocos nativos e preserve a edição de textos, links e mídias no Gutenberg. Mantenha os dados de Projetos no plugin e a apresentação no tema. Dados de exemplo e arquivos do site importado ficam na instalação local.

## Editabilidade exigida na entrega

Ao criar um site a partir de referência ou descrição, siga a arquitetura de blocos deste projeto e implemente a edição pelo painel de todo conteúdo apresentado. Preserve a liberdade visual do pedido usando os tokens, padrões, templates e controles necessários para essa composição.

- Coloque textos, imagens, vídeos, links, rótulos de botões e itens repetidos em blocos ou registros com interface de edição no WordPress. Uma alteração de conteúdo deve poder ser feita pelo painel.
- Ofereça controles para opções de composição que o projeto apresentar como ajustáveis. Reaproveite Estilos, dimensões, layout e `metadata.wpcsVisual` quando atenderem à necessidade.
- Para um componente que exija código próprio, implemente seu bloco com controles e prévia no Gutenberg, persistência dos dados e renderização pública correspondente. Garanta edição por item para listas, slides e similares quando fizerem parte do pedido.
- Nomeie grupos e seções na visualização em lista. Use bloqueios estruturais apenas onde preservarem os controles de conteúdo e a edição esperada pelo usuário.
- Verifique cada seção nova ou alterada pelo painel. Troque um texto, uma mídia e um destino de link quando existirem, salve, reabra e confira o site publicado. Teste também o controle personalizado que a alteração introduzir. Restaure o conteúdo de teste.
- Ao entregar, informe onde editar as seções e os elementos globais. Identifique qualquer limitação e qualquer recurso ainda sem teste. Considere um recurso sem interface de edição solicitada como trabalho pendente.

No Local WP, páginas, navegação e personalizações também ficam no banco. Localize a instalação acima da pasta do tema e use o painel ou WP-CLI no Site shell para aplicar o conteúdo autorizado. Uma alteração em um arquivo de padrão oferece novas inserções. Para uma página existente, atualize os blocos dessa página preservando seu conteúdo e suas mídias. No repositório, `demo/` fornece os exemplos que serão criados em uma instalação nova.

## Escolha a skill pela tarefa

- [wpcs-design](.agents/skills/wpcs-design/SKILL.md) para criar ou redesenhar páginas e padrões.
- [wpcs-revisao-visual](.agents/skills/wpcs-revisao-visual/SKILL.md) para revisar ou corrigir defeitos visuais e aparência genérica.
- [wpcs-codigo](.agents/skills/wpcs-codigo/SKILL.md) para implementar ou corrigir código, controles do editor ou integração com Projetos.

Leia o `SKILL.md` correspondente quando a tarefa pedir. Comece pelos arquivos ligados à mudança e deixe indicado o que ficou sem verificação.
