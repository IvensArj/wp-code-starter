<?php
/**
 * Title: Grade ajustável
 * Slug: wp-code-starter/grid
 * Categories: wpcs-starter
 * Description: Dois itens com controles de colunas, largura e espaçamento.
 */
?>
<!-- wp:group {"align":"wide","className":"wpcs-grid","metadata":{"wpcsVisual":{"grid":true,"columns":2,"mobile":1,"gap":36}},"layout":{"type":"default"}} -->
<div class="wp-block-group alignwide wpcs-grid">
<!-- wp:group {"className":"wpcs-card","layout":{"type":"default"}} -->
<div class="wp-block-group wpcs-card">
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e( 'Primeiro serviço', 'wp-code-starter' ); ?></h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php esc_html_e( 'Descreva o que a pessoa recebe.', 'wp-code-starter' ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
<!-- wp:group {"className":"wpcs-card","layout":{"type":"default"}} -->
<div class="wp-block-group wpcs-card">
<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading"><?php esc_html_e( 'Segundo serviço', 'wp-code-starter' ); ?></h3>
<!-- /wp:heading -->
<!-- wp:paragraph -->
<p><?php esc_html_e( 'Explique para quem este serviço faz sentido.', 'wp-code-starter' ); ?></p>
<!-- /wp:paragraph -->
</div>
<!-- /wp:group -->
</div>
<!-- /wp:group -->
