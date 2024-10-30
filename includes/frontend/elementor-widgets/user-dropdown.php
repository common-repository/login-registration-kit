<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

class Elementor_Lrk_User_Dropdown extends \Elementor\Widget_Base {

    public function get_name() {
		return 'lrk_user_dropdown';
	}

	public function get_title() {
		return esc_html__( 'User dropdown menu', 'user-registration-kit' );
	}

    public function get_categories() {
		return [ 'elementor-lrk' ];
	}

    protected function _register_controls() {
        $this->start_controls_section(
			'lrk_user_dropdown',
			[
				'label' => __( 'User dropdown menu', 'user-registration-kit' ),
			]
		);

        $this->add_control(
            'avatar_text',
            [
                'type' => \Elementor\Controls_Manager::TEXT,
                'label' => esc_html__( 'Text with avatar', 'user-registration-kit' ),
                'description' => esc_html__( 'This option lets you enter the text with avatar.', 'user-registration-kit' ),
            ]
        );

        $this->add_control(
            'no_login_text',
            [
                'type' => \Elementor\Controls_Manager::TEXTAREA,
                'label' => esc_html__( 'No user login text (HTML)', 'user-registration-kit' ),
                'separator'    => 'before',
            ]
        );

		$this->end_controls_section();

        $this->start_controls_section(
            'lrk_user_dropdown_css',
            [
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
                'label' => esc_html__( 'Avatar', 'user-registration-kit' ),
            ]
        );

        $this->add_responsive_control(
			'lrk_user_avatar_size',
			[
				'label'          => __( 'Avatar size (width px)', 'user-registration-kit' ),
				'type'           => \Elementor\Controls_Manager::SLIDER,
                'default'        => [
                    'size' => 40
                ],
				'range'          => [
					'px' => [
						'min' => 1,
						'max' => 100,
					],
				],
			]
		);

        $this->add_group_control(
            'border',
            [
                'name'      => 'lrk_user_avatar_border',
                'label'     => esc_html__( 'Border', 'user-registration-kit' ),
                'selector'  => '{{WRAPPER}} #tk-lp-user .tk-lp-user-avatar img',
                'separator' => 'before'
            ]
        );

        $this->add_control(
            'lrk_user_avatar_border_radius',
            [
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'label'      => __( 'Border Radius', 'user-registration-kit' ),
                'size_units' => [ 'px', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} #tk-lp-user .tk-lp-user-avatar img' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before'
            ]
        );

		$this->end_controls_section();

        $this->start_controls_section(
            'lrk_user_dropdown_color_css',
            [
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
                'label' => esc_html__( 'Color settings', 'user-registration-kit' ),
            ]
        );

        $this->add_control(
			'lrk_user_dropdown_color_text_color',
			[
				'label'     => __( 'Text color', 'user-registration-kit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .tk-lp-text-with-avatar' => 'color: {{VALUE}};',
				],
                'separator'    => 'before',
			]
		);

        $this->add_control(
			'lrk_user_dropdown_color_icon_color',
			[
				'label'     => __( 'Icon color', 'user-registration-kit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} .additional-menu-item .tk-lp-icon' => 'fill: {{VALUE}};',
				],
                'separator'    => 'before',
			]
		);

		$this->end_controls_section();

        $this->start_controls_section(
            'lrk_user_dropdown_nav_css',
            [
                'tab'   => \Elementor\Controls_Manager::TAB_STYLE,
                'label' => esc_html__( 'Dropdown navigation', 'user-registration-kit' ),
            ]
        );

        $this->add_control(
			'lrk_user_dropdown_nav_bg_color',
			[
				'label'     => __( 'Background', 'user-registration-kit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #tk-lp-user .tk-lp-user-menu-dropdown' => 'background-color: {{VALUE}};',
				],
			]
		);

        $this->add_control(
			'lrk_user_dropdown_nav_text_color',
			[
				'label'     => __( 'Text color', 'user-registration-kit' ),
				'type'      => \Elementor\Controls_Manager::COLOR,
				'selectors' => [
					'{{WRAPPER}} #tk-lp-user .tk-lp-user-menu-dropdown .tk-lp-user-menu-dropdown-items a' => 'color: {{VALUE}};',
				],
                'separator'    => 'before',
			]
		);

        $this->add_control(
            'lrk_user_dropdown_nav_border_radius',
            [
                'type'       => \Elementor\Controls_Manager::DIMENSIONS,
                'label'      => __( 'Border Radius', 'user-registration-kit' ),
                'size_units' => [ 'px', '%' ],
                'selectors'  => [
                    '{{WRAPPER}} #tk-lp-user .tk-lp-user-menu-dropdown' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before'
            ]
        );

		$this->end_controls_section();
    }

    protected function render() {
		$settings = $this->get_settings_for_display();

        $menu_avatar_size = (!empty($settings['lrk_user_avatar_size']['size'])) ? intval($settings['lrk_user_avatar_size']['size']) : 40;
        $avatar_text = (!empty($settings['avatar_text'])) ? $settings['avatar_text'] : '';
        $no_login_text = (!empty($settings['no_login_text'])) ? $settings['no_login_text'] : '';

        echo do_shortcode('[user_registration_kit_dropdown menu_avatar_size="'.esc_attr($menu_avatar_size).'" menu_avatar_text="'.esc_attr($avatar_text).'" no_login_text="'.$no_login_text.'"]');
    }
}