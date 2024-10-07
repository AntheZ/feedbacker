<?php

if (!defined('ABSPATH')) exit;

class Feedbacker_DB_Manager {
    public function create_db_tables() {
        global $wpdb;
        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}fdbkr_modules (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            name tinytext NOT NULL,
            description text NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        $sql .= "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}fdbkr_user_settings (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            settings text NOT NULL,
            PRIMARY KEY  (id),
            FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID) ON DELETE CASCADE
        ) $charset_collate;";

        $sql .= "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}fdbkr_user_stats (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            stats text NOT NULL,
            PRIMARY KEY  (id),
            FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID) ON DELETE CASCADE
        ) $charset_collate;";

        $sql .= "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}fdbkr_modules_extra (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            module_id mediumint(9) NOT NULL,
            extra_info text NOT NULL,
            PRIMARY KEY  (id),
            FOREIGN KEY (module_id) REFERENCES {$wpdb->prefix}fdbkr_modules(id) ON DELETE CASCADE
        ) $charset_collate;";

        $sql .= "CREATE TABLE IF NOT EXISTS {$wpdb->prefix}fdbkr_user_errors (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            error_message text NOT NULL,
            error_time datetime DEFAULT CURRENT_TIMESTAMP NOT NULL,
            PRIMARY KEY  (id),
            FOREIGN KEY (user_id) REFERENCES {$wpdb->prefix}users(ID) ON DELETE CASCADE
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }

    public function clear_db_tables() {
        global $wpdb;
        $tables = ['fdbkr_modules', 'fdbkr_user_settings', 'fdbkr_user_stats', 'fdbkr_modules_extra', 'fdbkr_user_errors'];
        foreach ($tables as $table) {
            $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}$table;");
        }
        $this->create_db_tables(); // Перестворити таблиці після видалення
    }
}
