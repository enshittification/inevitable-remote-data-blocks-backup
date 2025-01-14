<?php

namespace RemoteDataBlocks\Example\Airtable\EldenRingMap;

use RemoteDataBlocks\Config\QueryContext\HttpQueryContext;

class AirtableEldenRingListLocationsQuery extends HttpQueryContext {
	public array $input_variables = [
		'map_name' => [
			'type' => 'string',
		],
	];

	public array $output_variables = [
		'root_path'     => '$.records[*]',
		'is_collection' => true,
		'mappings'      => [
			'id'    => [
				'name' => 'Location ID',
				'path' => '$.id',
				'type' => 'id',
			],
			'title' => [
				'name' => 'Name',
				'path' => '$.fields.Name',
				'type' => 'string',
			],
			'x'     => [
				'name' => 'x',
				'path' => '$.fields.x',
				'type' => 'string',
			],
			'y'     => [
				'name' => 'y',
				'path' => '$.fields.y',
				'type' => 'string',
			],
		],
	];

	public function get_endpoint( array $input_variables ): string {
		return $this->get_datasource()->get_endpoint( 'locations' ) . '?filterByFormula=FIND%28%27' . $input_variables['map_name'] . '%27%2C%20%7BMap%7D%29%3E0';
	}
}
