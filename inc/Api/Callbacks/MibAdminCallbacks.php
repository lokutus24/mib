<?php

/**
 * @package Admin menü class
 */

namespace Inc\Api\Callbacks;

use \Inc\Base\MibBaseController;

/**
 * 
 */
class MibAdminCallbacks extends MibBaseController
{
	public function adminDashboard(){

		return require_once ($this->pluginPath."templates/admin.php");
	}

	public function adminOptionSubmenu(){
		return require_once ($this->pluginPath."templates/options.php");
	}
  
	public function displayRealEstates(){
		return require_once ($this->pluginPath."templates/real_estates.php");
	}
}