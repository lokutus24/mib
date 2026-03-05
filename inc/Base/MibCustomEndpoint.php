<?php

namespace Inc\Base;

use WP_REST_Request;

class MibCustomEndpoint extends MibBaseController
{

    public function registerFunction()
    {
        add_action('rest_api_init', array($this, 'create_custom_endpoint'));
    }

    public function create_custom_endpoint()
    {
        register_rest_route('custom/v1', '/upload', [
            'methods' => 'POST',
            'callback' => [$this, 'custom_endpoint_callback'],
            'permission_callback' => [$this, 'custom_permission_callback'],
            'args' => [
                'type' => [
                    'required' => true,
                    'validate_callback' => function ($param) {
                        return in_array($param, ['alaprajz', 'szintrajz', 'lakas_kep', 'document', 'datasheet', 'other']);
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
                'park_id' => [
                    'required' => true,
                    'type' => 'integer',
                ],
            ],
        ]);

        register_rest_route('custom/v1', '/attachments', [
            'methods' => 'GET',
            'callback' => [$this, 'get_attachments_callback'],
            'permission_callback' => [$this, 'custom_permission_callback'],
            'args' => [
                'park_id' => [
                    'required' => true,
                    'type' => 'integer',
                ],
                'identifier' => [
                    'required' => false,
                    'type' => 'string',
                ],
                'property_id' => [
                    'required' => false,
                    'type' => 'integer',
                ],
            ],
        ]);
    }

    private function get_existing_attachment_by_type($identifier, $type, $park_id, $filename = null)
    {
        global $wpdb;

        // Lekérdezzük az attachment ID-t az identifier és type alapján
        $sql = "
            SELECT pm.post_id
            FROM {$wpdb->postmeta} pm
            INNER JOIN {$wpdb->posts} p ON pm.post_id = p.ID
            WHERE pm.meta_key = 'identifier' AND pm.meta_value = %s
            AND EXISTS (
                SELECT 1 FROM {$wpdb->postmeta} pm2
                WHERE pm2.post_id = pm.post_id AND pm2.meta_key = 'type' AND pm2.meta_value = %s
            )
            AND EXISTS (
                SELECT 1 FROM {$wpdb->postmeta} pm3
                WHERE pm3.post_id = pm.post_id AND pm3.meta_key = 'park_id' AND pm3.meta_value = %s
            )
        ";

        $args = [$identifier, $type, $park_id];

        if ($filename) {
            $sql .= " AND p.post_title = %s ";
            $args[] = $filename;
        }

        $sql .= " LIMIT 1";

        $attachment_id = $wpdb->get_var($wpdb->prepare($sql, ...$args));

        return $attachment_id ? intval($attachment_id) : false;
    }

    public function custom_endpoint_callback(WP_REST_Request $request)
    {
        // Betöltjük a szükséges WordPress fájlokat
        if (!function_exists('wp_handle_upload')) {
            require_once ABSPATH . 'wp-admin/includes/file.php';
        }

        $type = $request->get_param('type');
        $identifier = $request->get_param('identifier');
        $property_id = $request->get_param('property_id');
        $park_id = $request->get_param('park_id');
        $rezideo_update = $request->get_param('rezideo_update'); // Check for Rezideo update flag

        // HA Rezideo update kérés érkezik, akkor NEM várunk fájlt, hanem csak metát frissítünk a LAKÁSON (Post).
        if ($rezideo_update) {
            $url = $request->get_param('url');
            if (empty($url)) {
                return new \WP_Error('missing_url', 'Az URL hiányzik a Rezideo frissítéshez.', ['status' => 400]);
            }

            $post_id = $this->get_post_id_by_property_id($property_id);

            // Determine media_type (from request or guess from URL)
            $media_type = $request->get_param('media_type');
            if (empty($media_type)) {
                $extension = strtolower(pathinfo(parse_url($url, PHP_URL_PATH), PATHINFO_EXTENSION));
                $media_type = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']) ? 'image' : 'document';
            }

            // Shadow Table Strategy: Save to custom table directly
            global $wpdb;
            $table_name = $wpdb->prefix . 'mib_rezideo_links';

            // Check if record exists (by URL)
            $existing_id = $wpdb->get_var($wpdb->prepare(
                "SELECT id FROM $table_name WHERE identifier = %s AND url = %s",
                $identifier,
                $url
            ));

            if ($existing_id) {
                // Update
                $wpdb->update(
                    $table_name,
                    ['type' => $type, 'media_type' => $media_type, 'updated_at' => current_time('mysql')],
                    ['id' => $existing_id],
                    ['%s', '%s', '%s'],
                    ['%d']
                );
                $action = 'updated';
            } else {
                // Insert
                $wpdb->insert(
                    $table_name,
                    [
                        'identifier' => $identifier,
                        'type' => $type,
                        'media_type' => $media_type,
                        'url' => $url,
                        'updated_at' => current_time('mysql')
                    ],
                    ['%s', '%s', '%s', '%s', '%s']
                );
                $action = 'inserted';
            }

            return rest_ensure_response([
                'success' => true,
                'message' => 'Rezideo link mentve a shadow táblába (' . $action . ').',
                'identifier' => $identifier,
                'media_type' => $media_type,
                'type' => $type,
                'url' => $url
            ]);
        }



        // Ellenőrizzük, hogy a fájl létezik-e (NORMÁL MŰKÖDÉS)
        $file = $request->get_file_params(); // Fix: Retrieve files from request

        if (!isset($file['file'])) {
            return new \WP_Error('missing_file', 'A fájl hiányzik.', ['status' => 400]);
        }

        // Ellenőrizzük, hogy van-e már meglévő attachment ehhez a type-hoz
        // Minden típusnál engedélyezzük a többszöri feltöltést, kivéve ha a fájlnév egyezik.

        $filename = basename($file['file']['name']);

        // Mindig a konkrét fájlnévre szűrünk, hogy elkerüljük a duplikációt,
        // de ne töröljük a többi, azonos típusú de más nevű fájlt.
        $check_filename = $filename;

        $existing_attachment_id = $this->get_existing_attachment_by_type($identifier, $type, $park_id, $check_filename);

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
            'guid' => $upload['url'],
            'post_mime_type' => $upload['type'],
            'post_title' => basename($upload['file']),
            'post_content' => '',
            'post_status' => 'inherit',
            'post_type' => 'attachment',
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
        update_post_meta($attachment_id, 'park_id', $park_id);

        return rest_ensure_response([
            'message' => 'A fájl sikeresen feltöltve.',
            'attachment_id' => $attachment_id,
            'file_url' => $upload['url'],
        ]);
    }

    public function get_attachments_callback(WP_REST_Request $request)
    {
        $park_id = $request->get_param('park_id');
        $identifier = $request->get_param('identifier');
        $property_id = $request->get_param('property_id');

        $attachments = [];

        if ($identifier) {
            $attachments = $this->get_attachments_by_meta_values($identifier, $park_id);
        } else {
            $meta_query = [
                [
                    'key' => 'park_id',
                    'value' => $park_id,
                ],
            ];

            if ($property_id) {
                $meta_query[] = [
                    'key' => 'property_id',
                    'value' => $property_id,
                ];
            }

            $query = new \WP_Query([
                'post_type' => 'attachment',
                'post_status' => 'inherit',
                'posts_per_page' => -1,
                'meta_query' => $meta_query,
            ]);

            foreach ($query->posts as $post) {
                $attachments[] = [
                    'attachment_id' => $post->ID,
                    'attachment_url' => wp_get_attachment_url($post->ID),
                    'type' => get_post_meta($post->ID, 'type', true),
                    'identifier' => get_post_meta($post->ID, 'identifier', true),
                    'property_id' => get_post_meta($post->ID, 'property_id', true),
                    'park_id' => get_post_meta($post->ID, 'park_id', true),
                ];
            }
        }

        return rest_ensure_response($attachments);
    }

    /**
     * Permission callback.
     *
     * @param WP_REST_Request $request
     * @return bool|\WP_Error
     */
    public function custom_permission_callback(WP_REST_Request $request)
    {
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