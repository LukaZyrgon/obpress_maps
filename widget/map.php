<?php

class OBMap extends \Elementor\Widget_Base {
	
	public function __construct($data = [], $args = null) {
		parent::__construct($data, $args);

		wp_register_script('googlemaps', 'https://maps.googleapis.com/maps/api/js?&key=', [ 'elementor-frontend' ], '1.0.0', true );
		wp_register_script( 'map_js',  plugins_url( '/OBPress_Maps/widget/assets/js/map.js'), [ 'elementor-frontend' ], '1.0.0', true );

		wp_register_style( 'map_css', plugins_url( '/OBPress_Maps/widget/assets/css/map.css') );        
	}

	public function get_script_depends()
	{
		return ['googlemaps', 'map_js'];
	}

	public function get_style_depends()
	{
		return ['map_css'];
	}

	public function get_name() {
		return 'OBMap';
	}

	public function get_title() {
		return __( 'OB Map', 'OBPress_Maps' );
	}

	public function get_icon() {
		return 'fa fa-calendar';
	}

	public function get_categories() {
		return [ 'OBPress' ];
	}

	protected function _register_controls() {

		$this->start_controls_section(
			'content_section',
			[
				'label' => __( 'Content', 'OBPress_Maps' ),
				'tab' => \Elementor\Controls_Manager::TAB_CONTENT,
			]
		);

		$this->add_responsive_control(
			'obpress_map_image_height',
			[
				'label' => __( 'Height', 'OBPress_Maps' ),
				'type' => \Elementor\Controls_Manager::SLIDER,
				'size_units' => [ 'px' ],
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 2000,
						'step' => 5,
					]
				],
				'devices' => [ 'desktop', 'mobile' ],
				'desktop_default' => [
					'size' => 600,
					'unit' => 'px',
				],
				'mobile_default' => [
					'size' => 234,
					'unit' => 'px',
				],
				'selectors' => [
					'.obpress-map-holder .obpress-map #map' => 'height: {{SIZE}}{{UNIT}};',
				],
			]
		);
		

		$this->end_controls_section();

		$this->start_controls_section(
			'map_style_section',
			[
				'label' => __( 'Map', 'OBPress_Maps' ),
				'tab' => \Elementor\Controls_Manager::TAB_STYLE,
			]
		);

		$this->add_group_control(
			\Elementor\Group_Control_Css_Filter::get_type(),
			[
				'name' => 'thumbnail', // // Usage: `{name}_size` and `{name}_custom_dimension`, in this case `thumbnail_size` and `thumbnail_custom_dimension`.
				'selectors' => [
					'.obpress-map #map',
				],
			]
		);		

		$this->end_controls_section();
	}
    
	protected function render() {
		$settings_rooms = $this->get_settings_for_display();

		ini_set('xdebug.var_display_max_depth', 10);
		ini_set('xdebug.var_display_max_children', 256);
		ini_set('xdebug.var_display_max_data', 1024);        

		require_once(WP_CONTENT_DIR . '/plugins/obpress_plugin_manager/BeApi/BeApi.php');

        $chain = get_option('chain_id');
        $language = get_option('default_language_id');        

        // $hotels = BeApi::getHotelSearchForChain($chain, 'true', $language);
        $hotels = BeApi::ApiCache('hotel_search_chain_'.$chain.'_'.$language.'_true', BeApi::$cache_time['hotel_search_chain'], function() use ($chain, $language){
            return BeApi::getHotelSearchForChain($chain, "true",$language);
        });


        $hotelsInfo = [];

        foreach($hotels->PropertiesType->Properties as $hotel) {
            $hotelInfo = array(
                'hotelName' => $hotel->HotelRef->HotelName,
                'hotelLat' => $hotel->Position->Latitude,
                'hotelLong' => $hotel->Position->Longitude
            );
            array_push($hotelsInfo, $hotelInfo);
        }


        $hotelsInfo = json_encode($hotelsInfo);

        require_once(WP_PLUGIN_DIR . '/OBPress_Maps/widget/assets/templates/template.php');
	}
}

