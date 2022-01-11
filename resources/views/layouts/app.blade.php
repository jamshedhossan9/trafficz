<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="">
<meta name="author" content="">
<link rel="icon" type="image/png" sizes="16x16" href="../plugins/images/favicon.png">
<title>TrafficZ</title>
<!-- Bootstrap Core CSS -->
<link href="{{asset('template1/bootstrap/dist/css/bootstrap.min.css')}}" rel="stylesheet">
<!-- Menu CSS -->
<link href="{{asset('plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.css')}}" rel="stylesheet">
<!-- toast CSS -->
<link href="{{asset('plugins/bower_components/toast-master/css/jquery.toast.css')}}" rel="stylesheet">
<link rel="stylesheet" href="{{asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.css')}}">
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.2/dist/sweetalert2.min.css">
<link rel="stylesheet" href="{{asset('plugins/bower_components/json-viewer/jquery.json-viewer.css')}}">
<!-- animation CSS -->
<link href="{{asset('template1/css/animate.css')}}" rel="stylesheet">
<!-- Custom CSS -->
<link href="{{asset('template1/css/style.css')}}" rel="stylesheet">
<!-- color CSS -->
<link href="{{asset('template1/css/colors/blue.css')}}" id="theme"  rel="stylesheet">

<link href="{{asset('css/custom.css')}}{{version()}}" id="theme"  rel="stylesheet">
<script src="{{route('global-js')}}{{version()}}"></script>
@stack('css')
</head>
<body>
<!-- Preloader -->
<div class="preloader">
  <div class="cssload-speeding-wheel"></div>
</div>
<div id="wrapper">
    <!-- Navigation -->
    <nav class="navbar navbar-default navbar-static-top m-b-0">
        <div class="navbar-header p-l-10 p-r-10"> 
            <a class="navbar-toggle hidden-sm hidden-md hidden-lg " href="javascript:void(0)" data-toggle="collapse" data-target=".navbar-collapse"><i class="ti-menu"></i></a>
            <div class="top-left-part">
                <a class="logo page-logo" href="index.html">
                    <b><img src="{{asset('img/logo/logo.png')}}" alt="home" /></b>
                </a>
            </div>
            <ul class="nav navbar-top-links navbar-right pull-right">
                <li class="dropdown"> 
                    <a class="dropdown-toggle profile-pic" data-toggle="dropdown" href="#"> 
                        <b class="">{{ $user->name }}</b> 
                    </a>
                    <ul class="dropdown-menu dropdown-user animated flipInY">
                        {{-- <li><a href="#"><i class="ti-settings"></i> Account Setting</a></li> --}}
                        <li role="separator" class="divider"></li>
                        <li>
                            <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none hidden">
                                @csrf
                            </form>
                            <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form').submit();"><i class="fa fa-power-off"></i> Logout</a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </nav>
    <!-- Left navbar-header -->
    @include('sections.page-menu')
    <!-- Left navbar-header end -->

    <!-- Page Content -->
    <div id="page-wrapper">
        <div class="container-fluid">
            @if (!empty($__env->yieldContent('pageActionbar')) || !empty($pageTitle))
                <div class="flex-box align-center bg-title">
                    <div class="flex-grow">
                        <h4 class="page-title m-0">{{$pageTitle}}</h4>
                    </div>
                    <div class="flex-none flex-box gap-5 align-center">
                        @yield('pageActionbar')
                    </div>
                    <!-- /.col-lg-12 -->
                </div>
            @endif
            <div class="clearfix content-section">
                @yield('content')
            </div>
        </div>
        
        <!-- /.container-fluid -->
        <footer class="footer text-center"> 2022 &copy; trafficz.net </footer>
    </div>
  <!-- /#page-wrapper -->
</div>
<!-- /#wrapper -->
<!-- jQuery -->
<script src="{{asset('plugins/bower_components/jquery/dist/jquery.min.js')}}"></script>
<!-- Bootstrap Core JavaScript -->
<script src="{{asset('template1/bootstrap/dist/js/bootstrap.min.js')}}"></script>
<!-- Menu Plugin JavaScript -->
<script src="{{asset('plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js')}}"></script>
<!--slimscroll JavaScript -->
<script src="{{asset('template1/js/jquery.slimscroll.js')}}"></script>
<!--Wave Effects -->
<script src="{{asset('template1/js/waves.js')}}"></script>
<script src="{{asset('template1/js/custom.js')}}"></script>
<script src="{{asset('plugins/bower_components/toast-master/js/jquery.toast.js')}}"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.3.2/dist/sweetalert2.min.js"></script>
<script src="{{asset('plugins/bower_components/json-viewer/jquery.json-viewer.js')}}"></script>
<script type="text/javascript" src="{{asset('plugins/bower_components/bootstrap-select/bootstrap-select.min.js')}}"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/moment-timezone/0.5.33/moment-timezone-with-data.min.js"></script>
<script>
    moment.tz.setDefault("America/New_York");
</script>
<script src="{{asset('js/custom.js')}}{{version()}}"></script>
@stack('js')
</body>
</html>
