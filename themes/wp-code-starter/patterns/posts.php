<?php
/**
 * Title: Lista de artigos
 * Slug: wp-code-starter/posts
 * Categories: wpcs-starter
 * Description: Consulta paginada de posts publicados.
 */
?>
<!-- wp:query {"queryId":41,"align":"wide","query":{"perPage":6,"postType":"post","order":"desc","orderBy":"date","inherit":false}} -->
<div class="wp-block-query alignwide">
<!-- wp:post-template {"metadata":{"wpcsVisual":{"grid":true,"columns":2,"mobile":1,"gap":36}}} -->
<!-- wp:group {"className":"wpcs-card","layout":{"type":"default"}} -->
<div class="wp-block-group wpcs-card">
<!-- wp:post-date {"format":"j F Y","fontSize":"small"} /-->
<!-- wp:post-title {"isLink":true,"fontSize":"section"} /-->
<!-- wp:post-excerpt {"excerptLength":28} /-->
</div>
<!-- /wp:group -->
<!-- /wp:post-template -->
<!-- wp:query-pagination {"layout":{"type":"flex","justifyContent":"space-between"}} -->
<!-- wp:query-pagination-previous /-->
<!-- wp:query-pagination-numbers /-->
<!-- wp:query-pagination-next /-->
<!-- /wp:query-pagination -->
<!-- wp:query-no-results -->
<!-- wp:paragraph -->
<p><?php esc_html_e( 'Ainda não há artigos publicados.', 'wp-code-starter' ); ?></p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results -->
</div>
<!-- /wp:query -->
