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
                    <div class="dialog-card" role="dialog" aria-modal="true" aria-labelledby="dTitle" aria-describedby="dBody">
                        <div class="dialog-icon" id="dIcon"></div>
                        <h4 class="dialog-title" id="dTitle"></h4>
                        <p class="dialog-body" id="dBody"></p>
                        <div class="dialog-actions">
                            <button type="button" class="btn btn-no" onclick="closeDialog('no')">Cancel</button>
                            <button type="button" class="btn btn-yes-danger" id="btnYes" onclick="closeDialog('yes')">Yes</button>
                        </div>
                    </div>
                </div>
                <div class="result-toast" id="toast"></div>
                
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
                                <a class="nav-link" href="#"> Help </a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" href="#"> Licenses </a>
                            </li>
                        </ul>
                    </nav>
                    <div class="copyright">
                        {{  date('Y') == '2026' ? '2026' : '2026 - ' . date('Y') }}, made with <i
                            class="fa fa-heart heart text-danger"></i> by
                        <a href="https://github.com/CH-lihour" target="_blank">Chetha Lihour</a>
                    </div>
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
    // function showToast(t) {
    //     const el = document.getElementById('toast');
    //     el.style.background = t.bg;
    //     el.style.color = t.color;
    //     el.textContent = t.text;
    //     el.classList.add('show');
    //     clearTimeout(toastTimer);
    //     toastTimer = setTimeout(() => el.classList.remove('show'), 2800);
    // }

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
