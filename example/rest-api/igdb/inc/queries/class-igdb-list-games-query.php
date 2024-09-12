<?php

namespace RemoteDataBlocks\Example\ArtInstituteOfChicago;

use RemoteDataBlocks\Config\HttpDatasourceConfig;
use RemoteDataBlocks\Config\QueryContext;

class IGDBListGamesQuery extends QueryContext {
	public array $input_variables = [
		'search_terms' => [
			'type' => 'string',
		],
	];

	public array $output_variables = [];

	public function __construct( HttpDatasourceConfig $datasource ) {
		parent::__construct( $datasource );

		// Defining the output variables in the constructor allows us to provide
		// a generate function instead of a JSONPath selector.
		$this->output_variables = [
			'root_path'     => '$',
			'is_collection' => true,
			'mappings'      => [
				'id'        => [
					'name' => 'Art ID',
					'path' => '$.id',
					'type' => 'id',
				],
			]
		];
			// 	'title'     => [
			// 		'name' => 'Title',
			// 		'path' => '$.title',
			// 		'type' => 'string',
			// 	],
			// 	'image'     => [
			// 		'name' => 'Image',
			// 		'path' => '$.thumbnail.lqip',
			// 		'type' => 'image_url',
			// 	],
			// 	'image_url' => [
			// 		'name'     => 'Image URL',
			// 		'generate' => function ( $data ) {
			// 			return 'https://www.artic.edu/iiif/2/' . $data['data']['image_id'] . '/full/843,/0/default.jpg';
			// 		},
			// 		'type'     => 'image_url',
			// 	],
			// ],
		// ];
	}

	public function get_request_method(): string {
		return 'POST';
	}

	public function get_endpoint( $input_variables ): string {
		$query    = $input_variables['search_terms'];
		$endpoint = $this->get_datasource()->get_endpoint() . '/games';

		return add_query_arg( [ 'q' => $query ], $endpoint );
	}
}