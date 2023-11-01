<?php

namespace AwesomeCoder\Elements;

/**
 * The Main Widget Class
 *
 * @link              https://awesomecoder.dev
 * @since             1.0.0
 * @package           Elementor Elements Addons
 *																__
 *	                                                           |  |
 *	  __ ___      _____  ___  ___  _ __ ___   ___  ___ ___   __|  | ___ _ ____
 *	 / _` \ \ /\ / / _ \/ __|/ _ \| '_ ` _ \ / _ \/ __/ _ \ / _`  |/ _ \ ' __|
 *	| (_| |\ V  V /  __/\__ \ (_) | | | | | |  __/ (_| (_) | (_|  |  __/  |
 *	\__,_| \_/\_/ \___||___/\___/|_| |_| |_|\___|\___\___/ \__,___|\___|__|
 *
 */

class Elementor_Elements_Widget extends \Elementor\Widget_Base
{

	/**
	 * Get widget name.
	 *
	 * Retrieve oEmbed widget name.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget name.
	 */
	public function get_name()
	{
		return 'eea-embed';
	}

	/**
	 * Get widget title.
	 *
	 * Retrieve oEmbed widget title.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget title.
	 */
	public function get_title()
	{
		return esc_html__('EEA Widget', 'elementor-oembed-widget');
	}

	/**
	 * Get widget icon.
	 *
	 * Retrieve oEmbed widget icon.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget icon.
	 */
	public function get_icon()
	{
		return 'eea-icon bx bx-link';
	}

	/**
	 * Get custom help URL.
	 *
	 * Retrieve a URL where the user can get more information about the widget.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return string Widget help URL.
	 */
	public function get_custom_help_url()
	{
		return 'https://awesomecoder.dev/';
	}

	/**
	 * Get widget categories.
	 *
	 * Retrieve the list of categories the oEmbed widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget categories.
	 */
	public function get_categories()
	{
		return ['elements-addons-elementor'];
	}

	/**
	 * Get widget keywords.
	 *
	 * Retrieve the list of keywords the oEmbed widget belongs to.
	 *
	 * @since 1.0.0
	 * @access public
	 * @return array Widget keywords.
	 */
	public function get_keywords()
	{
		return ['eea', 'oembed', 'url', 'link'];
	}

	/**
	 * Register oEmbed widget controls.
	 *
	 * Add input fields to allow the user to customize the widget settings.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function register_controls()
	{

		$this->start_controls_section(
			'content_section',
			[
				'label' => esc_html__('Content', EEA_PLUGIN_TEXTDOMAIN),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_control(
			'url',
			[
				'label' => esc_html__('URL to embed', EEA_PLUGIN_TEXTDOMAIN),
				'type' => \Elementor\Controls_Manager::TEXT,
				'input_type' => 'url',
				'placeholder' => esc_html__('https://your-link.com', EEA_PLUGIN_TEXTDOMAIN),
			]
		);


		$this->add_control(
			'title',
			[
				'type' => \Elementor\Controls_Manager::TEXT,
				'label' => esc_html__('Title', EEA_PLUGIN_TEXTDOMAIN),
				'placeholder' => esc_html__('Enter your title', EEA_PLUGIN_TEXTDOMAIN),
			]
		);

		$this->add_control(
			'size',
			[
				'type' => \Elementor\Controls_Manager::NUMBER,
				'label' => esc_html__('Size', EEA_PLUGIN_TEXTDOMAIN),
				'placeholder' => '0',
				'min' => 0,
				'max' => 100,
				'step' => 1,
				'default' => 50,
			]
		);

		$this->add_control(
			'open_lightbox',
			[
				'type' => \Elementor\Controls_Manager::SELECT,
				'label' => esc_html__('Lightbox', EEA_PLUGIN_TEXTDOMAIN),
				'options' => [
					'default' => esc_html__('Default', EEA_PLUGIN_TEXTDOMAIN),
					'yes' => esc_html__('Yes', EEA_PLUGIN_TEXTDOMAIN),
					'no' => esc_html__('No', EEA_PLUGIN_TEXTDOMAIN),
				],
				'default' => 'no',
			]
		);

		$this->add_control(
			'alignment',
			[
				'type' => \Elementor\Controls_Manager::CHOOSE,
				'label' => esc_html__('Alignment', EEA_PLUGIN_TEXTDOMAIN),
				'options' => [
					'left' => [
						'title' => esc_html__('Left', EEA_PLUGIN_TEXTDOMAIN),
						'icon' => 'eicon-text-align-left',
					],
					'center' => [
						'title' => esc_html__('Center', EEA_PLUGIN_TEXTDOMAIN),
						'icon' => 'eicon-text-align-center',
					],
					'right' => [
						'title' => esc_html__('Right', EEA_PLUGIN_TEXTDOMAIN),
						'icon' => 'eicon-text-align-right',
					],
				],
				'default' => 'center',
			]
		);

		$this->end_controls_section();

		$this->start_controls_section(
			'section_style',
			[
				'label' => esc_html__('Style', 'textdomain'),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_control(
			'color',
			[
				'label' => esc_html__('Color', 'textdomain'),
				'type' => \Elementor\Controls_Manager::COLOR,
				'default' => '#f00',
				'selectors' => [
					'{{WRAPPER}} h3' => 'color: {{VALUE}}',
				],
			]
		);

		$this->add_control(
			'image',
			[
				'label' => __('<i class="eea bx bx-link" ></i> Choose Image', 'textdomain'),
				'type' => \Elementor\Controls_Manager::MEDIA,
				'default' => [
					'url' => \Elementor\Utils::get_placeholder_image_src(),
				],
			]
		);
		$this->end_controls_section();
	}

	/**
	 * Render oEmbed widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function render()
	{
		$settings = $this->get_settings_for_display();

		echo '<div class="oembed-elementor-widget">';
		echo '<h3>' . $settings['title'] . '</h3>';

		// Get image url
		echo '<img src="' . esc_url($settings['image']['url']) . '" alt="">';

		// Get image by id
		echo wp_get_attachment_image($settings['image']['id'], 'thumbnail');
		echo json_encode($settings, JSON_PRETTY_PRINT);
		echo '</div>';
	}
	/**
	 * Render oEmbed widget output on the frontend.
	 *
	 * Written in PHP and used to generate the final HTML.
	 *
	 * @since 1.0.0
	 * @access protected
	 */
	protected function content_template()
	{
?>
		<h3>{{{ settings.title }}}</h3>
		<img src="{{{ settings.image.url }}}">
<?php
	}
}
