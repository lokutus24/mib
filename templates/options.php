<div class="wrap">
	<h1 class="wp-heading-inline">Mib</h1>
	<?php 
		settings_errors(); 

		$mib_colors = '';
		$mib_cross_sell = '';
		$mib_filter_active = '';
		$short_code = '';
		$settings = 'active';

		if (isset($_POST['option_page']) && $_POST['option_page'] == 'mib_filter') {

			$mib_filter_active = 'active';
			$settings = '';
			$mib_cross_sell = '';
			$mib_colors = '';
			$short_code = '';

		}elseif(isset($_POST['option_page']) && $_POST['option_page'] == 'mib_cross_sell'){

			$mib_filter_active = '';
			$mib_colors = '';
			$settings = '';
			$short_code = '';
			$mib_cross_sell = 'active';

		}elseif(isset($_POST['option_page']) && $_POST['option_page'] == 'mib_colors'){

			$mib_colors = 'active';
			$mib_filter_active = '';
			$settings = '';
			$mib_cross_sell = '';
			$short_code = '';

		}elseif(isset($_POST['option_page']) && $_POST['option_page'] == 'short_code'){

			$mib_colors = '';
			$mib_filter_active = '';
			$settings = '';
			$mib_cross_sell = '';
			$short_code = 'active';

		}else{

			$mib_colors = '';
			$mib_cross_sell = '';
			$mib_filter_active = '';
			$short_code = '';
			$settings = 'active';
		}
	?>

	<ul class="nav nav-tabs">
		<li class="<?=$settings;?>"><a href="#tab-1">API beállítások</a></li>
		<li class="<?=$mib_filter_active;?>"><a href="#tab-2">Szűrési beállítások</a></li>
		<li class="<?=$mib_cross_sell;?>"><a href="#tab-3">Cross Sell lakások</a></li>
		<li class="<?=$mib_colors;?>"><a href="#tab-4">Szín beállítások</a></li>
		<li class="<?=$short_code;?>"><a href="#tab-5">Shortcode</a></li>
	</ul>

	<div class="tab-content">
		<div id="tab-1" class="tab-pane <?=$settings;?>">
			<form method="POST" action="">
				<?php
					settings_fields('mib_desc');//option_group_name
					do_settings_sections('mib_settings'); //section_page
					submit_button( 'Mentés', 'mentés', 'submit', false, array());
				?>
			</form>

		</div>

		<div id="tab-2" class="tab-pane <?=$mib_filter_active;?>">

			<form method="POST" action="">
				<?php
					settings_fields('mib_filter');//option_group_name
					do_settings_sections('mib_filter_settings'); //section_page
					submit_button( 'Mentés', 'mentés', 'submit', false, array());
				?>
			</form>

		</div>


		<div id="tab-3" class="tab-pane <?=$mib_cross_sell;?>">

			<form method="POST" action="">
				<?php
					settings_fields('mib_cross_sell');//option_group_name
					do_settings_sections('mib_crosssell_settings'); //section_page
					submit_button( 'Mentés', 'mentés', 'submit', false, array());
				?>
			</form>

		</div>

		<div id="tab-4" class="tab-pane <?=$mib_colors;?>">

			<form method="POST" action="">
				<?php
					settings_fields('mib_colors');//option_group_name
					do_settings_sections('mib_color_settings'); //section_page
					submit_button( 'Mentés', 'mentés', 'submit', false, array());
				?>
			</form>

		</div>

		<div id="tab-5" class="tab-pane <?=$short_code;?>">

			<form method="POST" action="">
				<?php
					settings_fields('mib_short_code');//option_group_name
					do_settings_sections('mib_short_code_settings'); //section_page
					submit_button( 'Mentés', 'mentés', 'submit', false, array());
				?>
			</form>

		</div>

		
	</div>

</div>
