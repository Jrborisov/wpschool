<?php
/**
 * Envato Elements:
 *
 * Elementor core integration here.
 *
 * @package Envato/Envato_Elements
 * @since 0.0.2
 */

namespace Envato_Elements;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Elementor registration and management.
 *
 * @since 0.0.2
 */
class Elementor extends Base {

	/**
	 * Elementor constructor.
	 */
	public function __construct() {
		parent::__construct();
		add_action( 'elementor/editor/before_enqueue_scripts', [ $this, 'enqueue_editor_scripts' ] );
		add_action( 'elementor/preview/enqueue_styles', [ $this, 'enqueue_editor_scripts' ] );
	}

	/**
	 * Figure out if we should enable deep Elementor integration.
	 *
	 * @return bool
	 */
	public function is_deep_integration_enabled() {
		return class_exists( '\Elementor\Plugin' ) && License::get_instance()->is_activated();
	}

	/**
	 * Load CSS for our custom Elementor modal.
	 */
	public function enqueue_editor_scripts() {
		if ( $this->is_deep_integration_enabled() ) {
			wp_enqueue_script( 'elements-elementor-modal', ENVATO_ELEMENTS_URI . 'assets/js/elementor-modal.min.js', [ 'jquery' ], ENVATO_ELEMENTS_VER );
			wp_enqueue_style( 'elements-elementor-modal', ENVATO_ELEMENTS_URI . 'assets/css/elementor-modal.min.css', [], ENVATO_ELEMENTS_VER );
			Plugin::get_instance()->admin_page_assets();
		}
	}


}

