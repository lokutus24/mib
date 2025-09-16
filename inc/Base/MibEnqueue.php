<?php

/**
 * Enqueue
 */

namespace Inc\Base;

use Inc\Base\MibBaseController;
use Inc\Base\MibAuthController;

class MibEnqueue extends MibBaseController
{
	//short code beállítások adott short code-hoz
	public $filterType = [];

	public function __construct()
	{
		parent::__construct();
                if (isset($_POST['shortcode'])) {
                        $this->filterType = $this->get_shortcode_config_by_name($_POST['shortcode']);
                        $this->selectedShortcodeOption = $this->filterType;
                }
        }

	public function registerFunction(){

		add_action('admin_enqueue_scripts', array($this, 'customFiles'));

		//ADMIN
		add_action("wp_ajax_setMib", array($this, "setMib" ) );
		add_action("wp_ajax_nopriv_setMib", array($this, "setMib") );

		//FRONTEND
		add_action('wp_enqueue_scripts', array($this, "enqueue_custom_script") );

		add_action('wp_ajax_load_paginated_data', array($this, "load_paginated_data") );
		add_action('wp_ajax_nopriv_load_paginated_data', array($this, "load_paginated_data")); // Allow for logged-out users

		add_action('wp_ajax_search_data', array($this, "search_data"));
		add_action('wp_ajax_nopriv_search_data', array($this, "search_data")); // Allow for logged-out users

		add_action('wp_ajax_filter_data_by_floor', array($this, "filter_data_by_floor") );
		add_action('wp_ajax_nopriv_filter_data_by_floor', array($this, "filter_data_by_floor"));

		add_action('wp_ajax_filter_data_by_room', array($this, "filter_data_by_room") );
		add_action('wp_ajax_nopriv_filter_data_by_room', array($this, "filter_data_by_room"));

        add_action('wp_ajax_filter_data_by_orientation', array($this, "filter_data_by_orientation") );
        add_action('wp_ajax_nopriv_filter_data_by_orientation', array($this, "filter_data_by_orientation"));

        add_action('wp_ajax_filter_data_by_availability', array($this, "filter_data_by_availability") );
        add_action('wp_ajax_nopriv_filter_data_by_availability', array($this, "filter_data_by_availability"));

        add_action('wp_ajax_filter_data_by_district', array($this, "filter_data_by_district") );
        add_action('wp_ajax_nopriv_filter_data_by_district', array($this, "filter_data_by_district"));

		add_action('wp_ajax_deletefilters', array($this, "delete_filters") );
		add_action('wp_ajax_nopriv_deletefilters', array($this, "delete_filters"));

		add_action('wp_ajax_delete_catalog_filters', array($this, "delete_catalog_filters") );
		add_action('wp_ajax_nopriv_delete_catalog_filters', array($this, "delete_catalog_filters"));


		add_action('wp_ajax_get_slider_range_data', array($this, "get_slider_range_data") );
		add_action('wp_ajax_nopriv_get_slider_range_data', array($this, "get_slider_range_data"));

		add_action('wp_ajax_get_price_slider_range_data', array($this, "get_price_slider_range_data") );
		add_action('wp_ajax_nopriv_get_price_slider_range_data', array($this, "get_price_slider_range_data"));

		add_action('wp_ajax_get_square_slider_range_data', array($this, "get_square_slider_range_data") );
		add_action('wp_ajax_nopriv_get_square_slider_range_data', array($this, "get_square_slider_range_data"));


		add_action('wp_ajax_set_slider_values', array($this, "set_slider_values") );
		add_action('wp_ajax_nopriv_set_slider_values', array($this, "set_slider_values"));

		add_action('wp_ajax_set_price_slider_values', array($this, "set_price_slider_values") );
		add_action('wp_ajax_nopriv_set_price_slider_values', array($this, "set_price_slider_values"));

		add_action('wp_ajax_load_more_items', array($this, "load_more_items") );
		add_action('wp_ajax_nopriv_load_more_items', array($this, "load_more_items") );

		/**
		 * Catalog filters
		 */
		add_action('wp_ajax_get_floor_slider_range_data', array($this,'get_floor_slider_range_data') );
		add_action('wp_ajax_nopriv_get_floor_slider_range_data', array($this, 'get_floor_slider_range_data') );

		add_action('wp_ajax_set_floor_slider_values', array($this,'set_floor_slider_values') );
		add_action('wp_ajax_nopriv_set_floor_slider_values', array($this,'set_floor_slider_values') );

		add_action('wp_ajax_get_room_slider_range_data', array($this,'get_room_slider_range_data') );
		add_action('wp_ajax_nopriv_get_room_slider_range_data', array($this, 'get_room_slider_range_data') );

		add_action('wp_ajax_set_room_slider_values', array($this,'set_room_slider_values') );
		add_action('wp_ajax_nopriv_set_room_slider_values', array($this,'set_room_slider_values') );

		add_action('wp_ajax_set_catalog_price_slider_values', array($this,'set_catalog_price_slider_values') );
		add_action('wp_ajax_nopriv_set_catalog_price_slider_values', array($this,'set_catalog_price_slider_values') );

		add_action('wp_ajax_set_catalog_square_slider_values', array($this,'set_catalog_square_slider_values') );
		add_action('wp_ajax_nopriv_set_catalog_square_slider_values', array($this,'set_catalog_square_slider_values') );

       	add_action('wp_ajax_set_orientation_values_by_catalog', array($this,'set_orientation_values_by_catalog') );
       	add_action('wp_ajax_nopriv_set_orientation_values_by_catalog', array($this,'set_orientation_values_by_catalog') );
       	// Sorting via AJAX
       	add_action('wp_ajax_set_sort_values', array($this, 'set_sort_values') );
       	add_action('wp_ajax_nopriv_set_sort_values', array($this, 'set_sort_values') );

		add_action('wp_ajax_mib_get_shortcode', array($this,'mib_get_shortcode') );
		add_action('wp_ajax_nopriv_mib_get_shortcode', array($this,'mib_get_shortcode') );

		add_action('wp_ajax_mib_delete_shortcode', array($this,'mib_delete_shortcode') );
		add_action('wp_ajax_nopriv_mib_delete_shortcode', array($this,'mib_delete_shortcode') );

		add_action('wp_ajax_set_garden_connection_values_by_catalog', array($this,'set_garden_connection_values_by_catalog') );
		add_action('wp_ajax_nopriv_set_garden_connection_values_by_catalog', array($this,'set_garden_connection_values_by_catalog') );

		add_action('wp_ajax_set_stairway_values', array($this,'set_stairway_values') );
		add_action('wp_ajax_nopriv_set_stairway_values', array($this,'set_stairway_values') );

		add_action('wp_ajax_set_residential_park_id', array($this,'set_residential_park_id') );
		add_action('wp_ajax_nopriv_set_residential_park_id', array($this,'set_residential_park_id') );


	}

	public function mib_get_shortcode() {

		$shortcodes = maybe_unserialize(get_option('mib_custom_shortcodes')) ?: [];
	    $name = sanitize_text_field($_POST['shortcode']);

	    if (isset($shortcodes[$name])) {
	        wp_send_json_success($shortcodes[$name]);
	    }

	    wp_send_json_error();
	}

	public function mib_delete_shortcode() {

		$shortcodes = maybe_unserialize(get_option('mib_custom_shortcodes')) ?: [];
	    $name = sanitize_text_field($_POST['shortcode']);

	    if (isset($shortcodes[$name])) {
	        unset($shortcodes[$name]);
	        update_option('mib_custom_shortcodes', serialize($shortcodes));
	        wp_send_json_success();
	    }

	    wp_send_json_error();
	}

	public function enqueue_custom_script() {
      
        wp_enqueue_style('mib', plugin_dir_url(dirname(__FILE__, 3)).'mib/assets/mib-frontend.css');

        // Include noUiSlider for improved mobile slider support
        wp_enqueue_script('nouislider-js', 'https://cdn.jsdelivr.net/npm/nouislider@15.7.0/dist/nouislider.min.js', array(), null, true);
        wp_enqueue_style('nouislider-css', 'https://cdn.jsdelivr.net/npm/nouislider@15.7.0/dist/nouislider.min.css');
        wp_enqueue_script('mib-frontend-script', plugin_dir_url(dirname(__FILE__, 3)).'mib/assets/mib-frontend.js', array('jquery', 'nouislider-js'), null, true);
            wp_localize_script('mib-frontend-script', 'ajaxurl', admin_url('admin-ajax.php'));

        // Swiper carousel for property shortcode
        wp_enqueue_style('swiper-css', 'https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.css');
        wp_enqueue_script('swiper-js', 'https://cdn.jsdelivr.net/npm/swiper@10/swiper-bundle.min.js', array(), null, true);
        wp_enqueue_script('mib-carousel', plugin_dir_url(dirname(__FILE__, 3)).'mib/assets/mib-carousel.js', array('swiper-js'), null, true);


            wp_enqueue_style('bootstrap-css', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css');
                wp_enqueue_script('bootstrap-js', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js', array('jquery'), null, true);
                wp_enqueue_script('cookie-js', 'https://cdn.jsdelivr.net/npm/js-cookie@3.0.5/dist/js.cookie.min.js', array('jquery'), null, true);

                wp_enqueue_style('dynamic-style', plugin_dir_url(dirname(__FILE__, 3)).'mib/assets/style.php');
        wp_enqueue_style(
                'font-awesome-mib',
	        'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css',
	        [],
	        '6.5.1'
	    );
        // jQuery UI touch punch eltávolítva, teljesen noUiSlider-t használunk
   		
	}

	public function customFiles(){

		/*
		* load custom styles and scripts
		*/
		wp_enqueue_style('mib', $this->pluginUrl.'assets/mib.css');
		/**
		 * Ajax handle
		 */
	  	wp_enqueue_script('ajaxHandle', plugin_dir_url(dirname(__FILE__, 3)).'mib/assets/mib.js', array('jquery'), null, true);

	  	wp_enqueue_script( 'ajaxHandle', $this->pluginUrl . 'assets/mib.js', array( 'jquery' ) );
	  	wp_localize_script( 'ajaxHandle', 'ajax_object', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ) ) );


	}

	public function setMib(){

		$obj = new Mib();
  		$result = $obj->getPostContent($_POST['post_id'], $_POST['target_lang']);

  		if ( !isset($result['error']) or !empty($result) ) {
  			$status = 'success';
  		}else{
  			$status = 'error';
  		}

		$returnData = array('status' => $status, 'errormsg' => $result);

		echo json_encode($returnData);

		wp_die(); // ajax call must die to avoid trailing 0 in your response
	}

	public function set_garden_connection_values_by_catalog() {


	    $perPage = ($_POST['page_type'] == 'card') ? ((isset($_POST['apartman_number']) && !empty($_POST['apartman_number']) ) ? $_POST['apartman_number'] : $this->numberOfApartmens) : 50;

                list($args, $page) = $this->getArgumentumsByCatalog($_POST);

                $currentPage = isset($_POST['page']) ? (int) $_POST['page'] : 1;
                if ($currentPage <= 0) {
                    $currentPage = 1;
                }

                list($slider_min, $slider_max, $price_slider_min, $price_slider_max, $floor_slider_min, $floor_slider_max, $room_slider_min, $room_slider_max) = $this->getBaseSliderDatas();
		$html = $this->getTable($args, $page, $perPage, $_POST['page_type']);

		wp_send_json_success([
			'html' 		 => $html,
	        'slider_min' => $slider_min,
	        'slider_max' => $slider_max,
	        'price_slider_min' => $price_slider_min,
	        'price_slider_max' => $price_slider_max,
	        'floor_slider_min' => $floor_slider_min,
	        'floor_slider_max' => $floor_slider_max,
	        'room_slider_min' => $room_slider_min,
	        'room_slider_max' => $room_slider_max,
	        'orientation_filter' => ( (isset($_POST['apartman_number']) && !empty($this->filterType) && in_array('orientation_filters', $this->filterType['extras'])) ) ? 1 : null,
	        'available_only' => ( (isset($_POST['apartman_number']) && !empty($this->filterType) && in_array('available_only', $this->filterType['extras'])) ) ? 1 : null
	    ]);

		wp_die();
	}

	public function set_residential_park_id(){

		$perPage = ($_POST['page_type'] == 'card') ? ((isset($_POST['apartman_number']) && !empty($_POST['apartman_number']) ) ? $_POST['apartman_number'] : $this->numberOfApartmens) : 50;

		list($args, $page) = $this->getArgumentumsByCatalog($_POST);


		list($slider_min, $slider_max, $price_slider_min, $price_slider_max, $floor_slider_min, $floor_slider_max, $room_slider_min, $room_slider_max, $garden_connection) = $this->getBaseSliderDatas();
		$html = $this->getTable($args, $page, $perPage, $_POST['page_type']);

		wp_send_json_success([
			'html' 		 => $html,
	        'slider_min' => $slider_min,
	        'slider_max' => $slider_max,
	        'price_slider_min' => $price_slider_min,
	        'price_slider_max' => $price_slider_max,
	        'floor_slider_min' => $floor_slider_min,
	        'floor_slider_max' => $floor_slider_max,
	        'room_slider_min' => $room_slider_min,
	        'room_slider_max' => $room_slider_max,
	        'garden_connection' => $garden_connection,
	        'orientation_filter' => ( (isset($_POST['apartman_number']) && !empty($this->filterType) && in_array('orientation_filters', $this->filterType['extras'])) ) ? 1 : null,
	        'available_only' => ( (isset($_POST['apartman_number']) && !empty($this->filterType) && in_array('available_only', $this->filterType['extras'])) ) ? 1 : null
	    ]);

		wp_die();
	}

	public function set_stairway_values() {


	    $perPage = ($_POST['page_type'] == 'card') ? ((isset($_POST['apartman_number']) && !empty($_POST['apartman_number']) ) ? $_POST['apartman_number'] : $this->numberOfApartmens) : 50;

		list($args, $page) = $this->getArgumentumsByCatalog($_POST);


		list($slider_min, $slider_max, $price_slider_min, $price_slider_max, $floor_slider_min, $floor_slider_max, $room_slider_min, $room_slider_max, $garden_connection) = $this->getBaseSliderDatas();
		$html = $this->getTable($args, $page, $perPage, $_POST['page_type']);

		wp_send_json_success([
			'html' 		 => $html,
	        'slider_min' => $slider_min,
	        'slider_max' => $slider_max,
	        'price_slider_min' => $price_slider_min,
	        'price_slider_max' => $price_slider_max,
	        'floor_slider_min' => $floor_slider_min,
	        'floor_slider_max' => $floor_slider_max,
	        'room_slider_min' => $room_slider_min,
	        'room_slider_max' => $room_slider_max,
	        'garden_connection' => $garden_connection,
	        'orientation_filter' => ( (isset($_POST['apartman_number']) && !empty($this->filterType) && in_array('orientation_filters', $this->filterType['extras'])) ) ? 1 : null,
	        'available_only' => ( (isset($_POST['apartman_number']) && !empty($this->filterType) && in_array('available_only', $this->filterType['extras'])) ) ? 1 : null
	    ]);

		wp_die();
	}

	public function set_orientation_values_by_catalog() {

	    $perPage = ($_POST['page_type'] == 'card') ? ((isset($_POST['apartman_number']) && !empty($_POST['apartman_number']) ) ? $_POST['apartman_number'] : $this->numberOfApartmens) : 50;

		list($args, $page) = $this->getArgumentumsByCatalog($_POST);

        list($slider_min, $slider_max, $price_slider_min, $price_slider_max, $floor_slider_min, $floor_slider_max, $room_slider_min, $room_slider_max, $garden_connection) = $this->getBaseSliderDatas();
		$html = $this->getTable($args, $page, $perPage, $_POST['page_type']);

        wp_send_json_success([
            'html'                => $html,
            'slider_min'          => $slider_min,
            'slider_max'          => $slider_max,
            'price_slider_min'    => $price_slider_min,
            'price_slider_max'    => $price_slider_max,
            'floor_slider_min'    => $floor_slider_min,
            'floor_slider_max'    => $floor_slider_max,
            'room_slider_min'     => $room_slider_min,
            'room_slider_max'     => $room_slider_max,
            'garden_connection'   => $garden_connection,
            'orientation_filter'  => ( (isset($_POST['apartman_number']) && !empty($this->filterType) && in_array('orientation_filters', $this->filterType['extras'])) ) ? 1 : null,
            'available_only'      => ( (isset($_POST['apartman_number']) && !empty($this->filterType) && in_array('available_only', $this->filterType['extras'])) ) ? 1 : null
        ]);

		wp_die();
	}

	public function set_floor_slider_values() {

	    $perPage = ($_POST['page_type'] == 'card') ? ((isset($_POST['apartman_number']) && !empty($_POST['apartman_number']) ) ? $_POST['apartman_number'] : $this->numberOfApartmens) : 50;

		list($args, $page) = $this->getArgumentumsByCatalog($_POST);


        list($slider_min, $slider_max, $price_slider_min, $price_slider_max, $floor_slider_min, $floor_slider_max, $room_slider_min, $room_slider_max, $garden_connection) = $this->getBaseSliderDatas();

		$html = $this->getTable($args, $page, $perPage, $_POST['page_type']);

        wp_send_json_success([
            'html'                => $html,
            'slider_min'          => $slider_min,
            'slider_max'          => $slider_max,
            'price_slider_min'    => $price_slider_min,
            'price_slider_max'    => $price_slider_max,
            'floor_slider_min'    => $floor_slider_min,
            'floor_slider_max'    => $floor_slider_max,
            'room_slider_min'     => $room_slider_min,
            'room_slider_max'     => $room_slider_max,
            'garden_connection'   => $garden_connection
        ]);

		wp_die();
	}

	public function set_room_slider_values() {

	    $perPage = ($_POST['page_type'] == 'card') ? ((isset($_POST['apartman_number']) && !empty($_POST['apartman_number']) ) ? $_POST['apartman_number'] : $this->numberOfApartmens) : 50;

		list($args, $page) = $this->getArgumentumsByCatalog($_POST);

        list($slider_min, $slider_max, $price_slider_min, $price_slider_max, $floor_slider_min, $floor_slider_max, $room_slider_min, $room_slider_max, $garden_connection) = $this->getBaseSliderDatas();
		$html = $this->getTable($args, $page, $perPage, $_POST['page_type']);

        wp_send_json_success([
            'html'                => $html,
            'slider_min'          => $slider_min,
            'slider_max'          => $slider_max,
            'price_slider_min'    => $price_slider_min,
            'price_slider_max'    => $price_slider_max,
            'floor_slider_min'    => $floor_slider_min,
            'floor_slider_max'    => $floor_slider_max,
            'room_slider_min'     => $room_slider_min,
            'room_slider_max'     => $room_slider_max,
            'garden_connection'   => $garden_connection
        ]);

		wp_die();
	}

	public function set_catalog_price_slider_values()
	{

		$perPage = ($_POST['page_type'] == 'card') ? ((isset($_POST['apartman_number']) && !empty($_POST['apartman_number']) ) ? $_POST['apartman_number'] : $this->numberOfApartmens) : 50;

		list($args, $page) = $this->getArgumentumsByCatalog($_POST);


        list($slider_min, $slider_max, $price_slider_min, $price_slider_max, $floor_slider_min, $floor_slider_max, $room_slider_min, $room_slider_max, $garden_connection) = $this->getBaseSliderDatas();
		$html = $this->getTable($args, $page, $perPage, $_POST['page_type']);

        wp_send_json_success([
            'html'                => $html,
            'slider_min'          => $slider_min,
            'slider_max'          => $slider_max,
            'price_slider_min'    => $price_slider_min,
            'price_slider_max'    => $price_slider_max,
            'floor_slider_min'    => $floor_slider_min,
            'floor_slider_max'    => $floor_slider_max,
            'room_slider_min'     => $room_slider_min,
            'room_slider_max'     => $room_slider_max,
            'garden_connection'   => $garden_connection
        ]);

		wp_die();
	}

	public function set_catalog_square_slider_values()
	{
		$perPage = ($_POST['page_type'] == 'card') ? ((isset($_POST['apartman_number']) && !empty($_POST['apartman_number']) ) ? $_POST['apartman_number'] : $this->numberOfApartmens) : 50;

		list($args, $page) = $this->getArgumentumsByCatalog($_POST);

        list($slider_min, $slider_max, $price_slider_min, $price_slider_max, $floor_slider_min, $floor_slider_max, $room_slider_min, $room_slider_max, $garden_connection) = $this->getBaseSliderDatas();
		$html = $this->getTable($args, $page, $perPage, $_POST['page_type']);

        wp_send_json_success([
            'html'                => $html,
            'slider_min'          => $slider_min,
            'slider_max'          => $slider_max,
            'price_slider_min'    => $price_slider_min,
            'price_slider_max'    => $price_slider_max,
            'floor_slider_min'    => $floor_slider_min,
            'floor_slider_max'    => $floor_slider_max,
            'room_slider_min'     => $room_slider_min,
            'room_slider_max'     => $room_slider_max,
            'garden_connection'   => $garden_connection
        ]);

		wp_die();
	}

	public function set_slider_values(){

		$perPage = ($_POST['page_type'] == 'card') ? ((isset($_POST['apartman_number']) && !empty($_POST['apartman_number']) ) ? $_POST['apartman_number'] : $this->numberOfApartmens) : 50;
		list($args, $page) = $this->getArgumentums($_POST);

		list($slider_min, $slider_max, $price_slider_min, $price_slider_max) = $this->getBaseSliderDatas();
		$html = $this->getTable($args, $page, $perPage, $_POST['page_type']);

		wp_send_json_success([
			'html' 		 => $html,
	        'slider_min' => $slider_min,
	        'slider_max' => $slider_max,
	        'price_slider_min' => $price_slider_min,
	        'price_slider_max' => $price_slider_max,
	    ]);

		wp_die();
	}
	/**
	 * price slider
	 */
	public function set_price_slider_values(){

		$perPage = ($_POST['page_type'] == 'card') ? ((isset($_POST['apartman_number']) && !empty($_POST['apartman_number']) ) ? $_POST['apartman_number'] : $this->numberOfApartmens) : 50;
		list($args, $page) = $this->getArgumentums($_POST);

		list($slider_min, $slider_max, $price_slider_min, $price_slider_max) = $this->getBaseSliderDatas();
		$html = $this->getTable($args, $page, $perPage, $_POST['page_type']);

		wp_send_json_success([
			'html' 		 => $html,
	        'slider_min' => $slider_min,
	        'slider_max' => $slider_max,
	        'price_slider_min' => $price_slider_min,
	        'price_slider_max' => $price_slider_max,
	    ]);

		wp_die();
	}

	public function get_floor_slider_range_data() {
	    $min = -1;
	    $max = 10;

	    if (isset( $this->filterOptionDatas['mib-filter-floor-from'] ) && isset( $this->filterOptionDatas['mib-filter-floor-to'] ) ) {
	    	
	        $min = $this->filterOptionDatas['mib-filter-floor-from'];
	        $max = $this->filterOptionDatas['mib-filter-floor-to'];
	    }
	    echo json_encode(["min" => $min, "max" => $max]);

	    wp_die();
	}

	public function get_room_slider_range_data() {
	    $min = -1;
	    $max = 10;

	    if (isset( $this->filterOptionDatas['mib-filter-room-from'] ) && isset( $this->filterOptionDatas['mib-filter-room-to'] ) ) {
	    	
	        $min = $this->filterOptionDatas['mib-filter-room-from'];
	        $max = $this->filterOptionDatas['mib-filter-room-to'];
	    }
	    echo json_encode(["min" => $min, "max" => $max]);

	    wp_die();
	}

	public function get_slider_range_data(){

		$min = 0;
		$max = 200;
		$availability = false;
		if ($this->filterOptionDatas['mib-filter-square-meter-slider-min'] !==null && $this->filterOptionDatas['mib-filter-square-meter-slider-max'] !== null) {
			
			$min = $this->filterOptionDatas['mib-filter-square-meter-slider-min'];
			$max = $this->filterOptionDatas['mib-filter-square-meter-slider-max'];
		}

		if ($this->filterOptionDatas['mib-filter-availability'] !== null) {
			
			$availability = $this->filterOptionDatas['inactive_hide'];
		}

		echo json_encode(["min" => $min, "max" => $max, "availability" => $availability]);

		wp_die();
	}

	public function get_price_slider_range_data(){

		$min = 0;
		$max = 100000;
		if ($this->filterOptionDatas['mib-filter-price-slider-min'] !==null && $this->filterOptionDatas['mib-filter-price-slider-max'] !== null) {
			
			$min = $this->filterOptionDatas['mib-filter-price-slider-min'];
			$max = $this->filterOptionDatas['mib-filter-price-slider-max'];
		}

		echo json_encode(["min" => $min, "max" => $max]);

		wp_die();
	}

	public function get_square_slider_range_data(){

		$min = 0;
		$max = 100000;
		if ($this->filterOptionDatas['mib-filter-square-meter-slider-min'] !==null && $this->filterOptionDatas['mib-filter-square-meter-slider-max'] !== null) {
			
			$min = $this->filterOptionDatas['mib-filter-square-meter-slider-min'];
			$max = $this->filterOptionDatas['mib-filter-square-meter-slider-max'];
		}

		echo json_encode(["min" => $min, "max" => $max]);

		wp_die();
	}

	public function load_paginated_data() {

		$perPage = ($_POST['page_type'] == 'card') ? ((isset($_POST['apartman_number']) && !empty($_POST['apartman_number']) ) ? $_POST['apartman_number'] : $this->numberOfApartmens) : 50;
		
		list($args, $page) = $this->getArgumentums($_POST);

        list($slider_min, $slider_max, $price_slider_min, $price_slider_max, $floor_slider_min, $floor_slider_max, $room_slider_min, $room_slider_max, $garden_connection) = $this->getBaseSliderDatas();
		$html = $this->getTable($args, $page, $perPage, $_POST['page_type']);

        wp_send_json_success([
            'html'                => $html,
            'slider_min'          => $slider_min,
            'slider_max'          => $slider_max,
            'price_slider_min'    => $price_slider_min,
            'price_slider_max'    => $price_slider_max,
            'floor_slider_min'    => $floor_slider_min,
            'floor_slider_max'    => $floor_slider_max,
            'room_slider_min'     => $room_slider_min,
            'room_slider_max'     => $room_slider_max,
            'garden_connection'   => $garden_connection,
            'orientation_filter'  => ( (isset($_POST['apartman_number']) && !empty($this->filterType) && in_array('orientation_filters', $this->filterType['extras'])) ) ? 1 : null,
            'available_only'      => ( (isset($_POST['apartman_number']) && !empty($this->filterType) && in_array('available_only', $this->filterType['extras'])) ) ? 1 : null
        ]);

	    wp_die();
	}

	public function delete_filters()
	{

		$params = [];

		$perPage = ($_POST['page_type'] == 'card') ? ((isset($_POST['apartman_number']) && !empty($_POST['apartman_number']) ) ? $_POST['apartman_number'] : $this->numberOfApartmens) : 50;
		list($args, $page) = $this->getArgumentums($params);

	    // Alapértelmezett értékek megadása (opcionálisan, ha nincs mentett érték)
	    list($slider_min, $slider_max, $price_slider_min, $price_slider_max) = $this->getBaseSliderDatas();
		$html = $this->getTable($args, $page, $perPage, $_POST['page_type']);


		wp_send_json_success([
			'html' 		 => $html,
	        'slider_min' => $slider_min,
	        'slider_max' => $slider_max,
	        'price_slider_min' => $price_slider_min,
	        'price_slider_max' => $price_slider_max,
	    ]);
	    wp_die();
	}

	public function delete_catalog_filters()
	{
		$params = [];
		$perPage = ($_POST['page_type'] == 'card') ? ((isset($_POST['apartman_number']) && !empty($_POST['apartman_number']) ) ? $_POST['apartman_number'] : $this->numberOfApartmens) : 50;

		list($args, $page) = $this->getArgumentumsByCatalog($params);

        list($slider_min, $slider_max, $price_slider_min, $price_slider_max, $floor_slider_min, $floor_slider_max, $room_slider_min, $room_slider_max, $garden_connection) = $this->getBaseSliderDatas();
		$html = $this->getTable($args, $page, $perPage, $_POST['page_type']);

		wp_send_json_success([
			'html' 		 => $html,
	        'slider_min' => $slider_min,
	        'slider_max' => $slider_max,
	        'price_slider_min' => $price_slider_min,
	        'price_slider_max' => $price_slider_max,
	        'floor_slider_min' => $floor_slider_min,
	        'floor_slider_max' => $floor_slider_max,
	        'room_slider_min' => $room_slider_min,
        'room_slider_max' => $room_slider_max,
        'garden_connection' => $garden_connection,
	        'orientation_filter' => (
			    (isset($_POST['apartman_number']) && !empty($this->filterType) && in_array('orientation_filters', $this->filterType['extras']))
			        ? 1
			        : ((isset($this->filterOptionDatas['mib-filter-orientation']) && !empty($this->filterOptionDatas['mib-filter-orientation'])) ? 1 : null)
			),
			'available_only' => (
			    (isset($_POST['apartman_number']) && !empty($this->filterType) && in_array('available_only', $this->filterType['extras']))
			        ? 1
			        : ((isset($this->filterOptionDatas['mib-filter-availability']) && !empty($this->filterOptionDatas['mib-filter-availability'])) ? 1 : null)
			),
	    ]);

		wp_die();
	}

	/**
	 * get default slider values
	 */
	private function getBaseSliderDatas()
	{

		if (isset($_POST['shortcode']) && !empty($_POST['shortcode'])) {
			
			$filters = $this->filterType['filters'] ?? [];

		    if (is_array($filters)) {

		        if (in_array('floor', $filters)) {
		            $floor_slider_min = $this->filterType['ranges']['floor']['min'];
		            $floor_slider_max = $this->filterType['ranges']['floor']['max'];
		        }

		        if (in_array('room', $filters)) {
		            $room_slider_min = $this->filterType['ranges']['room']['min'];
		            $room_slider_max = $this->filterType['ranges']['room']['max'];
		        }

		        if (in_array('area', $filters)) {
		            $slider_min = $this->filterType['ranges']['area']['min'];
		            $slider_max = $this->filterType['ranges']['area']['max'];
		        }

		        if (in_array('price', $filters)) {
		            $price_slider_min = $this->filterType['ranges']['price']['min'];
		            $price_slider_max = $this->filterType['ranges']['price']['max'];
		        }

		    }

		}else{

			$slider_min = isset($this->filterOptionDatas['mib-filter-square-meter-slider-min']) ? $this->filterOptionDatas['mib-filter-square-meter-slider-min'] : 0;
	    	$slider_max = isset($this->filterOptionDatas['mib-filter-square-meter-slider-max']) ? $this->filterOptionDatas['mib-filter-square-meter-slider-max'] : 200;
	    	$price_slider_min = isset($this->filterOptionDatas['mib-filter-price-slider-min']) ? $this->filterOptionDatas['mib-filter-price-slider-min'] : 0;
	    	$price_slider_max = isset($this->filterOptionDatas['mib-filter-price-slider-max']) ? $this->filterOptionDatas['mib-filter-price-slider-max'] : 100000000;
	    	$floor_slider_min = isset($this->filterOptionDatas['mib-filter-floor-from']) ? $this->filterOptionDatas['mib-filter-floor-from'] : 0;
	    	$floor_slider_max = isset($this->filterOptionDatas['mib-filter-floor-to']) ? $this->filterOptionDatas['mib-filter-floor-to'] : 10;
	    	$room_slider_min = isset($this->filterOptionDatas['mib-filter-room-from']) ? $this->filterOptionDatas['mib-filter-room-from'] : 0;
	    	$room_slider_max = isset($this->filterOptionDatas['mib-filter-room-to']) ? $this->filterOptionDatas['mib-filter-room-to'] : 10;
		}


	    return [$slider_min, $slider_max, $price_slider_min, $price_slider_max, $floor_slider_min, $floor_slider_max, $room_slider_min, $room_slider_max];
	}

	public function search_data() {

		print_r( $_POST['page'] );
	    // Itt implementáld a keresési logikát, hasonlóan a paginációhoz.
	    wp_die(); // Mindig befejezzük ezzel az AJAX kezelőt
	}

	public function filter_data_by_room()
	{
		$perPage = ($_POST['page_type'] == 'card') ? ((isset($_POST['apartman_number']) && !empty($_POST['apartman_number']) ) ? $_POST['apartman_number'] : $this->numberOfApartmens) : 50;
		list($args, $page) = $this->getArgumentums($_POST);
		list($slider_min, $slider_max, $price_slider_min, $price_slider_max) = $this->getBaseSliderDatas();
		$html = $this->getTable($args, $page, $perPage, $_POST['page_type']);

		wp_send_json_success([
			'html' 		 => $html,
	        'slider_min' => $slider_min,
	        'slider_max' => $slider_max,
	        'price_slider_min' => $price_slider_min,
	        'price_slider_max' => $price_slider_max,
	    ]);

		wp_die(); 
	}

	public function filter_data_by_floor() {

		$perPage = ($_POST['page_type'] == 'card') ? ((isset($_POST['apartman_number']) && !empty($_POST['apartman_number']) ) ? $_POST['apartman_number'] : $this->numberOfApartmens) : 50;
		list($args, $page) = $this->getArgumentums($_POST);
		list($slider_min, $slider_max, $price_slider_min, $price_slider_max) = $this->getBaseSliderDatas();
		$html = $this->getTable($args, $page, $perPage, $_POST['page_type']);

		wp_send_json_success([
			'html' 		 => $html,
	        'slider_min' => $slider_min,
	        'slider_max' => $slider_max,
	        'price_slider_min' => $price_slider_min,
	        'price_slider_max' => $price_slider_max,
	    ]);

	    wp_die(); 
	}
   /**
    * AJAX handler for sorting values
    */
   public function set_sort_values()
   {
       $perPage = ($_POST['page_type'] == 'card')
           ? ((isset($_POST['apartman_number']) && !empty($_POST['apartman_number']))
               ? $_POST['apartman_number']
               : $this->numberOfApartmens)
           : 50;

        list($args, $page) = ($_POST['page_type'] == 'card') ? $this->getArgumentumsByCatalog($_POST) : $this->getArgumentums($_POST);

       	// Retrieve all slider ranges: area, price, floor, room
        list($slider_min, $slider_max, $price_slider_min, $price_slider_max, $floor_slider_min, $floor_slider_max, $room_slider_min, $room_slider_max, $garden_connection) = $this->getBaseSliderDatas();
       $html = $this->getTable($args, $page, $perPage, $_POST['page_type']);

       wp_send_json_success([
           'html'                => $html,
           'slider_min'          => $slider_min,
           'slider_max'          => $slider_max,
           'price_slider_min'    => $price_slider_min,
           'price_slider_max'    => $price_slider_max,
           'floor_slider_min'    => $floor_slider_min,
           'floor_slider_max'    => $floor_slider_max,
           'room_slider_min'     => $room_slider_min,
           'room_slider_max'     => $room_slider_max,
       	   'garden_connection' 	 => $garden_connection,
       ]);
       wp_die();
   }

	public function filter_data_by_availability()
	{
		$perPage = ($_POST['page_type'] == 'card') ? ((isset($_POST['apartman_number']) && !empty($_POST['apartman_number']) ) ? $_POST['apartman_number'] : $this->numberOfApartmens) : 50;
		list($args, $page) = $this->getArgumentums($_POST);
		list($slider_min, $slider_max, $price_slider_min, $price_slider_max) = $this->getBaseSliderDatas();
		$html = $this->getTable($args, $page, $perPage, $_POST['page_type']);

		wp_send_json_success([
			'html' 		 => $html,
	        'slider_min' => $slider_min,
	        'slider_max' => $slider_max,
	        'price_slider_min' => $price_slider_min,
	        'price_slider_max' => $price_slider_max,
	    ]);

	    wp_die(); 
	}

	public function filter_data_by_orientation()
	{
		$perPage = ($_POST['page_type'] == 'card') ? ((isset($_POST['apartman_number']) && !empty($_POST['apartman_number']) ) ? $_POST['apartman_number'] : $this->numberOfApartmens) : 50;
		list($args, $page) = $this->getArgumentums($_POST);
		list($slider_min, $slider_max, $price_slider_min, $price_slider_max) = $this->getBaseSliderDatas();
		$html = $this->getTable($args, $page, $perPage, $_POST['page_type']);

		wp_send_json_success([
			'html' 		 => $html,
	        'slider_min' => $slider_min,
	        'slider_max' => $slider_max,
	        'price_slider_min' => $price_slider_min,
	        'price_slider_max' => $price_slider_max,
	    ]);

	    wp_die(); 
	}

	private function getArgumentumsByCatalog($params)
	{

		$args = [];

		if ( !empty($params['price_slider_min_value']) || !empty($params['price_slider_max_value']) ) {
		    $args = array_merge($args, ['price' =>  $params['price_slider_min_value']."-".$params['price_slider_max_value']]);
		}

		if ( !empty($params['slider_min_value']) || !empty($params['slider_max_value']) ) {
		    $args = array_merge($args, ['bruttoFloorArea' =>  $params['slider_min_value']."-".$params['slider_max_value']]);
		}

		if ( isset($params['floor_slider_min_value']) && isset($params['floor_slider_max_value']) ) {
		    $args = array_merge($args, ['floor' => $params['floor_slider_min_value']."-".$params['floor_slider_max_value']] );
		}

		if ( isset($params['room_slider_min_value']) && isset($params['room_slider_max_value'])) {
	
		    $args = array_merge($args, ['numberOfRooms' => $params['room_slider_min_value']."-".$params['room_slider_max_value'] ]);
		}

        if ( !empty($params['typeOfBalcony'] ) ) {
            $params['typeOfBalcony'] = (!is_string($params['typeOfBalcony'])) ? implode(',', $params['typeOfBalcony']) : $params['typeOfBalcony'];
            $args = array_merge($args, ['typeOfBalcony' => $params['typeOfBalcony']]);
        }


        if ( !empty($params['district'] ) ) {
			$args = array_merge($args, ['district' => sanitize_text_field($params['district'])]);
		}

		if ( !empty($params['availability'] ) ) {

			$params['availability'] = (!is_string($params['availability'])) ? implode(',', $params['availability']) : $params['availability'];

		    $args = array_merge($args, ['status' => $params['availability']]);
		}

		if ( !empty($params['garden_connection'] ) ) {
		    
		   	$gardenConnection = (isset($params['garden_connection'])) ? 'true' : 'false';
		    $args = array_merge($args, ['gardenConnection' => $gardenConnection]);
		}

		if ( !empty($params['stairway'] ) ) {
		    $params['stairway'] = (!is_string($params['stairway'])) ? implode(',',$params['stairway']) : $params['stairway'];
		    $args = array_merge($args, ['stairway' => $params['stairway']]);
		} 
		if ( !empty($params['district'] ) ) {
			$args = array_merge($args, ['district' => sanitize_text_field($params['district'])]);
		}
		// Sorting parameters
		if ( !empty($params['sort']) ) {
		    $args = array_merge($args, ['sort' => sanitize_text_field($params['sort'])]);
		}
		if ( !empty($params['sortType']) ) {
		    $sortType = strtoupper($params['sortType']) === 'DESC' ? 'DESC' : 'ASC';
		    $args = array_merge($args, ['sortType' => $sortType]);
		}

            if ( isset($params['residental_park_id']) && !empty($params['residental_park_id']) ) {

                $args = array_merge($args, ['residentialParkId' => $params['residental_park_id'] ]);
            }else{

                    if (!empty($this->filterType['residential_park_ids'])) {

                            $args = array_merge($args, ['residentialParkId' => implode(',', $this->filterType['residential_park_ids']) ]);
                    }elseif (!empty($this->filterOptionDatas['residential_park_ids'])) {
                            $args = array_merge($args, ['residentialParkId' => implode(',', $this->filterOptionDatas['residential_park_ids']) ]);
                    }else{
                            $args = array_merge($args, ['residentialParkId' => $this->residentialParkId ]);
                    }
            }

            //Shortcode

		

                if (!empty($this->filterType['apartment_skus'])) {

                        $args = array_merge($args, ['name' => implode(',', $this->filterType['apartment_skus']) ]);
                }

                if (!empty($this->filterType['type'])) {
                        $args = array_merge($args, ['type' => $this->filterType['type']]);
                }

                if (!empty($this->filterType['extras']) && in_array('hide_unavailable', $this->filterType['extras']) ) {
                        $args = array_merge($args, ['status' => 'Available']);
                }

                // Force apartment type for table view
                if (isset($params['page_type']) && $params['page_type'] === 'table') {
                        $args = array_merge($args, ['type' => 'lakás']);
                }

		$page = 1;
		if (isset($params['page']) ) {

			$page = $params['page'];
		}
		

		return [$args, $page];
	}

	private function getArgumentums($params)
	{

		$args = [];

		if ( !empty($params['price_slider_min_value']) || !empty($params['price_slider_max_value']) ) {
		    $args = array_merge($args, ['price' =>  $params['price_slider_min_value']."-".$params['price_slider_max_value']]);
		}

		if ( !empty($params['slider_min_value']) || !empty($params['slider_max_value']) ) {
		    $args = array_merge($args, ['bruttoFloorArea' =>  $params['slider_min_value']."-".$params['slider_max_value']]);
		}

		if ( !empty($params['floor']) ) {
		    $args = array_merge($args, ['floor' => (!is_string($params['floor'])) ? implode(',',$params['floor']) : $params['floor']]);
		}

		if ( !empty($params['room']) ) {
		    $params['room'] = (!is_string($params['room'])) ? implode(',',$params['room']) : $params['room'];
		    $args = array_merge($args, ['numberOfRooms' => $params['room']]);
		}

                if ( !empty($params['typeOfBalcony'] ) ) {
                    $params['typeOfBalcony'] = (!is_string($params['typeOfBalcony'])) ? implode(',', $params['typeOfBalcony']) : $params['typeOfBalcony'];
                    $args = array_merge($args, ['typeOfBalcony' => $params['typeOfBalcony']]);
                }


		if ( !empty($params['garden_connection'] ) ) {
		    
		   	$gardenConnection = (isset($params['garden_connection'])) ? 'true' : 'false';
		    $args = array_merge($args, ['gardenConnection' => $gardenConnection]);
		} 

		if ( !empty($params['availability'] ) ) {

			
			$params['availability'] = (!is_string($params['availability'])) ? implode(',',$params['availability']) : $params['availability'];
       		    $args = array_merge($args, ['status' => $params['availability']]);
       		}
		if ( !empty($params['district'] ) ) {
			$args = array_merge($args, ['district' => sanitize_text_field($params['district'])]);
		}
       		// Sorting parameters
       		if (!empty($params['sort'])) {
       		    $args = array_merge($args, ['sort' => sanitize_text_field($params['sort'])]);
       		}
       		if (!empty($params['sortType'])) {
       		    $sortType = strtoupper($params['sortType']) === 'DESC' ? 'DESC' : 'ASC';
       		    $args = array_merge($args, ['sortType' => $sortType]);
       		}

            if (!empty($this->filterType['residential_park_ids'])) {

                    $args = array_merge($args, ['residentialParkId' => implode(',', $this->filterType['residential_park_ids']) ]);
            }elseif (!empty($this->filterOptionDatas['residential_park_ids'])) {
                    $args = array_merge($args, ['residentialParkId' => implode(',', $this->filterOptionDatas['residential_park_ids']) ]);
            }else{
                    $args = array_merge($args, ['residentialParkId' => $this->residentialParkId ]);
            }

                if (!empty($this->filterType['apartment_skus'])) {


                        $args = array_merge($args, ['name' => implode(',', $this->filterType['apartment_skus']) ]);
                }

                if (!empty($this->filterType['type'])) {
                        $args = array_merge($args, ['type' => $this->filterType['type']]);
                }
                if (!empty($this->filterType['extras']) && in_array('hide_unavailable', $this->filterType['extras']) ) {
                        $args = array_merge($args, ['status' => 'Available']);

                }elseif(!empty($this->filterOptionDatas) && $this->filterOptionDatas['inactive_hide'] == 1){
                        $args = array_merge($args, ['status' => 'Available']);
                }

                // Force apartment type for table view
                if (isset($params['page_type']) && $params['page_type'] === 'table') {
                        $args = array_merge($args, ['type' => 'lakás']);
                }

		$page = 1;
		if (isset($params['page']) ) {

			$page = $params['page'];
		}
		

		return [$args, $page];
	}

	public function getTable($args = [], $currentPage = 1, $perPage = 50, $type = 'table')
	{
		$mib = new MibAuthController();

		$datas = $mib->getApartmentsForFrontEnd($perPage, $currentPage, $args);

		$table_data = $this->setDataToTable($datas);

                //egyedi shortcode-os megjelenítés.
                if (isset($_POST['shortcode']) && !empty($_POST['shortcode'])) {

                        // merge dynamic filter configuration with current args so
                        // sort options remain selected after AJAX refresh
                        $shortcodeConfig = array_merge($this->filterType, $args);

                        $html = $this->getCardHtmlShortCode(
                            $table_data,
                            $datas['total'],
                            $currentPage,
                            $shortcodeConfig,
                            $_POST['shortcode'],
                            $_POST['apartman_number']
                        );
                }
		elseif ($type == 'card') {

			$html = $this->getCardHtml($table_data, $datas['total'], $currentPage, $args);

		}else{

			$html = $this->getTableHtml($table_data, $datas['total'], $currentPage, $args);
		}

		return $html;

	}

	public function load_more_items() {

		//print_r($_POST);

            $perPage = (isset($_POST['apartman_number']) && !empty($_POST['apartman_number'])) ? (int) $_POST['apartman_number'] : (int) $this->numberOfApartmens;
            if ($perPage <= 0) {
                $perPage = (int) $this->numberOfApartmens;
            }

                list($args, $page) = $this->getArgumentumsByCatalog($_POST);

                $currentPage = isset($_POST['page']) ? (int) $_POST['page'] : 1;
                if ($currentPage <= 0) {
                    $currentPage = 1;
                }

                list($slider_min, $slider_max, $price_slider_min, $price_slider_max, $floor_slider_min, $floor_slider_max, $room_slider_min, $room_slider_max) = $this->getBaseSliderDatas();

                $mib = new MibAuthController();
                $datas = $mib->getApartmentsForFrontEnd($perPage, $currentPage, $args);

                $table_data = $this->setDataToTable($datas);
                if (isset($_POST['page_type']) && $_POST['page_type'] === 'carousel') {
                    $html = $this->getCarouselSlidesHtml($table_data);
                } else {
                    $html = $this->getMoreCards($table_data, $datas['total'], $currentPage);
                }

		$hasMore = ($currentPage * $perPage) < (int) $datas['total'];

		wp_send_json_success([
			'html' 		 => $html,
			'count'		 => count($table_data),
			'has_more'        => $hasMore,
			'slider_min' => $slider_min,
	        'slider_max' => $slider_max,
	        'price_slider_min' => $price_slider_min,
	        'price_slider_max' => $price_slider_max,
	        'floor_slider_min' => $floor_slider_min,
	        'floor_slider_max' => $floor_slider_max,
                'room_slider_min' => $room_slider_min,
                'room_slider_max' => $room_slider_max,
                'orientation_filter' => ( (isset($_POST['apartman_number']) && !empty($this->filterType) && in_array('orientation_filters', $this->filterType['extras'])) ) ? 1 : null,
                'available_only' => ( (isset($_POST['apartman_number']) && !empty($this->filterType) && in_array('available_only', $this->filterType['extras'])) ) ? 1 : null
            ]);

	    wp_die();
		
	}



    public function filter_data_by_district()
    {
        $perPage = ($_POST['page_type'] == 'card') ? ((isset($_POST['apartman_number']) && !empty($_POST['apartman_number']) ) ? $_POST['apartman_number'] : $this->numberOfApartmens) : 50;

		list($args, $page) = $this->getArgumentumsByCatalog($_POST);

        list($slider_min, $slider_max, $price_slider_min, $price_slider_max, $floor_slider_min, $floor_slider_max, $room_slider_min, $room_slider_max, $garden_connection) = $this->getBaseSliderDatas();
		$html = $this->getTable($args, $page, $perPage, $_POST['page_type']);

        wp_send_json_success([
            'html'                => $html,
            'slider_min'          => $slider_min,
            'slider_max'          => $slider_max,
            'price_slider_min'    => $price_slider_min,
            'price_slider_max'    => $price_slider_max,
            'floor_slider_min'    => $floor_slider_min,
            'floor_slider_max'    => $floor_slider_max,
            'room_slider_min'     => $room_slider_min,
            'room_slider_max'     => $room_slider_max,
            'garden_connection'   => $garden_connection,
            'orientation_filter'  => ( (isset($_POST['apartman_number']) && !empty($this->filterType) && in_array('orientation_filters', $this->filterType['extras'])) ) ? 1 : null,
            'available_only'      => ( (isset($_POST['apartman_number']) && !empty($this->filterType) && in_array('available_only', $this->filterType['extras'])) ) ? 1 : null
        ]);

		wp_die();
    }
}
