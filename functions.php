<?php
/**
 * Plugin Name: DevDiggers MultiPOS for WooCommerce
 * Description: Make your WooCommerce store work like a real shop. Sell in person, keep stock synced, run your retail or restaurant outlet, and keep selling even if the internet goes slow.
 * Plugin URI: https://devdiggers.com/product/multipos-point-of-sale-for-woocommerce/
 * Author: DevDiggers
 * Author URI: https://devdiggers.com/
 * Version: 1.0.0
 * Text Domain: devdiggers-multipos-for-woocommerce
 * Domain Path: /i18n
 * WC requires at least: 9.5.0
 * WC tested up to: 10.8.1
 * WP requires at least: 6.0.0
 * WP tested up to: 7.0
 * DevDiggersPrefix: ddwcpos
 * Requires Plugins: woocommerce
 * License: GNU General Public License v3.0
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 *
 * @package MultiPOS - Point of Sale for WooCommerce
 */

// ddwcpos: MultiPOS - Point of Sale for WooCommerce.
use DDWCMultiPOS\Includes\DDWCPOS_File_Handler;
use DDWCMultiPOS\API\DDWCPOS_API_Register_Routes;

defined( 'ABSPATH' ) || exit();

// Define Constants.
defined( 'DEVDIGGERS_FREE_PLUGIN' ) || define( 'DEVDIGGERS_FREE_PLUGIN', true );

if ( ! class_exists( 'DDWCPOS_Free_Init' ) ) {
	/**
	 * Free Init class
	 */
	final class DDWCPOS_Free_Init {
		/**
		 * Instance variable
		 *
		 * @var DDWCPOS_Free_Init|null
		 */
		private static $_instance = null;

		/**
		 * Class constructor
		 */
		public function __construct() {
			add_action( 'init', [ $this, 'ddwcpos_init' ] );
			add_action( 'woocommerce_init', [ $this, 'ddwcpos_woocommerce_init' ] );
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), [ $this, 'ddwcpos_plugin_settings_link' ] );
			add_filter( 'plugin_row_meta', [ $this, 'ddwcpos_plugin_row_meta' ], 10, 2 );
		}

		/**
		 * Create a plugin instance.
		 *
		 * @return static
		 * @since 1.0.0
		 */
		public static function get_instance() {
			if ( null === self::$_instance ) {
				self::$_instance = new self();

				/**
				 * Action hook fired when the main plugin instance is loaded.
				 *
				 * @since 1.0.0
				 */
				do_action( 'ddwcpos_loaded' );
			}

			return self::$_instance;
		}

		/**
		 * WooCommerce Init function
		 *
		 * @return void
		 */
		public function ddwcpos_woocommerce_init() {
			require_once DDWCPOS_PLUGIN_FILE . 'autoload/autoload.php';
			require_once DDWCPOS_PLUGIN_FILE . 'includes/globals-variables.php';

			global $ddwcpos_configuration;
			new DDWCPOS_API_Register_Routes( $ddwcpos_configuration );
		}

		/**
		 * Init function
		 *
		 * @return void
		 */
		public function ddwcpos_init() {
			// WordPress.org loads plugin translations automatically.

			if ( ! class_exists( 'WooCommerce' ) ) {
				add_action( 'admin_notices', function () {
					?>
					<div class="error">
						<p>
							<?php
							/* translators: %1$s for a opening tag and %2$s for a closing tag */
							echo sprintf( esc_html__( 'MultiPOS - Point of Sale for WooCommerce is activated but not effective. It requires %1$sWooCommerce Plugin%2$s in order to use its functionalities.', 'devdiggers-multipos-for-woocommerce' ), '<a href="' . esc_url( '//wordpress.org/plugins/woocommerce/' ) . '" target="_blank">', '</a>' );
							?>
						</p>
					</div>
					<?php
				} );
			} else {
				require_once DDWCPOS_PLUGIN_FILE . 'autoload/autoload.php';
				require_once DDWCPOS_PLUGIN_FILE . 'includes/globals-variables.php';
				new DDWCPOS_File_Handler();

				// Initialize review notice if framework is available.
				if ( class_exists( '\DevDiggers\Framework\Includes\DDFW_Review_Notice' ) ) {
					new \DevDiggers\Framework\Includes\DDFW_Review_Notice( [
						'plugin_name'   => esc_html__( 'MultiPOS - Point of Sale for WooCommerce', 'devdiggers-multipos-for-woocommerce' ),
						'plugin_prefix' => 'ddwcpos',
						'review_url'    => 'https://wordpress.org/support/plugin/devdiggers-multipos-for-woocommerce/reviews/#new-post',
					] );
				}
			}
		}

		/**
		 * Plugin settings link
		 *
		 * @param array $links Links Array.
		 * @return array $links
		 */
		public function ddwcpos_plugin_settings_link( $links ) {
			ob_start();
			?>
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=ddwcpos-dashboard' ) ); ?>"><?php esc_html_e( 'Dashboard', 'devdiggers-multipos-for-woocommerce' ); ?></a>
			|
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=ddwcpos-dashboard&menu=configuration' ) ); ?>"><?php esc_html_e( 'Configuration', 'devdiggers-multipos-for-woocommerce' ); ?></a>
			|
			<a href="<?php echo esc_url( admin_url( 'admin.php?page=ddwcpos-dashboard&setup-wizard=true' ) ); ?>"><?php esc_html_e( 'Setup Wizard', 'devdiggers-multipos-for-woocommerce' ); ?></a>
			|
			<a href="//devdiggers.com/product/multipos-point-of-sale-for-woocommerce/" style="color: #0256ff; font-weight: bold;" target="_blank"><?php esc_html_e( 'Upgrade to Pro', 'devdiggers-multipos-for-woocommerce' ); ?></a>
			<?php
			$new_links = ob_get_clean();
			array_unshift( $links, $new_links );
			return $links;
		}

		/**
		 * Plugin Doc link
		 *
		 * @param array  $links Links.
		 * @param string $file File name.
		 * @return array $links
		 */
		public function ddwcpos_plugin_row_meta( $links, $file ) {
			if ( plugin_basename( __FILE__ ) === $file ) {
				$row_meta = [
					'support'       => '<a href="//devdiggers.com/contact/" aria-label="' . esc_attr__( 'Support', 'devdiggers-multipos-for-woocommerce' ) . '">' . esc_html__( 'Support', 'devdiggers-multipos-for-woocommerce' ) . '</a>',
					'documentation' => '<a href="//devdiggers.com/multipos-point-of-sale-for-woocommerce/" aria-label="' . esc_attr__( 'Documentation', 'devdiggers-multipos-for-woocommerce' ) . '">' . esc_html__( 'Documentation', 'devdiggers-multipos-for-woocommerce' ) . '</a>',
					'review'        => '<a href="//wordpress.org/support/plugin/devdiggers-multipos-for-woocommerce/reviews/#new-post" target="_blank" title="' . esc_attr__( 'Review', 'devdiggers-multipos-for-woocommerce' ) . '" aria-label="' . esc_attr__( 'Review', 'devdiggers-multipos-for-woocommerce' ) . '"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 192 32" height="10"><path d="M16 26.534L6.111 32 8 20.422l-8-8.2 11.056-1.688L16 0l4.944 10.534L32 12.223l-8 8.2L25.889 32zm40 0L46.111 32 48 20.422l-8-8.2 11.056-1.688L56 0l4.944 10.534L72 12.223l-8 8.2L65.889 32zm40 0L86.111 32 88 20.422l-8-8.2 11.056-1.688L96 0l4.944 10.534L112 12.223l-8 8.2L105.889 32zm40 0L126.111 32 128 20.422l-8-8.2 11.056-1.688L136 0l4.944 10.534L152 12.223l-8 8.2L145.889 32zm40 0L166.111 32 168 20.422l-8-8.2 11.056-1.688L176 0l4.944 10.534L192 12.223l-8 8.2L185.889 32z" fill="#F5A623" fill-rule="evenodd"/></svg></a>',
				];
				$links = array_merge( $links, $row_meta );
			}

			return $links;
		}
	}
}

// Load DevDiggers Framework if not loaded already.
add_action( 'plugins_loaded', function() {
	if ( ! class_exists( 'DDWCPOS_Init' ) ) {
		defined( 'DDWCPOS_PLUGIN_FILE' ) || define( 'DDWCPOS_PLUGIN_FILE', plugin_dir_path( __FILE__ ) );
		defined( 'DDWCPOS_PLUGIN_URL' ) || define( 'DDWCPOS_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		// Load Free version.
		DDWCPOS_Free_Init::get_instance();

		// Load DevDiggers Framework if not loaded already.
		if ( ! defined( 'DDFW_LOADED' ) && file_exists( DDWCPOS_PLUGIN_FILE . 'devdiggers-framework/init.php' ) ) {
			$should_load = true;

			// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Page is read-only admin routing input.
			if ( ! empty( $_GET['page'] ) ) {
				// phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Page is read-only admin routing input.
				$current_page = sanitize_text_field( wp_unslash( $_GET['page'] ) );
				$prefix       = explode( '-', $current_page )[0];

				if ( 0 === strpos( $prefix, 'ddwc' ) || 0 === strpos( $prefix, 'ddwp' ) ) {
					$pro_class  = strtoupper( $prefix ) . '_Init';
					$free_class = strtoupper( $prefix ) . '_Free_Init';

					if ( class_exists( $free_class ) && ! class_exists( $pro_class ) && 'ddwcpos' !== $prefix ) {
						$should_load = false;
					}
				}
			}

			if ( $should_load ) {
				require DDWCPOS_PLUGIN_FILE . 'devdiggers-framework/init.php';
			}
		}
	}
}, 10 );

// For HPOS Compatibility
add_action( 'before_woocommerce_init', function() {
	if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
		\Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
	}
} );

require_once plugin_dir_path( __FILE__ ) . 'includes/install.php';
register_activation_hook( __FILE__, [ 'DDWCPOS_Install', 'ddwcpos_on_plugin_activation' ] );
