<?php

/**
 * @package Active
 */

namespace Inc\Base;

class MibActivate
{

	public static function activate()
	{
		flush_rewrite_rules();

		$default = array();

		if (!get_option('mib_options')) {
			update_option('mib_options', $default);
		}

		self::sync_rezideo_links_table_schema();
	}

	public static function sync_rezideo_links_table_schema()
	{
		global $wpdb;
		$table_name = $wpdb->prefix . 'mib_rezideo_links';
		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE $table_name (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            entity_type varchar(50) DEFAULT 'apartment',
            entity_id bigint(20) unsigned DEFAULT NULL,
            park_id bigint(20) unsigned DEFAULT NULL,
            identifier varchar(255) NOT NULL,
            type varchar(50) NOT NULL,
            media_type varchar(20) DEFAULT 'image',
            url text NOT NULL,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY identifier (identifier),
            KEY entity_lookup (entity_type, entity_id),
            KEY park_lookup (park_id)
        ) $charset_collate;";

		require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
		dbDelta($sql);
	}

}
