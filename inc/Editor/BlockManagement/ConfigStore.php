<?php

namespace RemoteDataBlocks\Editor\BlockManagement;

defined( 'ABSPATH' ) || exit();

use RemoteDataBlocks\Logging\LoggerManager;
use Psr\Log\LoggerInterface;
use RemoteDataBlocks\Config\Datasource\CompatibleHttpDatasource;
use RemoteDataBlocks\Config\QueryContext\HttpQueryContext;

use function sanitize_title;

class ConfigStore {
	private static array $configurations;
	private static LoggerInterface $logger;

	public static function init( LoggerInterface $logger = null ): void {
		self::$configurations = [];
		self::$logger         = $logger ?? LoggerManager::instance();
	}

	/**
	 * Convert a block title to a block name. Mainly this is to reduce the burden
	 * of configuration and to ensure that block names are unique (since block
	 * titles must be unique).
	 *
	 * @param string $block_title
	 * @return string
	 */
	public static function get_block_name( string $block_title ): string {
		return 'remote-data-blocks/' . sanitize_title( $block_title );
	}

	/**
	 * Get all registered block names.
	 *
	 * @return string[]
	 */
	public static function get_block_names(): array {
		return array_keys( self::$configurations );
	}

	/**
	 * Get the configuration for a block.
	 *
	 * @param string $block_name
	 * @return array|null
	 */
	public static function get_configuration( string $block_name ): ?array {
		if ( ! self::is_registered_block( $block_name ) ) {
			self::$logger->error( sprintf( 'Block %s has not been registered', $block_name ) );
			return null;
		}

		return self::$configurations[ $block_name ];
	}

	/**
	 * Set or update the configuration for a block.
	 *
	 * @param string $block_name
	 * @param array $config
	 * @return void
	 */
	public static function set_configuration( string $block_name, array $config ): void {
		// @TODO: Validate config shape.
		self::$configurations[ $block_name ] = $config;
	}

	/**
	 * Check if a block is registered.
	 *
	 * @param string $block_name
	 * @return bool
	 */
	public static function is_registered_block( string $block_name ): bool {
		return isset( self::$configurations[ $block_name ] );
	}

	/**
	 * Return an array of data sources that are compatible with the dynamic data
	 * sources generated by DatasoureCrud. This allows us to display a representation
	 * of them in the settings screen.
	 *
	 * @return HttpDatasource[]
	 */
	public static function get_compatible_data_sources(): array {
		$compatible_data_sources = [];

		foreach ( self::$configurations as $config ) {
			foreach ( $config['queries'] as $query ) {
				if ( ! $query instanceof HttpQueryContext ) {
					continue;
				}

				$data_source = $query->get_datasource();

				if ( ! $data_source instanceof CompatibleHttpDatasource ) {
					continue;
				}

				// Get the object representation of the data source, which is compatible
				// with the settings screen. Queries can be reused, so filter for unique
				// data sources using the slug.
				$compatible_data_sources = array_merge(
					$compatible_data_sources,
					$data_source->get_object_representations()
				);
			}
		}

		return array_values( $compatible_data_sources );
	}
}
