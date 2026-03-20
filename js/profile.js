$(document).ready(function() {
    const token = localStorage.getItem('auth_token');
    
    // Redirect if invalid token
    if (!token) {
        window.location.href = 'login.html';
        return;
    }

    // Load profile on load
    $.ajax({
        url: 'php/profile.php',
        type: 'GET',
        headers: { 'Authorization': 'Bearer ' + token },
        dataType: 'json',
        success: function(response) {
            $('#pageBody').removeClass('d-none');
            if (response.success && response.data) {
                const data = response.data;
                $('#fullName').val(data.fullName || '');
                $('#age').val(data.age || '');
                $('#dob').val(data.dob || '');
                $('#contact').val(data.contact || '');
                $('#address').val(data.address || '');
                $('#bio').val(data.bio || '');
            }
        },
        error: function(xhr) {
            if (xhr.status === 401) {
                localStorage.removeItem('auth_token');
                window.location.href = 'login.html';
            } else {
                $('#pageBody').removeClass('d-none');
                $('#alertBox').text('Failed to load profile.').addClass('alert-danger').removeClass('d-none');
            }
        }
    });

    // Save profile via AJAX
    $('#saveBtn').on('click', function(e) {
        e.preventDefault();
        const alertBox = $('#alertBox');
        const btn = $(this);

        alertBox.addClass('d-none').removeClass('alert-danger alert-success').text('');
        btn.prop('disabled', true).text('Saving...');

        // Field values
        const profileData = {
            fullName: $('#fullName').val().trim(),
            age: $('#age').val().trim(),
            dob: $('#dob').val(),
            contact: $('#contact').val().trim(),
            address: $('#address').val().trim(),
            bio: $('#bio').val().trim()
        };

        $.ajax({
            url: 'php/profile.php',
            type: 'POST',
            headers: { 'Authorization': 'Bearer ' + token },
            dataType: 'json',
            data: profileData,
            success: function(response) {
                if (response.success) {
                    alertBox.text('Profile saved successfully!').addClass('alert-success').removeClass('d-none');
                } else {
                    alertBox.text(response.message || 'Failed to save profile.').addClass('alert-danger').removeClass('d-none');
                }
                btn.prop('disabled', false).text('Save Profile');
            },
            error: function(xhr) {
                if (xhr.status === 401) {
                    localStorage.removeItem('auth_token');
                    window.location.href = 'login.html';
                } else {
                    let errorMsg = 'An error occurred while saving.';
                    if(xhr.responseJSON && xhr.responseJSON.message) {
                        errorMsg = xhr.responseJSON.message;
                    }
                    alertBox.text(errorMsg).addClass('alert-danger').removeClass('d-none');
                    btn.prop('disabled', false).text('Save Profile');
                }
            }
        });
    });

    // Logout via AJAX
    $('#logoutBtn').on('click', function() {
        $.ajax({
            url: 'php/logout.php',
            type: 'POST',
            headers: { 'Authorization': 'Bearer ' + token },
            success: function() {
                localStorage.removeItem('auth_token');
                window.location.href = 'login.html';
            },
            error: function() {
                localStorage.removeItem('auth_token');
                window.location.href = 'login.html';
            }
        });
    });
});
