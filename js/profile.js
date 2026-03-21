$(document).ready(function() {
    const token = localStorage.getItem('token');
    
    if (!token) {
        window.location.href = 'login.html';
        return;
    }

    // Load profile on page load
    $.ajax({
        url: 'php/profile.php',
        type: 'GET',
        headers: { 'Authorization': 'Bearer ' + token },
        dataType: 'json',
        success: function(response) {
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
                localStorage.removeItem('token');
                window.location.href = 'login.html';
            } else {
                $('#alertBox').text('Failed to load profile.')
                    .addClass('alert-danger')
                    .removeClass('d-none');
            }
        }
    });

    // Save profile
    $('#saveBtn').on('click', function(e) {
        e.preventDefault();
        const alertBox = $('#alertBox');
        const btn = $(this);

        alertBox.addClass('d-none').text('');
        btn.prop('disabled', true).text('Saving...');

        $.ajax({
            url: 'php/profile.php',
            type: 'POST',
            headers: { 'Authorization': 'Bearer ' + token },
            dataType: 'json',
            data: {
                fullName: $('#fullName').val().trim(),
                age: $('#age').val().trim(),
                dob: $('#dob').val(),
                contact: $('#contact').val().trim(),
                address: $('#address').val().trim(),
                bio: $('#bio').val().trim()
            },
            success: function(response) {
                if (response.success) {
                    alertBox.text('Profile saved successfully!')
                        .addClass('alert-success')
                        .removeClass('d-none alert-danger');
                } else {
                    alertBox.text(response.message || 'Failed to save.')
                        .addClass('alert-danger')
                        .removeClass('d-none alert-success');
                }
                btn.prop('disabled', false).text('Save Profile');
            },
            error: function(xhr) {
                if (xhr.status === 401) {
                    localStorage.removeItem('token');
                    window.location.href = 'login.html';
                } else {
                    alertBox.text('Error saving profile.')
                        .addClass('alert-danger')
                        .removeClass('d-none');
                    btn.prop('disabled', false).text('Save Profile');
                }
            }
        });
    });

    // Logout
    $('#logoutBtn').on('click', function() {
        $.ajax({
            url: 'php/logout.php',
            type: 'POST',
            headers: { 'Authorization': 'Bearer ' + token },
            complete: function() {
                localStorage.removeItem('token');
                window.location.href = 'login.html';
            }
        });
    });
});