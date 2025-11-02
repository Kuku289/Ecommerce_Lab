$(document).ready(function() {
    $('#register-form').submit(function(e) {
        e.preventDefault();

        name = $('#name').val();
        email = $('#email').val();
        password = $('#password').val();
        phone_number = $('#phone_number').val();
        role = $('input[name="role"]:checked').val() || 2; // Default to customer
        country = $('#country').val();
        city = $('#city').val();

        if (name == '' || email == '' || password == '' || phone_number == '') {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please fill in all fields!',
            });

            return;
        } else if (password.length < 6 || !password.match(/[a-z]/) || !password.match(/[A-Z]/) || !password.match(/[0-9]/)) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Password must be at least 6 characters long and contain at least one lowercase letter, one uppercase letter, and one number!',
            });

            return;
        }

        // Email validation
        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailPattern.test(email)) {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'Please enter a valid email address!',
            });
            return;
        }

        $.ajax({
            url: '../actions/register_user_action.php',
            type: 'POST',
            data: {
                name: name,
                email: email,
                password: password,
                phone_number: phone_number,
                role: role,
                country: country,
                city: city
            },
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: response.message,
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'login.php';
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: response.message,
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'An error occurred! Please try again later.',
                });
            }
        });
    });
});