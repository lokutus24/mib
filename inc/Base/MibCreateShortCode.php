<?php

namespace Inc\Base;

use Inc\Base\MibBaseController;
use Inc\Base\MibAuthController;

class MibCreateShortCode extends MibBaseController
{
    public function registerFunction()
    {
        add_shortcode('mib_list_table', array($this, 'custom_list_table_shortcode'));
        add_shortcode('mib_list_apartman', array($this, 'mib_list_apartman'));
        add_shortcode('mib_list_apartman_catalog', array($this, 'mib_list_apartman_catalog'));

        //csak a szűrők jelennek meg.
        add_shortcode('mib_list_apartman_catalog_filters', array($this, 'mib_list_apartman_catalog_filters'));
        add_shortcode('mib_residential_documents', array($this, 'mib_residential_documents'));
        add_shortcode('mib_residential_gallery', array($this, 'mib_residential_gallery'));
        add_shortcode('mib_property_carousel', array($this, 'mib_property_carousel'));


        add_action('init', array($this, 'custom_property_rewrite_rule'));
        add_action('init', array($this, 'register_dynamic_shortcodes'));
        add_filter('query_vars', array($this, 'add_custom_query_var'));
        add_action('template_redirect', array($this, 'redirect_old_apartment_url'));
    }

    public function mib_residential_documents($atts)
    {
        $atts = shortcode_atts(['id' => 0], $atts, 'mib_residential_documents');
        $id = intval($atts['id']);

        if (!$id) {
            return '<p>Hiányzó lakópark azonosító.</p>';
        }

        $mibAuth = new MibAuthController();
        $options = $mibAuth->getOptionDatas();

        if (!empty($options)) {
            $expired = $mibAuth->checkExpireToken($options['expiry']);
            if ($expired) {
                $mibAuth->loginToMib();
            }
        } else {
            return '<p>Hiányzó konfiguráció.</p>';
        }

        $data = $mibAuth->getResidentialDocuments($id);
        $docs = isset($data['documents']) ? $data['documents'] : [];

        if (empty($docs)) {
            return '<p>Nincsenek dokumentumok.</p>';
        }

        $html = '<div class="mib-residential-documents">';
        $html .= '<label>Válasszon dokumentumot:</label>';
        $html .= '<select class="mib-res-doc-select"><option value="">-- Válasszon dokumentumot --</option>';

        foreach ($docs as $doc) {
            $name = esc_html($doc['name'] ?? ($doc['title'] ?? 'Dokumentum'));
            $url = esc_url($doc['url'] ?? ($doc['file'] ?? ''));
            if ($url) {
                $html .= '<option value="' . $url . '">' . $name . '</option>';
            }
        }

        $html .= '</select>';
        $html .= '<button type="button" class="mib-res-doc-btn">Letöltés</button>';
        $html .= '</div>';

        return $html;
    }

    public function mib_residential_gallery($atts)
    {
        $atts = shortcode_atts(['id' => 0], $atts, 'mib_residential_gallery');
        $id = intval($atts['id']);

        if (!$id) {
            return '<p>Hiányzó lakópark azonosító.</p>';
        }

        $mibAuth = new MibAuthController();
        $options = $mibAuth->getOptionDatas();

        if (!empty($options)) {
            $expired = $mibAuth->checkExpireToken($options['expiry']);
            if ($expired) {
                $mibAuth->loginToMib();
            }
        } else {
            return '<p>Hiányzó konfiguráció.</p>';
        }

        $data = $mibAuth->getResidentialDocuments($id);
        $images = isset($data['images']) ? $data['images'] : [];

        if (empty($images)) {
            return '<p>Nincsenek képek.</p>';
        }

        $html = '<div class="mib-residential-gallery">';
        foreach ($images as $img) {
            $preview = esc_url($img['previewUrl'] ?? '');
            $urlRaw = $img['url'] ?? '';
            $full = filter_var($urlRaw, FILTER_VALIDATE_URL) ? esc_url($urlRaw) : $preview;
            if ($preview) {
                $name = esc_attr($img['name'] ?? '');
                $html .= '<a href="' . $full . '"><img src="' . $preview . '" alt="' . $name . '" decoding="async" crossorigin="anonymous"></a>';
            }
        }
        $html .= '</div>';

        return $html;
    }

    public function custom_property_rewrite_rule()
    {
        add_rewrite_rule(
            '^lakas/([^/]+)/([^/]+)/?$',
            'index.php?pagename=lakas&project_slug=$matches[1]&apartment_slug=$matches[2]',
            'top'
        );
    }

    public function register_dynamic_shortcodes()
    {
        //$shortcodes = maybe_unserialize(get_option('mib_custom_shortcodes')) ?: [];

        foreach ($this->shortcodesOptions as $shortcode_name => $config) {
            add_shortcode($shortcode_name, function ($atts) use ($config, $shortcode_name) {
    
                $this->numberOfApartmens = $config['number_of_apartment'];

                $this->residentialParkId = implode(',', $config['residential_park_ids']);

                $this->selectedApartmanNames = implode(',', $config['apartment_skus']);

                $this->shortCodeApartmanName = $shortcode_name;

                $this->selectedShortcodeOption = $config;

                $this->shortcodeType = isset($config['type']) ? $config['type'] : '';

                list($datas, $total) = $this->getDatas(false, 0, $config['number_of_apartment']);

                $html = $this->getCardHtmlShortCode($datas, $total, 1, $config, $shortcode_name, $this->numberOfApartmens);

                return $html;

            });
        }
    }

    public function add_custom_query_var($vars)
    {
        $vars[] = 'project_slug';
        $vars[] = 'apartment_slug';
        return $vars;
    }

    public function redirect_old_apartment_url()
    {
        if (is_page('lakas') && isset($_GET['id'])) {

            $id = intval($_GET['id']);


            $mibAuth = new MibAuthController();
            $options = $mibAuth->getOptionDatas();

            if (!empty($options)) {

                $expired = $mibAuth->checkExpireToken($options['expiry']);

                if ($expired) {
                    $mibAuth->loginToMib();
                    $options = $mibAuth->getOptionDatas();
                }

            }else{

                exit("You need to fill option datas!");
            }

            $apartment = $mibAuth->getOneApartmentsById($id);

            if (!empty($apartment['data'][0]) && (!isset($apartment['data'][0]->error) && empty($apartment['data'][0]->error) ) ) {

                $ap = $apartment['data'][0];

                $host = parse_url(home_url(), PHP_URL_HOST);
                $parts = explode('.', $host);
                $projectSlug = (count($parts) >= 2) ? $parts[count($parts) - 2] : 'projekt';
                $apartmentSlug = sanitize_title($ap->slug ?? $ap->name);

                $new_url = home_url("/lakas/{$projectSlug}/{$apartmentSlug}/");

                wp_redirect($new_url, 301);
                exit;

            }else{

                exit("Apartman is not exist with this iD: ".$id);
            }
        }
    }

    public function custom_list_table_shortcode($atts, $content = "")
    {
        list($datas, $total) = $this->getDatas(false, 0, 50, ['type' => 'lakás']);

        $html = $this->getTableHtml($datas, $total, 1, []);
        return $html;
    }

    public function mib_list_apartman_catalog_filters($atts){

        $html = $this->getFilters();
        //echo 'dgdg';
        return $html;
    }

    public function mib_list_apartman($atts)
    {
        $html = '';
        //$id = isset($_GET['id']) ? intval($_GET['id']) : '';
        $project = get_query_var('project_slug', '');
        $apartmentSlug = get_query_var('apartment_slug', '');

        if ($apartmentSlug != '') {
            list($data, $total, $recommendDatas) = $this->getDatas(true, $apartmentSlug);
            $html = $this->getSingleApartmanHtml($data, $recommendDatas);
        }

        return $html;
    }

    public function mib_list_apartman_catalog($atts)
    {
        list($datas, $total) = $this->getDatas(false, 0, 9);
        $html = $this->getCardHtml($datas, $total, 1, []);
        return $html;
    }

    public function mib_property_carousel($atts)
    {
        list($datas, $total) = $this->getDatas(false, 0, 12);
        $html = $this->getCarouselHtml($datas);
        return $html;
    }

    private function getDatas(bool $single = false, string $id = '', $perpage = 50, $filterArgs = [])
    {

        $data = [];
        $table_data = [];
        $recommendDatas = [];

        $mibAuth = new MibAuthController();
        $options = $mibAuth->getOptionDatas();


        if (!empty($options)) {
            $expired = $mibAuth->checkExpireToken($options['expiry']);

            if ($expired) {
                $mibAuth->loginToMib();
                $options = $mibAuth->getOptionDatas();
            }

            if ($single) {
                $all_data = $mibAuth->getOneApartmentsById($id);

                if (!empty($all_data)) {
                    $recommendDatas = $this->getRecommendedDatas($all_data);
                }
            } else {

                $arg = [];
                if (isset($this->filterOptionDatas['mib-filter-availability']) && $this->filterOptionDatas['mib-filter-availability'] == true && (isset($this->filterOptionDatas['inactive_hide']) && $this->filterOptionDatas['inactive_hide'] == true)) {
                    $arg['status'] = 'Available';
                }

                if (isset($this->filterOptionDatas['mib-filter-square-meter']) && $this->filterOptionDatas['mib-filter-square-meter'] == true && (isset($this->filterOptionDatas['mib-filter-square-meter-slider-min']) && isset($this->filterOptionDatas['mib-filter-square-meter-slider-max']))) {
                    $arg = array_merge($arg, ['bruttoFloorArea' => $this->filterOptionDatas['mib-filter-square-meter-slider-min'] . "-" . $this->filterOptionDatas['mib-filter-square-meter-slider-max']]);
                }

                if (isset($this->filterOptionDatas['mib-filter-price_range']) && $this->filterOptionDatas['mib-filter-price_range'] == true && (isset($this->filterOptionDatas['mib-filter-price-slider-min']) && isset($this->filterOptionDatas['mib-filter-price-slider-max']))) {
                    $arg = array_merge($arg, ['price' => $this->filterOptionDatas['mib-filter-price-slider-min'] . "-" . $this->filterOptionDatas['mib-filter-price-slider-max']]);
                }

                //nem elérhetők elrejtése.
                if (!empty($this->selectedShortcodeOption)) {

                    if (in_array('hide_unavailable', $this->selectedShortcodeOption['extras']) ) {
                        $arg['status'] = 'Available';
                    }
                }

                if (!empty($this->selectedApartmanNames)) {
                    $arg['name'] = $this->selectedApartmanNames;
                }

                if (!empty($this->shortcodeType)) {
                    $arg['type'] = $this->shortcodeType;
                }

                // Add filterArgs to $arg
                if (!empty($filterArgs)) {
                    $arg = array_merge($arg, $filterArgs);
                }

                $arg['residentialParkId'] = $this->residentialParkId;
                $all_data = $mibAuth->getApartmentsForFrontEnd($perpage, 1, $arg);

            }

            $table_data = $this->setDataToTable($all_data);
            return [$table_data, $all_data['total'], $recommendDatas];
        }
    }

    private function getRecommendedDatas($datas)
    {
        $all_datas = [];
        $mibAuth = new MibAuthController();
        $options = $mibAuth->getOptionDatas();

        if (!empty($options)) {
            $expired = $mibAuth->checkExpireToken($options['expiry']);

            if ($expired) {
                $mibAuth->loginToMib();
                $options = $mibAuth->getOptionDatas();
            }

            $arg = [];

            if (isset($this->filterOptionCrossSellDatas['mib-cross-floor']) && $this->filterOptionCrossSellDatas['mib-cross-floor'] == 1) {
                $arg['floor'] = $datas['data'][0]->bruttoFloorArea;
            }

            if (isset($this->filterOptionCrossSellDatas['mib-cross-room']) && $this->filterOptionCrossSellDatas['mib-cross-room'] == 1) {
                $arg['numberOfRooms'] = $datas['data'][0]->numberOfRooms;
            }

            if (isset($this->filterOptionCrossSellDatas['mib-cross-orientation']) && $this->filterOptionCrossSellDatas['mib-cross-orientation'] == 1) {
                $arg['orientation'] = $datas['data'][0]->orientation;
            }

            if (!empty($arg) && !empty($datas['data'])) {
                $recommend_data = $mibAuth->getApartmentsForFrontEnd(6, 1, $arg);

                if (!empty($recommend_data['data'])) {
                    foreach ($recommend_data['data'] as $index => $data) {
                        $apartman = $mibAuth->getOneApartment($data->id);

                        if (!empty($apartman['data']) && ($apartman['data'][0]->id != $datas['data'][0]->id)) {
                            $filteredPngDocuments = array_values(array_filter($datas['data'][0]->apartmentsImages, fn($doc) => $doc->extension === 'png'));

                            $all_datas[] = [
                                "image" => $filteredPngDocuments[0]->src,
                                "name" => $apartman['data'][0]->name,
                                "price" => number_format($datas['data'][0]->price, 0) . ' Ft',
                                'id' => $apartman['data'][0]->id
                            ];
                        }
                    }
                }
            }

            return $all_datas;
        }
    }
}