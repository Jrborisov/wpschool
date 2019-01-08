<?php
/**
 * Envato Elements:
 *
 * This starts things up. Registers the SPL and starts up some classes.
 *
 * @package Envato/Envato_Elements
 * @since 0.0.2
 */

namespace Envato_Elements;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Category registration and management.
 *
 * @since 0.0.2
 */
class Category extends Base {

	public $categories = [];

	/**
	 * Category constructor.
	 */
	public function __construct() {
		parent::__construct();

		$this->categories = [];

		$elementor_missing = Required_Plugin::get_instance()->get_missing_plugins( [], 'elementor' );
		$beaver_missing    = Required_Plugin::get_instance()->get_missing_plugins( [], 'beaver-builder' );

		if ( $elementor_missing && $beaver_missing ) {

			$this->categories['elementor']      = [
				'url'         => add_query_arg( 'category', 'elementor', Collection::get_instance()->get_url() ),
				'nav_title'   => 'Template Kits',
				'edit_button' => 'Edit Template with Elementor',
				'page_title'  => 'Elementor ' . ENVATO_ELEMENTS_CONTENT_NAME . 's',
				'main_nav'    => true,
				'type'        => 'templates',
				'subtypes'    => [
					'elementor'      => 'Elementor',
					'beaver-builder' => 'Beaver Builder',
				],
			];
			$this->categories['beaver-builder'] = [
				'url'         => add_query_arg( 'category', 'beaver-builder', Collection::get_instance()->get_url() ),
				'nav_title'   => 'Beaver Builder',
				'main_nav'    => false,
				'edit_button' => 'Edit Page with Beaver Builder',
				'page_title'  => 'Beaver Builder ' . ENVATO_ELEMENTS_CONTENT_NAME . 's',
				'type'        => 'templates',
				'subtypes'    => [],
			];

		} elseif ( $elementor_missing ) {

			$this->categories['beaver-builder'] = [
				'url'         => add_query_arg( 'category', 'beaver-builder', Collection::get_instance()->get_url() ),
				'nav_title'   => 'Beaver Builder',
				'main_nav'    => true,
				'edit_button' => 'Edit Page with Beaver Builder',
				'page_title'  => 'Beaver Builder ' . ENVATO_ELEMENTS_CONTENT_NAME . 's',
				'type'        => 'templates',
				'subtypes'    => [],
			];

		} elseif ( $beaver_missing ) {

			$this->categories['elementor']        = [
				'url'         => add_query_arg( 'category', 'elementor', Collection::get_instance()->get_url() ),
				'nav_title'   => 'Elementor',
				'edit_button' => 'Edit Template with Elementor',
				'page_title'  => 'Elementor ' . ENVATO_ELEMENTS_CONTENT_NAME . 's',
				'main_nav'    => true,
				'type'        => 'templates',
				'subtypes'    => [
					'elementor'        => 'Template Kits',
					'elementor-blocks' => 'Blocks',
				],
			];
			$this->categories['elementor-blocks'] = [
				'url'         => add_query_arg( 'category', 'elementor-blocks', Collection::get_instance()->get_url() ),
				'nav_title'   => 'Blocks',
				'edit_button' => 'Edit Template with Elementor',
				'page_title'  => 'Elementor Blocks',
				'main_nav'    => false,
				'type'        => 'blocks',
				'subtypes'    => [
					'elementor'        => 'Template Kits',
					'elementor-blocks' => 'Blocks',
				],
			];

		} else {

			// All are active.
			$this->categories['elementor']        = [
				'url'         => add_query_arg( 'category', 'elementor', Collection::get_instance()->get_url() ),
				'nav_title'   => 'Elementor',
				'edit_button' => 'Edit Template with Elementor',
				'page_title'  => 'Elementor ' . ENVATO_ELEMENTS_CONTENT_NAME . 's',
				'main_nav'    => true,
				'type'        => 'templates',
				'subtypes'    => [
					'elementor'        => 'Template Kits',
					'elementor-blocks' => 'Blocks',
				],
			];
			$this->categories['elementor-blocks'] = [
				'url'         => add_query_arg( 'category', 'elementor-blocks', Collection::get_instance()->get_url() ),
				'nav_title'   => 'Blocks',
				'edit_button' => 'Edit Template with Elementor',
				'page_title'  => 'Elementor Blocks',
				'main_nav'    => false,
				'type'        => 'blocks',
				'subtypes'    => [
					'elementor'        => 'Template Kits',
					'elementor-blocks' => 'Blocks',
				],
			];
			$this->categories['beaver-builder']   = [
				'url'         => add_query_arg( 'category', 'beaver-builder', Collection::get_instance()->get_url() ),
				'nav_title'   => 'Beaver Builder',
				'main_nav'    => true,
				'edit_button' => 'Edit Page with Beaver Builder',
				'page_title'  => 'Beaver Builder ' . ENVATO_ELEMENTS_CONTENT_NAME . 's',
				'type'        => 'templates',
				'subtypes'    => [],
			];

		}


	}

	public function get_remote_categorys() {

		$api_data = [
			'all' => 1,
		];

		return API::get_instance()->api_call( 'v1/categories', $api_data );

	}

	public function get_current( $category = false ) {
		$current_category = $category ? $category : ( ! empty( $_GET['category'] ) ? $_GET['category'] : '' );
		if ( ! isset( $this->categories[ $current_category ] ) && $current_category ) {
			foreach ( $this->categories as $possible_cat => $possible_cat_details ) {
				if ( ! empty( $possible_cat_details['subtypes'] ) && isset( $possible_cat_details['subtypes'][ $current_category ] ) ) {
					$current_category = $possible_cat;
					break;
				}
			}
		}
		if ( $current_category && isset( $this->categories[ $current_category ] ) ) {
			$current              = new \stdClass();
			$current->slug        = $current_category;
			$current->nav_title   = $this->categories[ $current_category ]['nav_title'];
			$current->edit_button = $this->categories[ $current_category ]['edit_button'];
			$current->page_title  = $this->categories[ $current_category ]['page_title'];
			$current->type        = $this->categories[ $current_category ]['type'];
			$current->subtypes    = $this->categories[ $current_category ]['subtypes'];

			return $current;
		}

		return false;
	}


	public function header_nav() {

		$current = $this->get_current();

		foreach ( $this->categories as $slug => $category ) {
			if ( $category['main_nav'] ) {
				?>
				<li class="envato-elements__header-menuitem <?php echo ! empty( $category['subtypes'] ) ? 'envato-elements__header-menuitem--has-children' : ''; ?>">
					<a href="<?php echo esc_url( $category['url'] ); ?>"
						class="envato-elements__header-menulink envato-elements--action<?php
						echo $current && $current->slug === $slug ? ' envato-elements__header-menulink--current' : ''; ?>"
						data-nav-top="top"
						data-nav-type="main-category"
						data-category-slug="<?php echo esc_attr( $slug ); ?>"
						data-category-slugs="<?php echo filter_var( json_encode( array_keys( $category['subtypes'] ) ), FILTER_SANITIZE_SPECIAL_CHARS );; ?>"
					><?php echo esc_html( $category['nav_title'] ); ?></a>
					<?php if ( ! empty( $category['subtypes'] ) ) { ?>
						<ul class="envato-elements__header-menusubwrap">
							<?php foreach ( $category['subtypes'] as $subtype_slug => $subtype_name ) { ?>
								<li class="envato-elements__header-menusubitem">
									<a href="<?php echo esc_url( $category['url'] ); ?>"
										class="envato-elements__header-menulink envato-elements--action<?php
										echo $current && $current->slug === $subtype_slug ? ' envato-elements__header-menulink--current' : ''; ?>"
										data-nav-top="top"
										data-nav-type="main-category"
										data-category-slug="<?php echo esc_attr( $subtype_slug ); ?>"
										data-search='{ "pg": 1 }'
									><?php echo esc_html( $subtype_name ); ?></a>
								</li>
							<?php } ?>
						</ul>
					<?php } ?>
				</li>
			<?php }
		}
	}

	public function pre_collection_all() {
		$this->pre_collection_plugin_check();
	}

	public function pre_collection_single() {
		$this->pre_collection_plugin_check();
	}

	public function pre_collection_plugin_check() {
		Required_Plugin::get_instance()->display_notice( $this->category );
	}

}
