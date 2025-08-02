<?php
class Task_Manager_Pro_Ajax {
    public function __construct() {
        add_action('wp_ajax_update_task_status', array($this, 'update_task_status'));
        add_action('wp_ajax_get_task_details', array($this, 'get_task_details'));
        add_action('wp_ajax_edit_task', array($this, 'edit_task'));
        add_action('wp_ajax_delete_task', array($this, 'delete_task'));
        add_action('wp_ajax_get_user_details', array($this, 'get_user_details'));
        add_action('wp_ajax_filter_tasks', array($this, 'filter_tasks'));
    }
    
    public function update_task_status() {
        check_ajax_referer('task-manager-pro-nonce', 'nonce');
        
        if (!current_user_can('team_member')) {
            wp_send_json_error(array('message' => __('Permission denied', 'task-manager-pro')));
        }
        
        $task_id = intval($_POST['task_id']);
        $status = sanitize_text_field($_POST['status']);
        
        $allowed_statuses = array('open', 'in-progress', 'in-review', 'pending', 'completed');
        if (!in_array($status, $allowed_statuses)) {
            wp_send_json_error(array('message' => __('Invalid status', 'task-manager-pro')));
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'task_manager_tasks';
        
        $updated = $wpdb->update(
            $table_name,
            array('status' => $status),
            array('id' => $task_id, 'team_member_id' => get_current_user_id()),
            array('%s'),
            array('%d', '%d')
        );
        
        if ($updated) {
            wp_send_json_success(array('message' => __('Status updated', 'task-manager-pro')));
        } else {
            wp_send_json_error(array('message' => __('Update failed', 'task-manager-pro')));
        }
    }
    
    public function get_task_details() {
        

        check_ajax_referer('task-manager-pro-nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => __('Please login', 'task-manager-pro')));
        }
        
        $task_id = intval($_POST['task_id']);
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'task_manager_tasks';
        
        if (current_user_can('project_manager')) {
            $task = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $table_name WHERE id = %d",
                $task_id
            ));
        } else {
            $task = $wpdb->get_row($wpdb->prepare(
                "SELECT * FROM $table_name WHERE id = %d AND team_member_id = %d",
                $task_id,
                get_current_user_id()
            ));
        }
        
        if ($task) {
            $team_member = get_user_by('id', $task->team_member_id);
            $project_manager = get_user_by('id', $task->project_manager_id);
            
            wp_send_json_success(array(
                'title' => esc_html($task->title),
                'description' => esc_html($task->description),
                'status' => esc_html($task->status),
                'due_date' => $task->due_date ? date('M j, Y', strtotime($task->due_date)) : __('No due date', 'task-manager-pro'),
                'team_member' => $team_member ? esc_html($team_member->display_name) : __('Unassigned', 'task-manager-pro'),
                'project_manager' => $project_manager ? esc_html($project_manager->display_name) : __('Unknown', 'task-manager-pro')
            ));
        } else {
            wp_send_json_error(array('message' => __('Task not found', 'task-manager-pro')));
        }
    }
    
    public function edit_task() {
        check_ajax_referer('task-manager-pro-nonce', 'nonce');
        
        if (!current_user_can('project_manager')) {
            wp_send_json_error(array('message' => __('Permission denied', 'task-manager-pro')));
        }
        
        $task_id = intval($_POST['task_id']);
        $title = sanitize_text_field($_POST['title']);
        $description = sanitize_textarea_field($_POST['description']);
        $team_member_id = intval($_POST['team_member_id']);
        $status = sanitize_text_field($_POST['status']);
        $due_date = sanitize_text_field($_POST['due_date']);
        
        $allowed_statuses = array('open', 'in-progress', 'in-review', 'pending', 'completed');
        if (!in_array($status, $allowed_statuses)) {
            wp_send_json_error(array('message' => __('Invalid status', 'task-manager-pro')));
        }
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'task_manager_tasks';
        
        $updated = $wpdb->update(
            $table_name,
            array(
                'title' => $title,
                'description' => $description,
                'team_member_id' => $team_member_id,
                'status' => $status,
                'due_date' => $due_date
            ),
            array('id' => $task_id),
            array('%s', '%s', '%d', '%s', '%s'),
            array('%d')
        );
        
        if ($updated !== false) {
            wp_send_json_success(array('message' => __('Task updated successfully', 'task-manager-pro')));
        } else {
            wp_send_json_error(array('message' => __('Update failed', 'task-manager-pro')));
        }
    }
    
    public function delete_task() {
        check_ajax_referer('task-manager-pro-nonce', 'nonce');
        
        if (!current_user_can('project_manager')) {
            wp_send_json_error(array('message' => __('Permission denied', 'task-manager-pro')));
        }
        
        $task_id = intval($_POST['task_id']);
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'task_manager_tasks';
        
        $deleted = $wpdb->delete(
            $table_name,
            array('id' => $task_id),
            array('%d')
        );
        
        if ($deleted) {
            wp_send_json_success(array('message' => __('Task deleted successfully', 'task-manager-pro')));
        } else {
            wp_send_json_error(array('message' => __('Delete failed', 'task-manager-pro')));
        }
    }
    
    public function get_user_details() {
        check_ajax_referer('task-manager-pro-nonce', 'nonce');
        
        if (!current_user_can('project_manager')) {
            wp_send_json_error(array('message' => __('Permission denied', 'task-manager-pro')));
        }
        
        $user_id = intval($_POST['user_id']);
        $user = get_user_by('id', $user_id);
        
        if ($user) {
            $user_roles = $user->roles;
            $role = in_array('project_manager', $user_roles) ? 'project_manager' : 'team_member';
            
            wp_send_json_success(array(
                'user_id' => $user->ID,
                'display_name' => $user->display_name,
                'email' => $user->user_email,
                'role' => $role
            ));
        } else {
            wp_send_json_error(array('message' => __('User not found', 'task-manager-pro')));
        }
    }
    
    public function filter_tasks() {
        check_ajax_referer('task-manager-pro-nonce', 'nonce');
        
        if (!is_user_logged_in()) {
            wp_send_json_error(array('message' => __('Please login', 'task-manager-pro')));
        }
        
        $status = sanitize_text_field($_POST['status']);
        $user_id = get_current_user_id();
        
        global $wpdb;
        $table_name = $wpdb->prefix . 'task_manager_tasks';
        
        if (current_user_can('project_manager')) {
            if ($status === 'all') {
                $tasks = $wpdb->get_results("SELECT * FROM $table_name ORDER BY due_date ASC");
            } else {
                $tasks = $wpdb->get_results($wpdb->prepare(
                    "SELECT * FROM $table_name WHERE status = %s ORDER BY due_date ASC",
                    $status
                ));
            }
        } else {
            if ($status === 'all') {
                $tasks = $wpdb->get_results($wpdb->prepare(
                    "SELECT * FROM $table_name WHERE team_member_id = %d ORDER BY due_date ASC",
                    $user_id
                ));
            } else {
                $tasks = $wpdb->get_results($wpdb->prepare(
                    "SELECT * FROM $table_name WHERE team_member_id = %d AND status = %s ORDER BY due_date ASC",
                    $user_id,
                    $status
                ));
            }
        }
        
        $html = '';
        foreach ($tasks as $task) {
            $team_member = get_user_by('id', $task->team_member_id);
            $status_classes = [
                'open' => 'primary',
                'in-progress' => 'info',
                'in-review' => 'warning',
                'pending' => 'secondary',
                'completed' => 'success'
            ];
            $badge_class = $status_classes[$task->status] ?? 'secondary';
            
            $html .= '<tr data-status="' . esc_attr($task->status) . '">';
            $html .= '<td>' . esc_html($task->title) . '</td>';
            if (current_user_can('project_manager')) {
                $html .= '<td>' . ($team_member ? esc_html($team_member->display_name) : __('Unassigned', 'task-manager-pro')) . '</td>';
            }
            $html .= '<td><span class="badge bg-' . esc_attr($badge_class) . '">' . esc_html(ucfirst(str_replace('-', ' ', $task->status))) . '</span></td>';
            $html .= '<td>' . ($task->due_date ? date('M j, Y', strtotime($task->due_date)) : __('No due date', 'task-manager-pro')) . '</td>';
            $html .= '<td>';
            if (current_user_can('project_manager')) {
                $html .= '<button class="btn btn-sm btn-primary edit-task-btn" data-task-id="' . $task->id . '">' . __('Edit', 'task-manager-pro') . '</button> ';
                $html .= '<button class="btn btn-sm btn-danger delete-task-btn" data-task-id="' . $task->id . '">' . __('Delete', 'task-manager-pro') . '</button>';
            } else {
                $html .= '<button class="btn btn-sm btn-primary view-task-details" data-task-id="' . $task->id . '">' . __('View', 'task-manager-pro') . '</button>';
            }
            $html .= '</td></tr>';
        }
        
        wp_send_json_success(array('html' => $html));
    }
}