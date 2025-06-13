<?php

/**
 * @package Active
 */

namespace Inc\Base;

class MibActivate
{	

	public static function activate(){

		flush_rewrite_rules();

		$default = array();

		if ( ! get_option( 'mib_options' ) ) {
			update_option( 'mib_options', $default );
		}
	}

}