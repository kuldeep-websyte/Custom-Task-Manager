jQuery(document).ready(function($) {
  
    console.log('Task Manager Pro loaded:', taskManagerPro);
    
  
    $(document).on('click', '#testAjaxBtn', function() {
        console.log('Test AJAX button clicked');
        $.ajax({
            url: taskManagerPro.ajaxurl,
            type: 'POST',
            data: {
                action: 'get_task_details',
                task_id: 1,
                nonce: taskManagerPro.nonce || ''
            },
            success: function(response) {
                console.log('Test AJAX response:', response);
                alert('AJAX Test: ' + JSON.stringify(response));
            },
            error: function(xhr, status, error) {
                console.error('Test AJAX error:', error);
                alert('AJAX Test Error: ' + error);
            }
        });
    });
   
    $(document).on('change', '.task-status', function() {
        const taskId = $(this).data('task-id');
        const status = $(this).val();
        
        $.ajax({
            url: taskManagerPro.ajaxurl,
            type: 'POST',
            data: {
                action: 'update_task_status',
                task_id: taskId,
                status: status,
                nonce: taskManagerPro.nonce || ''
            },
            beforeSend: function() {
                // Show loading state
            },
            success: function(response) {
                if (response.success) {
                    showToast('success', response.data.message);
                } else {
                    showToast('error', response.data.message);
                }
            },
            error: function() {
                showToast('error', 'Error updating task status');
            }
        });
    });

    $(document).on('change', '#task-filter', function() {
        const status = $(this).val();
        
        $.ajax({
            url: taskManagerPro.ajaxurl,
            type: 'POST',
            data: {
                action: 'filter_tasks',
                status: status,
                nonce: taskManagerPro.nonce || ''
            },
            beforeSend: function() {
                $('tbody').html('<tr><td colspan="5" class="text-center"><div class="spinner-border" role="status"></div></td></tr>');
            },
            success: function(response) {
                if (response.success) {
                    $('tbody').html(response.data.html);
                } else {
                    showToast('error', response.data.message);
                }
            },
            error: function() {
                showToast('error', 'Error filtering tasks');
            }
        });
    });

    $(document).on('click', '.edit-task-btn', function(e) {
        e.preventDefault();
        const taskId = $(this).data('task-id');
        console.log('Edit button clicked for task ID:', taskId);
        openEditModal(taskId);
    });

    
    function openEditModal(taskId) {
        console.log('Opening edit modal for task ID:', taskId);
        console.log('AJAX URL:', taskManagerPro.ajaxurl);
        console.log('Nonce:', taskManagerPro.nonce);
        
        $.ajax({
            url: taskManagerPro.ajaxurl,
            type: 'POST',
            data: {
                action: 'get_task_details',
                task_id: taskId,
                nonce: taskManagerPro.nonce || ''
            },
            beforeSend: function() {
                
                $('#editTaskModal .modal-body').html(`
                    <div class="text-center py-4">
                        <div class="spinner-border" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                `);
                const editModal = new bootstrap.Modal(document.getElementById('editTaskModal'));
                editModal.show();
            },
            success: function(response) {
                if (response.success) {
                    populateEditForm(response.data, taskId);
                } else {
                    alert(response.data.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', status, error);
                console.error('Response:', xhr.responseText);
                alert('Error loading task details: ' + error);
            }
        });
    }

   
    function populateEditForm(taskData, taskId) {
        const editForm = `
            <form id="taskEditForm">
                <input type="hidden" name="task_id" value="${taskId}">
                <div class="mb-3">
                    <label for="editTitle" class="form-label">Title</label>
                    <input type="text" class="form-control" id="editTitle" name="title" value="${taskData.title}" required>
                </div>
                <div class="mb-3">
                    <label for="editDescription" class="form-label">Description</label>
                    <textarea class="form-control" id="editDescription" name="description" rows="5" required>${taskData.description}</textarea>
                </div>
                ${taskManagerPro.currentUserCan.edit_assignments ? `
                <div class="mb-3">
                    <label for="editTeamMember" class="form-label">Assigned To</label>
                    <select class="form-select" id="editTeamMember" name="team_member_id" required>
                        ${getTeamMemberOptions(taskData.team_member_id)}
                    </select>
                </div>
                ` : ''}
                <div class="mb-3">
                    <label for="editStatus" class="form-label">Status</label>
                    <select class="form-select" id="editStatus" name="status" required>
                        <option value="open" ${taskData.status === 'open' ? 'selected' : ''}>Open</option>
                        <option value="in-progress" ${taskData.status === 'in-progress' ? 'selected' : ''}>In Progress</option>
                        <option value="in-review" ${taskData.status === 'in-review' ? 'selected' : ''}>In Review</option>
                        <option value="pending" ${taskData.status === 'pending' ? 'selected' : ''}>Pending</option>
                        <option value="completed" ${taskData.status === 'completed' ? 'selected' : ''}>Completed</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="editDueDate" class="form-label">Due Date</label>
                    <input type="date" class="form-control" id="editDueDate" name="due_date" value="${taskData.due_date}" required>
                </div>
            </form>
        `;

        $('#editTaskModal .modal-body').html(editForm);
                            const editModal = new bootstrap.Modal(document.getElementById('editTaskModal'));
                    editModal.show();
    }

 
    $('#saveTaskBtn').on('click', function() {
        const formData = $('#taskEditForm').serializeArray();
        formData.push({
            name: 'action',
            value: 'edit_task'
        }, {
            name: 'nonce',
            value: taskManagerPro.nonce || ''
        });

        $.ajax({
            url: taskManagerPro.ajaxurl,
            type: 'POST',
            data: formData,
            beforeSend: function() {
                $('#saveTaskBtn').prop('disabled', true).html(`
                    <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                    Saving...
                `);
            },
            success: function(response) {
                if (response.success) {
                    const editModal = bootstrap.Modal.getInstance(document.getElementById('editTaskModal'));
                    if (editModal) {
                        editModal.hide();
                    }
                    showToast('success', response.data.message);
                    // Refresh or update the task list
                    refreshTaskList();
                } else {
                    showToast('error', response.data.message);
                }
            },
            error: function() {
                showToast('error', 'Error saving task');
            },
            complete: function() {
                $('#saveTaskBtn').prop('disabled', false).text('Save Changes');
            }
        });
    });

  
    function getTeamMemberOptions(selectedId) {
        let options = '';
        if (taskManagerPro.teamMembers && Array.isArray(taskManagerPro.teamMembers)) {
            taskManagerPro.teamMembers.forEach(member => {
                options += `<option value="${member.ID}" ${member.ID == selectedId ? 'selected' : ''}>${member.display_name}</option>`;
            });
        }
        return options;
    }

   
    function refreshTaskList() {
 
        window.location.reload();
    }

    $(document).on('click', '.delete-task-btn', function(e) {
        e.preventDefault();
        const taskId = $(this).data('task-id');
        console.log('Delete button clicked for task ID:', taskId);
        
        if (confirm('Are you sure you want to delete this task?')) {
            $.ajax({
                url: taskManagerPro.ajaxurl,
                type: 'POST',
                data: {
                    action: 'delete_task',
                    task_id: taskId,
                    nonce: taskManagerPro.nonce || ''
                },
                success: function(response) {
                    if (response.success) {
                        showToast('success', response.data.message);
                        $(`tr[data-task-id="${taskId}"]`).remove();
                    } else {
                        showToast('error', response.data.message);
                    }
                },
                error: function() {
                    showToast('error', 'Error deleting task');
                }
            });
        }
    });

    $(document).on('click', '.view-task-details', function(e) {
        e.preventDefault();
        const taskId = $(this).data('task-id');
        
        $.ajax({
            url: taskManagerPro.ajaxurl,
            type: 'POST',
            data: {
                action: 'get_task_details',
                task_id: taskId,
                nonce: taskManagerPro.nonce || ''
            },
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    $('#taskDetailsModalTitle').text(data.title);
                    $('#taskDetailsModalBody').html(`
                        <div class="mb-3">
                            <strong>Description:</strong><br>
                            ${data.description}
                        </div>
                        <div class="mb-3">
                            <strong>Status:</strong> 
                            <span class="badge bg-${getStatusClass(data.status)}">${data.status}</span>
                        </div>
                        <div class="mb-3">
                            <strong>Due Date:</strong> ${data.due_date}
                        </div>
                        <div class="mb-3">
                            <strong>Assigned To:</strong> ${data.team_member}
                        </div>
                        <div class="mb-3">
                            <strong>Created By:</strong> ${data.project_manager}
                        </div>
                    `);
                    const detailsModal = new bootstrap.Modal(document.getElementById('taskDetailsModal'));
                    detailsModal.show();
                } else {
                    showToast('error', response.data.message);
                }
            },
            error: function() {
                showToast('error', 'Error loading task details');
            }
        });
    });

    $(document).on('click', '.edit-user-btn', function(e) {
        e.preventDefault();
        const userId = $(this).data('user-id');
        
        $.ajax({
            url: taskManagerPro.ajaxurl,
            type: 'POST',
            data: {
                action: 'get_user_details',
                user_id: userId,
                nonce: taskManagerPro.nonce || ''
            },
            success: function(response) {
                if (response.success) {
                    const data = response.data;
                    $('#edit_user_id').val(data.user_id);
                    $('#edit_display_name').val(data.display_name);
                    $('#edit_email').val(data.email);
                    $('#edit_role').val(data.role);
                    const userModal = new bootstrap.Modal(document.getElementById('editUserModal'));
                    userModal.show();
                } else {
                    showToast('error', response.data.message);
                }
            },
            error: function() {
                showToast('error', 'Error loading user details');
            }
        });
    });

    $(document).on('click', '.delete-user-btn', function(e) {
        e.preventDefault();
        const userId = $(this).data('user-id');
        const userName = $(this).data('user-name');
        
        if (confirm(`Are you sure you want to delete user "${userName}"?`)) {
            const form = $('<form method="post">')
                .append($('<input type="hidden" name="task_manager_delete_user" value="1">'))
                .append($('<input type="hidden" name="user_id" value="' + userId + '">'))
                .append($('<input type="hidden" name="_wpnonce" value="' + taskManagerPro.nonce + '">'));
            
            $('body').append(form);
            form.submit();
        }
    });

    $(document).on('change', '#role-filter', function() {
        const role = $(this).val();
        if (role) {
            $(`tr[data-role]`).hide();
            $(`tr[data-role="${role}"]`).show();
        } else {
            $(`tr[data-role]`).show();
        }
    });

    $('#addUserForm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serializeArray();
        formData.push({
            name: 'task_manager_add_user',
            value: '1'
        });
        
        $.ajax({
            url: window.location.href,
            type: 'POST',
            data: formData,
            success: function() {
                window.location.reload();
            }
        });
    });

    $('#editUserForm').on('submit', function(e) {
        e.preventDefault();
        const formData = $(this).serializeArray();
        formData.push({
            name: 'task_manager_edit_user',
            value: '1'
        });
        
        $.ajax({
            url: window.location.href,
            type: 'POST',
            data: formData,
            success: function() {
                window.location.reload();
            }
        });
    });

    function getStatusClass(status) {
        const statusClasses = {
            'open': 'primary',
            'in-progress': 'info',
            'in-review': 'warning',
            'pending': 'secondary',
            'completed': 'success'
        };
        return statusClasses[status] || 'secondary';
    }

    function showToast(type, message) {
        const toastClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const toast = $(`
            <div class="alert ${toastClass} alert-dismissible fade show position-fixed" 
                 style="top: 20px; right: 20px; z-index: 9999; min-width: 300px;">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `);
        
        $('body').append(toast);
        
        setTimeout(function() {
            toast.alert('close');
        }, 5000);
    }
});