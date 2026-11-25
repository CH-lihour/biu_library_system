<!DOCTYPE html>
<html lang="en">

<head>
    <title>{{ config('app.name') }}</title>
    @include('auth.partials.head')
</head>

<body class="auth-page">
    @include('auth.partials.theme-toggle')
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <img src="{{ asset('assets/img/logos/biu_logo.png') }}" alt="BIU Library Logo" class="brand-logo">
                <h2>Sign In</h2>
                <p>Welcome back to {{ config('app.name') }}</p>
            </div>

            @if (session('success'))
                <p role="status" style="color: #16a34a; text-align: center; margin-bottom: 16px;">{{ session('success') }}</p>
            @endif

            <form action="{{ route('store') }}" method="POST" class="login-form" id="loginForm">
                @csrf

                {{-- email --}}
                <div class="form-group @error('email') error @enderror">
                    <div class="input-wrapper">
                        <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder=" " required autocomplete="email">
                        <label for="email">Email Address</label>
                    </div>
                    <span class="error-message @error('email') show @enderror" id="emailError" role="alert">{{ $errors->first('email') }}</span>
                </div>

                {{-- password --}}
                <div class="form-group @error('password') error @enderror">
                    <div class="input-wrapper password-wrapper">
                        <input type="password" id="password" name="password" placeholder=" " required
                            autocomplete="current-password">
                        <label for="password">Password</label>
                        <button type="button" class="password-toggle" id="passwordToggle"
                            aria-label="Toggle password visibility">
                            <span class="eye-icon"></span>
                        </button>
                    </div>
                    <span class="error-message @error('password') show @enderror" id="passwordError" role="alert">{{ $errors->first('password') }}</span>
                </div>

                {{-- remember me --}}
                <div class="form-options">
                    <label class="remember-wrapper">
                        <input type="checkbox" id="remember" name="remember">
                        <span class="checkbox-label">
                            <span class="checkmark"></span>
                            Remember me
                        </span>
                    </label>
                    <a href="{{ route('password.request') }}" class="forgot-password">Forgot password?</a>
                </div>

                <button type="submit" class="login-btn">
                    <span class="btn-text">Sign In</span>
                    <span class="btn-loader"></span>
                </button>
            </form>

            <div class="signup-link">
                <p>New member? <a href="{{ route('register') }}">Create an account</a></p>
            </div>

            <div class="success-message" id="successMessage">
                <div class="success-icon">✓</div>
                <h3>Login Successful!</h3>
                <p>Redirecting to your dashboard...</p>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const toggleBtn = document.getElementById("passwordToggle");
            const passwordInput = document.getElementById("password");
            const eyeIcon = toggleBtn ? toggleBtn.querySelector(".eye-icon") : null;

            if (!toggleBtn || !passwordInput) return;

            toggleBtn.addEventListener("click", function () {
                const isCurrentlyPassword = passwordInput.type === "password";
                passwordInput.type = isCurrentlyPassword ? "text" : "password";
                if (eyeIcon) {
                    eyeIcon.classList.toggle("show-password", isCurrentlyPassword);
                }
            });
        });
    </script>

    <script src="{{ asset('assets/js/dark-mode.js') }}"></script>
</body>

</html>
