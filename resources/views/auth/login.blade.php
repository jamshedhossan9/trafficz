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
<!-- animation CSS -->
<link href="{{asset('template1/css/animate.css')}}" rel="stylesheet">
<!-- Custom CSS -->
<link href="{{asset('template1/css/style.css')}}" rel="stylesheet">
<!-- color CSS -->
<link href="{{asset('template1/css/colors/blue.css')}}" id="theme"  rel="stylesheet">
<style>
    #wrapper{
        max-width: none;
    }
    .invalid-feedback{
        padding-top: 5px;
    }
</style>
</head>
<body>
<!-- Preloader -->
<div class="preloader">
  <div class="cssload-speeding-wheel"></div>
</div>
<section id="wrapper" class="login-register">
  <div class="login-box">
    <div class="white-box">

        <form method="POST" action="{{ route('login') }}"  class="form-horizontal form-material" id="loginform">
            @csrf
            <h3 class="box-title m-b-20">Sign In</h3>
            <div class="form-group ">
                <div class="col-xs-12">
                    <input name="email" class="form-control @error('email') is-invalid @enderror" id="email" type="email" required placeholder="{{ __('E-Mail Address') }}" value="{{ old('email') }}" autocomplete="email" autofocus>
                    @error('email')
                        <div class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </div>
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-12">
                    <input name="password" class="form-control @error('password') is-invalid @enderror"  id="password" type="password" required placeholder="{{ __('Password') }}">
                    @error('password')
                        <div class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </div>
                    @enderror
                </div>
            </div>
            <div class="form-group">
                <div class="col-xs-12">
                    <div class="checkbox checkbox-info">
                        <input type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                        <label for="remember">Remember Me</label>
                    </div> 
                </div> 
            </div>
            <div class="form-action">
                <button type="submit" class="btn btn-info btn-block">
                    {{ __('Login') }}
                </button>
            </div>

        </form>
    </div>
  </div>
</section>
<!-- jQuery -->
<script src="{{asset('plugins/bower_components/jquery/dist/jquery.min.js')}}"></script>
<!-- Bootstrap Core JavaScript -->
<script src="{{asset('template1/bootstrap/dist/js/bootstrap.min.js')}}"></script>
<!-- Menu Plugin JavaScript -->
<script src="{{asset('plugins/bower_components/sidebar-nav/dist/sidebar-nav.min.js')}}"></script>
<script src="{{asset('template1/js/custom.js')}}"></script>
</body>
</html>
