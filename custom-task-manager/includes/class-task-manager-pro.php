<?php

class Task_Manager_Pro {
    private static $instance = null;
    
    public static function instance() {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    private function __construct() {
        register_deactivation_hook(__FILE__, array($this, 'deactivate'));
        $this->includes();
        $this->init_hooks();
    }
    
    private function includes() {
        require_once TASK_MANAGER_PRO_PLUGIN_DIR . 'includes/class-roles.php';
        require_once TASK_MANAGER_PRO_PLUGIN_DIR . 'includes/class-database.php';
        require_once TASK_MANAGER_PRO_PLUGIN_DIR . 'includes/class-shortcodes.php';
        require_once TASK_MANAGER_PRO_PLUGIN_DIR . 'includes/class-forms.php';
        require_once TASK_MANAGER_PRO_PLUGIN_DIR . 'includes/class-ajax.php';
        new Task_Manager_Pro_Shortcodes();
        new Task_Manager_Pro_Forms();
        new Task_Manager_Pro_Ajax();
    }
    
    private function init_hooks() {
        add_action('init', array($this, 'load_textdomain'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_assets'));
        add_action('admin_init', array($this, 'restrict_admin_access'));
        add_action('plugins_loaded', array($this, 'verify_tables_exist'));
    }
    
    public function setup_database_tables() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'task_manager_tasks';
        
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            Task_Manager_Pro_Database::create_tables();
            
            if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
                error_log('Task Manager Pro: Failed to create database tables');
            }
        }
    }
    
    public function verify_tables_exist() {
        global $wpdb;
        $table_name = $wpdb->prefix . 'task_manager_tasks';
        
        if ($wpdb->get_var("SHOW TABLES LIKE '$table_name'") != $table_name) {
            $this->setup_database_tables();
        }
    }
    
    public function deactivate() {
        // Cleanup if needed
    }
    
    public function load_textdomain() {
        load_plugin_textdomain('task-manager-pro', false, dirname(plugin_basename(__FILE__)) . '/languages/');
    }
    
    public function enqueue_assets() {
        wp_enqueue_style('task-manager-pro-bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css');
        wp_enqueue_style('task-manager-pro-fontawesome', 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');
        wp_enqueue_style('task-manager-pro-style', TASK_MANAGER_PRO_PLUGIN_URL . 'assets/css/style.css');
        wp_enqueue_script('jquery');
        wp_enqueue_script('task-manager-pro-bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js', array('jquery'), '', true);
        wp_enqueue_script('task-manager-pro-script', TASK_MANAGER_PRO_PLUGIN_URL . 'assets/js/script.js', array('jquery'), TASK_MANAGER_PRO_VERSION, true);
        wp_localize_script('task-manager-pro-script', 'taskManagerPro', array(
            'ajaxurl' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('task-manager-pro-nonce'),
            'currentUserCan' => array(
                'edit_assignments' => current_user_can('project_manager')
            ),
            'teamMembers' => $this->get_team_members_data()
        ));
    }
    
    private function get_team_members_data() {
        $team_members = get_users(['role' => 'team_member']);
        $data = [];
        foreach ($team_members as $member) {
            $data[] = [
                'ID' => $member->ID,
                'display_name' => $member->display_name
            ];
        }
        return $data;
    }
    
    public function restrict_admin_access() {
        if (defined('DOING_AJAX') && DOING_AJAX) return;
        
        // Prevent access to wp-admin for custom roles
        if (current_user_can('project_manager') || current_user_can('team_member')) {
            if (is_admin() && !current_user_can('administrator')) {
                wp_redirect(home_url('/task-dashboard'));
                exit;
            }
        }
        
        // Prevent access to wp-login.php for custom roles
        if (strpos($_SERVER['REQUEST_URI'], 'wp-login.php') !== false) {
            if (current_user_can('project_manager') || current_user_can('team_member')) {
                wp_redirect(home_url('/task-dashboard'));
                exit;
            }
        }
    }
    
    public function create_pages() {
        $pages = array(
            'task-login' => array(
                'title' => 'Task Login',
                'content' => '[task_login]',
                'status' => 'publish'
            ),
            'task-dashboard' => array(
                'title' => 'Task Dashboard',
                'content' => '[task_dashboard]',
                'status' => 'publish'
            ),
            'task-user-list' => array(
                'title' => 'User List',
                'content' => '[task_user_list]',
                'status' => 'publish'
            ),
            'task-create-task' => array(
                'title' => 'Create Task',
                'content' => '[task_create_task]',
                'status' => 'publish'
            ),
            'task-all-tasks' => array(
                'title' => 'All Tasks',
                'content' => '[task_all_tasks]',
                'status' => 'publish'
            ),
            'task-my-tasks' => array(
                'title' => 'My Tasks',
                'content' => '[task_my_tasks]',
                'status' => 'publish'
            ),
            'register-member' => array(
                'title' => 'Members',
                'content' => '[register_member]',
                'status' => 'publish'
            )
        );
        
        foreach ($pages as $slug => $page) {
            if (!get_page_by_path($slug)) {
                wp_insert_post(array(
                    'post_title' => $page['title'],
                    'post_name' => $slug,
                    'post_content' => $page['content'],
                    'post_status' => $page['status'],
                    'post_type' => 'page'
                ));
            }
        }
    }
}