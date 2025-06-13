<?php

/**
 * @package Active
 */

namespace Inc\Base;

class MibDeactivate
{
	
	public static function deactivate(){

		flush_rewrite_rules();
	}
}