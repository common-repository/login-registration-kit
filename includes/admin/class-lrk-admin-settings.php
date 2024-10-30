<?php
/**
 * UserRegistrationKit Admin Settings Class
 *
 * @class    LRK_Admin_Settings
 * @version  1.0.0
 * @package  UserRegistrationKit/Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * LRK_Admin_Settings Class.
 */
class LRK_Admin_Settings {
    
    /**
	 * Settings page output.
	 */
	public static function output() {
        $default_tab = 'general';
        $default_sub_tab = 'login';
        $tab = isset($_GET['tab']) ? sanitize_text_field($_GET['tab']) : sanitize_text_field($default_tab);
        $sub_tab = isset($_GET['subtab']) ? sanitize_text_field($_GET['subtab']) : sanitize_text_field($default_sub_tab);

        ?>
        <div class="wrap lrk-main-settings-wrap">
            <form method="POST" action="<?php echo esc_url($_SERVER['REQUEST_URI']); ?>" enctype="multipart/form-data">
                <input type="hidden" name="user_register_kit_settings" value="<?php echo (!$sub_tab) ? 'login' : $sub_tab; ?>">
                <h1><?php echo esc_html__( 'User Registration Settings', 'user-registration-kit' ); ?></h1>
                <nav class="nav-tab-wrapper">
                    <a href="?page=user-register-kit-settings" class="nav-tab <?php if($tab == 'general'){echo esc_attr('nav-tab-active'); } ?>"><?php echo esc_html__( 'General', 'user-registration-kit' ); ?></a>
                    <a href="?page=user-register-kit-settings&tab=captcha&subtab=captcha" class="nav-tab <?php if($sub_tab === 'captcha'){echo esc_attr('nav-tab-active'); } ?>">ReCaptcha</a>
                    <a href="?page=user-register-kit-settings&tab=user_menu&subtab=user_menu" class="nav-tab <?php if($sub_tab === 'user_menu'){echo esc_attr('nav-tab-active'); } ?>"><?php echo esc_html__( 'User Menu Dropdown', 'user-registration-kit' ); ?></a>
                    <a href="?page=user-register-kit-settings&tab=my_account&subtab=my_account" class="nav-tab <?php if($sub_tab === 'my_account'){echo esc_attr('nav-tab-active'); } ?>"><?php echo esc_html__( 'My account Section', 'user-registration-kit' ); ?></a>
                    <a href="?page=user-register-kit-settings&tab=advanced&subtab=advanced" class="nav-tab <?php if($sub_tab === 'advanced'){echo esc_attr('nav-tab-active'); } ?>"><?php echo esc_html__( 'Advanced', 'user-registration-kit' ); ?></a>
                </nav>

                <?php if( $tab == 'general' ){ ?>
                <div class="sub-nav">
                    <a href="?page=user-register-kit-settings&tab=general&subtab=login" class="<?php if($sub_tab == 'login'){echo esc_attr('active-tab'); } ?>"><?php echo esc_html__( 'Login Options', 'user-registration-kit' ); ?></a>
                    <a href="?page=user-register-kit-settings&tab=general&subtab=register" class="<?php if($sub_tab == 'register'){echo esc_attr('active-tab'); } ?>"><?php echo esc_html__( 'Register Options', 'user-registration-kit' ); ?></a>
                </div>
                <?php } ?>

                <div class="tab-content">
                    <table class="form-table">
                        <?php
                            if(!$tab){
                                self::output_fields(self::get_fields('login'));
                            }else{
                                self::output_fields(self::get_fields($sub_tab));
                            }
                        ?>
                    </table>
                </div>
                
                <p class="submit">
                    <?php if( $tab == 'general' ){ ?>
                    <div class="shordcodes">
                        <p><b><?php echo esc_html__( 'Register shordcode:', 'user-registration-kit' ); ?> </b><i>[user_registration_kit_register]</i></p>
                        <p><b><?php echo esc_html__( 'Sign in shordcode:', 'user-registration-kit' ); ?> </b><i>[user_registration_kit_sign]</i></p>
                        <p><b><?php echo esc_html__( 'Both shordcode:', 'user-registration-kit' ); ?> </b><i>[user_registration_kit_both]</i></p>
                    </div>
                    <?php } ?>

                    <?php if( $tab == 'my_account' ){ ?>
                    <div class="shordcodes">
                        <p><b><?php echo esc_html__( 'Account page shordcode:', 'user-registration-kit' ); ?> </b><i>[user_registration_kit_my_account]</i></p>
                    </div>
                    <?php } ?>
                    
                    <?php submit_button(); ?>
                    <?php wp_nonce_field( 'user-register-kit-settings' ); ?>
                </p>
            </form>
        </div>
        <?php
    }

    /**
	 * Output admin fields.
	 *
	 * Loops though the user registration options array and outputs each field.
	 *
	 * @param array[] $options Opens array to output.
	 */
	public static function output_fields( $options ) {
        foreach ( $options as $opt_k => $value ) {
			if ( ! isset( $value['type'] ) ) {
				continue;
            }
            if ( ! isset( $value['id'] ) ) {
				$value['id'] = '';
			}
			if ( ! isset( $value['row_class'] ) ) {
				$value['row_class'] = '';
			}
			if ( ! isset( $value['title'] ) ) {
				$value['title'] = isset( $value['name'] ) ? $value['name'] : '';
			}
			if ( ! isset( $value['class'] ) ) {
				$value['class'] = '';
			}
			if ( ! isset( $value['css'] ) ) {
				$value['css'] = '';
			}
			if ( ! isset( $value['default'] ) ) {
				$value['default'] = '';
			}
			if ( ! isset( $value['desc'] ) ) {
				$value['desc'] = '';
			}
			if ( ! isset( $value['placeholder'] ) ) {
				$value['placeholder'] = '';
            }
            if ( ! isset( $value['required'] ) ) {
				$value['required'] = false;
            }
            if ( ! isset( $value['desc_tip'] ) ) {
				$value['desc_tip'] = '';
            }
            
            // Custom attribute handling
			$custom_attributes = array();

			if ( ! empty( $value['custom_attributes'] ) && is_array( $value['custom_attributes'] ) ) {
				foreach ( $value['custom_attributes'] as $attribute => $attribute_value ) {
					$custom_attributes[] = esc_attr( $attribute ) . "=" . esc_attr( $attribute_value );
				}
            }
            
            $conditional_logic = ( isset( $value['conditional_logic'] ) ) ? $value['conditional_logic'] : '';

            

			// Description handling.
            switch ( $value['type'] ) {
                // Text boxes.
				case 'text':
                case 'number':
                    $repeater_val = '';
                    if( isset($value['repeater_val']) ){
                        $repeater_val = $value['repeater_val'];
                    }
                    $option_value = (!isset($value['repeater_val_option'])) ? self::get_option( $value['id'], $value['default'] ) : $value['repeater_val_option'];
                    if($repeater_val != ''){
                        $v_id = esc_attr( $repeater_val );
                        $value['class'] .= ' repeater-field';
                    }else{
                        $v_id = esc_attr( $value['id'] );
                    }
					?>

                    <tr valign="top" class="<?php echo esc_attr( $value['row_class'] ); ?>" <?php if( $conditional_logic != '' ){echo 'data-cond="'.$conditional_logic.'"';} ?>>
						<th scope="row" class="titledesc">
                            <label for="<?php echo esc_attr($v_id); ?>"><?php echo esc_html( $value['title'] ); ?></label>
                            <?php
                            if($value['desc_tip'] != ''){
                                echo sprintf('<span class="user-registration-kit-help-tip" data-tip="%s"></span>',
                                    htmlspecialchars(
                                        wp_kses(
                                            html_entity_decode( $value['desc_tip'] ),
                                            array(
                                                'br'     => array(),
                                                'em'     => array(),
                                                'strong' => array(),
                                                'small'  => array(),
                                                'span'   => array(),
                                                'ul'     => array(),
                                                'li'     => array(),
                                                'ol'     => array(),
                                                'p'      => array(),
                                                'b'      => array(),
                                                'a'      => array(
                                                    'href' => array(),
                                                    'target' => array(),
                                                ),
                                            )
                                        )
                                    )
                                );
                            }
                            ?>
                        </th>
						<td class="forminp forminp-<?php echo sanitize_title( $value['type'] ); ?>">
							<input
								name="<?php echo esc_attr($v_id); ?>"
								id="<?php echo esc_attr($v_id); ?>"
								type="<?php echo esc_attr( $value['type'] ); ?>"
								style="<?php echo esc_attr( $value['css'] ); ?>"
								value="<?php echo esc_attr( $option_value ); ?>"
								class="<?php echo esc_attr( $value['class'] ); ?>"
								placeholder="<?php echo esc_attr( $value['placeholder'] ); ?>"
								<?php echo esc_attr(implode( ' ', $custom_attributes )); ?>
                                <?php if( $value['required'] ){ echo 'required'; } ?>
								/>
						</td>
					</tr>
                    <?php
                    break;

                // Select boxes.
				case 'select':
				case 'multiselect':
                    $option_value = self::get_option( $value['id'], $value['default'] );
					?>
					<tr tiptip class="<?php echo esc_attr( $value['row_class'] ); ?>">
						<th scope="row" class="titledesc">
                            <label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
                            <?php
                            if($value['desc_tip'] != ''){
                                echo sprintf('<span class="user-registration-kit-help-tip" data-tip="%s"></span>',
                                    htmlspecialchars(
                                        wp_kses(
                                            html_entity_decode( $value['desc_tip'] ),
                                            array(
                                                'br'     => array(),
                                                'em'     => array(),
                                                'strong' => array(),
                                                'small'  => array(),
                                                'span'   => array(),
                                                'ul'     => array(),
                                                'li'     => array(),
                                                'ol'     => array(),
                                                'p'      => array(),
                                                'b'      => array(),
                                                'a'      => array(
                                                    'href' => array(),
                                                    'target' => array(),
                                                ),
                                            )
                                        )
                                    )
                                );
                            }
                            ?>
                        </th>
						<td class="forminp forminp-<?php echo esc_html( sanitize_title( $value['type'] ) ); ?>">
							<select
								name="<?php echo esc_attr( $value['id'] ); ?><?php echo ( 'multiselect' === $value['type'] ) ? '[]' : ''; ?>"
								id="<?php echo esc_attr( $value['id'] ); ?>"
								style="<?php echo esc_attr( $value['css'] ); ?>"
								class="<?php echo esc_attr( $value['class'] ); ?>"
								<?php echo esc_attr(implode( ' ', $custom_attributes )); ?>
								<?php echo ( 'multiselect' == $value['type'] ) ? 'multiple="multiple"' : ''; ?>
								>
								<?php
								foreach ( $value['options'] as $key => $val ) {
									?>
									<option value="<?php echo esc_attr( $key ); ?>"
									<?php
									if ( is_array( $option_value ) ) {
										selected( in_array( $key, $option_value ), true );
									} else {
										selected( $option_value, $key );
									}
									?>
									><?php echo esc_html( $val ); ?></option>
									<?php
								}
								?>
							</select>
						</td>
					</tr>
					<?php
                    break;
                
                 // Select boxes.
                case 'checkbox':
                    // $option_value = self::get_option( $value['id'], $value['default'] );
                    $repeater_val = '';
                    if( isset($value['repeater_val']) ){
                        $repeater_val = $value['repeater_val'];
                    }
                    $option_value = (!isset($value['repeater_val_option'])) ? self::get_option( $value['id'], $value['default'] ) : $value['repeater_val_option'];
                    if($repeater_val != ''){
                        $v_id = esc_attr( $repeater_val );
                        $value['class'] .= ' repeater-field';
                    }else{
                        $v_id = esc_attr( $value['id'] );
                    }
					?>
                    <tr valign="top" class="<?php echo esc_attr( $value['row_class'] ); ?>">
                        <th scope="row" class="titledesc">
							<label for="<?php echo esc_attr( $v_id ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
                            <?php
                            if($value['desc_tip'] != ''){
                                echo sprintf('<span class="user-registration-kit-help-tip" data-tip="%s"></span>',
                                    htmlspecialchars(
                                        wp_kses(
                                            html_entity_decode( $value['desc_tip'] ),
                                            array(
                                                'br'     => array(),
                                                'em'     => array(),
                                                'strong' => array(),
                                                'small'  => array(),
                                                'span'   => array(),
                                                'ul'     => array(),
                                                'li'     => array(),
                                                'ol'     => array(),
                                                'p'      => array(),
                                                'b'      => array(),
                                                'a'      => array(
                                                    'href' => array(),
                                                    'target' => array(),
                                                ),
                                            )
                                        )
                                    )
                                );
                            }
                            ?>
						</th>
                        <td class="forminp forminp-<?php echo esc_html( sanitize_title( $value['type'] ) ); ?>">
                            <?php if($repeater_val == ''){ ?>
                            <input
								name="<?php echo esc_attr( $v_id ); ?>"
								id="<?php echo esc_attr( $v_id ); ?>"
								type="checkbox"
								class="<?php echo esc_attr( isset( $value['class'] ) ? $value['class'] : '' ); ?>"
								value="1"
								<?php checked( $option_value, 'yes' ); ?>
								<?php echo esc_attr(implode( ' ', $custom_attributes )); ?>
							/>
                            <?php } else { ?> 
                            <div class="check-rep">
                                <?php 
                                $check_inp_val = ($option_value == 'yes') ? 'yes' : 'no';
                                ?>
                                <input name="<?php echo esc_attr( $v_id ); ?>" class="send-val" type="hidden" value="<?php echo esc_attr($check_inp_val); ?>" />
                                <input
                                    type="checkbox"
                                    class="<?php echo esc_attr( isset( $value['class'] ) ? $value['class'] : '' ); ?>"
                                    value="1"
                                    <?php checked( $option_value, 'yes' ); ?>
                                    <?php echo esc_attr(implode( ' ', $custom_attributes )); ?>
                                />
                            </div>
                            <?php } ?>
                        </td>
                    </tr>
                    <?php
                    break;
                
                // Textarea
                case 'textarea':
                    $repeater_val = '';
                    if( isset($value['repeater_val']) ){
                        $repeater_val = $value['repeater_val'];
                    }
                    $option_value = (!isset($value['repeater_val_option'])) ? self::get_option( $value['id'], $value['default'] ) : $value['repeater_val_option'];
                    if($repeater_val != ''){
                        $v_id = esc_attr( $repeater_val );
                        $value['class'] .= ' repeater-field';
                    }else{
                        $v_id = esc_attr( $value['id'] );
                    }
					?>
					<tr valign="top" class="<?php echo esc_attr( $value['row_class'] ); ?>">
						<th scope="row" class="titledesc">
							<label for="<?php echo esc_attr($v_id); ?>"><?php echo esc_html( $value['title'] ); ?></label>
							<?php
                            if($value['desc_tip'] != ''){
                                echo sprintf('<span class="user-registration-kit-help-tip" data-tip="%s"></span>',
                                    htmlspecialchars(
                                        wp_kses(
                                            html_entity_decode( $value['desc_tip'] ),
                                            array(
                                                'br'     => array(),
                                                'em'     => array(),
                                                'strong' => array(),
                                                'small'  => array(),
                                                'span'   => array(),
                                                'ul'     => array(),
                                                'li'     => array(),
                                                'ol'     => array(),
                                                'p'      => array(),
                                                'b'      => array(),
                                                'a'      => array(
                                                    'href' => array(),
                                                    'target' => array(),
                                                ),
                                            )
                                        )
                                    )
                                );
                            }
                            ?>
						</th>
						<td class="forminp forminp-<?php echo sanitize_title( $value['type'] ); ?>">
							<textarea
								name="<?php echo esc_attr($v_id); ?>"
								id="<?php echo esc_attr($v_id); ?>"
								style="<?php echo esc_attr( $value['css'] ); ?>"
								class="<?php echo esc_attr( $value['class'] ); ?>"
								placeholder="<?php echo esc_attr( $value['placeholder'] ); ?>"
								<?php echo esc_attr(implode( ' ', $custom_attributes )); ?>
								><?php echo esc_textarea( $option_value ); ?></textarea>
						</td>
					</tr>
					<?php
                    break;
                case 'color':
                    $option_value = self::get_option( $value['id'], $value['default'] );
                    ?>
                    <tr valign="top" class="<?php echo esc_attr( $value['row_class'] ); ?>">
						<th scope="row" class="titledesc">
							<label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
                            <?php
                            if($value['desc_tip'] != ''){
                                echo sprintf('<span class="user-registration-kit-help-tip" data-tip="%s"></span>',
                                    htmlspecialchars(
                                        wp_kses(
                                            html_entity_decode( $value['desc_tip'] ),
                                            array(
                                                'br'     => array(),
                                                'em'     => array(),
                                                'strong' => array(),
                                                'small'  => array(),
                                                'span'   => array(),
                                                'ul'     => array(),
                                                'li'     => array(),
                                                'ol'     => array(),
                                                'p'      => array(),
                                                'b'      => array(),
                                                'a'      => array(
                                                    'href' => array(),
                                                    'target' => array(),
                                                ),
                                            )
                                        )
                                    )
                                );
                            }
                            ?>
						</th>
						<td class="forminp forminp-<?php echo sanitize_title( $value['type'] ); ?>">&lrm;
							<span class="colorpickpreview" style="background: <?php echo esc_attr( $option_value ); ?>"></span>
							<input
								name="<?php echo esc_attr( $value['id'] ); ?>"
								id="<?php echo esc_attr( $value['id'] ); ?>"
								type="text"
								dir="ltr"
								style="<?php echo esc_attr( $value['css'] ); ?>"
								value="<?php echo esc_attr( $option_value ); ?>"
								class="<?php echo esc_attr( $value['class'] ); ?>colorpick"
								placeholder="<?php echo esc_attr( $value['placeholder'] ); ?>"
								<?php echo esc_attr(implode( ' ', $custom_attributes )); ?>
								/>
								<div id="colorPickerDiv_<?php echo esc_attr( $value['id'] ); ?>" class="colorpickdiv" style="z-index: 100;background:#eee;border:1px solid #ccc;position:absolute;display:none;"></div>
						</td>
					</tr>
                    <?php
                    break;
                case 'repeater':
                    $option_value = self::get_option( $value['id'], $value['default'] );
                    ?>
                        <tr valign="top" class="<?php echo esc_attr( $value['row_class'] ); ?>">
                            <th scope="row" class="titledesc">
                                <label for="<?php echo esc_attr( $value['id'] ); ?>"><?php echo esc_html( $value['title'] ); ?></label>
                            </th>
                            <td class="forminp forminp-<?php echo sanitize_title( $value['type'] ); ?>">
                                <?php
                                foreach( $value['options'] as $key => $val ){
                                    $value['options'][$key]['repeater_val'] = $value['id'] . '[' . $val['id'] . '][]';
                                    $value['options'][$key]['repeater_id'] = $value['id'];
                                }

                                if( !empty($option_value) ){
                                    foreach($option_value as $option_value_k => $option_value_v){
                                        foreach( $value['options'] as $key => $val ){
                                            $option_val = (isset($option_value_v[$val['id']])) ? $option_value_v[$val['id']] : '';
                                            $value['options'][$key]['repeater_val_option'] = $option_val;
                                        }
                                        ?>
                                        <span class="rep-item">
                                            <?php if( $option_value_k > 0 ){ ?>
                                            <div class="lrk-del-repeater-item"><?php echo esc_html__( 'Delete', 'user-registration-kit' ); ?></div>
                                            <?php } ?>
                                            <table>
                                            <?php
                                                self::output_fields($value['options']);
                                            ?> 
                                            </table>
                                        </span>
                                        <?php
                                    }
                                }else{
                                    ?>
                                    <span class="rep-item">
                                        <table>
                                        <?php
                                            self::output_fields($value['options']);
                                        ?> 
                                        </table>
                                    </span>
                                    <?php
                                }
                                ?>                                
                                <button class="lrk-add-new-repeater-el button button-primary"><?php echo esc_html__( 'Add new', 'user-registration-kit' ); ?></button>
                            </td>
                        </tr>
                    <?php
                    break;
            }
        }
    }

    /**
	 * Save admin fields.
	 *
	 * Loops though the user registration options array and outputs each field.
	 *
	 * @param  array $options Options array to output.
	 *
	 * @return bool
	 */
	public static function save_fields() {
        if ( isset($_POST['user_register_kit_settings']) ) {

            $fields_type = sanitize_post( $_POST['user_register_kit_settings'] );
            $options = self::get_fields($fields_type);

            if(empty($options)){
                return false;
            }

            // Options to update will be stored here and saved later.
            $update_options = array();

            // Loop options and get values to save.
            foreach ( $options as $option ) {
                if ( ! isset( $option['id'] ) || ! isset( $option['type'] ) ) {
                    continue;
                }

                if ( strstr( $option['id'], '[' ) ) {
                    parse_str( $option['id'], $option_name_array );
                    $option_name = current( array_keys( $option_name_array ) );
                    $raw_value    = isset( $_POST[ $option_name ] ) ? sanitize_post( $_POST[ $option_name ] ) : null;
                } else {
                    $option_name  = $option['id'];
                    $setting_name = '';
                    $raw_value    = isset( $_POST[ $option['id'] ] ) ? sanitize_post( $_POST[ $option['id'] ] ) : null;
                }

                    $value = null;
                    // Format the value based on option type.
                    switch ( $option['type'] ) {
                        case 'checkbox':
                            $value = '1' === $raw_value || 'yes' === $raw_value ? 'yes' : 'no';
                            break;
                        case 'textarea':
                            $value = wp_kses_post( trim( $raw_value ) );
                            break;
                        case 'multiselect':
                            $value = array_filter( array_map( 'self::lrk_clean', (array) $raw_value ) );
                            break;
                        case 'select':
                            $allowed_values = !empty( $option['options'] ) ? array_keys( $option['options'] ) : array();
                            if ( empty( $option['default'] ) && empty( $allowed_values ) ) {
                                $value = null;
                                break;
                            }
                            $default = ( empty( $option['default'] ) ? $allowed_values[0] : $option['default'] );
                            if( !in_array($raw_value, $allowed_values) ){
                                $value = $default;
                            }else{
                                $value = $raw_value;
                            }
                            break;
                        case 'repeater':
                            $rv = array();

                            if(!empty($raw_value)){
                                $count = count( reset($raw_value) ) - 1;
                                for($el = 0; $el <= $count; $el++){
                                    $new_el = array();
                                    foreach($raw_value as $raw_value_k => $raw_value_v){
                                        if(isset($raw_value_v[$el])){
                                            $new_el[$raw_value_k] = $raw_value_v[$el];
                                        }
                                    }
                                    array_push($rv, $new_el);
                                }
                            }

                            $value = array_filter( array_map( 'self::lrk_clean_with_html', (array) $rv ) );
                            break;
                        default:
                            $value = self::lrk_clean($raw_value);
                            break;
                    }

                    if( isset($option['update_option']) ){
                        update_option($option['update_option'], 1);
                    }

                    if ( is_null( $value ) ) {
                        continue;
                    }

                    // Check if option is an array and handle that differently to single values.
                    if ( $option_name && $setting_name ) {
                        if ( ! isset( $update_options[ $option_name ] ) ) {
                            $update_options[ $option_name ] = get_option( $option_name, array() );
                        }
                        if ( ! is_array( $update_options[ $option_name ] ) ) {
                            $update_options[ $option_name ] = array();
                        }
                        $update_options[ $option_name ][ $setting_name ] = $value;
                    } else {
                        $update_options[ $option_name ] = $value;
                    }
            }

            // Save all options in our array.
            foreach ( $update_options as $name => $value ) {
                update_option( $name, $value );
            }

            return true;
        }else{
            return false;
        }
    }

    public static function lrk_clean( $var ) {
        if ( is_array( $var ) ) {
            return array_map( 'self::lrk_clean', $var );
        } else {
            return is_scalar( $var ) ? sanitize_text_field( stripslashes($var) ) : stripslashes($var);
        }
    }

    public static function lrk_clean_with_html( $var ) {
        if ( is_array( $var ) ) {
            return array_map( 'self::lrk_clean_with_html', $var );
        } else {
            return is_scalar( $var ) ?  wp_kses_post( trim( $var ) ) : $var;
        }
    }

    /**
	 * Get a setting from the settings API.
	 *
	 * @param mixed $option_name Option Name.
	 * @param mixed $default Default.
	 *
	 * @return string
	 */
	public static function get_option( $option_name, $default = '' ) {
        $option_value = get_option( $option_name );
        return (null === $option_value) ? $default : $option_value;
    }

    public static function get_global_settings( $page = '' ) {
        $fields = self::get_fields($page);
        $return_array = array();
        foreach($fields as $field_val){
            if(isset($field_val['id'])){
                $default = ( isset($field_val['default']) ) ? $field_val['default'] : '';
                $val = self::get_option($field_val['id'], $default);
                $return_array[$field_val['id']] = $val;
            }
        }

        return $return_array;
    }

    private static function get_user_roles(){
        global $wp_roles;

        if ( ! class_exists( 'WP_Roles' ) ) {
            return;
        }

        if ( ! isset( $wp_roles ) ) {
            $wp_roles = new WP_Roles(); // @codingStandardsIgnoreLine
        }

        $roles     = isset( $wp_roles->roles ) ? $wp_roles->roles : array();
        $all_roles = array();

        foreach ( $roles as $role_key => $role ) {
            $all_roles[ $role_key ] = $role['name'];
        }
        return $all_roles;
    }

    private static function get_all_menus(){
        $result = array();
        $all_menus = wp_get_nav_menus();
        if(!empty($all_menus)){
            foreach($all_menus as $menu){
                $result[$menu->slug] = $menu->name;
            }
        }

        return $result;
    }

    private static function get_all_pages(){
        $result = array();
        $all_pages = get_pages();
        if(!empty($all_pages)){
            foreach($all_pages as $page){
                $result[$page->ID] = $page->post_title;
            }
        }

        return $result;
    }

    public static function get_fields( $page = '' ){
        $fields_result = array();
        $fields_login = array(
            array(
                'type' => 'multiselect',
                'id' => 'user_registration_kit_form_login_prevent_access',
                'title' => esc_html__( 'Prevent dashboard access', 'user-registration-kit' ),
                'options' => self::get_user_roles(),
                'desc_tip' => esc_html__( 'This option lets you limit which roles you are willing to prevent dashboard access.', 'user-registration-kit' )
            ),
            array(
                'type' => 'select',
                'id' => 'user_registration_kit_form_login_type',
                'title' => esc_html__( 'Login form template', 'user-registration-kit' ),
                'default' => 'simple',
                'options' => array(
                    'simple' => esc_html__( 'Simple', 'user-registration-kit' ),
                    'custom_shortcode' => esc_html__( 'Custom shortcode', 'user-registration-kit' )
                ),
                'class' => 'conditional',
                'custom_attributes' => array(
                    'data-conditional' => 'shortcode'
                ),
                'desc_tip' => esc_html__( 'Choose the login form template.', 'user-registration-kit' )
            ),
            array(
                'type' => 'text',
                'id' => 'user_registration_kit_form_login_shortcode',
                'title' => esc_html__( 'Shortcode', 'user-registration-kit' ),
                'conditional_logic' => 'custom_shortcode',
                'custom_attributes' => array(
                    'data-conditional' => 'shortcode'
                )
            ),
            array(
                'type' => 'text',
                'id' => 'user_registration_kit_form_login_redirect',
                'title' => esc_html__( 'Redirect URL', 'user-registration-kit' ),
                'desc_tip' => esc_html__( 'URL to redirect to after login.', 'user-registration-kit' )
            ),
            array(
                'type' => 'checkbox',
                'id' => 'user_registration_kit_form_login_hide_password',
                'title' => esc_html__( 'Enable hide/show password', 'user-registration-kit' ),
                'default' => 'yes',
                'desc_tip' => esc_html__( 'Check to enable hide/show password icon.', 'user-registration-kit' )
            ),
            array(
                'type' => 'checkbox',
                'id' => 'user_registration_kit_form_login_hide_labels',
                'title' => esc_html__( 'Hide field labels', 'user-registration-kit' ),
                'default' => '',
                'desc_tip' => esc_html__( 'Check to hide field labels.', 'user-registration-kit' )
            ),
            array(
                'type' => 'checkbox',
                'id' => 'user_registration_kit_form_login_remember_me',
                'title' => esc_html__( 'Enable remember me', 'user-registration-kit' ),
                'default' => 'yes',
                'desc_tip' => esc_html__( 'Check to enable/disable remember me.', 'user-registration-kit' )
            ),
            array(
                'type' => 'checkbox',
                'id' => 'user_registration_kit_form_login_lost_password',
                'title' => esc_html__( 'Enable lost password', 'user-registration-kit' ),
                'default' => 'yes',
                'desc_tip' => esc_html__( 'Check to enable/disable lost password.', 'user-registration-kit' )
            ),
        );

        $default_register_user_role = get_option('default_role', true);
        $fields_register = array(
            array(
                'type' => 'select',
                'id' => 'user_registration_kit_form_register_option',
                'title' => esc_html__( 'User login option', 'user-registration-kit' ),
                'default' => 'default',
                'options' => array(
                    'default' => esc_html__( 'Manual login after registration', 'user-registration-kit' ),
                    'auto_login' => esc_html__( 'Auto login after registration', 'user-registration-kit' ),
                ),
                'desc_tip' => esc_html__( 'This option lets you choose login option after user registration.', 'user-registration-kit' )
            ),
            array(
                'type' => 'select',
                'id' => 'user_registration_kit_form_register_user_role',
                'title' => esc_html__( 'Default user role', 'user-registration-kit' ),
                'options' => self::get_user_roles(),
                'default' => esc_html($default_register_user_role),
                'desc_tip' => esc_html__( 'Default role for the users registered through this form.', 'user-registration-kit' )
            ),
            array(
                'type' => 'select',
                'id' => 'user_registration_kit_form_register_type',
                'title' => esc_html__( 'Register form template', 'user-registration-kit' ),
                'default' => 'simple',
                'options' => array(
                    'simple' => esc_html__( 'Simple', 'user-registration-kit' ),
                    'custom_shortcode' => esc_html__( 'Custom shortcode', 'user-registration-kit' )
                ),
                'class' => 'conditional',
                'custom_attributes' => array(
                    'data-conditional' => 'shortcode'
                ),
                'desc_tip' => esc_html__( 'Choose the register form template.', 'user-registration-kit' )
            ),
            array(
                'type' => 'text',
                'id' => 'user_registration_kit_form_register_shortcode',
                'title' => esc_html__( 'Shortcode', 'user-registration-kit' ),
                'conditional_logic' => 'custom_shortcode',
                'custom_attributes' => array(
                    'data-conditional' => 'shortcode'
                )
            ),
        );

        $fields_register_bp = array(
            'type' => 'select',
            'id' => 'user_registration_kit_form_register_fields',
            'title' => esc_html__( 'Fields to display', 'user-registration-kit' ),
            'desc_tip' => '<b>- Basic</b><br>Form is non-editable and consists of basic WP fields \'First name\', \'Last name\', \'Username\', \'Email\', \'Password\' fields.<br><br><b>- Standard</b><br>Form is editable via Users > Profile fields. Only the required field will be displayed in the form.
            \'Username\', \'Email\', \'Password\' are non-editable fields.<br><br><b>- Extended</b><br>Form is editable via Users > Profile fields. Only the required field will be displayed in the form.
            After filling in form fields user will be redirected to the Register page, where can complete other Profile Info Fields
            \'Username\', \'Email\' are non-editable fields.',
            'default' => 'basic',
            'options' => array(
                'basic' => esc_html__( 'Basic', 'user-registration-kit' ),
                'required' => esc_html__( 'Required', 'user-registration-kit' ),
                'all_fields' => esc_html__( 'All Fields', 'user-registration-kit' ),
            )
        );

        if( class_exists('BuddyPress') && bp_is_active( 'xprofile' ) ){
            array_push( $fields_register, $fields_register_bp );
        }

        $fields_register_add = array(
            array(
                'type' => 'text',
                'id' => 'user_registration_kit_form_register_redirect',
                'title' => esc_html__( 'Redirect URL', 'user-registration-kit' ),
                'desc_tip' => esc_html__( 'URL to redirect to after registration.', 'user-registration-kit' )
            ),
            array(
                'type' => 'checkbox',
                'id' => 'user_registration_kit_form_register_hide_labels',
                'title' => esc_html__( 'Hide field labels', 'user-registration-kit' ),
                'default' => '',
                'desc_tip' => esc_html__( 'Check to hide field labels.', 'user-registration-kit' )
            ),
            array(
                'type' => 'checkbox',
                'id' => 'user_registration_kit_register_disable_page_login',
                'title' => esc_html__( 'Disable standard registration page.', 'user-registration-kit' ),
                'default' => 'yes',
                'desc_tip' => esc_html__( 'Disable page url '.site_url().'/wp-login.php?action=register.', 'user-registration-kit' ),
                'class' => 'conditional',
                'custom_attributes' => array(
                    'data-conditional' => 'user_registration_kit_register_page_login_redirect'
                )
            ),
            array(
                'type' => 'text',
                'id' => 'user_registration_kit_register_page_login_redirect',
                'title' => esc_html__( 'Standart registration redirect URL', 'user-registration-kit' ),
                'conditional_logic' => '1',
                'custom_attributes' => array(
                    'data-conditional' => 'user_registration_kit_register_page_login_redirect'
                )
            ),
        );

        $fields_register = array_merge($fields_register, $fields_register_add);

        $fields_captcha = array(
            array(
                'type' => 'checkbox',
                'id' => 'user_registration_kit_captcha',
                'title' => esc_html__( 'Enable Google reCaptcha V3', 'user-registration-kit' ),
                'desc_tip' => esc_html__( 'About <a href="https://www.google.com/recaptcha" target="_blank"> reCaptcha </a>.', 'user-registration-kit' )
            ),
            array(
                'type' => 'text',
                'id' => 'user_registration_kit_captcha_site_key',
                'title' => esc_html__( 'ReCaptcha V3 Site Key', 'user-registration-kit' ),
                'desc_tip' => esc_html__( 'Get site key from google <a href="https://www.google.com/recaptcha" target="_blank"> reCaptcha </a>.', 'user-registration-kit' )
            ),
            array(
                'type' => 'text',
                'id' => 'user_registration_kit_captcha_secret_key',
                'title' => esc_html__( 'ReCaptcha V3 Secret Key', 'user-registration-kit' ),
                'desc_tip' => esc_html__( 'Get secret key from google <a href="https://www.google.com/recaptcha" target="_blank"> reCaptcha </a>.', 'user-registration-kit' )
            )
        );

        $fields_user_menu = array(
            array(
                'type' => 'checkbox',
                'id' => 'user_registration_kit_user_menu_enable',
                'title' => esc_html__( 'In the end of navigation', 'user-registration-kit' ),
                'desc_tip' => esc_html__( 'Set User Menu Dropdown as a last menu item.', 'user-registration-kit' )
            ),
            array(
                'type' => 'select',
                'id' => 'user_registration_kit_user_menu_navigation',
                'title' => esc_html__( 'Select menu', 'user-registration-kit' ),
                'desc_tip' => esc_html__( 'Select navigation for user menu dropdown.', 'user-registration-kit' ),
                'options' => self::get_all_menus()
            ),
            array(
                'type' => 'number',
                'id' => 'user_registration_kit_user_menu_avatar_size',
                'title' => esc_html__( 'Avatar size (width px)', 'user-registration-kit' ),
                'default' => '40',
                'desc_tip' => esc_html__( 'This option lets you enter the avatar image with.', 'user-registration-kit' ),
            ),
            array(
                'type' => 'text',
                'id' => 'user_registration_kit_user_menu_avatar_text',
                'title' => esc_html__( 'Text with avatar', 'user-registration-kit' ),
                'desc_tip' => esc_html__( 'This option lets you enter the text with avatar.', 'user-registration-kit' ),
                'default' => esc_html__( 'Your account', 'user-registration-kit' ),
            ),
            array(
                'type' => 'textarea',
                'id' => 'user_registration_kit_user_menu_no_login_text',
                'title' => esc_html__( 'No user login text (HTML)', 'user-registration-kit' ),
                'default' => esc_html__( 'Login', 'user-registration-kit' ),
            ),
            array(
                'type' => 'color',
                'id' => 'user_registration_kit_user_menu_icon_color',
                'title' => esc_html__( 'Icon color', 'user-registration-kit' ),
                'default' => '#000000'
            ),
        );

        $fields_my_account = array(
            array(
                'type' => 'select',
                'id' => 'user_registration_kit_account_page',
                'title' => esc_html__( 'My account page', 'user-registration-kit' ),
                'desc_tip' => esc_html__( 'Select the page which contains your account settings: [user_registration_kit_my_account].', 'user-registration-kit' ),
                'options' => self::get_all_pages()
            ),
            array(
                'type' => 'repeater',
                'id' => 'user_registration_kit_account_tabs',
                'title' => esc_html__( 'Tabs', 'user-registration-kit' ),
                'update_option' => 'lrk_flush_rewrite_rules',
                'options' => array(
                    array(
                        'type' => 'text',
                        'id' => 'user_registration_kit_account_tabs_title',
                        'title' => esc_html__( 'Title *', 'user-registration-kit' ),
                        'required' => true
                    ),
                    array(
                        'type' => 'textarea',
                        'id' => 'user_registration_kit_account_tabs_text',
                        'title' => esc_html__( 'Text', 'user-registration-kit' ),
                    ),
                    array(
                        'type' => 'checkbox',
                        'id' => 'user_registration_kit_account_tabs_show_in_menu',
                        'title' => esc_html__( 'Show in drop-down', 'user-registration-kit' ),
                    ),
                )
            ),
        );

        $fields_advanced = array(
            array(
                'type' => 'textarea',
                'id' => 'user_registration_kit_advanced_css',
                'title' => esc_html__( 'Custom CSS', 'user-registration-kit' ),
                'default' => '',
                'class' => 'reg-kit-code-editor',
            ),
        );

        if( $page == 'login' ){
            $fields_result = $fields_login;
        }elseif( $page == 'register' ){
            $fields_result = $fields_register;
        }elseif( $page == 'captcha' ){
            $fields_result = $fields_captcha;
        }elseif( $page == 'user_menu' ){
            $fields_result = $fields_user_menu;
        }elseif( $page == 'my_account' ){
            $fields_result = $fields_my_account;
        }elseif( $page == 'advanced' ){
            $fields_result = $fields_advanced;
        }else{
            $fields_result = array_merge($fields_login, $fields_register, $fields_captcha, $fields_user_menu, $fields_my_account, $fields_advanced);
        }

        return $fields_result;
    }
}