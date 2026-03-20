$(document).ready(function() {
    $('#registerBtn').on('click', function(e) {
        e.preventDefault();

        const username = $('#username').val().trim();
        const email = $('#email').val().trim();
        const password = $('#password').val();
        const confirm_password = $('#confirm_password').val();
        const alertBox = $('#alertBox');
        const btn = $(this);

        alertBox.addClass('d-none').removeClass('alert-danger alert-success').text('');

        if(!username || !email || !password || !confirm_password) {
            alertBox.text('All fields are required.').addClass('alert-danger').removeClass('d-none');
            return;
        }

        if(password !== confirm_password) {
            alertBox.text('Passwords do not match.').addClass('alert-danger').removeClass('d-none');
            return;
        }

        btn.prop('disabled', true).text('Registering...');

        $.ajax({
            url: 'php/register.php',
            type: 'POST',
            dataType: 'json',
            data: {
                username: username,
                email: email,
                password: password
            },
            success: function(response) {
                if(response.success) {
                    alertBox.text('Registration successful! Redirecting to login...').addClass('alert-success').removeClass('d-none');
                    setTimeout(function() {
                        window.location.href = 'login.html';
                    }, 2000);
                } else {
                    alertBox.text(response.message || 'Registration failed.').addClass('alert-danger').removeClass('d-none');
                    btn.prop('disabled', false).text('Register');
                }
            },
            error: function(xhr, status, error) {
                let errorMsg = 'An error occurred during registration.';
                if(xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                alertBox.text(errorMsg).addClass('alert-danger').removeClass('d-none');
                btn.prop('disabled', false).text('Register');
            }
        });
    });
});
