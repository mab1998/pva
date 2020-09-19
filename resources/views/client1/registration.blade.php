<!doctype html>
<html class="no-js" lang="">

<head>
    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Login Register | Notika - Notika Admin Template</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- favicon
		============================================ -->
    <link rel="shortcut icon" type="image/x-icon" href="img/favicon.ico">
    <!-- Google Fonts
		============================================ -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,700,900" rel="stylesheet">
    <!-- Bootstrap CSS
		============================================ -->
    <link rel="stylesheet" href="assets2/css/bootstrap.min.css">
    <!-- font awesome CSS
		============================================ -->
    <link rel="stylesheet" href="assets2/css/font-awesome.min.css">
    <!-- owl.carousel CSS
		============================================ -->
    <link rel="stylesheet" href="assets2/css/owl.carousel.css">
    <link rel="stylesheet" href="assets2/css/owl.theme.css">
    <link rel="stylesheet" href="assets2/css/owl.transitions.css">
    <!-- animate CSS
		============================================ -->
    <link rel="stylesheet" href="assets2/css/animate.css">
    <!-- normalize CSS
		============================================ -->
    <link rel="stylesheet" href="assets2/css/normalize.css">
    <!-- mCustomScrollbar CSS
		============================================ -->
    <link rel="stylesheet" href="assets2/css/scrollbar/jquery.mCustomScrollbar.min.css">
    <!-- wave CSS
		============================================ -->
    <link rel="stylesheet" href="assets2/css/wave/waves.min.css">
    <!-- Notika icon CSS
		============================================ -->
    <link rel="stylesheet" href="assets2/css/notika-custom-icon.css">
    <!-- main CSS
		============================================ -->
    <link rel="stylesheet" href="assets2/css/main.css">
    <!-- style CSS
		============================================ -->
    <link rel="stylesheet" href="assets2/style.css">
    <!-- responsive CSS
		============================================ -->
    <link rel="stylesheet" href="assets2/css/responsive.css">
    <!-- modernizr JS
		============================================ -->
    <script src="assets2/js/vendor/modernizr-2.8.3.min.js"></script>
</head>

<body>
            @include('notification.notify')

    <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
    <!-- Login Register area Start-->
    <div class="login-content">
        



        <!-- Register -->
        <div class="nk-block toggled" id="l-register">
            {{-- <form class="nk-form"> --}}
            <form class="nk-form" role="form" method="post" action="{{url('user/post-registration1')}}">

            <div class="input-group mg-t-15">
                    <span class="input-group-addon nk-ic-st-pro"><i class="notika-icon notika-support"></i></span>
                    <div class="nk-int-st">
                            <input type="text" class="form-control" placeholder="First Name" required name="first_name" id="first_name" value="{{old('first_name')}}">
                    </div>
                </div>

                <div class="input-group mg-t-15">
                    <span class="input-group-addon nk-ic-st-pro"><i class="notika-icon notika-support"></i></span>
                    <div class="nk-int-st">
                        <input type="text" class="form-control" placeholder="Last Name" name="last_name"  value="{{old('last_name')}}">

                    </div>
                </div>

                <div class="input-group mg-t-15">
                    <span class="input-group-addon nk-ic-st-pro"><i class="notika-icon notika-support"></i></span>
                    <div class="nk-int-st">
                            <input type="text" placeholder="Email" class="form-control" name="email" required  value="{{old('email')}}">

                    </div>
                </div>



                <div class="input-group mg-t-15">
                    <span class="input-group-addon nk-ic-st-pro"><i class="notika-icon notika-support"></i></span>
                    <div class="nk-int-st">
                        <input type="text" class="form-control" placeholder="User Name" required name="user_name"  value="{{old('user_name')}}">
                    </div>
                </div>



             

                <div class="input-group mg-t-15">
                    <span class="input-group-addon nk-ic-st-pro"><i class="notika-icon notika-edit"></i></span>
                    <div class="nk-int-st">
                                <input type="password" placeholder="Password"  class="form-control" required name="password"  value="{{old('password')}}">
                    </div>
                </div>

                <div class="input-group mg-t-15">
                    <span class="input-group-addon nk-ic-st-pro"><i class="notika-icon notika-edit"></i></span>
                    <div class="nk-int-st">
                                <input type="password" placeholder="Rewrite Password" class="form-control"  required name="cpassword"  value="{{old('cpassword')}}">
                    </div>
                </div>

                <div class="input-group mg-t-15">
                    <span class="input-group-addon nk-ic-st-pro"><i class="notika-icon notika-edit"></i></span>
                    <div class="nk-int-st">
                                <input type="text" placeholder="Phone" class="form-control" required name="phone"  value="{{old('phone')}}">
                    </div>
                </div>

                <div class="input-group mg-t-15">
                    <span class="input-group-addon nk-ic-st-pro"><i class="notika-icon notika-edit"></i></span>
                    <div class="nk-int-st">
                                <select name="country" class="form-control selectpicker" data-live-search="true">
                                    {!!countries(app_config('Country'))!!}
                                </select>
                   </div>
                </div>

                

                <div class="fm-checkbox">
                    <label><input type="checkbox"   name="email_notify" class="i-checks" checked> <i></i>{{language_data('Notify Client with email')}}</label>
                </div>

                <a  onclick="document.getElementById('submit_reg').click();"  class="btn btn-login btn-success btn-float"><i class="notika-icon notika-right-arrow"></i></a>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <input type="submit" style="display:none" id="submit_reg"  value="{{language_data('Login')}}">

            
            </div>
			            @include('notification.notify')


            <div class="nk-navigation rg-ic-stl">
                <a href="#" data-ma-action="nk-login-switch" data-ma-block="#l-login"><i class="notika-icon notika-right-arrow"></i> <span>Sign in</span></a>
                <a href="" data-ma-action="nk-login-switch" data-ma-block="#l-forget-password"><i>?</i> <span>Forgot Password</span></a>
            </form>
        </div>
            @include('notification.notify')

        <!-- Forgot Password -->
        <div class="nk-block" id="l-forget-password">
            <form class="nk-form" role="form" action="{{url('user/forgot-password-token')}}" method="post">

            {{-- <form class="nk-form"> --}}
                <p class="text-left">Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nulla eu risus. Curabitur commodo lorem fringilla enim feugiat commodo sed ac lacus.</p>

                <div class="input-group">
                    <span class="input-group-addon nk-ic-st-pro"><i class="notika-icon notika-mail"></i></span>
                    <div class="nk-int-st">
                        {{-- <input type="text" class="form-control" placeholder="Email Address"> --}}
                        <input type="email" name="email" class="form-control" placeholder="Email Address" required="">

                    </div>
                </div>

                <a  onclick="document.getElementById('submit_forget').click();"  class="btn btn-login btn-success btn-float"><i class="notika-icon notika-right-arrow"></i></a>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                <input type="submit" style="display:none" id="submit_forget"  value="{{language_data('Login')}}">

                        </form>


        </div>
    </div>
    <!-- Login Register area End-->
    <!-- jquery
		============================================ -->
    <script src="assets2/js/vendor/jquery-1.12.4.min.js"></script>
    <!-- bootstrap JS
		============================================ -->
    <script src="assets2/js/bootstrap.min.js"></script>
    <!-- wow JS
		============================================ -->
    <script src="assets2/js/wow.min.js"></script>
    <!-- price-slider JS
		============================================ -->
    <script src="assets2/js/jquery-price-slider.js"></script>
    <!-- owl.carousel JS
		============================================ -->
    <script src="assets2/js/owl.carousel.min.js"></script>
    <!-- scrollUp JS
		============================================ -->
    <script src="assets2/js/jquery.scrollUp.min.js"></script>
    <!-- meanmenu JS
		============================================ -->
    <script src="assets2/js/meanmenu/jquery.meanmenu.js"></script>
    <!-- counterup JS
		============================================ -->
    <script src="assets2/js/counterup/jquery.counterup.min.js"></script>
    <script src="assets2/js/counterup/waypoints.min.js"></script>
    <script src="assets2/js/counterup/counterup-active.js"></script>
    <!-- mCustomScrollbar JS
		============================================ -->
    <script src="assets2/js/scrollbar/jquery.mCustomScrollbar.concat.min.js"></script>
    <!-- sparkline JS
		============================================ -->
    <script src="assets2/js/sparkline/jquery.sparkline.min.js"></script>
    <script src="assets2/js/sparkline/sparkline-active.js"></script>
    <!-- flot JS
		============================================ -->
    <script src="assets2/js/flot/jquery.flot.js"></script>
    <script src="assets2/js/flot/jquery.flot.resize.js"></script>
    <script src="assets2/js/flot/flot-active.js"></script>
    <!-- knob JS
		============================================ -->
    <script src="assets2/js/knob/jquery.knob.js"></script>
    <script src="assets2/js/knob/jquery.appear.js"></script>
    <script src="assets2/js/knob/knob-active.js"></script>
    <!--  Chat JS
		============================================ -->
    <script src="assets2/js/chat/jquery.chat.js"></script>
    <!--  wave JS
		============================================ -->
    <script src="assets2/js/wave/waves.min.js"></script>
    <script src="assets2/js/wave/wave-active.js"></script>
    <!-- icheck JS
		============================================ -->
    <script src="js/icheck/icheck.min.js"></script>
    <script src="js/icheck/icheck-active.js"></script>
    <!--  todo JS
		============================================ -->
    <script src="assets2/js/todo/jquery.todo.js"></script>
    <!-- Login JS
		============================================ -->
    <script src="assets2/js/login/login-action.js"></script>
    <!-- plugins JS
		============================================ -->
    <script src="assets2/js/plugins.js"></script>
    <!-- main JS
		============================================ -->
    <script src="assets2/js/main.js"></script>
</body>

</html>