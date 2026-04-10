<!DOCTYPE html>
<html lang="en">

<head>
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <title>{{ config('app.name') }}</title>
    <meta content="width=device-width, initial-scale=1.0, shrink-to-fit=no" name="viewport" />
    <link rel="icon" href="{{ asset('assets/img/logos/biu_logo.svg') }}" type="image/x-icon" />

    <!-- Fonts and icons -->
    <script src="{{ asset('assets/js/plugin/webfont/webfont.min.js') }}"></script>
    <script>
        WebFont.load({
            google: { families: ["Public Sans:300,400,500,600,700"] },
            custom: {
                families: [
                    "Font Awesome 5 Solid",
                    "Font Awesome 5 Regular",
                    "Font Awesome 5 Brands",
                    "simple-line-icons",
                ],
                urls: ["{{ asset('assets/css/fonts.min.css') }}"],
            },
            active: function () {
                sessionStorage.fonts = true;
            },
        });
    </script>

    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/plugins.min.css') }}" />
    <link rel="stylesheet" href="{{ asset('assets/css/kaiadmin.min.css') }}" />

    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.2/css/buttons.dataTables.min.css">

    {{-- Select2 --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    @stack('styles')

    {{-- Flash Message Styles --}}
    <style>
        .flash-container {
            position: fixed;
            top: 84px;
            right: 24px;
            z-index: 2000;
            width: min(440px, calc(100vw - 32px));
            display: flex;
            flex-direction: column;
            gap: 10px;
            pointer-events: none;
        }

        .flash {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            padding: 14px 16px;
            border-radius: 10px;
            border: 0.5px solid transparent;
            pointer-events: all;
            position: relative;
            overflow: hidden;
            animation: slideIn 0.32s cubic-bezier(0.34, 1.56, 0.64, 1) both;
            max-width: 440px;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(30px) scale(0.97)
            }

            to {
                opacity: 1;
                transform: translateX(0) scale(1)
            }
        }

        @keyframes fadeOut {
            to {
                opacity: 0;
                transform: translateX(10px) scale(0.96);
                max-height: 0;
                margin: 0;
                padding: 0
            }
        }

        .flash.removing {
            animation: fadeOut 0.25s ease forwards;
        }

        .flash-success {
            background: #EAF3DE;
            border-color: #C0DD97
        }

        .flash-error {
            background: #FCEBEB;
            border-color: #F7C1C1
        }

        .flash-warning {
            background: #FAEEDA;
            border-color: #FAC775
        }

        .flash-info {
            background: #E6F1FB;
            border-color: #B5D4F4
        }

        .flash-neutral {
            background: #f1efe8;
            border-color: #d3d1c7
        }

        .flash-icon {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .flash-body {
            flex: 1
        }

        .flash-title {
            font-size: 14px;
            font-weight: 500;
            line-height: 1.4;
            margin-bottom: 2px;
        }

        .flash-msg {
            font-size: 13px;
            line-height: 1.5;
            opacity: 0.8;
        }

        .flash-success .flash-title,
        .flash-success .flash-msg {
            color: #27500A
        }

        .flash-error .flash-title,
        .flash-error .flash-msg {
            color: #791F1F
        }

        .flash-warning .flash-title,
        .flash-warning .flash-msg {
            color: #633806
        }

        .flash-info .flash-title,
        .flash-info .flash-msg {
            color: #0C447C
        }

        .flash-neutral .flash-title {
            color: #2c2c2a
        }

        .flash-neutral .flash-msg {
            color: #5f5e5a
        }

        .flash-close {
            background: none;
            border: none;
            cursor: pointer;
            padding: 2px;
            border-radius: 4px;
            opacity: 0.5;
            transition: opacity 0.15s;
            color: inherit;
            font-size: 16px;
            line-height: 1;
            flex-shrink: 0;
        }

        .flash-close:hover {
            opacity: 1
        }

        .flash-progress {
            position: absolute;
            bottom: 0;
            left: 0;
            height: 2px;
            background: currentColor;
            opacity: 0.25;
            animation: progress var(--dur, 4s) linear forwards;
            transform-origin: left;
        }

        @keyframes progress {
            from {
                width: 100%
            }

            to {
                width: 0%
            }
        }

        .dialog-overlay {
            position: fixed;
            inset: 0;
            background: rgba(11, 18, 26, .45);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 16px;
            opacity: 0;
            visibility: hidden;
            transition: opacity .2s ease, visibility .2s ease;
            z-index: 3000;
        }

        .dialog-overlay.show {
            opacity: 1;
            visibility: visible;
        }

        .dialog-card {
            width: min(460px, 100%);
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 12px 30px rgba(0, 0, 0, .18);
        }

        .dialog-icon {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            display: grid;
            place-items: center;
            margin-bottom: 12px;
        }

        .dialog-title {
            margin: 0 0 8px;
            font-size: 18px;
            font-weight: 700;
            color: #1f2937;
        }

        .dialog-body {
            margin: 0;
            font-size: 14px;
            color: #4b5563;
            line-height: 1.55;
        }

        .dialog-actions {
            margin-top: 18px;
            display: flex;
            justify-content: flex-end;
            gap: 10px;
        }

        .dialog-actions .btn {
            border-radius: 8px;
            padding: 8px 14px;
            font-size: 13px;
            font-weight: 600;
            border: none;
        }

        .btn-no {
            background: #f3f4f6;
            color: #374151;
        }

        .btn-yes-danger {
            background: #e24b4a;
            color: #fff;
        }

        .btn-yes-warning {
            background: #ef9f27;
            color: #1f2937;
        }

        .btn-yes-info {
            background: #378add;
            color: #fff;
        }

        .result-toast {
            position: fixed;
            bottom: 24px;
            right: 24px;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 13px;
            font-weight: 500;
            box-shadow: 0 10px 28px rgba(0, 0, 0, .14);
            opacity: 0;
            transform: translateY(12px);
            pointer-events: none;
            transition: all .2s ease;
            z-index: 3200;
        }

        .result-toast.show {
            opacity: 1;
            transform: translateY(0);
        }

        @media (max-width: 576px) {
            .flash-container {
                top: 72px;
                right: 12px;
                left: 12px;
                width: auto;
            }

            .result-toast {
                right: 12px;
                left: 12px;
                bottom: 12px;
                text-align: center;
            }
        }
    </style>

    {{-- Better Select2 look (Bootstrap-like) --}}
    <style>
        .select2-container {
            width: 100% !important;
        }

        .select2-container--default .select2-selection--single {
            height: 38px;
            border: 1px solid #ced4da;
            border-radius: .375rem;
            padding: .375rem .75rem;
            display: flex;
            align-items: center;
            background-color: #fff;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #212529;
            line-height: normal;
            padding-left: 0;
        }

        .select2-container--default .select2-selection--single .select2-selection__placeholder {
            color: #6c757d;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 36px;
            right: 8px;
        }

        .select2-container--default .select2-selection--multiple {
            min-height: 38px;
            border: 1px solid #ced4da;
            border-radius: .375rem;
            padding: .375rem .75rem;
            background-color: #fff;
            display: flex;
            align-items: center;
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__rendered {
            display: flex;
            flex-wrap: wrap;
            align-items: center;
            gap: .25rem;
            width: 100%;
            padding: 0;
            margin: 0;
            color: #212529;
            line-height: normal;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice {
            background-color: #e9f2ff;
            border: 1px solid #b6d4fe;
            color: #0a58ca;
            border-radius: .25rem;
            padding: .2rem .45rem;
            margin: 0;
            font-size: 12px;
            line-height: 1.2;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
            color: #0a58ca;
            margin-right: .35rem;
            border: none;
            background: transparent;
            font-size: 12px;
        }

        .select2-container--default .select2-selection--multiple .select2-search--inline .select2-search__field {
            margin-top: 0;
            height: 28px;
            padding: 0;
            font-size: 14px;
            min-width: 120px;
            width: 100% !important;
            line-height: 28px;
            border: 0;
            box-shadow: none;
            background: transparent;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__placeholder {
            margin-top: 0;
            color: #6c757d;
            flex: 1 1 auto;
        }

        .select2-container--default .select2-selection--multiple .select2-selection__clear {
            margin-right: .35rem;
        }

        .select2-container--default .select2-selection--multiple .select2-search--inline {
            flex: 1 1 auto;
        }

        .select2-container--default .select2-selection--multiple .select2-search--inline .select2-search__field::placeholder {
            color: #6c757d;
            opacity: 1;
        }

        .select2-container--default.select2-container--focus .select2-selection--single,
        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: #86b7fe;
            box-shadow: 0 0 0 .2rem rgba(13, 110, 253, .25);
        }

        .select2-dropdown {
            border: 1px solid #ced4da;
            border-radius: .375rem;
            box-shadow: 0 8px 20px rgba(0, 0, 0, .08);
        }

        .select2-search--dropdown .select2-search__field {
            border: 1px solid #ced4da;
            border-radius: .375rem;
            padding: .375rem .5rem;
        }

        /* Error state */
        .is-invalid+.select2-container .select2-selection--single,
        .select2-container .select2-selection--single.is-invalid,
        .is-invalid+.select2-container .select2-selection--multiple,
        .select2-container .select2-selection--multiple.is-invalid {
            border-color: #dc3545 !important;
        }
    </style>

    {{-- Dropdown Action --}}
    <style>
        :root {
            --color-background-primary: #ffffff;
            --color-background-secondary: #f5f5f3;
            --color-background-danger: #fcebeb;
            --color-background-success: #eaf3de;
            --color-background-warning: #faeeda;
            --color-text-primary: #1a1a18;
            --color-text-secondary: #5f5e5a;
            --color-text-tertiary: #888780;
            --color-text-danger: #a32d2d;
            --color-text-success: #3b6d11;
            --color-text-warning: #854f0b;
            --color-border-tertiary: rgba(0, 0, 0, 0.12);
            --color-border-secondary: rgba(0, 0, 0, 0.25);
            --color-border-primary: rgba(0, 0, 0, 0.4);
            --border-radius-md: 8px;
            --border-radius-lg: 12px;
            --font-sans: system-ui, -apple-system, sans-serif;
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --color-background-primary: #1e1e1c;
                --color-background-secondary: #2a2a28;
                --color-background-danger: #501313;
                --color-background-success: #173404;
                --color-background-warning: #412402;
                --color-text-primary: #f0ede6;
                --color-text-secondary: #b4b2a9;
                --color-text-tertiary: #888780;
                --color-text-danger: #f09595;
                --color-text-success: #c0dd97;
                --color-text-warning: #fac775;
                --color-border-tertiary: rgba(255, 255, 255, 0.1);
                --color-border-secondary: rgba(255, 255, 255, 0.2);
                --color-border-primary: rgba(255, 255, 255, 0.35);
            }
        }

        .demo-wrap {
            display: flex;
            flex-direction: column;
            gap: 48px;
            padding: 2rem;
            background: var(--color-background-secondary);
            border-radius: var(--border-radius-lg);
        }

        .demo-row {
            display: flex;
            align-items: flex-start;
            gap: 2rem;
            flex-wrap: wrap;
        }

        .demo-label {
            font-size: 12px;
            color: var(--color-text-tertiary);
            margin-bottom: 10px;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .dd-wrap {
            position: relative;
            display: inline-block;
        }

        .dd-trigger {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 0 14px;
            height: 36px;
            border-radius: var(--border-radius-md);
            border: 0.5px solid var(--color-border-secondary);
            background: var(--color-background-primary);
            color: var(--color-text-primary);
            font-size: 14px;
            cursor: pointer;
            user-select: none;
            transition: background 0.12s;
            white-space: nowrap;
        }

        .dd-trigger:hover {
            background: var(--color-background-secondary);
        }

        .dd-trigger.open {
            border-color: var(--color-border-primary);
        }

        .dd-trigger svg.chevron {
            width: 14px;
            height: 14px;
            fill: none;
            stroke: var(--color-text-secondary);
            stroke-width: 1.8;
            transition: transform 0.18s;
        }

        .dd-trigger.open svg.chevron {
            transform: rotate(180deg);
        }

        .dd-trigger-icon {
            width: 16px;
            height: 16px;
            fill: var(--color-text-secondary);
            flex-shrink: 0;
        }

        .dd-menu {
            position: absolute;
            top: calc(100% + 6px);
            left: 0;
            min-width: 200px;
            background: var(--color-background-primary);
            border: 0.5px solid var(--color-border-secondary);
            border-radius: var(--border-radius-lg);
            padding: 6px;
            z-index: 100;
            opacity: 0;
            transform: translateY(-6px);
            pointer-events: none;
            transition: opacity 0.14s, transform 0.14s;
        }

        .dd-menu.right {
            left: auto;
            right: 0;
        }

        .dd-menu.visible {
            opacity: 1;
            transform: translateY(0);
            pointer-events: all;
        }

        .dd-item {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 10px;
            border-radius: var(--border-radius-md);
            font-size: 14px;
            color: var(--color-text-primary);
            cursor: pointer;
            transition: background 0.1s;
        }

        .dd-item:hover {
            background: var(--color-background-secondary);
        }

        .dd-item.danger {
            color: var(--color-text-danger);
        }

        .dd-item.danger:hover {
            background: var(--color-background-danger);
        }

        .dd-item svg {
            width: 15px;
            height: 15px;
            fill: var(--color-text-secondary);
            flex-shrink: 0;
        }

        .dd-item.danger svg {
            fill: var(--color-text-danger);
        }

        .dd-item span.shortcut {
            margin-left: auto;
            font-size: 12px;
            color: var(--color-text-tertiary);
        }

        .dd-divider {
            height: 0.5px;
            background: var(--color-border-tertiary);
            margin: 5px 0;
        }

        .dd-section-label {
            font-size: 11px;
            color: var(--color-text-tertiary);
            padding: 6px 10px 2px;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .icon-btn {
            width: 32px;
            height: 32px;
            border-radius: var(--border-radius-md);
            border: 0.5px solid var(--color-border-secondary);
            background: var(--color-background-success);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background 0.12s;
        }

        .icon-btn:hover {
            background: var(--color-background-secondary);
        }

        .icon-btn svg {
            width: 15px;
            height: 15px;
            fill: var(--color-text-secondary);
        }
    </style>
</head>

<body>
    <div class="wrapper">
        @include('partials.sidebar')
        <!-- End Sidebar -->

        <div class="main-panel">
            <div class="main-header">
                <!-- Navbar Header -->
                @include('partials.navbar')
                <!-- End Navbar -->
            </div>

            <div class="container">
                <div class="flash-container" id="flashContainer"></div>

                <div class="dialog-overlay" id="overlay" onclick="handleOverlayClick(event)">
                    <div class="dialog-card" role="dialog" aria-modal="true" aria-labelledby="dTitle"
                        aria-describedby="dBody">
                        <div class="dialog-icon" id="dIcon"></div>
                        <h4 class="dialog-title" id="dTitle"></h4>
                        <p class="dialog-body" id="dBody"></p>
                        <div class="dialog-actions">
                            <button type="button" class="btn btn-no" onclick="closeDialog('no')">Cancel</button>
                            <button type="button" class="btn btn-yes-danger" id="btnYes"
                                onclick="closeDialog('yes')">Yes</button>
                        </div>
                    </div>
                </div>
                <div class="result-toast" id="toast"></div>

                <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
                    <div class="modal-dialog modal-dialog-centered modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title" id="imagePreviewTitle">Image Preview</h5>
                                <button type="button" class="btn btn-light btn-close text-black" data-bs-dismiss="modal"
                                    aria-label="Close"><i class="fas fa-times"></i></button>
                            </div>
                            <div class="modal-body text-center">
                                <img id="imagePreviewModalImg" src="" alt="Preview" class="img-fluid rounded"
                                    style="max-height: 75vh; object-fit: contain;" />
                            </div>
                        </div>
                    </div>
                </div>

                <div class="page-inner">
                    <div class="page-header flex-wrap">
                        <h3 class="fw-bold mb-3">@yield('page-title', 'Dashboard')</h3>
                        @yield('page-path')
                        <h6 class="op-7 mb-2 w-100">@yield('page-subtitle')</h6>
                    </div>

                    @yield('content')

                </div>
            </div>

            <footer class="footer">
                <div class="container-fluid d-flex justify-content-between">
                    <nav class="pull-left">
                        <ul class="nav">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('dashboard') }}">
                                    {{ config('app.name') }}
                                </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="https://github.com/CH-lihour"> Help </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="https://github.com/CH-lihour"> Licenses </a>
                            </li>
                        </ul>
                    </nav>
                    {{-- <div class="copyright">
                        {{ date('Y') == '2026' ? '2026' : '2026 - ' . date('Y') }}, made with <i
                            class="fa fa-heart heart text-danger"></i> by
                        <a href="https://github.com/CH-lihour" target="_blank">Chetha Lihour</a>
                    </div> --}}
                    <div>
                        Distributed by
                        <a target="_blank" href="https://github.com/CH-lihour">Chetha Lihour</a>.
                    </div>
                </div>
            </footer>
        </div>
    </div>
    <!--   Core JS Files   -->
    <script src="{{ asset('assets/js/core/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/popper.min.js') }}"></script>
    <script src="{{ asset('assets/js/core/bootstrap.min.js') }}"></script>

    <!-- jQuery Scrollbar -->
    <script src="{{ asset('assets/js/plugin/jquery-scrollbar/jquery.scrollbar.min.js') }}"></script>

    <!-- Chart JS -->
    <script src="{{ asset('assets/js/plugin/chart.js/chart.min.js') }}"></script>

    <!-- jQuery Sparkline -->
    <script src="{{ asset('assets/js/plugin/jquery.sparkline/jquery.sparkline.min.js') }}"></script>

    <!-- Chart Circle -->
    <script src="{{ asset('assets/js/plugin/chart-circle/circles.min.js') }}"></script>

    <!-- Datatables -->
    <script src="{{ asset('assets/js/plugin/datatables/datatables.min.js') }}"></script>

    <!-- Bootstrap Notify -->
    <script src="{{ asset('assets/js/plugin/bootstrap-notify/bootstrap-notify.min.js') }}"></script>

    <!-- jQuery Vector Maps -->
    <script src="{{ asset('assets/js/plugin/jsvectormap/jsvectormap.min.js') }}"></script>
    <script src="{{ asset('assets/js/plugin/jsvectormap/world.js') }}"></script>

    <!-- Sweet Alert -->
    <script src="{{ asset('assets/js/plugin/sweetalert/sweetalert.min.js') }}"></script>

    <!-- Kaiadmin JS -->
    <script src="{{ asset('assets/js/kaiadmin.min.js') }}"></script>

    <!-- Kaiadmin DEMO methods, don't include it in your project! -->
    <script src="{{ asset('assets/js/setting-demo.js') }}"></script>
    <script src="{{ asset('assets/js/demo.js') }}"></script>

    <script src="https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js"></script>

    {{-- Select2 --}}
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        $(function () {
            $('.js-select2').each(function () {
                const $el = $(this);
                $el.select2({
                    width: '100%',
                    placeholder: $el.data('placeholder') || 'Select an option',
                    allowClear: $el.data('allow-clear') === true || $el.data('allow-clear') === 1 || $el.data('allow-clear') === '1'
                });
            });
        });
    </script>

    <script>
        $("#lineChart").sparkline([102, 109, 120, 99, 110, 105, 115], {
            type: "line",
            height: "70",
            width: "100%",
            lineWidth: "2",
            lineColor: "#177dff",
            fillColor: "rgba(23, 125, 255, 0.14)",
        });

        $("#lineChart2").sparkline([99, 125, 122, 105, 110, 124, 115], {
            type: "line",
            height: "70",
            width: "100%",
            lineWidth: "2",
            lineColor: "#f3545d",
            fillColor: "rgba(243, 84, 93, .14)",
        });

        $("#lineChart3").sparkline([105, 103, 123, 100, 95, 105, 115], {
            type: "line",
            height: "70",
            width: "100%",
            lineWidth: "2",
            lineColor: "#ffa534",
            fillColor: "rgba(255, 165, 52, .14)",
        });
    </script>
    @stack('scripts')
</body>

</html>
<script>
    const configs = {
        success: {
            title: 'Changes saved',
            msg: 'Your profile has been updated successfully.',
            dur: 4000,
            icon: `<svg class="flash-icon" viewBox="0 0 18 18" fill="none"><circle cx="9" cy="9" r="8" fill="#639922" opacity=".2"/><path d="M5.5 9l2.5 2.5 4.5-4.5" stroke="#27500A" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>`
        },
        error: {
            title: 'Something went wrong',
            msg: 'Unable to connect to the server. Please try again.',
            dur: 6000,
            icon: `<svg class="flash-icon" viewBox="0 0 18 18" fill="none"><circle cx="9" cy="9" r="8" fill="#E24B4A" opacity=".2"/><path d="M6 6l6 6M12 6l-6 6" stroke="#791F1F" stroke-width="1.5" stroke-linecap="round"/></svg>`
        },
        warning: {
            title: 'Session expiring soon',
            msg: 'You will be logged out in 5 minutes due to inactivity.',
            dur: 5000,
            icon: `<svg class="flash-icon" viewBox="0 0 18 18" fill="none"><path d="M9 2L16.5 15H1.5L9 2z" fill="#EF9F27" opacity=".25"/><path d="M9 7v3M9 12v.5" stroke="#633806" stroke-width="1.5" stroke-linecap="round"/></svg>`
        },
        info: {
            title: 'New version available',
            msg: 'Refresh the page to get the latest features.',
            dur: 5000,
            icon: `<svg class="flash-icon" viewBox="0 0 18 18" fill="none"><circle cx="9" cy="9" r="8" fill="#378ADD" opacity=".2"/><path d="M9 8v5M9 6v.5" stroke="#0C447C" stroke-width="1.5" stroke-linecap="round"/></svg>`
        },
        neutral: {
            title: 'Copied to clipboard',
            msg: 'The link has been copied and is ready to share.',
            dur: 3000,
            icon: `<svg class="flash-icon" viewBox="0 0 18 18" fill="none"><rect x="5" y="7" width="8" height="8" rx="1.5" fill="none" stroke="var(--color-text-secondary)" stroke-width="1.2"/><path d="M7 7V5.5A1.5 1.5 0 018.5 4h2A1.5 1.5 0 0112 5.5V7" stroke="var(--color-text-secondary)" stroke-width="1.2" stroke-linecap="round"/></svg>`
        }
    };

    let counter = 0;

    function showFlash(type, message = null, title = null) {
        const cfg = configs[type];
        if (!cfg) return;

        const id = 'flash-' + (++counter);
        const dur = cfg.dur;
        const finalMessage = message ?? cfg.msg;
        const finalTitle = title ?? cfg.title;

        const el = document.createElement('div');
        el.className = `flash flash-${type}`;
        el.id = id;
        el.style.setProperty('--dur', dur / 1000 + 's');
        el.innerHTML = `
    ${cfg.icon}
    <div class="flash-body">
            <div class="flash-title">${finalTitle}</div>
            <div class="flash-msg">${finalMessage}</div>
    </div>
    <button class="flash-close" onclick="removeFlash('${id}')" aria-label="Dismiss">×</button>
    <div class="flash-progress"></div>
  `;

        document.getElementById('flashContainer').appendChild(el);

        setTimeout(() => removeFlash(id), dur);
    }

    function removeFlash(id) {
        const el = document.getElementById(id);
        if (!el || el.classList.contains('removing')) return;
        el.classList.add('removing');
        setTimeout(() => el.remove(), 260);
    }

    $(function () {
        @if (session('success'))
            showFlash('success', @json(session('success')), 'Success');
        @endif

        @if (session('error'))
            showFlash('error', @json(session('error')), 'Error');
        @endif

        @if (session('warning'))
            showFlash('warning', @json(session('warning')), 'Warning');
        @endif

        @if (session('info'))
            showFlash('info', @json(session('info')), 'Info');
        @endif

        @if ($errors->any())
            showFlash('error', @json($errors->first()), 'Validation Error');
        @endif
    });
</script>

<script>
    const variants = {
        danger: {
            title: 'Delete this item?',
            body: 'This action is permanent and cannot be undone. The item will be removed immediately.',
            iconBg: '#FCEBEB',
            iconSvg: `<svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M8 4h4M3 6h14M5 6l1 10h8l1-10" stroke="#A32D2D" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"/><path d="M9 10v4M11 10v4" stroke="#A32D2D" stroke-width="1.4" stroke-linecap="round"/></svg>`,
            yesCls: 'btn-yes-danger',
            yesLabel: 'Yes, delete',
            toastYes: { bg: '#EAF3DE', color: '#27500A', text: 'Item deleted.' },
            toastNo: { bg: '#f1efe8', color: '#5f5e5a', text: 'Cancelled — nothing was deleted.' }
        },
        warning: {
            title: 'Leave without saving?',
            body: 'You have unsaved changes. If you leave now, your changes will be lost.',
            iconBg: '#FAEEDA',
            iconSvg: `<svg width="20" height="20" viewBox="0 0 20 20" fill="none"><path d="M10 3L18 16H2L10 3z" fill="#EF9F27" opacity=".25"/><path d="M10 8.5v3.5M10 14v.5" stroke="#633806" stroke-width="1.4" stroke-linecap="round"/></svg>`,
            yesCls: 'btn-yes-warning',
            yesLabel: 'Yes, leave',
            toastYes: { bg: '#FAEEDA', color: '#633806', text: 'Left page — changes discarded.' },
            toastNo: { bg: '#f1efe8', color: '#5f5e5a', text: 'Stayed — changes are safe.' }
        },
        info: {
            title: 'Submit your response?',
            body: 'Once submitted, you will not be able to edit your answers. Make sure everything looks good.',
            iconBg: '#E6F1FB',
            iconSvg: `<svg width="20" height="20" viewBox="0 0 20 20" fill="none"><circle cx="10" cy="10" r="7" fill="#378ADD" opacity=".2"/><path d="M10 9v5M10 7v.5" stroke="#0C447C" stroke-width="1.4" stroke-linecap="round"/></svg>`,
            yesCls: 'btn-yes-info',
            yesLabel: 'Yes, submit',
            toastYes: { bg: '#E6F1FB', color: '#0C447C', text: 'Submitted successfully.' },
            toastNo: { bg: '#f1efe8', color: '#5f5e5a', text: 'Cancelled — not submitted.' }
        }
    };

    let current = null;
    let confirmAction = null;

    function openDialog(type, onYes = null) {
        current = type;
        confirmAction = onYes;
        const v = variants[type];
        document.getElementById('dIcon').style.background = v.iconBg;
        document.getElementById('dIcon').innerHTML = v.iconSvg;
        document.getElementById('dTitle').textContent = v.title;
        document.getElementById('dBody').textContent = v.body;
        const btnYes = document.getElementById('btnYes');
        btnYes.className = 'btn ' + v.yesCls;
        btnYes.textContent = v.yesLabel;
        document.getElementById('overlay').classList.add('show');
    }

    function closeDialog(answer) {
        document.getElementById('overlay').classList.remove('show');
        if (!current) return;

        if (answer === 'yes' && typeof confirmAction === 'function') {
            confirmAction();
        }

        const v = variants[current];
        const t = answer === 'yes' ? v.toastYes : v.toastNo;
        showToast(t);
        current = null;
        confirmAction = null;
    }

    function handleOverlayClick(e) {
        if (e.target === document.getElementById('overlay')) closeDialog('no');
    }

    let toastTimer;

    $(function () {
        $(document).on('click', '.js-delete-btn', function (event) {
            event.preventDefault();
            const form = $(this).closest('form')[0];

            if (!form) {
                return;
            }

            openDialog('danger', function () {
                form.dataset.confirmed = '1';
                form.submit();
            });
        });

        $(document).on('click', '.js-image-preview', function (event) {
            event.preventDefault();

            const image = $(this).data('image');
            const title = $(this).data('title') || 'Image Preview';

            $('#imagePreviewTitle').text(title);
            $('#imagePreviewModalImg').attr('src', image);
            $('#imagePreviewModal').modal('show');
        });

        $(document).on('submit', '.js-delete-form', function (event) {
            const form = this;

            if (form.dataset.confirmed === '1') {
                return true;
            }

            event.preventDefault();
            openDialog('danger', function () {
                form.dataset.confirmed = '1';
                form.submit();
            });
        });
    });
</script>

<script>
    function toggle(menuId, triggerId) {
        const menu = document.getElementById(menuId);
        const trigger = document.getElementById(triggerId);
        const isOpen = menu.classList.contains('visible');
        closeAll();
        if (!isOpen) {
            menu.classList.add('visible');
            trigger.classList.add('open');
        }
    }

    function closeAll() {
        document.querySelectorAll('.dd-menu').forEach(m => m.classList.remove('visible'));
        document.querySelectorAll('.dd-trigger, .icon-btn').forEach(t => t.classList.remove('open'));
    }

    document.addEventListener('click', function (e) {
        if (!e.target.closest('.dd-wrap') && !e.target.closest('.icon-btn')) closeAll();
    });
</script>
