<?php
/**
 * $user_registration_kit_form_register_type
 * $user_registration_kit_form_register_shortcode
 * $user_registration_kit_form_register_fields
 * $user_registration_kit_form_register_user_option
 * $user_registration_kit_form_register_hide_labels
 * $user_registration_kit_captcha
 * $user_registration_kit_captcha_site_key
 * $user_registration_kit_captcha_secret_key
 * $user_registration_kit_form_register_redirect
 * $user_registration_kit_form_register_option
 * $both
 * $users_can_register
 * $unique_id
*/

$unique_id_registration = uniqid( 'tk_lp_registration_id' );

if( esc_html($user_registration_kit_form_register_type) == 'simple' ):
?>

<form id="sign-up<?php echo esc_attr($unique_id); ?>" class="tk-lp-form user-register-kit-register <?php if($both){echo 'tk-lp-tabs-form-content';}else{echo 'active';} ?>" data-handler="lrk_register_action">
    <?php do_action( 'register_form' ); ?>
    <div class="tk-lp-alert-cont"></div>
    <?php if($user_registration_kit_captcha == 'yes'){ ?>
    <input class="lrk-register-captcha-token simple-input" type="hidden" name="token">
    <?php } ?>
    <div class="tk-lp-success-message">
        <svg xmlns="http://www.w3.org/2000/svg" xml:space="preserve" viewBox="0 0 512 512">
            <path d="m350.2859099 280.6030326 5.831 8.124-10.4279664 7.4846716-5.831-8.124zM33.68 318.611h15.754v10H33.68zM12.377 231.519h15.755v10H12.377z"/>
            <path d="M481.264 172.216c-10.822-39.862-47.328-68.533-89.738-68.533-51.274 0-92.988 41.716-92.988 92.991 0 12.128 2.268 23.814 6.748 34.845H41.419v10h62.855v18.14H68.167v10h36.108v48.953h-43.19v10h43.19v17.318H80.191v10h24.084v73.868h321.023V283.332c16.862-6.557 31.308-17.788 41.936-32.65l-8.134-5.818c-8.717 12.189-20.311 21.634-33.802 27.628v-40.974H316.191c-5.077-10.926-7.654-22.632-7.654-34.845 0-45.761 37.229-82.991 82.988-82.991 38.169 0 70.969 26.019 80.318 62.056l-17.575-10.014-4.95 8.689 31.857 18.152 18.447-32.372-8.689-4.952-9.669 16.975zm-76.51 69.303-44.913 32.229 5.83 8.125 49.628-35.612v168.291l-104.161-81.224-6.149 7.886 100.775 78.584h-281.95l100.769-78.584-6.149-7.886-104.158 81.227V246.261l134.653 96.624c4.743 3.404 10.301 5.105 15.859 5.105 5.56 0 11.119-1.701 15.862-5.105l54.986-39.459-5.831-8.124-54.985 39.458c-6 4.305-14.062 4.307-20.062 0l-129.939-93.241h279.935z"/>
            <path d="m307.505 133.801-7.948-6.068c-15.486 20.284-23.671 44.528-23.671 70.111h10c0-23.371 7.475-45.517 21.619-64.043zM335.381 108.341l-5.322-8.466c-6.842 4.301-13.258 9.343-19.07 14.983l6.966 7.176c5.311-5.155 11.174-9.763 17.426-13.693zM391.527 92.202c19.936 0 39.348 5.58 56.141 16.135l5.322-8.466c-18.389-11.559-39.643-17.669-61.463-17.669-15.795 0-31.107 3.129-45.508 9.3l3.938 9.191c13.15-5.634 27.137-8.491 41.57-8.491zM473.717 116.493c-3.195-3.228-6.617-6.3-10.168-9.13l-6.232 7.82c3.246 2.587 6.372 5.396 9.293 8.346l7.107-7.036zM387.02 128.683v53.931c-4.388 1.529-7.861 5.003-9.391 9.391h-38.583v10h38.583c2.076 5.957 7.735 10.25 14.391 10.25 8.409 0 15.25-6.842 15.25-15.25 0-6.656-4.293-12.314-10.25-14.391v-53.931h-10zm5 73.572c-2.895 0-5.25-2.355-5.25-5.25 0-2.896 2.355-5.25 5.25-5.25 2.896 0 5.25 2.354 5.25 5.25 0 2.894-2.355 5.25-5.25 5.25zM463.418 238.168l8.655 5.008c2.356-4.073 4.416-8.356 6.122-12.732l-9.316-3.632c-1.522 3.902-3.359 7.723-5.461 11.356z"/>
        </svg>
        <p class="tk-lp-form-title"><?php esc_html_e( 'Thanks for registration!', 'user-registration-kit' ); ?></p>
        <p><?php echo sprintf( __( 'We just send you an Email. %s Please Open it up to activate your account.', 'user-registration-kit' ), '<br />' ); ?></p>
    </div>
    <input type="hidden" value="<?php echo wp_create_nonce( 'user-registration-kit' ); ?>" name="_ajax_nonce" />
    <input type="hidden" name="redirect_to" value="<?php echo esc_attr( $user_registration_kit_form_register_redirect ); ?>" />
    <input type="hidden" name="after_login" value="<?php echo esc_attr( $user_registration_kit_form_register_option ); ?>" />

    <div class="tk-lp-form-title"><?php echo esc_html__( 'Sign Up', 'user-registration-kit' ); ?></div>
    <div class="tk-lp-form-item">
        <?php if( esc_html($user_registration_kit_form_register_hide_labels) != 'yes' ){ ?>
        <label for="username<?php echo esc_attr($unique_id_registration); ?>" class="tk-lp-label"><?php echo esc_html__( 'Username', 'user-registration-kit' ); ?></label>
        <?php } ?>
        <input class="tk-lp-input tk-lp-first-input" id="username<?php echo esc_attr($unique_id_registration); ?>" name="user_login" type="text" placeholder="<?php if( esc_html($user_registration_kit_form_register_hide_labels) == 'yes' ){ echo esc_attr( 'Username', 'user-registration-kit' ); } ?>" />
    </div>
    <div class="tk-lp-form-item">
        <?php if( esc_html($user_registration_kit_form_register_hide_labels) != 'yes' ){ ?>
        <label for="email-address<?php echo esc_attr($unique_id_registration); ?>" class="tk-lp-label"><?php echo esc_html__( 'Email Address', 'user-registration-kit' ); ?></label>
        <?php } ?>
        <input class="tk-lp-input" id="email-address<?php echo esc_attr($unique_id_registration); ?>" name="user_email" type="text" placeholder="<?php if( esc_html($user_registration_kit_form_register_hide_labels) == 'yes' ){ echo esc_attr( 'Email Address', 'user-registration-kit' ); } ?>" />
    </div>
    
    <?php
    if( isset($user_registration_kit_form_register_fields) && $user_registration_kit_form_register_fields != 'basic' ){
        $bp_all_fields = LRK_Admin::get_bp_fields($user_registration_kit_form_register_fields);
        echo LRK_Admin::get_bp_fields_html($bp_all_fields, $user_registration_kit_form_register_hide_labels);
    }
    ?>

    <div class="tk-lp-form-item">
        <?php if( esc_html($user_registration_kit_form_register_hide_labels) != 'yes' ){ ?>
        <label for="password<?php echo esc_attr($unique_id_registration); ?>" class="tk-lp-label"><?php echo esc_html__( 'Password', 'user-registration-kit' ); ?></label>
        <?php } ?>
        <input class="tk-lp-input" id="password<?php echo esc_attr($unique_id_registration); ?>" name="user_password" type="password" placeholder="<?php if( esc_html($user_registration_kit_form_register_hide_labels) == 'yes' ){ echo esc_attr( 'Password', 'user-registration-kit' ); } ?>" />
    </div>
    <div class="tk-lp-form-item">
        <?php if( esc_html($user_registration_kit_form_register_hide_labels) != 'yes' ){ ?>
        <label for="confirm-password<?php echo esc_attr($unique_id_registration); ?>" class="tk-lp-label"><?php echo esc_html__( 'Confirm Password', 'user-registration-kit' ); ?></label>
        <?php } ?>
        <input class="tk-lp-input" id="confirm-password<?php echo esc_attr($unique_id_registration); ?>" name="user_password_confirm" type="password" placeholder="<?php if( esc_html($user_registration_kit_form_register_hide_labels) == 'yes' ){ echo esc_attr( 'Confirm Password', 'user-registration-kit' ); } ?>" />
    </div>
    <?php 
        $privacy_page_id = get_option('wp_page_for_privacy_policy');
        $privacy_page_title = get_the_title( $privacy_page_id );
        $privacy_url = get_permalink($privacy_page_id);
        if( $privacy_page_id != 0){
    ?>
    <div class="tk-lp-form-item">
        <div class="tk-lp-check">
            <label class="tk-lp-checkbox">
                <input type="checkbox" name="gdpr" value="1" />
                <span class="tk-lp-control-indicator"></span>
            </label>
            <div class="tk-lp-check-text">
                <?php echo esc_html__( 'I agree to ', 'user-registration-kit' ); ?>
                <a target="_blank" href="<?php echo esc_url($privacy_url); ?>"><?php echo esc_html($privacy_page_title); ?></a>
            </div>
        </div>
    </div>
    <?php } ?>

    <?php if($users_can_register){ ?>
    <button type="button" class="submit-bttn tk-lp-button tk-lp-button--dark tk-lp-w-full"><?php echo esc_html__( 'Register', 'user-registration-kit' ); ?></button>
    <?php }else{ ?>
    <div class="tk-lp-alert tk-lp-alert-error">
        <div class="tk-lp-alert-icon">
        <svg class="tk-lp-icon" width="13" height="11"><path fill-rule="evenodd" d="M12.524 8.285L8.268.913c-.696-1.218-2.454-1.218-3.146 0L.862 8.285c-.695 1.218.171 2.73 1.574 2.73h8.5c1.403 0 2.284-1.528 1.588-2.73zM6.692 9.38a.684.684 0 0 1-.678-.678c0-.37.308-.677.678-.677.37 0 .678.307.663.695.017.352-.308.66-.663.66zm.618-4.381c-.03.525-.063 1.048-.093 1.573-.015.17-.015.325-.015.492a.51.51 0 0 1-.51.493.5.5 0 0 1-.51-.478c-.045-.817-.093-1.62-.138-2.438-.015-.215-.03-.432-.047-.647 0-.355.2-.648.525-.741a.68.68 0 0 1 .788.386.814.814 0 0 1 .062.34c-.015.342-.047.682-.062 1.02z"/></svg>
        </div>
        <div class="tk-lp-alert-text"><?php echo esc_html__('To allow users to register for your website via User registration, you must first enable user registration.', 'user-registration-kit') ?></div>
    </div>
    <?php } ?>
    <?php if($both){ ?>
    <button type="button" class="tk-lp-button tk-lp-button--grey tk-lp-w-full tk-lp-tabs-form-item" data-id="sign-in<?php echo esc_attr($unique_id); ?>"><?php echo esc_html__( 'I have an account!', 'user-registration-kit' ); ?></button>
    <?php } ?>
</form>

<?php
else: 
    echo do_shortcode( wp_kses_post($user_registration_kit_form_register_shortcode) );    
endif;
?>