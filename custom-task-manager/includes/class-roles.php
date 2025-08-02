<?php
class Task_Manager_Pro_Roles {
    public static function create_roles() {
        add_role('project_manager', __('Project Manager', 'task-manager-pro'), array(
            'read' => true,
            'edit_posts' => false,
            'delete_posts' => false,
            'manage_tasks' => true,
            'assign_tasks' => true,
            'manage_users' => true
        ));
        
        add_role('team_member', __('Team Member', 'task-manager-pro'), array(
            'read' => true,
            'edit_posts' => false,
            'delete_posts' => false,
            'view_own_tasks' => true,
            'update_task_status' => true
        ));
    }
    
    public static function remove_roles() {
        remove_role('project_manager');
        remove_role('team_member');
    }
}