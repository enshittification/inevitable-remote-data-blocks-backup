<?php

namespace RemoteDataBlocks\Example\Airtable\Events;

use RemoteDataBlocks\Config\QueryContext\HttpQueryContext;

class AirtableGetEventQuery extends HttpQueryContext {
	public array $input_variables = [
		'record_id' => [
			'name'      => 'Record ID',
			'overrides' => [
				[
					'target' => 'utm_content',
					'type'   => 'query_var',
				],
			],
			'type'      => 'id',
		],
	];

	public array $output_variables = [
		'is_collection' => false,
		'mappings'      => [
			'id'       => [
				'name' => 'Record ID',
				'path' => '$.id',
				'type' => 'id',
			],
			'title'    => [
				'name' => 'Title',
				'path' => '$.fields.Activity',
				'type' => 'string',
			],
			'location' => [
				'name' => 'Location',
				'path' => '$.fields.Location',
				'type' => 'string',
			],
			'type'     => [
				'name' => 'Type',
				'path' => '$.fields.Type',
				'type' => 'string',
			],
		],
	];

	/**
	 * Airtable API endpoint for fetching a single event.
	 */
	public function get_endpoint( array $input_variables ): string {
		return $this->get_datasource()->get_endpoint() . '/' . $input_variables['record_id'];
	}

	public function get_query_name(): string {
		return 'Get event';
	}
}
