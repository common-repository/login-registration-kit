<?php
/**
 * $user_registration_kit_account_tabs
 * $myaccount_page_id
 * $user_data
*/

global $wp_query;
$myaccount_page_url = get_permalink( $myaccount_page_id );
$display_tab = false;
if(!empty($user_registration_kit_account_tabs)){
    foreach($user_registration_kit_account_tabs as $user_registration_kit_account_tab){
        $slug = 'tklp-' . sanitize_title($user_registration_kit_account_tab['user_registration_kit_account_tabs_title']);
        if( array_key_exists( $slug, $wp_query->query_vars ) ){
            $display_tab = true;
        }
    }
}

?>
<div class="tk-lp-tabs">
    <div class="tk-lp-tabs-items">
        <div class="tk-lp-tabs-item <?php if( !$display_tab ){echo 'active';} ?>">
            <a <?php if(empty($user_data)){echo 'data-modal-trigger="modal-sign-forms"';} ?> href="<?php if(!empty($user_data)){echo esc_url($myaccount_page_url);}else{echo '#';} ?>"><?php echo esc_html__( 'Settings', 'user-registration-kit' ); ?></a>
        </div>
        <?php
        if(!empty($user_registration_kit_account_tabs)){
        foreach($user_registration_kit_account_tabs as $user_registration_kit_account_tab){
        $slug = 'tklp-' . sanitize_title($user_registration_kit_account_tab['user_registration_kit_account_tabs_title']);
        ?>
        <div class="tk-lp-tabs-item <?php if( array_key_exists( $slug, $wp_query->query_vars ) ){echo 'active';} ?>">
            <a <?php if(empty($user_data)){echo 'data-modal-trigger="modal-sign-forms"';} ?> href="<?php if(!empty($user_data)){echo esc_url($user_registration_kit_account_tab['url']);}else{echo '#';} ?>"><?php echo esc_html($user_registration_kit_account_tab['user_registration_kit_account_tabs_title']); ?></a>
        </div>
        <?php
        }
        }
        ?>
        <div class="tk-lp-tabs-item">
            <a href="<?php echo wp_logout_url(); ?>"><?php echo esc_html__( 'Logout', 'user-registration-kit' ); ?></a>
        </div>
    </div>
    <div class="tk-lp-tabs-content">
        <div class="tk-lp-tabs-content-item active">
            <?php
            $display = false;
            if(!empty($user_registration_kit_account_tabs)){
                foreach($user_registration_kit_account_tabs as $user_registration_kit_account_tab){
                        $slug = 'tklp-' . sanitize_title($user_registration_kit_account_tab['user_registration_kit_account_tabs_title']);
                        if( array_key_exists( $slug, $wp_query->query_vars ) ){
                        $display = true;
                    ?>
                    <div class="tk-lp-form-title"><?php echo esc_html($user_registration_kit_account_tab['user_registration_kit_account_tabs_title']); ?></div>
                    <?php
                        echo wp_kses_post( do_shortcode(trim( $user_registration_kit_account_tab['user_registration_kit_account_tabs_text'] )) );
                    }
                }
            } 

            if( !$display && !empty($user_data) ){
                ?>
				<div class="tk-lp-component-form">
                    <div class="tk-lp-form-title"><?php echo esc_html__( 'Settings', 'user-registration-kit' ); ?></div>
                    <div class="tk-lp-form-title-small"><?php echo esc_html__( 'Edit profile', 'user-registration-kit' ); ?></div>
                    <form class="tk-lp-form tk-lp-user-settings-form">
                        <div class="tk-lp-alert-cont"></div>
                        <div class="tk-lp-form-item tk-lp-file-input-cont">
                            <div class="tk-lp-change-avatar">
                                <div class="tk-lp-autor-avatar">
                                    <?php echo get_avatar( $user_data->ID, 'lrk_image_small', '', '', array('height' => 200, 'width' => 200, 'class' => 'tk-lp-file-input-image') ); ?>
                                </div>
                                <div class="tk-lp-buttons">
                                    <button type="button" class="tk-lp-upload-avatar tk-lp-button tk-lp-button--dark">
                                        <?php echo esc_html__( 'Update profile image', 'user-registration-kit' ); ?>
                                        <input class="tk-lp-file-input" name="image" type="file" accept="image/*" />
                                    </button>
                                    <button type="button" class="tk-lp-delete-avatar">
                                        <?php echo esc_html__( 'Delete', 'user-registration-kit' ); ?>
                                        <input type="hidden" name="delete_avatar" value="0">
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="tk-lp-form-item">
                            <label for="yourname" class="tk-lp-label"><?php echo esc_html__( 'Your Name', 'user-registration-kit' ); ?></label>
                            <input class="tk-lp-input" id="yourname" value="<?php echo esc_html($user_data->display_name); ?>" name="yourname" type="text" />
                        </div>
                        <div class="tk-lp-form-item">
                            <label for="nickname" class="tk-lp-label"><?php echo esc_html__( 'Nickname', 'user-registration-kit' ); ?></label>
                            <input class="tk-lp-input" id="nickname" value="<?php echo esc_html($user_data->nickname); ?>" name="nickname" type="text" />
                        </div>
                        <div class="tk-lp-form-item">
                            <label for="email-address" class="tk-lp-label"><?php echo esc_html__( 'Email Address', 'user-registration-kit' ); ?></label>
                            <input class="tk-lp-input" id="email-address" value="<?php echo esc_html($user_data->user_email); ?>" name="email" type="mail" />
                        </div>
                        <div class="tk-lp-form-item">
                            <h4><?php echo esc_html__( 'Password change', 'user-registration-kit' ); ?></h4>
                        </div>
                        <div class="tk-lp-form-item">
                            <label for="current-password" class="tk-lp-label"><?php echo esc_html__( 'Current Password', 'user-registration-kit' ); ?></label>
                            <input class="tk-lp-input" id="current-password" name="current_password" type="password">
                        </div>
                        <div class="tk-lp-form-item">
                            <label for="new-password" class="tk-lp-label"><?php echo esc_html__( 'New Password', 'user-registration-kit' ); ?></label>
                            <input class="tk-lp-input" id="new-password" name="new_password" type="password">
                        </div>
                        <div class="tk-lp-form-item">
                            <label for="confirm-password" class="tk-lp-label"><?php echo esc_html__( 'Confirm Password', 'user-registration-kit' ); ?></label>
                            <input class="tk-lp-input" id="confirm-password" name="confirm_password" type="password">
                        </div>
                        <button class="tk-lp-button tk-lp-button--dark tk-lp-submit-bttn"><?php echo esc_html__( 'Save Changes', 'user-registration-kit' ); ?></button>
                    </form>
				</div>
                <?php
            } else if(!$display && empty($user_data)) {
                ?>
                <a href="#" data-modal-trigger="modal-sign-forms"><?php echo esc_html__( 'Please sign in', 'user-registration-kit' ); ?></a>
                <?php
            }
            ?>
        </div>
    </div>
</div>