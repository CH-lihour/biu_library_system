<!DOCTYPE html>
<html lang="en">

<head>
    <title>Set new password - {{ config('app.name') }}</title>
    @include('auth.partials.reset-head')
</head>

<body class="reset-page">
    @include('auth.partials.theme-toggle')
    <main class="reset-card">
        <div class="reset-logo">
            <span>
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="#FFFFFF" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <path d="M4 19.5v-15A2.5 2.5 0 0 1 6.5 2H20v20H6.5a2.5 2.5 0 0 1 0-5H20" />
                </svg>
            </span>
            BIU Library
        </div>

        <h1>Set new password</h1>
        <p class="reset-subtitle">Use at least 8 characters.</p>

        @error('token')
            <div class="reset-alert danger" role="alert">{{ $message }}</div>
        @enderror

        <form action="{{ route('password.update') }}" method="POST">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">

            <div class="reset-field @error('email') error @enderror">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" value="{{ old('email', $email) }}" maxlength="150" required
                    @readonly($email) autocomplete="username" @error('email') aria-invalid="true" aria-describedby="emailError" @enderror>
                @error('email')
                    <span class="reset-field-error" id="emailError" role="alert">{{ $message }}</span>
                @enderror
            </div>

            <div class="reset-field @error('password') error @enderror">
                <label for="password">New password</label>
                <input type="password" id="password" name="password" minlength="8" required autofocus
                    autocomplete="new-password" @error('password') aria-invalid="true" aria-describedby="passwordError" @enderror>
                @error('password')
                    <span class="reset-field-error" id="passwordError" role="alert">{{ $message }}</span>
                @enderror
            </div>

            <div class="reset-field">
                <label for="password_confirmation">Confirm password</label>
                <input type="password" id="password_confirmation" name="password_confirmation" minlength="8" required
                    autocomplete="new-password">
            </div>

            <button type="submit" class="reset-btn">Save new password</button>
        </form>

        <a href="{{ route('login') }}" class="reset-back">
            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                <path d="m12 19-7-7 7-7" />
                <path d="M19 12H5" />
            </svg>
            Back to sign in
        </a>

        <p class="reset-note">Reset links expire after {{ config('auth.passwords.users.expire') }} minutes.</p>
    </main>

    <script src="{{ asset('assets/js/dark-mode.js') }}"></script>
</body>

</html>
