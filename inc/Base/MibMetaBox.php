<?php

/**
 * Cronhoz a routeApi
 */

namespace Inc\Base;

use Inc\Base\MibBaseController;
use Inc\Base\Mib;

class MibMetaBox extends MibBaseController
{
	/**
	 * @var Mib options
	 */
	private $options = [];

	public function registerFunction(){

		$this->options = unserialize(get_option('cf_Mib'));
		if (!empty($this->options['Mib-api'] ) ) {
			add_action( 'add_meta_boxes', [$this, 'RegisterMetaBox'] );
		}
	}

	public function RegisterMetaBox($post_type = 'post', $post = 1 ){

		add_meta_box(
		    'cf_Mib',
		    __( 'Mib', 'cf_Mib' ), // Title
		    [$this, 'render_meta_box_content'],   // Callback function that renders the content of the meta box
		    'post',                               // Admin page (or post type) to show the meta box on
		    'side',                               // Context where the box is shown on the page
		    'high',                               // Priority within that context
		    $post                                 // Arguments to pass the callback function, if any
		);
	}

	public function render_meta_box_content( $post ){

		$target_lang = (isset($this->options['target-lang'])) ? $this->options['target-lang'] : '';

		echo '
		<p class="form-field _cf_target_lang">
			<label for="_cf_target_lang">'.__( 'Cél nyelv', 'cf_Mib' ).'</label>
			<input type="text" class="short" style="" name="_cf_target_lang" id="cf_target_lang" value="'.$target_lang.'" placeholder="Add meg a cél nyelvet!Pl.: HU">
			<span>A forrás nyelvet automatikusan felismeri</span>
		 </p>';

		echo "<button type='button' postid='{$post->ID}' id='cf_translate' class='cf_tranlator_btn button button-primary' style='margin-bottom: 20px;'>
				".__( 'Fordítás', 'cf_Mib' )."</button>";

		echo'<div class="row" id="loading_cf_Mib" style="display:none"><img src="https://c.tenor.com/I6kN-6X7nhAAAAAi/loading-buffering.gif" width="50" height="50" alt="Loading Buffering Sticker - Loading Buffering Spinning Stickers" style="max-width: 522px; background-color: unset; margin: 12px;"></div>';

		echo '<div class="row"> <p id="erromsgcfMib" class="erromsgcfMib" style="color:red;display:none;margin-left: 5%;"></p></div>';
	}

}