<?php
/**
 * Plugin Name: Login Registration Kit
 * Contributors: crumina
 * Tags: login form, frontend login, registration, frontend registration, user profile
 * Requires at least: 5.5
 * Requires PHP: 7.0
 * Tested up to: 5.8.1
 * Version: 1.1
 * Description: Simply great frontend login and registration tool. We created it for us but think it will helpful for you too. Main plugin purpose - create beautiful forms and popups for simple frontend login and registration. And creating simple User dashboard.
 * Author: Crumina team
 * Author URI: https://crumina.net/
 * Plugin URI: https://crumina.net/wp-plugins/registration-kit/
 * License: GNU General Public License v3 or later
 * License URI: http://www.gnu.org/licenses/gpl-3.0.html
 * Text Domain: login-registration-kit
 * Domain Path: /languages
 *
 * @package LoginRegistrationKit
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

if ( ! class_exists( 'LoginRegistrationKit' ) ) :

	/**
	 * Main LoginRegistrationKit Class.
	 *
	 * @class   LoginRegistrationKit
	 * @version 1.1.0
	 */
	final class LoginRegistrationKit {
		/**
		 * Instance of this class.
		 *
		 * @var object
		 */
		protected static $_instance = null;

		/**
		 * Return an instance of this class.
		 *
		 * @return object A single instance of this class.
		 */
		public static function instance() {
			// If the single instance hasn't been set, set it now.
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}

			return self::$_instance;
		}

		/**
		 * LoginRegistrationKit Constructor.
		 */
		public function __construct() {
			$this->define_constants();
			$this->includes();
		}

		/**
		 * Define FT Constants.
		 */
		private function define_constants() {
			$upload_dir = wp_upload_dir();
			$this->define( 'LRK_PLUGIN_FILE', __FILE__ );
			$this->define( 'LRK_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
			$this->define( 'LRK_PLUGIN_URL', plugins_url( '', __FILE__ ) );
			$this->define( 'LRK_DS', DIRECTORY_SEPARATOR );
			$this->define( 'LRK_ABSPATH', dirname( __FILE__ ) . LRK_DS );
			$this->define( 'LRK_VERSION', '1.1.0' );

			load_plugin_textdomain( 'login-registration-kit', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		}

		/**
		 * Define constant if not already set.
		 *
		 * @param string $name
		 * @param string|bool $value
		 */
		private function define( $name, $value ) {
			if ( ! defined( $name ) ) {
				define( $name, $value );
			}
		}

		/**
		 * Includes.
		 */
		private function includes() {
			if ( is_admin() ) {
				// Main admin class
				include_once LRK_ABSPATH . 'includes/admin/class-lrk-admin.php';
			}

			// Main frontend class
			include_once LRK_ABSPATH . 'includes/frontend/class-lrk-frontend.php';
		}
	}

	/**
	 * Returns one instance
	 *
	 * @since 1.0.0
	 * @return object
	 */
	function LRK() {
		return LoginRegistrationKit::instance();
	}

	LRK();
endif;