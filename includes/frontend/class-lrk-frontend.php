<?php
/**
 * UserRegistrationKit Admin.
 *
 * @class    LRK_Frontend
 * @version  1.0.0
 * @package  UserRegistrationKit/Frontend
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * LRK_Frontend Class
 */
class LRK_Frontend {

    private $templates_path;

    /**
	 * Class Constructor.
	 */
	public function __construct() {
        $this->templates_path = LRK_ABSPATH . 'includes/frontend/views/';

		// Include classes
		$this->includes();

        // Scripts
		add_action( 'wp_enqueue_scripts', array( $this, 'load_scripts' ) );
		
		// Add custom styles
		add_action( 'wp_enqueue_scripts', array( $this, 'lrk_custom_styles' ) );

        // Add shortcodes
        add_shortcode( 'user_registration_kit_sign', array($this, 'user_registration_kit_sign_shortcode') );
		add_shortcode( 'user_registration_kit_register', array($this, 'user_registration_kit_register_shortcode') );
		add_shortcode( 'user_registration_kit_both', array($this, 'user_registration_kit_both_shortcode') );
		add_shortcode( 'user_registration_kit_my_account', array($this, 'user_registration_kit_my_account_shortcode') );
		add_shortcode( 'user_registration_kit_dropdown', array($this, 'user_registration_kit_dropdown_shortcode') );
		
		// Add Login registration kit category to the Gutenberg
        add_filter( 'block_categories_all', array( $this, 'lrk_editor_block_category' ), 10, 2);

		// Gutenberg blocks
		add_action( 'init', array( $this, 'user_registration_kit_g_blocks' ) );

		// Ajax sign in
		add_action( 'wp_ajax_lrk_sign_in_action', array( $this, 'lrk_sign_in_action_f' ) );
		add_action( 'wp_ajax_nopriv_lrk_sign_in_action', array( $this, 'lrk_sign_in_action_f' ) );

		// Ajax register
		add_action( 'wp_ajax_lrk_register_action', array( $this, 'lrk_register_action_f' ) );
		add_action( 'wp_ajax_nopriv_lrk_register_action', array( $this, 'lrk_register_action_f' ) );

		// Ajax user settings
		add_action( 'wp_ajax_lrk_user_settings_update', array( $this, 'lrk_user_settings_update_f' ) );
		add_action( 'wp_ajax_nopriv_lrk_user_settings_update', array( $this, 'lrk_user_settings_update_f' ) );

		// Ajax lost password
		add_action( 'wp_ajax_lrk_lost_password', array( $this, 'lrk_lost_password_f' ) );
		add_action( 'wp_ajax_nopriv_lrk_lost_password', array( $this, 'lrk_lost_password_f' ) );

		// Prevent dashboard access
		add_action( 'init', array( $this, 'lrk_prevent_dashboard_access' ) );

		// Add User Menu Dropdown
		add_filter( 'wp_nav_menu_items', array( $this, 'lrk_add_user_menu_dropdown' ), 10, 2 );

		// Register endpoints
		add_action( 'init', array( $this, 'lrk_register_endpoints' ) );

		// Change avatar
		add_filter( 'pre_get_avatar_data', array( $this, 'lrk_custom_avatar' ), 10, 5 );

		// Add modal popup
		add_action('wp_footer', array( $this, 'lrk_forms_popup' ));

		// Add image size
		add_image_size( 'lrk_image_small', 200, 200, true );
		
		// Add custom CSS
		add_action( 'wp_enqueue_scripts', array( $this, 'lrk_custom_css' ), 99 );

		// Set user xprofile fields
		add_action( 'bp_core_activated_user', array( $this, 'lrk_form_activated_user' ), 10, 3 );

		// Redirect default login page
		add_action( 'init', array( $this, 'lrk_form_redirect_default_login' ) );

		// Elementor block
		if (true === in_array( 'elementor/elementor.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) || true === in_array( 'elementor-pro/elementor-pro.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
			add_action( 'elementor/elements/categories_registered', array( $this, 'lrk_init_elem_categories' ) );
			add_action( 'elementor/widgets/widgets_registered', array( $this, 'lrk_init_elem_widgets' ) );
		}
	}
	
	/**
	 * Includes any classes we need within admin.
	 */
	public function includes() {
        include_once LRK_ABSPATH . 'includes/admin/class-lrk-admin.php';
        include_once LRK_ABSPATH . 'includes/admin/class-lrk-admin-settings.php';
    }

    /**
	 * Scripts
	 */
    public function load_scripts() {
		wp_register_style( 'user-registration-kit-main-css', LRK_PLUGIN_URL . '/assets/css/tk-login-plugin.css', array(), LRK_VERSION );
		wp_register_script( 'user-registration-kit-login-js', LRK_PLUGIN_URL . '/assets/js/tk-login-plugin.js', array( 'jquery' ), LRK_VERSION, true );
		wp_register_script( 'user-registration-kit-main-js', LRK_PLUGIN_URL . '/assets/js/lrk-main.js', array( 'jquery' ), LRK_VERSION, true );
		
		wp_enqueue_style( 'user-registration-kit-main-css' );
		
		$enable_captcha = ( $this->get_option('user_registration_kit_captcha') == 'yes' ) ? true : false;
		$captcha_site_key = $this->get_option('user_registration_kit_captcha_site_key');
		if($enable_captcha){
			wp_enqueue_script( 'lrk-google-recaptcha-key-v3', "https://www.google.com/recaptcha/api.js?render={$captcha_site_key}", array( 'jquery' ), false, true );
		}	

		wp_enqueue_script( 'user-registration-kit-login-js' );
		wp_enqueue_script( 'user-registration-kit-main-js' );
		wp_localize_script( 'user-registration-kit-main-js', 'ajax_url', array( admin_url('admin-ajax.php') ) );
		wp_localize_script( 'user-registration-kit-main-js', 'signFormConfigCaptcha', array('enable_captcha' => $enable_captcha, 'captcha_site_key' => $captcha_site_key) );
		wp_localize_script( 'user-registration-kit-main-js', 'lrkPluginUrl', array( LRK_PLUGIN_URL ) );
	}

	/**
	 * Add custom styles
	 */
	public function lrk_custom_styles(){
		$custom_css = '';
		$user_nav_icon_color = $this->get_option('user_registration_kit_user_menu_icon_color', '#000000');
		if($user_nav_icon_color != ''){
			$custom_css .= '.additional-menu-item .tk-lp-icon{fill: '.$user_nav_icon_color.'}';
		}
		wp_add_inline_style( 'user-registration-kit-main-css', $custom_css );
	}

    /**
	 * Sign in form shortcode
	 */
    public function user_registration_kit_sign_shortcode( $atts ) {
		$login_fields = LRK_Admin_Settings::get_fields('login');
		$captcha_fields = LRK_Admin_Settings::get_fields('captcha');
		$sign_fields = array_merge($login_fields, $captcha_fields);
		$view_variables = array();
		foreach($sign_fields as $sign_field){
			if(isset($sign_field['id'])){
				$default = isset($sign_field['default']) ? $sign_field['default'] : '';
				$view_variables[$sign_field['id']] = $this->get_option($sign_field['id'], $default);
			}
		}
		
		$view_variables = shortcode_atts( $view_variables, $atts );

		$view_variables['both'] = false;
		$users_can_register = get_option('users_can_register');
		$view_variables['users_can_register'] = $users_can_register;
		$output = '';
		ob_start();

		$unique_id = uniqid( 'tk_lp_id' );
		$view_variables['unique_id'] = $unique_id;
		$atts_width = isset( $atts['width'] ) ? esc_attr('width:' . $atts['width']) : '';
		?>
		<div class="tk-lp-component-form" style="<?php echo esc_attr($atts_width); ?>">
			<?php
				extract( $view_variables, EXTR_REFS );
				unset( $view_variables );
				require $this->templates_path . 'sign-form.php';
			?>
		</div>
		<?php
		$output = ob_get_clean();

        return $output;
	}
	
	/**
	 * Register form shortcode
	 */
    public function user_registration_kit_register_shortcode( $atts ) {
		$register_fields = LRK_Admin_Settings::get_fields('register');
		$captcha_fields = LRK_Admin_Settings::get_fields('captcha');
		$sign_fields = array_merge($register_fields, $captcha_fields);
		$view_variables = array();
		foreach($sign_fields as $sign_field){
			if(isset($sign_field['id'])){
				$default = isset($sign_field['default']) ? $sign_field['default'] : '';
				$view_variables[$sign_field['id']] = $this->get_option($sign_field['id'], $default);
			}
		}

		$view_variables = shortcode_atts( $view_variables, $atts );

		$view_variables['both'] = false;
		$users_can_register = get_option('users_can_register');
		$view_variables['users_can_register'] = $users_can_register;

		$output = '';
		ob_start();

		$unique_id = uniqid( 'tk_lp_id' );
		$view_variables['unique_id'] = $unique_id;

		$atts_width = isset( $atts['width'] ) ? esc_attr('width:' . $atts['width']) : '';
		?>
		<div class="tk-lp-component-form" style="<?php echo esc_attr($atts_width); ?>">
			<?php
				extract( $view_variables, EXTR_REFS );
				unset( $view_variables );
				require $this->templates_path . 'register-form.php';
			?>
		</div>
		<?php
		$output = ob_get_clean();
		
		return $output;
	}

	/**
	 * Both form shortcode
	 */
    public function user_registration_kit_both_shortcode( $atts ) {
		$login_fields = LRK_Admin_Settings::get_fields('login');
		$register_fields = LRK_Admin_Settings::get_fields('register');
		$captcha_fields = LRK_Admin_Settings::get_fields('captcha');

		$both_fields = array_merge($login_fields, $register_fields, $captcha_fields);

		$view_variables = array();
		foreach($both_fields as $both_field){
			if(isset($both_field['id'])){
				$default = isset($both_field['default']) ? $both_field['default'] : '';
				$view_variables[$both_field['id']] = $this->get_option($both_field['id'], $default);
			}
		}

		$view_variables = shortcode_atts( $view_variables, $atts );

		$view_variables['both'] = true;
		$users_can_register = get_option('users_can_register');
		$view_variables['users_can_register'] = $users_can_register;
		$output = '';
		ob_start();

		$unique_id = uniqid( 'tk_lp_id' );
		$view_variables['unique_id'] = $unique_id;
		$atts_width = isset( $atts['width'] ) ? esc_attr('width:' . $atts['width']) : '';
		?>
		<div class="tk-lp-component-form" style="<?php echo esc_attr($atts_width); ?>">
			<?php
			extract( $view_variables, EXTR_REFS );
			unset( $view_variables );
			require $this->templates_path . 'sign-form.php';
			require $this->templates_path . 'register-form.php';
			?>
		</div>
		<?php
		$output = ob_get_clean();

		return $output;
	}
	
	/**
	 * My account shortcode
	 */
	public function user_registration_kit_my_account_shortcode( $atts ) {
		$view_variables = array();
		$account_tabs = $this->get_option('user_registration_kit_account_tabs', array());
		$page_on_front = get_option( 'page_on_front' );
		$myaccount_page_id = $this->get_option('user_registration_kit_account_page', $page_on_front);
		$myaccount_page_url = get_permalink( $myaccount_page_id );

		if(!empty($account_tabs)){
			foreach($account_tabs as $account_tab_k => $account_tab){
				$tab_slug = 'tklp-' . sanitize_title($account_tab['user_registration_kit_account_tabs_title']);
				$tab_url = LRK_Admin::get_endpoint_url($tab_slug, $myaccount_page_url);
				$account_tabs[$account_tab_k]['url'] = $tab_url;
			}
		}
		
		$view_variables['user_registration_kit_account_tabs'] = $account_tabs;
		$view_variables['myaccount_page_id'] = $myaccount_page_id;

		$user_data = array();
		if( is_user_logged_in() ){
			$user_data = wp_get_current_user();
		}
		$view_variables['user_data'] = $user_data;

		$output = '';
		ob_start();

		extract( $view_variables, EXTR_REFS );
		unset( $view_variables );
		require $this->templates_path . 'my-account.php';

		$output = ob_get_clean();

		return $output;
	}

	public function user_registration_kit_dropdown_shortcode( $atts ) {
		return '<ul>' . $this->get_user_dropdown($atts) . '</ul>';
	}
	
	/**
	 * Get a setting from the settings API.
	 *
	 * @param mixed $option_name Option Name.
	 * @param mixed $default Default.
	 *
	 * @return string
	 */
	public function get_option( $option_name, $default = '' ) {
        $option_value = get_option( $option_name, null );

        return (null == $option_value) ? $default : $option_value;
	}

	/**
	 * Creating a block category
	 */
    public function lrk_editor_block_category( $categories, $post ) {
        return array_merge(
            $categories,
            array(
                array(
                    'slug' => 'lrk_blocks',
                    'title' => esc_html__( 'Login registration kit', 'user-registration-kit' ),
                ),
            )
        );
    }

	/**
	 * Register gutenberg blocks
	 */
	public function user_registration_kit_g_blocks() {
		$global_settings = LRK_Admin_Settings::get_global_settings();
		$depends = array( 'wp-blocks', 'wp-i18n' );

		if ( wp_script_is( 'wp-edit-widgets' ) ) {
			$depends[] = 'wp-edit-widgets';
		} else {
			$depends[] = 'wp-edit-post';
		}
        wp_enqueue_style( 'lrk-editor-block-css', LRK_PLUGIN_URL . '/assets/css/tk-login-plugin.css' );
		wp_register_script( 'lrk-editor-blocks', LRK_PLUGIN_URL . '/assets/js/lrk-block.js', $depends, true );
        wp_localize_script( 'lrk-editor-blocks', 'lrk_global_settings', array( json_encode($global_settings) ) );
		
		// Register form
		register_block_type(
			'lrk/registerform',
			array(
				'api_version' => 2,
				'editor_style' => 'lrk-editor-block-css',
				'editor_script' => 'lrk-editor-blocks',
				'render_callback' => array($this, 'user_registration_kit_register_block' ),
				'attributes' => array(
					'userLoginOption' => array(
						'type'    => 'string',
					),
					'registerRedirectURL' => array(
						'type'    => 'string',
					),
					'registerhideFieldLabels' => array(
						'type'    => 'boolean',
					),
					'blockWidth' => array(
						'type'    => 'number',
					)
				)
			)
		);

		// Sign form
		register_block_type(
			'lrk/signinform',
			array(
				'api_version' => 2,
				'editor_style' => 'lrk-editor-block-css',
				'editor_script' => 'lrk-editor-blocks',
				'render_callback' => array($this, 'user_registration_kit_sign_block' ),
				'attributes' => array(
					'redirectURL' => array(
						'type'    => 'string',
					),
					'hideFieldLabels' => array(
						'type'    => 'boolean',
					),
					'enableRememberMe' => array(
						'type'    => 'boolean',
					),
					'enableLostPassword' => array(
						'type'    => 'boolean',
					),
					'blockWidth' => array(
						'type'    => 'number',
					)
				)
			)
		);

		// Both
		register_block_type(
			'lrk/bothform',
			array(
				'api_version' => 2,
				'editor_style' => 'lrk-editor-block-css',
				'editor_script' => 'lrk-editor-blocks',
				'render_callback' => array($this, 'user_registration_kit_both_block' ),
				'attributes' => array(
					'userLoginOption' => array(
						'type'    => 'string',
					),
					'registerRedirectURL' => array(
						'type'    => 'string',
					),
					'registerhideFieldLabels' => array(
						'type'    => 'boolean',
					),
					'redirectURL' => array(
						'type'    => 'string',
					),
					'hideFieldLabels' => array(
						'type'    => 'boolean',
					),
					'enableRememberMe' => array(
						'type'    => 'boolean',
					),
					'enableLostPassword' => array(
						'type'    => 'boolean',
					),
					'blockWidth' => array(
						'type'    => 'number',
					)
				)
			)
		);
	}

	/**
	 * Register block callback
	 */
	public function user_registration_kit_register_block($block_attributes, $content) {
		$shortcode_attr = '';
		if( isset($block_attributes['userLoginOption']) && $block_attributes['userLoginOption'] != '' ) {
			$shortcode_attr .= ' user_registration_kit_form_register_option=' . esc_attr($block_attributes['userLoginOption']);
		}
		if( isset($block_attributes['registerRedirectURL']) && $block_attributes['registerRedirectURL'] != '' ) {
			$shortcode_attr .= ' user_registration_kit_form_register_redirect=' . esc_url($block_attributes['registerRedirectURL']);
		}
		if( isset($block_attributes['registerhideFieldLabels']) && $block_attributes['registerhideFieldLabels'] ) {
			$shortcode_attr .= ' user_registration_kit_form_register_hide_labels=yes';
		} else {
			$shortcode_attr .= ' user_registration_kit_form_register_hide_labels=no';
		}
		if( isset($block_attributes['blockWidth']) && $block_attributes['blockWidth'] != '' ) {
			$shortcode_attr .= ' width=' . esc_attr(intval($block_attributes['blockWidth'])) . '%';
		}

		return do_shortcode('[user_registration_kit_register'.esc_attr($shortcode_attr).']');
	}

	/**
	 * Login block callback
	 */
	public function user_registration_kit_sign_block($block_attributes, $content) {
		$shortcode_attr = '';
		if( isset($block_attributes['redirectURL']) && $block_attributes['redirectURL'] != '' ) {
			$shortcode_attr .= ' user_registration_kit_form_login_redirect=' . esc_attr($block_attributes['redirectURL']);
		}
		if( isset($block_attributes['hideFieldLabels']) && $block_attributes['hideFieldLabels'] ) {
			$shortcode_attr .= ' user_registration_kit_form_login_hide_labels=yes';
		} else {
			$shortcode_attr .= ' user_registration_kit_form_login_hide_labels=no';
		}
		if( isset($block_attributes['enableRememberMe']) && $block_attributes['enableRememberMe'] ) {
			$shortcode_attr .= ' user_registration_kit_form_login_remember_me=yes';
		} else {
			$shortcode_attr .= ' user_registration_kit_form_login_remember_me=no';
		}
		if( isset($block_attributes['enableLostPassword']) && $block_attributes['enableLostPassword'] ) {
			$shortcode_attr .= ' user_registration_kit_form_login_lost_password=yes';
		} else {
			$shortcode_attr .= ' user_registration_kit_form_login_lost_password=no';
		}
		if( isset($block_attributes['blockWidth']) && $block_attributes['blockWidth'] != '' ) {
			$shortcode_attr .= ' width=' . esc_attr(intval($block_attributes['blockWidth'])) . '%';
		}

		return do_shortcode('[user_registration_kit_sign'.esc_attr($shortcode_attr).']');
	}

	/**
	 * Both block callback
	 */
	public function user_registration_kit_both_block($block_attributes, $content) {
		$shortcode_attr = '';
		if( isset($block_attributes['userLoginOption']) && $block_attributes['userLoginOption'] != '' ) {
			$shortcode_attr .= ' user_registration_kit_form_register_option=' . esc_attr($block_attributes['userLoginOption']);
		}
		if( isset($block_attributes['registerRedirectURL']) && $block_attributes['registerRedirectURL'] != '' ) {
			$shortcode_attr .= ' user_registration_kit_form_register_redirect=' . esc_attr($block_attributes['registerRedirectURL']);
		}
		if( isset($block_attributes['registerhideFieldLabels']) && $block_attributes['registerhideFieldLabels'] ) {
			$shortcode_attr .= ' user_registration_kit_form_register_hide_labels=yes';
		} else {
			$shortcode_attr .= ' user_registration_kit_form_register_hide_labels=no';
		}
		if( isset($block_attributes['redirectURL']) && $block_attributes['redirectURL'] != '' ) {
			$shortcode_attr .= ' user_registration_kit_form_login_redirect=' . esc_attr($block_attributes['redirectURL']);
		}
		if( isset($block_attributes['hideFieldLabels']) && $block_attributes['hideFieldLabels'] ) {
			$shortcode_attr .= ' user_registration_kit_form_login_hide_labels=yes';
		} else {
			$shortcode_attr .= ' user_registration_kit_form_login_hide_labels=no';
		}
		if( isset($block_attributes['enableRememberMe']) && $block_attributes['enableRememberMe'] ) {
			$shortcode_attr .= ' user_registration_kit_form_login_remember_me=yes';
		} else {
			$shortcode_attr .= ' user_registration_kit_form_login_remember_me=no';
		}
		if( isset($block_attributes['enableLostPassword']) && $block_attributes['enableLostPassword'] ) {
			$shortcode_attr .= ' user_registration_kit_form_login_lost_password=yes';
		} else {
			$shortcode_attr .= ' user_registration_kit_form_login_lost_password=no';
		}
		if( isset($block_attributes['blockWidth']) && $block_attributes['blockWidth'] != '' ) {
			$shortcode_attr .= ' width=' . esc_attr(intval($block_attributes['blockWidth'])) . '%';
		}

		return do_shortcode('[user_registration_kit_both'.esc_attr($shortcode_attr).']');
	}

	/**
	 * @param string $token Recaptcha token
	 * @param string $sicret_key Recaptcha secret key
	 * @return array
	 */
	public static function returnReCaptcha( $token, $captcha_secret_key ){
		$recaptcha_url = 'https://www.google.com/recaptcha/api/siteverify';
		$recaptcha_r = wp_remote_get($recaptcha_url . '?secret=' . $captcha_secret_key . '&response=' . $token);
		$recaptcha = wp_remote_retrieve_body($recaptcha_r);
		$recaptcha = json_decode($recaptcha, true);
		return $recaptcha;
	}
	
	public function lrk_sign_in_action_f() {
		$errors = array();
		$log = filter_input( INPUT_POST, 'log' );
		$pwd = filter_input( INPUT_POST, 'pwd' );
		$rememberme	= filter_input( INPUT_POST, 'rememberme' );
		$redirect_to = filter_input( INPUT_POST, 'redirect_to', FILTER_VALIDATE_URL );
		$token = (isset($_POST['token'])) ? sanitize_post( $_POST['token'] ) : '';

		if ( !$log ) {
			$errors[ 'log' ] = esc_html__( 'Login is required', 'user-registration-kit' );
		}

		if ( !$pwd ) {
			$errors[ 'pwd' ] = esc_html__( 'Password is required', 'user-registration-kit' );
		}

		if($token != ''){
			$captcha_secret_key = $this->get_option('user_registration_kit_captcha_secret_key');
			$captcha_response = self::returnReCaptcha($token, $captcha_secret_key);

			if ($captcha_response['success'] != 1){
				$errors[ 'captcha' ] = esc_html__( 'Whrong captcha', 'user-registration-kit' );
			}
		}

		if ( !empty( $errors ) ) {
			wp_send_json_error( array(
				'errors' => $errors,
			) );
		}

		$user = wp_signon( array(
			'user_login'	 => $log,
			'user_password'	 => $pwd,
			'remember'		 => $rememberme,
				) );

		if ( is_wp_error( $user ) ) {
			wp_send_json_error( array(
				'message' => $user->get_error_message(),
			) );
		}

		wp_send_json_success( array(
			'redirect_to' => $redirect_to ? $redirect_to : ''
		) );
		
		exit;
	}

	public function lrk_register_action_f() {
		$errors = array();

		$user_login	= filter_input( INPUT_POST, 'user_login' );
		$user_email	= filter_input( INPUT_POST, 'user_email', FILTER_VALIDATE_EMAIL );
		$redirect_to = filter_input( INPUT_POST, 'redirect_to', FILTER_VALIDATE_URL );
		$user_password = filter_input( INPUT_POST, 'user_password' );
		$user_password_confirm = filter_input( INPUT_POST, 'user_password_confirm' );
		$after_login = filter_input( INPUT_POST, 'after_login' );
		$gdpr		 = filter_input( INPUT_POST, 'gdpr' );
		$token = (isset($_POST['token'])) ? sanitize_post( $_POST['token'] ) : '';

		if ( !$user_login ) {
			$errors[ 'user_login' ] = esc_html__( 'Login is required', 'user-registration-kit' );
		}
		if ( !$user_email ) {
			$errors[ 'user_email' ] = esc_html__( 'Email is required', 'user-registration-kit' );
		}
		if ( strlen( $user_password ) < 6 && isset($user_password) ) {
			$errors[ 'user_password' ] = esc_html__( 'Minimum password length is 6 characters', 'user-registration-kit' );
		} else if ( $user_password !== $user_password_confirm && isset($user_password_confirm) && isset($user_password) ) {
			$errors[ 'user_password_confirm' ] = esc_html__( 'Password and confirm password does not match', 'user-registration-kit' );
		}

		$privacy_page_id = get_option('wp_page_for_privacy_policy');

		if ( !$gdpr && $privacy_page_id != 0 ) {
			$errors[ 'gdpr' ] = esc_html__( 'Please, accept privacy policy', 'user-registration-kit' );
		}

		$register_fields_type = $this->get_option('user_registration_kit_form_register_fields');
		$bp_fields = self::getBPFields();
		if($register_fields_type != 'basic'){
			if(!empty($bp_fields)){
				foreach($bp_fields as $bp_field_key => $bp_field_value){
					$post_val = (isset($_POST[$bp_field_key])) ? sanitize_post($_POST[$bp_field_key]) : '';
					if(trim($post_val) == '' && $bp_field_value['required']){
						$errors[ $bp_field_key ] = esc_html__( $bp_field_value['label'] . ' is required', 'user-registration-kit' );
					}
				}
			}
		}

		if($token != ''){
			$captcha_secret_key = $this->get_option('user_registration_kit_captcha_secret_key');
			$captcha_response = self::returnReCaptcha($token, $captcha_secret_key);

			if ($captcha_response['success'] != 1){
				$errors[ 'captcha' ] = esc_html__( 'Whrong captcha', 'user-registration-kit' );
			}
		}

		if ( !empty( $errors ) ) {
			wp_send_json_error( array(
				'errors' => $errors,
			) );
		}

		$sanitized_user_login = sanitize_user( $user_login );
		$user_id = username_exists( $sanitized_user_login );
		if ( !$user_id && email_exists($user_email) == false ) {
			if(!self::useBuddyPress()){
				$user_id = wp_create_user( $sanitized_user_login, $user_password, $user_email );
				$user_registration_kit_form_register_user_role = $this->get_option('user_registration_kit_form_register_user_role');

				if( esc_html($user_registration_kit_form_register_user_role) != '' ){
					$u = new WP_User( $user_id );
					$u->set_role( $user_registration_kit_form_register_user_role );
				}
				
				if($after_login == 'auto_login'){
					wp_set_auth_cookie( $user_id, true );
				}

				if ( is_wp_error( $user_id ) ) {
					wp_send_json_error( array(
						'message' => $user_id->get_error_message(),
					) );
				}
			} else {
				$user_meta_arr = array();
				if(!empty($bp_fields)){
					$date_val = array();
					foreach($bp_fields as $bp_field_key => $bp_field_value){
						$post_val = (isset($_POST[$bp_field_key])) ? sanitize_post($_POST[$bp_field_key]) : '';
						if($bp_field_value['type'] != 'datebox'){
							$user_meta_arr['lrk_' . $bp_field_key] = $post_val;
						}else{
							if(!isset($date_text)){
								$date_text = '';
							}
							$date_text .= $post_val . '-';
							array_push($date_val, 1);
							if(count($date_val) == 3){
								$date_text = substr($date_text, 0, -1);
								$user_meta_arr['lrk_' . $bp_field_value['id']] = $date_text;
								$date_text = '';
								$date_val = array();
							}
						}
					}
				}

				$user_id = bp_core_signup_user( $sanitized_user_login, $user_password, $user_email, $user_meta_arr );
				$user_registration_kit_form_register_user_role = $this->get_option('user_registration_kit_form_register_user_role');

				if( esc_html($user_registration_kit_form_register_user_role) != '' ){
					$u = new WP_User( $user_id );
					$u->set_role( $user_registration_kit_form_register_user_role );
				}

				if ( is_wp_error( $user_id ) ) {
					wp_send_json_error( array(
						'message' => $user_id->get_error_message(),
					) );
				}
			}
		}elseif($user_id){
			wp_send_json_error( array(
				'message' => esc_html__( 'This username is already registered. Please choose another one.', 'user-registration-kit' ),
			) );
		}elseif(email_exists($user_email) != false){
			wp_send_json_error( array(
				'message' => esc_html__( 'This email is already registered, please choose another one.', 'user-registration-kit' ),
			) );
		}

		wp_send_json_success( array(
			'redirect_to' => $redirect_to ? $redirect_to : ''
		) );

		exit;
	}

	public static function useBuddyPress() {
		if ( function_exists( 'bp_core_get_user_domain' ) && function_exists( 'bp_activity_get_user_mentionname' ) && function_exists( 'bp_attachments_get_attachment' ) && function_exists( 'bp_loggedin_user_domain' ) && function_exists( 'bp_is_active' ) && function_exists( 'bp_get_activity_slug' ) && function_exists( 'bp_is_active' ) && function_exists( 'bp_get_notifications_unread_permalink' ) && function_exists( 'bp_loggedin_user_domain' ) && function_exists( 'bp_get_settings_slug' ) ) {
			return true;
		}

		return false;
	}

	public static function getBPFields(){
		$fields_arr = array();
		if ( self::useBuddyPress() ) {
			if( !function_exists('bp_nouveau_has_signup_xprofile_fields') ) {
				if ( bp_is_active( 'xprofile' ) ) : 
				if ( bp_has_profile( array( 'profile_group_id' => 1, 'fetch_field_data' => false ) ) ) : 
					while ( bp_profile_groups() ) : bp_the_profile_group();
					while ( bp_profile_fields() ) : bp_the_profile_field();
						if(bp_get_the_profile_field_type() != 'datebox'){
								$fields_arr['field_' . bp_get_the_profile_field_id()] = array(
									'required' => bp_get_the_profile_field_is_required(),
									'label' => bp_get_the_profile_field_name(),
									'type' => bp_get_the_profile_field_type(),
									'id' => bp_get_the_profile_field_id()
								);
						}else{
								$fields_arr['field_' . bp_get_the_profile_field_id() . '_day'] = array(
									'required' => bp_get_the_profile_field_is_required(),
									'label' => bp_get_the_profile_field_name(),
									'type' => bp_get_the_profile_field_type(),
									'id' => bp_get_the_profile_field_id()
								);
								$fields_arr['field_' . bp_get_the_profile_field_id() . '_month'] = array(
									'required' => bp_get_the_profile_field_is_required(),
									'label' => bp_get_the_profile_field_name(),
									'type' => bp_get_the_profile_field_type(),
									'id' => bp_get_the_profile_field_id()
								);
								$fields_arr['field_' . bp_get_the_profile_field_id() . '_year'] = array(
									'required' => bp_get_the_profile_field_is_required(),
									'label' => bp_get_the_profile_field_name(),
									'type' => bp_get_the_profile_field_type(),
									'id' => bp_get_the_profile_field_id()
								);
						}
					endwhile;
					endwhile;
				endif;
				endif;
			} else {
				if ( bp_is_active( 'xprofile' ) && bp_nouveau_has_signup_xprofile_fields( true ) ) :
					while ( bp_profile_groups() ) : bp_the_profile_group();
					while ( bp_profile_fields() ) : bp_the_profile_field();
						if(bp_get_the_profile_field_type() != 'datebox'){
								$fields_arr['field_' . bp_get_the_profile_field_id()] = array(
									'required' => bp_get_the_profile_field_is_required(),
									'label' => bp_get_the_profile_field_name(),
									'type' => bp_get_the_profile_field_type(),
									'id' => bp_get_the_profile_field_id()
								);
						}else{
								$fields_arr['field_' . bp_get_the_profile_field_id() . '_day'] = array(
									'required' => bp_get_the_profile_field_is_required(),
									'label' => bp_get_the_profile_field_name(),
									'type' => bp_get_the_profile_field_type(),
									'id' => bp_get_the_profile_field_id()
								);
								$fields_arr['field_' . bp_get_the_profile_field_id() . '_month'] = array(
									'required' => bp_get_the_profile_field_is_required(),
									'label' => bp_get_the_profile_field_name(),
									'type' => bp_get_the_profile_field_type(),
									'id' => bp_get_the_profile_field_id()
								);
								$fields_arr['field_' . bp_get_the_profile_field_id() . '_year'] = array(
									'required' => bp_get_the_profile_field_is_required(),
									'label' => bp_get_the_profile_field_name(),
									'type' => bp_get_the_profile_field_type(),
									'id' => bp_get_the_profile_field_id()
								);
						}
					endwhile;
					endwhile;
				endif;
			}
		}

		return $fields_arr;
	}

	public function lrk_form_activated_user( $user_id, $key, $user ){
		$register_fields_type = $this->get_option('user_registration_kit_form_register_fields');
		$bp_fields = self::getBPFields();
		if($register_fields_type != 'basic'){
			if ( !empty($bp_fields) ) {
				foreach($bp_fields as $bp_field_key => $bp_field_value){
					if($bp_field_value['type'] != 'datebox'){
						$meta = (isset($user['meta']['lrk_' . $bp_field_key])) ? $user['meta']['lrk_' . $bp_field_key] : '';
						if(!is_array($meta)){
							$meta = wp_unslash($meta);
						}
						if( $meta != '' ){
							xprofile_set_field_data( $bp_field_value['id'], $user_id, $meta );
						}
					}else{
						$meta = (isset($user['meta']['lrk_' . $bp_field_value['id']])) ? date("Y-m-d 00:00:00", strtotime($user['meta']['lrk_' . $bp_field_value['id']])) : '';
						if( $meta != '' ){
							xprofile_set_field_data( $bp_field_value['id'], $user_id, $meta );
						}
					}
				}
			}
		}
	}

	public function lrk_user_settings_update_f(){
		$posted_data =  isset( $_POST ) ? sanitize_post($_POST) : array();
		$file_data = isset( $_FILES['profileImage'] ) ? (array) $_FILES['profileImage'] : array();
		$file_data = array_map( 'esc_attr', $file_data );
		
		$data = $posted_data;
		if( !empty($file_data) ) {
			$data['profileImage'] = $file_data;
		}

		$response = array();
		$uploaded_file = wp_handle_upload( $data['profileImage'], array( 'test_form' => false ) );
		$img_id = 0;
		if( $uploaded_file && ! isset( $uploaded_file['error'] ) ) {
			$response['response'] = "SUCCESS";
			$response['filename'] = basename( $uploaded_file['url'] );
			$response['url'] = $uploaded_file['url'];
			$response['type'] = $uploaded_file['type'];
			$img_id = $this->addImageToMedia($response['url']);
		}

		$errors = array();

		if( is_user_logged_in() ){
			$cur_user_id = get_current_user_id();

			if( trim($data['yourname']) == '' ){
				$errors['empty_yourname'] = esc_html__( 'Your Name field is required', 'user-registration-kit' );
			}

			if( trim($data['email']) == '' ){
				$errors['empty_email'] = esc_html__( 'E-mail field is required', 'user-registration-kit' );
			}

			if( trim($data['nickname']) == '' ){
				$errors['empty_nickname'] = esc_html__( 'Nickname field is required', 'user-registration-kit' );
			}

			if( !is_email($data['email']) && trim($data['email']) != '' ){
				$errors['invalid_email'] = esc_html__( 'Invalid e-mail', 'user-registration-kit' );
			}

			// Change password
			$check_user = wp_authenticate(trim($data['email']), trim($data['current_password']));
			if( trim($data['current_password']) != '' && is_wp_error( $check_user ) ){
				$errors['invalid_password'] = esc_html__( 'Invalid password', 'user-registration-kit' );
			}

			if( trim($data['new_password']) == '' && trim($data['current_password']) != '' ){
				$errors['empty_password'] = esc_html__( 'Please, enter new password', 'user-registration-kit' );
			}

			if( trim($data['new_password']) != trim($data['confirm_password']) ){
				$errors['match_password'] = esc_html__( 'Passwords do not match', 'user-registration-kit' );
			}


			if ( empty( $errors ) ) {
				$user_id = wp_update_user( array(
					'ID' => $cur_user_id,
					'display_name' => esc_html($data['yourname']),
					'user_email' => $data['email'],
					'nickname' => esc_html($data['nickname']),
				) );

				if( $data['confirm_password'] != '' ){
					wp_set_password( trim($data['confirm_password']), $cur_user_id );
					wp_set_auth_cookie( $cur_user_id, true );
				}

				if( $img_id != 0 ){
					update_user_meta( $cur_user_id, 'lrk_avatar_image', $img_id );
				}

				if( $data['delete_avatar'] == '1' ){
					delete_user_meta( $cur_user_id, 'lrk_avatar_image' );
				}
			}

			if ( is_wp_error( $user_id ) ) {
				$errors['error'] = esc_html__( 'Oops something went wrong updaing your account.', 'user-registration-kit' );
			}
		}
		
		if ( !empty( $errors ) ) {
			wp_send_json_error( array(
				'errors' => $errors,
			) );
		}

		wp_send_json_success( array(
			'message' => '1'
		) );

		exit;
	}

	public function lrk_lost_password_f(){
		$errors = array();
		$user_login	= filter_input( INPUT_POST, 'user_login' );
		if( empty( $user_login ) ) {
			$errors['empty_email'] = esc_html__( 'Enter a username or e-mail address..', 'user-registration-kit' );
		} else if( ! is_email( $user_login )) {
			$errors['invalid_email'] = esc_html__( 'Invalid username or e-mail address.', 'user-registration-kit' );
		} else if( ! email_exists( $user_login ) ) {
			$errors['no_user'] = esc_html__( 'There is no user registered with that email address.', 'user-registration-kit' );
		} else {
			$random_password = wp_generate_password( 12, false );
            $user = get_user_by( 'email', $user_login );
                
            $update_user = wp_update_user( array (
                'ID' => $user->ID, 
                'user_pass' => $random_password
                )
            );
                
            if( $update_user ) {
                $to = $user_login;
                $subject = esc_html__( 'Your new password', 'user-registration-kit' );
                $sender = get_option('name');
                    
				$message = sprintf( esc_html__( 'Your new password is: %1$s', 'user-registration-kit' ), $random_password );
                    
                $headers[] = 'MIME-Version: 1.0' . "\r\n";
                $headers[] = 'Content-type: text/html; charset=utf-8' . "\r\n";
                $headers[] = "X-Mailer: PHP \r\n";
				$headers[] = 'From: '.$sender.' < '.$user_login.'>' . "\r\n";
				
                $mail = wp_mail( $to, $subject, $message, $headers );
				if( $mail ){
					wp_send_json_success( array(
						'message' => esc_html__( 'Check your email address for you new password.', 'user-registration-kit' )
					) );
				} else {
					$errors['error'] = esc_html__( 'Oops something went wrong updaing your account!', 'user-registration-kit' );
					wp_send_json_error( array(
						'errors' => $errors,
					) );
				}
            } else {
                $errors['error'] = esc_html__( 'Oops something went wrong updaing your account.', 'user-registration-kit' );
            }
		}

		if ( !empty( $errors ) ) {
			wp_send_json_error( array(
				'errors' => $errors,
			) );
		}

		exit;
	}

	public function addImageToMedia( $image_url ){
		$upload_dir = wp_upload_dir();
		$image_data_r = wp_remote_get($image_url);
		$image_data = wp_remote_retrieve_body( $image_data_r );
		$filename = basename($image_url);
		if(wp_mkdir_p($upload_dir['path'])) $file = $upload_dir['path'] . '/' . $filename;
		else $file = $upload_dir['basedir'] . '/' . $filename;
		file_put_contents($file, $image_data);

		$wp_filetype = wp_check_filetype($filename, null );
		$attachment = array(
			'post_mime_type' => $wp_filetype['type'],
			'post_title' => sanitize_file_name($filename),
			'post_content' => '',
			'post_status' => 'inherit'
		);
		$attach_id = wp_insert_attachment( $attachment, $file );
		require_once(ABSPATH . 'wp-admin/includes/image.php');
		$attach_data = wp_generate_attachment_metadata( $attach_id, $file );
		$res1 = wp_update_attachment_metadata( $attach_id, $attach_data );

		return $attach_id;
	}

	/**
	 * Prevent dashboard access
	 */
	public function lrk_prevent_dashboard_access(){
		$prevent_access = $this->get_option('user_registration_kit_form_login_prevent_access');
		if(!empty($prevent_access)){
			foreach($prevent_access as $prevent_access_v){
				if( is_admin() && !defined('DOING_AJAX') && current_user_can($prevent_access_v) ){
					wp_redirect(home_url());
					exit;
				}
			}
		}
	}

	/**
	 * User menu dropdown
	 */
	public function lrk_add_user_menu_dropdown( $items, $args ){
		$user_menu_enable = esc_html( $this->get_option('user_registration_kit_user_menu_enable') );
		$user_menu_navigation = esc_html( $this->get_option('user_registration_kit_user_menu_navigation') );

		$dropdown = $items;

		if( $user_menu_enable == 'yes' && $user_menu_navigation != '' ){
			if( isset($args->menu->slug) && $args->menu->slug == $user_menu_navigation ){
				$dropdown .= $this->get_user_dropdown();
			}
		}

		return $dropdown;
	}

	private function get_user_dropdown( $atts = array() ) {
		$items = '';
		if( is_user_logged_in() ){
			$menu_avatar_size = (esc_html( $this->get_option('user_registration_kit_user_menu_avatar_size') ) != '') ? intval(esc_html( $this->get_option('user_registration_kit_user_menu_avatar_size') )) : 40;
			$menu_avatar_text = (trim(esc_html( $this->get_option('user_registration_kit_user_menu_avatar_text') )) != '') ? esc_html( $this->get_option('user_registration_kit_user_menu_avatar_text') ) : '';
			$current_user = wp_get_current_user();
			$myaccount_page_id = $this->get_option('user_registration_kit_account_page', get_option('page_on_front'));
			$myaccount_page_url = get_permalink( $myaccount_page_id );

			if( isset($atts['menu_avatar_size']) && $atts['menu_avatar_size'] != '' ){
				$menu_avatar_size = intval($atts['menu_avatar_size']);
			}

			if( isset($atts['menu_avatar_text']) ){
				$menu_avatar_text = $atts['menu_avatar_text'];
			}

			ob_start();
			?>
			<li id="tk-lp-user" class="additional-menu-item tk-lp-user">
				<?php if( esc_url($myaccount_page_url) != '' ){ ?>
				<a href="<?php echo esc_url($myaccount_page_url); ?>" class="tk-lp-profile-settings-link" title="<?php echo esc_html__( 'Profile Settings', 'user-registration-kit' ); ?>">
				<?php } ?>
				<div class="tk-lp-user-avatar">
					<?php echo get_avatar( $current_user->ID, $menu_avatar_size, '', '', array('height' => $menu_avatar_size, 'width' => $menu_avatar_size, 'class' => 'tk-lp-file-input-image') ); ?>
				</div>
				<?php if( $menu_avatar_text != '' ){ ?>
				<div class="tk-lp-text-with-avatar">
					<?php echo esc_html($menu_avatar_text); ?>
				</div>
				<?php } ?>
				<div class="tk-lp-dropdown-icon-cont <?php if( $menu_avatar_text == '' ){echo 'tk-lp-dropdown-icon-c';} ?>">
					<svg class="tk-lp-icon tk-lp-dropdown-icon" width="8" height="4">
						<path fill-rule="evenodd" d="M7.821.902L4.429 3.837a.678.678 0 01-.858 0L.18.902C-.204.57.067.001.609.001h6.783c.541 0 .812.569.429.901z" />
					</svg>
				</div>
				<?php if( esc_url($myaccount_page_url) != '' ){ ?>
				</a>
				<?php } ?>
				<div class="tk-lp-user-menu-dropdown">
					<?php
						$all_locations = get_nav_menu_locations();
						$nav_obj = (isset($all_locations['lrk-user-navigation'])) ? wp_get_nav_menu_object( $all_locations['lrk-user-navigation'] ) : false;
						if($nav_obj !== false) {
							$menu_items = wp_get_nav_menu_items( $nav_obj->term_id );

							echo '<div class="tk-lp-user-menu-dropdown-items">';
							if( !empty($menu_items) ){
								foreach( $menu_items as $menu_item ){
									$menu_item_title = apply_filters( 'the_title', $menu_item->title, $menu_item->ID );
									echo '<a href="' . esc_url($menu_item->url) . '" title="' . esc_html($menu_item->attr_title) . '">' . $menu_item_title . '</a>';
								}
							} else {
								echo '<a href="' . esc_url($myaccount_page_url) . '" title="' . esc_html__( 'Profile Settings', 'user-registration-kit' ) . '">' .  esc_html__( 'Profile Settings', 'user-registration-kit' ) . '</a>';
							}
							echo '<a href="' . wp_logout_url() . '">'.esc_html__( 'Logout', 'user-registration-kit' ).'</a></div>';
						} else {
							$account_tabs = $this->get_option('user_registration_kit_account_tabs', array());
							echo '<div class="tk-lp-user-menu-dropdown-items">';
							echo '<a href="' . esc_url($myaccount_page_url) . '" title="' . esc_html__( 'Profile Settings', 'user-registration-kit' ) . '">' .  esc_html__( 'Profile Settings', 'user-registration-kit' ) . '</a>';
							if(!empty($account_tabs)){
								foreach($account_tabs as $account_tab_val){
									$account_tab_show_in_nav = $account_tab_val['user_registration_kit_account_tabs_show_in_menu'];
									$account_tab_title = $account_tab_val['user_registration_kit_account_tabs_title'];
									if( $account_tab_show_in_nav == 'yes' && esc_html($account_tab_title) != '' ){
										$account_tab_slug = 'tklp-' . sanitize_title($account_tab_val['user_registration_kit_account_tabs_title']);
										$account_tab_url = LRK_Admin::get_endpoint_url($account_tab_slug, $myaccount_page_url);
										echo '<a href="' . esc_url($account_tab_url) . '" title="' . esc_html($account_tab_title) . '">'. esc_html($account_tab_title) .'</a>';
									}
								}
							}
							echo '<a href="' . wp_logout_url() . '">'.esc_html__( 'Logout', 'user-registration-kit' ).'</a></div>';
						}
					?>
				</div>
			</li>
			<?php
			$items .= ob_get_clean();
		} else {
			$no_login_text = ($this->get_option('user_registration_kit_user_menu_no_login_text') != '') ? wp_kses_post($this->get_option('user_registration_kit_user_menu_no_login_text')) : '';
			if( isset($atts['no_login_text']) && $atts['no_login_text'] != '' ){
				$no_login_text = $atts['no_login_text'];
			}
			ob_start();
			?>
			<li id="tk-lp-user" class="additional-menu-item tk-lp-user">
				<a href="#" data-modal-trigger="modal-sign-forms">
				<div class="tk-lp-user-avatar">
					<svg class="tk-lp-icon tk-lp-icon-user" width="20" height="20">
						<path fill-rule="evenodd" d="M17.071 12.929a9.958 9.958 0 0 0-3.8-2.384 5.777 5.777 0 0 0 2.51-4.764A5.787 5.787 0 0 0 10 0a5.787 5.787 0 0 0-5.781 5.781c0 1.975.995 3.721 2.51 4.764a9.965 9.965 0 0 0-3.8 2.384A9.934 9.934 0 0 0 0 20h1.562c0-4.652 3.786-8.438 8.438-8.438 4.652 0 8.438 3.786 8.438 8.438H20a9.934 9.934 0 0 0-2.929-7.071zM10 10a4.224 4.224 0 0 1-4.219-4.219A4.223 4.223 0 0 1 10 1.563a4.223 4.223 0 0 1 4.219 4.218A4.224 4.224 0 0 1 10 10z" />
					</svg>
				</div>
				<?php if( $no_login_text != '' ){ ?>
				<div class="tk-lp-unlogined-text"><?php echo wp_kses_post($no_login_text); ?></div>
				<?php } ?>
				</a>
			</li>
			<?php
			$items .= ob_get_clean();
		}

		return $items;
	}

	/**
	 * Endpoint mask describing the places the endpoint should be added.
	 *
	 * @return int
	 */
	private function get_endpoints_mask() {
		if ( 'page' === get_option( 'show_on_front' ) ) {
			$page_on_front = get_option( 'page_on_front' );
			$myaccount_page_id = $this->get_option('user_registration_kit_account_page', $page_on_front);

			if ( in_array( $page_on_front, array( $myaccount_page_id ), true ) ) {
				return EP_ROOT | EP_PAGES;
			}
		}

		return EP_PAGES;
	}

	/**
	 * Add endpoints for query vars.
	 */
	public function lrk_register_endpoints(){
		$mask = $this->get_endpoints_mask();
		$do_changes = $this->get_option('lrk_flush_rewrite_rules');
		$account_tabs = $this->get_option('user_registration_kit_account_tabs', array());
		if(!empty($account_tabs)){
			foreach($account_tabs as $account_tab_val){
				$slug = 'tklp-' . sanitize_title($account_tab_val['user_registration_kit_account_tabs_title']);
				if(!empty($slug)){
					add_rewrite_endpoint( $slug, $mask );
				}
			}
		}
			
		if($do_changes == 1){
			flush_rewrite_rules();
			update_option('lrk_flush_rewrite_rules', 0);
		}
	}

	public function lrk_custom_avatar( $args, $id_or_email ){
		$user_id = 0;

		if ( is_object( $id_or_email ) ) {

		/* If this is a comment object, check if user is registered */
		if ( isset( $id_or_email->comment_ID ) ) {
			if ( $id_or_email->user_id ) $user_id = $id_or_email->user_id;
			else {
				$user = get_user_by( 'email', $id_or_email->comment_author_email );
				if ( $user ) $user_id = $user->ID;
			}
		} else {
			$user_id = $id_or_email->ID;
		}
		
		} else if ( is_numeric( $id_or_email ) ) {
			/* If this is the user ID, set it as such */
			$user_id = $id_or_email;

		} else if ( is_string( $id_or_email ) && is_email( $id_or_email ) ) {
			/* If this is email, see if it's a registered user */
			$user = get_user_by( 'email', $id_or_email );
			if ( $user ) $user_id = $user->ID;
		}

		/* Get the custom user image, if available */
		if ( $user_id ) {
			$saved = get_user_meta( $user_id, 'lrk_avatar_image', true );
			if( 0 < absint( $saved ) ) {
				$args['url'] = esc_url(wp_get_attachment_image_url( $saved, [ $args['width'], $args['height'] ] ));
			}
		}

		return $args;
	}

	/**
	 * Forms popup.
	 */
	public function lrk_forms_popup(){
		?>
		<div class="tk-lp-modal" data-modal-name="modal-sign-forms">
			<button class="tk-lp-modal-close">
				<svg class="tk-lp-icon" xmlns="http://www.w3.org/2000/svg" width="18" height="18">
					<path fill-rule="evenodd" d="M10.587 8.995l7.076-7.083A1.127 1.127 0 0 0 16.071.32L8.995 7.403 1.919.32A1.125 1.125 0 1 0 .328 1.912l7.076 7.083-7.076 7.084a1.125 1.125 0 1 0 1.591 1.592l7.076-7.083 7.076 7.083a1.123 1.123 0 0 0 1.592 0 1.127 1.127 0 0 0 0-1.592l-7.076-7.084z"></path>
				</svg>
				<span class="tk-lp-modal-close-link" data-modal-dismiss></span>
			</button>
			<?php echo do_shortcode('[user_registration_kit_both]'); ?>
		</div>
		<?php
	}
	
	/**
	 * Add custom CSS.
	 */
	public function lrk_custom_css(){
		$custom_css = '';
		$custom_css = wp_unslash($this->get_option('user_registration_kit_advanced_css'));
		wp_add_inline_style( 'user-registration-kit-main-css', $custom_css );
	}

	/**
	 * Redirect default login page.
	 */
	public function lrk_form_redirect_default_login() {
		$disable_page_login = $this->get_option('user_registration_kit_register_disable_page_login', 'no');
		$page_login_redirect = $this->get_option('user_registration_kit_register_page_login_redirect', get_bloginfo('url'));

		if( $disable_page_login == 'yes' ) {
			global $pagenow;
			$action = (isset($_GET['action'])) ? sanitize_text_field($_GET['action']) : '';
			if( $pagenow == 'wp-login.php' && ( ! $action || ( $action && ! in_array($action, array('logout', 'lostpassword', 'rp', 'resetpass'))))) {
				$page = esc_url($page_login_redirect);
				wp_redirect($page);
				exit();
			}
		}
	}

	/**
	 * Add element category.
	 */
	public function lrk_init_elem_categories( $elements_manager ) {
		$elements_manager->add_category(
	        'elementor-lrk',
	        [
				'title' => esc_html__( 'Login registration kit', 'user-registration-kit' ),
	        ]
	    );
	}

	/**
	 * Add elementor widgets.
	 */
	public function lrk_init_elem_widgets() {
		$widgets_manager = \Elementor\Plugin::instance()->widgets_manager;
		require_once( LRK_ABSPATH . 'includes/frontend/elementor-widgets/user-dropdown.php' );
		$widgets_manager->register_widget_type( new Elementor_Lrk_User_Dropdown() );
	}
}

return new LRK_Frontend();