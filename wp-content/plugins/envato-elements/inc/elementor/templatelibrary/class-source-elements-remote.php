<?php
/**
 * Elementor Template Library: Source_Elements_Remote class
 *
 * This class is used to manage remote templates.
 *
 * @package Envato/Envato_Elements
 * @since 0.1.1
 */

namespace Elementor\TemplateLibrary;

use Elementor\Plugin;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * Elementor template library remote source.
 *
 * @since 0.0.2
 */
class Source_Elements_Remote extends Source_Base {

	/**
	 * ID used in Elementor template integration.
	 *
	 * @var string ID.
	 */
	private $_our_id = 'elements-kits';

	/**
	 * Source_Elements_Remote constructor.
	 *
	 * @param array $args args.
	 */
	public function __construct( $args ) {
		if ( ! empty( $args['id'] ) ) {
			$this->_our_id = $args['id'];
		}
		parent::__construct();
	}

	/**
	 * Get remote template ID.
	 *
	 * Retrieve the remote template ID.
	 *
	 * @since 0.0.2
	 * @access public
	 *
	 * @return string The remote template ID.
	 */
	public function get_id() {
		return $this->_our_id;
	}

	/**
	 * Get remote template title.
	 *
	 * Retrieve the remote template title.
	 *
	 * @since 0.0.2
	 * @access public
	 *
	 * @return string The remote template title.
	 */
	public function get_title() {
		return __( 'Envato Elements', 'elementor' );
	}

	/**
	 * Register remote template data.
	 *
	 * Used to register custom template data like a post type, a taxonomy or any
	 * other data.
	 *
	 * @since 0.0.2
	 * @access public
	 */
	public function register_data() {
	}

	/**
	 * Get remote templates.
	 *
	 * @since 0.0.2
	 * @access public
	 *
	 * @param array $args Optional. Filter templates list based on a set of
	 *                    arguments. Default is an empty array.
	 *
	 * @return array Remote templates.
	 */
	public function get_items( $args = [] ) {

		$block_manager = new \Envato_Elements\Collection_Elementor_Blocks();
		$all_blocks    = $block_manager->get_all_blocks();

		$templates = [];

		// Testing memory limit issue.
		// todo: remove this and find out if our filter below can be improved
		\Envato_Elements\Collection_Elementor_Blocks::get_instance()->check_memory_limit();

		if ( $all_blocks ) {
			foreach ( $all_blocks['data'] as $template_data ) {
				$template = \Envato_Elements\Collection_Elementor_Blocks::get_instance()->filter_template( $template_data, [ 'collectionId' => $template_data['collection_id'] ] );
				if ( $template ) {
					$templates[] = [
						'template_id'     => $template['templateId'],
						'collection_id'   => $template_data['collection_id'],
						'source'          => 'remote',
						'type'            => 'block',
						// This is the category shown in Elementor UI:
						'subtype'         => ! empty( $template['templateType'] ) ? strtolower( current( $template['templateType'] ) ) : 'envato elements',
						'title'           => $template['templateName'],
						'thumbnail'       => ! empty( $template['previewThumb2x'] ) ? $template['previewThumb2x'] : $template['previewThumb'],
						'date'            => time(),
						'human_date'      => date( 'Y-m-d' ),
						'author'          => 'Envato',
						'tags'            => [ 'Envato' ],
						'isPro'           => ! empty( $template['templateFeatures'] ) && ! empty( $template['templateFeatures']['elementor-pro'] ),
						'popularityIndex' => 9999,
						'trendIndex'      => 0,
						'hasPageSettings' => 0,
						'url'             => $template['previewUrl'],
						'favorite'        => ! empty( $template['templateInstalled'] ),
					];
				}
			}
		}

		return $templates;
	}

	/**
	 * Get remote template.
	 *
	 * @since 0.0.2
	 * @access public
	 *
	 * @param array $template_data Remote template data.
	 *
	 * @return array Remote template.
	 */
	public function get_item( $template_data ) {

		return [];
	}

	/**
	 * Save remote template.
	 *
	 * @since 0.0.2
	 * @access public
	 *
	 * @param array $template_data Remote template data.
	 *
	 * @return bool Return false.
	 */
	public function save_item( $template_data ) {
		return false;
	}

	/**
	 * Update remote template.
	 *
	 * @since 0.0.2
	 * @access public
	 *
	 * @param array $new_data New template data.
	 *
	 * @return bool Return false.
	 */
	public function update_item( $new_data ) {
		return false;
	}

	/**
	 * Delete remote template.
	 *
	 * @since 0.0.2
	 * @access public
	 *
	 * @param int $template_id The template ID.
	 *
	 * @return bool Return false.
	 */
	public function delete_template( $template_id ) {
		return false;
	}

	/**
	 * Export remote template.
	 *
	 * @since 0.0.2
	 * @access public
	 *
	 * @param int $template_id The template ID.
	 *
	 * @return bool Return false.
	 */
	public function export_template( $template_id ) {
		return false;
	}

	/**
	 * Get remote template data.
	 *
	 * @since 0.0.2
	 * @access public
	 *
	 * @param array $args Custom template arguments.
	 * @param string $context Optional. The context. Default is `display`.
	 *
	 * @return array Remote Template data.
	 */
	public function get_data( array $args, $context = 'display' ) {
		// we're requesting data for a template ID, but we're unsure what collection it is in.
		$data        = [];
		$template_id = $args['template_id'];

		$all           = $this->get_items();
		$collection_id = false;
		foreach ( $all as $template ) {
			if ( ! empty( $template['template_id'] ) && $template['template_id'] === $template_id ) {
				$collection_id = $template['collection_id'];
			}
		}

		if ( $template_id && $collection_id ) {
			// we're good to pull this data down from our API.
			$collection        = new \Envato_Elements\Collection_Elementor_Blocks();
			$local_template_id = $collection->schedule_remote_template_install( $collection_id, $template_id );
			if ( ! is_wp_error( $local_template_id ) && $local_template_id ) {
				$result = $collection->install_remote_template( $collection_id, $template_id );

				if ( $result && ! is_wp_error( $result ) && ! empty( $result['post_id'] ) ) {
					$document = Plugin::$instance->documents->get( $result['post_id'] );
					if ( $document ) {
						$data['content'] = $document->get_elements_raw_data( null, true );
					}
				}
			}
		}

		return $data;
	}
}