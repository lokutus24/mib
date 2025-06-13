<?php

/**
 * @package Admin menü class
 */

namespace Inc\Pages;

use Inc\Base\MibBaseController;
use Inc\Api\MibSettingsApi;
use Inc\Api\Callbacks\MibAdminCallbacks;
use Inc\Api\Callbacks\MibManagerCallbacks;

class MibDashboard extends MibBaseController
{
	
	public $settingsApiClass = array();

	public $adminMenuPages = array();

	public $adminSubMenuPages = array();

	public $callBacks = array();

	public $managerCallbacks = array();

	public $setSettings = array();

	public $setSections = array();

	public $setFields = array();


	public function registerFunction(){

		$this->callBacks = new MibAdminCallbacks();

		$this->settingsApiClass = new MibSettingsApi();

		$this->managerCallbacks = new MibManagerCallbacks();

		$this->setAdminPages()->addSubPages();

		//menühoz tartozó beállítások, szekciók, mezők
		$this->setSettings()
		->setSections()
		->setFields();
		$this->settingsApiClass->addPages($this->adminMenuPages)->addSubPages($this->adminSubMenuPages)
		//->setSubPagesTitle('Dashboard')
		->addSettings($this->setSettings)
		->addSections($this->setSections)
		->addFields($this->setFields)
		->register();
	}


	public function setAdminPages(){

		$this->adminMenuPages = array(
			
			[
				'page_title' => "Mib Plugin",
				'menu_title' => "Mib",
				'capability' => "manage_options",
				'menu_slug' => "Mib",
				'callback' => array($this->callBacks, 'adminDashboard'),
				'icon_url' => 'dashicons-admin-home',
				'position' => 110
			],
		);
		return $this;
	}

	public function addSubPages(){
		//sub page

		$adminPages = $this->adminMenuPages[0];


		$this->adminSubMenuPages = [

			[
				'parent_slug' => $adminPages['menu_slug'],
				'page_title' => 'Beállítások',
				'menu_title' => 'Beállítások',
				'capability' => 'manage_options',
				'menu_slug' => 'miboptions',
				'callback' => array($this->callBacks, 'adminOptionSubmenu'),
			],
			[
	            'parent_slug' => $adminPages['menu_slug'],
	            'page_title' => 'Ingatlanok',
	            'menu_title' => 'Ingatlanok',
	            'capability' => 'manage_options',
	            'menu_slug' => 'real_estates',
	            'callback' => array($this->callBacks, 'displayRealEstates'),
	        ],

		];

		
	}

	public function setSettings(){
		
		//foreach ( $this->managers as $key => $value ) {
			$this->setSettings = [

				array(
					'option_group' => 'mib_desc',
					'option_name' => 'option_desc',
					'callback' => array()
				),
				array(
					'option_group' => 'mib_settings',
					'option_name' => 'mib_option',
					'callback' => array()
				),
				array(
					'option_group' => 'mib_filter_settings',
					'option_name' => 'mib_filter',
					'callback' => array()
				),
				array(
					'option_group' => 'mib_crosssell_settings',
					'option_name' => 'mib_cross_sell',
					'callback' => array()
				),
				array(
					'option_group' => 'mib_color_settings',
					'option_name' => 'mib_colors',
					'callback' => array()
				),
				array(
					'option_group' => 'mib_short_code_settings',
					'option_name' => 'mib_short_code',
					'callback' => array()
				),

			];
		//}

		return $this;
	}

	public function setSections(){
		
		$this->setSections = array(
			[
				'id' => 'option_section', //akarmi
				'title' => "Mib ingatlan listázó",
				'callback' => array($this->managerCallbacks, 'linkUplaoder' ),
				'page' => "option_desc"
			],
			[
				'id' => 'mib_admin_index', //akarmi
				'title' => "Beállítás",
				'callback' => array($this->managerCallbacks, 'adminSectionManager' ),
				'page' => "mib_settings"
			],
			[
				'id' => 'mib_filter', //akarmi
				'title' => "Szűrő beállítás",
				'callback' => array($this->managerCallbacks, 'adminFilterSettings' ),
				'page' => "mib_filter_settings"
			],
			[
				'id' => 'mib_cross_sell', //akarmi
				'title' => "Cross Sell beállítás",
				'callback' => array($this->managerCallbacks, 'adminFilterCrossSell' ),
				'page' => "mib_crosssell_settings"
			],
			[
				'id' => 'mib_colors', //akarmi
				'title' => "Alapértelmezett szín beállítás",
				'callback' => array($this->managerCallbacks, 'adminFilterColors' ),
				'page' => "mib_color_settings"
			],
			[
				'id' => 'mib_short_code', //akarmi
				'title' => "Shortcode beállítás",
				'callback' => array($this->managerCallbacks, 'adminFilterShortCode' ),
				'page' => "mib_short_code_settings"
			],
		);

		return $this;
	}

	public function setFields(){
		
		$this->setFields = [
			
			array(
				'id' => 'option',
				'title' => 'Adatok',
				'callback' => array( $this->managerCallbacks, 'optionInputs' ),
				'page' => 'mib_settings',
				'section' => 'mib_admin_index',
				'args' => array(
					'label_for' => 'options',
					'class' => 'ui-toggle'
				)
			),
			array(
				'id' => 'filter_option',
				'title' => 'Szűrők',
				'callback' => array( $this->managerCallbacks, 'optionFilters' ),
				'page' => 'mib_filter_settings',
				'section' => 'mib_filter',
				'args' => array(
					'label_for' => 'options',
					'class' => 'ui-toggle'
				)
			),
			array(
				'id' => 'filter_option_cross_sell',
				'title' => 'Cross Sell Szűrők',
				'callback' => array( $this->managerCallbacks, 'optionCrossSell' ),
				'page' => 'mib_crosssell_settings',
				'section' => 'mib_cross_sell',
				'args' => array(
					'label_for' => 'options',
					'class' => 'ui-toggle'
				)
			),
			array(
				'id' => 'filter_option_colors',
				'title' => 'Színek',
				'callback' => array( $this->managerCallbacks, 'colorOptions' ),
				'page' => 'mib_color_settings',
				'section' => 'mib_colors',
				'args' => array(
					'label_for' => 'options',
					'class' => 'ui-toggle'
				)
			),
			array(
				'id' => 'filter_option_short_code',
				'title' => 'Shortcode létrehozása / szerkesztése',
				'callback' => array( $this->managerCallbacks, 'shortcodeOptions' ),
				'page' => 'mib_short_code_settings',
				'section' => 'mib_short_code',
				'args' => array(
					'label_for' => 'options',
					'class' => 'ui-toggle'
				)
			),
			
		];


		return $this;
	}

}