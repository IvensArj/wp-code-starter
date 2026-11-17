<?php
/**
 * Title: Chamada com botão
 * Slug: wp-code-starter/callout
 * Categories: wpcs-starter
 * Description: Seção curta para encaminhar o visitante.
 */
?>
<!-- wp:group {"align":"wide","backgroundColor":"linen","className":"wpcs-callout","layout":{"type":"constrained"}} -->
<div class="wp-block-group alignwide wpcs-callout has-linen-background-color has-background">
<!-- wp:heading -->
<h2 class="wp-block-heading"><?php esc_html_e( 'Próximo passo', 'wp-code-starter' ); ?></h2>
<!-- /wp:heading -->
<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button -->
<div class="wp-block-button"><a class="wp-block-button__link wp-element-button" href="<?php echo esc_url( home_url( '/' ) ); ?>"><?php esc_html_e( 'Voltar ao início', 'wp-code-starter' ); ?></a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons -->
</div>
<!-- /wp:group -->
