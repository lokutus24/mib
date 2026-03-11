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
        'Nyugati' => 'west',
        'Északnyugati' => 'northWest'
    ];

    private $orientationShortName = [
        'Északi' => 'É',
        'Északkeleti' => 'ÉK',
        'Keleti' => 'K',
        'Délkeleti' => 'DK',
        'Déli' => 'D',
        'Délnyugati' => 'DNY',
        'Nyugati' => 'W',
        'Északnyugati' => 'ÉNY'
    ];

    private $balconyTypes = [
        'Terasz' => 'terasz',
        'Erkély' => 'erkély',
        'Lodgia' => 'lodgia'
    ];

    private $availability = [
        'Nem elérhetőek elrejtése' => 'Available',
    ];

    private $gardenConnection = [
        'Igen' => 1,
    ];

    private $otthonStart = [
        'Igen' => 1,
    ];

    private $stairWay = [
        'A' => 'A',
        'B' => 'B',
        'C' => 'C',
        'D' => 'D'
    ];


    private $types = [
        'lakás',
        'üzlethelyiség',
        'iroda',
        'apartman',
        // 'tároló',
        //  'emeleti tároló',
        // 'tároló beállóval',
        //  'beálló',
        //  'E töltős',
        // 'mozgáskorlátozott',
        //  'beálló tárolóval',
        //  'csökkentett',
    ];

    private $parkNames = [
        7 => 'Albion 32',
        9 => 'BudaBright',
        10 => 'PápayPark',
        11 => 'Loft52',
        12 => 'MyLelle',
        31 => 'Páva 8',
        35 => 'BartokHarmonyHomes',
        40 => 'Novus Liget',
        41 => 'Frangepán',
        42 => 'BrickeryHomes',
        43 => 'Revital Park',
        44 => 'Vác Dunakert',
        45 => 'Cascade Residence',
    ];

    private $parkDistrictName = [
        12 => 'Balatonlelle'
    ];

    private $districtNames = [
        'I' => 'I. kerület',
        'II' => 'II. kerület',
        'III' => 'III. kerület',
        'IV' => 'IV. kerület',
        'V' => 'V. kerület',
        'VI' => 'VI. kerület',
        'VII' => 'VII. kerület',
        'VIII' => 'VIII. kerület',
        'IX' => 'IX. kerület',
        'X' => 'X. kerület',
        'XI' => 'XI. kerület',
        'XII' => 'XII. kerület',
        'XIII' => 'XIII. kerület',
        'XIV' => 'XIV. kerület',
        'XV' => 'XV. kerület',
        'XVI' => 'XVI. kerület',
        'XVII' => 'XVII. kerület',
        'XVIII' => 'XVIII. kerület',
        'XIX' => 'XIX. kerület',
        'XX' => 'XX. kerület',
        'XXI' => 'XXI. kerület',
        'XXII' => 'XXII. kerület',
        'XXIII' => 'XXIII. kerület',
    ];


    public function getTypes()
    {
        return $this->types;
    }

    public $numberOfApartmens = 9;

    public $filterOptionDatas = [];

    public $mibOptions = [];

    public $filterOptionCrossSellDatas = [];

    public function getCorsAttribute($url)
    {
        if (strpos($url, 'ugyfel.mibportal.hu') !== false) {
            return ' crossorigin="anonymous"';
        }
        return '';
    }

    private function getApartmentListingImageUrl(array $data): string
    {
        $candidates = [
            $data['szintrajz_img'] ?? '',
            $data['gallery_first'] ?? '',
            $data['main_image'] ?? '',
            $data['image'] ?? '',
        ];

        foreach ($candidates as $candidate) {
            if (is_string($candidate) && $candidate !== '') {
                return $candidate;
            }
        }

        return '';
    }

    private function renderApartmentListingImage(array $data, string $class = 'card-img-top', string $alt = 'Lakás képe'): string
    {
        $imgSrc = $this->getApartmentListingImageUrl($data);

        if ($imgSrc === '') {
            return '';
        }

        $cors = $this->getCorsAttribute($imgSrc);

        return '<img src="' . esc_url($imgSrc) . '" class="' . esc_attr($class) . '" alt="' . esc_attr($alt) . '"' . $cors . '>';
    }

    private function getResidentialParkLogoUrls($park, int $parkId): array
    {
        $defaultLightLogo = $park->lightlogo ?? $park->light_logo ?? $park->logo ?? '';
        $defaultDarkLogo = $park->darklogo ?? $park->dark_logo ?? $park->logo ?? '';
        $defaultBadge = $park->badge ?? '';
        $attachmentLogos = $this->get_residential_park_attachments($park->name ?? '', $parkId);

        $shadowLinks = $this->get_rezideo_links($park->name ?? '', 'residential_park', $parkId, $parkId);

        $map = [
            'light_logo' => $attachmentLogos['light_logo'] ?? $defaultLightLogo,
            'dark_logo' => $attachmentLogos['dark_logo'] ?? $defaultDarkLogo,
            'logo' => $attachmentLogos['logo'] ?? ($defaultLightLogo ?: $defaultDarkLogo),
            'badge' => $attachmentLogos['badge'] ?? $defaultBadge,
        ];

        foreach ($shadowLinks as $link) {
            if (!empty($link['url']) && isset($map[$link['type']])) {
                $map[$link['type']] = $link['url'];
            }
        }

        if (empty($map['light_logo']) && !empty($map['logo'])) {
            $map['light_logo'] = $map['logo'];
        }

        if (empty($map['dark_logo']) && !empty($map['logo'])) {
            $map['dark_logo'] = $map['logo'];
        }

        if (empty($map['logo'])) {
            $map['logo'] = $map['light_logo'] ?: $map['dark_logo'];
        }

        return $map;
    }

    public $residentialParkId = 12;

    public $shortcodesOptions = [];

    public $selectedShortcodeOption = [];

    public $selectedApartmanNames = [];

    public $shortCodeApartmanName = '';

    public $shortcodeType = '';

    public $parkDistricts = [];

    public function __construct()
    {
        MibActivate::sync_rezideo_links_table_schema();

        $this->pluginPath = plugin_dir_path(dirname(__FILE__, 2));
        $this->pluginUrl = plugin_dir_url(dirname(__FILE__, 2));
        $this->pluginName = plugin_basename(dirname(__FILE__, 3)) . "/mib.php";
        $this->filterOptionDatas = maybe_unserialize(get_option('mib_filter_options'));
        $this->filterOptionCrossSellDatas = maybe_unserialize(get_option('mib_cross_sell_options'));

        // Adminon beállított residentialParkId
        $this->mibOptions = maybe_unserialize(get_option('mib_options'));
        $this->parkDistricts = isset($this->mibOptions['park_districts']) && is_array($this->mibOptions['park_districts'])
            ? $this->sanitizeParkDistricts($this->mibOptions['park_districts'])
            : [];
        $this->residentialParkId = isset($this->mibOptions['mib-residential-park-id']) ? $this->mibOptions['mib-residential-park-id'] : null;

        // Ha az admin felületen van beállítva ID, azt használjuk, ha nem akkor mylelle most
        $this->shortcodesOptions = maybe_unserialize(get_option('mib_custom_shortcodes'));
    }

    public function activated(string $key)
    {
        $option = get_option($key);
        return isset($option) ? $option : false;
    }

    public function get_shortcode_config_by_name($shortcode_name)
    {
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
            $currentParkId = (int) (
                !empty($this->residentialParkId)
                ? $this->residentialParkId
                : ($item->residentialPark->id ?? 0)
            );

            // Lekérjük az adatokat a get_attachments_by_meta_values függvényből
            $attachments = $this->get_attachments_by_meta_values(
                $item->name,
                $currentParkId
            );

            // [Rezideo Sync] Shadow Table Lookup & Merger
            // Fetch ALL Rezideo links (list of ['type'=>, 'url'=>])
            $rezideo_links = $this->get_rezideo_links($item->name, 'apartment', (int) $item->id, $currentParkId);

            // Merge them into the attachments array so the loop below processes them naturally
            // We PREPEND them so that WP Media Library items (already in $attachments) come later 
            // and overwrite them in the processing loop if duplicates exist. (WP > Shadow)
            if (!empty($rezideo_links)) {
                $rezideo_attachments = [];
                foreach ($rezideo_links as $r_link) {
                    $rezideo_attachments[] = [
                        'type' => $r_link['type'],
                        'media_type' => $r_link['media_type'],
                        'attachment_url' => $r_link['url']
                    ];
                }

                if (empty($attachments)) {
                    $attachments = $rezideo_attachments;
                } else {
                    $attachments = array_merge($rezideo_attachments, $attachments);
                }
            }

            $image = '';
            $szintrajz = '';
            $szintrajz_img = '';
            $szintrajz_url = ''; // Raw URL for link generation
            $alaprajz = '';
            $alaprajz_url = ''; // Raw URL for link generation
            $other_documents = [];
            $main_image = '';
            $alaprajz_image = '';
            $gallery_first = '';
            $siteplan_image = '';
            $docsynopsisimg = '';

            $useGalleryImage = (
                !empty($this->selectedShortcodeOption['extras']) &&
                in_array('gallery_first_image', $this->selectedShortcodeOption['extras'])
            );

            if (isset($item->apartmentsImages) && !empty($item->apartmentsImages) && empty($attachments)) {
                $i = 0;
                foreach ($item->apartmentsImages as $img) {

                    if (
                        //$useGalleryImage &&
                        empty($image) &&
                        isset($img->category) &&
                        $img->category === 'Gallery' &&
                        isset($img->src)
                    ) {
                        $image = $img->src;
                        if ($useGalleryImage && $i == 0) {
                            $szintrajz_img = $image;
                        }

                        if ($i == 0) {
                            $gallery_first = $img->src;
                        }
                    }

                    if (isset($img->category) && $img->category === 'Synopsis' && isset($img->src)) {
                        $szintrajz = '<a href="' . $img->src . '" target="_blank" rel="noopener">Szintrajz megtekintése</a>';
                        if (empty($szintrajz_img)) {
                            $szintrajz_img = $img->src;
                        }

                    }

                    if (isset($img->category) && $img->category === 'Main image' && isset($img->src)) {
                        $main_image = $img->src;
                    }

                    if (isset($img->category) && $img->category === 'Floorplan' && isset($img->src)) {
                        $alaprajz_image = $img->src;
                    }

                    if (isset($img->category) && $img->category === 'Siteplan' && isset($img->src)) {
                        $siteplan_image = $img->src;
                    }

                }


                if (isset($item->apartmentsDocuments) && !empty($item->apartmentsDocuments)) {
                    foreach ($item->apartmentsDocuments as $img) {
                        if (isset($img->category) && $img->category === 'Floorplan' && isset($img->src)) {
                            $alaprajz = '<a href="' . $img->src . '" target="_blank" rel="noopener">Alaprajz megtekintése</a>';
                            //break;
                        }
                        if (isset($img->category) && $img->category === 'Synopsis' && isset($img->src)) {

                            $docsynopsisimg = $img->preview;
                        }
                    }
                }
            }



            //print_r($alaprajz_image);
            //die($img->src);

            // Végigmegyünk az adatbázisból lekért csatolmányokon és frissítjük a megfelelő értékeket
            if (!empty($attachments)) {
                foreach ($attachments as $attachment) {
                    $url = $attachment['attachment_url'];

                    if (isset($attachment['media_type'])) {
                        $is_image = ($attachment['media_type'] === 'image');
                    } else {
                        $is_image = preg_match('/\.(jpg|jpeg|png|gif|webp)$/i', $url);
                    }

                    if ($attachment['type'] === 'alaprajz') {
                        $alaprajz_url = $url;
                        if ($is_image) {
                            $alaprajz = $url;
                            $alaprajz_image = $url;
                        } else {
                            // If it's a PDF/Document, still set it as the main download link
                            $alaprajz = '<a href="' . $url . '" target="_blank" rel="noopener">Alaprajz megtekintése</a>';
                        }
                    } elseif ($attachment['type'] === 'szintrajz') {
                        $szintrajz_url = $url;
                        if ($is_image) {
                            $szintrajz_img = $url;
                            // Only overwrite link if it's not set or if we want the image to be the default link
                            $szintrajz = '<a href="' . $url . '" target="_blank" rel="noopener">Szintrajz megtekintése</a>';
                        } else {
                            // If it's a PDF/Document, still set it as the main download link
                            $szintrajz = '<a href="' . $url . '" target="_blank" rel="noopener">Szintrajz megtekintése</a>';
                        }
                    } elseif ($attachment['type'] === 'lakas_kep') {
                        $image = $url;
                        if (empty($gallery_first)) {
                            $gallery_first = $url;
                        }
                    } elseif (in_array($attachment['type'], ['document', 'datasheet', 'other'])) {
                        $other_documents[] = [
                            'type' => $attachment['type'],
                            'url' => $url
                        ];
                    }
                }
            }

            // [Rezideo Sync] Shadow Table Lookup & Merger
            // Fetch ALL Rezideo links (list of ['type'=>, 'url'=>])
            $rezideo_links = $this->get_rezideo_links($item->name, 'apartment', (int) $item->id, $currentParkId);

            // Merge them into the attachments array so the loop below processes them naturally
            // This handles multiple gallery images, documents, etc.
            if (!empty($rezideo_links)) {
                if (empty($attachments)) {
                    $attachments = [];
                }
                foreach ($rezideo_links as $r_link) {
                    $attachments[] = [
                        'type' => $r_link['type'],
                        'attachment_url' => $r_link['url']
                    ];
                }
            }

            // [Rezideo Sync] Check for Direct URL overrides in Post Meta
            // $rezideo_alaprajz = get_po... (Legacy check removed/superseded by shadow table)

            if (!empty($rezideo_alaprajz)) {
                $alaprajz = $rezideo_alaprajz;
                $alaprajz_image = $rezideo_alaprajz;
            }

            $rezideo_szintrajz = get_post_meta($item->id, 'mib_rezideo_url_szintrajz', true);
            if (!empty($rezideo_szintrajz)) {
                $szintrajz_img = $rezideo_szintrajz;
                $szintrajz = '<a href="' . $rezideo_szintrajz . '" target="_blank" rel="noopener">Szintrajz megtekintése</a>';
            }

            $rezideo_lakas_kep = get_post_meta($item->id, 'mib_rezideo_url_lakas_kep', true);
            if (!empty($rezideo_lakas_kep)) {
                if (empty($image) || strpos($image, 'placeholder') !== false) {
                    $image = $rezideo_lakas_kep;
                }
                if (empty($gallery_first)) {
                    $gallery_first = $rezideo_lakas_kep;
                }
            }

            $host = parse_url(home_url(), PHP_URL_HOST);
            $parts = explode('.', $host);
            $projectSlug = (count($parts) >= 2) ? $parts[count($parts) - 2] : 'projekt';

            $otthonStart = false;
            $badgeUrl = '';

            $sale_white_badgeUrl = plugin_dir_url(dirname(__DIR__)) . 'assets/akcio_badge_feher.png';
            $sale_black_badgeUrl = plugin_dir_url(dirname(__DIR__)) . 'assets/akcio_badge_fekete.png';

            // Segédfüggvény: null / string → float
            $toFloat = function ($value) {
                return is_numeric($value) ? (float) $value : 0.0;
            };

            $netto = $toFloat($item->nettoFloorArea ?? null);

            $external =
                $toFloat($item->balconyFloorArea ?? null) +
                $toFloat($item->terraceFloorArea ?? null) +
                $toFloat($item->loggiaFloorArea ?? null);

            // Számított alapterület
            $calculatedArea = $netto + ($external * 0.5);

            if (!is_null($item->price) && $calculatedArea > 0) {

                $pricePerMeter = $item->price / $calculatedArea;

                // 3%-os Otthon Start feltételek
                if ($pricePerMeter <= 1500000 && $item->price < 100000000) {
                    $otthonStart = true;

                    if ($item->type === 'lakás') {
                        $badgeUrl = plugin_dir_url(dirname(__DIR__)) . 'assets/os.png';
                    }
                }
            }

            $priceDisplay = '';
            $supportedPriceDisplay = '';
            $discountPriceDisplay = '';
            $basePriceRaw = null;
            $discountPriceRaw = null;
            $sale_price_badge = '';

            $isRustZone = (
                isset($item->residentialPark) &&
                is_object($item->residentialPark) &&
                property_exists($item->residentialPark, 'isRustZone')
            ) ? (bool) $item->residentialPark->isRustZone : false;

            if (($item->status == 'Available' || $item->status == 'Reserved') && !is_null($item->price)) {
                $priceDisplay = number_format($item->price, 0) . ' Ft';
                $supportedPriceDisplay = ($item->type == 'lakás' && $isRustZone)
                    ? number_format($item->price / 1.05, 0) . ' Ft'
                    : '';
                $basePriceRaw = (int) $item->price;

                if (isset($item->discountPrice) && $item->discountPrice !== null && $item->discountPrice !== '' && is_numeric($item->discountPrice) && (float) $item->discountPrice > 0) {

                    $supportedPriceDisplay = number_format($item->discountPrice / 1.05, 0) . ' Ft';
                    $discountPriceDisplay = number_format((int) $item->discountPrice, 0, ',', ',') . ' Ft';
                    $discountPriceRaw = (int) $item->discountPrice;

                    $sale_price_badge = ($item->residentialPark->id == 43) ? $sale_white_badgeUrl : $sale_black_badgeUrl;
                }
            }

            $parkLogoUrls = $this->getResidentialParkLogoUrls($item->residentialPark, $currentParkId);

            $table_data[] = array(
                'id' => $item->id,
                'rawname' => $item->name,
                'name' => ($item->status == 'Sold')
                    ? '<a id="mibhre">' . esc_html($item->name) . '</a>'
                    : '<a id="mibhre" href="' . home_url('/lakas/' . $projectSlug . '/' . sanitize_title($item->name) . '/') . '">' . $item->name . '</a>',
                'url' => home_url('/lakas/' . $projectSlug . '/' . sanitize_title($item->name) . '/'),
                'numberOfRooms' => $item->numberOfRooms,
                'price' => !empty($discountPriceDisplay) ? $discountPriceDisplay : $priceDisplay,
                'originalPrice' => !empty($discountPriceDisplay) ? $priceDisplay : '',
                'discountPrice' => $discountPriceDisplay,
                'basePriceRaw' => $basePriceRaw,
                'discountPriceRaw' => $discountPriceRaw,
                'supportedPrice' => $supportedPriceDisplay,
                'isRustZone' => $isRustZone,
                'salesFloorArea' => $item->salesFloorArea . ' m²',
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
                'alaprajz_image' => $alaprajz_image,
                'alaprajz_url' => $alaprajz_url, // New raw URL
                'szintrajz' => $szintrajz, // Frissített szintrajz
                'szintrajz_img' => $szintrajz_img,
                'szintrajz_url' => $szintrajz_url, // New raw URL
                'docsynopsisimg' => $docsynopsisimg,
                'other_documents' => $other_documents,
                'siteplan_image' => $siteplan_image,
                'main_image' => $main_image,
                'gallery_first' => $gallery_first,
                'notes' => ($item->residentialPark->notes) ? $item->residentialPark->notes : '',
                'darkLogo' => ($parkLogoUrls['dark_logo'] ?? '') ?: ($parkLogoUrls['logo'] ?? ''),
                'logo' => (
                    (isset($this->filterOptionDatas['mib-dark_logo']) && $this->filterOptionDatas['mib-dark_logo'] == 1) ||
                    (!empty($this->selectedShortcodeOption['extras']) && in_array('dark_logo', $this->selectedShortcodeOption['extras']))
                )
                    ? (($parkLogoUrls['dark_logo'] ?? '') ?: ($parkLogoUrls['logo'] ?? ''))
                    : (($parkLogoUrls['light_logo'] ?? '') ?: ($parkLogoUrls['logo'] ?? '')),
                'address' => ($item->residentialPark->address) ? $item->residentialPark->address : '',
                'otthonStart' => $otthonStart,
                'otthonStartBadge' => $badgeUrl,
                'sale_price_badge' => $sale_price_badge,
                'rooms' => isset($item->rooms) && is_array($item->rooms) ? array_map(function ($room) {
                    return [
                        'category_name' => $room->category_name ?? '',
                        'floorArea' => $room->floorArea ?? ''
                    ];
                }, $item->rooms) : [],
            );
        }

        return $table_data;
    }

    public function get_attachments_by_meta_values($identifier, $park_id)
    {
        global $wpdb;

        // Lekérdezzük azokat az attachment ID-ket, amelyek megfelelnek az identifier, type és park_id feltételeknek
        $query = $wpdb->prepare("
            SELECT pm1.post_id
            FROM {$wpdb->postmeta} pm1
            INNER JOIN {$wpdb->postmeta} pm2 ON pm1.post_id = pm2.post_id
            INNER JOIN {$wpdb->postmeta} pm3 ON pm1.post_id = pm3.post_id
            WHERE pm1.meta_key = 'identifier'
            AND pm1.meta_value = %s
            AND pm2.meta_key = 'type'
            AND pm2.meta_value IN ('szintrajz', 'alaprajz', 'lakas_kep', 'document', 'datasheet', 'other', 'synopsis', 'floorplan')
            AND pm3.meta_key = 'park_id'
            AND pm3.meta_value = %s
        ", $identifier, $park_id);

        $post_ids = $wpdb->get_col($query);

        if (empty($post_ids)) {
            return [];
        }

        // Most lekérjük az attachment-ek URL-jeit
        $attachments = [];

        foreach ($post_ids as $post_id) {
            $attachments[] = [
                'attachment_id' => $post_id,
                'attachment_url' => wp_get_attachment_url($post_id),
                'type' => get_post_meta($post_id, 'type', true),
                'identifier' => get_post_meta($post_id, 'identifier', true),
                'property_id' => get_post_meta($post_id, 'property_id', true),
                'park_id' => get_post_meta($post_id, 'park_id', true),
            ];
        }

        return $attachments;
    }

    public function get_residential_park_attachments($identifier, $park_id): array
    {
        global $wpdb;

        $query = $wpdb->prepare("
            SELECT pm1.post_id
            FROM {$wpdb->postmeta} pm1
            INNER JOIN {$wpdb->postmeta} pm2 ON pm1.post_id = pm2.post_id
            WHERE pm1.meta_key = 'type'
            AND pm1.meta_value IN ('light_logo', 'dark_logo', 'logo', 'badge')
            AND pm2.meta_key = 'park_id'
            AND pm2.meta_value = %s
        ", $park_id);

        $post_ids = $wpdb->get_col($query);

        if (empty($post_ids) && !empty($identifier)) {
            $query = $wpdb->prepare("
                SELECT pm1.post_id
                FROM {$wpdb->postmeta} pm1
                INNER JOIN {$wpdb->postmeta} pm2 ON pm1.post_id = pm2.post_id
                INNER JOIN {$wpdb->postmeta} pm3 ON pm1.post_id = pm3.post_id
                WHERE pm1.meta_key = 'identifier'
                AND pm1.meta_value = %s
                AND pm2.meta_key = 'type'
                AND pm2.meta_value IN ('light_logo', 'dark_logo', 'logo', 'badge')
                AND pm3.meta_key = 'park_id'
                AND pm3.meta_value = %s
            ", $identifier, $park_id);

            $post_ids = $wpdb->get_col($query);
        }

        if (empty($post_ids)) {
            return [];
        }

        $attachments = [];

        foreach ($post_ids as $post_id) {
            $type = get_post_meta($post_id, 'type', true);
            $url = wp_get_attachment_url($post_id);

            if (!empty($type) && !empty($url)) {
                $attachments[$type] = $url;
            }
        }

        return $attachments;
    }

    public function get_post_id_by_property_id($property_id)
    {

        global $wpdb;

        $post_id = $wpdb->get_var($wpdb->prepare(
            "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'property_id' AND meta_value = %s ORDER BY meta_id DESC LIMIT 1",
            $property_id
        ));

        return $post_id ? intval($post_id) : null;
    }

    public function get_post_id_by_identifier($identifier)
    {
        global $wpdb;

        // 1. Try meta key 'identifier'
        $post_id = $wpdb->get_var($wpdb->prepare(
            "SELECT post_id FROM {$wpdb->postmeta} WHERE meta_key = 'identifier' AND meta_value = %s ORDER BY meta_id DESC LIMIT 1",
            $identifier
        ));

        if ($post_id) {
            return intval($post_id);
        }

        // 2. Try post_title (Exact match)
        $post_id = $wpdb->get_var($wpdb->prepare(
            "SELECT ID FROM {$wpdb->posts} WHERE post_title = %s AND post_status = 'publish' LIMIT 1",
            $identifier
        ));

        if ($post_id) {
            return intval($post_id);
        }

        // 3. Try post_name (slug)
        $slug = sanitize_title($identifier);
        $post_id = $wpdb->get_var($wpdb->prepare(
            "SELECT ID FROM {$wpdb->posts} WHERE post_name = %s AND post_status = 'publish' LIMIT 1",
            $slug
        ));

        return $post_id ? intval($post_id) : null;
    }

    public function get_rezideo_links($identifier, $entity_type = 'apartment', $entity_id = null, $park_id = null)
    {
        global $wpdb;
        $table_name = $wpdb->prefix . 'mib_rezideo_links';

        // Ensure table exists before querying to avoid errors on first run if not activated
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            return [];
        }

        $has_entity_columns = $wpdb->get_var("SHOW COLUMNS FROM $table_name LIKE 'entity_type'") === 'entity_type';

        if ($has_entity_columns && $entity_id !== null) {
            $results = $wpdb->get_results($wpdb->prepare(
                "SELECT type, media_type, url FROM $table_name WHERE entity_type = %s AND entity_id = %d",
                $entity_type,
                $entity_id
            ));
            if (empty($results) && !empty($identifier)) {
                $results = $wpdb->get_results($wpdb->prepare(
                    "SELECT type, media_type, url FROM $table_name WHERE identifier = %s",
                    $identifier
                ));
            }
        } elseif ($park_id !== null && $has_entity_columns && $entity_type === 'residential_park') {
            $results = $wpdb->get_results($wpdb->prepare(
                "SELECT type, media_type, url FROM $table_name WHERE entity_type = %s AND park_id = %d",
                $entity_type,
                $park_id
            ));
            if (empty($results) && !empty($identifier)) {
                $results = $wpdb->get_results($wpdb->prepare(
                    "SELECT type, media_type, url FROM $table_name WHERE identifier = %s",
                    $identifier
                ));
            }
        } else {
            $results = $wpdb->get_results($wpdb->prepare(
                "SELECT type, media_type, url FROM $table_name WHERE identifier = %s",
                $identifier
            ));
        }

        $links = [];
        foreach ($results as $row) {
            $links[] = [
                'type' => $row->type,
                'media_type' => $row->media_type ?? 'image', // Fallback to image if null
                'url' => $row->url
            ];
        }

        return $links;
    }

    public function get_candidate_posts($identifier)
    {
        global $wpdb;
        $like = '%' . $wpdb->esc_like($identifier) . '%';

        $results = $wpdb->get_results($wpdb->prepare(
            "SELECT ID, post_title, post_name, post_status, post_type FROM {$wpdb->posts} 
             WHERE (post_title LIKE %s OR post_name LIKE %s) 
             LIMIT 10",
            $like,
            $like
        ));

        return $results;
    }

    public function create_rezideo_attachment($post_id, $type, $url, $identifier, $park_id)
    {
        // Check if attachment with this URL already exists to avoid duplicates
        // Note: get_existing_attachment_by_type might not be available here directly if it's private in MibCustomEndpoint,
        // so we reimplement a basic check or just proceed. 
        // Actually, MibCustomEndpoint extends MibBaseController, but get_existing_attachment_by_type is in MibCustomEndpoint.
        // We should move it to Base or just use a simple check here.

        // Simple check by guid
        global $wpdb;
        $existing_id = $wpdb->get_var($wpdb->prepare(
            "SELECT ID FROM {$wpdb->posts} WHERE guid = %s AND post_type = 'attachment' LIMIT 1",
            $url
        ));

        if ($existing_id) {
            update_post_meta($existing_id, 'mib_rezideo_url_' . $type, $url);
            update_post_meta($post_id, 'mib_rezideo_url_' . $type, $url);
            return $existing_id;
        }

        // Create new attachment post
        if (!function_exists('wp_check_filetype')) {
            require_once ABSPATH . 'wp-includes/functions.php';
        }

        $filetype = wp_check_filetype(basename($url), null);
        $mime_type = $filetype['type'] ? $filetype['type'] : 'image/jpeg';

        $attachment = [
            'guid' => $url,
            'post_mime_type' => $mime_type,
            'post_title' => basename($url),
            'post_content' => '',
            'post_status' => 'inherit',
            'post_parent' => $post_id,
            'post_type' => 'attachment',
        ];

        // Insert attachment
        $attachment_id = wp_insert_attachment($attachment, $url, $post_id);

        if (!is_wp_error($attachment_id)) {
            // Update meta
            update_post_meta($attachment_id, 'type', $type);
            update_post_meta($attachment_id, 'identifier', $identifier);
            update_post_meta($attachment_id, 'park_id', $park_id);
            update_post_meta($attachment_id, 'mib_rezideo_url_' . $type, $url);

            // Also update parent apartment meta for direct access
            update_post_meta($post_id, 'mib_rezideo_url_' . $type, $url);
        }

        return $attachment_id;
    }

    public function getSingleApartmanHtml($data, $recommend = [])
    {

        $html = '';

        if (!empty($data)) {
            $data = $data[0];

            $formattedPrice = '';
            $formattedOriginalPrice = '';

            $newPriceRaw = !empty($data['discountPriceRaw']) ? $data['discountPriceRaw'] : ($data['basePriceRaw'] ?? null);

            if (!empty($newPriceRaw)) {
                $formattedPrice = number_format((int) $newPriceRaw, 0, ',', ' ') . ' Ft';
            } elseif (!empty($data['price'])) {
                $cleanPrice = preg_replace('/[^0-9]/', '', $data['price']);
                if (!empty($cleanPrice)) {
                    $formattedPrice = number_format((int) $cleanPrice, 0, ',', ' ') . ' Ft';
                }
            }

            if (!empty($data['discountPriceRaw']) && !empty($data['basePriceRaw'])) {
                $formattedOriginalPrice = number_format((int) $data['basePriceRaw'], 0, ',', ' ') . ' Ft';
            } elseif (!empty($data['originalPrice'])) {
                $cleanOriginal = preg_replace('/[^0-9]/', '', $data['originalPrice']);
                if (!empty($cleanOriginal)) {
                    $formattedOriginalPrice = number_format((int) $cleanOriginal, 0, ',', ' ') . ' Ft';
                }
            }

            $separationLabels = [
                'separate' => 'Különálló',
                'together' => 'Együtt',
                'separateAndTogether' => 'Együtt és különálló',
            ];

            $logo = '';
            $logo = '';
            if (isset($this->filterOptionDatas['mib-display_logo']) && !empty($data['darkLogo'])) {
                $cors = $this->getCorsAttribute($data['darkLogo']);
                $logo = '<img src="' . $data['darkLogo'] . '" alt="Logó" class="apartman-logo"' . $cors . '>';
            }

            $address = '';
            if (isset($this->filterOptionDatas['mib-display_address']) && $this->filterOptionDatas['mib-display_address'] == 1) {
                $address = $data['address'];
            }

            $html .= '<div class="apartment-box">';

            // Felső tartalom: alaprajz + jobb oldali adatok
            $html .= '<div class="apartment-top primary-color">';

            // Bal oldal: alaprajz
            $html .= '<div class="apartment-plan position-relative">';
            $cors = $this->getCorsAttribute($data['gallery_first']);
            $html .= '<img' . $cors . ' src="' . esc_url($data['gallery_first']) . '" alt="Lakás Kép">';
            if (!empty($data['otthonStartBadge'])) {
                $html .= '<img class="mib-otthonstart-badge" src="' . esc_url($data['otthonStartBadge']) . '" alt="Otthon Start" role="button" tabindex="0" />';
            }
            $html .= '</div>';

            // Jobb oldal
            $html .= '<div class="apartment-details">';

            // Logó és helyiség
            $html .= '<div class="apartment-logo-header">';
            if (!empty($logo)) {
                $html .= '<div class="park-logo">' . $logo . '</div>';
            }

            if (!empty($address)) {
                $html .= '<div class="location-name">' . esc_html($data['address'] ?? '—') . '</div>';
            }

            $html .= '</div>';

            // Adatok sötét háttéren
            $html .= '<div class="apartment-info-box">';
            $html .= '<div class="apartment-quickinfo responsive-grid">';
            $html .= '<div><strong class="third-text-color">Elérhető</strong><br><span class="quick-info-value">' . esc_html($data['rawname'] ?? '—') . '</span></div>';
            $html .= '<div><strong>Méret (m²)</strong><br><span class="quick-info-value">' . esc_html($data['salesFloorArea']) . '</span></div>';
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

            $html .= '<div class="apartment-price third-text-color">';
            if (!empty($formattedOriginalPrice)) {
                $html .= '<span class="mib-old-price">' . esc_html($formattedOriginalPrice) . '</span>';
            }
            if (!empty($formattedPrice)) {
                if (!empty($data['sale_price_badge'])) {
                    $html .= '<img id="saleimagebadge" src="' . $data['sale_price_badge'] . '" alt="Akciós lakás">';
                }
                $html .= '<span class="mib-new-price">' . esc_html($formattedPrice) . '</span>';
            }
            $html .= '</div>';

            if (!empty($data['supportedPrice']) && !empty($data['isRustZone'])) {
                $html .= '<div class="apartment-rustzone-flag">';
                $html .= '' . esc_html__('5% ÁFA visszaigényelhető! Ennyibe kerül neked:', 'mib') . '
                            <b>' . esc_html($data['supportedPrice']) . '</b></p>';
                $html .= '</div>';
            }
            $html .= '</div>'; // .apartment-info-box

            $html .= '</div>'; // .apartment-details

            $html .= '</div>'; // .apartment-top

            // Letöltések és infók
            $html .= '<div class="apartment-downloads">';
            $html .= '<div class="downloads-column">';

            if (!empty($data['szintrajz_img'])) {
                $html .= '<h4>Alaprajz</h4>';
                $floorplanUrl = esc_url($data['szintrajz_img']);
                $html .= '<a href="' . $floorplanUrl . '" class="mib-floorplan-link" data-elementor-open-lightbox="no">';
                $cors = $this->getCorsAttribute($floorplanUrl);
                $html .= '<img src="' . $floorplanUrl . '" alt="Logó"' . $cors . ' id="floorplanimg">';
                $html .= '</a>';
            }

            if (!empty($data['siteplan_image'])) {
                $html .= '<h4>Helyszín rajz</h4>';
                $floorplanUrl = esc_url($data['siteplan_image']);
                $html .= '<a href="' . $floorplanUrl . '" class="mib-floorplan-link" data-elementor-open-lightbox="no">';
                $cors = $this->getCorsAttribute($floorplanUrl);
                $html .= '<img src="' . $floorplanUrl . '" alt="Logó"' . $cors . ' id="floorplanimg">';
                $html .= '</a>';
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



            $html .= '<h4>Letölthető dokumentumok</h4>';

            // A fenti képek (alaprajz, szintrajz, helyszínrajz) már megjelennek grafikusan,
            // így a szöveges listából kivesszük őket, hogy ne legyenek duplikációk.

            // Ellenőrizzük, hogy van-e már ilyen típusú dokumentum a listában
            $has_alaprajz_doc = false;
            $has_szintrajz_doc = false;

            if (!empty($data['other_documents'])) {
                foreach ($data['other_documents'] as $doc) {
                    if ($doc['type'] === 'alaprajz')
                        $has_alaprajz_doc = true;
                    if ($doc['type'] === 'szintrajz')
                        $has_szintrajz_doc = true;
                }
            }

            // ---------------------------------------------------------
            // ---------------------------------------------------------
            // 1. "Alaprajz megtekintése" Logic
            // ---------------------------------------------------------
            $alaprajz_candidate = '';

            // SWAPPED LOGIC: We want the PDF (which is usually Synopsis/Szintrajz type in sync) to appear here.
            // So we look for SZINTRAJZ data for the ALAPRAJZ link.

            // 0. Priorities: szintrajz_url > szintrajz_img (WP) > Legacy Cross-link
            if (!empty($data['szintrajz_url'])) {
                $alaprajz_candidate = $data['szintrajz_url'];
            }
            // A) ÚJ (WP Media) elem esetén:
            elseif (!empty($data['szintrajz_img']) && strpos($data['szintrajz_img'], '/wp-content/uploads/') !== false) {
                $alaprajz_candidate = $data['szintrajz_img'];
            }
            // B) RÉGI (Legacy) elem esetén:
            elseif (!empty($data['alaprajz_image']) && strpos($data['alaprajz_image'], '/wp-content/uploads/') === false) {
                // Here we keep the cross-wired logic because it was "doc or szintrajz_img".
                // Since we are swapping, we want the "other" one.
                // Original for Alaprajz was: docsynopsisimg OR szintrajz_img.
                // Now we want the PDF here.
                $alaprajz_candidate = (!empty($data['docsynopsisimg'])) ? $data['docsynopsisimg'] : $data['szintrajz_img'];
            }

            // Ha találtunk Alaprajz URL-t (ami most a PDF/Szintrajz adat), akkor megjelenítjük
            if (!empty($alaprajz_candidate) && !$has_alaprajz_doc) {
                // User explicit request: NO lightbox, just blank new tab.
                $html .= '<a href="' . $alaprajz_candidate . '" target="_blank" rel="noopener" class="" data-elementor-open-lightbox="no">Alaprajz megtekintése</a><br/>';
            }

            // ---------------------------------------------------------
            // 2. "Szintrajz megtekintése" Logic
            // ---------------------------------------------------------
            $szintrajz_candidate = '';

            // SWAPPED LOGIC: We want the IMAGE (which is usually Floorplan/Alaprajz type in sync) to appear here.
            // So we look for ALAPRAJZ data for the SZINTRAJZ link.

            // 0. Priorities: alaprajz_url > alaprajz_image (WP) > Legacy Cross-link
            if (!empty($data['alaprajz_url'])) {
                $szintrajz_candidate = $data['alaprajz_url'];
            }
            // A) ÚJ (WP Media) elem esetén:
            elseif (!empty($data['alaprajz_image']) && strpos($data['alaprajz_image'], '/wp-content/uploads/') !== false) {
                $szintrajz_candidate = $data['alaprajz_image'];
            }
            // B) RÉGI (Legacy) elem esetén:
            elseif (!empty($data['szintrajz_img']) && strpos($data['szintrajz_img'], '/wp-content/uploads/') === false) {
                // Original for Szintrajz was: alaprajz_image.
                // Now we want the Image here.
                $szintrajz_candidate = $data['alaprajz_image'];
            }

            // Ha találtunk Szintrajz URL-t (ami most az Alaprajz/Kép adat), akkor megjelenítjük
            if (!empty($szintrajz_candidate) && !$has_szintrajz_doc) {
                // User explicit request: NO lightbox, just blank new tab.
                $html .= '<a href="' . $szintrajz_candidate . '" target="_blank" rel="noopener" class="" data-elementor-open-lightbox="no">Szintrajz megtekintése</a><br/>';
            }

            if (!empty($data['siteplan_image'])) {
                $html .= '<a href="' . $data['siteplan_image'] . '" target="_blank" rel="noopener" data-elementor-open-lightbox="no">Helyszínrajz megtekintése</a><br/>';
            }

            if (!empty($data['other_documents'])) {
                foreach ($data['other_documents'] as $doc) {
                    $label = 'Dokumentum megtekintése';
                    if ($doc['type'] === 'datasheet')
                        $label = 'Adatlap megtekintése';
                    if ($doc['type'] === 'other')
                        $label = 'Egyéb dokumentum';
                    if ($doc['type'] === 'alaprajz')
                        $label = 'Alaprajz megtekintése';
                    if ($doc['type'] === 'szintrajz')
                        $label = 'Szintrajz megtekintése';

                    $html .= '<a href="' . $doc['url'] . '" target="_blank" rel="noopener">' . $label . '</a><br/>';
                }
            }


            $html .= '</div>';
            $html .= '</div>';




            // Ajánlott
            $html .= $this->getRecommendedApartmentsHtml($recommend);

            $html .= '</div>'; // .apartment-box

        } else {
            $html .= '<p>Nem található lakás ezzel az ID-vel</p>';
        }

        return $html;
    }

    public function getRecommendedApartmentsHtml($recommended)
    {

        $html = '';
        if (!empty($recommended)) {

            $html = '<div><h2 id="recommended-h4">Ajánlott ingatlanok</h2></div><div class="recommended-apartments">';
            foreach ($recommended as $apartment) {

                $html .= '<div class="recommended-apartment">';
                $html .= '<a href="/lakas/?id=' . $apartment['id'] . '">';
                $cors = $this->getCorsAttribute($apartment['image']);
                $html .= '<img' . $cors . ' src="' . $apartment['image'] . '" alt="' . esc_attr($apartment['name']) . '" class="recommended-image">';
                $html .= '<h3 class="recommended-title">' . esc_html($apartment['name']) . '</h3>';
                $priceHtml = 'Ár: ';
                if (!empty($apartment['originalPrice'])) {
                    $priceHtml .= '<span class="mib-old-price">' . esc_html($apartment['originalPrice']) . '</span>';
                }
                $priceHtml .= '<span class="mib-new-price">' . esc_html($apartment['price']) . '</span>';
                $html .= '<p class="recommended-price">' . $priceHtml . '</p>';
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

            if (isset($this->filterOptionDatas['mib-filter-floor']) && $this->filterOptionDatas['mib-filter-floor'] == true) {
                $html .= $this->getFilterFloor($filterType);
            }

            if (isset($this->filterOptionDatas['mib-filter-room']) && $this->filterOptionDatas['mib-filter-room'] == true) {
                $html .= $this->getFilterRoom($filterType);
            }

            if (isset($this->filterOptionDatas['mib-filter-orientation']) && $this->filterOptionDatas['mib-filter-orientation'] == true) {
                $html .= $this->getFilterOrientation($filterType);
            }

            if (isset($this->filterOptionDatas['mib-filter-availability']) && $this->filterOptionDatas['mib-filter-availability'] == true) {
                $html .= $this->getFilterAvailability($filterType);
            }

            if (isset($this->filterOptionDatas['mib-filter-square-meter']) && $this->filterOptionDatas['mib-filter-square-meter'] == true) {
                $html .= $this->squareFilters($filterType);
            }

            if (isset($this->filterOptionDatas['mib-filter-price_range']) && $this->filterOptionDatas['mib-filter-price_range'] == true) {

                $html .= $this->priceFilters($filterType);
            }

            if (isset($this->filterOptionDatas['mib-filter-deletefilters']) && $this->filterOptionDatas['mib-filter-deletefilters'] == true) {
                $html .= $this->deleteFilters($filterType);
            }
        }

        $html .= '</div>

		    </div>';

        //$html .= $this->getFilterFloor($filterType);
        // Start building the HTML for the table.
        // Display total count returned by API
        $html .= '<p id="mib-total-count" class="mb-2">' . sprintf(__('Találatok száma: %d', 'mib'), $totalItems) . '</p>';
        // Sorting options (AJAX)
        $sort_labels = [
            'name' => __('Név', 'mib'),
            'price' => __('Ár', 'mib'),
            'salesFloorArea' => __('Méret', 'mib'),
            'numberOfRooms' => __('Szobaszám', 'mib'),
        ];
        // Determine current sort from AJAX parameters
        $current_sort = $filterType['sort'] ?? '';
        $current_type = isset($filterType['sortType']) ? strtoupper($filterType['sortType']) : '';
        $html .= '<div class="mb-3 d-flex align-items-center">';
        $html .= '<label for="mib-sort-select" class="me-2">' . __('Rendezés:', 'mib') . '</label>';
        $html .= '<select id="mib-sort-select" class="form-select form-select-sm" style="width:auto;">';
        $html .= '<option value="">' . __('-- Rendezés --', 'mib') . '</option>';
        foreach ($sort_labels as $key => $label) {
            $sel_asc = ($current_sort === $key && $current_type === 'ASC') ? ' selected' : '';
            $sel_desc = ($current_sort === $key && $current_type === 'DESC') ? ' selected' : '';
            $html .= '<option value="' . esc_attr("{$key}|ASC") . '"' . $sel_asc . '>' . esc_html($label) . ' ↑</option>';
            $html .= '<option value="' . esc_attr("{$key}|DESC") . '"' . $sel_desc . '>' . esc_html($label) . ' ↓</option>';
        }
        $html .= '</select>';
        $html .= '</div>';
        $html .= '<table class="custom-list-table">';
        $html .= '<thead>';
        $html .= '<tr id="mibheadertr" class="secondary-color">';
        $html .= '<th id="mibheaderth" class="secondary-color">' . __('Lakás', 'mib') . '</th>';
        $html .= '<th id="mibheaderth" class="secondary-color">' . __('Méret', 'mib') . '</th>';
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
                $html .= '<td id="mibtd">' . $data['name'] . '</td>';
                $html .= '<td id="mibtd">' . esc_html($data['salesFloorArea']) . '</td>';
                $html .= '<td id="mibtd">' . esc_html($data['numberOfRooms']) . '</td>';
                $html .= '<td id="mibtd">' . esc_html($data['floor']) . '</td>';
                $html .= '<td id="mibtd">' . esc_html($data['orientation']) . '</td>';
                $html .= '<td id="mibtd">' . esc_html($data['price']) . '</td>';
                $html .= '<td id="mibtd"> ' . $data['status'] . '</td>';
                $html .= '</tr>';
            }

        } else {

            $html .= '<tr> <td> <p><b> Nem található ingatlan </b></p> </td></tr>';

        }

        $html .= '</tbody>';
        $html .= '</table>';

        $html .= $this->getPaginate($currentPage, $totalItems, 50);

        $html .= '</div>';


        return $html;

    }

    public function getCardHtmlShortCode($datas, $totalItems, $currentPage, $filterType = [], $shortcodeName = '', $apartman_number = 9)
    {

        $extras = (isset($filterType['extras']) && is_array($filterType['extras'])) ? $filterType['extras'] : [];
        $infiniteScrollEnabled = in_array('infinite_scroll', $extras);
        $loadMoreEnabled = in_array('load_more', $extras) || $infiniteScrollEnabled;
        $itemsPerPage = (is_numeric($apartman_number) && (int) $apartman_number > 0)
            ? (int) $apartman_number
            : (int) $this->numberOfApartmens;
        $totalPages = ($itemsPerPage > 0) ? (int) ceil($totalItems / $itemsPerPage) : 0;

        $containerAttributes = [
            'id="custom-card-container"',
            'class="row shortcode-card"',
            'data-shortcode="' . esc_attr($shortcodeName) . '"',
            'data-apartman_number="' . esc_attr($apartman_number) . '"'
        ];

        if ($infiniteScrollEnabled) {
            $containerAttributes[] = 'data-infinite-scroll="1"';
        }

        if ($totalPages > 0) {
            $containerAttributes[] = 'data-total-pages="' . $totalPages . '"';
            $containerAttributes[] = 'data-current-page="' . (int) $currentPage . '"';
        }

        $html = '<div ' . implode(' ', $containerAttributes) . '>';

        $html .= '<div id="mib-spinner" class="mib-spinner spinner-border text-dark m-3" role="status">
	              <span class="visually-hidden">Töltés...</span>
	            </div>';

        $html .= '<div class="custom-filter-container">
	                <div class="d-flex">';

        // Filterek megjelenítése
        if (!empty($filterType)) {

            if (in_array('use_number_inputs', $filterType['extras'])) {
                echo '';
            } else {

                if (count($filterType['residential_park_ids']) > 1) {
                    $html .= $this->getFilterResidentalParksShortCodeByCatalog($filterType['residential_park_ids']);
                }
                if (in_array('price', $filterType['filters'])) {
                    $html .= $this->getFilterPriceShortCodeByCatalog($filterType['ranges']['price']['min'], $filterType['ranges']['price']['max']);
                }

                if (in_array('room', $filterType['filters'])) {
                    $html .= $this->getFilterRoomShortCodeByCatalog($filterType['ranges']['room']['min'], $filterType['ranges']['room']['max']);
                }

                if (in_array('area', $filterType['filters'])) {

                    $html .= $this->getFilterAreaShortCodeByCatalog($filterType['ranges']['area']['min'], $filterType['ranges']['area']['max']);
                }

                $html .= '<div class="mb-2" id="parksfilter">';

                if (in_array('orientation_filters', $filterType['extras']) || in_array('available_only', $filterType['extras']) || in_array('garden_connection_filter', $filterType['extras']) || in_array('staircase_filter', $filterType['extras']) || in_array('otthon_start_filter', $filterType['extras']) || in_array('district_filter', $filterType['extras']) || in_array('discount_price_filter', $filterType['extras'])) {
                    $html .= '<button type="button" class="btn btn-outline-secondary btn-sm" id="toggle-advanced-filters">';
                    $html .= '<i class="fas fa-sliders-h me-1"></i> További szűrők';
                    $html .= '</button>';

                }
                $html .= '</div>';


            }
        }

        $html .= '</div>';

        $html .= '</div>';


        $html .= '<div class="custom-filter-container">';




        $html .= '<div id="advanced-filters" class="flex-wrap" style="display:none;">';

        if (in_array('floor', $filterType['filters'])) {

            $html .= $this->getFilterFloorShortCodeByCatalog($filterType['ranges']['floor']['min'], $filterType['ranges']['floor']['max']);
        }
        // Kerület szűrő
        if (in_array('district_filter', $filterType['extras'])) {
            $html .= $this->getFilterDistrictByCatalog($filterType);
        }

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

        if (in_array('otthon_start_filter', $filterType['extras'])) {
            $html .= $this->getFilterOtthonStartByCatalog($filterType);
        }

        if (in_array('discount_price_filter', $filterType['extras'])) {
            $html .= $this->getFilterDiscountPriceCheckboxByCatalog($filterType);
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
            if (!empty($filterType) && in_array('reset_filters', $filterType['extras'])) {

                $resetFilters = '<div class="reset-filters-wrapper ms-3">
			    					<button id="reset-filters-button" class="btn btn-outline-secondary d-flex align-items-center gap-2">
			    						<i class="fas fa-undo-alt"></i> Szűrők törlése
			    					</button>
			    				</div>';
            }

            $html .= '<div id="view-toggle" class="d-flex justify-content-end mb-3 gap-2">
	    				' . $resetFilters . '
					    <button id="grid-view" class="btn btn-outline-secondary active" title="Rács nézet">
					        <i class="fas fa-th-large"></i>
					    </button>
					    <button id="list-view" class="btn btn-outline-secondary" title="Lista nézet">
					        <i class="fas fa-bars"></i>
					    </button>
					    ' . $favorites . '
					</div>';

            // Display total count returned by API
            $html .= '<p id="mib-total-count" class="mb-3">' . sprintf(__('Találatok száma: %d', 'mib'), $totalItems) . '</p>';
            // Sorting options (AJAX)
            $sort_labels = [
                'name' => __('Név', 'mib'),
                'price' => __('Ár', 'mib'),
                'salesFloorArea' => __('Méret', 'mib'),
                'numberOfRooms' => __('Szobaszám', 'mib'),
            ];
            // Determine current sort settings
            $current_sort = $filterType['sort'] ?? '';
            $current_type = isset($filterType['sortType']) ? strtoupper($filterType['sortType']) : '';

            if (in_array('sort_filter', $filterType['extras'])) {

                $html .= '<div class="mb-3 d-flex align-items-center">';
                $html .= '<label for="mib-sort-select" class="me-2">' . __('Rendezés:', 'mib') . '</label>';
                $html .= '<select id="mib-sort-select" class="form-select form-select-sm" style="width:auto;">';
                $html .= '<option value="">' . __('-- Rendezés --', 'mib') . '</option>';
                foreach ($sort_labels as $key => $label) {
                    $sel_asc = ($current_sort === $key && $current_type === 'ASC') ? ' selected' : '';
                    $sel_desc = ($current_sort === $key && $current_type === 'DESC') ? ' selected' : '';
                    $html .= '<option value="' . esc_attr("{$key}|ASC") . '"' . $sel_asc . '>' . esc_html($label) . ' ↑</option>';
                    $html .= '<option value="' . esc_attr("{$key}|DESC") . '"' . $sel_desc . '>' . esc_html($label) . ' ↓</option>';
                }
                $html .= '</select>';
                $html .= '</div>';

            }

            foreach ($datas as $data) {

                $logo = '';
                if (in_array('display_logo', $filterType['extras']) && !empty($data['logo'])) {
                    $cors = $this->getCorsAttribute($data['logo']);
                    $logo = '<img src="' . $data['logo'] . '"' . $cors . '>';
                }

                $address = '';
                if (in_array('display_address', $filterType['extras'])) {
                    $address = $data['address'];
                }

                $html .= '<div class="card-wrapper col-md-4 mb-3" data-id="' . esc_attr($data['id']) . '" data-otthon-start="' . ($data['otthonStart'] ? 1 : 0) . '">'; // 4 oszlopos grid
                $html .= '<div class="card h-100 position-relative">'; // A kártyát relatív pozicionáljuk

                // Kép wrapper, flexbox középre igazítással
                $html .= '<div class="primary-color card-image-wrapper">';
                $html .= $this->renderApartmentListingImage($data);
                if (!empty($data['otthonStartBadge'])) {
                    $html .= '<img class="mib-otthonstart-badge" src="' . esc_url($data['otthonStartBadge']) . '" alt="Otthon Start" role="button" tabindex="0" />';
                }
                $html .= '</div>';

                $html .= '<div id="apartment-card-body" class="secondary-color card-body d-flex flex-column justify-content-between text-white">';

                // Cím és emelet
                $html .= '<div class="mb-3">';
                $html .= '<div class="d-flex justify-content-between">';

                if (!empty($logo)) {
                    $html .= '<div>';
                    $html .= '<div class="park-logo">' . $logo . '</div>';
                    $html .= '<strong>' . $address . '</strong>';
                    $html .= '</div>';
                }


                $html .= '<div>';
                $html .= '<small class="third-text-color ' . $data['statusclass'] . ' d-block">' . $data['statusrow'] . '</small>';
                $html .= '<strong class="fs-5 apartman-id">' . esc_html($data['rawname']) . '</strong>';
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
                $html .= '<small class="d-block text-muted">Méret (m2)</small>';
                $html .= '<strong class="fs-5">' . esc_html($data['salesFloorArea']) . '</strong>';
                $html .= '</div>';
                $html .= '<div class="text-end">';
                $html .= '<small class="d-block text-muted">Emelet</small>';
                $html .= '<strong class="fs-5">' . esc_html($data['floor']) . '</strong>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '<hr>';
                $html .= '</div>';

                // Ár
                $html .= '<div class="list-view-price-container mt-2 mt-md-0" style="display: contents; align-items: center; gap: 10px;">';
                if (!empty($data['originalPrice'])) {
                    $html .= '<span class="mib-old-price">' . esc_html($data['originalPrice']) . '</span>';
                }
                if (!empty($data['sale_price_badge'])) {
                    $html .= '<img id="saleimagebadge" src="' . $data['sale_price_badge'] . '" alt="Akciós lakás">';
                }
                $html .= '<strong class="fs-4 text-success third-text-color mib-new-price">' . esc_html($data['price']) . '</strong>';
                $html .= '</div>';

                if (!empty($filterType['extras']) && in_array('display_supported_price', $filterType['extras']) && !empty($data['supportedPrice']) && !empty($data['isRustZone'])) {
                    $html .= '<div class="mib-supported-price">';
                    $html .= '<span class="mib-supported-price-label">' . esc_html__('5% ÁFA visszaigényelhető! Ennyibe kerül neked:', 'mib') . '</span>';
                    $html .= '<span class="mib-supported-price-value">' . esc_html($data['supportedPrice']) . '</span>';
                    $html .= '</div>';
                }

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
                } else {

                    $html .= '<div style="margin-bottom: 80px;"></div>';
                }


                $html .= '</div>'; // card-body vége
                $html .= '</div>'; // card vége
                $html .= '</div>'; // col-md-4 vége
            }

        } else {
            $html .= '<div class="col-12"><p><b> Nem található ingatlan </b></p></div>';
        }

        if ($loadMoreEnabled) {
            if ($infiniteScrollEnabled && $totalPages > $currentPage) {
                $html .= '<div class="mib-infinite-scroll-sentinel" data-loading="0"></div>';
                $html .= $this->getLoadMoreButton($currentPage, $totalItems, $itemsPerPage, true);
            } elseif (!$infiniteScrollEnabled) {
                $html .= $this->getLoadMoreButton($currentPage, $totalItems, $itemsPerPage);
            }
        } else {
            $html .= $this->getPaginate($currentPage, $totalItems, $itemsPerPage);
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

        $html .= $this->getCatalogFilterHtml($filterType);

        //mib-stairway

        // Display total count returned by API
        $html .= '<p id="mib-total-count" class="mb-3">' . sprintf(__('Találatok száma: %d', 'mib'), $totalItems) . '</p>';
        // Sorting options (AJAX)
        $sort_labels = [
            'name' => __('Név', 'mib'),
            'price' => __('Ár', 'mib'),
            'salesFloorArea' => __('Méret', 'mib'),
            'numberOfRooms' => __('Szobaszám', 'mib'),
        ];
        // Determine current sort settings
        $current_sort = $filterType['sort'] ?? '';
        $current_type = isset($filterType['sortType']) ? strtoupper($filterType['sortType']) : '';

        if (isset($this->filterOptionDatas['mib-display_sort']) && !empty($this->filterOptionDatas['mib-display_sort']) != 0) {

            $html .= '<div class="mb-3 d-flex align-items-center">';
            $html .= '<label for="mib-sort-select" class="me-2">' . __('Rendezés:', 'mib') . '</label>';
            $html .= '<select id="mib-sort-select" class="form-select form-select-sm" style="width:auto;">';
            $html .= '<option value="">' . __('-- Rendezés --', 'mib') . '</option>';
            foreach ($sort_labels as $key => $label) {
                $sel_asc = ($current_sort === $key && $current_type === 'ASC') ? ' selected' : '';
                $sel_desc = ($current_sort === $key && $current_type === 'DESC') ? ' selected' : '';
                $html .= '<option value="' . esc_attr("{$key}|ASC") . '"' . $sel_asc . '>' . esc_html($label) . ' ↑</option>';
                $html .= '<option value="' . esc_attr("{$key}|DESC") . '"' . $sel_desc . '>' . esc_html($label) . ' ↓</option>';
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
                    $cors = $this->getCorsAttribute($data['logo']);
                    $logo = '<img src="' . $data['logo'] . '"' . $cors . '>';
                }

                $address = '';
                if (isset($this->filterOptionDatas['mib-display_address']) && $this->filterOptionDatas['mib-display_address'] == 1) {
                    $address = $data['address'];
                }

                $html .= '<div class="card-wrapper col-md-4 mb-3" data-otthon-start="' . ($data['otthonStart'] ? 1 : 0) . '">'; // 4 oszlopos grid
                $html .= '<div class="card h-100 position-relative">'; // A kártyát relatív pozicionáljuk

                // Kép wrapper, flexbox középre igazítással
                $html .= '<div class="primary-color card-image-wrapper">';
                $html .= $this->renderApartmentListingImage($data);
                if (!empty($data['otthonStartBadge'])) {
                    $html .= '<img class="mib-otthonstart-badge" src="' . esc_url($data['otthonStartBadge']) . '" alt="Otthon Start" role="button" tabindex="0" />';
                }
                $html .= '</div>';

                $html .= '<div id="apartment-card-body" class="secondary-color card-body d-flex flex-column justify-content-between text-white">';

                // Cím és emelet
                $html .= '<div class="mb-3">';
                $html .= '<div class="d-flex justify-content-between">';

                if (!empty($logo)) {
                    $html .= '<div>';
                    $html .= '<div class="park-logo">' . $logo . '</div>';
                    $html .= '<strong>' . $address . '</strong>';
                    $html .= '</div>';
                }

                $html .= '<div>';
                $html .= '<small class="third-text-color ' . $data['statusclass'] . ' d-block">' . $data['statusrow'] . '</small>';
                $html .= '<strong class="fs-5 apartman-id">' . esc_html($data['rawname']) . '</strong>';
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
                $html .= '<small class="d-block text-muted">Méret (m2)</small>';
                $html .= '<strong class="fs-5">' . esc_html($data['salesFloorArea']) . '</strong>';
                $html .= '</div>';
                $html .= '<div class="text-end">';
                $html .= '<small class="d-block text-muted">Emelet</small>';
                $html .= '<strong class="fs-5">' . esc_html($data['floor']) . '</strong>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '<hr>';
                $html .= '</div>';

                // Ár
                $html .= '<div class="list-view-price-container mt-2 mt-md-0" style="display: contents; align-items: center; gap: 10px;">';
                if (!empty($data['originalPrice'])) {
                    $html .= '<span class="mib-old-price">' . esc_html($data['originalPrice']) . '</span>';
                }
                if (!empty($data['sale_price_badge'])) {
                    $html .= '<img id="saleimagebadge" src="' . $data['sale_price_badge'] . '" alt="Akciós lakás">';
                }
                $html .= '<strong class="fs-4 text-success third-text-color mib-new-price">' . esc_html($data['price']) . '</strong>';


                if (
                    !empty($this->filterOptionDatas['mib-display_supported_price'])
                    && (int) $this->filterOptionDatas['mib-display_supported_price'] === 1
                    && !empty($data['supportedPrice'])
                    && !empty($data['isRustZone'])
                ) {
                    $html .= '<div class="mib-supported-price">';
                    $html .= '<span class="mib-supported-price-label">' . esc_html__('5% ÁFA visszaigényelhető! Ennyibe kerül neked:', 'mib') . '</span>';
                    $html .= '<span class="mib-supported-price-value">' . esc_html($data['supportedPrice']) . '</span>';
                    $html .= '</div>';
                }

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
                } else {

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

            $html .= $this->getLoadMoreButton($currentPage, $totalItems, 9);

        } else {

            $html .= $this->getPaginate($currentPage, $totalItems, 9);
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
                if (isset($this->filterOptionDatas['mib-display_logo']) && $this->filterOptionDatas['mib-display_logo'] == 1 && !empty($data['logo']) && $this->filterType === null) {
                    $cors = $this->getCorsAttribute($data['logo']);
                    $logo = '<img src="' . $data['logo'] . '"' . $cors . '>';
                } elseif (isset($this->filterType['extras']) && in_array('display_logo', $this->filterType['extras']) && !empty($data['logo'])) {
                    $cors = $this->getCorsAttribute($data['logo']);
                    $logo = '<img src="' . $data['logo'] . '"' . $cors . '>';
                }

                $address = '';
                if (isset($this->filterOptionDatas['mib-display_address']) && $this->filterOptionDatas['mib-display_address'] == 1) {
                    $address = $data['address'];
                } elseif (isset($filterType) && in_array('display_address', $filterType['extras'])) {
                    $address = $data['address'];
                }

                $html .= '<div class="card-wrapper col-md-4 mb-3" data-id="' . esc_attr($data['id']) . '" data-otthon-start="' . ($data['otthonStart'] ? 1 : 0) . '">'; // 4 oszlopos grid
                $html .= '<div class="card h-100 position-relative">'; // A kártyát relatív pozicionáljuk

                // Kép wrapper, flexbox középre igazítással
                $html .= '<div class="primary-color card-image-wrapper">';
                $html .= $this->renderApartmentListingImage($data);
                if (!empty($data['otthonStartBadge'])) {
                    $html .= '<img class="mib-otthonstart-badge" src="' . esc_url($data['otthonStartBadge']) . '" alt="Otthon Start" role="button" tabindex="0" />';
                }
                $html .= '</div>';

                $html .= '<div id="apartment-card-body" class="secondary-color card-body d-flex flex-column justify-content-between text-white">';

                // Cím és emelet
                $html .= '<div class="mb-3">';
                $html .= '<div class="d-flex justify-content-between">';

                if (!empty($logo)) {
                    $html .= '<div>';
                    $html .= '<div class="park-logo">' . $logo . '</div>';
                    $html .= '<strong>' . $address . '</strong>';
                    $html .= '</div>';
                }

                $html .= '<div>';
                $html .= '<small class="third-text-color ' . $data['statusclass'] . ' d-block">' . $data['statusrow'] . '</small>';
                $html .= '<strong class="fs-5 apartman-id">' . esc_html($data['rawname']) . '</strong>';
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
                $html .= '<small class="d-block text-muted">Méret (m2)</small>';
                $html .= '<strong class="fs-5">' . esc_html($data['salesFloorArea']) . '</strong>';
                $html .= '</div>';
                $html .= '<div class="text-end">';
                $html .= '<small class="d-block text-muted">Emelet</small>';
                $html .= '<strong class="fs-5">' . esc_html($data['floor']) . '</strong>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '<hr>';
                $html .= '</div>';

                // Ár
                $html .= '<div class="list-view-price-container mt-2 mt-md-0">';
                if (!empty($data['originalPrice'])) {
                    $html .= '<span class="mib-old-price">' . esc_html($data['originalPrice']) . '</span>';
                }
                if (!empty($data['sale_price_badge'])) {
                    $html .= '<img id="saleimagebadge" src="' . $data['sale_price_badge'] . '" alt="Akciós lakás">';
                }
                $html .= '<strong class="fs-4 text-success third-text-color mib-new-price">' . esc_html($data['price']) . '</strong>';

                // Supported price (5% ÁFA visszaigényelhető)

                $html .= '</div>';

                if (!empty($data['supportedPrice']) && !empty($data['isRustZone'])) {
                    $html .= '<div class="mib-supported-price">';
                    $html .= '<span class="mib-supported-price-label">' . esc_html__('5% ÁFA visszaigényelhető! Ennyibe kerül neked:', 'mib') . '</span>';
                    $html .= '<span class="mib-supported-price-value">' . esc_html($data['supportedPrice']) . '</span>';
                    $html .= '</div>';
                }

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
                } else {

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

    public function getCarouselHtml($datas, $shortcodeName = '', $apartman_number = 12)
    {
        // Külső wrapper: ebbe kerül a Swiper és KÍVÜLRE a nyilak/pagination
        $html = '<div class="mib-carousel-outer" data-shortcode="' . esc_attr($shortcodeName) . '" data-apartman_number="' . esc_attr($apartman_number) . '" data-page="1">';

        // Belső Swiper konténer
        $html .= '  <div class="mib-property-carousel swiper">';
        $html .= '    <div class="swiper-wrapper">';

        if (!empty($datas)) {
            $otthonStartFilterUrl = esc_url(home_url('/lakaslista/?otthonStart=1&fromSlider=1'));
            foreach ($datas as $data) {

                $logo = '';
                if (isset($this->filterOptionDatas['mib-display_logo']) && $this->filterOptionDatas['mib-display_logo'] == 1 && !empty($data['logo'])) {
                    $cors = $this->getCorsAttribute($data['logo']);
                    $logo = '<img src="' . esc_url($data['logo']) . '"' . $cors . ' alt="Park logó">';
                } elseif (isset($this->selectedShortcodeOption['extras']) && in_array('display_logo', $this->selectedShortcodeOption['extras']) && !empty($data['logo'])) {
                    $cors = $this->getCorsAttribute($data['logo']);
                    $logo = '<img src="' . esc_url($data['logo']) . '"' . $cors . ' alt="Park logó">';
                }

                $address = '';
                if (isset($this->filterOptionDatas['mib-display_address']) && $this->filterOptionDatas['mib-display_address'] == 1) {
                    $address = isset($data['address']) ? $data['address'] : '';
                } elseif (isset($this->selectedShortcodeOption['extras']) && in_array('display_address', $this->selectedShortcodeOption['extras'])) {
                    $address = isset($data['address']) ? $data['address'] : '';
                }

                $html .= '      <div class="swiper-slide">';
                $html .= '        <div class="card-wrapper" data-id="' . esc_attr($data['id']) . '" data-otthon-start="' . (!empty($data['otthonStart']) ? 1 : 0) . '">';
                $html .= '          <div class="card h-100 position-relative">';

                // Kép blokk
                $html .= '            <div class="primary-color card-image-wrapper">';
                $html .= $this->renderApartmentListingImage($data);
                if (!empty($data['otthonStartBadge'])) {
                    $html .= '              <a href="' . $otthonStartFilterUrl . '" class="mib-otthonstart-badge-link" aria-label="Otthon Start szűrő megnyitása">';
                    $html .= '                <img class="mib-otthonstart-badge" src="' . esc_url($data['otthonStartBadge']) . '" alt="Otthon Start">';
                    $html .= '              </a>';
                }
                $html .= '            </div>';

                // Card body
                $html .= '            <div id="apartment-card-body" class="secondary-color card-body d-flex flex-column justify-content-between text-white">';

                // Felső rész (logó + cím / státusz + név)
                $html .= '              <div class="mb-3">';
                $html .= '                <div class="d-flex justify-content-between">';
                if (!empty($logo)) {
                    $html .= '                  <div><div class="park-logo">' . $logo . '</div><strong>' . esc_html($address) . '</strong></div>';
                } else {
                    // Ha nincs logó, csak az esetleges cím (ha van)
                    if (!empty($address)) {
                        $html .= '                  <div><strong>' . esc_html($address) . '</strong></div>';
                    } else {
                        $html .= '                  <div></div>';
                    }
                }
                $html .= '                  <div>';
                $html .= '                    <small class="third-text-color ' . esc_attr($data['statusclass']) . ' d-block">' . esc_html($data['statusrow']) . '</small>';
                $html .= '                    <strong class="fs-5 apartman-id">' . esc_html($data['rawname']) . '</strong>';
                $html .= '                  </div>';
                $html .= '                </div>';
                $html .= '                <hr>';
                $html .= '              </div>';

                // Középső metrikák
                $html .= '              <div class="mb-3">';
                $html .= '                <div class="d-flex justify-content-between">';
                $html .= '                  <div><small class="d-block text-muted">Szobák</small><strong class="fs-5">' . esc_html($data['numberOfRooms']) . '</strong></div>';
                $html .= '                  <div><small class="d-block text-muted">Méret (m²)</small><strong class="fs-5">' . esc_html($data['salesFloorArea']) . '</strong></div>';
                $html .= '                  <div class="text-end"><small class="d-block text-muted">Emelet</small><strong class="fs-5">' . esc_html($data['floor']) . '</strong></div>';
                $html .= '                </div>';
                $html .= '                <hr>';
                $html .= '              </div>';

                // Ár
                $html .= '              <div class="list-view-price-container mt-2 mt-md-0">';
                if (!empty($data['originalPrice'])) {
                    $html .= '                <span class="mib-old-price">' . esc_html($data['originalPrice']) . '</span>';
                }
                if (!empty($data['sale_price_badge'])) {
                    $html .= '<img id="saleimagebadge" src="' . $data['sale_price_badge'] . '" alt="Akciós lakás">';
                }
                $html .= '                <strong class="fs-4 text-success third-text-color mib-new-price">' . esc_html($data['price']) . '</strong>';
                $html .= '              </div>';

                if (!empty($this->selectedShortcodeOption['extras']) && in_array('display_supported_price', $this->selectedShortcodeOption['extras']) && !empty($data['supportedPrice']) && !empty($data['isRustZone'])) {
                    $html .= '              <div class="mib-supported-price">';
                    $html .= '                <span class="mib-supported-price-label">' . esc_html__('5% ÁFA visszaigényelhető! Ennyibe kerül neked:', 'mib') . '</span>';
                    $html .= '                <span class="mib-supported-price-value">' . esc_html($data['supportedPrice']) . '</span>';
                    $html .= '              </div>';
                }

                $html .= '              <div class="card-divider list-view-only"></div>';

                // Gomb sor
                $html .= '              <div class="list-view-button-wrapper d-flex align-items-center button-row">';
                if (isset($data['statusrow']) && $data['statusrow'] === 'Elérhető') {
                    $html .= '                <i class="fa fa-regular fa-heart favorite-icon" aria-hidden="true" data-id="' . esc_attr($data['id']) . '"></i>';
                    $html .= '                <a id="cardhref" href="' . esc_url($data['url']) . '" class="flex-grow-1">';
                    $html .= '                  <button class="primary-color btn btn-light w-100 d-flex align-items-center justify-content-center gap-2 rounded-pill" type="button">';
                    $html .= '                    Tudj meg többet <i class="fa fa-arrow-right" aria-hidden="true"></i>';
                    $html .= '                  </button>';
                    $html .= '                </a>';
                } else {
                    // Ha nem elérhető, kis függőleges hely kitöltés
                    $html .= '                <div style="margin-bottom: 80px;"></div>';
                }
                $html .= '              </div>'; // .button-row

                $html .= '            </div>'; // .card-body
                $html .= '          </div>';   // .card
                $html .= '        </div>';     // .card-wrapper
                $html .= '      </div>';       // .swiper-slide
            }
        } else {
            $html .= '      <div class="swiper-slide"><p><strong>Nem található ingatlan</strong></p></div>';
        }

        $html .= '    </div>'; // .swiper-wrapper
        $html .= '  </div>';   // .mib-property-carousel.swiper

        // NYILAK + PAGINATION a KÜLSŐ WRAPPERBEN (nem a .swiper-ben!)
        $html .= '  <div class="swiper-button-prev"></div>';
        $html .= '  <div class="swiper-button-next"></div>';
        $html .= '  <div class="swiper-pagination"></div>';

        $html .= '</div>'; // .mib-carousel-outer

        return $html;
    }

    public function getCarouselSlidesHtml($datas)
    {
        $html = '';

        if (!empty($datas)) {
            foreach ($datas as $data) {
                $logo = '';
                if (isset($this->filterOptionDatas['mib-display_logo']) && $this->filterOptionDatas['mib-display_logo'] == 1 && !empty($data['logo'])) {
                    $cors = $this->getCorsAttribute($data['logo']);
                    $logo = '<img src="' . $data['logo'] . '"' . $cors . '>';
                } elseif (isset($this->selectedShortcodeOption['extras']) && in_array('display_logo', $this->selectedShortcodeOption['extras']) && !empty($data['logo'])) {
                    $cors = $this->getCorsAttribute($data['logo']);
                    $logo = '<img src="' . $data['logo'] . '"' . $cors . '>';
                }

                $address = '';
                if (isset($this->filterOptionDatas['mib-display_address']) && $this->filterOptionDatas['mib-display_address'] == 1) {
                    $address = $data['address'];
                } elseif (isset($this->selectedShortcodeOption['extras']) && in_array('display_address', $this->selectedShortcodeOption['extras'])) {
                    $address = $data['address'];
                }

                $html .= '<div class="swiper-slide">';
                $html .= '<div class="card-wrapper" data-id="' . esc_attr($data['id']) . '" data-otthon-start="' . ($data['otthonStart'] ? 1 : 0) . '">';
                $html .= '<div class="card h-100 position-relative">';

                $html .= '<div class="primary-color card-image-wrapper">';
                $html .= $this->renderApartmentListingImage($data);
                if (!empty($data['otthonStartBadge'])) {
                    $html .= '<img class="mib-otthonstart-badge" src="' . esc_url($data['otthonStartBadge']) . '" alt="Otthon Start" role="button" tabindex="0" />';
                }
                $html .= '</div>';

                $html .= '<div id="apartment-card-body" class="secondary-color card-body d-flex flex-column justify-content-between text-white">';

                $html .= '<div class="mb-3">';
                $html .= '<div class="d-flex justify-content-between">';
                if (!empty($logo)) {
                    $html .= '<div><div class="park-logo">' . $logo . '</div><strong>' . $address . '</strong></div>';
                }
                $html .= '<div>';
                $html .= '<small class="third-text-color ' . $data['statusclass'] . ' d-block">' . $data['statusrow'] . '</small>';
                $html .= '<strong class="fs-5 apartman-id">' . esc_html($data['rawname']) . '</strong>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '<hr>';
                $html .= '</div>';

                $html .= '<div class="mb-3">';
                $html .= '<div class="d-flex justify-content-between">';
                $html .= '<div><small class="d-block text-muted">Szobák</small><strong class="fs-5">' . esc_html($data['numberOfRooms']) . '</strong></div>';
                $html .= '<div><small class="d-block text-muted">Méret (m2)</small><strong class="fs-5">' . esc_html($data['salesFloorArea']) . '</strong></div>';
                $html .= '<div class="text-end"><small class="d-block text-muted">Emelet</small><strong class="fs-5">' . esc_html($data['floor']) . '</strong></div>';
                $html .= '</div>';
                $html .= '<hr>';
                $html .= '</div>';

                $html .= '<div class="list-view-price-container mt-2 mt-md-0">';
                if (!empty($data['originalPrice'])) {
                    $html .= '<span class="mib-old-price">' . esc_html($data['originalPrice']) . '</span>';
                }
                if (!empty($data['sale_price_badge'])) {
                    $html .= '<img id="saleimagebadge" src="' . $data['sale_price_badge'] . '" alt="Akciós lakás">';
                }
                $html .= '<strong class="fs-4 text-success third-text-color mib-new-price">' . esc_html($data['price']) . '</strong>';
                $html .= '</div>';

                if (!empty($this->selectedShortcodeOption['extras']) && in_array('display_supported_price', $this->selectedShortcodeOption['extras']) && !empty($data['supportedPrice']) && !empty($data['isRustZone'])) {
                    $html .= '<div class="mib-supported-price">';
                    $html .= '<span class="mib-supported-price-label">' . esc_html__('5% ÁFA visszaigényelhető! Ennyibe kerül neked:', 'mib') . '</span>';
                    $html .= '<span class="mib-supported-price-value">' . esc_html($data['supportedPrice']) . '</span>';
                    $html .= '</div>';
                }

                $html .= '<div class="card-divider list-view-only"></div>';

                $html .= '<div class="list-view-button-wrapper d-flex align-items-center button-row">';
                if ($data['statusrow'] == 'Elérhető') {
                    $html .= '<i class="fa fa-regular fa-heart favorite-icon" aria-hidden="true" data-id="' . esc_attr($data['id']) . '"></i>';
                    $html .= '<a id="cardhref" href="' . $data['url'] . '" class="flex-grow-1">';
                    $html .= '<button class="primary-color btn btn-light w-100 d-flex align-items-center justify-content-center gap-2 rounded-pill">';
                    $html .= 'Tudj meg többet <i class="fa fa-arrow-right" aria-hidden="true"></i>';
                    $html .= '</button>';
                    $html .= '</a>';
                } else {
                    $html .= '<div style="margin-bottom: 80px;"></div>';
                }
                $html .= '</div>';

                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
            }
        }

        return $html;
    }

    public function getFilters($selectedParkId = null)
    {
        $filters = [];

        $map = [
            'district' => 'district',
            'orientation' => 'typeOfBalcony',
            'availability' => 'status',
            'garden_connection' => 'garden_connection',
            'stairway' => 'stairway',
            'otthonStart' => 'otthon_start',
            'discountPrice' => 'discount_price',
            // slider params
            'price_min' => 'price-slider-min',
            'price_max' => 'price-slider-max',
            'floor_min' => 'floor_min',
            'floor_max' => 'floor_max',
            'room_min' => 'room_min',
            'room_max' => 'room_max',
            'area_min' => 'square-meter-slider-min',
            'area_max' => 'square-meter-slider-max',
        ];

        foreach ($map as $queryKey => $filterKey) {
            if (isset($_GET[$queryKey])) {
                $value = $_GET[$queryKey];
                if (is_array($value)) {
                    $filters[$filterKey] = array_map('sanitize_text_field', $value);
                } else {
                    $filters[$filterKey] = sanitize_text_field($value);
                }
            }
        }

        return $this->getCatalogFilterHtml($filters, true, $selectedParkId);
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

        if (isset($this->filterOptionDatas['mib-filterslider_checked']) && $this->filterOptionDatas['mib-filterslider_checked'] == 1) {

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

        } else {

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

        if (isset($this->filterOptionDatas['mib-filterslider_checked']) && $this->filterOptionDatas['mib-filterslider_checked'] == 1) {

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

        } else {

            $html = '<div class="range-slider-container primary-color"><div id="price-slider-range"></div>
					<p id="price-slider-value">
					  Ár: <span id="price-range-value"></span>
				</p></div>';

        }

        return $html;
    }

    private function priceFilterPriceByCatalog($filterType, $custom = '')
    {
        // Alapértelmezett értékek
        $priceFrom = $this->filterOptionDatas['mib-filter-price-slider-min'] ?? -1;
        $priceTo = $this->filterOptionDatas['mib-filter-price-slider-max'] ?? 10;

        $selectedMin = $filterType['price-slider-min'] ?? $priceFrom;
        $selectedMax = $filterType['price-slider-max'] ?? $priceTo;

        $html = '<div class="custom-slider-container">
	        <label class="custom-slider-label">Ár</label>
	        <div id="custom-price-slider" class="' . $custom . ' slider-inactive-color custom-noui-slider"></div>
	        <p class="custom-price-range-value">' . $selectedMin . ' - ' . $selectedMax . '</p>
	    </div>';

        return $html;
    }

    private function getFilterFloorByCatalog($filterType, $custom = '')
    {
        // Alapértelmezett értékek
        $floorFrom = $this->filterOptionDatas['mib-filter-floor-from'] ?? -1;
        $floorTo = $this->filterOptionDatas['mib-filter-floor-to'] ?? 10;

        $selectedMin = $filterType['floor_min'] ?? $floorFrom;
        $selectedMax = $filterType['floor_max'] ?? $floorTo;

        $html = '<div class="custom-slider-container">
	        <label class="custom-slider-label">Emelet</label>
	        <div id="custom-floor-slider" class="' . $custom . ' custom-noui-slider slider-inactive-color"></div>
	        <p class="custom-floor-range-value">' . $selectedMin . ' - ' . $selectedMax . '</p>
	    </div>';

        return $html;
    }

    private function getFilterFloorShortCodeByCatalog($selectedMin, $selectedMax)
    {

        // Alapértelmezett értékek
        $html = '<div class="custom-slider-container">
	        <label class="custom-slider-label">Emelet</label>
	        <div id="custom-floor-slider" class="custom-noui-slider slider-inactive-color"></div>
	        <p class="custom-floor-range-value">' . $selectedMin . ' - ' . $selectedMax . '</p>
	    </div>';

        return $html;
    }

    private function getFilterPriceShortCodeByCatalog($selectedMin, $selectedMax)
    {

        // Alapértelmezett értékek
        $html = '<div class="custom-slider-container">
	        <label class="custom-slider-label">Ár</label>
	        <div id="custom-price-slider" class="custom-noui-slider slider-inactive-color"></div>
	        <p class="custom-price-range-value">' . $selectedMin . ' - ' . $selectedMax . '</p>
	    </div>';

        return $html;
    }

    private function getFilterRoomShortCodeByCatalog($selectedMin, $selectedMax)
    {

        // Alapértelmezett értékek
        $html = '<div class="custom-slider-container">
	        <label class="custom-slider-label">Szobák száma</label>
	        <div id="custom-room-slider" class="custom-noui-slider slider-inactive-color"></div>
	        <p class="custom-room-range-value">' . $selectedMin . ' - ' . $selectedMax . '</p>
	    </div>';

        return $html;
    }

    private function getFilterAreaShortCodeByCatalog($selectedMin, $selectedMax)
    {

        $html = '<div class="custom-slider-container">
	        <label class="custom-square-label">Terület</label>
	        <div id="custom-square-slider" class="custom-noui-slider slider-inactive-color"></div>
	        <p class="custom-square-range-value">' . $selectedMin . ' - ' . $selectedMax . '</p>
	    </div>';

        return $html;
    }

    private function getFilterResidentalParksShortCodeByCatalog($parkIds)
    {

        // Alapértelmezett értékek
        $html = '<select class="form-select select-residential-park" id="select-residential-park" aria-label="Lakópark kiválasztása">';
        $html .= '<option value="" selected>Lakópark kiválasztása</option>';

        foreach ($parkIds as $park => $id) {
            $name = $this->parkNames[$id] ?? $id;
            $html .= '<option value="' . $id . '">' . $name . '</option>';
        }

        $html .= '</select>';

        return $html;
    }

    private function getFilterResidentalParksForJustFilters($selectedParkId = null)
    {
        // Alapértelmezett értékek
        $html = '<select class="form-select select-residential-park" id="select-residential-park" aria-label="Lakópark kiválasztása">';

        $defaultSelected = empty($selectedParkId) ? ' selected' : '';
        $html .= '<option value=""' . $defaultSelected . '>Lakópark kiválasztása</option>';

        $allowedParks = [];
        if (!empty($this->filterOptionDatas['residential_park_ids'])) {
            $allowedParks = $this->filterOptionDatas['residential_park_ids'];
            if (is_string($allowedParks)) {
                $allowedParks = array_map('trim', explode(',', $allowedParks));
            }
        }

        // Az összes lakópark megjelenítése a parkNames tömbből
        foreach ($this->parkNames as $id => $name) {

            if (!empty($allowedParks) && !in_array((string) $id, $allowedParks)) {
                continue;
            }

            $isSelected = ($selectedParkId !== null && (string) $id === (string) $selectedParkId) ? ' selected' : '';
            $html .= '<option value="' . $id . '"' . $isSelected . '>' . $name . '</option>';
        }

        $html .= '</select>';

        return $html;
    }


    private function getFilterResidentalParkShortCodeByCatalog()
    {

        // Alapértelmezett értékek
        $html = '<div class="custom-slider-container">
	        <label class="custom-slider-label">Terület</label>
	        <div id="custom-square-slider" class="custom-noui-slider slider-inactive-color"></div>
	        <p class="custom-square-range-value">' . $selectedMin . ' - ' . $selectedMax . '</p>
	    </div>';

        return $html;
    }

    private function getFilterRoomByCatalog($filterType, $custom = '')
    {
        // Alapértelmezett értékek
        $roomFrom = $this->filterOptionDatas['mib-filter-room-from'] ?? -1;
        $roomTo = $this->filterOptionDatas['mib-filter-room-to'] ?? 10;

        $selectedMin = $filterType['room_min'] ?? $roomFrom;
        $selectedMax = $filterType['room_max'] ?? $roomTo;

        $html = '<div class="custom-slider-container">
	        <label class="custom-slider-label">Szobák száma</label>
	        <div id="custom-room-slider" class="' . $custom . ' custom-noui-slider slider-inactive-color"></div>
	        <p class="custom-room-range-value">' . $selectedMin . ' - ' . $selectedMax . '</p>
	    </div>';

        return $html;
    }

    private function squareFiltersByCatalog($filterType, $custom = '')
    {

        // Alapértelmezett értékek
        $squareFrom = $this->filterOptionDatas['mib-filter-square-meter-slider-min'] ?? -1;
        $squareTo = $this->filterOptionDatas['mib-filter-square-meter-slider-max'] ?? 10;

        $selectedMin = $filterType['square-meter-slider-min'] ?? $squareFrom;
        $selectedMax = $filterType['square-meter-slider-max'] ?? $squareTo;

        $html = '<div class="custom-slider-container">
	        <label class="custom-slider-label">Terület</label>
	        <div id="custom-square-slider" class="' . $custom . ' custom-noui-slider slider-inactive-color"></div>
	        <p class="custom-square-range-value">' . $selectedMin . ' - ' . $selectedMax . '</p>
	    </div>';

        return $html;
    }

    private function getFilterOrientationByCatalog($filterType)
    {
        // Erkély típusok konvertálása tömbbé, ha szükséges
        if (isset($filterType['typeOfBalcony']) && !is_array($filterType['typeOfBalcony'])) {
            $filterType['typeOfBalcony'] = explode(',', $filterType['typeOfBalcony']);
        }

        $html = '<div class="catalog-dropdown">
		                <fieldset>
		                    <legend class="form-label mb-1">Erkély típusa</legend>
		                    <div class="dropdown">
		                        <button class="btn btn-dark dropdown-toggle" type="button" id="orientationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
		                            Válassz típust
		                        </button>
		                        <ul class="third-color mt-1 p-2 dropdown-menu" aria-labelledby="orientationDropdown">';

        // Erkély típusok listája
        foreach ($this->balconyTypes as $key => $value) {
            $orientationChecked = (isset($filterType['typeOfBalcony']) && in_array($value, (array) $filterType['typeOfBalcony'])) ? 'checked' : '';

            $html .= '<li>
		                    <label class="dropdown-item">
		                        <input type="checkbox" class="catalog-orientation-checkbox form-check-input" name="typeOfBalcony[]" value="' . $value . '" ' . $orientationChecked . '> ' . esc_html($key) . '
		                    </label>
		                  </li>';
        }

        $html .= '</ul>
		                    </div>
		                </fieldset>
		            </div>';

        return $html;
    }


    private function getCatalogFilterHtml($filterType = [], $includeSearchButton = false, $selectedParkId = null)
    {
        $html = '<div class="custom-filter-container">';
        $html .= '<div class="d-flex">';

        if (!empty($this->filterOptionDatas)) {
            $html .= $this->getFilterResidentalParksForJustFilters($selectedParkId);

            if (!empty($this->filterOptionDatas['mib-filter-price_range'])) {
                $html .= $this->priceFilterPriceByCatalog($filterType);
            }

            if (!empty($this->filterOptionDatas['mib-filter-room'])) {
                $html .= $this->getFilterRoomByCatalog($filterType);
            }

            if (!empty($this->filterOptionDatas['mib-filter-square-meter'])) {
                $html .= $this->squareFiltersByCatalog($filterType);
            }



            $advancedToggles = [
                !empty($this->filterOptionDatas['mib-filter-district']),
                !empty($this->filterOptionDatas['mib-filter-orientation']),
                !empty($this->filterOptionDatas['mib-garden_connection']),
                !empty($this->filterOptionDatas['mib-otthonstart']),
                !empty($this->filterOptionDatas['mib-stairway']),
            ];

            $html .= '<div class="custom-filter-container">';
            $showAdvanced = false;
            if (isset($this->filterOptionDatas['mib-filter-district']) && $this->filterOptionDatas['mib-filter-district'] == true) {
                $showAdvanced = true;
            }
            if (isset($this->filterOptionDatas['mib-filter-orientation']) && $this->filterOptionDatas['mib-filter-orientation'] == true) {
                $showAdvanced = true;
            }
            if (isset($this->filterOptionDatas['mib-filter-availability']) && $this->filterOptionDatas['mib-filter-availability'] == true && $this->filterOptionDatas['inactive_hide'] != 1) {
                $showAdvanced = true;
            }
            if (isset($this->filterOptionDatas['mib-garden_connection']) && $this->filterOptionDatas['mib-garden_connection'] == true) {
                $showAdvanced = true;
            }
            if (isset($this->filterOptionDatas['mib-otthonstart']) && $this->filterOptionDatas['mib-otthonstart'] == true) {
                $showAdvanced = true;
            }
            if (isset($this->filterOptionDatas['mib-discount_price']) && $this->filterOptionDatas['mib-discount_price'] == true) {
                $showAdvanced = true;
            }
            if (isset($this->filterOptionDatas['mib-stairway']) && $this->filterOptionDatas['mib-stairway'] == true) {
                $showAdvanced = true;
            }

            if (!empty($this->filterOptionDatas['mib-filter-availability']) && empty($this->filterOptionDatas['inactive_hide'])) {
                $advancedToggles[] = true;
            }

            if (in_array(true, $advancedToggles, true)) {
                $html .= '<div class="custom-filter-container">';
                $html .= '<div class="mb-2">';
                $html .= '<div class="mb-2" id="parksfilter">';
                $html .= '<button type="button" class="btn btn-outline-secondary btn-sm" id="toggle-advanced-filters">';
                $html .= '<i class="fas fa-sliders-h me-1"></i> További szűrők';
                $html .= '</button>';
                $html .= '</div>';
                $html .= '</div>';
                $html .= '</div>';
            }
            $html .= '</div>';
        }

        $html .= '</div>';

        $html .= '<div id="advanced-filters" class="flex-wrap" style="display:none;">';


        if (isset($this->filterOptionDatas['mib-filter-floor']) && $this->filterOptionDatas['mib-filter-floor'] == true) {
            $html .= $this->getFilterFloorByCatalog($filterType);
        }
        if (isset($this->filterOptionDatas['mib-filter-district']) && $this->filterOptionDatas['mib-filter-district'] == true) {
            $html .= $this->getFilterDistrictByCatalog($filterType);
        }
        if (isset($this->filterOptionDatas['mib-filter-orientation']) && $this->filterOptionDatas['mib-filter-orientation'] == true) {
            $html .= $this->getFilterOrientationByCatalog($filterType);
        }
        if (isset($this->filterOptionDatas['mib-filter-availability']) && $this->filterOptionDatas['mib-filter-availability'] == true && $this->filterOptionDatas['inactive_hide'] != 1) {
            $html .= $this->getFilterAvailabilityByCatalog($filterType);
        }
        if (isset($this->filterOptionDatas['mib-garden_connection']) && $this->filterOptionDatas['mib-garden_connection'] == true) {
            $html .= $this->getFilterGardenConnectionByCatalog($filterType);
        }
        if (isset($this->filterOptionDatas['mib-stairway']) && $this->filterOptionDatas['mib-stairway'] == true) {
            $html .= $this->getFilterStairwayByCatalog($filterType);
        }
        if (isset($this->filterOptionDatas['mib-otthonstart']) && $this->filterOptionDatas['mib-otthonstart'] == true) {
            $html .= $this->getFilterOtthonStartCheckboxByCatalog($filterType);
        }
        if (isset($this->filterOptionDatas['mib-discount_price']) && $this->filterOptionDatas['mib-discount_price'] == true) {
            $html .= $this->getFilterDiscountPriceCheckboxByCatalog($filterType);
        }




        $html .= '</div>';

        if ($includeSearchButton) {
            $html .= '<div class="search-mib-filter-container" id="search-apartman-btn" class="btn third-color">Lakások keresése <i class="fa fa-arrow-right" aria-hidden="true"></i></div>';
        }

        $html .= '</div>';

        return $html;
    }

    private function getFilterDistrictByCatalog($filterType)
    {

        $selected = $filterType['district'] ?? '';
        $districtOptions = $this->getDistrictOptionsForFilters($filterType);

        $html = '<div class="catalog-dropdown">'
            . '<label for="district-select" class="form-label">Helyszín</label>'
            . '<select id="district-select" class="form-select district-select">'
            . '<option value="">Válassz helyszínt</option>';



        foreach ($districtOptions as $key => $value) {
            $nameLabel = '';
            if ($value == 0) {
                $nameLabel = $this->parkDistrictName[$key];
                $key = 0;
            } else {
                $nameLabel = 'Budapest ' . esc_html($value);
            }
            $selectedAttr = ($selected === $key && !empty($key)) ? ' selected' : '';
            $html .= '<option value="' . $key . '"' . $selectedAttr . '>' . $nameLabel . '</option>';
        }

        $html .= '</select></div>';

        return $html;
    }


    private function getFilterFloor($filterType)
    {

        if (isset($filterType['floor']) && !is_array($filterType['floor'])) {
            $filterType['floor'] = explode(',', $filterType['floor']);
        }

        $html = '<div class="mb-2">
	                <div class="dropdown">
	                    <button class="btn btn-dark dropdown-toggle primary-color" type="button" id="floorDropdown" data-bs-toggle="dropdown" aria-expanded="false">
	                        Emelet
	                    </button>
	                    <ul class="primary-color mt-1 dropdown-menu" aria-labelledby="floorDropdown">';

        $floorFrom = (isset($this->filterOptionDatas['mib-filter-floor-from']) && !empty($this->filterOptionDatas['mib-filter-floor-from'])) ? $this->filterOptionDatas['mib-filter-floor-from'] : 0;
        $floorTo = (isset($this->filterOptionDatas['mib-filter-floor-to']) && !empty($this->filterOptionDatas['mib-filter-floor-to'])) ? $this->filterOptionDatas['mib-filter-floor-to'] : 0;

        for ($i = $floorFrom; $i <= $floorTo; $i++) {

            $floorLabel = ($i == 0) ? __('Földszint', 'mib') : $i;
            $floorChecked = (isset($filterType['floor']) && in_array($i, (array) $filterType['floor'])) ? 'checked' : '';

            $html .= '<li>
	                    <label class="dropdown-item">
	                        <input type="checkbox" class="floor-checkbox" name="floors[]" value="' . $i . '" ' . $floorChecked . '> ' . $floorLabel . '
	                    </label>
	                  </li>';
        }

        $html .= '</ul>
	                </div>
	            </div>';


        return $html;
    }


    private function getFilterRoom($filterType)
    {
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
            $roomChecked = (isset($filterType['numberOfRooms']) && in_array($i, (array) $filterType['numberOfRooms'])) ? 'checked' : '';

            $html .= '<li>
	                    <label class="dropdown-item">
	                        <input type="checkbox" class="room-checkbox" name="numberOfRooms[]" value="' . $i . '" ' . $roomChecked . '> ' . $roomLabel . '
	                    </label>
	                  </li>';
        }

        $html .= '</ul>
	                </div>
	            </div>';

        return $html;
    }

    private function getFilterOrientation($filterType)
    {
        // Erkély típusok konvertálása tömbbé, ha szükséges
        if (isset($filterType['typeOfBalcony']) && !is_array($filterType['typeOfBalcony'])) {
            $filterType['typeOfBalcony'] = explode(',', $filterType['typeOfBalcony']);
        }

        $html = '<div class="mb-2">
		                <fieldset>
		                    <legend class="form-label mb-1">Erkély típusa</legend>
		                    <div class="dropdown">
		                        <button class="btn btn-dark dropdown-toggle primary-color" type="button" id="orientationDropdown" data-bs-toggle="dropdown" aria-expanded="false">
		                            Válassz típust
		                        </button>
		                        <ul class="primary-color mt-1 dropdown-menu" aria-labelledby="orientationDropdown">';

        // Erkély típusok listája
        foreach ($this->balconyTypes as $key => $value) {
            $orientationChecked = (isset($filterType['typeOfBalcony']) && in_array($value, (array) $filterType['typeOfBalcony'])) ? 'checked' : '';

            $html .= '<li>
		                    <label class="dropdown-item">
		                        <input type="checkbox" class="orientation-checkbox" name="typeOfBalcony[]" value="' . esc_attr($value) . '" ' . $orientationChecked . '> ' . esc_html($key) . '
		                    </label>
		                  </li>';
        }

        $html .= '</ul>
		                    </div>
		                </fieldset>
		            </div>';

        return $html;
    }

    private function getFilterDistrict($filterType)
    {
        $selected = $filterType['district'] ?? '';
        $districtOptions = $this->getDistrictOptionsForFilters($filterType);

        $html = '<div class="mb-2">'
            . '<label for="district-select" class="form-label">Helyszín</label>'
            . '<select id="district-select" class="form-select district-select">'
            . '<option value="">Válassz helyszínt</option>';

        foreach ($districtOptions as $key => $value) {
            $selectedAttr = ($selected === $key) ? ' selected' : '';
            $html .= '<option value="' . esc_attr($key) . '"' . $selectedAttr . '>' . esc_html($value) . '</option>';
        }

        $html .= '</select></div>';

        return $html;
    }

    private function getFilterAvailability($filterType)
    {
        if (isset($filterType['status']) && !is_array($filterType['status'])) {
            $filterType['status'] = explode(',', $filterType['status']);
        }

        $html = '<div class="mb-2">
		                <fieldset>
		                    <legend class="form-label mb-1">Státusz</legend>
		                    <div class="dropdown">
		                        <button class="btn btn-dark dropdown-toggle primary-color" type="button" id="availabilityDropdown" data-bs-toggle="dropdown" aria-expanded="false">
		                            Válassz státuszt
		                        </button>
		                        <ul class="primary-color mt-1 dropdown-menu" aria-labelledby="availabilityDropdown">';

        // elérhetőség
        foreach ($this->availability as $key => $value) {
            $availabilityChecked = (isset($filterType['status']) && in_array($value, (array) $filterType['status'])) ? 'checked' : '';

            $html .= '<li>
		                    <label class="dropdown-item">
		                        <input type="checkbox" class="availability-checkbox" name="availability[]" value="' . esc_attr($value) . '" ' . $availabilityChecked . '> ' . esc_html($key) . '
		                    </label>
		                  </li>';
        }

        $html .= '</ul>
		                    </div>
		                </fieldset>
		            </div>';

        return $html;
    }

    private function getFavoriteOption()
    {


        return $html;
    }

    private function getFilterAvailabilityByCatalog($filterType = [])
    {
        if (isset($filterType['status']) && !is_array($filterType['status'])) {
            $filterType['status'] = explode(',', $filterType['status']);
        }

        $html = '<div class="catalog-dropdown">
		                <fieldset>
		                    <legend class="form-label mb-1">Státusz</legend>
		                    <div class="dropdown">
		                        <button class="btn btn-dark dropdown-toggle" type="button" id="availabilityDropdown" data-bs-toggle="dropdown" aria-expanded="false">
		                            Válassz státuszt
		                        </button>
		                        <ul class="third-color mt-1 p-2 dropdown-menu" aria-labelledby="availabilityDropdown">';

        // elérhetőség
        foreach ($this->availability as $key => $value) {
            $availabilityChecked = (isset($filterType['status']) && in_array($value, (array) $filterType['status'])) ? 'checked' : '';

            $html .= '<li>
		                    <label class="dropdown-item">
		                        <input type="checkbox" class="catalog-availability-checkbox form-check-input" name="availability[]" value="' . esc_attr($value) . '" ' . $availabilityChecked . '> ' . esc_html($key) . '
		                    </label>
		                  </li>';
        }

        $html .= '</ul>
		                    </div>
		                </fieldset>
		            </div>';

        return $html;
    }


    private function getFilterGardenConnectionByCatalog($filterType = [])
    {
        if (isset($filterType['garden_connection']) && !is_array($filterType['garden_connection'])) {
            $filterType['garden_connection'] = explode(',', $filterType['garden_connection']);
        }

        $html = '<div class="catalog-dropdown">
                        <fieldset>
                            <legend class="form-label mb-1">Kertkapcsolat</legend>
                            <div class="dropdown">
                                <button class="btn btn-dark dropdown-toggle" type="button" id="gardenConnectionDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                    Válassz kertkapcsolat típust
                                </button>
                                <ul class="third-color mt-1 p-2 dropdown-menu" aria-labelledby="gardenConnectionDropdown">';

        foreach ($this->gardenConnection as $key => $value) {
            $availabilityChecked = (isset($filterType['garden_connection']) && in_array($value, (array) $filterType['garden_connection'])) ? 'checked' : '';

            $html .= '<li>
                            <label class="dropdown-item">
                                <input type="checkbox" class="catalog-gardenconnection-checkbox form-check-input" name="garden_connection[]" value="' . esc_attr($value) . '" ' . $availabilityChecked . '> ' . esc_html($key) . '
                            </label>
                          </li>';
        }

        $html .= '</ul>
                            </div>
                        </fieldset>
                    </div>';

        return $html;
    }

    private function getFilterOtthonStartCheckboxByCatalog($filterType = [])
    {
        $selected = [];
        if (isset($filterType['otthon_start'])) {
            $selected = is_array($filterType['otthon_start'])
                ? $filterType['otthon_start']
                : explode(',', $filterType['otthon_start']);
        }

        $isChecked = in_array('1', array_map('strval', (array) $selected), true);
        $checked = $isChecked ? 'checked' : '';

        $html = '<div class="catalog-dropdown">
                        <fieldset>
                            <legend class="form-label mb-1">Otthon Start</legend>
                            <div class="p-2">
                                <div class="form-check">
                                    <input type="checkbox"
                                           class="form-check-input catalog-otthonstart-checkbox"
                                           id="catalog-otthonstart-filter"
                                           name="otthon_start[]"
                                           value="1" ' . $checked . '>
                                    <label class="form-check-label" for="catalog-otthonstart-filter">'
            . esc_html__('Otthon Start feltételeinek megfelelő', 'mib') .
            '</label>
                                </div>
                            </div>
                        </fieldset>
                    </div>';

        return $html;
    }

    private function getFilterDiscountPriceCheckboxByCatalog($filterType = [])
    {
        $selected = [];
        if (isset($filterType['discount_price'])) {
            $selected = is_array($filterType['discount_price'])
                ? $filterType['discount_price']
                : explode(',', $filterType['discount_price']);
        }

        $isChecked = in_array('1', array_map('strval', (array) $selected), true);
        $checked = $isChecked ? 'checked' : '';

        $html = '<div class="catalog-dropdown">
                        <fieldset>
                            <legend class="form-label mb-1">Akciós árak</legend>
                            <div class="p-2">
                                <div class="form-check">
                                    <input type="checkbox"
                                           class="form-check-input catalog-discountprice-checkbox"
                                           id="catalog-discountprice-filter"
                                           name="discount_price[]"
                                           value="1" ' . $checked . '>
                                    <label class="form-check-label" for="catalog-discountprice-filter">'
            . esc_html__('Akciós árak', 'mib') .
            '</label>
                                </div>
                            </div>
                        </fieldset>
                    </div>';

        return $html;
    }

    private function getFilterOtthonStartByCatalog($filterType = [])
    {
        $selected = [];
        if (isset($filterType['otthon_start'])) {
            $selected = is_array($filterType['otthon_start'])
                ? $filterType['otthon_start']
                : explode(',', $filterType['otthon_start']);
        }

        $isChecked = in_array('1', array_map('strval', (array) $selected), true);
        $checked = $isChecked ? 'checked' : '';

        $html = '<div class="catalog-dropdown">
                        <fieldset>
                            <legend class="form-label mb-1">Otthon Start</legend>
                            <div class="p-2">
                                <div class="form-check">
                                    <input type="checkbox"
                                           class="form-check-input catalog-otthonstart-checkbox"
                                           id="catalog-otthonstart-filter"
                                           name="otthon_start[]"
                                           value="1" ' . $checked . '>
                                    <label class="form-check-label" for="catalog-otthonstart-filter">'
            . esc_html__('Otthon Start feltételeinek megfelelő', 'mib') .
            '</label>
                                </div>
                            </div>
                        </fieldset>
                    </div>';

        return $html;
    }

    private function getFilterStairwayByCatalog($filterType = [])
    {
        if (isset($filterType['stairway']) && !is_array($filterType['stairway'])) {
            $filterType['stairway'] = explode(',', $filterType['stairway']);
        }

        $html = '<div class="catalog-dropdown">
		                <fieldset>
		                    <legend class="form-label mb-1">Lépcsőház</legend>
		                    <div class="dropdown">
		                        <button class="btn btn-dark dropdown-toggle" type="button" id="stairWayDropdown" data-bs-toggle="dropdown" aria-expanded="false">
		                            Válassz lépcsőház típust
		                        </button>
		                        <ul class="third-color mt-1 p-2 dropdown-menu" aria-labelledby="stairWayDropdown">';

        foreach ($this->stairWay as $key => $value) {
            $availabilityChecked = (isset($filterType['stairway']) && in_array($value, (array) $filterType['stairway'])) ? 'checked' : '';

            $html .= '<li>
		                    <label class="dropdown-item">
		                        <input type="checkbox" class="catalog-stairway-checkbox form-check-input" name="stairway[]" value="' . esc_attr($value) . '" ' . $availabilityChecked . '> ' . esc_html($key) . '
		                    </label>
		                  </li>';
        }

        $html .= '</ul>
		                    </div>
		                </fieldset>
		            </div>';

        return $html;
    }



    public function getPaginate($currentPage = 1, $totalItems = 0, $itemsPerPage = 50)
    {
        // Normalizálás
        $currentPage = max(1, (int) $currentPage);
        $totalItems = max(0, (int) $totalItems);
        $itemsPerPage = (int) $itemsPerPage;

        // Ha üres string/0 jött, állítsunk biztonságos alapértéket
        if ($itemsPerPage <= 0) {
            $itemsPerPage = 50;
        }

        // Oldalszám
        $totalPages = (int) ceil($totalItems / $itemsPerPage);

        // Ha nincs vagy csak 1 oldal, ne rajzoljunk paginát
        if ($totalPages <= 1) {
            return '';
        }

        // Biztonság kedvéért a currentPage ne lógjon ki
        if ($currentPage > $totalPages) {
            $currentPage = $totalPages;
        }

        $html = '<nav aria-label="mib pagination">';
        $html .= '<ul class="pagination" style="display:flex;">';

        // Előző
        $prevPage = ($currentPage > 1) ? $currentPage - 1 : 1;
        $disabledClass = ($currentPage > 1) ? '' : ' disabled';
        $html .= '<li class="page-item' . $disabledClass . '"><a id="page-link" class="page-link" data-page="' . $prevPage . '">«</a></li>';

        // Oldalszámok (ha sok oldal van, érdemes lenne ablakosítani, de most marad a teljes lista)
        for ($i = 1; $i <= $totalPages; $i++) {
            $activeClass = ($currentPage === $i) ? ' active' : '';
            $html .= '<li class="page-item' . $activeClass . '"><a id="page-link" class="page-link" data-page="' . $i . '">' . $i . '</a></li>';
        }

        // Következő
        $nextPage = ($currentPage < $totalPages) ? $currentPage + 1 : $totalPages;
        $disabledClass = ($currentPage < $totalPages) ? '' : ' disabled';
        $html .= '<li class="page-item' . $disabledClass . '"><a id="page-link" class="page-link" data-page="' . $nextPage . '">»</a></li>';

        $html .= '</ul></nav>';

        return $html;
    }


    public function getLoadMoreButton($currentPage = 1, $totalItems = 750, $itemsPerPage = 50, $hidden = false)
    {
        $html = '';

        // Védekezés: ha nem szám, vagy 0, állítsuk 1-re
        $itemsPerPage = (is_numeric($itemsPerPage) && $itemsPerPage > 0) ? (int) $itemsPerPage : 1;

        $totalPages = ceil($totalItems / $itemsPerPage);

        if ($currentPage < $totalPages) {
            $nextPage = $currentPage + 1;
            $containerAttributes = 'class="load-more-container"';
            if ($hidden) {
                $containerAttributes .= ' style="display:none;" data-auto-load="1"';
            }

            $buttonAttributes = 'id="load-more-button" class="btn btn-primary" data-page="' . $nextPage . '"';
            if ($hidden) {
                $buttonAttributes .= ' data-auto-load="1"';
            }

            $html .= '<div ' . $containerAttributes . '>';
            $html .= '<button ' . $buttonAttributes . '>Még több ingatlan</button>';
            $html .= '</div>';
        }

        return $html;
    }

    protected function getParkNameMap()
    {
        return $this->parkNames;
    }

    protected function fetchParkDistrictsFromApi(array $parkIds, array $existingDistricts = []): array
    {
        $districts = $existingDistricts;
        $headers = $this->buildMibApiHeaders();

        foreach ($parkIds as $parkId) {
            $parkId = (int) $parkId;
            if ($parkId <= 0) {
                continue;
            }

            $url = "https://ugyfel.mibportal.hu:3000/residential_parks/get/{$parkId}";
            $response = wp_remote_get($url, [
                'timeout' => 10,
                'redirection' => 10,
                'httpversion' => '1.1',
                'headers' => $headers,
            ]);

            if (is_wp_error($response)) {
                error_log('MIB fetchParkDistrictsFromApi error (' . $parkId . '): ' . $response->get_error_message());
                // akkor is mentsünk üreset
                $districts[$parkId] = [];
                continue;
            }

            $body = wp_remote_retrieve_body($response);
            $json = json_decode($body, true);

            $rawValues = (!empty($json['district'])) ? $json['district'] : [];

            if (!is_array($rawValues)) {
                $rawValues = [$rawValues];
            }

            $codes = [];

            foreach ($rawValues as $value) {
                if (is_array($value)) {
                    foreach ($value as $nested) {
                        $code = $this->resolveDistrictCode($nested);
                        if ($code !== null) {
                            $codes[$code] = true;
                        }
                    }
                    continue;
                }

                $code = $this->resolveDistrictCode($value);

                if ($code !== null) {
                    $codes[$code] = true;
                }
            }

            // mindig mentsük el – ha nincs kód, akkor üres tömbként
            $districts[$parkId] = array_keys($codes);
        }

        return $districts;
    }

    protected function buildMibApiHeaders(): array
    {
        $headers = [];

        if (!empty($this->mibOptions['token'])) {
            $headers['Authorization'] = "Bearer {$this->mibOptions['token']}";
        }

        return $headers;
    }

    private function resolveDistrictCode($value): ?string
    {
        if (!is_string($value)) {
            return null;
        }

        $value = trim($value);

        if ($value === '') {
            return null;
        }

        if (isset($this->districtNames[$value])) {
            return $value;
        }

        $valueLower = function_exists('mb_strtolower') ? mb_strtolower($value) : strtolower($value);

        foreach ($this->districtNames as $code => $label) {
            $codeLower = function_exists('mb_strtolower') ? mb_strtolower($code) : strtolower($code);
            $labelLower = function_exists('mb_strtolower') ? mb_strtolower($label) : strtolower($label);

            if ($valueLower === $codeLower || $valueLower === $labelLower) {
                return $code;
            }

            $normalizedValue = str_replace(['.', '-', 'kerület', 'kerulet', 'ker.', ' '], '', $valueLower);
            $normalizedCode = str_replace(['.', '-', ' '], '', $codeLower);
            $normalizedLabel = str_replace(['.', '-', ' '], '', $labelLower);

            if ($normalizedValue === $normalizedCode || $normalizedValue === $normalizedLabel) {
                return $code;
            }
        }

        return null;
    }

    private function parseParkIds($parkIds): array
    {
        if ($parkIds instanceof \Traversable) {
            $parkIds = iterator_to_array($parkIds, false);
        }

        if (is_string($parkIds)) {
            $decoded = json_decode($parkIds, true);

            if (json_last_error() === JSON_ERROR_NONE) {
                $parkIds = $decoded;
            }
        }

        if (!is_array($parkIds)) {
            $parkIds = [$parkIds];
        }

        $queue = $parkIds;
        $ids = [];

        while (!empty($queue)) {
            $value = array_shift($queue);

            if ($value instanceof \Traversable) {
                foreach ($value as $item) {
                    $queue[] = $item;
                }

                continue;
            }

            if (is_array($value)) {
                foreach ($value as $item) {
                    $queue[] = $item;
                }

                continue;
            }

            if (is_string($value)) {
                $value = trim($value);

                if ($value === '') {
                    continue;
                }

                if (preg_match('/[\s,]+/', $value)) {
                    $parts = preg_split('/[\s,]+/', $value, -1, PREG_SPLIT_NO_EMPTY);

                    foreach ($parts as $part) {
                        $queue[] = $part;
                    }

                    continue;
                }

                if (!is_numeric($value)) {
                    continue;
                }
            }

            if (is_numeric($value)) {
                $ids[] = (int) $value;
            }
        }

        $ids = array_values(array_unique(array_filter($ids, static function ($value) {
            return $value > 0;
        })));

        return $ids;
    }

    private function getDistrictOptionsForFilters($filterType = []): array
    {
        $parkIds = [];

        if (!empty($filterType['residentialParkId'])) {
            $parkIds = $this->parseParkIds($filterType['residentialParkId']);
        } elseif (!empty($filterType['residential_park_ids'])) {
            $parkIds = $this->parseParkIds($filterType['residential_park_ids']);
        } elseif (!empty(($filterTypeProperty['residential_park_ids'] ?? null))) {
            $parkIds = $this->parseParkIds($filterTypeProperty['residential_park_ids']);
        } elseif (!empty($this->selectedShortcodeOption['residential_park_ids'])) {
            $parkIds = $this->parseParkIds($this->selectedShortcodeOption['residential_park_ids']);
        } elseif (!empty($this->filterOptionDatas['residential_park_ids'])) {
            $parkIds = $this->parseParkIds($this->filterOptionDatas['residential_park_ids']);
        } elseif (!empty($this->residentialParkId)) {
            $parkIds = $this->parseParkIds($this->residentialParkId);
        }

        if (!is_array($parkIds)) {
            $parkIds = array_map('trim', explode(',', (string) $parkIds));
        }

        $parkIds = array_values(array_unique(array_filter(array_map('intval', $parkIds))));

        if (empty($parkIds)) {
            return $this->districtNames;
        }

        $options = [];

        foreach ($parkIds as $parkId) {
            if (empty($this->parkDistricts[$parkId])) {
                // ha nincs hozzárendelt körzet → akkor is adjunk vissza egy default értéket
                $options[$parkId] = 0;
                continue;
            }

            $codes = $this->parkDistricts[$parkId];

            if (!is_array($codes)) {
                $codes = [$codes];
            }

            foreach ($codes as $code) {
                $resolved = $this->resolveDistrictCode($code);

                if ($resolved !== null && isset($this->districtNames[$resolved])) {
                    $options[$resolved] = $this->districtNames[$resolved];
                }
            }
        }

        return !empty($options) ? $options : $this->districtNames;
    }

    protected function sanitizeParkDistricts($districts): array
    {
        if (!is_array($districts)) {
            return [];
        }

        $sanitized = [];

        foreach ($districts as $parkId => $values) {
            $parkId = (int) $parkId;
            if ($parkId <= 0) {
                continue;
            }

            if (is_array($values) && array_key_exists('codes', $values) && is_array($values['codes'])) {
                $values = $values['codes'];
            } elseif (!is_array($values)) {
                $values = [$values];
            }

            $codes = [];

            foreach ($values as $value) {
                if (is_array($value)) {
                    foreach ($value as $nested) {
                        $code = $this->resolveDistrictCode($nested);
                        if ($code !== null) {
                            $codes[$code] = true;
                        }
                    }
                    continue;
                }

                $code = $this->resolveDistrictCode($value);

                if ($code !== null) {
                    $codes[$code] = true;
                }
            }

            if (!empty($codes)) {
                $sanitized[$parkId] = array_keys($codes);
            }
        }

        return $sanitized;
    }
}
