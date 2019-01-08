<?php
/**
 * Collections: Collection_Photos class
 *
 * This class is used to manage collections of Elements photos.
 *
 * @package Envato/Envato_Elements
 * @since 0.0.2
 */

namespace Envato_Elements;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}


/**
 * Photo Collection management.
 *
 * @since 0.0.2
 *
 * @see Collection
 */
class Collection_Photos extends Collection {

	/**
	 * Collection_Photos constructor.
	 */
	public function __construct() {
		parent::__construct();
		$this->category = 'photos';
	}

	/**
	 * Get photo collections from Elements API.
	 *
	 * @return array
	 */
	public function get_remote_collections() {

		$api_data = [
			'beta' => true,
		];

		Feedback::get_instance()->page_view( 'all-photos' );

		$data = API::get_instance()->api_call( 'v1/photos', $api_data );

		if ( $data && ! is_wp_error( $data ) && ! empty( $data['groups'] ) ) {
			// filter api response data?
			$filtered_data = [];
			foreach ( $data['groups'] as $collection ) {
				if ( ! empty( $collection['photos'] ) ) {
					$filtered_templates = [];
					foreach ( $collection['photos'] as $template ) {
						$filtered_templates[] = $this->filter_template( $template, $collection );
					}
					if ( $filtered_templates ) {
						$filtered_collection              = $this->filter_collection( $collection );
						$filtered_collection['templates'] = $filtered_templates;
						$filtered_data[]                  = $filtered_collection;
					}
				}
			}
			$data = [
				'data' => [
					'feedback'    => Feedback::get_instance()->generate_form( 'photos' ),
					'collections' => $filtered_data,
				],
			];

		}

		return $data;

	}


	/**
	 * Filter our list of installed template data.
	 *
	 * @param array $api_data Raw API data.
	 * @param array $catagory_data Parent category API data.
	 *
	 * @return array
	 */
	private function filter_template( $api_data, $catagory_data ) {

		$filtered_data = [
			'templateId'            => $api_data['id'],
			'previewThumb'          => $api_data['thumb'],
			'previewThumbAspect'    => '100%',
			'templateName'          => $api_data['name'],
			'templateUrl'           => add_query_arg(
				[
					'category'      => $this->category,
					'collection_id' => $catagory_data['slug'],
					'photo_id'      => $api_data['id'],
				], Collection::get_instance()->get_url()
			),
			'templateError'         => false,
			'templateInstalled'     => false,
			'templateInstalledURL'  => '#',
			'templateInstalledText' => 'Open Image',
			'largeThumb'            => [
				'src'    => $api_data['thumb'],
				'width'  => 'auto',
				'height' => 'auto',
			],
		];

		return $filtered_data;
	}

	/**
	 * Filter a list of installed collection data.
	 *
	 * @param array $api_data Raw API data.
	 *
	 * @return array
	 */
	private function filter_collection( $api_data ) {
		$filtered_data = [
			'collectionId'   => $api_data['slug'],
			'categorySlug'   => $this->category,
			'collectionName' => $api_data['name'],
			'collectionUrl'  => add_query_arg(
				[
					'category'      => $this->category,
					'collection_id' => $api_data['slug'],
				], Collection::get_instance()->get_url()
			),
			'templates'      => [],
		];

		return $filtered_data;
	}

}
