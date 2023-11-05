<?php
namespace AwesomeCoder\Elements;

// If this file is called directly, abort.
if (!defined('ABSPATH')) {
    exit;
}

use \Elementor\Controls_Manager;
use \Elementor\Widget_Base;

class Career_Page extends Widget_Base {

	public function get_name() {
		return 'eea-career-page';
	}

	public function get_title() {
		return esc_html__( 'Career Page', EEA_PLUGIN_TEXTDOMAIN);
	}

	public function get_icon() {
		return 'eea-icon bx bxs-credit-card-front';
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
		return [
        'eea',
        'addons',
        'ea',
        'career',
        'job',
        'career page',
        'eea addons',];

	}


	protected function register_controls() {
        $this->start_controls_section(
            'eea_global_warning',
            [
                'label' => __('Warning!', EEA_PLUGIN_TEXTDOMAIN),
            ]
        );

        $this->add_control(
            'eea_global_warning_text',
            [
                'type'            => Controls_Manager::RAW_HTML,
                'raw'             => __('<strong>EasyJobs</strong> is not installed/activated on your site. Please install and activate <a href="plugin-install.php?s=easyjobs&tab=search&type=term" target="_blank">EasyJobs</a> first.',
                    EEA_PLUGIN_TEXTDOMAIN),
                'content_classes' => 'eea-warning',
            ]
        );

        $this->end_controls_section();
	}


	protected function render() {
	    echo "Hello";
	}
}
