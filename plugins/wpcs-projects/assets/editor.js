(function (wp) {
  'use strict';

  const h = wp.element.createElement;
  const { __ } = wp.i18n;
  const { InspectorControls, useBlockProps } = wp.blockEditor;
  const { PanelBody, TextControl } = wp.components;
  const serverPreview = wp.serverSideRender.ServerSideRender || wp.serverSideRender;
  const domain = 'wpcs-projects';

  wp.blocks.registerBlockType('wpcs/area-filters', {
    edit({ attributes, setAttributes, context }) {
      return h('div', useBlockProps(),
        h(InspectorControls, null, h(PanelBody, { title: __('Filtros por área', domain) },
          h(TextControl, {
            label: __('Texto para todas as áreas', domain),
            value: attributes.allLabel,
            onChange: (value) => setAttributes({ allLabel: value }),
          })
        )),
        h(serverPreview, {
          block: 'wpcs/area-filters',
          attributes,
          urlQueryArgs: context.postId ? { post_id: context.postId } : {},
          EmptyResponsePlaceholder: () => h('p', null, __('Publique projetos com áreas para mostrar os filtros.', domain)),
        })
      );
    },
    save: () => null,
  });
})(window.wp);
