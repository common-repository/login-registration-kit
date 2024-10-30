jQuery( document ).ready(function($) {
    // Select
    $('.lrk-main-settings-wrap select').selectWoo({
        minimumResultsForSearch: -1
    });

    // Conditional logic
    $('.lrk-main-settings-wrap *[data-conditional]').each(function(){
        var conditional_group = $(this).data('conditional');
        if( !$(this).hasClass('conditional') ){
            var select_v = $('.lrk-main-settings-wrap .conditional[data-conditional="'+conditional_group+'"]').val();
            if( $('.lrk-main-settings-wrap input[type="checkbox"].conditional[data-conditional="'+conditional_group+'"]').length != 0 ){
                select_v = $('.lrk-main-settings-wrap .conditional[data-conditional="'+conditional_group+'"]').is(':checked');
            }

            if( $(this).closest('tr').data('cond') == select_v ){
                $(this).closest('tr').css('display', 'table-row');
            }else{
                $(this).closest('tr').css('display', 'none');
            }
        }
    });

    $('.lrk-main-settings-wrap .conditional').on('change', function(){
        var el = $(this);
        var v = el.val();
        if( typeof el.attr('type') !== 'undefined' && el.attr('type') == 'checkbox' ) {
            v = el.is(':checked');
        }
        var conditional_group = el.data('conditional');
        $('.lrk-main-settings-wrap *[data-conditional="'+conditional_group+'"]').each(function(){
            if( $(this).closest('tr').data('cond') == v ){
                $(this).closest('tr').css('display', 'table-row');
            }else{
                $(this).not(el).closest('tr').css('display', 'none');
            }
        });
    });

    // Tooltips
    function lrk_init_tooltips( $elements, options ) {
        if ( undefined !== $elements && null !== $elements && '' !== $elements ) {
            var args = {
                'attribute': 'data-tip',
                'fadeIn': 50,
                'fadeOut': 50,
                'delay': 200,
                'keepAlive': true,
            };
    
            if ( options && 'object' === typeof options ) {
                Object.keys( options ).forEach( function( key ) {
                    args[ key ] = options[ key ];
                });
            }
    
            if ( 'string' === typeof $elements ) {
                jQuery( $elements ).tipTip( args );
            } else {
                $elements.tipTip( args );
            }
        }
    }

    lrk_init_tooltips( '.user-registration-kit-help-tip' );

    // Repeater
    $('.lrk-main-settings-wrap .lrk-add-new-repeater-el').on('click', function(){
        var content = $(this).closest('.forminp-repeater').find('.rep-item').html();
        var counter = $(this).closest('.forminp-repeater').find('.rep-item').length;
        $(this).before('<span class="rep-item rep-item'+counter+'">'+content+'</span>');
        $(this).closest('.forminp-repeater').find('.rep-item'+counter).find('.repeater-field').val('');
        var l_del = $(this).closest('.forminp-repeater').find('.rep-item'+counter).find('.lrk-del-repeater-item').length;
        if( l_del == 0 ){
            $(this).closest('.forminp-repeater').find('.rep-item'+counter).prepend('<div class="lrk-del-repeater-item">Delete</div>');
        }
        return false;
    });

    $('.lrk-main-settings-wrap').on('click', '.lrk-del-repeater-item', function(){
        $(this).closest('.rep-item').remove();
    });

    $('.lrk-main-settings-wrap .colorpick').wpColorPicker();

    $('.lrk-main-settings-wrap').on('change', '.check-rep input[type="checkbox"]', function(){
        if( $(this).is(':checked') ){
            $(this).parent().find('.send-val').attr('value', 'yes');
        } else {
            $(this).parent().find('.send-val').attr('value', 'no');
        }
    });

    if( $('.reg-kit-code-editor').length ) {
        var editorSettings = wp.codeEditor.defaultSettings ? _.clone( wp.codeEditor.defaultSettings ) : {};
        editorSettings.codemirror = _.extend(
            {},
            editorSettings.codemirror,
            {
                indentUnit: 2,
                tabSize: 2,
                mode: 'css',
            }
        );
        var editor = wp.codeEditor.initialize( $('.reg-kit-code-editor'), editorSettings );
    }
});