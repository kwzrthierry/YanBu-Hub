<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
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
        .form-outline input {
            background-color: transparent;
            border: 1px solid #00aaff;
            border-radius: 5px;
            color: #0056b3;
        }
        .form-outline input:focus {
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
                <h1>Welcome Back to Yanbu</h1>
                <p>We missed you! 😊 Log in to continue your journey with Yanbu Hub. Reconnect and explore your dashboard, courses, and more. We’re excited to have you back!</p>
            </div>
            <div class="col-md-6">
                <div class="card form-card">
                    <h2 class="fw-bold mb-4 text-center" style="margin-top: 40px;">Login</h2>
                    <form id="loginForm" action="lg.php" method="POST" onsubmit="return validateLoginForm()">
                        <div class="form-outline mb-3 position-relative">
                            <i class="fas fa-envelope"></i>
                            <input type="email" id="email" name="email" class="form-control" required />
                            <label class="form-label" for="email">Email</label>
                        </div>
                        <div class="form-outline mb-3 position-relative">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password" name="password" class="form-control" required />
                            <label class="form-label" for="password">Password</label>
                        </div>
                        <div class="text-center">
                            <button type="submit" class="btn btn-custom1 btn-block mt-3">Login</button>
                        </div>
                        <p class="text-center mt-3">Don't have an account? <a href="signup.php">Sign up here</a></p>
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
        });

        function validateLoginForm() {
            const email = document.getElementById('email').value;
            const password = document.getElementById('password').value;

            if (email === '' || password === '') {
                alert('Both email and password are required');
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
