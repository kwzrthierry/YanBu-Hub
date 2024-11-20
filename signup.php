<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup Page</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" integrity="sha512-KyZXEAg3QhqLMpG8r+Knujsl5+5hbErD/sZ8ykQhA6UtO69C0Z8t8W8HpZZPQZt9x4E1BchRXwDqTo0c0KDfTw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: url('1.jpg') no-repeat center center fixed;
            background-size: cover;
            margin: 0;
            padding: 0;
            color: black;
        }
        .gradient-custom {
            background: rgba(0, 0, 0, 0.6);
            min-height: 100vh;
            padding: 20px;
        }
        .form-card {
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            padding: 25px;
        }
        .form-outline input, .form-outline select {
            background-color: transparent;
            border: 1px solid #00aaff;
            border-radius: 5px;
            color: #0056b3;
        }
        .form-outline input:focus, .form-outline select:focus {
            border-color: #0056b3;
            box-shadow: 0 0 0 0.2rem rgba(0, 166, 255, 0.25);
        }
        .btn-custom1 {
            background-color: rgb(23, 132, 140);
            padding: 10px;
            border: none;
            color: #fff;
        }
        .btn-custom1:hover {
            background-color: black;
            color: white;
            transition: background-color 0.3s;
        }
        .form-outline i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #0056b3;
        }
        .form-outline .form-control {
            padding-left: 35px;
            font-size: 0.9rem;
        }
        .left-content {
            color: #fff;
            margin-bottom: 30px;
        }
        .yanbu-hub-logo {
            font-size: 1.8rem;
            font-weight: bold;
            color: rgb(23, 132, 140);
            position: absolute;
            top: 20px;
            left: 20px;
        }
        .footer {
            background: rgb(23, 132, 140);
            color: #fff;
            padding: 15px;
            text-align: center;
            font-size: 0.8rem;
            position: fixed;
            bottom: 0;
            width: 100%;
        }
    </style>
</head>
<body>
    <a href="index.html">
        <div class="yanbu-hub-logo">Yanbu Hub</div>
    </a>
    <section class="vh-100 gradient-custom d-flex align-items-center justify-content-center">
        <div class="container d-flex align-items-center justify-content-between">
            <div class="left-content col-md-5">
                <h1>Welcome to Yanbu</h1>
                <p>Join the Yanbu Community! 🚀Kick-start your journey with us and unlock amazing opportunities. Sign up now to be part of a growing community of creators, and let’s build your future together! Don’t wait—start creating today!</p>
                <a href="courses.html" class="btn btn-custom1 mt-3">Explore Courses</a>
            </div>
            <div class="col-md-6">
                <div class="card form-card">
                    <h2 class="fw-bold mb-4 text-center">Signup</h2>
                    <form id="signupForm" action="sign.php" method="POST" onsubmit="return validateForm()">
                        <div class="row mb-3">
                            <div class="col-6">
                                <div class="form-outline position-relative">
                                    <i class="fas fa-user"></i>
                                    <input type="text" id="firstName" name="firstName" class="form-control" required />
                                    <label class="form-label" for="firstName">First Name</label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-outline position-relative">
                                    <i class="fas fa-user"></i>
                                    <input type="text" id="lastName" name="lastName" class="form-control" required />
                                    <label class="form-label" for="lastName">Last Name</label>
                                </div>
                            </div>
                        </div>
                        <div class="form-outline mb-3 position-relative">
                            <i class="fas fa-phone"></i>
                            <div class="input-group">
                                <select class="form-select" id="countryCode" onchange="updatePhoneLimit()" required style="width: 30%;">
                                    <option value="">Country Code</option>
                                    <option value="+254" data-length="9">+254 (Kenya)</option>
                                    <option value="+255" data-length="9">+255 (Tanzania)</option>
                                    <option value="+256" data-length="9">+256 (Uganda)</option>
                                    <option value="+250" data-length="9">+250 (Rwanda)</option>
                                    <option value="+257" data-length="8">+257 (Burundi)</option>
                                    <option value="+211" data-length="9">+211 (South Sudan)</option>
                                </select>
                                <input type="tel" id="phone" name="phone" class="form-control" placeholder="Phone Number" required oninput="limitPhoneNumber()" style="width: 70%;">
                            </div>
                        </div>
                        <div class="form-outline mb-3 position-relative">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="email" name="email" class="form-control" required />
                            <label class="form-label" for="email">Email</label>
                        </div>
                        <div class="form-outline mb-3 position-relative">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password" name="password" class="form-control" required />
                            <label class="form-label" for="password">Password</label>
                            <span id="passwordStrength" class="mt-2"></span> <!-- Added this line -->
                        </div>

                        <div class="form-outline mb-3 position-relative">
                            <i class="fas fa-venus-mars"></i>
                            <select id="gender" name="gender" class="form-control" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-custom1 btn-block mt-3">Signup</button>
                        </div>
                        <p class="text-center mt-3">Already have an account? <a href="login.php">Login here</a></p>
                    </form>
                </div>
            </div>
        </div>
    </section>
    <footer class="footer">
        <p>&copy; 2024 Yanbu Hub. All Rights Reserved.</p>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    <script type="text/javascript">
        // JavaScript for form effects and validation
        document.addEventListener('DOMContentLoaded', function () {
            // Password strength checker
            const passwordInput = document.getElementById('password');
            const passwordStrength = document.getElementById('passwordStrength');
            passwordInput.addEventListener('input', function () {
                checkPasswordStrength(passwordInput.value);
            });

            function checkPasswordStrength(password) {
                let strength = 0;

                if (password.length >= 8) strength++;
                if (password.match(/[A-Z]/)) strength++;
                if (password.match(/[a-z]/)) strength++;
                if (password.match(/[0-9]/)) strength++;
                if (password.match(/[@$!%*?&]/)) strength++;

                if (strength < 3) {
                    passwordStrength.textContent = 'Weak';
                    passwordStrength.style.color = 'red';
                } else if (strength < 5) {
                    passwordStrength.textContent = 'Medium';
                    passwordStrength.style.color = 'orange';
                } else {
                    passwordStrength.textContent = 'Strong';
                    passwordStrength.style.color = 'green';
                }
            }

            // Email validation
            const emailInput = document.getElementById('email');
            emailInput.addEventListener('input', function () {
                validateEmail(emailInput);
            });

            function validateEmail(email) {
                const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!emailPattern.test(email.value)) {
                    email.setCustomValidity('Invalid email format');
                    email.style.borderColor = 'red';
                } else {
                    email.setCustomValidity('');
                    email.style.borderColor = 'green';
                }
            }

            // Hover effects for buttons
            const buttons = document.querySelectorAll('.btn-custom');
            buttons.forEach(button => {
                button.addEventListener('mouseenter', function () {
                    button.style.backgroundColor = 'darkblue';
                    button.style.color = 'white';
                });

                button.addEventListener('mouseleave', function () {
                    button.style.backgroundColor = 'transparent';
                    button.style.color = '#0056b3';
                });
            });

            // Phone number length check based on country code
            const countryCodeSelect = document.getElementById('countryCode');
            const phoneInput = document.getElementById('phone');
            countryCodeSelect.addEventListener('change', updatePhoneLimit);

            function updatePhoneLimit() {
                const selectedOption = countryCodeSelect.options[countryCodeSelect.selectedIndex];
                const phoneLength = selectedOption.getAttribute('data-length');
                phoneInput.setAttribute('maxlength', phoneLength);
            }

            phoneInput.addEventListener('input', limitPhoneNumber);
            function limitPhoneNumber() {
                const maxLength = phoneInput.getAttribute('maxlength');
                if (phoneInput.value.length > maxLength) {
                    phoneInput.value = phoneInput.value.slice(0, maxLength);
                }
            }
        });

    </script>
</body>
</html>
