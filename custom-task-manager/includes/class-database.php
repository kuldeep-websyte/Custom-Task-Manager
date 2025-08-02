<?php
class Task_Manager_Pro_Database {
    public static function create_tables() {
        global $wpdb;
        
        $task_table = $wpdb->prefix . 'task_manager_tasks';
        $login_table = $wpdb->prefix . 'task_manager_logins';
        $charset_collate = $wpdb->get_charset_collate();

        $task_sql = "CREATE TABLE $task_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            title varchar(255) NOT NULL,
            description text NOT NULL,
            project_manager_id bigint(20) NOT NULL,
            team_member_id bigint(20) NOT NULL,
            status varchar(50) NOT NULL DEFAULT 'open',
            due_date datetime DEFAULT NULL,
            created_at datetime DEFAULT CURRENT_TIMESTAMP,
            updated_at datetime DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            PRIMARY KEY  (id),
            KEY project_manager_id (project_manager_id),
            KEY team_member_id (team_member_id)
        ) $charset_collate;";

        $login_sql = "CREATE TABLE $login_table (
            id mediumint(9) NOT NULL AUTO_INCREMENT,
            user_id bigint(20) NOT NULL,
            username varchar(60) NOT NULL,
            email varchar(100) NOT NULL,
            role varchar(20) NOT NULL,
            login_time datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
            ip_address varchar(45) NOT NULL,
            user_agent text NOT NULL,
            login_status varchar(20) NOT NULL,
            PRIMARY KEY  (id)
        ) $charset_collate;";

        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($task_sql);
        dbDelta($login_sql);
    }
    
    public static function drop_tables() {
        global $wpdb;
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}task_manager_tasks");
        $wpdb->query("DROP TABLE IF EXISTS {$wpdb->prefix}task_manager_logins");
    }
    
    public static function record_auth($user_id, $username, $email, $role, $status) {
        global $wpdb;
        
        $wpdb->insert("{$wpdb->prefix}task_manager_logins", [
            'user_id' => $user_id,
            'username' => $username,
            'email' => $email,
            'role' => $role,
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'login_status' => $status
        ]);
    }
}