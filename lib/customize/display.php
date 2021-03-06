<?php
/**
 * Load User-Generated JavaScript and CSS
 *
 * @package   FoodiePro\Functions\Customizer
 * @copyright Copyright (c) 2018, Feast Design Co.
 * @license   GPL-2.0+
 * @since     3.0.0
 */

defined( 'WPINC' ) || die;

add_action( 'wp_enqueue_scripts', 'foodie_pro_load_google_fonts' );
/**
 * Load Google Fonts libraries using dynamically generated user data.
 *
 * @since  2.0.0
 *
 * @uses   feastco_customizer_get_google_font_uri()
 * @uses   CHILD_THEME_VERSION
 */
function foodie_pro_load_google_fonts() {
	if ( apply_filters( 'foodie_pro_disable_google_fonts', false ) ) {
		return;
	}

	$fonts = array();

	foreach ( foodie_pro_get_fonts() as $font => $setting ) {
		$fonts[] = get_theme_mod( $font . '_family', $setting['default_family'] );
	}

	wp_enqueue_style(
		'google-fonts',
		feastco_customizer_get_google_font_uri( $fonts ),
		array(),
		CHILD_THEME_VERSION
	);
}

add_action( 'wp_enqueue_scripts', 'foodie_pro_add_customizer_styles' );
/**
 * Load all CSS rules generated by the customizer.
 *
 * @since  2.0.0
 *
 * @uses  Feastco_Customizer_Styles()
 */
function foodie_pro_add_customizer_styles() {
	do_action( 'foodie_pro_inline_styles' );
	wp_add_inline_style( 'foodie-pro-theme', Feastco_Customizer_Styles::instance()->build() );
}

add_action( 'foodie_pro_inline_styles', 'foodie_pro_build_styles' );
/**
 * Process user options to generate CSS needed to implement the choices.
 *
 * @since  2.0.0.
 *
 * @uses   foodie_pro_get_colors()
 * @uses   foodie_pro_get_fonts()
 * @return void
 */
function foodie_pro_build_styles() {
	// * Build color styles.
	foreach ( foodie_pro_get_colors() as $color => $setting ) {

		$mod = get_theme_mod( $color, $setting['default'] );

		if ( $mod !== $setting['default'] ) {

			$styles = array(
				'selectors' => array(
					$setting['selector'],
				),
				'declarations' => array(
					$setting['rule'] => sanitize_hex_color( $mod ),
				),
			);

			Feastco_Customizer_Styles::instance()->add( $styles );
		}
	}

	// * Allow users to disable Google Fonts Output.
	if ( apply_filters( 'foodie_pro_disable_google_fonts', false ) ) {
		return;
	}

	// * Build font styles.
	foreach ( foodie_pro_get_fonts() as $font => $setting ) {

		$mod   = get_theme_mod( $font . '_family', $setting['default_family'] );
		$stack = feastco_customizer_get_font_stack( $mod );
		// * Build styles for the font family.
		$styles = array(
			'selectors' => array(
				$setting['selector'],
			),
			'declarations' => array(
				'font-family' => $stack,
			),
		);

		Feastco_Customizer_Styles::instance()->add( $styles );

		$mod = get_theme_mod( $font . '_weight', $setting['default_weight'] );
		// * Build styles for the font weight if it isn't disabled.
		if ( $mod !== $setting['default_weight']  && 'disabled' !== $setting['default_weight']  ) {

			$styles = array(
				'selectors' => array(
					$setting['selector'],
				),
				'declarations' => array(
					'font-weight' => $mod,
				),
			);

			Feastco_Customizer_Styles::instance()->add( $styles );
		}

		$mod = get_theme_mod( $font . '_size', $setting['default_size'] );
		// * Build styles for the font size if it isn't disabled.
		if ( $mod !== $setting['default_size']  && 'disabled' !== $setting['default_size']  ) {
			$styles = array(
				'selectors' => array(
					$setting['selector'],
				),
				'declarations' => array(
					'font-size' => $mod,
				),
			);

			Feastco_Customizer_Styles::instance()->add( $styles );
		}

		$mod = get_theme_mod( $font . '_style', $setting['default_style'] );
		// * Build styles for the font style if it isn't disabled.
		if ( $mod !== $setting['default_style'] && 'disabled' !== $setting['default_style'] ) {

			$styles = array(
				'selectors' => array(
					$setting['selector'],
				),
				'declarations' => array(
					'font-style' => $mod,
				),
			);

			Feastco_Customizer_Styles::instance()->add( $styles );
		}
	}
}

add_action( 'genesis_before_loop', 'foodie_pro_archive_maybe_add_grid' );
/**
 * Add the archive grid filter to the main loop.
 *
 * @since  2.0.0
 *
 * @uses   foodie_pro_archive_grid()
 */
function foodie_pro_archive_maybe_add_grid() {
	if ( $grid = foodie_pro_archive_grid() ) {
		add_filter( 'post_class', 'foodie_pro_grid_' . $grid );
	}
}

add_action( 'genesis_after_loop', 'foodie_pro_archive_maybe_remove_grid' );
/**
 * Remove the archive grid filter to ensure other loops are unaffected.
 *
 * @since  2.0.0
 *
 * @uses   foodie_pro_archive_grid()
 */
function foodie_pro_archive_maybe_remove_grid() {
	if ( $grid = foodie_pro_archive_grid() ) {
		remove_filter( 'post_class', 'foodie_pro_grid_' . $grid );
	}
}

add_action( 'genesis_before_content', 'foodie_pro_archive_maybe_remove_title' );
/**
 * Remove the entry title if the user has disabled it via the customizer.
 *
 * @since  2.0.0
 *
 * @uses   foodie_pro_is_blog()
 */
function foodie_pro_archive_maybe_remove_title() {
	if ( ! foodie_pro_is_blog() ) {
		return;
	}
	if ( ! get_theme_mod( 'foodie_pro_archive_show_title', true ) ) {
		remove_action( 'genesis_entry_header', 'genesis_do_post_title' );
	}
}

add_action( 'genesis_before_content', 'foodie_pro_archive_maybe_remove_info' );
/**
 * Remove the entry info if the user has disabled it via the customizer.
 *
 * @since  2.0.0
 *
 * @uses   foodie_pro_is_blog()
 */
function foodie_pro_archive_maybe_remove_info() {
	if ( ! foodie_pro_is_blog() ) {
		return;
	}
	if ( ! get_theme_mod( 'foodie_pro_archive_show_info', true ) ) {
		remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
	}
}

add_action( 'genesis_before_content', 'foodie_pro_archive_maybe_remove_content' );
/**
 * Remove the entry content if the user has disabled it via the customizer.
 *
 * @since  2.0.0
 *
 * @uses   foodie_pro_is_blog()
 */
function foodie_pro_archive_maybe_remove_content() {
	if ( ! foodie_pro_is_blog() ) {
		return;
	}
	if ( ! get_theme_mod( 'foodie_pro_archive_show_content', true ) ) {
		remove_action( 'genesis_entry_content', 'genesis_do_post_content' );
	}
}

add_action( 'genesis_before_content', 'foodie_pro_archive_maybe_remove_meta' );
/**
 * Remove the entry meta if the user has disabled it via the customizer.
 *
 * @since  2.0.0
 *
 * @uses   foodie_pro_is_blog()
 */
function foodie_pro_archive_maybe_remove_meta() {
	if ( ! foodie_pro_is_blog() ) {
		return;
	}
	if ( ! get_theme_mod( 'foodie_pro_archive_show_meta', true ) ) {
		remove_action( 'genesis_entry_footer', 'genesis_post_meta' );
	}
}

add_action( 'genesis_before_content', 'foodie_pro_archive_maybe_move_image' );
/**
 * Move the post image if the user has changed the placement via the customizer.
 *
 * @since  2.0.0
 *
 * @uses   foodie_pro_is_blog()
 */
function foodie_pro_archive_maybe_move_image() {
	if ( ! foodie_pro_is_blog() ) {
		return;
	}
	$image_placement = get_theme_mod( 'foodie_pro_archive_image_placement', 'after_title' );

	if ( 'after_title' !== $image_placement ) {
		remove_action( 'genesis_entry_content', 'genesis_do_post_image', 8 );
	}

	if ( 'before_title' === $image_placement ) {
		add_action( 'genesis_entry_header', 'genesis_do_post_image', 5 );
	}

	if ( 'after_content' === $image_placement ) {
		add_action( 'genesis_entry_footer', 'genesis_do_post_image', 0 );
	}
}

add_action( 'genesis_after_loop', 'foodie_pro_archive_reset_loop' );
/**
 * Make sure our archive customizations don't spill over into other loops.
 *
 * @since  2.0.0
 */
function foodie_pro_archive_reset_loop() {
	remove_action( 'genesis_before_loop', 'foodie_pro_archive_maybe_add_grid' );
	remove_action( 'genesis_before_content', 'foodie_pro_archive_maybe_remove_title' );
	remove_action( 'genesis_before_content', 'foodie_pro_archive_maybe_remove_info' );
	remove_action( 'genesis_before_content', 'foodie_pro_archive_maybe_remove_content' );
	remove_action( 'genesis_before_content', 'foodie_pro_archive_maybe_remove_meta' );
	remove_action( 'genesis_before_content', 'foodie_pro_archive_maybe_move_image' );
}
