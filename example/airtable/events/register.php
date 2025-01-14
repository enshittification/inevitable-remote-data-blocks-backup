<?php

namespace RemoteDataBlocks\Example\Airtable\Events;

use RemoteDataBlocks\Integrations\Airtable\AirtableDatasource;

require_once __DIR__ . '/inc/queries/class-airtable-get-event-query.php';
require_once __DIR__ . '/inc/queries/class-airtable-list-events-query.php';

function register_airtable_events_block() {
	$block_name   = 'Conference Event';
	$access_token = \RemoteDataBlocks\Example\get_access_token( 'airtable_events' );
	$base         = 'appVQ2PAl95wQSo9S';
	$table        = 'tblyGtuxblLtmoqMI';


	$airtable_datasource        = new AirtableDatasource( $access_token, $base, $table );
	$airtable_get_event_query   = new AirtableGetEventQuery( $airtable_datasource );
	$airtable_list_events_query = new AirtableListEventsQuery( $airtable_datasource );

	register_remote_data_block( $block_name, $airtable_get_event_query );
	register_remote_data_list_query( $block_name, $airtable_list_events_query );
	register_remote_data_loop_block( 'Airtable Event List', $airtable_list_events_query );
	register_remote_data_page( $block_name, 'airtable-event' );
}

add_action( 'init', __NAMESPACE__ . '\\register_airtable_events_block' );
