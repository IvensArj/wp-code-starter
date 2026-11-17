<?php defined( 'ABSPATH' ) || exit; ?>
<!-- wp:wpcs/area-filters {"align":"wide"} /-->
<!-- wp:query {"queryId":81,"align":"wide","query":{"perPage":6,"postType":"wpcs_project","order":"desc","orderBy":"date","inherit":false,"wpcsFilterByArea":true}} -->
<div class="wp-block-query alignwide">
<!-- wp:post-template {"metadata":{"wpcsVisual":{"grid":true,"columns":2,"mobile":1,"gap":32}}} -->
<!-- wp:group {"className":"wpcs-card","layout":{"type":"default"}} -->
<div class="wp-block-group wpcs-card">
<!-- wp:post-featured-image {"isLink":true,"aspectRatio":"4/3"} /-->
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
<p><?php esc_html_e( 'Nenhum projeto encontrado.', 'wpcs-projects' ); ?></p>
<!-- /wp:paragraph -->
<!-- /wp:query-no-results -->
</div>
<!-- /wp:query -->
