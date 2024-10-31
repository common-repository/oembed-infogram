<?php
/**
 * @wordpress-plugin
 * Plugin Name:       oEmbed Infogram
 * Description:       A simple plugin that adds support for embedding Infogram.
 * Version:           1.1.3
 * Plugin URI:        https://github.com/android-com-pl/oembed-infogram
 * Author:            android.com.pl
 * Author URI:        https://android.com.pl/
 * License:           GPL v3
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.html
 */

namespace ACP\oEmbed;

defined( 'ABSPATH' ) || die;

class Infogram {
	/**
	 * The unique instance of the plugin
	 * @var ?Infogram
	 */
	protected static ?Infogram $instance = null;

	/**
	 * Gets an instance of plugin
	 * @return Infogram
	 */
	public static function get_instance(): Infogram {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	private function __construct() {
		add_action( 'init', [ $this, 'add_provider' ] );
		add_filter( 'amp_content_embed_handlers', [ $this, 'add_amp_handler' ], 10, 2 );
		add_filter( 'plugin_row_meta', [ $this, 'add_plugin_row_meta_links' ], 10, 4 );
	}

	public function add_provider(): void {
		wp_oembed_add_provider( 'https://infogram.com/*', 'https://infogram.com/oembed/?format=json' );
	}

	/**
	 * AMP Support
	 *
	 * @param array $handler_classes
	 *
	 * @return array
	 * @see https://amp-wp.org/documentation/playbooks/custom-embed-handler/
	 */
	public function add_amp_handler( array $handler_classes ): array {
		require_once( plugin_dir_path( __FILE__ ) . 'class-amp-infogram-oembed-handler.php' );
		$handler_classes[ __NAMESPACE__ . '\\Infogram_Embed_Handler' ] = [];

		return $handler_classes;
	}

	/**
	 * Adds elements to the plugin's meta row
	 *
	 * @param string[] $plugin_meta
	 * @param string $plugin_file
	 * @param array $plugin_data
	 * @param string $status
	 *
	 * @return array
	 */
	public function add_plugin_row_meta_links( array $plugin_meta, string $plugin_file, array $plugin_data, string $status ): array {
		if ( str_contains( $plugin_file, basename( __FILE__ ) ) ) {
			$plugin_meta[] = '<a href="https://github.com/android-com-pl/oembed-infogram">GitHub</a>';
			$plugin_meta[] = sprintf(
				'<a href="https://github.com/sponsors/android-com-pl">%s</a>',
				__( 'Donate', 'oembed-infogram' )
			);
		}

		return $plugin_meta;
	}
}

$oembed_infogram = Infogram::get_instance();
