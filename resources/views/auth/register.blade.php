<!DOCTYPE html>
<html lang="en">

<head>
    <title>Create account - {{ config('app.name') }}</title>
    @include('auth.partials.head')
    <style>
        /* An empty optional field counts as :valid, which would float its label. */
        .input-wrapper input:placeholder-shown:not(:focus) {
            border-color: #dbe7ff;
            box-shadow: none;
        }

        .input-wrapper input:placeholder-shown:not(:focus)+label {
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            font-weight: 400;
        }
    </style>
</head>

<body class="auth-page">
    @include('auth.partials.theme-toggle')
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <img src="{{ asset('assets/img/logos/biu_logo.png') }}" alt="BIU Library Logo" class="brand-logo">
                <h2>Create account</h2>
                <p>Join {{ config('app.name') }} as a member</p>
            </div>

            <form action="{{ route('register.store') }}" method="POST" class="login-form">
                @csrf

                @foreach ([
                    ['name', 'text', 'Full Name', 'name', true, 50],
                    ['email', 'email', 'Email Address', 'email', true, 100],
                    ['phone', 'tel', 'Phone Number (optional)', 'tel', false, 15],
                    ['address', 'text', 'Address (optional)', 'street-address', false, 255],
                ] as [$field, $type, $label, $autocomplete, $required, $max])
                    <div class="form-group @error($field) error @enderror">
                        <div class="input-wrapper">
                            <input type="{{ $type }}" id="{{ $field }}" name="{{ $field }}" value="{{ old($field) }}" placeholder=" "
                                maxlength="{{ $max }}" autocomplete="{{ $autocomplete }}" @required($required)
                                class="{{ old($field) ? 'has-value' : '' }}">
                            <label for="{{ $field }}">{{ $label }}</label>
                        </div>
                        <span class="error-message @error($field) show @enderror" role="alert">{{ $errors->first($field) }}</span>
                    </div>
                @endforeach

                @foreach ([['password', 'Password (at least 8 characters)'], ['password_confirmation', 'Confirm Password']] as [$field, $label])
                    <div class="form-group @error($field) error @enderror">
                        <div class="input-wrapper">
                            <input type="password" id="{{ $field }}" name="{{ $field }}" placeholder=" " minlength="8" required
                                autocomplete="new-password">
                            <label for="{{ $field }}">{{ $label }}</label>
                        </div>
                        <span class="error-message @error($field) show @enderror" role="alert">{{ $errors->first($field) }}</span>
                    </div>
                @endforeach

                <button type="submit" class="login-btn">
                    <span class="btn-text">Create account</span>
                </button>
            </form>

            <div class="signup-link">
                <p>Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
            </div>
        </div>
    </div>

    <script src="{{ asset('assets/js/dark-mode.js') }}"></script>
</body>

</html>
