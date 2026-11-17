<?php
/**
 * Plugin Name: WP Code Starter · Projetos
 * Description: Exemplo de cadastro de projetos com áreas e galeria filtrável.
 * Version: 1.0.0
 * Requires at least: 7.1
 * Requires PHP: 8.3
 * License: GPL-2.0-or-later
 * Text Domain: wpcs-projects
 */

defined( 'ABSPATH' ) || exit;

function wpcs_projects_register(): void {
	register_post_type(
		'wpcs_project',
		array(
			'labels' => array(
				'name'          => __( 'Projetos', 'wpcs-projects' ),
				'singular_name' => __( 'Projeto', 'wpcs-projects' ),
				'add_new_item'  => __( 'Adicionar projeto', 'wpcs-projects' ),
				'edit_item'     => __( 'Editar projeto', 'wpcs-projects' ),
			),
			'public'       => true,
			'show_in_rest' => true,
			'rewrite'      => array( 'slug' => 'projetos', 'with_front' => false ),
			'menu_icon'    => 'dashicons-portfolio',
			'supports'     => array( 'title', 'editor', 'thumbnail', 'excerpt', 'revisions' ),
		)
	);
	register_taxonomy(
		'wpcs_project_area',
		'wpcs_project',
		array(
			'labels' => array(
				'name'          => __( 'Áreas de projeto', 'wpcs-projects' ),
				'singular_name' => __( 'Área de projeto', 'wpcs-projects' ),
			),
			'public'       => true,
			'show_in_rest' => true,
			'hierarchical' => true,
			'rewrite'      => array( 'slug' => 'area-de-projeto', 'with_front' => false ),
		)
	);
}
add_action( 'init', 'wpcs_projects_register' );

register_activation_hook( __FILE__, static function (): void {
	wpcs_projects_register();
	flush_rewrite_rules();
} );

register_deactivation_hook( __FILE__, static function (): void {
	unregister_taxonomy( 'wpcs_project_area' );
	unregister_post_type( 'wpcs_project' );
	flush_rewrite_rules();
} );

function wpcs_projects_blocks(): void {
	wp_register_script(
		'wpcs-project-filters-editor',
		plugins_url( 'assets/editor.js', __FILE__ ),
		array( 'wp-block-editor', 'wp-blocks', 'wp-components', 'wp-element', 'wp-i18n', 'wp-server-side-render' ),
		'1.0.0',
		true
	);
	wp_set_script_translations( 'wpcs-project-filters-editor', 'wpcs-projects' );
	register_block_type( __DIR__ . '/blocks/filters' );
}
add_action( 'init', 'wpcs_projects_blocks' );

function wpcs_projects_active_area(): ?WP_Term {
	$slug = $_GET['wpcs_area'] ?? '';
	if ( ! is_string( $slug ) || '' === $slug ) {
		return null;
	}
	$term = get_term_by( 'slug', sanitize_title( wp_unslash( $slug ) ), 'wpcs_project_area' );
	return $term instanceof WP_Term ? $term : null;
}

function wpcs_projects_filter_query( array $query_vars, WP_Block $block ): array {
	$query = $block->context['query'] ?? array();
	if ( 'wpcs_project' !== ( $query['postType'] ?? '' ) || empty( $query['wpcsFilterByArea'] ) ) {
		return $query_vars;
	}
	$area = wpcs_projects_active_area();
	if ( $area ) {
		$tax_query = $query_vars['tax_query'] ?? array();
		$query_vars['tax_query'] = array(
			'relation' => 'AND',
			array(
				'taxonomy' => 'wpcs_project_area',
				'field'    => 'term_id',
				'terms'    => array( $area->term_id ),
			),
		);
		if ( $tax_query ) {
			$query_vars['tax_query'][] = $tax_query;
		}
	}
	return $query_vars;
}
add_filter( 'query_loop_block_query_vars', 'wpcs_projects_filter_query', 10, 2 );

function wpcs_projects_patterns(): void {
	ob_start();
	require __DIR__ . '/patterns/gallery.php';
	$markup = ob_get_clean();
	register_block_pattern(
		'wpcs-projects/gallery',
		array(
			'title'       => __( 'Galeria de projetos', 'wpcs-projects' ),
			'description' => __( 'Filtros por área e lista de projetos publicados.', 'wpcs-projects' ),
			'categories'  => array( 'posts' ),
			'content'     => $markup,
		)
	);
}
add_action( 'init', 'wpcs_projects_patterns', 15 );
