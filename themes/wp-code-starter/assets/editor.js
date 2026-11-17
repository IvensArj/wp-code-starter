(function (wp, settings) {
  'use strict';

  const { createElement: h, Fragment } = wp.element;
  const { InspectorControls } = wp.blockEditor;
  const { Button, FocalPointPicker, PanelBody, RangeControl, SelectControl, ToggleControl } = wp.components;
  const { __ } = wp.i18n;
  const domain = 'wp-code-starter';
  const supported = ['core/group', 'core/post-template', 'core/paragraph', 'core/image'];
  const options = (attrs) => attrs.metadata?.wpcsVisual || {};
  const isHeader = (name, attrs) => name === 'core/group' && (attrs.className || '').split(/\s+/).includes('wpcs-site-header');
  const canGrid = (name, attrs) => name === 'core/post-template' || (
    name === 'core/group' && !isHeader(name, attrs) && !['flex', 'grid'].includes(attrs.layout?.type)
  );
  const alignment = [
    { label: __('Preencher', domain), value: 'stretch' },
    { label: __('Início', domain), value: 'start' },
    { label: __('Centro', domain), value: 'center' },
    { label: __('Fim', domain), value: 'end' },
  ];

  function VisualControls({ name, attributes, setAttributes }) {
    const values = options(attributes);
    const set = (key, value) => {
      setAttributes({ metadata: { ...attributes.metadata, wpcsVisual: { ...values, [key]: value } } });
    };
    const toggle = (label, key, current = values[key] === true) => {
      return h(ToggleControl, { label, checked: current, onChange: (next) => set(key, next) });
    };
    const range = (label, key, fallback, min, max) => {
      return h(RangeControl, { label, value: values[key] ?? fallback, min, max, onChange: (next) => set(key, next) });
    };
    const select = (label, key, fallback, choices) => {
      return h(SelectControl, { label, value: values[key] || fallback, options: choices, onChange: (next) => set(key, next) });
    };
    const grid = canGrid(name, attributes);
    const header = isHeader(name, attributes);
    if (!grid && !header && name !== 'core/paragraph' && name !== 'core/image') return null;

    return h(InspectorControls, null, h(PanelBody, { title: __('Composição visual', domain), initialOpen: false },
      header && toggle(__('Fixar cabeçalho ao rolar', domain), 'sticky'),
      grid && h(Fragment, null,
        toggle(__('Grade ajustável', domain), 'grid'),
        values.grid && h(Fragment, null,
          range(__('Colunas no desktop', domain), 'columns', 2, 1, 6),
          range(__('Colunas no celular', domain), 'mobile', 1, 1, 3),
          (values.columns ?? 2) === 2 && range(__('Primeira coluna (%)', domain), 'first', 50, 25, 75),
          range(__('Espaçamento (px)', domain), 'gap', 24, 0, 100),
          select(__('Alinhamento vertical', domain), 'align', 'stretch', alignment),
        )
      ),
      name === 'core/paragraph' && h(Fragment, null,
        h(ToggleControl, {
          label: __('Definir largura do parágrafo', domain),
          checked: values.width != null,
          onChange: (next) => set('width', next ? 100 : null),
        }),
        values.width != null && h(Fragment, null,
          range(__('Largura (%)', domain), 'width', 100, 10, 100),
          select(__('Posição horizontal', domain), 'position', 'start', alignment.slice(1)),
        )
      ),
      name === 'core/image' && h(Fragment, null,
        toggle(__('Definir recorte responsivo', domain), 'crop'),
        values.crop && h(Fragment, null,
          range(__('Altura no desktop (px)', domain), 'desktopHeight', 420, 120, 800),
          range(__('Altura no celular (px)', domain), 'mobileHeight', 280, 120, 640),
          attributes.url && h(FocalPointPicker, {
            label: __('Ponto de foco', domain),
            url: attributes.url,
            value: { x: (values.focus?.x ?? 50) / 100, y: (values.focus?.y ?? 50) / 100 },
            onChange: ({ x, y }) => set('focus', { x: Math.round(x * 100), y: Math.round(y * 100) }),
          }),
          h(Button, {
            variant: 'secondary',
            onClick: () => setAttributes({ metadata: { ...attributes.metadata, wpcsVisual: {} } }),
          }, __('Restaurar recorte do tema', domain))
        )
      )
    ));
  }

  wp.hooks.addFilter('editor.BlockEdit', 'wpcs/visual-controls', (BlockEdit) => (props) => h(
    Fragment,
    null,
    h(BlockEdit, props),
    props.isSelected && supported.includes(props.name) && h(VisualControls, props)
  ));

  wp.hooks.addFilter('editor.BlockListBlock', 'wpcs/visual-preview', (Block) => (props) => {
    const attrs = props.attributes || {};
    const values = options(attrs);
    const style = {};
    const classes = [props.className];

    if (canGrid(props.name, attrs) && values.grid) {
      const columns = values.columns ?? 2;
      const first = values.first ?? 50;
      classes.push('wpcs-visual-grid');
      style['--wpcs-columns'] = columns === 2
        ? 'minmax(0,' + first + 'fr) minmax(0,' + (100 - first) + 'fr)'
        : 'repeat(' + columns + ',minmax(0,1fr))';
      style['--wpcs-mobile'] = values.mobile ?? 1;
      style['--wpcs-gap'] = (values.gap ?? 24) + 'px';
      style['--wpcs-align'] = values.align || 'stretch';
    }

    if (props.name === 'core/paragraph' && values.width != null) {
      classes.push('wpcs-visual-paragraph', 'wpcs-visual-' + (values.position || 'start'));
      style['--wpcs-width'] = values.width + '%';
    }

    if (props.name === 'core/image' && values.crop) {
      classes.push('wpcs-visual-crop');
      style['--wpcs-desktop-height'] = (values.desktopHeight ?? 420) + 'px';
      style['--wpcs-mobile-height'] = (values.mobileHeight ?? 280) + 'px';
      style['--wpcs-focus'] = (values.focus?.x ?? 50) + '% ' + (values.focus?.y ?? 50) + '%';
    }

    if (!Object.keys(style).length) return h(Block, props);

    return h(Block, {
      ...props,
      className: classes.filter(Boolean).join(' '),
      wrapperProps: { ...props.wrapperProps, style: { ...props.wrapperProps?.style, ...style } },
    });
  });

  function editorLink(path) {
    const url = new URL(settings.siteEditorUrl);
    url.searchParams.set('p', path);
    url.searchParams.set('canvas', 'edit');
    return url.toString();
  }

  function EditingGuide() {
    const post = wp.data.useSelect((select) => {
      const editor = select('core/editor');
      return { type: editor.getCurrentPostType(), template: editor.getEditedPostAttribute('template') };
    }, []);
    const { setIsListViewOpened } = wp.data.useDispatch('core/editor');
    const defaults = { page: 'page', post: 'single', wpcs_project: 'single-wpcs_project' };
    if (!defaults[post.type]) return null;

    const template = post.template && post.template !== 'default' ? post.template : defaults[post.type];
    const target = (settings.theme + '//' + template);
    const part = (slug) => '/wp_template_part/' + (settings.theme + '//' + slug);
    const link = (label, path) => h('p', { key: path }, h('a', { href: editorLink(path), target: '_blank', rel: 'noopener' }, label));

    return h(wp.editor.PluginDocumentSettingPanel, { name: 'edit-guide', title: __('Onde editar', domain) },
      h('p', null, __('Use a visualização em lista para selecionar blocos. Os estilos e a composição ficam na barra lateral do bloco.', domain)),
      h(Button, { variant: 'secondary', onClick: () => setIsListViewOpened(true) }, __('Abrir visualização em lista', domain)),
      settings.canEditSite && [
        link(__('Editar modelo desta página', domain), '/wp_template/' + target),
        link(__('Editar cabeçalho', domain), part('header')),
        link(__('Editar rodapé', domain), part('footer')),
        link(__('Editar estilos do site', domain), '/styles'),
      ]
    );
  }

  wp.plugins.registerPlugin('wpcs-edit-guide', { render: EditingGuide });
})(window.wp, window.wpcsEditor);
