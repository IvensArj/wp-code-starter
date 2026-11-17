<?php
/**
 * Theme assets and block editor integration.
 *
 * @package wp-code-starter
 */

defined( 'ABSPATH' ) || exit;

require_once __DIR__ . '/inc/visual-editor.php';

function wpcs_setup(): void {
	add_theme_support( 'editor-styles' );
	add_theme_support( 'post-thumbnails' );
	register_block_pattern_category( 'wpcs-starter', array( 'label' => __( 'WP Code Starter', 'wp-code-starter' ) ) );
}
add_action( 'init', 'wpcs_setup' );

function wpcs_block_styles(): void {
	$asset = 'assets/site.css';
	wp_enqueue_style( 'wpcs-site', get_theme_file_uri( $asset ), array(), (string) filemtime( get_theme_file_path( $asset ) ) );
}
add_action( 'enqueue_block_assets', 'wpcs_block_styles' );

function wpcs_editor_settings( array $settings, WP_Block_Editor_Context $context ): array {
	$type = $context->post->post_type ?? '';
	if ( in_array( $type, array( 'page', 'post', 'wpcs_project' ), true ) ) {
		$settings['defaultRenderingMode'] = 'template-locked';
	}
	return $settings;
}
add_filter( 'block_editor_settings_all', 'wpcs_editor_settings', 10, 2 );

function wpcs_editor_assets(): void {
	$asset = 'assets/editor.js';
	wp_enqueue_script(
		'wpcs-editor',
		get_theme_file_uri( $asset ),
		array( 'wp-block-editor', 'wp-components', 'wp-data', 'wp-editor', 'wp-element', 'wp-hooks', 'wp-i18n', 'wp-plugins' ),
		(string) filemtime( get_theme_file_path( $asset ) ),
		true
	);

	$settings = array(
		'canEditSite'   => current_user_can( 'edit_theme_options' ),
		'siteEditorUrl' => admin_url( 'site-editor.php' ),
		'theme'         => get_stylesheet(),
	);
	wp_add_inline_script( 'wpcs-editor', 'window.wpcsEditor = ' . wp_json_encode( $settings, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT ) . ';', 'before' );
	wp_set_script_translations( 'wpcs-editor', 'wp-code-starter' );
}
add_action( 'enqueue_block_editor_assets', 'wpcs_editor_assets' );

/**
 * Make the main landmark focusable for WordPress's built-in skip link.
 */
function wpcs_focusable_main( string $content, array $block ): string {
	if ( 'main' !== ( $block['attrs']['tagName'] ?? '' ) ) {
		return $content;
	}
	$tag = new WP_HTML_Tag_Processor( $content );
	if ( $tag->next_tag( 'MAIN' ) && $tag->has_class( 'wpcs-main' ) ) {
		$tag->set_attribute( 'tabindex', '-1' );
	}
	return $tag->get_updated_html();
}
add_filter( 'render_block_core/group', 'wpcs_focusable_main', 10, 2 );
