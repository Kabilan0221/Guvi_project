$(document).ready(function() {
    // If already logged in, redirect to profile
    if(localStorage.getItem('token')) {
        window.location.href = 'profile.html';
        return;
    }

    $('#loginBtn').on('click', function(e) {
        e.preventDefault();

        const email = $('#email').val().trim();
        const password = $('#password').val();
        const alertBox = $('#alertBox');
        const btn = $(this);

        alertBox.addClass('d-none').removeClass('alert-danger alert-success').text('');

        if(!email || !password) {
            alertBox.text('Both email and password are required.').addClass('alert-danger').removeClass('d-none');
            return;
        }

        btn.prop('disabled', true).text('Logging in...');

        $.ajax({
            url: 'php/login.php',
            type: 'POST',
            dataType: 'json',
            data: {
                email: email,
                password: password
            },
            success: function(response) {
                if(response.success && response.token) {
                    localStorage.setItem('token', response.token);
                    alertBox.text('Login successful! Redirecting...').addClass('alert-success').removeClass('d-none');
                    setTimeout(function() {
                        window.location.href = 'profile.html';
                    }, 1000);
                } else {
                    alertBox.text(response.message || 'Login failed.').addClass('alert-danger').removeClass('d-none');
                    btn.prop('disabled', false).text('Login');
                }
            },
            error: function(xhr, status, error) {
                let errorMsg = 'An error occurred during login.';
                if(xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                alertBox.text(errorMsg).addClass('alert-danger').removeClass('d-none');
                btn.prop('disabled', false).text('Login');
            }
        });
    });
});
