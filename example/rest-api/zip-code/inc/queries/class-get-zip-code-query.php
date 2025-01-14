<?php

namespace RemoteDataBlocks\Example\ZipCode;

use RemoteDataBlocks\Config\QueryContext\HttpQueryContext;

class GetZipCodeQuery extends HttpQueryContext {
	public array $input_variables = [
		'zip_code' => [
			'type' => 'string',
		],
	];

	public array $output_variables = [
		'is_collection' => false,
		'mappings'      => [
			'zip_code' => [
				'name' => 'Zip Code',
				'path' => '$["post code"]',
				'type' => 'string',
			],
			'city'     => [
				'name' => 'City',
				'path' => '$.places[0]["place name"]',
				'type' => 'string',
			],
			'state'    => [
				'name' => 'State',
				'path' => '$.places[0].state',
				'type' => 'string',
			],
		],
	];

	public function get_endpoint( $input_variables ): string {
		return $this->get_datasource()->get_endpoint() . $input_variables['zip_code'];
	}
}
