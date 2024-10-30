(function ($) {
    var lrkMainForm = {
        init: function () {
            this.signAjax.init();
        },

        signAjax: {
            busy: false,
            $forms: null,
    
            init: function () {
                this.$forms = jQuery('.user-register-kit-sign, .user-register-kit-register');
    
                this.addEventListeners();
                this.enableCaptcha();
            },

            enableCaptcha: function () {
                if(signFormConfigCaptcha.enable_captcha && typeof grecaptcha !== 'undefined'){
                    grecaptcha.ready(function() {
                        grecaptcha.execute(signFormConfigCaptcha.captcha_site_key).then(function(token) {
                            var olympus_captcha_token_els = document.getElementsByClassName('lrk-register-captcha-token');
                            for (var i = 0; i < olympus_captcha_token_els.length; ++i) {
                                var item = olympus_captcha_token_els[i];  
                                item.value = token;
                            }
                        });
                    });
                    grecaptcha.ready(function() {
                        grecaptcha.execute(signFormConfigCaptcha.captcha_site_key).then(function(token) {
                            var olympus_captcha_token_els = document.getElementsByClassName('lrk-sign-captcha-token');
                            for (var i = 0; i < olympus_captcha_token_els.length; ++i) {
                                var item = olympus_captcha_token_els[i];  
                                item.value = token;
                            }
                        });
                    });
                }
            },

            addEventListeners: function () {
                var _this = this;
    
                this.$forms.each(function () {
                    jQuery(this).find('.submit-bttn').on('click', function (event) {
                        event.preventDefault();
                        _this.sign(jQuery(this).closest('form'));
                        return false;
                    });
                });
    
                jQuery('input', this.$forms).on('change', function () {
                    var $self = jQuery(this);
    
                    $self.siblings('.invalid-feedback').remove();
                    $self.removeClass('is-invalid');
                    $self.closest('.has-errors').removeClass('has-errors');
                });
            },

            sign: function ($form) {
                var _this = this;
    
                var handler = $form.data('handler');
                var $messages = $form.find('.tk-lp-alert-cont');
    
                if (!handler || this.busy) {
                    return;
                }
    
                var prepared = {
                    action: handler
                };
    
                var data = $form.serializeArray();
    
                jQuery.each(data, function(i, field) {
                    if (Array.isArray(prepared[field.name])) {
                        prepared[field.name].push(field.value);
                    } else if (typeof prepared[field.name] !== 'undefined') {
                        var val = prepared[field.name];
                        prepared[field.name] = new Array();
                        prepared[field.name].push(val);
                        prepared[field.name].push(field.value);
                    } else {
                        prepared[field.name] = field.value;
                    }
                });

                jQuery.ajax({
                    url: ajax_url,
                    dataType: 'json',
                    type: 'POST',
                    data: prepared,
    
                    beforeSend: function () {
                        _this.busy = true;
                        $form.addClass('loading');
    
                        //Clear old errors
                        $messages.empty();
                        $messages.find('.tk-lp-alert').removeClass('tk-lp-alert-error');
                        $form.find('.invalid-feedback').remove();
                        $form.find('.is-invalid, .has-errors').removeClass('is-invalid has-errors');
                        _this.enableCaptcha();
                    },
                    success: function (response) {
    
                        if (response.success) {
                            //Prevent double form submit during redirect
                            _this.busy = true;
    
                            if (response.data.redirect_to) {
                                location.replace(response.data.redirect_to);
                                return false;
                            }
                            
                            if( handler == 'lrk_register_action' ){
                                $form.find('.tk-lp-success-message').css('display', 'block');

                                jQuery('html, body').animate({
                                    scrollTop: $form.offset().top - 140
                                }, 500);
                                return false;
                            } else {
                                location.reload();
                                return false;
                            }
                        }
    
                        $form.removeClass('loading');
                        if (response.data.message) {
                            var $msg = '<div class="tk-lp-alert"><div class="tk-lp-alert-icon"><svg class="tk-lp-icon" width="13" height="11"><path fill-rule="evenodd" d="M12.524 8.285L8.268.913c-.696-1.218-2.454-1.218-3.146 0L.862 8.285c-.695 1.218.171 2.73 1.574 2.73h8.5c1.403 0 2.284-1.528 1.588-2.73zM6.692 9.38a.684.684 0 0 1-.678-.678c0-.37.308-.677.678-.677.37 0 .678.307.663.695.017.352-.308.66-.663.66zm.618-4.381c-.03.525-.063 1.048-.093 1.573-.015.17-.015.325-.015.492a.51.51 0 0 1-.51.493.5.5 0 0 1-.51-.478c-.045-.817-.093-1.62-.138-2.438-.015-.215-.03-.432-.047-.647 0-.355.2-.648.525-.741a.68.68 0 0 1 .788.386.814.814 0 0 1 .062.34c-.015.342-.047.682-.062 1.02z"/></svg></div><div class="tk-lp-alert-text">' + response.data.message + '</div></div>';
                            $messages.append($msg);
                            $messages.find('.tk-lp-alert').addClass('tk-lp-alert-error');
                        }
    
                        if (response.data.errors) {
                            _this.renderFormErrors($form, response.data.errors);
                        }
                    },
                    error: function (jqXHR, textStatus) {
                        $form.removeClass('loading');
                        alert(textStatus);
                    },
                    complete: function () {
                        _this.busy = false;
                    }
                });
            },

            renderFormErrors: function ($form, errors) {
                var $messages = $form.find('.tk-lp-alert-cont');
                $messages.find('.tk-lp-alert').removeClass('tk-lp-alert-error');
                $messages.empty();
    
                for (var key in errors) {
                    var $msg = '<div class="tk-lp-alert"><div class="tk-lp-alert-icon"><svg class="tk-lp-icon" width="13" height="11"><path fill-rule="evenodd" d="M12.524 8.285L8.268.913c-.696-1.218-2.454-1.218-3.146 0L.862 8.285c-.695 1.218.171 2.73 1.574 2.73h8.5c1.403 0 2.284-1.528 1.588-2.73zM6.692 9.38a.684.684 0 0 1-.678-.678c0-.37.308-.677.678-.677.37 0 .678.307.663.695.017.352-.308.66-.663.66zm.618-4.381c-.03.525-.063 1.048-.093 1.573-.015.17-.015.325-.015.492a.51.51 0 0 1-.51.493.5.5 0 0 1-.51-.478c-.045-.817-.093-1.62-.138-2.438-.015-.215-.03-.432-.047-.647 0-.355.2-.648.525-.741a.68.68 0 0 1 .788.386.814.814 0 0 1 .062.34c-.015.342-.047.682-.062 1.02z"/></svg></div><div class="tk-lp-alert-text">' + errors[key] + '</div></div>';
                    $messages.append($msg);
                    $messages.find('.tk-lp-alert').addClass('tk-lp-alert-error');
                }
                jQuery('html, body').animate({
                    scrollTop: $form.offset().top - 140
                }, 500);
            }
        }
    }

    var lrkFileInput = {
        init: function () {
            this.fileInput.init();
        },

        fileInput: {
            $input: null,
    
            init: function () {
                this.$input = jQuery('.tk-lp-file-input');
                this.$delete = jQuery('.tk-lp-delete-avatar');
    
                this.addEventListeners();
            },

            addEventListeners: function () {
                jQuery(this.$input).on('change', function () {
                    var $self = jQuery(this);
                    var fileName = $self.prop('files')[0];
                    if( fileName ){
                        $self.closest('.tk-lp-file-input-cont').find('.tk-lp-delete-avatar input').val('0');
                        $self.closest('.tk-lp-file-input-cont').find('a').css('opacity', '.4');
                        $self.closest('.tk-lp-file-input-cont').find('.tk-lp-upload-avatar input').css('display', 'none');
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            $self.closest('.tk-lp-file-input-cont').find('.tk-lp-file-input-image').attr('src', e.target.result);
                            $self.closest('.tk-lp-file-input-cont').find('.tk-lp-file-input-image').attr('srcset', e.target.result);
                            $self.closest('.tk-lp-file-input-cont').find('a').css('opacity', '1');
                            $self.closest('.tk-lp-file-input-cont').find('.tk-lp-upload-avatar input').css('display', 'block');
                        }
                        reader.readAsDataURL($self.prop('files')[0]);
                    }

                    return false;
                });

                jQuery(this.$delete).on('click', function () {
                    var $self = jQuery(this);
                    var imageDefault = lrkPluginUrl + '/img/default-avatar.png';
                    $self.closest('.tk-lp-file-input-cont').find('.tk-lp-file-input-image').attr('src', imageDefault);
                    $self.closest('.tk-lp-file-input-cont').find('.tk-lp-file-input-image').attr('srcset', imageDefault);
                    $self.find('input').val('1');
                    $self.parent().find('.tk-lp-file-input').val('');

                    return false;
                });
            },
        }
    }

    var lrkUserSettingsFrom = {
        init: function () {
            this.settingsForm.init();
        },

        settingsForm: {
            $form: null,

            init: function () {
                this.$form = jQuery('.tk-lp-user-settings-form');
                this.addEventListeners();
            },

            addEventListeners: function () {
                var _this = this;
                
                this.$form.each(function () {
                    jQuery(this).find('.tk-lp-submit-bttn').on('click', function (event) {
                        event.preventDefault();
                        _this.sign(jQuery(this).closest('form'));
                        return false;
                    });
                });
            },

            sign: function ($form) {
                var _this = this;
                var $sbmBttn = $form.find('.tk-lp-submit-bttn');
                $sbmBttn.css('opacity', '.4');
                var file_data = ( $form.find('.tk-lp-file-input').prop('files').length != 0 ) ? $form.find('.tk-lp-file-input').prop('files')[0] : '';
                var form_data = new FormData();
                form_data.append('profileImage', file_data);

                $form.find('input[type="text"], input[type="mail"], input[type="hidden"], input[type="password"]').each(function(){
                    form_data.append(jQuery(this).attr('name'), jQuery(this).val());
                });

                form_data.append('action', 'lrk_user_settings_update');

                jQuery.ajax({
                    url: ajax_url,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    data: form_data,
                    type: "POST",
                    success: function (response) {
                        $sbmBttn.css('opacity', '1');
                        if (response.success) {
                            document.location.reload();
                        }

                        if (response.data.errors) {
                            _this.renderFormErrors($form, response.data.errors);
                        }
                    }
                });
            },

            renderFormErrors: function ($form, errors) {
                var $messages = $form.find('.tk-lp-alert-cont');
                $messages.find('.tk-lp-alert').removeClass('tk-lp-alert-error');
                $messages.empty();
    
                for (var key in errors) {
                    var $msg = '<div class="tk-lp-alert"><div class="tk-lp-alert-icon"><svg class="tk-lp-icon" width="13" height="11"><path fill-rule="evenodd" d="M12.524 8.285L8.268.913c-.696-1.218-2.454-1.218-3.146 0L.862 8.285c-.695 1.218.171 2.73 1.574 2.73h8.5c1.403 0 2.284-1.528 1.588-2.73zM6.692 9.38a.684.684 0 0 1-.678-.678c0-.37.308-.677.678-.677.37 0 .678.307.663.695.017.352-.308.66-.663.66zm.618-4.381c-.03.525-.063 1.048-.093 1.573-.015.17-.015.325-.015.492a.51.51 0 0 1-.51.493.5.5 0 0 1-.51-.478c-.045-.817-.093-1.62-.138-2.438-.015-.215-.03-.432-.047-.647 0-.355.2-.648.525-.741a.68.68 0 0 1 .788.386.814.814 0 0 1 .062.34c-.015.342-.047.682-.062 1.02z"/></svg></div><div class="tk-lp-alert-text">' + errors[key] + '</div></div>';
                    $messages.append($msg);
                    $messages.find('.tk-lp-alert').addClass('tk-lp-alert-error');
                }
            }
        }
    }

    var lrkLostPasswordFrom = {
        init: function () {
            this.lostPasswordForm.init();
        },

        lostPasswordForm: {
            $form: null,

            init: function () {
                this.$form = jQuery('.user-register-kit-lost-password');
                this.addEventListeners();
            },

            addEventListeners: function () {
                var _this = this;
                this.$form.each(function () {
                    jQuery(this).find('.tk-lp-submit-bttn').on('click', function (event) {
                        event.preventDefault();
                        _this.sign(jQuery(this).closest('form'));
                        return false;
                    });
                });
            },

            sign: function ($form) {
                var _this = this;

                jQuery.ajax({
                    url: ajax_url,
                    dataType: 'json',
                    type: 'POST',
                    data: {
                        action: 'lrk_lost_password',
                        user_login: $form.find('input[name="email"]').val()
                    },
                    success: function (response) {
                        if (response.success) {
                            var $messages = $form.find('.tk-lp-alert-cont');
                            $messages.empty();
                            $messages.append('<div class="tk-lp-alert tk-lp-alert-success"><div class="tk-lp-alert-icon"><svg class="tk-lp-icon" width="12" height="9"><path fill-rule="evenodd" d="M4.595 8.826a.62.62 0 0 1-.433.173.62.62 0 0 1-.434-.173L.269 5.484a.866.866 0 0 1 0-1.255l.433-.418a.942.942 0 0 1 1.3 0l2.16 2.086L9.997.26a.942.942 0 0 1 1.3 0l.433.418a.867.867 0 0 1 0 1.256L4.595 8.826z"/></svg></div><div class="tk-lp-alert-text">'+response.data.message+'</div></div>');
                        }
    
                        if (response.data.errors) {
                            _this.renderFormErrors($form, response.data.errors);
                        }
                    },
                    error: function (jqXHR, textStatus) {
                        alert(textStatus);
                    }
                });
            },

            renderFormErrors: function ($form, errors) {
                var $messages = $form.find('.tk-lp-alert-cont');
                $messages.find('.tk-lp-alert').removeClass('tk-lp-alert-error');
                $messages.empty();
    
                for (var key in errors) {
                    var $msg = '<div class="tk-lp-alert"><div class="tk-lp-alert-icon"><svg class="tk-lp-icon" width="13" height="11"><path fill-rule="evenodd" d="M12.524 8.285L8.268.913c-.696-1.218-2.454-1.218-3.146 0L.862 8.285c-.695 1.218.171 2.73 1.574 2.73h8.5c1.403 0 2.284-1.528 1.588-2.73zM6.692 9.38a.684.684 0 0 1-.678-.678c0-.37.308-.677.678-.677.37 0 .678.307.663.695.017.352-.308.66-.663.66zm.618-4.381c-.03.525-.063 1.048-.093 1.573-.015.17-.015.325-.015.492a.51.51 0 0 1-.51.493.5.5 0 0 1-.51-.478c-.045-.817-.093-1.62-.138-2.438-.015-.215-.03-.432-.047-.647 0-.355.2-.648.525-.741a.68.68 0 0 1 .788.386.814.814 0 0 1 .062.34c-.015.342-.047.682-.062 1.02z"/></svg></div><div class="tk-lp-alert-text">' + errors[key] + '</div></div>';
                    $messages.append($msg);
                    $messages.find('.tk-lp-alert').addClass('tk-lp-alert-error');
                }
            }
        }
    }

    lrkLostPasswordFrom.init();
    lrkMainForm.init();
    lrkFileInput.init();
    lrkUserSettingsFrom.init();

})(jQuery);