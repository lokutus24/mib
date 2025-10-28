<?php 
/**
 * @package  AlecadddPlugin
 */
namespace Inc\Api\Callbacks;

use Inc\Base\MibBaseController;
use Inc\Base\MibAuthController;


class MibManagerCallbacks extends MibBaseController
{

	public function adminFilterSettings(){

		echo 'Állítsd be azokat a szűrőket, amelyeket szeretnéd, hogy megjelenjenek a Lakások kilistázásánál.';
	}

	public function adminFilterCrossSell(){

		echo 'Állítsd be a Cross Sell szűrési feltételeket, hogy mi alapján ajánljon további ingatlanokat a lakás aloldalán';
	}

	public function adminFilterColors(){

		echo 'Állítsd be az alapértelmezett színeket, amiket használni szeretnél.';
	}


	public function adminFilterShortCode(){

		echo 'Saját shortcode-ok létrehozása';
	}

	public function adminSectionManager(){

		echo 'Az email / jelszó segítségével szerzi be a tokent az API-hoz szükséges működéshez!';
	}

	public function linkUplaoder(){

		echo 'Megjeleníti az ingatlanokat szűrési feltételek segítségével';

	}



	public function addLink(){
		echo 'Adatok megadása';
	}

	public function optionFilters(){

		if (isset($_POST['option_page']) && $_POST['option_page'] == 'mib_filter') {

			 $datas = serialize(
				[
					'inactive_hide' => (isset($_POST['inactive_hide'] ) && $_POST['inactive_hide'] == 'on') ? true : false, 
					'mib-filter-floor' => (isset($_POST['mib-filter-floor'] ) && $_POST['mib-filter-floor'] == 'on') ? true : false, 
					'mib-filter-floor-from' => (isset($_POST['mib-filter-floor-from'] ) ) ? $_POST['mib-filter-floor-from'] : '',
					'mib-filter-floor-to' => (isset($_POST['mib-filter-floor-to'] ) ) ? $_POST['mib-filter-floor-to'] : '',
					'mib-filter-room-from' => (isset($_POST['mib-filter-room-from'] ) ) ? $_POST['mib-filter-room-from'] : '',
					'mib-filter-room-to' => (isset($_POST['mib-filter-room-to'] ) ) ? $_POST['mib-filter-room-to'] : '',
                                        'mib-filter-room' => (isset($_POST['mib-filter-room'] ) && $_POST['mib-filter-room'] == 'on') ? true : false,
                                        'mib-filter-district' => (isset($_POST['mib-filter-district'] ) && $_POST['mib-filter-district'] == 'on') ? true : false,
                                        'mib-filter-orientation' => (isset($_POST['mib-filter-orientation'] ) && $_POST['mib-filter-orientation'] == 'on') ? true : false,
					'mib-filter-availability' => (isset($_POST['mib-filter-availability'] ) && $_POST['mib-filter-availability'] == 'on') ? true : false,
					'mib-filter-deletefilters' => (isset($_POST['mib-filter-deletefilters'] ) && $_POST['mib-filter-deletefilters'] == 'on') ? true : false,
					'mib-filter-square-meter' => (isset($_POST['mib-filter-square-meter'] ) && $_POST['mib-filter-square-meter'] == 'on') ? true : false,
					'mib-filter-square-meter-slider-min' => (isset($_POST['mib-filter-square-meter-slider-min'] )) ? $_POST['mib-filter-square-meter-slider-min'] : 0,
					'mib-filter-square-meter-slider-max' => (isset($_POST['mib-filter-square-meter-slider-max'] ) ) ? $_POST['mib-filter-square-meter-slider-max'] : 200,
					'mib-filter-price_range' => (isset($_POST['mib-filter-price_range'] ) && $_POST['mib-filter-price_range'] == 'on') ? true : false,
					'mib-filter-price-slider-min' => (isset($_POST['mib-filter-price-slider-min'] )) ? $_POST['mib-filter-price-slider-min'] : 0,
					'mib-filter-price-slider-max' => (isset($_POST['mib-filter-price-slider-max'] ) ) ? $_POST['mib-filter-price-slider-max'] : 100000,
					'mib-filterslider_checked'	  => (isset($_POST['mib-filterslider_checked'] ) && $_POST['mib-filterslider_checked'] == 'on') ? true : false,
					'mib-loadmore_checked'	  => (isset($_POST['mib-loadmore_checked'] ) && $_POST['mib-loadmore_checked'] == 'on') ? true : false,
					'mib-display_logo'	  => (isset($_POST['mib-display_logo'] ) && $_POST['mib-display_logo'] == 'on') ? true : false,
                                        'mib-dark_logo'        => (isset($_POST['mib-dark_logo'] ) && $_POST['mib-dark_logo'] == 'on') ? true : false,
					'mib-display_address'	  => (isset($_POST['mib-display_address'] ) && $_POST['mib-display_address'] == 'on') ? true : false,
					'mib-display_sort'	  => (isset($_POST['mib-display_sort'] ) && $_POST['mib-display_sort'] == 'on') ? true : false,
					'mib-garden_connection'	  => (isset($_POST['mib-garden_connection'] ) && $_POST['mib-garden_connection'] == 'on') ? true : false,
					'mib-otthonstart'	  => (isset($_POST['mib-otthonstart'] ) && $_POST['mib-otthonstart'] == 'on') ? true : false,
					'mib-stairway'	  => (isset($_POST['mib-stairway'] ) && $_POST['mib-stairway'] == 'on') ? true : false,
                                        'residential_park_ids' => isset($_POST['residential_park_ids']) ? array_filter(array_map('intval', explode(',', $_POST['residential_park_ids']))) : [],
				]
			);
			update_option('mib_filter_options', $datas);
		}

		$mib_filter_options = maybe_unserialize( get_option('mib_filter_options') );

		$inactive_hide = (isset($mib_filter_options['inactive_hide'])  && $mib_filter_options['inactive_hide'] == 1) ? 'checked' : '';

		$floor_checked = (isset($mib_filter_options['mib-filter-floor'])  && $mib_filter_options['mib-filter-floor'] == 1) ? 'checked' : '';
		$floor_from = (isset($mib_filter_options['mib-filter-floor-from'] ) ) ? $mib_filter_options['mib-filter-floor-from'] : '';
		$floor_to = (isset($mib_filter_options['mib-filter-floor-to']) ) ? $mib_filter_options['mib-filter-floor-to'] : '';

		$filterslider_checked = (isset($mib_filter_options['mib-filterslider_checked'])  && $mib_filter_options['mib-filterslider_checked'] == 1) ? 'checked' : '';

		$room_checked = (isset($mib_filter_options['mib-filter-room'])  && $mib_filter_options['mib-filter-room'] == 1) ? 'checked' : '';
		$room_from = (isset($mib_filter_options['mib-filter-room-from'] ) ) ? $mib_filter_options['mib-filter-room-from'] : '';
                $room_to = (isset($mib_filter_options['mib-filter-room-to']) ) ? $mib_filter_options['mib-filter-room-to'] : '';


                $district_checked = (isset($mib_filter_options['mib-filter-district'])  && $mib_filter_options['mib-filter-district'] == 1) ? 'checked' : '';
                $orientation_checked = (isset($mib_filter_options['mib-filter-orientation'])  && $mib_filter_options['mib-filter-orientation'] == 1) ? 'checked' : '';
		$availability_checked = (isset($mib_filter_options['mib-filter-availability'])  && $mib_filter_options['mib-filter-availability'] == 1) ? 'checked' : '';
		$deletefilters_checked = (isset($mib_filter_options['mib-filter-deletefilters'])  && $mib_filter_options['mib-filter-deletefilters'] == 1) ? 'checked' : '';
		$square_meter = (isset($mib_filter_options['mib-filter-square-meter'])  && $mib_filter_options['mib-filter-square-meter'] == 1) ? 'checked' : '';
		$price_range = (isset($mib_filter_options['mib-filter-price_range'])  && $mib_filter_options['mib-filter-price_range'] == 1) ? 'checked' : '';
		$loadmore_checked = (isset($mib_filter_options['mib-loadmore_checked'])  && $mib_filter_options['mib-loadmore_checked'] == 1) ? 'checked' : '';

		$square_meter_min = (isset($mib_filter_options['mib-filter-square-meter-slider-min'])) ? $mib_filter_options['mib-filter-square-meter-slider-min'] : 0;
		$square_meter_max = (isset($mib_filter_options['mib-filter-square-meter-slider-max'])) ? $mib_filter_options['mib-filter-square-meter-slider-max'] : 200;

		$price_min = (isset($mib_filter_options['mib-filter-price-slider-min'])) ? $mib_filter_options['mib-filter-price-slider-min'] : 0;
		$price_max = (isset($mib_filter_options['mib-filter-price-slider-max'])) ? $mib_filter_options['mib-filter-price-slider-max'] : 100000000;

		$display_logo = (isset($mib_filter_options['mib-display_logo'])  && $mib_filter_options['mib-display_logo'] == 1) ? 'checked' : '';
                $dark_logo = (isset($mib_filter_options['mib-dark_logo'])  && $mib_filter_options['mib-dark_logo'] == 1) ? 'checked' : '';
		$display_address = (isset($mib_filter_options['mib-display_address'])  && $mib_filter_options['mib-display_address'] == 1) ? 'checked' : '';

		$display_sort = (isset($mib_filter_options['mib-display_sort'])  && $mib_filter_options['mib-display_sort'] == 1) ? 'checked' : '';

		$garden_connection = (isset($mib_filter_options['mib-garden_connection'])  && $mib_filter_options['mib-garden_connection'] == 1) ? 'checked' : '';
                $otthonstart = (isset($mib_filter_options['mib-otthonstart'])  && $mib_filter_options['mib-otthonstart'] == 1) ? 'checked' : '';

		$stairway = (isset($mib_filter_options['mib-stairway'])  && $mib_filter_options['mib-stairway'] == 1) ? 'checked' : '';
                $residential_park_ids = isset($mib_filter_options['residential_park_ids']) ? implode(',', $mib_filter_options['residential_park_ids']) : '';

		?>


                <div class='row' style='margin-bottom: 10px;'>
                        Lakópark ID-k: <input type='text' name='residential_park_ids' size='45' value='<?=$residential_park_ids;?>' placeholder='pl. 7,9,10'>
                </div>
		<div class="row" style="margin-bottom: 10px;">
			Emeletek: <input type="checkbox" name="mib-filter-floor" size="45" <?=$floor_checked;?> >
		</div>

		<div class="row" style="margin-bottom: 10px;">
			Emeletek megjelenítése: 
			<input type="text" name="mib-filter-floor-from" size="20" value="<?=$floor_from;?>" placeholder="tól. földszint = 0">
			<input type="text" name="mib-filter-floor-to" size="20" value="<?=$floor_to;?>" placeholder="ig">
		</div>
		
		<div class="row" style="margin-bottom: 10px;">
			Szobák: <input type="checkbox" name="mib-filter-room" size="45" <?=$room_checked;?>>
		</div>

                <div class="row" style="margin-bottom: 10px;">
                        Szobák megjelenítése:
                        <input type="number" name="mib-filter-room-from" size="20" value="<?=$room_from;?>" placeholder="tól. szoba = 0">
                        <input type="number" name="mib-filter-room-to" size="20" value="<?=$room_to;?>" placeholder="ig">
                </div>

                <div class="row" style="margin-bottom: 10px;">
                        Kerület: <input type="checkbox" name="mib-filter-district" size="45" <?=$district_checked;?> >
                </div>

                <div class="row" style="margin-bottom: 10px;">
                        Erkély típusa: <input type="checkbox" name="mib-filter-orientation" size="45" <?=$orientation_checked;?> >
                </div>

                <div class="row" style="margin-bottom: 10px;">
                        Elérhetőség: <input type="checkbox" name="mib-filter-availability" size="45" <?=$availability_checked;?> >
                </div>

		<div class="row" style="margin-bottom: 10px;">
			Nem elérhetők elrejtése alapbeállítás: <input type="checkbox" name="inactive_hide" size="45" <?=$inactive_hide;?> >
		</div>

		<div class="row" style="margin-bottom: 10px;">
			m² szűrő: <input type="checkbox" name="mib-filter-square-meter" size="45" <?=$square_meter;?>>
			<input type="number" name="mib-filter-square-meter-slider-min" placeholder="min" value="<?=$square_meter_min;?>">
			<input type="number" name="mib-filter-square-meter-slider-max" placeholder="max" value="<?=$square_meter_max;?>">
		</div>

		<div class="row" style="margin-bottom: 10px;">
			Ár szűrő: <input type="checkbox" name="mib-filter-price_range" size="45" <?=$price_range;?>>
			<input type="number" name="mib-filter-price-slider-min" placeholder="min" value="<?=$price_min;?>">
			<input type="number" name="mib-filter-price-slider-max" placeholder="max" value="<?=$price_max;?>">
		</div>

		<div class="row" style="margin-bottom: 10px;">
			Szűrők törlése: <input type="checkbox" name="mib-filter-deletefilters" size="45" <?=$deletefilters_checked;?>>
		</div>

		<div class="row" style="margin-bottom: 10px;">
			Slider helyett szám inputok: <input type="checkbox" name="mib-filterslider_checked" size="45" <?=$filterslider_checked;?>>
		</div>

		<div class="row" style="margin-bottom: 10px;">
			Paginate helyett Load more: <input type="checkbox" name="mib-loadmore_checked" size="45" <?=$loadmore_checked;?>>
		</div>

                <div class="row" style="margin-bottom: 10px;">
                        Logó megjelenítés: <input type="checkbox" name="mib-display_logo" size="45" <?=$display_logo;?>>
                </div>

                <div class="row" style="margin-bottom: 10px;">
                        Sötét logó: <input type="checkbox" name="mib-dark_logo" size="45" <?=$dark_logo;?>>
                </div>

		<div class="row" style="margin-bottom: 10px;">
			Helység megjelenítés: <input type="checkbox" name="mib-display_address" size="45" <?=$display_address;?>>
		</div>

		<div class="row" style="margin-bottom: 10px;">
			Rendezés: <input type="checkbox" name="mib-display_sort" size="45" <?=$display_sort;?>>
		</div>

		<div class="row" style="margin-bottom: 10px;">
			Kert kapcsolat: <input type="checkbox" name="mib-garden_connection" size="45" <?=$garden_connection;?>>
		</div>
		<div class="row" style="margin-bottom: 10px;">
			Otthon Start szűrő: <input type="checkbox" name="mib-otthonstart" size="45" <?=$otthonstart;?>>
		</div>

		<div class="row" style="margin-bottom: 10px;">
			Lépcsőház: <input type="checkbox" name="mib-stairway" size="45" <?=$stairway;?>>
		</div>

		<?php
	}

        public function optionInputs( $args ){

                if ( isset($_POST['mib-login-email']) ) {

                        $email    = isset($_POST['mib-login-email']) ? sanitize_text_field(wp_unslash($_POST['mib-login-email'])) : '';
                        $password = isset($_POST['mib-login-password']) ? sanitize_text_field(wp_unslash($_POST['mib-login-password'])) : '';
                        $parkId   = isset($_POST['mib-residential-park-id']) ? sanitize_text_field(wp_unslash($_POST['mib-residential-park-id'])) : '';

                        $options = maybe_unserialize(get_option('mib_options'));
                        if (!is_array($options)) {
                                $options = [];
                        }

                        $options['mib-login-email'] = $email;
                        $options['mib-login-password'] = $password;
                        $options['mib-residential-park-id'] = $parkId;

                        update_option('mib_options', $options);
                        $this->mibOptions = $options;

                        $authController = new MibAuthController();
                        $response = $authController->loginToMib();

                        if (isset($response->token)) {

                                $this->mibOptions = maybe_unserialize(get_option('mib_options')) ?: [];

                                $existingDistricts = [];
                                if (isset($this->mibOptions['park_districts']) && is_array($this->mibOptions['park_districts'])) {
                                        $existingDistricts = $this->sanitizeParkDistricts($this->mibOptions['park_districts']);
                                }

                                $parkIds = array_keys($this->getParkNameMap());
                                $districts = $this->fetchParkDistrictsFromApi($parkIds, $existingDistricts);


                                $this->mibOptions['park_districts'] = $districts;
                                if (function_exists('current_time')) {
                                        $this->mibOptions['park_districts_last_updated'] = current_time('mysql');
                                }
                                $this->parkDistricts = $districts;

                                update_option('mib_options', $this->mibOptions);

                                echo "<p style='color:green'>Sikeres mentés!</p>";

                        }else{

                                $errorMessage = isset($response->error->message) ? $response->error->message : __('Ismeretlen hiba történt.', 'mib');
                                echo "<p style='color:red'>Hiba a bevitt adatokban! - {$errorMessage}</p>";

                        }
                }

                $mib_options = maybe_unserialize(get_option('mib_options'));

		?>

		<table class="options-table-responsive dt-options-table">
			<tbody>
				<tr id="dt_desc_box">

					<td class="label">
					<label for='mib-email'>Mib email:</label><br>
					</td>
					<td class="field">
                                                <input type="text" name="mib-login-email" size="45" value="<?= isset($mib_options['mib-login-email']) ? esc_html( $mib_options['mib-login-email']) : '';?>">
					</td>
				</tr>
				<tr id="dt_desc_box">
					<td class="label">
						<label for="dt_post_desc">Mib jelszó</label>
					</td>
					<td class="field">
						<input type="text" name="mib-login-password" size="45" value="<?= isset($mib_options['mib-login-password']) ? esc_html( $mib_options['mib-login-password']) : '';?>">
					</td>
				</tr>
		                <tr id="dt_desc_box">
		                    <td class="label">
		                        <label for="dt_post_desc">Residential Park ID:</label>
		                    </td>
		                    <td class="field">
		                        <input type="text" name="mib-residential-park-id" size="45" value="<?= isset($mib_options['mib-residential-park-id']) ? esc_html( $mib_options['mib-residential-park-id']) : '';?>"> <!-- Új mező input -->
		                    </td>
		                </tr>

			</tbody>
		</table>

		<?php

	}


	public function optionCrossSell( $args ){

		if ( isset($_POST['option_page']) && $_POST['option_page'] == 'mib_cross_sell' ) {

			$datas = serialize(
				[
					'mib-cross-floor' => (isset($_POST['mib-cross-floor'] ) && $_POST['mib-cross-floor'] == 'on') ? true : false, 
					'mib-cross-room' => (isset($_POST['mib-cross-room'] ) && $_POST['mib-cross-room'] == 'on') ? true : false,
					'mib-cross-orientation' => (isset($_POST['mib-cross-orientation'] ) && $_POST['mib-cross-orientation'] == 'on') ? true : false,
					'mib-cross-square-meter' => (isset($_POST['mib-cross-square-meter'] ) && $_POST['mib-cross-square-meter'] == 'on') ? true : false,
					'mib-cross-price' => (isset($_POST['mib-cross-price'] ) && $_POST['mib-cross-price'] == 'on') ? true : false,
					
				]
			);
			update_option('mib_cross_sell_options', $datas);
		}

		$mib_filter_options = maybe_unserialize( get_option('mib_cross_sell_options') );


		$floor_cross_checked = (isset($mib_filter_options['mib-cross-floor'])  && $mib_filter_options['mib-cross-floor'] == 1) ? 'checked' : '';
		$room_cross_checked = (isset($mib_filter_options['mib-cross-room'])  && $mib_filter_options['mib-cross-room'] == 1) ? 'checked' : '';
		$orientation_cross_checked = (isset($mib_filter_options['mib-cross-orientation'])  && $mib_filter_options['mib-cross-orientation'] == 1) ? 'checked' : '';
		$square_meter_cross_checked = (isset($mib_filter_options['mib-cross-square-meter'])  && $mib_filter_options['mib-cross-square-meter'] == 1) ? 'checked' : '';
		$price_cross_checked = (isset($mib_filter_options['mib-cross-price'])  && $mib_filter_options['mib-cross-price'] == 1) ? 'checked' : '';


		?>

		<div class="row" style="margin-bottom: 10px;">
			Emelet <input type="checkbox" name="mib-cross-floor" size="45" <?=$floor_cross_checked;?> >
		</div>
		
		<div class="row" style="margin-bottom: 10px;">
			Szobák <input type="checkbox" name="mib-cross-room" size="45" <?=$room_cross_checked;?>>
		</div>
		
		<div class="row" style="margin-bottom: 10px;">
			Tájolás <input type="checkbox" name="mib-cross-orientation" size="45" <?=$orientation_cross_checked;?>>
		</div>

		<div class="row" style="margin-bottom: 10px;">
			m2: <input type="checkbox" name="mib-cross-square-meter" size="45" <?=$square_meter_cross_checked;?>>
		</div>

		<div class="row" style="margin-bottom: 10px;">
			Ár: <input type="checkbox" name="mib-cross-price" size="45" <?=$price_cross_checked;?>>
		</div>
		<?php

	}
	//shortcode beállítások
	public function shortcodeOptions()
	{
	    // Shortcode mentése (POST submit)
	    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['shortcode_name'])) {

	        $shortcodes = maybe_unserialize(get_option('mib_custom_shortcodes')) ?: [];

	        $shortcode_name = sanitize_text_field($_POST['shortcode_name']);
	        $residential_park_ids = isset($_POST['residential_park_ids'])
		    ? array_filter(array_map('intval', explode(',', $_POST['residential_park_ids'])))
		    : [];

		    $apartment_skus = isset($_POST['apartment_skus'])
		    ? array_filter(array_map('sanitize_text_field', explode(',', $_POST['apartment_skus'])))
		    : [];
	        $filters = isset($_POST['filters']) ? array_map('sanitize_text_field', $_POST['filters']) : [];

                $favorites_filter = isset($_POST['favorites_filter']) ? array_map('sanitize_text_field', $_POST['favorites_filter']) : [];

                $number_of_apartment = isset($_POST['number_of_apartment']) ? $_POST['number_of_apartment'] : [];

                $type = isset($_POST['type']) ? sanitize_text_field($_POST['type']) : '';

	        // Intervallum értékek begyűjtése
	        $ranges = [];
	        foreach (['floor', 'room', 'area', 'price'] as $key) {
	            $min = isset($_POST[$key . '_min']) ? sanitize_text_field($_POST[$key . '_min']) : null;
	            $max = isset($_POST[$key . '_max']) ? sanitize_text_field($_POST[$key . '_max']) : null;
	            if ($min !== null || $max !== null) {
	                $ranges[$key] = ['min' => $min, 'max' => $max];
	            }
	        }

	        // Extra beállítások
	        $extras = isset($_POST['extras']) ? array_map('sanitize_text_field', $_POST['extras']) : [];

	        if ($shortcode_name) {
                    $shortcodes[$shortcode_name] = [
                        'residential_park_ids' => $residential_park_ids,
                        'apartment_skus' => $apartment_skus,
                        'number_of_apartment' => $number_of_apartment,
                        'type' => $type,
                        'filters' => $filters,
                        'ranges' => $ranges,
                        'extras' => $extras,
                        'created_at' => current_time('mysql'),
                    ];
	            update_option('mib_custom_shortcodes', serialize($shortcodes));
	            echo '<div class="notice notice-success is-dismissible"><p>Shortcode mentve: <code>[' . esc_html($shortcode_name) . ']</code></p></div>';
	        }
	    }

	    ?>
	    <form method="post" id="shortcode-form">
	        <p>
	            <label for="shortcode_name">Shortcode neve (pl. <code>mib_lakasok1</code>):</label><br>
	            <input type="text" name="shortcode_name" id="shortcode_name" required />
	        </p>
	        <p>
			    <label>Residential Park ID hozzáadása:</label><br>
			    <input type="number" id="residential_park_input" placeholder="pl. 7" style="width: 120px;" />
			    <button type="button" id="add_residential_park_id" class="button">Hozzáadás</button>
			</p>
			<div id="residential_park_tags"></div>
			<input type="hidden" name="residential_park_ids" id="residential_park_ids_hidden" />

			<p>
			    <label>Egy oldalon megjelenő lakások száma:</label><br>
			    <input type="number" name="number_of_apartment" id="number_of_apartment" placeholder="pl. 9" />
			</p>
			<p>
			    <label>Lakás cikkszám(ok) hozzáadása:</label><br>
			    <input type="text" id="apartment_sku_input" placeholder="pl. ABC123" style="width: 160px;" />
			    <button type="button" id="add_apartment_sku" class="button">Hozzáadás</button>
			</p>
                        <div id="apartment_skus_tags"></div>
                        <input type="hidden" name="apartment_skus" id="apartment_skus_hidden" />
                        <p>
                            <label for="shortcode_type">Típus:</label><br>
                            <select name="type" id="shortcode_type">
                                <option value="">-- Minden --</option>
                                <?php foreach ($this->getTypes() as $type) { ?>
                                    <option value="<?= esc_attr($type); ?>"><?= esc_html($type); ?></option>
                                <?php } ?>
                            </select>
                        </p>
                <p>
                    <label for="filters">Választható szűrők:</label><br>
	            <?php
	            $available_filters = [
	                'floor' => 'Emelet',
	                'room' => 'Szoba',
	                'area' => 'Alapterület (m²)',
	                'price' => 'Ár (Ft)'
	            ];
	            foreach ($available_filters as $key => $label) {
	                echo "<label><input type='checkbox' name='filters[]' value='{$key}' class='toggle-filter' data-target='{$key}-inputs'> {$label}</label><br>";
	                echo "<div id='{$key}-inputs' class='filter-extra-inputs' style='margin-left:20px;display:none'>";
	                echo "<input type='number' name='{$key}_min' placeholder='{$label} minimum' style='width:120px'/> ";
	                echo "<input type='number' name='{$key}_max' placeholder='{$label} maximum' style='width:120px'/>";
	                echo "</div>";
	            }

	            // Extra opciók
                    $extra_options = [
                        'favorites_filter' => 'Kedvencek szűrése',
                        'reset_filters' => 'Szűrők törlése',
                        'use_number_inputs' => 'Slider helyett szám inputok',
                        'load_more' => 'Paginate helyett Load more',
                        'infinite_scroll' => 'Végtelen görgetés',
	                'available_only' => 'Elérhetőség',
                        'hide_unavailable' => 'Nem elérhetők elrejtése alapbeállítás',
                        'orientation_filters' => 'Tájolás szűrés',
                        'district_filter' => 'Kerület szűrés',
                        'display_logo' => 'Logo megjelenítés',
                        'dark_logo' => 'Sötét logó',
                        'display_address' => 'Helység megjelenítés',
                        'display_supported_price' => 'Támogatással elérhető ár megjelenítése',
                        'garden_connection_filter' => 'Kertkapcsolat szűrés',
                        'staircase_filter' => 'Lépcsőház szűrés',
                        'sort_filter' => 'Rendezés',
                        'gallery_first_image' => 'Lakás galéria első képének megjelenítése',
                        'carousel_display' => 'Carousel megjelenítés',
                    ];
	            echo "<hr><strong>További beállítások:</strong><br>";
	            foreach ($extra_options as $key => $label) {
	                echo "<label><input type='checkbox' name='extras[]' value='{$key}'> {$label}</label><br>";
	            }
	            ?>
	        </p>
	        <p>
	            <button type="submit" class="button button-primary">Mentés</button>
	            <button type="reset" id="reset" class="button">Űrlap ürítése</button>
	        </p>
	    </form>

	    <hr>
	    <h3>Elérhető shortcode-ok</h3>
	    <?php
	    $shortcodes = maybe_unserialize(get_option('mib_custom_shortcodes')) ?: [];
	    if (!empty($shortcodes)) {
	        echo '<table class="shortcode-table widefat fixed striped">';
	        echo '<thead><tr>';
	        echo '<th>Shortcode</th>';
	        echo '<th>Residential Park ID</th>';
                echo '<th>Lakás cikkszámok</th>';
                echo '<th>Típus</th>';
                echo '<th>Szűrők</th>';
	        echo '<th>Extra opciók</th>';
	        echo '<th>Lakások száma egy oldalon</th>';
	        echo '<th>Műveletek</th>';
	        echo '</tr></thead>';
	        echo '<tbody>';
	        foreach ($shortcodes as $name => $config) {
	            echo '<tr>';
	            echo '<td><code>[' . esc_html($name) . ']</code></td>';
	            echo '<td>' . (!empty($config['residential_park_ids']) ? implode(', ', array_map('esc_html', $config['residential_park_ids'])) : '-') . '</td>';
                    echo '<td>' . (!empty($config['apartment_skus']) ? implode(', ', array_map('esc_html', $config['apartment_skus'])) : '-') . '</td>';
                    echo '<td>' . (!empty($config['type']) ? esc_html($config['type']) : '-') . '</td>';
                    echo '<td>' . (!empty($config['filters']) ? implode(', ', array_map('esc_html', $config['filters'])) : '-') . '</td>';
	            echo '<td>' . (!empty($config['extras']) ? implode(', ', array_map('esc_html', $config['extras'])) : '-') . '</td>';
	            echo '<td>' . (!empty($config['number_of_apartment']) ? $config['number_of_apartment'] : '-') . '</td>';
	            echo '<td>';
	            echo '<a href="#" class="edit-shortcode" data-shortcode="' . esc_attr($name) . '">Szerkesztés</a> | ';
	            echo '<a href="#" class="delete-shortcode" data-shortcode="' . esc_attr($name) . '">Törlés</a>';
	            echo '</td>';
	            echo '</tr>';
	        }
	        echo '</tbody>';
	        echo '</table>';
	    } else {
	        echo '<p>Nincsenek mentett shortcode-ok.</p>';
	    }
	}

	public function colorOptions(){

		
		if ( isset($_POST['option_page']) && $_POST['option_page'] == 'mib_colors' ) {


			$datas = serialize(
				[
					'mib-first-color' => (isset($_POST['mib-first-color']) ) ? $_POST['mib-first-color'] : '', 
					'mib-second-color' => (isset($_POST['mib-second-color']) ) ? $_POST['mib-second-color'] : '', 
					'mib-third-color' => (isset($_POST['mib-third-color']) ) ? $_POST['mib-third-color'] : '',
                    'mib-apartment-color' => (isset($_POST['mib-apartment-color']) ) ? $_POST['mib-apartment-color'] : '',
                    'mib-apartment-table-color' => (isset($_POST['mib-apartment-table-color']) ) ? $_POST['mib-apartment-table-color'] : '',
                    'mib-apartment-slider-active-color' => (isset($_POST['mib-apartment-slider-active-color']) ) ? $_POST['mib-apartment-slider-active-color'] : '',
                    'mib-apartment-slider-inactive-color' => (isset($_POST['mib-apartment-slider-inactive-color']) ) ? $_POST['mib-apartment-slider-inactive-color'] : '',
                ]
			);
			update_option('mib_color_settings', $datas);

		}

		$mib_color_options = maybe_unserialize( get_option('mib_color_settings') );

		?>
		<tr id="dt_desc_box">

			<td class="label">
				<label for='api-key'>Elsődleges szín</label><br>
			</td>
			<td class="field">
				<input type="color" name="mib-first-color" size="45" value="<?= isset($mib_color_options['mib-first-color']) ? esc_html( $mib_color_options['mib-first-color']) : '';?>" >
			</td>
		</tr>

		<tr id="dt_desc_box">

			<td class="label">
				<label for='api-key'>Másodlagos szín</label><br>
			</td>
			<td class="field">
				<input type="color" name="mib-second-color" size="45" value="<?= isset($mib_color_options['mib-second-color']) ? esc_html( $mib_color_options['mib-second-color']) : '';?>" >
			</td>
		</tr>

		<tr id="dt_desc_box">

			<td class="label">
				<label for='api-key'>Harmadlagos szín</label><br>
			</td>
			<td class="field">
				<input type="color" name="mib-third-color" size="45" value="<?= isset($mib_color_options['mib-third-color']) ? esc_html( $mib_color_options['mib-third-color']) : '';?>" >
			</td>
		</tr>

		<tr id="dt_desc_box">

            <td class="label">
                <label for="api-key">Csúszka aktív háttér szín</label><br>
            </td>
            <td class="field">
                <input type="color" name="mib-apartment-slider-active-color" size="45" value="<?= isset($mib_color_options['mib-apartment-slider-active-color']) ? esc_html( $mib_color_options['mib-apartment-slider-active-color']) : '';?>">
            </td>
        </tr>
        
		<tr id="dt_desc_box">

            <td class="label">
                <label for="api-key">Csúszka inaktív háttér szín </label><br>
            </td>
            <td class="field">
                <input type="color" name="mib-apartment-slider-inactive-color" size="45" value="<?= isset($mib_color_options['mib-apartment-slider-inactive-color']) ? esc_html( $mib_color_options['mib-apartment-slider-inactive-color']) : '';?>">
            </td>
        </tr>

        <tr id="dt_desc_box">

            <td class="label">
                <label for="api-key">Egy lakás szövegének színe</label><br>
            </td>
            <td class="field">
                <input type="color" name="mib-apartment-color" size="45" value="<?= isset($mib_color_options['mib-apartment-color']) ? esc_html( $mib_color_options['mib-apartment-color']) : '';?>">
            </td>
        </tr>

        <tr id="dt_desc_box">

            <td class="label">
                <label for="api-key">Egy lakás táblázat szövegének színe</label><br>
            </td>
            <td class="field">
                <input type="color" name="mib-apartment-table-color" size="45" value="<?= isset($mib_color_options['mib-apartment-table-color']) ? esc_html( $mib_color_options['mib-apartment-table-color']) : '';?>">
            </td>
        </tr>

		<?php

	}
	

}
