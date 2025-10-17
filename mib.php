<?php
/**
* Plugin Name: MIB Wordpress Connector
* Plugin URI: https://lionstack.hu
* Description: A plugin képes az ingatlanokat különböző szűrési feltételek mellett megjeleníteni
* Version: v3.1.7
* Author: Codefusion Kft.
* Author URI: https://lionstack.hu
**/

defined('ABSPATH') or die('nem kéne..');

if (!defined('MIB_VERSION')) {
    define('MIB_VERSION', 'v3.5.5');
}



if (file_exists(dirname(__FILE__)."/vendor/autoload.php")) {
  require_once dirname(__FILE__)."/vendor/autoload.php";
}

if(!class_exists('WP_List_Table')){
    require_once( ABSPATH . 'wp-admin/includes/class-wp-list-table.php' );
}

function Mibactivate(){
	Inc\Base\MibActivate::activate();
}

function Mibdeactivate(){
	Inc\Base\MibDeactivate::deactivate();
}

register_activation_hook(__FILE__, 'Mibactivate');

register_deactivation_hook(__FILE__, 'Mibdeactivate');

if (class_exists('Inc\\MibInitial')) {
	Inc\MibInitial::MibServiceRegister();

}