<?php
/**
 * Plugin Name: Primary Redirect
 * Plugin URI: https://handyplugins.co
 * Description: Redirects users to a custom URL or their primary blog's dashboard after login, replacing the default WordPress behavior.
 * Version: 2.0
 * Requires at least: 5.0
 * Requires PHP: 7.4
 * Author: HandyPlugins
 * Author URI: https://handyplugins.co/
 * License: GPL v2 or later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: primary-redirect
 * Domain Path: /languages
 *
 * @package PrimaryRedirect
 */

// Prevent direct access.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Define plugin constants.
define( 'PRIMARY_REDIRECT_VERSION', '2.0' );
define( 'PRIMARY_REDIRECT_PLUGIN_FILE', __FILE__ );
define( 'PRIMARY_REDIRECT_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'PRIMARY_REDIRECT_PLUGIN_URL', plugin_dir_url( __FILE__ ) );

/**
 * Main Primary Redirect Class
 *
 * @since 2.0.0
 */
class Primary_Redirect {

	/**
	 * Plugin instance.
	 *
	 * @since 2.0.0
	 * @var Primary_Redirect
	 */
	private static $instance = null;

	/**
	 * Get plugin instance.
	 *
	 * @since 2.0.0
	 * @return Primary_Redirect
	 */
	public static function get_instance() {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}

		return self::$instance;
	}

	/**
	 * Constructor.
	 *
	 * @since 2.0.0
	 */
	private function __construct() {
		$this->init();
	}

	/**
	 * Initialize the plugin.
	 *
	 * @since 2.0.0
	 */
	private function init() {
		add_action( 'plugins_loaded', array( $this, 'load_textdomain' ) );
		add_filter( 'login_redirect', array( $this, 'handle_login_redirect' ), 10, 3 );

		if ( is_multisite() ) {
			add_action( 'wpmu_options', array( $this, 'render_network_settings' ) );
			add_action( 'update_wpmu_options', array( $this, 'update_network_settings' ) );
		} else {
			add_action( 'admin_init', array( $this, 'register_single_site_settings' ) );
		}
	}

	/**
	 * Load plugin textdomain.
	 *
	 * @since 2.0.0
	 */
	public function load_textdomain() {
		load_plugin_textdomain(
			'primary-redirect',
			false,
			dirname( plugin_basename( __FILE__ ) ) . '/languages/'
		);
	}

	/**
	 * Handle login redirect.
	 *
	 * @since 2.0.0
	 * @param string  $redirect_to           The redirect destination URL.
	 * @param string  $requested_redirect_to The requested redirect destination URL passed as a parameter.
	 * @param WP_User $user                  The WP_User object for the user being redirected.
	 * @return string The redirect URL.
	 */
	public function handle_login_redirect( $redirect_to, $requested_redirect_to, $user ) {
		// Don't redirect if there's an error with the user.
		if ( is_wp_error( $user ) ) {
			return $redirect_to;
		}

		// Don't redirect for interim login or reauth.
		$interim_login = isset( $_REQUEST['interim-login'] );
		$reauth = ! empty( $_REQUEST['reauth'] );

		if ( $interim_login || $reauth ) {
			return $redirect_to;
		}

		// Check if primary dashboard redirect is enabled (multisite only).
		if ( is_multisite() && $this->is_primary_dashboard_redirect_enabled() ) {
			$primary_redirect_url = $this->get_primary_dashboard_url( $user );
			if ( $primary_redirect_url ) {
				return $primary_redirect_url;
			}
		}

		// Check for custom redirect URL.
		$custom_redirect_url = $this->get_custom_redirect_url();
		if ( ! empty( $custom_redirect_url ) ) {
			return esc_url_raw( $custom_redirect_url );
		}

		return $redirect_to;
	}

	/**
	 * Check if primary dashboard redirect is enabled.
	 *
	 * @since 2.0.0
	 * @return bool
	 */
	private function is_primary_dashboard_redirect_enabled() {
		return '1' === get_site_option( 'primary_redirect_dashboard_enabled', '0' );
	}

	/**
	 * Get primary dashboard URL for user.
	 *
	 * @since 2.0.0
	 * @param WP_User $user The user object.
	 * @return string|false Primary dashboard URL or false if not available.
	 */
	private function get_primary_dashboard_url( $user ) {
		if ( ! is_multisite() || ! isset( $user->primary_blog ) ) {
			return false;
		}

		$primary_blog_id = absint( $user->primary_blog );
		if ( $primary_blog_id <= 0 ) {
			return false;
		}

		$primary_url = get_blogaddress_by_id( $primary_blog_id );
		if ( $primary_url ) {
			return trailingslashit( $primary_url ) . 'wp-admin/';
		}

		return false;
	}

	/**
	 * Get custom redirect URL.
	 *
	 * @since 2.0.0
	 * @return string
	 */
	private function get_custom_redirect_url() {
		if ( is_multisite() ) {
			return get_site_option( 'primary_redirect_url', '' );
		}

		return get_option( 'primary_redirect_url', '' );
	}

	/**
	 * Render network settings.
	 *
	 * @since 2.0.0
	 */
	public function render_network_settings() {
		$redirect_url = get_site_option( 'primary_redirect_url', '' );
		$dashboard_enabled = get_site_option( 'primary_redirect_dashboard_enabled', '0' );
		?>
		<h3><?php esc_html_e( 'Primary Redirect Settings', 'primary-redirect' ); ?></h3>
		<table class="form-table">
			<tr>
				<th scope="row">
					<label for="primary_redirect_url"><?php esc_html_e( 'Custom Redirect URL', 'primary-redirect' ); ?></label>
				</th>
				<td>
					<input
						name="primary_redirect_url"
						type="url"
						id="primary_redirect_url"
						value="<?php echo esc_attr( $redirect_url ); ?>"
						class="regular-text"
						placeholder="https://example.com/dashboard"
					/>
					<p class="description">
						<?php esc_html_e( 'Enter a custom URL to redirect users after login. Leave empty to use WordPress default behavior.', 'primary-redirect' ); ?>
					</p>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php esc_html_e( 'Primary Dashboard Redirect', 'primary-redirect' ); ?>
				</th>
				<td>
					<label>
						<input
							type="checkbox"
							name="primary_redirect_dashboard_enabled"
							value="1"
							<?php checked( $dashboard_enabled, '1' ); ?>
						/>
						<?php esc_html_e( 'Redirect users to their primary blog dashboard (overrides custom URL)', 'primary-redirect' ); ?>
					</label>
					<p class="description">
						<?php esc_html_e( 'This option is only available in multisite installations.', 'primary-redirect' ); ?>
					</p>
				</td>
			</tr>
		</table>
		<?php
	}

	/**
	 * Update network settings.
	 *
	 * @since 2.0.0
	 */
	public function update_network_settings() {
		// Verify user capabilities.
		if ( ! current_user_can( 'manage_network_options' ) ) {
			return;
		}

		// Sanitize and update redirect URL.
		$redirect_url = isset( $_POST['primary_redirect_url'] ) ? esc_url_raw( wp_unslash( $_POST['primary_redirect_url'] ) ) : '';
		update_site_option( 'primary_redirect_url', $redirect_url );

		// Update dashboard redirect setting.
		$dashboard_enabled = isset( $_POST['primary_redirect_dashboard_enabled'] ) ? '1' : '0';
		update_site_option( 'primary_redirect_dashboard_enabled', $dashboard_enabled );
	}

	/**
	 * Register single site settings.
	 *
	 * @since 2.0.0
	 */
	public function register_single_site_settings() {
		add_settings_section(
			'primary_redirect_settings',
			__( 'Primary Redirect Settings', 'primary-redirect' ),
			array( $this, 'render_settings_section_description' ),
			'general'
		);

		add_settings_field(
			'primary_redirect_url',
			__( 'Redirect URL after login', 'primary-redirect' ),
			array( $this, 'render_single_site_setting_field' ),
			'general',
			'primary_redirect_settings'
		);

		register_setting(
			'general',
			'primary_redirect_url',
			array(
				'type'              => 'string',
				'sanitize_callback' => 'esc_url_raw',
				'default'           => '',
			)
		);
	}

	/**
	 * Render settings section description.
	 *
	 * @since 2.0.0
	 */
	public function render_settings_section_description() {
		echo '<p>' . esc_html__( 'Configure where users should be redirected after logging in.', 'primary-redirect' ) . '</p>';
	}

	/**
	 * Render single site setting field.
	 *
	 * @since 2.0.0
	 */
	public function render_single_site_setting_field() {
		$redirect_url = get_option( 'primary_redirect_url', '' );
		?>
		<input
			name="primary_redirect_url"
			type="url"
			id="primary_redirect_url"
			value="<?php echo esc_attr( $redirect_url ); ?>"
			class="regular-text"
			placeholder="https://example.com/dashboard"
		/>
		<p class="description">
			<?php esc_html_e( 'Enter a custom URL to redirect users after login. Leave empty to use WordPress default behavior.', 'primary-redirect' ); ?>
		</p>
		<?php
	}
}

/**
 * Initialize the plugin.
 *
 * @since 2.0.0
 */
function primary_redirect_init() {
	Primary_Redirect::get_instance();
}

// Initialize the plugin.
add_action( 'init', 'primary_redirect_init' );
