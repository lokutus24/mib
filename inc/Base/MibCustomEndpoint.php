<?php

namespace Inc\Base;

use WP_REST_Request;

class MibCustomEndpoint extends MibBaseController {

    public function registerFunction() {
        add_action('rest_api_init', array($this, 'create_custom_endpoint'));
    }

    public function create_custom_endpoint() {
        register_rest_route('custom/v1', '/upload', [
            'methods' => 'POST',
            'callback' => [$this, 'custom_endpoint_callback'],
            'permission_callback' => [$this, 'custom_permission_callback'],
            'args' => [
                'type' => [
                    'required' => true,
                    'validate_callback' => function ($param) {
                        return in_array($param, ['alaprajz', 'szintrajz', 'lakas_kep']);
                    }
                ],
                'identifier' => [
                    'required' => true,
                    'type' => 'string',
                ],
                'property_id' => [
                    'required' => true,
                    'type' => 'integer',
                ],
            ],
        ]);
    }

    private function get_existing_attachment_by_type($identifier, $type) {
        global $wpdb;

        // Lekérdezzük az attachment ID-t az identifier és type alapján
        $attachment_id = $wpdb->get_var($wpdb->prepare("
            SELECT pm.post_id
            FROM {$wpdb->postmeta} pm
            INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
            WHERE pm.meta_key = 'identifier' AND pm.meta_value = %s
            AND EXISTS (
                SELECT 1 FROM {$wpdb->postmeta} pm2 
                WHERE pm2.post_id = pm.post_id AND pm2.meta_key = 'type' AND pm2.meta_value = %s
            )
            LIMIT 1
        ", $identifier, $type));

        return $attachment_id ? intval($attachment_id) : false;
    }

    public function custom_endpoint_callback(WP_REST_Request $request) {
        // Betöltjük a szükséges WordPress fájlokat
        if (!function_exists('wp_handle_upload')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }

        $type = $request->get_param('type');
        $identifier = $request->get_param('identifier');
        $property_id = $request->get_param('property_id');
        $file = $request->get_file_params();

        // Ellenőrizzük, hogy a fájl létezik-e
        if (!isset($file['file'])) {
            return new \WP_Error('missing_file', 'A fájl hiányzik.', ['status' => 400]);
        }

        // Ellenőrizzük, hogy van-e már meglévő attachment ehhez a type-hoz
        $existing_attachment_id = $this->get_existing_attachment_by_type($identifier, $type);

        // Ha létezik, töröljük a régit
        if ($existing_attachment_id) {
            wp_delete_attachment($existing_attachment_id, true);
        }

        // Fájl feltöltése a médiatárba
        $upload = wp_handle_upload($file['file'], ['test_form' => false]);

        if (isset($upload['error'])) {
            return new \WP_Error('upload_error', $upload['error'], ['status' => 500]);
        }

        // Új attachment létrehozása
        $attachment = [
            'guid'           => $upload['url'],
            'post_mime_type' => $upload['type'],
            'post_title'     => basename($upload['file']),
            'post_content'   => '',
            'post_status'    => 'inherit',
            'post_type'      => 'attachment',
        ];

        $attachment_id = wp_insert_attachment($attachment, $upload['file']);

        // Kép metaadatainak generálása
        require_once ABSPATH . 'wp-admin/includes/image.php';
        $attach_data = wp_generate_attachment_metadata($attachment_id, $upload['file']);
        wp_update_attachment_metadata($attachment_id, $attach_data);

        // Metaadatok frissítése: felülírjuk a régit
        update_post_meta($attachment_id, 'type', $type);
        update_post_meta($attachment_id, 'identifier', $identifier);
        update_post_meta($attachment_id, 'property_id', $property_id);

        return rest_ensure_response([
            'message'        => 'A fájl sikeresen feltöltve.',
            'attachment_id'  => $attachment_id,
            'file_url'       => $upload['url'],
        ]);
    }

    /**
     * Permission callback.
     *
     * @param WP_REST_Request $request
     * @return bool|\WP_Error
     */
    public function custom_permission_callback(WP_REST_Request $request) {
        // Az API kulcsot az `Authorization` headerből olvassuk ki
        $auth_header = $request->get_header('Authorization');

        if (!$auth_header) {
            return new \WP_Error('missing_api_key', 'Az API kulcs hiányzik.', ['status' => 403]);
        }
        // Leválasztjuk a 'Bearer ' előtagot
        $api_key = str_replace('Bearer ', '', $auth_header);

        // API kulcs ellenőrzése
        if ($api_key !== $this->mibOptions['mib-login-password']) {
            return new \WP_Error('invalid_api_key', 'Az API kulcs érvénytelen.', ['status' => 403]);
        }

        return true;
    }
}