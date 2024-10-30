"use strict";

(function (wpI18n, wpBlocks, wpElement, wpBlockEditor, wpComponents, serverSideRender) {
  const {
    registerBlockType
  } = wp.blocks;
  const {
    InspectorControls,
    useBlockProps
  } = wp.blockEditor;
  const ServerSideRender = serverSideRender;
  const {
    PanelBody,
    PanelRow,
    CheckboxControl,
    TextControl,
    TextareaControl,
    SelectControl,
    RangeControl
  } = wp.components;
  const {
    __
  } = wpI18n;
  const data_global_settings = JSON.parse(lrk_global_settings);
  registerBlockType('lrk/registerform', {
    title: 'Login / Register form',
    apiVersion: 2,
    icon: 'admin-users',
    category: 'lrk_blocks',
    keywords: ['register', 'login', 'form'],
    attributes: {
      userLoginOption: {
        type: 'string',
        default: data_global_settings.user_registration_kit_form_register_option
      },
      registerRedirectURL: {
        type: 'string',
        default: data_global_settings.user_registration_kit_form_register_redirect != '' ? data_global_settings.user_registration_kit_form_register_redirect : ''
      },
      registerhideFieldLabels: {
        type: 'boolean',
        default: data_global_settings.user_registration_kit_form_register_hide_labels == 'yes' ? true : false
      },
      blockWidth: {
        type: 'number',
        default: 100
      }
    },
    edit: props => {
      const {
        attributes,
        setAttributes
      } = props;
      const blockProps = useBlockProps();
      return /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement(InspectorControls, null, /*#__PURE__*/React.createElement(PanelBody, {
        title: __('Register form', 'user-registration-kit'),
        initialOpen: true
      }, /*#__PURE__*/React.createElement(PanelRow, null, /*#__PURE__*/React.createElement(RangeControl, {
        label: __('Form width (%)', 'user-registration-kit'),
        value: attributes.blockWidth,
        onChange: val => setAttributes({
          blockWidth: val
        }),
        min: 10,
        max: 100
      })), /*#__PURE__*/React.createElement(PanelRow, null, /*#__PURE__*/React.createElement(SelectControl, {
        label: __('User login option', 'user-registration-kit'),
        value: attributes.userLoginOption,
        options: [{
          label: __('Manual login after registration', 'user-registration-kit'),
          value: 'manual'
        }, {
          label: __('Auto login after registration', 'user-registration-kit'),
          value: 'auto_login'
        }],
        onChange: newval => setAttributes({
          userLoginOption: newval
        })
      })), /*#__PURE__*/React.createElement(PanelRow, null, /*#__PURE__*/React.createElement(TextControl, {
        label: __('Redirect URL', 'user-registration-kit'),
        value: attributes.registerRedirectURL,
        onChange: newval => setAttributes({
          registerRedirectURL: newval
        })
      })), /*#__PURE__*/React.createElement(PanelRow, null, /*#__PURE__*/React.createElement(CheckboxControl, {
        label: __('Hide field labels', 'user-registration-kit'),
        checked: attributes.registerhideFieldLabels,
        onChange: newval => setAttributes({
          registerhideFieldLabels: newval
        })
      })))), /*#__PURE__*/React.createElement("div", blockProps, /*#__PURE__*/React.createElement(ServerSideRender, {
        block: "lrk/registerform",
        attributes: attributes
      })));
    },
    save: props => {
      return null;
    }
  });
  registerBlockType('lrk/signinform', {
    title: 'LRK Login form',
    category: 'lrk_blocks',
    icon: 'businessman',
    keywords: ['sign', 'form'],
    attributes: {
      redirectURL: {
        type: 'string',
        default: data_global_settings.user_registration_kit_form_login_redirect != '' ? data_global_settings.user_registration_kit_form_login_redirect : ''
      },
      hideFieldLabels: {
        type: 'boolean',
        default: data_global_settings.user_registration_kit_form_login_hide_labels == 'yes' ? true : false
      },
      enableRememberMe: {
        type: 'boolean',
        default: data_global_settings.user_registration_kit_form_login_remember_me == 'yes' ? true : false
      },
      enableLostPassword: {
        type: 'boolean',
        default: data_global_settings.user_registration_kit_form_login_lost_password == 'yes' ? true : false
      },
      blockWidth: {
        type: 'number',
        default: 100
      }
    },
    edit: props => {
      const {
        attributes,
        setAttributes
      } = props;
      const blockProps = useBlockProps();
      return /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement(InspectorControls, null, /*#__PURE__*/React.createElement(PanelBody, {
        title: __('Sign in form', 'user-registration-kit'),
        initialOpen: true
      }, /*#__PURE__*/React.createElement(PanelRow, null, /*#__PURE__*/React.createElement(RangeControl, {
        label: __('Form width (%)', 'user-registration-kit'),
        value: attributes.blockWidth,
        onChange: val => setAttributes({
          blockWidth: val
        }),
        min: 10,
        max: 100
      })), /*#__PURE__*/React.createElement(PanelRow, null, /*#__PURE__*/React.createElement(TextControl, {
        label: __('Redirect URL', 'user-registration-kit'),
        value: attributes.redirectURL,
        onChange: newval => setAttributes({
          redirectURL: newval
        })
      })), /*#__PURE__*/React.createElement(PanelRow, null, /*#__PURE__*/React.createElement(CheckboxControl, {
        label: __('Hide field labels', 'user-registration-kit'),
        checked: attributes.hideFieldLabels,
        onChange: newval => setAttributes({
          hideFieldLabels: newval
        })
      })), /*#__PURE__*/React.createElement(PanelRow, null, /*#__PURE__*/React.createElement(CheckboxControl, {
        label: __('Enable remember me', 'user-registration-kit'),
        checked: attributes.enableRememberMe,
        onChange: newval => setAttributes({
          enableRememberMe: newval
        })
      })), /*#__PURE__*/React.createElement(PanelRow, null, /*#__PURE__*/React.createElement(CheckboxControl, {
        label: __('Enable lost password', 'user-registration-kit'),
        checked: attributes.enableLostPassword,
        onChange: newval => setAttributes({
          enableLostPassword: newval
        })
      })))), /*#__PURE__*/React.createElement("div", blockProps, /*#__PURE__*/React.createElement(ServerSideRender, {
        block: "lrk/signinform",
        attributes: attributes
      })));
    },
    save: props => {
      return null;
    }
  }); // Both

  registerBlockType('lrk/bothform', {
    title: 'LRK Forms',
    category: 'lrk_blocks',
    icon: 'groups',
    keywords: ['sign', 'register', 'form'],
    attributes: {
      userLoginOption: {
        type: 'string',
        default: data_global_settings.user_registration_kit_form_register_option
      },
      registerRedirectURL: {
        type: 'string',
        default: data_global_settings.user_registration_kit_form_register_redirect != '' ? data_global_settings.user_registration_kit_form_register_redirect : ''
      },
      registerhideFieldLabels: {
        type: 'boolean',
        default: data_global_settings.user_registration_kit_form_register_hide_labels == 'yes' ? true : false
      },
      redirectURL: {
        type: 'string',
        default: data_global_settings.user_registration_kit_form_login_redirect != '' ? data_global_settings.user_registration_kit_form_login_redirect : ''
      },
      hideFieldLabels: {
        type: 'boolean',
        default: data_global_settings.user_registration_kit_form_login_hide_labels == 'yes' ? true : false
      },
      enableRememberMe: {
        type: 'boolean',
        default: data_global_settings.user_registration_kit_form_login_remember_me == 'yes' ? true : false
      },
      enableLostPassword: {
        type: 'boolean',
        default: data_global_settings.user_registration_kit_form_login_lost_password == 'yes' ? true : false
      },
      blockWidth: {
        type: 'number',
        default: 100
      }
    },
    edit: props => {
      const {
        attributes,
        setAttributes
      } = props;
      const blockProps = useBlockProps();
      return /*#__PURE__*/React.createElement("div", null, /*#__PURE__*/React.createElement(InspectorControls, null, /*#__PURE__*/React.createElement(PanelBody, {
        title: __('Sign in form', 'user-registration-kit'),
        initialOpen: true
      }, /*#__PURE__*/React.createElement(PanelRow, null, /*#__PURE__*/React.createElement(RangeControl, {
        label: __('Form width (%)', 'user-registration-kit'),
        value: attributes.blockWidth,
        onChange: val => setAttributes({
          blockWidth: val
        }),
        min: 10,
        max: 100
      })), /*#__PURE__*/React.createElement(PanelRow, null, /*#__PURE__*/React.createElement(TextControl, {
        label: __('Redirect URL', 'user-registration-kit'),
        value: attributes.redirectURL,
        onChange: newval => setAttributes({
          redirectURL: newval
        })
      })), /*#__PURE__*/React.createElement(PanelRow, null, /*#__PURE__*/React.createElement(CheckboxControl, {
        label: __('Hide field labels', 'user-registration-kit'),
        checked: attributes.hideFieldLabels,
        onChange: newval => setAttributes({
          hideFieldLabels: newval
        })
      })), /*#__PURE__*/React.createElement(PanelRow, null, /*#__PURE__*/React.createElement(CheckboxControl, {
        label: __('Enable remember me', 'user-registration-kit'),
        checked: attributes.enableRememberMe,
        onChange: newval => setAttributes({
          enableRememberMe: newval
        })
      })), /*#__PURE__*/React.createElement(PanelRow, null, /*#__PURE__*/React.createElement(CheckboxControl, {
        label: __('Enable lost password', 'user-registration-kit'),
        checked: attributes.enableLostPassword,
        onChange: newval => setAttributes({
          enableLostPassword: newval
        })
      }))), /*#__PURE__*/React.createElement(PanelBody, {
        title: __('Register form', 'user-registration-kit'),
        initialOpen: true
      }, /*#__PURE__*/React.createElement(PanelRow, null, /*#__PURE__*/React.createElement(SelectControl, {
        label: __('User login option', 'user-registration-kit'),
        value: attributes.userLoginOption,
        options: [{
          label: __('Manual login after registration', 'user-registration-kit'),
          value: 'manual'
        }, {
          label: __('Auto login after registration', 'user-registration-kit'),
          value: 'auto_login'
        }],
        onChange: newval => setAttributes({
          userLoginOption: newval
        })
      })), /*#__PURE__*/React.createElement(PanelRow, null, /*#__PURE__*/React.createElement(TextControl, {
        label: __('Redirect URL', 'user-registration-kit'),
        value: attributes.registerRedirectURL,
        onChange: newval => setAttributes({
          registerRedirectURL: newval
        })
      })), /*#__PURE__*/React.createElement(PanelRow, null, /*#__PURE__*/React.createElement(CheckboxControl, {
        label: __('Hide field labels', 'user-registration-kit'),
        checked: attributes.registerhideFieldLabels,
        onChange: newval => setAttributes({
          registerhideFieldLabels: newval
        })
      })))), /*#__PURE__*/React.createElement("div", blockProps, /*#__PURE__*/React.createElement(ServerSideRender, {
        block: "lrk/bothform",
        attributes: attributes
      })));
    },
    save: props => {
      return null;
    }
  });
})(wp.i18n, wp.blocks, wp.element, wp.blockEditor, wp.components, wp.serverSideRender);