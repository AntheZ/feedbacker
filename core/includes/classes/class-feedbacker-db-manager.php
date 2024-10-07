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
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);

        // Додайте схожі запити для створення інших таблиць
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
