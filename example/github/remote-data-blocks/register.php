<?php

namespace RemoteDataBlocks\Example\GitHub;

use RemoteDataBlocks\Logging\LoggerManager;

require_once __DIR__ . '/inc/queries/class-github-datasource.php';
require_once __DIR__ . '/inc/queries/class-github-get-file-as-html-query.php';
require_once __DIR__ . '/inc/queries/class-github-list-files-query.php';

function register_github_file_as_html_block() {
	$repo_owner = 'Automattic';
	$repo_name  = 'remote-data-blocks';
	$branch     = 'trunk';

	$block_name = sprintf( 'GitHub File As HTML (%s/%s)', $repo_owner, $repo_name );

	$github_datasource             = new GitHubDatasource( $repo_owner, $repo_name, $branch );
	$github_get_file_as_html_query = new GitHubGetFileAsHtmlQuery( $github_datasource );
	$github_get_list_files_query   = new GitHubListFilesQuery( $github_datasource, '.md' );

	register_remote_data_block( $block_name, $github_get_file_as_html_query );
	register_remote_data_list_query( $block_name, $github_get_list_files_query );

	$block_pattern1 = file_get_contents( __DIR__ . '/inc/patterns/file-picker.html' );
	$block_pattern2 = file_get_contents( __DIR__ . '/inc/patterns/file-render.html' );
	register_remote_data_block_pattern( $block_name, 'remote-data-blocks/github-file-picker', $block_pattern1, [
		'title'    => 'GitHub File Picker',
		'inserter' => false,
	] );
	register_remote_data_block_pattern( $block_name, 'remote-data-blocks/github-file-render', $block_pattern2, [ 'title' => 'GitHub File Render' ] );

	$logger = LoggerManager::instance();
	$logger->info( sprintf( 'Registered %s block (branch: %s)', $block_name, $branch ) );
}
add_action( 'init', __NAMESPACE__ . '\\register_github_file_as_html_block' );
