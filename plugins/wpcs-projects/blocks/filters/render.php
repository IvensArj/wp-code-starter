<?php
/**
 * Output project taxonomy links. The query string survives native pagination.
 *
 * @package wpcs-projects
 */

defined( 'ABSPATH' ) || exit;

$terms = get_terms( array( 'taxonomy' => 'wpcs_project_area', 'hide_empty' => true ) );
if ( is_wp_error( $terms ) || ! $terms ) {
	return;
}

$active  = wpcs_projects_active_area();
$base    = get_permalink( $block->context['postId'] ?? get_queried_object_id() ) ?: home_url( '/' );
$wrapper = get_block_wrapper_attributes();
?>
<nav <?php echo $wrapper; // Generated and escaped by WordPress. ?> aria-label="<?php esc_attr_e( 'Filtrar projetos por área', 'wpcs-projects' ); ?>">
	<ul>
		<li><a href="<?php echo esc_url( $base ); ?>"<?php if ( ! $active ) { echo ' aria-current="true"'; } ?>><?php echo esc_html( $attributes['allLabel'] ?: __( 'Todas as áreas', 'wpcs-projects' ) ); ?></a></li>
		<?php foreach ( $terms as $term ) : ?>
			<li><a href="<?php echo esc_url( add_query_arg( 'wpcs_area', $term->slug, $base ) ); ?>"<?php if ( $active && $term->term_id === $active->term_id ) { echo ' aria-current="true"'; } ?>><?php echo esc_html( $term->name ); ?></a></li>
		<?php endforeach; ?>
	</ul>
</nav>
