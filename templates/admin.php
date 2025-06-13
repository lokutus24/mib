<div class="wrap">
	<h1 class="wp-heading-inline">Mib</h1>
	<?php settings_errors();?>

	<ul class="nav nav-tabs">
		<li class="active"><a href="#tab-1">Leírás</a></li>
	</ul>


	<div class="tab-content">
		<div id="tab-1" class="tab-pane active">
			<form method="POST" action="">
				<?php
				
					settings_fields('mib_desc');//option_group_name
					do_settings_sections('option_desc'); //section_page
					//submit_button( 'Feltöltés', 'feltoltes', 'submit', false, array());
				?>
			</form>

		</div>
		
	</div>

</div>