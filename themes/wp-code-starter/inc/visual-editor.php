<?php
/**
 * Apply visual options to existing core blocks without changing their saved HTML.
 *
 * @package wp-code-starter
 */

defined( 'ABSPATH' ) || exit;

function wpcs_visual_number( array $options, string $key, int $default, int $min, int $max ): int {
	return isset( $options[ $key ] ) && is_numeric( $options[ $key ] )
		? max( $min, min( $max, (int) $options[ $key ] ) )
		: $default;
}

function wpcs_render_visual_options( string $content, array $block ): string {
	$attributes = $block['attrs'] ?? array();
	$options    = $attributes['metadata']['wpcsVisual'] ?? array();
	if ( ! is_array( $options ) || ! $options ) {
		return $content;
	}

	$tag = new WP_HTML_Tag_Processor( $content );
	if ( ! $tag->next_tag() ) {
		return $content;
	}

	$name = $block['blockName'];
	$css  = array();
	if ( 'core/group' === $name && $tag->has_class( 'wpcs-site-header' ) ) {
		if ( true === ( $options['sticky'] ?? false ) ) {
			$tag->set_attribute( 'data-wpcs-sticky', 'true' );
		}
		return $tag->get_updated_html();
	}

	if ( ( 'core/group' === $name || 'core/post-template' === $name ) && true === ( $options['grid'] ?? false ) ) {
		$columns = wpcs_visual_number( $options, 'columns', 2, 1, 6 );
		$mobile  = wpcs_visual_number( $options, 'mobile', 1, 1, 3 );
		$gap     = wpcs_visual_number( $options, 'gap', 24, 0, 100 );
		$first   = wpcs_visual_number( $options, 'first', 50, 25, 75 );
		$align   = $options['align'] ?? 'stretch';
		$align   = in_array( $align, array( 'start', 'center', 'end', 'stretch' ), true ) ? $align : 'stretch';
		$track   = 2 === $columns
			? 'minmax(0,' . $first . 'fr) minmax(0,' . ( 100 - $first ) . 'fr)'
			: 'repeat(' . $columns . ',minmax(0,1fr))';
		$tag->add_class( 'wpcs-visual-grid' );
		$css[] = '--wpcs-columns:' . $track;
		$css[] = '--wpcs-mobile:' . $mobile;
		$css[] = '--wpcs-gap:' . $gap . 'px';
		$css[] = '--wpcs-align:' . $align;
	}

	if ( 'core/paragraph' === $name && is_numeric( $options['width'] ?? null ) ) {
		$tag->add_class( 'wpcs-visual-paragraph' );
		$position = $options['position'] ?? 'start';
		if ( in_array( $position, array( 'center', 'end' ), true ) ) {
			$tag->add_class( 'wpcs-visual-' . $position );
		}
		$css[] = '--wpcs-width:' . wpcs_visual_number( $options, 'width', 100, 10, 100 ) . '%';
	}

	if ( 'core/image' === $name && true === ( $options['crop'] ?? false ) ) {
		$tag->add_class( 'wpcs-visual-crop' );
		$focus = is_array( $options['focus'] ?? null ) ? $options['focus'] : array();
		$x     = wpcs_visual_number( $focus, 'x', 50, 0, 100 );
		$y     = wpcs_visual_number( $focus, 'y', 50, 0, 100 );
		$css[] = '--wpcs-desktop-height:' . wpcs_visual_number( $options, 'desktopHeight', 420, 120, 800 ) . 'px';
		$css[] = '--wpcs-mobile-height:' . wpcs_visual_number( $options, 'mobileHeight', 280, 120, 640 ) . 'px';
		$css[] = '--wpcs-focus:' . $x . '% ' . $y . '%';
	}

	if ( $css ) {
		$existing = rtrim( (string) $tag->get_attribute( 'style' ), " ;\r\n\t" );
		$tag->set_attribute( 'style', implode( ';', array_filter( array_merge( array( $existing ), $css ) ) ) );
	}

	return $tag->get_updated_html();
}

foreach ( array( 'group', 'post-template', 'image', 'paragraph' ) as $wpcs_block ) {
	add_filter( 'render_block_core/' . $wpcs_block, 'wpcs_render_visual_options', 20, 2 );
}
