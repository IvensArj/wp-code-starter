<?php
/**
 * Load the fictional site in wp-env or WordPress Playground.
 *
 * @package wp-code-starter
 */

defined( 'ABSPATH' ) || exit;

$wpcs_mode = defined( 'WP_CLI' ) && WP_CLI ? ( $args[0] ?? 'base' ) : ( defined( 'WPCS_PLAYGROUND_SEED' ) ? WPCS_PLAYGROUND_SEED : null );
if ( ! in_array( $wpcs_mode, array( 'base', 'portfolio' ), true ) || 'production' === wp_get_environment_type() ) {
	throw new RuntimeException( 'Execute a carga de exemplo em um ambiente local com o modo base ou portfolio.' );
}

function wpcs_demo_markup( string $name, array $replacements = array() ): string {
	return strtr( file_get_contents( __DIR__ . '/content/' . $name . '.html' ), $replacements );
}

function wpcs_demo_existing( string $slug, string $type ): int {
	return (int) ( get_posts( array(
		'post_type'      => $type,
		'post_status'    => array( 'publish', 'future', 'draft', 'pending', 'private', 'trash', 'inherit' ),
		'posts_per_page' => 1,
		'fields'         => 'ids',
		'meta_key'       => '_wpcs_demo_key',
		'meta_value'     => $slug,
	) )[0] ?? 0 );
}

function wpcs_demo_post( string $slug, string $title, string $type, string $content, array $extra = array() ): int {
	$existing = wpcs_demo_existing( $slug, $type );
	if ( $existing ) {
		return $existing;
	}
	if ( get_page_by_path( $slug, OBJECT, array( $type ) ) ) {
		throw new RuntimeException( 'Já existe um registro com este slug. Resolva o conflito antes de continuar: ' . $slug );
	}
	$id = wp_insert_post( wp_slash( array_merge( array(
		'post_name'    => $slug,
		'post_title'   => $title,
		'post_type'    => $type,
		'post_status'  => 'publish',
		'post_content' => $content,
		'meta_input'   => array( '_wpcs_demo_key' => $slug ),
	), $extra ) ), true );
	if ( is_wp_error( $id ) || ! $id ) {
		throw new RuntimeException( 'Falha ao criar o registro: ' . $slug );
	}
	return $id;
}

function wpcs_demo_image( string $slug, string $title, string $alt ): int {
	$existing = wpcs_demo_existing( $slug, 'attachment' );
	if ( $existing ) {
		return $existing;
	}

	require_once ABSPATH . 'wp-admin/includes/image.php';
	$contents = file_get_contents( __DIR__ . '/media/' . $slug . '.jpg' );
	if ( false === $contents ) {
		throw new RuntimeException( 'Imagem de exemplo indisponível: ' . $slug );
	}
	$upload = wp_upload_bits( $slug . '.jpg', null, $contents );
	if ( $upload['error'] ) {
		throw new RuntimeException( $upload['error'] );
	}
	$id = wp_insert_attachment( wp_slash( array(
		'post_name'      => $slug,
		'post_title'     => $title,
		'post_mime_type' => 'image/jpeg',
		'meta_input'     => array( '_wpcs_demo_key' => $slug, '_wp_attachment_image_alt' => $alt ),
	) ), $upload['file'], 0, true );
	if ( is_wp_error( $id ) || ! $id ) {
		wp_delete_file( $upload['file'] );
		throw new RuntimeException( 'Falha ao importar a imagem: ' . $slug );
	}
	wp_update_attachment_metadata( $id, wp_generate_attachment_metadata( $id, $upload['file'] ) );
	return $id;
}

function wpcs_demo_menu( array $pages ): string {
	$links = array();
	foreach ( $pages as $id ) {
		if ( 'publish' !== get_post_status( $id ) ) {
			continue;
		}
		$links[] = get_comment_delimited_block_content( 'core/navigation-link', array(
			'label' => get_the_title( $id ),
			'type'  => 'page',
			'id'    => $id,
			'url'   => get_permalink( $id ),
			'kind'  => 'post-type',
		), '' );
	}
	return implode( "\n", $links );
}

function wpcs_demo_add_portfolio( int $courtyard ): int {
	require_once ABSPATH . 'wp-admin/includes/plugin.php';
	$result = activate_plugin( 'wpcs-projects/wpcs-projects.php' );
	if ( is_wp_error( $result ) ) {
		throw new RuntimeException( $result->get_error_message() );
	}
	if ( ! WP_Block_Patterns_Registry::get_instance()->is_registered( 'wpcs-projects/gallery' ) ) {
		wpcs_projects_blocks();
		wpcs_projects_patterns();
	}
	$workshop = wpcs_demo_image( 'oficina-do-bairro', 'Oficina do Bairro', 'Oficina com bancadas de madeira, parede de tijolo e vista para o pátio' );
	$projects = array(
		array( 'casa-do-patio', 'Casa do Pátio', 'Residencial', 'residencial', 'courtyard', $courtyard ),
		array( 'oficina-do-bairro', 'Oficina do Bairro', 'Espaços coletivos', 'espacos-coletivos', 'workshop', $workshop ),
	);
	foreach ( $projects as list( $slug, $title, $area, $area_slug, $body, $image ) ) {
		if ( wpcs_demo_existing( $slug, 'wpcs_project' ) ) {
			continue;
		}
		$term = term_exists( $area_slug, 'wpcs_project_area' ) ?: wp_insert_term( $area, 'wpcs_project_area', array( 'slug' => $area_slug ) );
		if ( is_wp_error( $term ) ) {
			throw new RuntimeException( $term->get_error_message() );
		}
		$id = wpcs_demo_post( $slug, $title, 'wpcs_project', wpcs_demo_markup( $body ), array( '_thumbnail_id' => $image ) );
		$result = wp_set_object_terms( $id, (int) $term['term_id'], 'wpcs_project_area' );
		if ( is_wp_error( $result ) ) {
			throw new RuntimeException( $result->get_error_message() );
		}
	}
	$pattern = WP_Block_Patterns_Registry::get_instance()->get_registered( 'wpcs-projects/gallery' );
	return wpcs_demo_post( 'galeria', 'Galeria', 'page', wpcs_demo_markup( 'gallery' ) . "\n" . $pattern['content'] );
}

$wpcs_courtyard = wpcs_demo_image( 'casa-do-patio', 'Casa do Pátio', 'Casa térrea em madeira de cedro cercada por um jardim em declive' );
$wpcs_about = wpcs_demo_post( 'sobre', 'Sobre', 'page', wpcs_demo_markup( 'about' ) );
$wpcs_posts = WP_Block_Patterns_Registry::get_instance()->get_registered( 'wp-code-starter/posts' );
if ( empty( $wpcs_posts['content'] ) ) {
	throw new RuntimeException( 'Ative primeiro o tema WP Code Starter.' );
}

$wpcs_blog = wpcs_demo_post( 'blog', 'Blog', 'page', wpcs_demo_markup( 'blog' ) . "\n" . $wpcs_posts['content'] );
$wpcs_home = wpcs_demo_post( 'inicio', 'Início', 'page', wpcs_demo_markup( 'home', array(
	'{{image_url}}' => esc_url( wp_get_attachment_image_url( $wpcs_courtyard, 'full' ) ),
	'{{image_id}}'  => $wpcs_courtyard,
	'{{about_url}}' => esc_url( get_permalink( $wpcs_about ) ),
	'{{blog_url}}'  => esc_url( get_permalink( $wpcs_blog ) ),
) ), array( 'page_template' => 'landing' ) );

wpcs_demo_post( 'sombra-na-varanda', 'Como dimensionamos a sombra da varanda', 'post', wpcs_demo_markup( 'shade' ) );
wpcs_demo_post( 'testar-o-tijolo', 'Antes de erguer a parede, uma amostra', 'post', wpcs_demo_markup( 'bricks' ) );

$wpcs_pages = array( $wpcs_home, $wpcs_about, $wpcs_blog );
$wpcs_base_menu = wpcs_demo_menu( $wpcs_pages );

if ( 'portfolio' === $wpcs_mode ) {
	$wpcs_pages[] = wpcs_demo_add_portfolio( $wpcs_courtyard );
}
$wpcs_navigation = wpcs_demo_menu( $wpcs_pages );
$wpcs_menu = wpcs_demo_post( 'menu-exemplo', 'Menu de exemplo', 'wp_navigation', $wpcs_navigation );
if ( 'portfolio' === $wpcs_mode && $wpcs_base_menu === get_post_field( 'post_content', $wpcs_menu ) ) {
	$result = wp_update_post( wp_slash( array( 'ID' => $wpcs_menu, 'post_content' => $wpcs_navigation ) ), true );
	if ( is_wp_error( $result ) ) {
		throw new RuntimeException( $result->get_error_message() );
	}
}

if ( ! get_option( 'wpcs_demo_initialized' ) ) {
	if ( 'posts' === get_option( 'show_on_front' ) && ! get_option( 'page_on_front' ) && 'publish' === get_post_status( $wpcs_home ) ) {
		update_option( 'page_on_front', $wpcs_home );
		update_option( 'show_on_front', 'page' );
	}
	if ( in_array( get_option( 'blogname' ), array( '', 'WordPress', 'My WordPress Website', 'My WordPress Site', 'WordPress Development Site' ), true ) ) {
		update_option( 'blogname', 'Estúdio Linha de Cedro' );
	}
	if ( ! get_option( 'permalink_structure' ) ) {
		$wp_rewrite->set_permalink_structure( '/%postname%/' );
		flush_rewrite_rules();
	}
	update_option( 'wpcs_demo_initialized', '1' );
}
if ( defined( 'WP_CLI' ) && WP_CLI ) {
	WP_CLI::success( 'Conteúdo de exemplo disponível: ' . home_url( '/' ) );
}
