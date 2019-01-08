<?php
/**
 * Envato Elements: Elementor
 *
 * Elementor template display/import.
 *
 * @package Envato/Envato_Elements
 * @since 0.0.2
 */

namespace Envato_Elements;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Collection registration and management.
 *
 * @since 0.0.9
 */
class Collection_Elementor_Blocks extends Collection_Elementor {

	public function __construct() {
		parent::__construct();
		$this->category = 'elementor-blocks';
	}

	public function get_all_blocks() {
		$api_data = [
			'category' => $this->category,
		];

		return API::get_instance()->api_call( 'v2/blocks', $api_data );
	}

	public function get_remote_collections( $search = [] ) {

		$page_number = empty( $search['pg'] ) || (int) $search['pg'] < 1 || (int) $search['pg'] > 100 ? 1 : (int) $search['pg'];
		$filters     = ! empty( $search['filters'] ) && is_array( $search['filters'] ) ? $search['filters'] : [];
		$per_page    = 30;

		$all_blocks = $this->get_all_blocks();

		$imported_templates = CPT_Kits::get_instance()->get_imported_templates();

		if ( $all_blocks && ! is_wp_error( $all_blocks ) && ! empty( $all_blocks['data'] ) ) {
			$total_block_results = count( $all_blocks['data'] );
			// First we extract any filters.
			$blocks_to_filter = $all_blocks['data'];
			// Clean up filters
			foreach ( $filters as $filter_key => $filter_value ) {
				if ( empty( $filter_value ) ) {
					unset( $filters[ $filter_key ] );
				}
			}
			if ( ! empty( $filters ) && ! empty( $filters['type'] ) ) {
				foreach ( $blocks_to_filter as $block_id => $block ) {
					$has_filter_match = false;
					if ( ! empty( $block['type'] ) && isset( $block['type'][ $filters['type'] ] ) ) {
						$has_filter_match = true;
					}
					if ( ! $has_filter_match ) {
						unset( $blocks_to_filter[ $block_id ] );
					}
				}
			}
			// Update: We group these blocks by type.
			$grouped_blocks = [];
			if ( ! empty( $all_blocks['meta']['filters']['type'] ) ) {
				foreach ( $all_blocks['meta']['filters']['type'] as $type_slug => $type ) {
					if ( $type['count'] > 0 ) {
						$grouped_blocks[ $type_slug ] = [
							'title'  => $type['name'],
							'slug'   => $type_slug,
							'blocks' => [],
						];
					}
				}
			}
			foreach ( $blocks_to_filter as $block ) {
				$block_types = ! empty( $block['type'] ) ? $block['type'] : false;
				if ( $block_types && is_array( $block_types ) ) {
					foreach ( $block_types as $block_type => $block_type_name ) {
						if ( isset( $grouped_blocks[ $block_type ] ) ) {
							$grouped_blocks[ $block_type ]['blocks'][] = $block;
						}
					}
				}
			}
			// Remove any groups that no longer have results.
			foreach ( $grouped_blocks as $block_type => $grouped_block ) {
				if ( ! count( $grouped_block['blocks'] ) ) {
					unset( $grouped_blocks[ $block_type ] );
				}
			}
			$filtered_block_count = count( $grouped_blocks );
			unset( $blocks_to_filter );
			$paged_data = array_slice( $grouped_blocks, ( $page_number - 1 ) * $per_page, $per_page );
			if ( $paged_data ) {
				foreach ( $paged_data as $page_id => $block_grouping ) {
					foreach ( $block_grouping['blocks'] as $block_id => $block ) {
						$filtered_block = $this->filter_template( $block, [ 'collectionId' => $block['collection_id'] ] );
						if ( $filtered_block ) {
							$filtered_block['collectionId'] = $block['collection_id'];
							foreach ( $imported_templates as $imported_template ) {
								if ( $imported_template['categorySlug'] === $this->category && $imported_template['collectionId'] === $filtered_block['collectionId'] && $imported_template['templateId'] === $filtered_block['templateId'] ) {
									if ( ! empty( $imported_template['imported'] ) ) {
										// Todo: store global query of all library `_source_kit_id` items so we don't query on each one.
										$cc_args               = [
											'posts_per_page' => - 1,
											'post_type'      => 'elementor_library',
											'meta_key'       => '_source_kit_id',
											'meta_value'     => $imported_template['ID'],
										];
										$has_elementor_library = false;
										$cc_query              = new \WP_Query( $cc_args );
										if ( $cc_query->have_posts() ) {
											$posts = $cc_query->get_posts();
											$post  = current( $posts );
											if ( $post && $post->ID ) {
												$imported_template['ID'] = $post->ID;
												$has_elementor_library   = true;
											}
										}

										if ( $has_elementor_library ) {
											$filtered_block['templateInstalled']    = true;
											$filtered_block['templateInstalledID']  = $imported_template['ID'];
											$filtered_block['templateInstalledURL'] = $this->edit_post_link( $imported_template['ID'] );
											$filtered_block['templateInstalleText'] = Category::get_instance()->get_current( $this->category )->edit_button;
										}
									}

									// We also return the "Inserted Template" details so our template can choose to display this information.
									if ( ! empty( $imported_template['inserted'] ) ) {
										$filtered_block['templateInserted'] = $imported_template['inserted'];
									}
								}
							}
							$paged_data[ $page_id ]['blocks'][ $block_id ] = $filtered_block;
						} else {
							// Failed to filter block data?
							unset( $paged_data[ $page_id ]['blocks'][ $block_id ] );
						}
					}
				}
			}
			$all_blocks['data'] = [
				'page_number'   => $page_number,
				'per_page'      => $per_page,
				'all_results'   => $total_block_results, // All results, unfiltered.
				'total_results' => $filtered_block_count, // Results, filtered.
				'blocks'        => array_values( $paged_data ),
			];

			// Sort our meta keys alphabetically.
			if ( isset( $all_blocks['meta']['filters']['type'] ) ) {
				// array_multisort( array_keys( $all_blocks['meta']['filters']['type'] ), SORT_NATURAL | SORT_FLAG_CASE, $all_blocks['meta']['filters']['type'] );
			}
		}

		return $all_blocks;

	}


	public function install_remote_template( $collection_id, $template_id, $options = [] ) {
		$options['skip_title']             = true;
		$options['elementor_library_type'] = 'section';
		return parent::install_remote_template( $collection_id, $template_id, $options );
	}

}
