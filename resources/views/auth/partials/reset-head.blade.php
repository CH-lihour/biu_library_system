    @include('auth.partials.head')
    <script src="{{ asset('assets/js/plugin/webfont/webfont.min.js') }}"></script>
    <script>
        WebFont.load({ google: { families: ["Public Sans:400,500,600,700"] } });
    </script>
    {{-- Design: docs/sdlc/figma-svg/06-forgot-password.svg --}}
    <style>
        .reset-page {
            --primary: #1572e8;
            --primary-hover: #1269db;
            --card: #ffffff;
            --text: #2a2f5b;
            --muted: #8d9498;
            --border: #ebedf2;
            --danger: #f25961;
            --danger-soft: rgba(242, 89, 97, 0.12);
            --success: #1f9d23;
            --success-soft: rgba(49, 206, 54, 0.12);

            font-family: 'Public Sans', Inter, Arial, sans-serif;
            background: #1a2035;
            color: var(--text);
            overflow-x: hidden;
        }

        html[data-theme="dark"] .reset-page {
            --primary: var(--dm-accent);
            --primary-hover: var(--dm-accent-hover);
            --card: var(--dm-surface);
            --text: var(--dm-text);
            --muted: var(--dm-muted);
            --border: var(--dm-border);
            --danger: var(--dm-danger);
            --danger-soft: var(--dm-danger-soft);
            --success: var(--dm-success);
            --success-soft: var(--dm-success-soft);
        }

        .reset-page::before,
        .reset-page::after {
            content: '';
            position: fixed;
            z-index: -1;
            border-radius: 50%;
        }

        .reset-page::before {
            top: -140px;
            left: -120px;
            width: 520px;
            height: 520px;
            background: rgba(21, 114, 232, 0.08);
        }

        .reset-page::after {
            right: -180px;
            bottom: -220px;
            width: 600px;
            height: 600px;
            background: rgba(104, 97, 206, 0.08);
        }

        .reset-card {
            width: 100%;
            max-width: 420px;
            padding: 32px 32px 22px;
            border-radius: 12px;
            background: var(--card);
        }

        .reset-logo {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
            font-size: 17px;
            font-weight: 700;
        }

        .reset-logo span {
            display: grid;
            place-items: center;
            width: 36px;
            height: 36px;
            border-radius: 8px;
            background: #1572e8;
        }

        .reset-card h1 {
            margin-bottom: 1px;
            font-size: 22px;
            font-weight: 700;
            line-height: 28px;
            color: var(--text);
        }

        .reset-page .reset-subtitle {
            margin-bottom: 13px;
            font-size: 13px;
            line-height: 18px;
            color: var(--muted);
        }

        .reset-alert {
            margin-bottom: 12px;
            padding: 10px 14px;
            border-radius: 4px;
            background: var(--success-soft);
            color: var(--success);
            font-size: 13px;
            font-weight: 500;
            line-height: 20px;
        }

        .reset-page .reset-alert.danger {
            background: var(--danger-soft);
            color: var(--danger);
        }

        .reset-field + .reset-field {
            margin-top: 10px;
        }

        .reset-field label {
            display: block;
            margin-bottom: 4px;
            font-size: 13px;
            font-weight: 600;
            line-height: 16px;
            color: var(--text);
        }

        .reset-field input {
            width: 100%;
            height: 38px;
            padding: 0 12px;
            border: 1px solid var(--border);
            border-radius: 4px;
            outline: none;
            background: var(--card);
            color: var(--text);
            font: 400 14px 'Public Sans', Inter, Arial, sans-serif;
        }

        .reset-page .reset-field input[readonly] {
            color: var(--muted) !important;
        }

        .reset-field input:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(21, 114, 232, 0.15);
        }

        /* dark-mode.css sets its input border with !important */
        .reset-page .reset-field.error input {
            border-color: var(--danger) !important;
        }

        .reset-page .reset-field-error {
            display: block;
            margin-top: 6px;
            color: var(--danger);
            font-size: 12px;
            line-height: 16px;
        }

        .reset-btn {
            width: 100%;
            height: 42px;
            margin: 20px 0 18px;
            border: 0;
            border-radius: 4px;
            background: #1572e8;
            color: #ffffff;
            font: 600 14px 'Public Sans', Inter, Arial, sans-serif;
            cursor: pointer;
        }

        .reset-btn:hover {
            background: #1269db;
        }

        .reset-btn:focus-visible,
        .reset-back:focus-visible {
            outline: 2px solid var(--primary);
            outline-offset: 2px;
        }

        .reset-back {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: fit-content;
            margin: 0 auto 10px;
            color: var(--primary);
            font-size: 13px;
            font-weight: 600;
            line-height: 20px;
            text-decoration: none;
        }

        .reset-back:hover {
            color: var(--primary-hover);
            text-decoration: underline;
        }

        .reset-page .reset-note {
            color: var(--muted);
            font-size: 12px;
            line-height: 16px;
            text-align: center;
        }

        @media (max-width: 480px) {
            .reset-card {
                padding: 24px 24px 20px;
            }
        }
    </style>
