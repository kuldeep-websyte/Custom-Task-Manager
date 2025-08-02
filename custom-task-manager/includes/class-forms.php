<?php
class Task_Manager_Pro_Forms {
    public function __construct() {
        add_action('init', array($this, 'process_login_form'));
        add_action('init', array($this, 'process_registration_form'));
        add_action('init', array($this, 'process_task_form'));
        add_action('init', array($this, 'process_add_user_form'));
        add_action('init', array($this, 'process_edit_user_form'));
        add_action('init', array($this, 'process_delete_user_form'));
    }
    
   public function process_login_form() {
    if (!isset($_POST['task_manager_login'])) return;

    $credentials = [
        'user_login'    => sanitize_user($_POST['username']),
        'user_password' => $_POST['password'],
        'remember'      => isset($_POST['rememberme'])
    ];

  
    $user = wp_signon($credentials, false);

    if (is_wp_error($user)) {
      
        $error_code = $user->get_error_code();
        wp_redirect(add_query_arg([
            'login' => 'failed',
            'code' => $error_code
        ], wp_get_referer()));
        exit;
    }

 
    if (!in_array('project_manager', $user->roles) && !in_array('team_member', $user->roles)) {
        wp_logout();
        wp_redirect(add_query_arg('login', 'invalid_role', wp_get_referer()));
        exit;
    }


    wp_redirect(home_url('/task-dashboard'));
    exit;
}
    
   public function process_registration_form() {
    
    if (!isset($_POST['task_manager_register'])) {
        return;
    }

    if (!wp_verify_nonce($_POST['_wpnonce'], 'task-manager-register')) {
        wp_die(__('Security verification failed. Please try again.', 'task-manager-pro'));
    }

    $username = sanitize_user($_POST['username'] ?? '', true);
    $email = sanitize_email($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $role = in_array($_POST['role'] ?? '', ['project_manager', 'team_member']) ? $_POST['role'] : 'team_member';

    if (empty($username) || empty($email) || empty($password)) {
        wp_redirect(add_query_arg([
            'register' => 'failed',
            'message' => urlencode(__('All fields are required', 'task-manager-pro'))
        ], wp_get_referer()));
        exit;
    }

    if (username_exists($username)) {
        wp_redirect(add_query_arg([
            'register' => 'failed',
            'message' => urlencode(__('Username already exists', 'task-manager-pro'))
        ], wp_get_referer()));
        exit;
    }

    if (email_exists($email)) {
        wp_redirect(add_query_arg([
            'register' => 'failed',
            'message' => urlencode(__('Email already registered', 'task-manager-pro'))
        ], wp_get_referer()));
        exit;
    }

    $user_id = wp_create_user($username, $password, $email);

    if (is_wp_error($user_id)) {
        wp_redirect(add_query_arg([
            'register' => 'failed',
            'message' => urlencode($user_id->get_error_message())
        ], wp_get_referer()));
        exit;
    }

    $user = new WP_User($user_id);
    if (!in_array($role, ['project_manager', 'team_member'])) {
        $role = 'team_member';
    }
    $user->set_role($role);

    wp_set_auth_cookie($user_id);
    wp_set_current_user($user_id);
    do_action('wp_login', $username, $user);

    global $wpdb;
    $wpdb->insert("{$wpdb->prefix}task_manager_logins", [
        'user_id' => $user_id,
        'username' => $username,
        'email' => $email,
        'role' => $role,
        'ip_address' => $_SERVER['REMOTE_ADDR'],
        'user_agent' => $_SERVER['HTTP_USER_AGENT'],
        'login_status' => 'registered',
        'login_time' => current_time('mysql')
    ]);

    wp_redirect(add_query_arg([
        'register' => 'success',
        'user_id' => $user_id
    ], home_url('/task-dashboard')));
    exit;
}
    
    public function process_task_form() {
        if (!isset($_POST['task_manager_create_task'])) return;
        
        if (!wp_verify_nonce($_POST['_wpnonce'], 'task-manager-create-task')) {
            wp_die(__('Security check failed', 'task-manager-pro'));
        }
        
        if (!current_user_can('project_manager')) {
            wp_die(__('Permission denied', 'task-manager-pro'));
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'task_manager_tasks';
        
        $data = array(
            'title' => sanitize_text_field($_POST['title']),
            'description' => sanitize_textarea_field($_POST['description']),
            'project_manager_id' => get_current_user_id(),
            'team_member_id' => intval($_POST['team_member_id']),
            'status' => 'open',
            'due_date' => sanitize_text_field($_POST['due_date'])
        );
        
        $format = array('%s', '%s', '%d', '%d', '%s', '%s');
        
        $result = $wpdb->insert($table_name, $data, $format);
        
        if ($result) {
            wp_redirect(add_query_arg('task', 'created', home_url('/task-all-tasks')));
        } else {
            wp_redirect(add_query_arg('task', 'failed', wp_get_referer()));
        }
        exit;
    }
    
    public function process_add_user_form() {
        if (!isset($_POST['task_manager_add_user'])) return;
        
        if (!wp_verify_nonce($_POST['_wpnonce'], 'task-manager-add-user')) {
            wp_die(__('Security check failed', 'task-manager-pro'));
        }
        
        if (!current_user_can('project_manager')) {
            wp_die(__('Permission denied', 'task-manager-pro'));
        }
        
        $username = sanitize_user($_POST['username']);
        $email = sanitize_email($_POST['email']);
        $password = $_POST['password'];
        $role = in_array($_POST['role'], ['project_manager', 'team_member']) ? $_POST['role'] : 'team_member';
        
        if (empty($username) || empty($email) || empty($password)) {
            wp_redirect(add_query_arg('user', 'validation_failed', wp_get_referer()));
            exit;
        }
        
        if (username_exists($username)) {
            wp_redirect(add_query_arg('user', 'username_exists', wp_get_referer()));
            exit;
        }
        
        if (email_exists($email)) {
            wp_redirect(add_query_arg('user', 'email_exists', wp_get_referer()));
            exit;
        }
    
        $user_id = wp_create_user($username, $password, $email);
        
        if (is_wp_error($user_id)) {
            wp_redirect(add_query_arg('user', 'creation_failed', wp_get_referer()));
            exit;
        }
       
        $user = new WP_User($user_id);
        $user->set_role($role);
       
        global $wpdb;
        $wpdb->insert("{$wpdb->prefix}task_manager_logins", [
            'user_id' => $user_id,
            'username' => $username,
            'email' => $email,
            'role' => $role,
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
            'login_status' => 'created',
            'login_time' => current_time('mysql')
        ]);
        
        wp_redirect(add_query_arg('user', 'created', wp_get_referer()));
        exit;
    }
    
    public function process_edit_user_form() {
        if (!isset($_POST['task_manager_edit_user'])) return;
        
        if (!wp_verify_nonce($_POST['_wpnonce'], 'task-manager-edit-user')) {
            wp_die(__('Security check failed', 'task-manager-pro'));
        }
        
        if (!current_user_can('project_manager')) {
            wp_die(__('Permission denied', 'task-manager-pro'));
        }
        
        $user_id = intval($_POST['user_id']);
        $display_name = sanitize_text_field($_POST['display_name']);
        $email = sanitize_email($_POST['email']);
        $role = in_array($_POST['role'], ['project_manager', 'team_member']) ? $_POST['role'] : 'team_member';
        $password = $_POST['password'];
        
        $user = get_user_by('id', $user_id);
        if (!$user) {
            wp_redirect(add_query_arg('user', 'not_found', wp_get_referer()));
            exit;
        }
    
        $user_data = array(
            'ID' => $user_id,
            'display_name' => $display_name,
            'user_email' => $email
        );
        
        if (!empty($password)) {
            $user_data['user_pass'] = $password;
        }
        
        $result = wp_update_user($user_data);
        
        if (is_wp_error($result)) {
            wp_redirect(add_query_arg('user', 'update_failed', wp_get_referer()));
            exit;
        }
    
        $user = new WP_User($user_id);
        $user->set_role($role);
        
        wp_redirect(add_query_arg('user', 'updated', wp_get_referer()));
        exit;
    }
    
    public function process_delete_user_form() {
        if (!isset($_POST['task_manager_delete_user'])) return;
        
        if (!wp_verify_nonce($_POST['_wpnonce'], 'task-manager-delete-user')) {
            wp_die(__('Security check failed', 'task-manager-pro'));
        }
        
        if (!current_user_can('project_manager')) {
            wp_die(__('Permission denied', 'task-manager-pro'));
        }
        
        $user_id = intval($_POST['user_id']);
        
        if ($user_id === get_current_user_id()) {
            wp_redirect(add_query_arg('user', 'cannot_delete_self', wp_get_referer()));
            exit;
        }
        
        $user = get_user_by('id', $user_id);
        if (!$user) {
            wp_redirect(add_query_arg('user', 'not_found', wp_get_referer()));
            exit;
        }
     
        global $wpdb;
        $task_count = $wpdb->get_var($wpdb->prepare(
            "SELECT COUNT(*) FROM {$wpdb->prefix}task_manager_tasks WHERE team_member_id = %d",
            $user_id
        ));
        
        if ($task_count > 0) {
            wp_redirect(add_query_arg('user', 'has_tasks', wp_get_referer()));
            exit;
        }
     
        $result = wp_delete_user($user_id);
        
        if ($result) {
            wp_redirect(add_query_arg('user', 'deleted', wp_get_referer()));
        } else {
            wp_redirect(add_query_arg('user', 'delete_failed', wp_get_referer()));
        }
        exit;
    }
}