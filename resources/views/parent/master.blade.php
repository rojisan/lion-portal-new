<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- <meta http-equiv="Content-Security-Policy" content="script-src 'self' http://islogistik.id 'unsafe-inline' 'unsafe-eval';"> -->
    <title>LION-PORTAL</title>
    <link href="{{asset('plugins/fontawesome-free/css/all.min.css')}}" rel="stylesheet">
    <link href="{{asset('plugins/daterangepicker/daterangepicker.css')}}" rel="stylesheet">
    <link href="{{asset('plugins/bootstrap-colorpicker/css/bootstrap-colorpicker.min.css')}}" rel="stylesheet">
    <link href="{{asset('plugins/dropzone/min/dropzone.min.css')}}" rel="stylesheet">
    @yield('extend-css')
    <link href="{{asset('css/adminlte.css')}}" rel="stylesheet">
    <link href="{{asset('dist/css/adminlte.min.css')}}" rel="stylesheet">
    <style>
        .main-sidebar > .brand-link {
            background-color: white;
            color: black;
        }
        .sidebar-dark-primary {
            background-color: #FFFFFF;
        }
        [class*=sidebar-dark-] .sidebar a {
            color: #3ea555;
        }
        [class*=sidebar-dark-] .nav-treeview>.nav-item>.nav-link {
            color: #3ea555;
        }

        [class*=sidebar-dark-] .nav-treeview>.nav-item>.nav-link.active {
            background-color: #3ea555;
            color: #FFFFFF;
        }

        [class*=sidebar-dark-] .nav-treeview>.nav-item>.nav-link.active:focus, [class*=sidebar-dark-] .nav-treeview>.nav-item>.nav-link.active:hover {
            background-color: #3ea555;
            color: #FFFFFF;
        }
        
        [class*=sidebar-dark-] .nav-treeview>.nav-item>.nav-link:hover {
            color: #FFFFFF;
            background-color: #3ea555;
        }
        [class*=sidebar-dark-] .nav-sidebar>.nav-item.menu-open>.nav-link, [class*=sidebar-dark-] .nav-sidebar>.nav-item:hover>.nav-link, [class*=sidebar-dark-] .nav-sidebar>.nav-item>.nav-link:focus {
            background-color: rgba(255,255,255,.1);
            color: #3ea555;
        }
        .sidebar-dark-primary .nav-sidebar>.nav-item>.nav-link.active, .sidebar-light-primary .nav-sidebar>.nav-item>.nav-link.active, .nav-treeview>.nav-item>.nav-link.active {
            background-color: #3ea555;
            color: #FFFFFF;
        }
        .sidebar-dark-primary .nav-sidebar>.nav-item>.nav-link:hover, .sidebar-light-primary .nav-sidebar>.nav-item>.nav-link:hover, .nav-treeview>.nav-item>.nav-link:hover {
            background-color: #3ea555;
            color: #FFFFFF;
        }
        .sidebar-dark-primary .nav-sidebar>.nav-item>.nav-link:focus, .sidebar-light-primary .nav-sidebar>.nav-item>.nav-link:focus, .nav-treeview>.nav-item>.nav-link:focus {
            background-color: rgba(15, 104, 168, 0.192);
            color: #0F68A8;
        }
        
        [class*=sidebar-dark] .user-panel {
            border-bottom: 1px solid #e5e5e5;
        }
        [class*=sidebar-dark] .brand-link {
            border-bottom: 1px solid #e5e5e5;
        }
        [class*=sidebar-dark-] .sidebar a:hover {
            color: #0F68A8;
        }
        .main-sidebar > .brand-link:hover {
            color: #0F68A8;
        }
        a.edit {
            margin-right: 5px;
        }
        .main-footer {
            border-left: 1px solid #dee2e6;
        }
        .btn-primary {
            color: #fff;
            background-color: #0F68A8;
            border-color: #0F68A8;
            box-shadow: none;
        }
    </style>
</head>
<body  class="hold-transition sidebar-mini">
    <div class="wrapper">
        @include('auth.header')
        @include('auth.menu')
        @yield('body')
        @include('parent.footer')
    </div>
    <script src="{{ asset('plugins/jquery/jquery.min.js') }}"></script>
    <script src="{{ asset('plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
    @yield('extend-js')
    <script src="{{ asset('dist/js/adminlte.min.js') }}"></script>
    <script src="{{ asset('dist/js/demo.js') }}"></script>
    <!-- Chech Idle Script -->
    <script type="text/javascript">
        var idleTime = 0;
        $(document).ready(function() {
            //Increment the idle time counter every minute.
            var idleInterval = setInterval(timerIncrement, 60000); // 1 minute
            //Zero the idle timer on mouse movement.
            $(this).mousemove(function(e) {
                idleTime = 0;
                isIdle = false;
            });
            $(this).keypress(function(e) {
                idleTime = 0;
                isIdle = false;
            });
        });

        var isIdle = false;

        function timerIncrement($username) {
            idleTime = idleTime + 1;
            if (idleTime > 15 && !isIdle) { // 15 minutes
                isIdle = true;
                alert("Session timeout");
                var url = "{{ route('login') }}";
                $.ajax({
                    type: "POST",
                    url:  "{{ route('login') }}",
                    data: { 
                        '_token': "{{csrf_token()}}",
                    },
                    success: function(response) {
                        document.location.href=url;
                    }
                });
            }
        }
    </script>
</body>
</html>