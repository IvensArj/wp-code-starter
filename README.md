# WP Code Starter

Tema de blocos para criar sites WordPress com código ou com ajuda de IA. Você define a estrutura e os estilos no repositório. Textos, imagens, menus e publicações continuam editáveis no Gutenberg.

## Comece pelo Local WP

1. [Baixe o ZIP pronto para importar](https://github.com/IvensArj/wp-code-starter/releases/latest/download/wp-code-starter-local.zip).
2. Abra o [Local WP](https://localwp.com/) e arraste o ZIP para a janela do aplicativo. Você também pode escolher **Import site** no menu.
3. Dê um nome ao site e conclua a importação. Use PHP 8.3 ou superior no ambiente do site.
4. Clique em **Open site** para ver o resultado. Para editar, ative **One-click admin**, selecione **admin** e clique em **WP Admin**.

O ZIP já traz o tema ativo, as páginas Início, Sobre e Blog, as imagens e uma Galeria com dois projetos fictícios do Estúdio Linha de Cedro. Abra **Páginas → Início → Editar** para começar pelo Gutenberg. Para personalizar pelo código, abra a pasta do site pelo Local e procure o tema em `app/public/wp-content/themes/wp-code-starter`.

Se quiser conhecer o editor antes de importar, abra a [demo do site](https://playground.wordpress.net/?blueprint-url=https%3A%2F%2Fraw.githubusercontent.com%2FIvensArj%2Fwp-code-starter%2Fdemo%2Fplayground-base.zip) ou a [demo da Galeria](https://playground.wordpress.net/?blueprint-url=https%3A%2F%2Fraw.githubusercontent.com%2FIvensArj%2Fwp-code-starter%2Fdemo%2Fplayground-portfolio.zip) no navegador.


## Desenvolva pelo repositório

1. No GitHub, clique em **Use this template → Create a new repository** e clone o repositório criado.
2. Instale Node.js 24.18 e npm 11.16 ou superiores e inicie o Docker com Compose v2.
3. No diretório do projeto, execute:

```sh
npm ci
npm run start
npm run demo
```

Abra [o site local](http://localhost:8888) ou [o painel](http://localhost:8888/wp-admin/). O `wp-env` cria a conta local `admin` com senha `password`. Use essa conta apenas no ambiente local. O WordPress do ambiente de referência é 7.1.4 com PHP 8.3.

`npm run demo:portfolio` ativa o plugin opcional e acrescenta a Galeria e os projetos fictícios. Os comandos de demo reconhecem registros já carregados, preservam edições nesses registros e sinalizam colisões de slugs com conteúdo seu. Use `npm run stop` para parar o ambiente.

### Instale em um WordPress que já existe

Baixe o tema e o plugin na [página de downloads](https://github.com/IvensArj/wp-code-starter/releases/latest). Em WordPress 7.1 ou superior com PHP 8.3 ou superior, use **Aparência → Temas → Adicionar → Enviar tema** e selecione `wp-code-starter.zip`. Ative o tema e monte as páginas a partir dos padrões no Gutenberg. Se quiser uma página inicial estática, selecione-a em **Configurações → Leitura**. O padrão **Lista de artigos** pode ser inserido numa página comum, mantendo a introdução editável.

Para testar Projetos, envie `wpcs-projects.zip` em **Plugins → Adicionar plugin → Enviar plugin**, ative-o, cadastre projetos com áreas e insira o padrão **Galeria de projetos** numa página. Use links permanentes com nomes de publicação para obter URLs como `/projetos/casa-do-patio/`.

## Escolha onde fazer cada mudança

| Para mudar | Onde |
| --- | --- |
| Cores, fontes, dimensões e escala de espaçamento | `themes/wp-code-starter/theme.json` |
| Estrutura de uma página, artigo, cabeçalho ou rodapé | `templates/` e `parts/` dentro do tema |
| Seções oferecidas pelo inseridor | `patterns/` dentro do tema |
| Aparência compartilhada entre editor e site | `assets/site.css` dentro do tema |
| Texto, imagem, menus e ajustes de um bloco | Gutenberg e Editor do Site |
| Ajustes reutilizáveis de grade, largura e recorte | Selecione o bloco e abra **Configurações → Composição visual** |

O painel **Onde editar** no editor de página ou artigo abre a visualização em lista e, para administradores com acesso ao Editor do Site, oferece atalhos para modificar o layout compartilhado. O cabeçalho tem um controle **Fixar cabeçalho ao rolar** no bloco principal da parte de template.

Quando você salva um modelo ou parte no Editor do Site, essa personalização fica no banco e tem prioridade sobre o arquivo do tema. Se a mudança fizer parte da próxima versão, copie a marcação de blocos para o arquivo correspondente, preserve uma cópia do ajuste e use o comando de limpar personalizações do modelo para voltar a acompanhar o arquivo. Estilos globais também podem ter personalizações locais.

### Faça sua primeira página Serviços

1. Abra **Páginas → Adicionar página**, dê o título **Serviços** e use o modelo padrão, que já exibe o H1.
2. Insira o padrão **Abertura de página** e troque o título da seção e a introdução. Insira **Grade ajustável** e edite os dois serviços. Use **Chamada com botão** para encaminhar o visitante.
3. Selecione o grupo da grade na visualização em lista, abra **Composição visual** e escolha duas colunas no desktop e uma no celular.
4. Salve como rascunho, confira as duas larguras e reabra a edição. Publique e adicione a página ao menu quando estiver pronta.

Com IA, experimente este pedido no seu clone:

> Leia o AGENTS.md. Crie um padrão reutilizável de Serviços com duas ofertas e uma chamada para contato. Use blocos nativos, os tokens do tema e a grade ajustável. Mantenha títulos, textos e links editáveis. Mostre como inserir o padrão numa página e testar no celular.

## Teste e publique seu site

- Execute `npx playwright install chromium` uma vez e `npm test` com o ambiente local e o conteúdo de exemplo prontos. Use uma instância de desenvolvimento dedicada para os testes de edição. Para incluir a Galeria, carregue o exemplo de portfólio e defina a variável de ambiente WPCS_TEST_PORTFOLIO=1.
- Rode `npm run package` para gerar em `dist/` os ZIPs do tema, do plugin e das demos do Playground.
- Instale as versões do tema e do plugin na hospedagem. Para publicar páginas, menus, mídias e ajustes que você já fez no painel, migre o banco e a biblioteca pelo fluxo da hospedagem. Valide URLs, formulários que você tiver adicionado e o conteúdo publicado após a migração.

Código sob [GPL-2.0-or-later](LICENSE). Créditos do conteúdo de demonstração em [demo/CREDITOS.md](demo/CREDITOS.md).
