<?php

/**
 * BaseController
 */

namespace Inc\Base;


class MibBaseController
{

	public $pluginPath = '';

	public $pluginName = '';

	public $pluginUrl = '';

	public $loginUrl = 'https://ugyfel.mibportal.hu:3000/auth/login';

	public $filterApartmentsUrl = 'https://ugyfel.mibportal.hu:3000/apartments';

	private $orientation = [
		'Északi' => 'north', 
		'Északkeleti' => 'northEast', 
		'Keleti' => 'east', 
		'Délkeleti' => 'southEast', 
		'Déli' => 'south',
		'Délnyugati' => 'southWest', 
		'Nyugati' => 'west' ,
		'Északnyugati' => 'northWest'
	];

	private $orientationShortName = [
		'Északi' => 'É', 
		'Északkeleti' => 'ÉK', 
		'Keleti' => 'K', 
		'Délkeleti' => 'DK', 
		'Déli' => 'D',
		'Délnyugati' => 'DNY', 
		'Nyugati' => 'W' ,
		'Északnyugati' => 'ÉNY'
	];

	private $availability = [
		'Nem elérhetőek elrejtése' => 'Available', 
	];

	private $gardenConnection = [
		'Igen' => 1, 
	];

	private $stairWay = [
		'A' => 'A', 
		'B' => 'B', 
		'C' => 'C', 
		'D' => 'D'
	];

	public $numberOfApartmens = 9;

	public $filterOptionDatas = [];

	public $mibOptions = [];

	public $filterOptionCrossSellDatas = [];

	public $residentialParkId = 12;

	public $shortcodesOptions = [];

	public $selectedShortcodeOption = [];

	public $selectedApartmanNames = [];

	public $shortCodeApartmanName = '';

    public function __construct() {

        $this->pluginPath = plugin_dir_path(dirname(__FILE__, 2));
        $this->pluginUrl = plugin_dir_url(dirname(__FILE__, 2));
        $this->pluginName = plugin_basename(dirname(__FILE__, 3)) . "/mib.php";
        $this->filterOptionDatas = maybe_unserialize(get_option('mib_filter_options'));

        // Adminon beállított residentialParkId
        $this->mibOptions = maybe_unserialize(get_option('mib_options'));
        $this->residentialParkId = isset($this->mibOptions['mib-residential-park-id']) ? $this->mibOptions['mib-residential-park-id'] : null;

        // Ha az admin felületen van beállítva ID, azt használjuk, ha nem akkor mylelle most
        $this->shortcodesOptions = maybe_unserialize(get_option('mib_custom_shortcodes'));
    }

    public function activated( string $key )
	{
		$option = get_option( $key );
		return isset( $option ) ? $option : false;
	}

	public function get_shortcode_config_by_name($shortcode_name) {
	    if (empty($shortcode_name)) {
	        return null;
	    }

	    // Teljes beállítások lekérdezése
	    $shortcodes = maybe_unserialize(get_option('mib_custom_shortcodes')) ?: [];

	    // Shortcode név alapján visszaadjuk a beállítást (ha létezik)
	    return $shortcodes[$shortcode_name] ?? null;
	}

	public function setDataToTable($datas)
	{

		$table_data = [];
        foreach ($datas['data'] as $item) {

		    /*if (isset($item->apartmentsDocuments)) {
		        $filteredPdfDocuments = array_values(array_filter($item->apartmentsDocuments, fn($doc) => $doc->extension === 'pdf'));
		        $filteredPngDocuments = array_values(array_filter($item->apartmentsDocuments, fn($doc) => $doc->extension === 'png'));
		    }

		    $pdfDocument = (!empty($filteredPdfDocuments) && isset($filteredPdfDocuments[0]->preview)) ? $filteredPdfDocuments[0]->preview : '';
		    $pngDocument = (!empty($filteredPngDocuments) && isset($filteredPngDocuments[0]->preview)) ? $filteredPngDocuments[0]->preview : '';*/

		    // Lekérjük az adatokat a get_attachments_by_meta_values függvényből
		    $attachments = $this->get_attachments_by_meta_values($item->name);

		    // Alapértelmezett értékek
		    //$alaprajz = (!empty($pdfDocument)) ? '<a href="'.$pdfDocument.'" target="_blank" rel="noopener">Alaprajz megtekintése</a>' : '';
		    //$szintrajz = (!empty($pngDocument)) ? '<a href="'.$pngDocument.'" target="_blank" rel="noopener">Szintrajz megtekintése</a>' : '';
		    
		    $image = '';
		    $szintrajz = '';
		    $alaprajz = '';
			if (isset($item->apartmentsImages) && !empty($item->apartmentsImages)) {
			    foreach ($item->apartmentsImages as $img) {
			        if (isset($img->category) && $img->category === 'Gallery' && isset($img->src)) {
			            $image = $img->src;
			            //break;
			        }
			        if (isset($img->category) && $img->category === 'Synopsis' && isset($img->src)) {
			            $szintrajz = '<a href="'.$img->src.'" target="_blank" rel="noopener">Szintrajz megtekintése</a>';
			            //break;
			        }
			    }

			    foreach ($item->apartmentsDocuments as $img) {

			    	if (isset($img->category) && $img->category === 'Floorplan' && isset($img->src)) {
			            $alaprajz = '<a href="'.$img->src.'" target="_blank" rel="noopener">Alaprajz megtekintése</a>';
			            break;
			        }
			    }
			    
			}

		    // Végigmegyünk az adatbázisból lekért csatolmányokon és frissítjük a megfelelő értékeket
		    if (!empty($attachments)) {
		        foreach ($attachments as $attachment) {
		            if ($attachment['type'] === 'alaprajz') {
		                $alaprajz = '<a href="'.$attachment['attachment_url'].'" target="_blank" rel="noopener">Alaprajz megtekintése</a>';
		            }
		            if ($attachment['type'] === 'szintrajz') {
		                $szintrajz = '<a href="'.$attachment['attachment_url'].'" target="_blank" rel="noopener">Szintrajz megtekintése</a>';
		            }
		            if ($attachment['type'] === 'lakas_kep') {
		                $image = $attachment['attachment_url'];
		            }
		        }
		    }

		    $host = parse_url(home_url(), PHP_URL_HOST);
			$parts = explode('.', $host);
			$projectSlug = (count($parts) >= 2) ? $parts[count($parts) - 2] : 'projekt';

		    $table_data[] = array(
		        'id' => $item->id,
		        'rawname' => $item->name,
		        'name' => ($item->status == 'Sold') 
				    ? '<a id="mibhre">' . esc_html($item->name) . '</a>' 
				    : '<a id="mibhre" href="' . home_url('/lakas/' . $projectSlug . '/' . sanitize_title($item->name) . '/') . '">' . $item->name . '</a>',
				'url' => home_url('/lakas/' . $projectSlug . '/' . sanitize_title($item->name) . '/'),
		        'numberOfRooms' => $item->numberOfRooms,
		        'price' => ($item->status == 'Available' || $item->status == 'Reserved') ? (!is_null($item->price) ? number_format($item->price, 0) . ' Ft' : '') : '',
		        'bruttoFloorArea' => $item->bruttoFloorArea . ' m²',
		        'floor' => ($item->floor == 0) ? 'földszint' : $item->floor,
		        'balcony' => $item->balconyFloorArea . ' m²',
		        'orientation' => array_search($item->orientation, $this->orientation), // Tájolás formázása
		        'view' => $item->view, // Kilátás formázása
		        'airConditioning' => $item->airConditioning ? 'Igen' : 'Nem', // Légkondi állapotának formázása
                'bathroomToiletSeparation' => $item->bathroomToiletSeparation, // Légkondi 
                'status' => ($item->status == 'Available' || $item->status == 'Reserved')
				    ? '<a id="mibhre" href="' . home_url('/lakas/' . $projectSlug . '/' . sanitize_title($item->name) . '/') . '">Megnézem</a>'
				    : '<a id="mibhrefinactive" href="#">- Nem elérhető</a>',
                'statusrow' => ($item->status == 'Available' || $item->status == 'Reserved') ? 'Elérhető' : 'Nem elérhető',
                'statusclass' => ($item->status == 'Available' || $item->status == 'Reserved') ? 'text-success' : 'text-info',
		        'image' => $image, // Frissített kép
		        'alaprajz' => $alaprajz, // Frissített alaprajz
		        'szintrajz' => $szintrajz, // Frissített szintrajz
		        'notes' => ($item->residentialPark->notes) ? $item->residentialPark->notes : '',
		        'logo' => ($item->residentialPark->logo) ? $item->residentialPark->logo : '',
                'address' => ($item->residentialPark->address) ? $item->residentialPark->address : '',
                'rooms' => isset($item->rooms) && is_array($item->rooms) ? array_map(function($room){
                            return [
                                'category_name' => $room->category_name ?? '',
                                'floorArea' => $room->floorArea ?? ''
                            ];
                }, $item->rooms) : [],
                    );
                }

        return $table_data;
	}

	public function get_attachments_by_meta_values($identifier) {
	    global $wpdb;

	    // Lekérdezzük azokat az attachment ID-ket, amelyek megfelelnek az identifier és type feltételeknek
	    $query = $wpdb->prepare("
	        SELECT pm1.post_id 
	        FROM {$wpdb->postmeta} pm1
	        INNER JOIN {$wpdb->postmeta} pm2 ON pm1.post_id = pm2.post_id
	        WHERE pm1.meta_key = 'identifier' 
	        AND pm1.meta_value = %s
	        AND pm2.meta_key = 'type'
	        AND pm2.meta_value IN ('szintrajz', 'alaprajz', 'lakas_kep')
	    ", $identifier);

	    $post_ids = $wpdb->get_col($query);

	    if (empty($post_ids)) {
	        return [];
	    }

	    // Most lekérjük az attachment-ek URL-jeit
	    $attachments = [];

	    foreach ($post_ids as $post_id) {
	        $attachments[] = [
	            'attachment_id'  => $post_id,
	            'attachment_url' => wp_get_attachment_url($post_id),
	            'type'           => get_post_meta($post_id, 'type', true),
	            'identifier'     => get_post_meta($post_id, 'identifier', true),
	            'property_id'    => get_post_meta($post_id, 'property_id', true),
	        ];
	    }

	    return $attachments;
	}

	public function get_post_id_by_property_id($property_id) {

	    global $wpdb;

	    $post_id = $wpdb->get_var($wpdb->prepare(
	        "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'property_id' AND meta_value = %s ORDER BY meta_id DESC LIMIT 1",
	        $property_id
	    ));

	    return $post_id ? intval($post_id) : null;
	}

	public function getSingleApartmanHtml($data, $recommend = []) {

	    $html = '';

	    if (!empty($data)) {
	        $data = $data[0];

	        $cleanPrice = preg_replace('/[^0-9]/', '', $data['price']);
	        $formattedPrice = number_format((int)$cleanPrice, 0, ',', ' ') . ' Ft';

	        $separationLabels = [
	            'separate' => 'Különálló',
	            'together' => 'Együtt',
	            'separateAndTogether' => 'Együtt és különálló',
	        ];

	        $logo = '';
			if (isset($this->filterOptionDatas['mib-display_logo']) && $this->filterOptionDatas['mib-display_logo'] == 1 && !empty($data['logo'])) {
				$logo = '<img src="'.$data['logo'].'" crossorigin="anonymous">';
			}

			$address = '';
			if (isset($this->filterOptionDatas['mib-display_address']) && $this->filterOptionDatas['mib-display_address'] == 1 ) {
				$address = $data['address'];
			}

	        $html .= '<div class="apartment-box">';

	        // Felső tartalom: alaprajz + jobb oldali adatok
	        $html .= '<div class="apartment-top primary-color">';

	            // Bal oldal: alaprajz
	            $html .= '<div class="apartment-plan">';
	            $html .= '<img crossorigin="anonymous" src="' . esc_url($data['image']) . '" alt="Lakás alaprajz">';
	            $html .= '</div>';

	            // Jobb oldal
	            $html .= '<div class="apartment-details">';

	                // Logó és helyiség
	                $html .= '<div class="apartment-logo-header">';
	                if (!empty($logo)) {
	                    $html .= '<div class="park-logo"><img src="' . esc_url($data['logo']) . '" alt="Logó" crossorigin="anonymous"></div>';
	                }

	                if (!empty($address)) {
	                	$html .= '<div class="location-name">' . esc_html($data['address'] ?? '—') . '</div>';
	                }
	                
	                $html .= '</div>';

	                // Adatok sötét háttéren
	                $html .= '<div class="apartment-info-box">';
	                $html .= '<div class="apartment-quickinfo responsive-grid">';
	                $html .= '<div><strong class="third-text-color">Elérhető</strong><br><span class="quick-info-value">' . esc_html($data['rawname'] ?? '—') . '</span></div>';
	                $html .= '<div><strong>Alapterület (m²)</strong><br><span class="quick-info-value">' . esc_html($data['bruttoFloorArea']) . '</span></div>';
	                $html .= '<div><strong>Szobák száma</strong><br><span class="quick-info-value">' . esc_html($data['numberOfRooms']) . '</span></div>';
	                $html .= '<div><strong>Emelet</strong><br><span class="quick-info-value">' . esc_html($data['floor']) . '</span></div>';
	                $html .= '</div>';

	                $html .= '<hr class="apartment-divider">';

	                $html .= '<div class="apartment-extra">';
	                $html .= '<p><strong>Tájolás:</strong> ' . esc_html($data['orientation']) . '</p>';
	                $html .= '<p><strong>Lakáshűtés:</strong> ' . esc_html($data['airConditioning']) . '</p>';
	                $html .= '<p><strong>Fürdő és WC:</strong> ' . esc_html($separationLabels[$data['bathroomToiletSeparation']] ?? '—') . '</p>';
	                $html .= '</div>';

	                $html .= '<hr class="apartment-divider">';

	                $html .= '<div class="apartment-price third-text-color">' . $formattedPrice . '</div>';
	                $html .= '</div>'; // .apartment-info-box

	            $html .= '</div>'; // .apartment-details

	        $html .= '</div>'; // .apartment-top

	        // Letöltések és infók
	        $html .= '<div class="apartment-downloads">';
	        $html .= '<div class="downloads-column">';
	        $html .= '<h4>Letölthető dokumentumok</h4>';
	        if (!empty($data['alaprajz'])) {
	            $html .= '<div class="apartment-plan-documents">' . $data['alaprajz'] . '</div>';
	        }
	        if (!empty($data['szintrajz'])) {
	            $html .= '<div class="apartment-plan-documents">' . $data['szintrajz'] . '</div>';
	        }
	        $html .= '</div>';

	        $html .= '<div class="info-column">';
	        $html .= '<h4>Egyéb információk</h4>';
                $html .= '<div class="notes">' . $data['notes'] . '</div>';
                if (!empty($data['rooms'])) {
                    $html .= '<ul class="room-list">';
                    foreach ($data['rooms'] as $room) {
                        $html .= '<li><b>' . esc_html($room['category_name']) . '</b>: ' . esc_html($room['floorArea']) . ' m²</li>';
                    }
                    $html .= '</ul>';
                }
                $html .= '</div>';
                $html .= '</div>'; // .apartment-downloads

	        // Ajánlott
	        $html .= $this->getRecommendedApartmentsHtml($recommend);

	        $html .= '</div>'; // .apartment-box

	    } else {
	        $html .= '<p>Nem található lakás ezzel az ID-vel</p>';
	    }

	    return $html;
	}

	public function getRecommendedApartmentsHtml($recommended) {

		$html = '';
		if (!empty($recommended)) {
		
		    $html = '<div><h2 id="recommended-h4">Ajánlott ingatlanok</h2></div><div class="recommended-apartments">';
		    foreach ($recommended as $apartment) {

		        $html .= '<div class="recommended-apartment">';
		        $html .= '<a href="/lakas/?id='.$apartment['id'].'">';
		        $html .= '<img crossorigin="anonymous" src="' . $apartment['image'] . '" alt="' . esc_attr($apartment['name']) . '" class="recommended-image">';
		        $html .= '<h3 class="recommended-title">' . esc_html($apartment['name']) . '</h3>';
		        $html .= '<p class="recommended-price">Ár: ' . esc_html($apartment['price']) . '</p>';
		        $html .= '</div>';
		        $html .= '</a>';
		    }
		    $html .= '</div></a>';
	    }

	    return $html;
	}

	public function getTableHtml($datas, $totalItems, $currentPage, $filterType = [])
    {
    		$html = '';
	        $html = '<div id="custom-list-table-container">';

	        $html .= '<div id="mib-spinner" class="mib-spinner spinner-border text-dark m-3" role="status">
					  <span class="visually-hidden">Töltés...</span>
					</div>';

	        $html .= '<div class="custom-filter-container">

				<div class="d-flex justify-content-end gap-1">';

					//$html .= $this->getSearch();
					//var_dump($this->filterOptionDatas['mib-filter-floor']);
					if (!empty($this->filterOptionDatas)) {
						
						if (isset($this->filterOptionDatas['mib-filter-floor']) && $this->filterOptionDatas['mib-filter-floor'] == true ) {
							$html .= $this->getFilterFloor($filterType);
						}

						if (isset($this->filterOptionDatas['mib-filter-room']) && $this->filterOptionDatas['mib-filter-room'] == true ) {
							$html .= $this->getFilterRoom($filterType);
						}

						if (isset($this->filterOptionDatas['mib-filter-orientation']) && $this->filterOptionDatas['mib-filter-orientation'] == true ) {
							$html .= $this->getFilterOrientation($filterType);
						}

						if (isset($this->filterOptionDatas['mib-filter-availability']) && $this->filterOptionDatas['mib-filter-availability'] == true ) {
							$html .= $this->getFilterAvailability($filterType);
						}

						if (isset($this->filterOptionDatas['mib-filter-square-meter']) && $this->filterOptionDatas['mib-filter-square-meter'] == true) {
							$html .= $this->squareFilters($filterType);
						}

						if (isset($this->filterOptionDatas['mib-filter-price_range']) && $this->filterOptionDatas['mib-filter-price_range'] == true) {

							$html .= $this->priceFilters($filterType);
						}

						if (isset($this->filterOptionDatas['mib-filter-deletefilters']) && $this->filterOptionDatas['mib-filter-deletefilters'] == true ) {
							$html .= $this->deleteFilters($filterType);
						}
					}

				$html .= '</div>

		    </div>';

	        //$html .= $this->getFilterFloor($filterType);
	        // Start building the HTML for the table.
        // Display total count returned by API
        $html .= '<p id="mib-total-count" class="mb-2">' . sprintf( __( 'Találatok száma: %d', 'mib' ), $totalItems ) . '</p>';
        // Sorting options (AJAX)
        $sort_labels = [
            'name' => __( 'Név', 'mib' ),
            'price' => __( 'Ár', 'mib' ),
            'bruttoFloorArea' => __( 'Alapterület', 'mib' ),
        ];
        // Determine current sort from AJAX parameters
        $current_sort = $filterType['sort'] ?? '';
        $current_type = isset($filterType['sortType']) ? strtoupper($filterType['sortType']) : '';
        $html .= '<div class="mb-3 d-flex align-items-center">';
        $html .= '<label for="mib-sort-select" class="me-2">' . __( 'Rendezés:', 'mib' ) . '</label>';
        $html .= '<select id="mib-sort-select" class="form-select form-select-sm" style="width:auto;">';
        $html .= '<option value="">' . __( '-- Rendezés --', 'mib' ) . '</option>';
        foreach ($sort_labels as $key => $label) {
            $sel_asc  = ($current_sort === $key && $current_type === 'ASC')  ? ' selected' : '';
            $sel_desc = ($current_sort === $key && $current_type === 'DESC') ? ' selected' : '';
            $html  .= '<option value="' . esc_attr("{$key}|ASC") . '"' . $sel_asc . '>'  . esc_html($label) . ' ↑</option>';
            $html  .= '<option value="' . esc_attr("{$key}|DESC") . '"' . $sel_desc . '>' . esc_html($label) . ' ↓</option>';
        }
        $html .= '</select>';
        $html .= '</div>';
        $html .= '<table class="custom-list-table">';
	        $html .= '<thead>';
	        $html .= '<tr id="mibheadertr" class="secondary-color">';
	        $html .= '<th id="mibheaderth" class="secondary-color">' . __('Lakás', 'mib') . '</th>';
	        $html .= '<th id="mibheaderth" class="secondary-color">' . __('Alapterület', 'mib') . '</th>';
	        $html .= '<th id="mibheaderth" class="secondary-color">' . __('Szobák száma', 'mib') . '</th>';
	        $html .= '<th id="mibheaderth" class="secondary-color">' . __('Emelet', 'mib') . '</th>';
	        $html .= '<th id="mibheaderth" class="secondary-color">' . __('Tájolás', 'mib') . '</th>';
	        $html .= '<th id="mibheaderth" class="secondary-color">' . __('Ár', 'mib') . '</th>';
	        $html .= '<th id="mibheaderth" class="secondary-color">' . __('Státusz', 'mib') . '</th>';
	        $html .= '</tr>';
	        $html .= '</thead>';
	        $html .= '<tbody>';


	        if (!empty($datas)) {

		        foreach ($datas as $data) {

		            $html .= '<tr id="mibtr">';
			            $html .= '<td id="mibtd">' . $data['name'].'</td>';
			            $html .= '<td id="mibtd">' . esc_html($data['bruttoFloorArea']) . '</td>';
			            $html .= '<td id="mibtd">' . esc_html($data['numberOfRooms']) . '</td>';
			            $html .= '<td id="mibtd">' . esc_html($data['floor']) . '</td>';
			            $html .= '<td id="mibtd">' . esc_html($data['orientation']) . '</td>';
			            $html .= '<td id="mibtd">' . esc_html($data['price']) . '</td>';
			            $html .= '<td id="mibtd"> '.$data['status'].'</td>';
		            $html .= '</tr>';
		        }

	    	}else{

	    		$html .= '<tr> <td> <p><b> Nem található ingatlan </b></p> </td></tr>';

	    	}

	        $html .= '</tbody>';
	        $html .= '</table>';

	        $html .= $this->getPaginate( $currentPage, $totalItems, 50);

	        $html .= '</div>';


        return $html;

    }

    public function getCardHtmlShortCode($datas, $totalItems, $currentPage, $filterType = [], $shortcodeName = '', $apartman_number = 9){

    	//print_r($filterType);
	    $html = '<div id="custom-card-container" class="row shortcode-card" data-shortcode="'.$shortcodeName.'" data-apartman_number="'.$apartman_number.'">';

	    $html .= '<div id="mib-spinner" class="mib-spinner spinner-border text-dark m-3" role="status">
	              <span class="visually-hidden">Töltés...</span>
	            </div>';

	    $html .= '<div class="custom-filter-container">
	                <div class="d-flex">';

	                // Filterek megjelenítése
	                if (!empty($filterType)) {

	                	if (in_array('use_number_inputs', $filterType['extras'])) {
	                		echo '';
	                	}else{

	                		if (in_array('price', $filterType['filters'])) {
		                        $html .= $this->getFilterPriceShortCodeByCatalog($filterType['ranges']['price']['min'], $filterType['ranges']['price']['max']);
		                    }

		                	if (in_array('floor', $filterType['filters'])) {

							    $html .= $this->getFilterFloorShortCodeByCatalog($filterType['ranges']['floor']['min'], $filterType['ranges']['floor']['max']);
							}

		                    if (in_array('room', $filterType['filters'])) {
		                        $html .= $this->getFilterRoomShortCodeByCatalog($filterType['ranges']['room']['min'], $filterType['ranges']['room']['max']);
		                    }

		                    if (in_array('area', $filterType['filters'])) {

		                        $html .= $this->getFilterAreaShortCodeByCatalog($filterType['ranges']['area']['min'], $filterType['ranges']['area']['max']);
		                    }

		                    if (count($filterType['residential_park_ids'])>1) {
		                    	$html .= $this->getFilterResidentalParksShortCodeByCatalog($filterType['residential_park_ids']);
		                    }

	                	}
	                }

	    			$html .= '</div>';

	    $html .= '</div>';


	    $html .= '<div class="custom-filter-container">';
		$html .= '<div class="mb-2">';
		$html .= '<button type="button" class="btn btn-outline-secondary btn-sm" id="toggle-advanced-filters">';
		$html .= '<i class="fas fa-sliders-h me-1"></i> További szűrők';
		$html .= '</button>';
		$html .= '</div>';

		$html .= '<div id="advanced-filters" class="flex-wrap" style="display:none;">';

			// Tájolás szűrő
			if (in_array('orientation_filters', $filterType['extras'])) {
			    $html .= $this->getFilterOrientationByCatalog($filterType);
			}

			// Elérhetőség szűrő
			if (in_array('available_only', $filterType['extras']) && !in_array('hide_unavailable', $filterType['extras'])) {
			    $html .= $this->getFilterAvailabilityByCatalog($filterType);
			}

			// Kertkapcsolat szűrő
			if (in_array('garden_connection_filter', $filterType['extras'])) {
			    $html .= $this->getFilterGardenConnectionByCatalog($filterType);
			}

			// Lépcsőház szűrő
			if (in_array('staircase_filter', $filterType['extras'])) {
			    $html .= $this->getFilterStairwayByCatalog($filterType);
			}

		$html .= '</div>'; // advanced-filters vége
		$html .= '</div>'; // custom-filter-container vége

		// Kedvencek gomb külön változóban marad
		$favorites = '';
		if (in_array('favorites_filter', $filterType['extras'])) {
		    $favorites = '<button id="favorite-view" class="btn btn-outline-secondary" title="Kedvencek">
		        <i class="fa fa-regular fa fa-heart"></i>
		    </button>';
		}
	    

	    if (!empty($datas)) {

	    	$resetFilters = '';//Ha beállításban ki van kapcsolva, akkor ne jelenjen meg.
	    	if ( !empty($filterType) && in_array('reset_filters', $filterType['extras'])) {

	    		$resetFilters = '<div class="reset-filters-wrapper ms-3">
			    					<button id="reset-filters-button" class="btn btn-outline-secondary d-flex align-items-center gap-2">
			    						<i class="fas fa-undo-alt"></i> Szűrők törlése
			    					</button>
			    				</div>';
	    	}

	    	$html .= '<div id="view-toggle" class="d-flex justify-content-end mb-3 gap-2">
	    				'.$resetFilters.'
					    <button id="grid-view" class="btn btn-outline-secondary active" title="Rács nézet">
					        <i class="fas fa-th-large"></i>
					    </button>
					    <button id="list-view" class="btn btn-outline-secondary" title="Lista nézet">
					        <i class="fas fa-bars"></i>
					    </button>
					    '.$favorites.'
					</div>';

			// Display total count returned by API
	        $html .= '<p id="mib-total-count" class="mb-3">' . sprintf( __( 'Találatok száma: %d', 'mib' ), $totalItems ) . '</p>';
	        // Sorting options (AJAX)
	        $sort_labels = [
	            'name' => __('Név', 'mib'),
	            'price' => __('Ár', 'mib'),
	            'bruttoFloorArea' => __('Alapterület', 'mib'),
	        ];
	        // Determine current sort settings
	        $current_sort = $filterType['sort'] ?? '';
	        $current_type = isset($filterType['sortType']) ? strtoupper($filterType['sortType']) : '';

	        if ( in_array('sort_filter', $filterType['extras']) ) {

		        $html .= '<div class="mb-3 d-flex align-items-center">';
		        $html .= '<label for="mib-sort-select" class="me-2">' . __('Rendezés:', 'mib') . '</label>';
		        $html .= '<select id="mib-sort-select" class="form-select form-select-sm" style="width:auto;">';
		        $html .= '<option value="">' . __('-- Rendezés --', 'mib') . '</option>';
		        foreach ($sort_labels as $key => $label) {
		            $sel_asc  = ($current_sort === $key && $current_type === 'ASC') ? ' selected' : '';
		            $sel_desc = ($current_sort === $key && $current_type === 'DESC')? ' selected' : '';
		            $html   .= '<option value="' . esc_attr("{$key}|ASC") . '"' . $sel_asc . '>' . esc_html($label) . ' ↑</option>';
		            $html   .= '<option value="' . esc_attr("{$key}|DESC") . '"' . $sel_desc . '>' . esc_html($label) . ' ↓</option>';
		        }
		        $html .= '</select>';
		        $html .= '</div>';

	        }

		    foreach ($datas as $data) {

		    	$logo = '';
				if (in_array('display_logo', $filterType['extras']) && !empty($data['logo'])) {
					$logo = '<img src="'.$data['logo'].'" crossorigin="anonymous">';
				}

				$address = '';
				if (in_array('display_address', $filterType['extras'])) {
					$address = $data['address'];
				}

		        $html .= '<div class="card-wrapper col-md-4 mb-3" data-id="' . esc_attr($data['id']) . '">'; // 4 oszlopos grid
		        $html .= '<div class="card h-100 position-relative">'; // A kártyát relatív pozicionáljuk
		        
		        // Kép wrapper, flexbox középre igazítással
		        $html .= '<div class="primary-color card-image-wrapper">';
		        $html .= '<img src="' . $data['image'] . '" class="card-img-top" alt="Lakás képe" crossorigin="anonymous">';
		        $html .= '</div>';

		        $html .= '<div id="apartment-card-body" class="secondary-color card-body d-flex flex-column justify-content-between text-white">';

				    // Cím és emelet
				    $html .= '<div class="mb-3">';
				        $html .= '<div class="d-flex justify-content-between">';

				        	if (!empty($logo)) {
				        		$html .= '<div>';
					                $html .= '<div class="park-logo">'.$logo.'</div>';
					                $html .= '<strong>'.$address.'</strong>';
					            $html .= '</div>';
				        	}
				        	

				            $html .= '<div>';
				                $html .= '<small class="third-text-color '.$data['statusclass'].' d-block">'.$data['statusrow'].'</small>';
				                $html .= '<strong class="fs-5">' . esc_html($data['rawname']) . '</strong>';
				            $html .= '</div>';
				        $html .= '</div>';
				        $html .= '<hr>';
				    $html .= '</div>';

				    // Szobák és alapterület
				    $html .= '<div class="mb-3">';
				        $html .= '<div class="d-flex justify-content-between">';
				        	$html .= '<div>';
				                $html .= '<small class="d-block text-muted">Szobák</small>';
				                $html .= '<strong class="fs-5">' . esc_html($data['numberOfRooms']) . '</strong>';
				            $html .= '</div>';
				            $html .= '<div>';
				                $html .= '<small class="d-block text-muted">Alapterület (m2)</small>';
				                $html .= '<strong class="fs-5">' . esc_html($data['bruttoFloorArea']) . '</strong>';
				            $html .= '</div>';
				            $html .= '<div class="text-end">';
				                $html .= '<small class="d-block text-muted">Emelet</small>';
				                $html .= '<strong class="fs-5">' . esc_html($data['floor'])  . '</strong>';
				            $html .= '</div>';
				        $html .= '</div>';
				        $html .= '<hr>';
				    $html .= '</div>';

					// Ár
					$html .= '<div class="list-view-price-container mt-2 mt-md-0" style="display: flex; align-items: center; gap: 10px;">';
						$html .= '<strong class="fs-4 text-success third-text-color">' . esc_html($data['price']) . '</strong>';
					$html .= '</div>';

					// Függőleges elválasztó (gomb elé, csak lista nézetben)
					$html .= '<div class="card-divider list-view-only"></div>';

					// Gomb
					if ($data['statusrow'] == 'Elérhető') {
						
						$html .= '<div class="list-view-button-wrapper d-flex align-items-center button-row">';
	    
						    // Szív ikon bal oldalon
						    $html .= '<i class="fa fa-regular fa-heart favorite-icon" aria-hidden="true" data-id="' . esc_attr($data['id']) . '"></i>';
						    
						    // Gomb
						    $html .= '<a id="cardhref" href="' . $data['url'] . '" class="flex-grow-1">';
						        $html .= '<button class="primary-color btn btn-light w-100 d-flex align-items-center justify-content-center gap-2 rounded-pill">';
						        $html .= 'Tudj meg többet <i class="fa fa-arrow-right" aria-hidden="true"></i>';
						        $html .= '</button>';
						    $html .= '</a>';

						$html .= '</div>';
					}else{

						$html .= '<div style="margin-bottom: 80px;"></div>';
					}
					

				$html .= '</div>'; // card-body vége
		        $html .= '</div>'; // card vége
		        $html .= '</div>'; // col-md-4 vége
		    }

		} else {
		    $html .= '<div class="col-12"><p><b> Nem található ingatlan </b></p></div>';
		}

		if (!empty($filterType) && in_array('load_more', $filterType['extras'])) {

			$html .= $this->getLoadMoreButton( $currentPage, $totalItems, $apartman_number);

		}else{

			$html .= $this->getPaginate( $currentPage, $totalItems, $apartman_number);
		}

	    $html .= '</div>';

	    return $html;
    }

    public function getCardHtml($datas, $totalItems, $currentPage, $filterType = [])
	{
	    $html = '<div id="custom-card-container" class="row">';

	    $html .= '<div id="mib-spinner" class="mib-spinner spinner-border text-dark m-3" role="status">
	              <span class="visually-hidden">Töltés...</span>
	            </div>';

	    $html .= '<div class="custom-filter-container">
	                <div class="d-flex">';

	                // Filterek megjelenítése
	                if (!empty($this->filterOptionDatas)) {

	                	if (isset($this->filterOptionDatas['mib-filter-price_range']) && $this->filterOptionDatas['mib-filter-price_range'] == true) {
	                        $html .= $this->priceFilterPriceByCatalog($filterType);
	                    }
	                    if (isset($this->filterOptionDatas['mib-filter-floor']) && $this->filterOptionDatas['mib-filter-floor'] == true) {
	                        $html .= $this->getFilterFloorByCatalog($filterType);
	                    }
	                    if (isset($this->filterOptionDatas['mib-filter-room']) && $this->filterOptionDatas['mib-filter-room'] == true) {
	                        $html .= $this->getFilterRoomByCatalog($filterType);
	                    }
	                    if (isset($this->filterOptionDatas['mib-filter-square-meter']) && $this->filterOptionDatas['mib-filter-square-meter'] == true) {
	                        $html .= $this->squareFiltersByCatalog($filterType);
	                    }
	                 
	                }

	    			$html .= '</div>';

	    $html .= '</div>';


	    $html .= '<div class="custom-filter-container">';
		$html .= '<div class="mb-2">';
		$html .= '<button type="button" class="btn btn-outline-secondary btn-sm" id="toggle-advanced-filters">';
		$html .= '<i class="fas fa-sliders-h me-1"></i> További szűrők';
		$html .= '</button>';
		$html .= '</div>';

		$html .= '<div id="advanced-filters" class="flex-wrap" style="display:none;">';

				if (isset($this->filterOptionDatas['mib-filter-orientation']) && $this->filterOptionDatas['mib-filter-orientation'] == true) {
	                $html .= $this->getFilterOrientationByCatalog($filterType);
	            }
	            //ha a "Nem elérhetők elrejtése alapbeállítás" alapból nincs bepipálva.
	            if (isset($this->filterOptionDatas['mib-filter-availability']) && $this->filterOptionDatas['mib-filter-availability'] == true && $this->filterOptionDatas['inactive_hide'] != 1) {
                    $html .= $this->getFilterAvailabilityByCatalog($filterType);
                }

                if (isset($this->filterOptionDatas['mib-garden_connection']) && $this->filterOptionDatas['mib-garden_connection'] == true) {
                    $html .= $this->getFilterGardenConnectionByCatalog($filterType);
                }

                if (isset($this->filterOptionDatas['mib-stairway']) && $this->filterOptionDatas['mib-stairway'] == true) {
                    $html .= $this->getFilterStairwayByCatalog($filterType);
                }

                //mib-stairway
                
        $html .= '</div></div>';
        // Display total count returned by API
        $html .= '<p id="mib-total-count" class="mb-3">' . sprintf( __( 'Találatok száma: %d', 'mib' ), $totalItems ) . '</p>';
        // Sorting options (AJAX)
        $sort_labels = [
            'name' => __('Név', 'mib'),
            'price' => __('Ár', 'mib'),
            'bruttoFloorArea' => __('Alapterület', 'mib'),
        ];
        // Determine current sort settings
        $current_sort = $filterType['sort'] ?? '';
        $current_type = isset($filterType['sortType']) ? strtoupper($filterType['sortType']) : '';

        if (isset($this->filterOptionDatas['mib-display_sort']) && !empty( $this->filterOptionDatas['mib-display_sort'] ) != 0) {

	        $html .= '<div class="mb-3 d-flex align-items-center">';
	        $html .= '<label for="mib-sort-select" class="me-2">' . __('Rendezés:', 'mib') . '</label>';
	        $html .= '<select id="mib-sort-select" class="form-select form-select-sm" style="width:auto;">';
	        $html .= '<option value="">' . __('-- Rendezés --', 'mib') . '</option>';
	        foreach ($sort_labels as $key => $label) {
	            $sel_asc  = ($current_sort === $key && $current_type === 'ASC') ? ' selected' : '';
	            $sel_desc = ($current_sort === $key && $current_type === 'DESC')? ' selected' : '';
	            $html   .= '<option value="' . esc_attr("{$key}|ASC") . '"' . $sel_asc . '>' . esc_html($label) . ' ↑</option>';
	            $html   .= '<option value="' . esc_attr("{$key}|DESC") . '"' . $sel_desc . '>' . esc_html($label) . ' ↓</option>';
	        }
	        $html .= '</select>';
	        $html .= '</div>';

        }

	    if (!empty($datas)) {

	    	$html .= '<div id="view-toggle" class="d-flex justify-content-end mb-3 gap-2">
	    				<div class="reset-filters-wrapper ms-3">
	    					<button id="reset-filters-button" class="btn btn-outline-secondary d-flex align-items-center gap-2">
	    						<i class="fas fa-undo-alt"></i> Szűrők törlése
	    					</button>
	    				</div>
					    <button id="grid-view" class="btn btn-outline-secondary active" title="Rács nézet">
					        <i class="fas fa-th-large"></i>
					    </button>
					    <button id="list-view" class="btn btn-outline-secondary" title="Lista nézet">
					        <i class="fas fa-bars"></i>
					    </button>
					</div>';

		    foreach ($datas as $data) {

		    	$logo = '';
				if (isset($this->filterOptionDatas['mib-display_logo']) && $this->filterOptionDatas['mib-display_logo'] == 1 && !empty($data['logo'])) {
					$logo = '<img src="'.$data['logo'].'" crossorigin="anonymous">';
				}

				$address = '';
				if (isset($this->filterOptionDatas['mib-display_address']) && $this->filterOptionDatas['mib-display_address'] == 1 ) {
					$address = $data['address'];
				}

		        $html .= '<div class="card-wrapper col-md-4 mb-3">'; // 4 oszlopos grid
		        $html .= '<div class="card h-100 position-relative">'; // A kártyát relatív pozicionáljuk
		        
		        // Kép wrapper, flexbox középre igazítással
		        $html .= '<div class="primary-color card-image-wrapper">';
		        $html .= '<img src="' . $data['image'] . '" class="card-img-top" alt="Lakás képe" crossorigin="anonymous">';
		        $html .= '</div>';

		        $html .= '<div id="apartment-card-body" class="secondary-color card-body d-flex flex-column justify-content-between text-white">';

				    // Cím és emelet
				    $html .= '<div class="mb-3">';
				        $html .= '<div class="d-flex justify-content-between">';

				        	if (!empty($logo)) {
				        		$html .= '<div>';
					                $html .= '<div class="park-logo">'.$logo.'</div>';
					                $html .= '<strong>'.$address.'</strong>';
					            $html .= '</div>';
				        	}
				        	
				            $html .= '<div>';
				                $html .= '<small class="third-text-color '.$data['statusclass'].' d-block">'.$data['statusrow'].'</small>';
				                $html .= '<strong class="fs-5">' . esc_html($data['rawname']) . '</strong>';
				            $html .= '</div>';
				        $html .= '</div>';
				        $html .= '<hr>';
				    $html .= '</div>';

				    // Szobák és alapterület
				    $html .= '<div class="mb-3">';
				        $html .= '<div class="d-flex justify-content-between">';
				        	$html .= '<div>';
				                $html .= '<small class="d-block text-muted">Szobák</small>';
				                $html .= '<strong class="fs-5">' . esc_html($data['numberOfRooms']) . '</strong>';
				            $html .= '</div>';
				            $html .= '<div>';
				                $html .= '<small class="d-block text-muted">Alapterület (m2)</small>';
				                $html .= '<strong class="fs-5">' . esc_html($data['bruttoFloorArea']) . '</strong>';
				            $html .= '</div>';
				            $html .= '<div class="text-end">';
				                $html .= '<small class="d-block text-muted">Emelet</small>';
				                $html .= '<strong class="fs-5">' . esc_html($data['floor'])  . '</strong>';
				            $html .= '</div>';
				        $html .= '</div>';
				        $html .= '<hr>';
				    $html .= '</div>';

				    // Ár
					$html .= '<div class="list-view-price-container mt-2 mt-md-0" style="display: flex; align-items: center; gap: 10px;">';
						$html .= '<strong class="fs-4 text-success third-text-color">' . esc_html($data['price']) . '</strong>';
					$html .= '</div>';

					// Függőleges elválasztó (gomb elé, csak lista nézetben)
					$html .= '<div class="card-divider list-view-only"></div>';

					$html .= '<div class="list-view-button-wrapper d-flex align-items-center button-row">';

					if ($data['statusrow'] == 'Elérhető') {

					    // Szív ikon bal oldalon
					    $html .= '<i class="fa fa-regular fa-heart favorite-icon" aria-hidden="true" data-id="' . esc_attr($data['id']) . '"></i>';
					    
					    // Gomb
					    $html .= '<a id="cardhref" href="' . $data['url'] . '" class="flex-grow-1">';
					        $html .= '<button class="primary-color btn btn-light w-100 d-flex align-items-center justify-content-center gap-2 rounded-pill">';
					        $html .= 'Tudj meg többet <i class="fa fa-arrow-right" aria-hidden="true"></i>';
					        $html .= '</button>';
					    $html .= '</a>';
					}else{

						$html .= '<div style="margin-bottom: 80px;"></div>';
					}

					$html .= '</div>';

				$html .= '</div>'; // card-body vége
		        $html .= '</div>'; // card vége
		        $html .= '</div>'; // col-md-4 vége
		    }

		} else {
		    $html .= '<div class="col-12"><p><b> Nem található ingatlan </b></p></div>';
		}


		if (isset($this->filterOptionDatas['mib-loadmore_checked']) && $this->filterOptionDatas['mib-loadmore_checked'] == true) {
			
			$html .= $this->getLoadMoreButton( $currentPage, $totalItems, 9);

		}else{

			$html .= $this->getPaginate( $currentPage, $totalItems, 9);
		}

	    $html .= '</div>';

	    return $html;
	}

	public function getMoreCards($datas, $totalItems, $currentPage)
	{
	    $html = '';

	   
	    if (!empty($datas)) {

		    foreach ($datas as $data) {

		    	$logo = '';
				if (isset($this->filterOptionDatas['mib-display_logo']) && $this->filterOptionDatas['mib-display_logo'] == 1 && !empty($data['logo'])) {
					$logo = '<img src="'.$data['logo'].'" crossorigin="anonymous">';
				}elseif (isset($this->filterType['extras']) && in_array('display_logo', $this->filterType['extras']) && !empty($data['logo']) ) {
					$logo = '<img src="'.$data['logo'].'" crossorigin="anonymous">';
				}

				$address = '';
				if (isset($this->filterOptionDatas['mib-display_address']) && $this->filterOptionDatas['mib-display_address'] == 1 ) {
					$address = $data['address'];
				}elseif(isset($filterType) && in_array('display_address', $filterType['extras'])){
					$address = $data['address'];
				}

		        $html .= '<div class="card-wrapper col-md-4 mb-3" data-id="' . esc_attr($data['id']) . '">'; // 4 oszlopos grid
		        $html .= '<div class="card h-100 position-relative">'; // A kártyát relatív pozicionáljuk
		        
		        // Kép wrapper, flexbox középre igazítással
		        $html .= '<div class="primary-color card-image-wrapper">';
		        $html .= '<img src="' . $data['image'] . '" class="card-img-top" alt="Lakás képe" crossorigin="anonymous">';
		        $html .= '</div>';

		        $html .= '<div id="apartment-card-body" class="secondary-color card-body d-flex flex-column justify-content-between text-white">';

				    // Cím és emelet
				    $html .= '<div class="mb-3">';
				        $html .= '<div class="d-flex justify-content-between">';

				        	if (!empty($logo)) {
				        		$html .= '<div>';
					                $html .= '<div class="park-logo">'.$logo.'</div>';
					                $html .= '<strong>'.$address.'</strong>';
					            $html .= '</div>';
				        	}
				        	
				            $html .= '<div>';
				                $html .= '<small class="third-text-color '.$data['statusclass'].' d-block">'.$data['statusrow'].'</small>';
				                $html .= '<strong class="fs-5">' . esc_html($data['rawname']) . '</strong>';
				            $html .= '</div>';
				        $html .= '</div>';
				        $html .= '<hr>';
				    $html .= '</div>';

				    // Szobák és alapterület
				    $html .= '<div class="mb-3">';
				        $html .= '<div class="d-flex justify-content-between">';
				        	$html .= '<div>';
				                $html .= '<small class="d-block text-muted">Szobák</small>';
				                $html .= '<strong class="fs-5">' . esc_html($data['numberOfRooms']) . '</strong>';
				            $html .= '</div>';
				            $html .= '<div>';
				                $html .= '<small class="d-block text-muted">Alapterület (m2)</small>';
				                $html .= '<strong class="fs-5">' . esc_html($data['bruttoFloorArea']) . '</strong>';
				            $html .= '</div>';
				            $html .= '<div class="text-end">';
				                $html .= '<small class="d-block text-muted">Emelet</small>';
				                $html .= '<strong class="fs-5">' . esc_html($data['floor'])  . '</strong>';
				            $html .= '</div>';
				        $html .= '</div>';
				        $html .= '<hr>';
				    $html .= '</div>';

				    // Ár
				    $html .= '<div class="list-view-price-container mt-2 mt-md-0">';
						$html .= '<strong class="fs-4 text-success third-text-color">' . esc_html($data['price']) . '</strong>';
					$html .= '</div>';

					// Függőleges elválasztó (gomb elé, csak lista nézetben)
					$html .= '<div class="card-divider list-view-only"></div>';

					$html .= '<div class="list-view-button-wrapper d-flex align-items-center button-row">';
					    // Gomb
					    if ($data['statusrow'] == 'Elérhető') {
								    
							    // Szív ikon bal oldalon
							    $html .= '<i class="fa fa-regular fa-heart favorite-icon" aria-hidden="true" data-id="' . esc_attr($data['id']) . '"></i>';
							    
							    // Gomb
							    $html .= '<a id="cardhref" href="' . $data['url'] . '" class="flex-grow-1">';
							        $html .= '<button class="primary-color btn btn-light w-100 d-flex align-items-center justify-content-center gap-2 rounded-pill">';
							        $html .= 'Tudj meg többet <i class="fa fa-arrow-right" aria-hidden="true"></i>';
							        $html .= '</button>';
							    $html .= '</a>';
						}else{

							$html .= '<div style="margin-bottom: 80px;"></div>';
						}

					$html .= '</div>';

				$html .= '</div>'; // card-body vége
		        $html .= '</div>'; // card vége
		        $html .= '</div>'; // col-md-4 vége
		    }

		} else {
		    $html .= '<div class="col-12"><p><b> Nem található több ingatlan </b></p></div>';
		}


		//$html .= $this->getLoadMoreButton( $currentPage, $totalItems, 9);

	    $html .= '</div>';

	    return $html;
	}

	public function getFilters(){

		$html .= '<div class="custom-filter-container">
	                <div class="d-flex">';

	                // Filterek megjelenítése
	                if (!empty($this->filterOptionDatas)) {

	                	if (isset($this->filterOptionDatas['mib-filter-price_range']) && $this->filterOptionDatas['mib-filter-price_range'] == true) {
	                        $html .= $this->priceFilterPriceByCatalog($filterType, 'custom-filter');
	                    }
	                    if (isset($this->filterOptionDatas['mib-filter-floor']) && $this->filterOptionDatas['mib-filter-floor'] == true) {
	                        $html .= $this->getFilterFloorByCatalog($filterType, 'custom-filter');
	                    }
	                    if (isset($this->filterOptionDatas['mib-filter-room']) && $this->filterOptionDatas['mib-filter-room'] == true) {
	                        $html .= $this->getFilterRoomByCatalog($filterType, 'custom-filter');
	                    }
	                    if (isset($this->filterOptionDatas['mib-filter-square-meter']) && $this->filterOptionDatas['mib-filter-square-meter'] == true) {
	                        $html .= $this->squareFiltersByCatalog($filterType, 'custom-filter');
	                    }
	                 
				}

				// Összetett kereső dropdown a sliderek mellett: Tájolás és Státusz szűrés
				$html .= '<div class="mb-2">
	                <div class="dropdown">
	                    <button class="btn btn-dark dropdown-toggle primary-color" type="button" id="advancedSearchDropdown" data-bs-toggle="dropdown" aria-expanded="false">
	                        Összetett kereső
	                    </button>
	                    <ul class="primary-color mt-1 dropdown-menu" aria-labelledby="advancedSearchDropdown">';
				foreach ($this->orientation as $key => $value) {
					$html .= '<li><label class="dropdown-item"><input type="checkbox" class="orientation-checkbox" name="orientation[]" value="' . esc_attr($value) . '"> ' . esc_html($key) . '</label></li>';
				}
				foreach ($this->availability as $key => $value) {
					$html .= '<li><label class="dropdown-item"><input type="checkbox" class="availability-checkbox" name="availability[]" value="' . esc_attr($value) . '"> ' . esc_html($key) . '</label></li>';
				}
				$html .= '</ul>
	                </div>
	            </div>';

				$html .= '</div>';


	    			$html .= '<div class="search-mib-filter-container"><button id="search-apartman-btn" class="btn third-color">Lakások keresése <i class="fa fa-arrow-right" aria-hidden="true"></i></button></div>';

	    $html .= '</div>';

	    return $html;
	}

    private function getSearch()
    {
    	$html = '<div class="col-md-3">
	               <input type="text" id="custom-search-input" placeholder="' . __('Keresés...', 'mib') . '">
	            </div>';

	    return $html;
    }

    private function deleteFilters()
    {
    	$html = '<button class="btn btn-dark primary-color" type="button" id="mib-filter-deletefilters">
                    Szűrők törlése
                </button>';

	    return $html;
    }


    private function squareFilters()
	{

		if ( isset($this->filterOptionDatas['mib-filterslider_checked'] ) && $this->filterOptionDatas['mib-filterslider_checked'] == 1 ) {
			
			$html = '<div class="area-range-container">
	            <div class="square-filter-container">

	                <!-- Minimális terület -->
	                <div class="min-area-container mb-3">
	                    
	                    <div class="input-group">
	                        <input type="number" id="min-area" class="form-control" name="min_area" placeholder="Min terület" min="0">
	                        <span class="input-group-text">m²</span>
	                    </div>
	                    <label for="min-area">Minimális terület</label>
	                </div>

	                <!-- Maximális terület -->
	                <div class="max-area-container mb-3">
	                    
	                    <div class="input-group">
	                        <input type="number" id="max-area" class="form-control" name="max_area" placeholder="Max terület" min="0">
	                        <span class="input-group-text">m²</span>
	                    </div>
	                    <label for="max-area">Maximális terület</label>
	                </div>

	            </div>
	        </div>';

		}else{

			$html = '<div class="range-slider-container primary-color"><div id="slider-range"></div>
					<p id="slider-value">
					  Terület: <span id="range-value"></span> m²
				</p></div>';

	    	return $html;
		}

	    

	    return $html;
	}

    private function priceFilters()
    {

    	if ( isset($this->filterOptionDatas['mib-filterslider_checked'] ) && $this->filterOptionDatas['mib-filterslider_checked'] == 1 ) {
			
			$html = '<div class="area-range-container">
            <div class="square-filter-container">

                <!-- Minimális ár -->
                <div class="min-area-container mb-3">
                    <div class="input-group">
                        <input type="number" id="min-price" class="form-control" name="min_price" placeholder="Min ár" min="0">
                        <span class="input-group-text">Ft</span>
                    </div>
                    <label for="min-price">Minimális ár</label>
                </div>

                <!-- Maximális ár -->
                <div class="max-area-container mb-3">
                    <div class="input-group">
                        <input type="number" id="max-price" class="form-control" name="max_price" placeholder="Max ár" min="0">
                        <span class="input-group-text">Ft</span>
                    </div>
                    <label for="max-price">Maximális ár</label>
                </div>

            </div>
        </div>';

		}else{

			$html = '<div class="range-slider-container primary-color"><div id="price-slider-range"></div>
					<p id="price-slider-value">
					  Ár: <span id="price-range-value"></span>
				</p></div>';

		}

	    return $html;
    }

    private function priceFilterPriceByCatalog($filterType, $custom = '') {
	    // Alapértelmezett értékek
	    $priceFrom = $this->filterOptionDatas['mib-filter-price-slider-min'] ?? -1;
	    $priceTo = $this->filterOptionDatas['mib-filter-price-slider-max'] ?? 10;

	    $selectedMin = $filterType['floor_min'] ?? $priceFrom;
	    $selectedMax = $filterType['floor_max'] ?? $priceTo;

	    $html = '<div class="custom-slider-container">
	        <label class="custom-slider-label">Ár</label>
	        <div id="custom-price-slider" class="'.$custom.' slider-inactive-color custom-noui-slider"></div>
	        <p class="custom-price-range-value">' . $selectedMin . ' - ' . $selectedMax . '</p>
	    </div>';

	    return $html;
	}

    private function getFilterFloorByCatalog($filterType, $custom = '') {
	    // Alapértelmezett értékek
	    $floorFrom = $this->filterOptionDatas['mib-filter-floor-from'] ?? -1;
	    $floorTo = $this->filterOptionDatas['mib-filter-floor-to'] ?? 10;

	    $selectedMin = $filterType['floor_min'] ?? $floorFrom;
	    $selectedMax = $filterType['floor_max'] ?? $floorTo;

	    $html = '<div class="custom-slider-container">
	        <label class="custom-slider-label">Emelet</label>
	        <div id="custom-floor-slider" class="'.$custom.' custom-noui-slider slider-inactive-color"></div>
	        <p class="custom-floor-range-value">' . $selectedMin . ' - ' . $selectedMax . '</p>
	    </div>';

	    return $html;
	}

	private function getFilterFloorShortCodeByCatalog($selectedMin, $selectedMax) {

	    // Alapértelmezett értékek
	    $html = '<div class="custom-slider-container">
	        <label class="custom-slider-label">Emelet</label>
	        <div id="custom-floor-slider" class="custom-noui-slider slider-inactive-color"></div>
	        <p class="custom-floor-range-value">' . $selectedMin . ' - ' . $selectedMax . '</p>
	    </div>';

	    return $html;
	}

	private function getFilterPriceShortCodeByCatalog($selectedMin, $selectedMax) {

	    // Alapértelmezett értékek
	    $html = '<div class="custom-slider-container">
	        <label class="custom-slider-label">Ár</label>
	        <div id="custom-price-slider" class="custom-noui-slider slider-inactive-color"></div>
	        <p class="custom-price-range-value">' . $selectedMin . ' - ' . $selectedMax . '</p>
	    </div>';

	    return $html;
	}

	private function getFilterRoomShortCodeByCatalog($selectedMin, $selectedMax) {

	    // Alapértelmezett értékek
	    $html = '<div class="custom-slider-container">
	        <label class="custom-slider-label">Szobák száma</label>
	        <div id="custom-room-slider" class="custom-noui-slider slider-inactive-color"></div>
	        <p class="custom-room-range-value">' . $selectedMin . ' - ' . $selectedMax . '</p>
	    </div>';

	    return $html;
	}

	private function getFilterAreaShortCodeByCatalog($selectedMin, $selectedMax) {

	    $html = '<div class="custom-slider-container">
	        <label class="custom-square-label">Terület</label>
	        <div id="custom-square-slider" class="custom-noui-slider slider-inactive-color"></div>
	        <p class="custom-square-range-value">' . $selectedMin . ' - ' . $selectedMax . '</p>
	    </div>';

	    return $html;
	}

	private function getFilterResidentalParksShortCodeByCatalog($parkIds) {

	    // Alapértelmezett értékek
	    $html = '<select class="form-select select-residential-park" aria-label="Park kiválasztása">';
	    $html .= '<option value="" selected>Park kiválasztása</option>';
		    		foreach ($parkIds as $park => $id) {
		    			$html .= '<option value="'.$id.'">'.$id.'</option>';
		    		}
	    $html .= '</select>';

	    return $html;
	}


	private function getFilterResidentalParkShortCodeByCatalog() {

	    // Alapértelmezett értékek
	    $html = '<div class="custom-slider-container">
	        <label class="custom-slider-label">Terület</label>
	        <div id="custom-square-slider" class="custom-noui-slider slider-inactive-color"></div>
	        <p class="custom-square-range-value">' . $selectedMin . ' - ' . $selectedMax . '</p>
	    </div>';

	    return $html;
	}

	private function getFilterRoomByCatalog($filterType, $custom = '') {
	    // Alapértelmezett értékek
	    $roomFrom = $this->filterOptionDatas['mib-filter-room-from'] ?? -1;
	    $roomTo = $this->filterOptionDatas['mib-filter-room-to'] ?? 10;

	    $selectedMin = $filterType['room_min'] ?? $roomFrom;
	    $selectedMax = $filterType['room_max'] ?? $roomTo;

	    $html = '<div class="custom-slider-container">
	        <label class="custom-slider-label">Szobák száma</label>
	        <div id="custom-room-slider" class="'.$custom.' custom-noui-slider slider-inactive-color"></div>
	        <p class="custom-room-range-value">' . $selectedMin . ' - ' . $selectedMax . '</p>
	    </div>';

	    return $html;
	}

	private function squareFiltersByCatalog($filterType, $custom = '') {

	    // Alapértelmezett értékek
	    $squareFrom = $this->filterOptionDatas['mib-filter-square-meter-slider-min'] ?? -1;
	    $squareTo = $this->filterOptionDatas['mib-filter-square-meter-slider-max'] ?? 10;

	    $selectedMin = $filterType['square-meter-slider-min'] ?? $squareFrom;
	    $selectedMax = $filterType['square-meter-slider-max'] ?? $squareTo;

	    $html = '<div class="custom-slider-container">
	        <label class="custom-slider-label">Terület</label>
	        <div id="custom-square-slider" class="'.$custom.' custom-noui-slider slider-inactive-color"></div>
	        <p class="custom-square-range-value">' . $selectedMin . ' - ' . $selectedMax . '</p>
	    </div>';

	    return $html;
	}

	private function getFilterOrientationByCatalog($filterType) {
	    // Tájolások konvertálása tömbbé, ha szükséges
	    if (isset($filterType['orientation']) && !is_array($filterType['orientation'])) {
	        $filterType['orientation'] = explode(',', $filterType['orientation']);
	    }

	    $html = '<div class="catalog-dropdown mt-3">
	                <div class="dropdown">
	                    <button class="btn btn-dark dropdown-toggle" type="button" id="orientationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
	                        Tájolás
	                    </button>
	                    <ul class="third-color mt-1 p-2 dropdown-menu" aria-labelledby="orientationDropdown">';

	    // Tájolások listája
	    foreach ($this->orientation as $key => $value) {
	        $orientationChecked = (isset($filterType['orientation']) && in_array($value, (array)$filterType['orientation'])) ? 'checked' : '';

	        $html .= '<li>
	                    <label class="dropdown-item">
	                        <input type="checkbox" class="catalog-orientation-checkbox form-check-input" name="orientation[]" value="' . $value . '" ' . $orientationChecked . '> ' . $key . '
	                    </label>
	                  </li>';
	    }

	    $html .=   '</ul>
	                </div>
	            </div>';

	    return $html;
	}


    private function getFilterFloor($filterType) {

    	if (isset($filterType['floor']) && !is_array($filterType['floor']) ) {
    		$filterType['floor'] = explode(',', $filterType['floor']);
    	}

	    $html = '<div class="mb-2">
	                <div class="dropdown">
	                    <button class="btn btn-dark dropdown-toggle primary-color" type="button" id="floorDropdown" data-bs-toggle="dropdown" aria-expanded="false">
	                        Emelet
	                    </button>
	                    <ul class="primary-color mt-1 dropdown-menu" aria-labelledby="floorDropdown">';
	    
	    $floorFrom = (isset($this->filterOptionDatas['mib-filter-floor-from']) && !empty($this->filterOptionDatas['mib-filter-floor-from']) ) ? $this->filterOptionDatas['mib-filter-floor-from']: 0;
	    $floorTo = (isset($this->filterOptionDatas['mib-filter-floor-to']) && !empty($this->filterOptionDatas['mib-filter-floor-to']) ) ? $this->filterOptionDatas['mib-filter-floor-to']: 0;

	    for ($i = $floorFrom; $i <= $floorTo; $i++) {

	        $floorLabel = ($i == 0) ? __('Földszint', 'mib') : $i;
	        $floorChecked = (isset($filterType['floor']) && in_array($i, (array)$filterType['floor'])) ? 'checked' : '';
	        
	        $html .= '<li>
	                    <label class="dropdown-item">
	                        <input type="checkbox" class="floor-checkbox" name="floors[]" value="' . $i . '" ' . $floorChecked . '> ' . $floorLabel . '
	                    </label>
	                  </li>';
	    }

	    $html .=   '</ul>
	                </div>
	            </div>';


	    return $html;
	}


	private function getFilterRoom($filterType) {
	    // Szobák számának konvertálása tömbbé, ha szükséges
	    if (isset($filterType['numberOfRooms']) && !is_array($filterType['numberOfRooms'])) {
	        $filterType['numberOfRooms'] = explode(',', $filterType['numberOfRooms']);
	    }

	    $html = '<div class="mb-2">
	                <div class="dropdown">
	                    <button class="btn btn-dark dropdown-toggle primary-color" type="button" id="roomDropdown" data-bs-toggle="dropdown" aria-expanded="false">
	                        Szobák
	                    </button>
	                    <ul class="primary-color mt-1 dropdown-menu" aria-labelledby="roomDropdown">';
	    
	    $roomFrom = (isset($this->filterOptionDatas['mib-filter-room-from']) && !empty($this->filterOptionDatas['mib-filter-room-from'])) ? $this->filterOptionDatas['mib-filter-room-from'] : 1;
	    $roomTo = (isset($this->filterOptionDatas['mib-filter-room-to']) && !empty($this->filterOptionDatas['mib-filter-room-to'])) ? $this->filterOptionDatas['mib-filter-room-to'] : 4;

	    for ($i = $roomFrom; $i <= $roomTo; $i++) {
	        $roomLabel = $i;
	        $roomChecked = (isset($filterType['numberOfRooms']) && in_array($i, (array)$filterType['numberOfRooms'])) ? 'checked' : '';
	        
	        $html .= '<li>
	                    <label class="dropdown-item">
	                        <input type="checkbox" class="room-checkbox" name="numberOfRooms[]" value="' . $i . '" ' . $roomChecked . '> ' . $roomLabel . '
	                    </label>
	                  </li>';
	    }

	    $html .=   '</ul>
	                </div>
	            </div>';

	    return $html;
	}

	private function getFilterOrientation($filterType) {
	    // Tájolások konvertálása tömbbé, ha szükséges
	    if (isset($filterType['orientation']) && !is_array($filterType['orientation'])) {
	        $filterType['orientation'] = explode(',', $filterType['orientation']);
	    }

	    $html = '<div class="mb-2">
	                <div class="dropdown">
	                    <button class="btn btn-dark dropdown-toggle primary-color" type="button" id="orientationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
	                        Tájolás
	                    </button>
	                    <ul class="primary-color mt-1 dropdown-menu" aria-labelledby="orientationDropdown">';

	    // Tájolások listája
	    foreach ($this->orientation as $key => $value) {
	        $orientationChecked = (isset($filterType['orientation']) && in_array($value, (array)$filterType['orientation'])) ? 'checked' : '';

	        $html .= '<li>
	                    <label class="dropdown-item">
	                        <input type="checkbox" class="orientation-checkbox" name="orientation[]" value="' . $value . '" ' . $orientationChecked . '> ' . $key . '
	                    </label>
	                  </li>';
	    }

	    $html .=   '</ul>
	                </div>
	            </div>';

	    return $html;
	}

	private function getFilterAvailability($filterType) {

	    if (isset($filterType['status']) && !is_array($filterType['status'])) {
	        $filterType['status'] = explode(',', $filterType['status']);
	    }

	    $html = '<div class="mb-2">
	                <div class="dropdown">
	                    <button class="btn btn-dark dropdown-toggle primary-color" type="button" id="availabilityDropdown" data-bs-toggle="dropdown" aria-expanded="false">
	                        Státusz
	                    </button>
	                    <ul class="primary-color mt-1 dropdown-menu" aria-labelledby="availabilityDropdown">';

	    // elérhetőség
	    foreach ($this->availability as $key => $value) {
	        $availabilityChecked = (isset($filterType['status']) && in_array($value, (array)$filterType['status'])) ? 'checked' : '';

	        $html .= '<li>
	                    <label class="dropdown-item">
	                        <input type="checkbox" class="availability-checkbox" name="availability[]" value="' . $value . '" ' . $availabilityChecked . '> ' . $key . '
	                    </label>
	                  </li>';
	    }

	    $html .=   '</ul>
	                </div>
	            </div>';

	    return $html;
	}

	private function getFavoriteOption()
	{


	    return $html;
	}

	private function getFilterAvailabilityByCatalog()
	{
		if (isset($filterType['status']) && !is_array($filterType['status'])) {
	        $filterType['status'] = explode(',', $filterType['status']);
	    }

	    $html = '<div class="catalog-dropdown mt-3">
	                <div class="dropdown">
	                    <button class="btn btn-dark dropdown-toggle" type="button" id="availabilityDropdown" data-bs-toggle="dropdown" aria-expanded="false">
	                        Státusz
	                    </button>
	                    <ul class="third-color mt-1 p-2 dropdown-menu" aria-labelledby="availabilityDropdown">';

	    // elérhetőség
	    foreach ($this->availability as $key => $value) {
	        $availabilityChecked = (isset($filterType['status']) && in_array($value, (array)$filterType['status'])) ? 'checked' : '';

	        $html .= '<li>
	                    <label class="dropdown-item">
	                        <input type="checkbox" class="catalog-availability-checkbox form-check-input" name="availability[]" value="' . $value . '" ' . $availabilityChecked . '> ' . $key . '
	                    </label>
	                  </li>';
	    }

	    $html .=   '</ul>
	                </div>
	            </div>';

	    return $html;
	}


	private function getFilterGardenConnectionByCatalog()
	{
		if (isset($filterType['mib-garden_connection']) && !is_array($filterType['mib-garden_connection'])) {
	        $filterType['mib-garden_connection'] = explode(',', $filterType['mib-garden_connection']);
	    }

	    $html = '<div class="catalog-dropdown mt-3">
	                <div class="dropdown">
	                    <button class="btn btn-dark dropdown-toggle" type="button" id="gardenConnectionDropdown" data-bs-toggle="dropdown" aria-expanded="false">
	                        Kert kapcsolat
	                    </button>
	                    <ul class="third-color mt-1 p-2 dropdown-menu" aria-labelledby="availabilityDropdown">';

	    foreach ($this->gardenConnection as $key => $value) {

	        $availabilityChecked = (isset($filterType['garden_connection']) && in_array($value, (array)$filterType['garden_connection'])) ? 'checked' : '';

	        $html .= '<li>
	                    <label class="dropdown-item">
	                        <input type="checkbox" class="catalog-gardenconnection-checkbox form-check-input" name="garden_connection[]" value="' . $value . '" ' . $availabilityChecked . '> ' . $key . '
	                    </label>
	                  </li>';
	    }

	    $html .=   '</ul>
	                </div>
	            </div>';

	    return $html;
	}



	private function getFilterStairwayByCatalog()
	{
		if (isset($filterType['mib-stairway']) && !is_array($filterType['mib-stairway'])) {
	        $filterType['mib-stairway'] = explode(',', $filterType['mib-stairway']);
	    }

	    $html = '<div class="catalog-dropdown mt-3">
	                <div class="dropdown">
	                    <button class="btn btn-dark dropdown-toggle" type="button" id="stairWayDropdown" data-bs-toggle="dropdown" aria-expanded="false">
	                        Lépcsőház
	                    </button>
	                    <ul class="third-color mt-1 p-2 dropdown-menu" aria-labelledby="availabilityDropdown">';

	    foreach ($this->stairWay as $key => $value) {

	        $availabilityChecked = (isset($filterType['stairway']) && in_array($value, (array)$filterType['stairway'])) ? 'checked' : '';

	        $html .= '<li>
	                    <label class="dropdown-item">
	                        <input type="checkbox" class="catalog-stairway-checkbox form-check-input" name="stairway[]" value="' . $value . '" ' . $availabilityChecked . '> ' . $key . '
	                    </label>
	                  </li>';
	    }

	    $html .=   '</ul>
	                </div>
	            </div>';

	    return $html;
	}



	public function getPaginate($currentPage = 1, $totalItems = 750, $itemsPerPage = 50) {

	    $html = ' ';

	    $totalPages = ceil($totalItems / $itemsPerPage);

	    $html .= '<nav aria-label="mib pagination">';
	    $html .= '<ul class="pagination" style="display:flex;">';

	    if ($totalPages>1) {
	    	// Előző oldal
	    	$prevPage = $currentPage > 1 ? $currentPage - 1 : 1;
	    	$disabledClass = $currentPage > 1 ? '' : ' disabled';
	    	$html .= '<li class="page-item' . $disabledClass . '"><a id="page-link" class="page-link" data-page="' . $prevPage . '">«</a></li>';
		}

	    // Oldalszámok
	    for ($i = 1; $i <= $totalPages; $i++) {
	        $activeClass = $currentPage == $i ? ' active' : '';
	        $html .= '<li class="page-item' . $activeClass . '"><a id="page-link" class="page-link" data-page="' . $i . '">' . $i . '</a></li>';
	    }

	    if ($totalPages>1) {
	    	
	    	// Következő oldal
	    	$nextPage = $currentPage < $totalPages ? $currentPage + 1 : $totalPages;
	    	$disabledClass = $currentPage < $totalPages ? '' : ' disabled';
	    	$html .= '<li class="page-item' . $disabledClass . '"><a id="page-link" class="page-link" data-page="' . $nextPage . '">»</a></li>';
	    }
	    

	    $html .= '</ul>';
	    $html .= '</nav>';

	    return $html;
	}
	

	public function getLoadMoreButton($currentPage = 1, $totalItems = 750, $itemsPerPage = 50) {
	    $html = '';
	    $totalPages = ceil($totalItems / $itemsPerPage);

	    // Csak akkor jelenik meg a "Még több" gomb, ha van még betöltendő tartalom
	    if ($currentPage < $totalPages) {
	        $nextPage = $currentPage + 1;
	        $html .= '<div class="load-more-container">';
	        $html .= '<button id="load-more-button" class="btn btn-primary" data-page="' . $nextPage . '">Még több ingatlan</button>';
	        $html .= '</div>';
	    }

	    return $html;
	}



}