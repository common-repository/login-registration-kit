<?php
/**
 * UserRegistrationKit Admin.
 *
 * @class    LRK_Admin
 * @version  1.0.0
 * @package  UserRegistrationKit/Admin
 * @category Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * LRK_Admin Class
 */
class LRK_Admin {
    /**
	 * LRK_Admin Constructor
	 */
	public function __construct() {
        add_action( 'init', array( $this, 'includes' ) );

        // On plugin activate hook
        register_activation_hook( LRK_PLUGIN_FILE, array( $this, 'on_lrk_activate' ) );

        // Add LRK post type
        // add_action( 'init', array( $this, 'add_lrk_form_post_type' ) );

        // Register User Menu Dropdown
        add_action( 'init', array( $this, 'lrk_register_user_menu' ) );

        // Add admin settings page
        add_action( 'admin_menu', array( $this, 'add_settings_page' ) );

        // Save options
        add_action( 'admin_init', array( $this, 'update_settings' ) );

        // Admin scripts
        add_action( 'admin_enqueue_scripts', array( $this, 'lrk_admin_scripts' ) );

        // User Menu Dropdown Metabox
        add_filter( 'nav_menu_meta_box_object', array($this, 'lrk_add_user_menu_meta_box'), 10, 1);
    }

	/**
	 * Includes any classes we need within admin.
	 */
	public function includes() {
        include_once dirname( __FILE__ ) . '/class-lrk-admin-settings.php';
    }

    /**
	 * On plugin activate hook.
	 */
    public function on_lrk_activate(){
        $post_data = array(
            'post_title'    => esc_html__( 'Account page', 'user-registration-kit' ),
            'post_content'  => '[user_registration_kit_my_account]',
            'post_status'   => 'publish',
            'post_type'     => 'page',
        );
        $query_meta = array(
            'post_type' => 'page',
            'meta_key' => 'lrk_user_account_page',
            'meta_value' => '1'
        );
        $account_page = new WP_Query( $query_meta );
        
        // Check if account page already exists
        if ( $account_page->post_count == 0 ) {
            $postId = wp_insert_post( $post_data );
            add_post_meta( $postId, 'lrk_user_account_page', '1' );
            $account_page_settings = get_option('user_registration_kit_account_page');
            if($account_page_settings == ''){
                update_option('user_registration_kit_account_page', $postId);
            }
        }
        
    }

    /**
	 * Admin scripts registration 
	 */
    public function lrk_admin_scripts($hook){
        if( $hook == 'toplevel_page_user-register-kit-settings' ){
            $users_can_register = get_option('users_can_register');
            if( !$users_can_register ){
                add_action( 'admin_notices', array( $this, 'admin_notice_no_users_can_register' ) );
            }
            wp_enqueue_style( 'wp-color-picker' );
            wp_enqueue_style( 'select2', LRK_PLUGIN_URL . '/assets/css/select2.css' );
            wp_enqueue_style( 'lrk-admin-css', LRK_PLUGIN_URL . '/assets/css/lrk-admin.css', array(), LRK_VERSION );
            wp_enqueue_script( 'wp-color-picker' );
            wp_register_script( 'jquery-tiptip', LRK_PLUGIN_URL . '/assets/js/jquery.tipTip.js', array( 'jquery' ), LRK_VERSION, true );
            wp_register_script( 'selectWoo', LRK_PLUGIN_URL . '/assets/js/selectWoo.full.js', array( 'jquery' ), '3.5.4' );
            wp_enqueue_code_editor( array( 'type' => 'text/html' ) );
            wp_enqueue_script( 'lrk-admin-js', LRK_PLUGIN_URL . '/assets/js/lrk-admin.js', array('jquery', 'selectWoo', 'jquery-tiptip', 'wp-color-picker'), LRK_VERSION, true );
        }
    }

    /**
	 * Add LRK post type.
	 */
    public function add_lrk_form_post_type(){
        register_post_type( 'lrk_form', array(
            'label'  => null,
            'labels' => array(
                'name'               => esc_html__( 'LRK Forms', 'user-registration-kit' ),
                'singular_name'      => esc_html__( 'LRK Form', 'user-registration-kit' ),
                'add_new_item'       => esc_html__( 'Add new form', 'user-registration-kit' ),
                'not_found'          => esc_html__( 'No forms found.', 'user-registration-kit' )
            ),
            'description'         => '',
            'public'              => true,
            'show_in_menu'        => null,
            'show_in_rest'        => null,
            'rest_base'           => null,
            'menu_position'       => null,
            'menu_icon'           => 'dashicons-id',
            'hierarchical'        => false,
            'supports'            => [ 'title' ],
            'taxonomies'          => [],
            'has_archive'         => false,
            'rewrite'             => true,
            'query_var'           => true,
        ));
    }
    
    /**
	 * Add admin settings page.
	 */
    public function add_settings_page() {
        add_menu_page(
            __( 'Frontend Registration Kit', 'user-registration-kit' ),
            __( 'Registration Kit', 'user-registration-kit' ),
            'manage_options',
            'user-register-kit-settings',
            array( $this, 'settings_page_output' ),
            'dashicons-id-alt'
        );
    }

    /**
	 * Admin settings page output.
	 */
    public function settings_page_output() {
        LRK_Admin_Settings::output();
    }

    /**
	 * Admin save settings
	 */
    public function update_settings() {
        $saved = LRK_Admin_Settings::save_fields();
        if( $saved ){
            add_action( 'admin_notices', array( $this, 'admin_notice' ) );
        }
    }

    /**
	 * Notice success
	 */
    public function admin_notice() {
        ?>
        <div class="notice notice-success is-dismissible">
            <p><?php echo esc_html__( 'Your settings have been saved.', 'user-registration-kit' ); ?></p>
        </div>
        <?php
    }

    /**
	 * Notice success
	 */
    public function admin_notice_no_users_can_register() {
        ?>
        <div class="notice notice-error is-dismissible">
            <p><?php echo sprintf( __( 'To allow users to register for your website via User registration, you must first enable user registration. Go to %1$sSettings > General%2$s tab, and under Membership make sure to check <strong>Anyone can register</strong>.', 'user-registration-kit' ), '<a target="_blank" href="' . admin_url( 'options-general.php#admin_email' ) . '">', '</a>' ); ?></p>
        </div>
        <?php
    }

    /**
	 * Register User Menu Dropdown
	 */
    public function lrk_register_user_menu() {
        register_nav_menu( 'lrk-user-navigation', esc_html__( 'LRK User Menu', 'user-registration-kit' ) );
    }

    /**
	 * Add User Menu Dropdown Metabox
	 */
    public function lrk_add_user_menu_meta_box( $object ) {
        add_meta_box( 'lrk-user-menu-metabox', esc_html__( 'User Menu Dropdown', 'user-registration-kit'), array($this, 'lrk_user_menu_meta_box'), 'nav-menus', 'side', 'default' );
        return $object;
    }
    
    /**
	 * User Menu Dropdown Metabox Function
	 */
    public function lrk_user_menu_meta_box( $object, $args ) {
        global $nav_menu_selected_id;
    
        $walker = new Walker_Nav_Menu_Checklist(false);
    
        $menu_items = array();
        $menu_items_data = array();
        $account_tabs = LRK_Admin_Settings::get_option('user_registration_kit_account_tabs', array());
        $myaccount_page_id = LRK_Admin_Settings::get_option('user_registration_kit_account_page', get_option('page_on_front'));
        $myaccount_page_url = get_permalink( $myaccount_page_id );
        $menu_items_data = array(
            array(
                'object' => 'lrk_user_menu_profile_settings',
                'title' => esc_html__( 'Profile Settings', 'user-registration-kit' ),
                'url' => esc_url($myaccount_page_url),
                'class' => ''
            ),
        );

	    if ( ! empty( $account_tabs ) && is_array( ( $account_tabs ) ) ) {
            foreach($account_tabs as $account_tabs_val){
                $slug = 'tklp-' . sanitize_title($account_tabs_val['user_registration_kit_account_tabs_title']);
				$account_tab_url = self::get_endpoint_url($slug, $myaccount_page_url);

                if(!empty($slug)){
                    array_push($menu_items_data, array(
                        'object' => esc_html($slug),
                        'title' => esc_html($account_tabs_val['user_registration_kit_account_tabs_title']),
                        'url' => esc_url($account_tab_url),
                        'class' => ''
                    ));
                }
            }
        }
    
        foreach($menu_items_data as $menu_items_data_key => $menu_items_data_val){
            $link_settings = new stdClass();
            $link_settings->object_id = $menu_items_data_key + 1;
            $link_settings->ID = $menu_items_data_key + 1;
            $link_settings->db_id = 0;
            $link_settings->object = $menu_items_data_val['object'];
            $link_settings->menu_item_parent = 0;
            $link_settings->type = 'custom';
            $link_settings->title = $menu_items_data_val['title'];
            $link_settings->url = $menu_items_data_val['url'];
            $link_settings->target = '';
            $link_settings->attr_title = '';
            $link_settings->classes = array('lrk-user-menu', $menu_items_data_val['class']);
            $link_settings->xfn = '';
            $menu_items[] = $link_settings;
        }
    
        $removed_args = array( 'action', 'customlink-tab', 'edit-menu-item', 'menu-item', 'page-tab', '_wpnonce' );
        ?>
        <div id="lrkmenuitems" class="categorydiv">
            <div id="tabs-panel-lrkmenuitems-all" class="tabs-panel tabs-panel-active">
                <ul id="lrkmenuitems-checklist-all" class="categorychecklist form-no-clear" >
                <?php
                    echo walk_nav_menu_tree( array_map('wp_setup_nav_menu_item', $menu_items), 0, (object) array( 'walker' => $walker) );
                ?>
                </ul>
            </div>
    
            <p class="button-controls wp-clearfix">
                <span class="add-to-menu">
                    <input type="submit"<?php wp_nav_menu_disabled_check( $nav_menu_selected_id ); ?> class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e('Add to Menu','user-registration-kit'); ?>" name="add-lrkmenuitems-menu-item" id="submit-lrkmenuitems" />
                    <span class="spinner"></span>
                </span>
            </p>
        </div>
        <?php
    }
    
    /**
	 * Generate account page permalinks
	 */
	public static function get_endpoint_url( $endpoint, $permalink = '' ){
		if ( ! $permalink ) {
			$permalink = get_permalink();
		}

		if ( get_option( 'permalink_structure' ) ) {
			if ( strstr( $permalink, '?' ) ) {
				$query_string = '?' . wp_parse_url( $permalink, PHP_URL_QUERY );
				$permalink = current( explode( '?', $permalink ) );
			} else {
				$query_string = '';
			}
			$url = trailingslashit( $permalink );
			$url .= user_trailingslashit( $endpoint );
			$url .= $query_string;
		} else {
			$url = add_query_arg( $endpoint, '', $permalink );
		}

		return $url;
	}

    /**
	 * Get BP fields
	 */
    public static function get_bp_fields($type = 'required'){
        $fields_array = array();
        if ( function_exists( 'bp_core_get_user_domain' ) && function_exists( 'bp_activity_get_user_mentionname' ) && function_exists( 'bp_attachments_get_attachment' ) && function_exists( 'bp_loggedin_user_domain' ) && function_exists( 'bp_is_active' ) && function_exists( 'bp_get_activity_slug' ) && function_exists( 'bp_is_active' ) && function_exists( 'bp_get_notifications_unread_permalink' ) && function_exists( 'bp_loggedin_user_domain' ) && function_exists( 'bp_get_settings_slug' ) ) {
            if( !function_exists('bp_nouveau_has_signup_xprofile_fields') ) {
                if ( bp_is_active( 'xprofile' ) ) : 
                if ( bp_has_profile( array( 'profile_group_id' => 1, 'fetch_field_data' => false ) ) ) : 
                    while ( bp_profile_groups() ) : bp_the_profile_group();
                    while ( bp_profile_fields() ) : bp_the_profile_field();
                    $f = array(
                        'id' => bp_get_the_profile_field_id(),
                        'type' => bp_get_the_profile_field_type(),
                        'name' => bp_get_the_profile_field_input_name(),
                        'label' => bp_get_the_profile_field_name(),
                        'value' => bp_get_the_profile_field_edit_value(),
                        'required' => bp_get_the_profile_field_is_required(),
                        'options' => bp_get_the_profile_field_options()
                    );
                    if( $type == 'required' ){
                        if( bp_get_the_profile_field_is_required() ){
                            array_push($fields_array, $f);
                        }
                    } else {
                        array_push($fields_array, $f);
                    }

                    endwhile;
                    endwhile;
                endif;
                endif;
            } else {
                if ( bp_is_active( 'xprofile' ) && bp_nouveau_has_signup_xprofile_fields( true ) ) :
                    while ( bp_profile_groups() ) : bp_the_profile_group();
                    while ( bp_profile_fields() ) : bp_the_profile_field();
                    $f = array(
                        'id' => bp_get_the_profile_field_id(),
                        'type' => bp_get_the_profile_field_type(),
                        'name' => bp_get_the_profile_field_input_name(),
                        'label' => bp_get_the_profile_field_name(),
                        'value' => bp_get_the_profile_field_edit_value(),
                        'required' => bp_get_the_profile_field_is_required(),
                        'options' => bp_get_the_profile_field_options()
                    );
                    if( $type == 'required' ){
                        if( bp_get_the_profile_field_is_required() ){
                            array_push($fields_array, $f);
                        }
                    } else {
                        array_push($fields_array, $f);
                    }

                    endwhile;
                    endwhile;
                endif;
            }
        }

        return $fields_array;
    }

    /**
	 * Get BP fields HTML
	 */
    public static function get_bp_fields_html( $fields = array(), $user_registration_kit_form_register_hide_l = '' ){
        if( $user_registration_kit_form_register_hide_l == '' ) {
            $user_registration_kit_form_register_hide_labels = LRK_Admin_Settings::get_option('user_registration_kit_form_register_hide_labels');
        } else {
            $user_registration_kit_form_register_hide_labels = $user_registration_kit_form_register_hide_l;
        }

        $res = '';
        ob_start();
        if( !empty($fields) ){
            foreach( $fields as $bp_all_field ){
                if ( 'textbox' == $bp_all_field['type'] || 'wp-textbox' == $bp_all_field['type'] ) :
                ?>
                <div class="tk-lp-form-item">
                    <?php if( esc_html($user_registration_kit_form_register_hide_labels) != 'yes' ){ ?>
                    <label for="<?php echo esc_attr($bp_all_field['name']); ?>" class="tk-lp-label"><?php echo esc_html($bp_all_field['label']); ?></label>
                    <?php } ?>
                    <input class="tk-lp-input" id="<?php echo esc_attr($bp_all_field['name']); ?>" name="<?php echo esc_attr($bp_all_field['name']); ?>" type="text" placeholder="<?php if( esc_html($user_registration_kit_form_register_hide_labels) == 'yes' ){ echo esc_html($bp_all_field['label']); } ?>" />
                </div>
                <?php
                endif;
                if ( 'number' == $bp_all_field['type'] ) :
                ?>
                <div class="tk-lp-form-item">
                    <?php if( esc_html($user_registration_kit_form_register_hide_labels) != 'yes' ){ ?>
                    <label for="<?php echo esc_attr($bp_all_field['name']); ?>" class="tk-lp-label"><?php echo esc_html($bp_all_field['label']); ?></label>
                    <?php } ?>
                    <input class="tk-lp-input" id="<?php echo esc_attr($bp_all_field['name']); ?>" name="<?php echo esc_attr($bp_all_field['name']); ?>" type="number" placeholder="<?php if( esc_html($user_registration_kit_form_register_hide_labels) == 'yes' ){ echo esc_html($bp_all_field['label']); } ?>" />
                </div>
                <?php
                endif;
                if ( 'telephone' == $bp_all_field['type'] ) :
                ?>
                <div class="tk-lp-form-item">
                    <?php if( esc_html($user_registration_kit_form_register_hide_labels) != 'yes' ){ ?>
                    <label for="<?php echo esc_attr($bp_all_field['name']); ?>" class="tk-lp-label"><?php echo esc_html($bp_all_field['label']); ?></label>
                    <?php } ?>
                    <input class="tk-lp-input" id="<?php echo esc_attr($bp_all_field['name']); ?>" name="<?php echo esc_attr($bp_all_field['name']); ?>" type="tel" placeholder="<?php if( esc_html($user_registration_kit_form_register_hide_labels) == 'yes' ){ echo esc_html($bp_all_field['label']); } ?>" />
                </div>
                <?php
                endif;
                if ( 'url' == $bp_all_field['type'] ) :
                ?>
                <div class="tk-lp-form-item">
                    <?php if( esc_html($user_registration_kit_form_register_hide_labels) != 'yes' ){ ?>
                    <label for="<?php echo esc_attr($bp_all_field['name']); ?>" class="tk-lp-label"><?php echo esc_html($bp_all_field['label']); ?></label>
                    <?php } ?>
                    <input class="tk-lp-input" id="<?php echo esc_attr($bp_all_field['name']); ?>" name="<?php echo esc_attr($bp_all_field['name']); ?>" type="text" inputmode="url" placeholder="<?php if( esc_html($user_registration_kit_form_register_hide_labels) == 'yes' ){ echo esc_html($bp_all_field['label']); } ?>" />
                </div>
                <?php
                endif;
                if ( 'textarea' == $bp_all_field['type'] || 'wp-biography' == $bp_all_field['type'] ) :
                ?>
                <div class="tk-lp-form-item">
                    <?php if( esc_html($user_registration_kit_form_register_hide_labels) != 'yes' ){ ?>
                    <label for="<?php echo esc_attr($bp_all_field['name']); ?>" class="tk-lp-label"><?php echo esc_html($bp_all_field['label']); ?></label>
                    <?php } ?>
                    <textarea class="tk-lp-input" id="<?php echo esc_attr($bp_all_field['name']); ?>" name="<?php echo esc_attr($bp_all_field['name']); ?>" placeholder="<?php if( esc_html($user_registration_kit_form_register_hide_labels) == 'yes' ){ echo esc_html($bp_all_field['label']); } ?>"></textarea>
                </div>
                <?php
                endif;
                if ( 'datebox' == $bp_all_field['type'] ) :
                ?>
                <div class="tk-lp-form-item tk-lp-form-item-date">
                    <div class="tk-lp-form-group">
                        <?php if( esc_html($user_registration_kit_form_register_hide_labels) != 'yes' ){ ?>
                        <label class="tk-lp-label"><?php esc_html_e( 'Day', 'user-registration-kit' ); ?></label>
                        <?php } ?>
                        <select class="tk-lp-input" name="<?php echo esc_attr($bp_all_field['name']); ?>_day">
                            <?php if( esc_html($user_registration_kit_form_register_hide_labels) == 'yes' ){ ?>
                            <option value="" disabled selected><?php esc_html_e( 'Day', 'user-registration-kit' ); ?></option>
                            <?php } ?>
                            <?php for ( $i = 1; $i < 32; ++$i ) { ?>
                            <?php echo sprintf( '<option value="%1$s">%2$s</option>', (int) $i, (int) $i ); ?>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="tk-lp-form-group">
                        <?php if( esc_html($user_registration_kit_form_register_hide_labels) != 'yes' ){ ?>
                        <label class="tk-lp-label"><?php esc_html_e( 'Month', 'user-registration-kit' ); ?></label>
                        <?php } ?>
                        <select class="tk-lp-input" name="<?php echo esc_attr($bp_all_field['name']); ?>_month">
                            <?php if( esc_html($user_registration_kit_form_register_hide_labels) == 'yes' ){ ?>
                            <option value="" disabled selected><?php esc_html_e( 'Month', 'user-registration-kit' ); ?></option>
                            <?php } ?>
                            <?php 
                                $months = array(
                                    __( 'January',   'buddypress' ),
                                    __( 'February',  'buddypress' ),
                                    __( 'March',     'buddypress' ),
                                    __( 'April',     'buddypress' ),
                                    __( 'May',       'buddypress' ),
                                    __( 'June',      'buddypress' ),
                                    __( 'July',      'buddypress' ),
                                    __( 'August',    'buddypress' ),
                                    __( 'September', 'buddypress' ),
                                    __( 'October',   'buddypress' ),
                                    __( 'November',  'buddypress' ),
                                    __( 'December',  'buddypress' )
                                );
                            for ( $i = 0; $i < 12; ++$i ) { ?>
                            <?php echo sprintf( '<option value="%1$s">%2$s</option>', $months[$i], $months[$i] ); ?>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="tk-lp-form-group">
                        <?php if( esc_html($user_registration_kit_form_register_hide_labels) != 'yes' ){ ?>
                        <label class="tk-lp-label"><?php esc_html_e( 'Year', 'user-registration-kit' ); ?></label>
                        <?php } ?>
                        <select class="tk-lp-input" name="<?php echo esc_attr($bp_all_field['name']); ?>_year">
                            <?php if( esc_html($user_registration_kit_form_register_hide_labels) == 'yes' ){ ?>
                            <option value="" disabled selected><?php esc_html_e( 'Year', 'user-registration-kit' ); ?></option>
                            <?php } ?>
                            <?php for ( $i = date('Y', time()-60*60*24); $i > 1901; $i-- ) { ?>
                            <?php echo sprintf( '<option value="%1$s">%2$s</option>', (int) $i, (int) $i ); ?>
                            <?php } ?>
                        </select>
                    </div>
                </div>
                <?php
                endif;
                if ( 'selectbox' == $bp_all_field['type'] ) :
                ?>
                <div class="tk-lp-form-item">
                    <?php if( esc_html($user_registration_kit_form_register_hide_labels) != 'yes' ){ ?>
                    <label for="<?php echo esc_attr($bp_all_field['name']); ?>" class="tk-lp-label"><?php echo esc_html($bp_all_field['label']); ?></label>
                    <?php } ?>
                    <select class="tk-lp-input" name="<?php echo esc_attr($bp_all_field['name']); ?>">
                        <?php echo wp_kses($bp_all_field['options'], array(
                            'option' => array(
                                'selected' => array(),
                                'value' => array()
                            )
                        )); ?>
                    </select>
                </div>
                <?php
                endif;
                if ( 'multiselectbox' == $bp_all_field['type'] ) :
                ?>
                <div class="tk-lp-form-item">
                    <?php if( esc_html($user_registration_kit_form_register_hide_labels) != 'yes' ){ ?>
                    <label for="<?php echo esc_attr($bp_all_field['name']); ?>" class="tk-lp-label"><?php echo esc_html($bp_all_field['label']); ?></label>
                    <?php } ?>
                    <select multiple class="tk-lp-input" name="<?php echo esc_attr($bp_all_field['name']); ?>">
                        <?php echo wp_kses($bp_all_field['options'], array(
                            'option' => array(
                                'selected' => array(),
                                'value' => array()
                            )
                        )); ?>
                    </select>
                </div>
                <?php
                endif;
                if ( 'checkbox' == $bp_all_field['type'] ) :
                    $field_ch = new BP_XProfile_Field($bp_all_field['id']);
                    $options_ch = $field_ch->get_children( true );
                ?>
                <div class="tk-lp-form-item">
                    <?php if( esc_html($user_registration_kit_form_register_hide_labels) != 'yes' ){ ?>
                    <label for="<?php echo esc_attr($bp_all_field['name']); ?>" class="tk-lp-label"><?php echo esc_html($bp_all_field['label']); ?></label>
                    <?php } ?>
                    <?php
                    if(!empty($options_ch)){
                    foreach($options_ch as $options_val){
                    ?>
                    <div class="tk-lp-checkbox">
                        <label>
                            <input type="checkbox" name="<?php echo esc_attr($bp_all_field['name']) . '[]'; ?>" value="<?php echo esc_attr($options_val->name); ?>" >
                            <?php echo esc_html($options_val->name); ?>
                        </label>
                        </div>
                    <?php
                    }
                    }
                    ?>
                </div>
                <?php
                endif;
                if ( 'radio' == $bp_all_field['type'] ) :
                    $field = new BP_XProfile_Field($bp_all_field['id']);
                    $options = $field->get_children( true );
                ?>
                <div class="tk-lp-form-item">
                    <?php if( esc_html($user_registration_kit_form_register_hide_labels) != 'yes' ){ ?>
                    <label for="<?php echo esc_attr($bp_all_field['name']); ?>" class="tk-lp-label"><?php echo esc_html($bp_all_field['label']); ?></label>
                    <?php } ?>
                    <?php
                    if(!empty($options)){
                    foreach($options as $options_val){
                    ?>
                    <div class="tk-lp-radio-button">
                        <label>
                            <input type="radio" name="<?php echo esc_attr($bp_all_field['name']); ?>" value="<?php echo esc_attr($options_val->name); ?>">
                            <?php echo esc_html($options_val->name); ?>
                        </label>
                    </div>
                    <?php
                    }
                    }
                    ?>
                </div>
                <?php
                endif;
            }
        }

        $res = ob_get_contents();
        ob_end_clean();
        return $res;
    }
}

return new LRK_Admin();