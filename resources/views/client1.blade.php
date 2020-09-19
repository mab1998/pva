<!doctype html>
<html class="no-js" lang="">

<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">


    <meta charset="utf-8">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>Dashboard One | Notika - Notika Admin Template</title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- favicon
		============================================ -->
    <link rel="shortcut icon" type="image/x-icon" href="<?php echo asset('assets2/img/favicon.ico'); ?>" />

    <!-- Google Fonts
		============================================ -->
    <link href="https://fonts.googleapis.com/css?family=Roboto:100,300,400,700,900" rel="stylesheet">
    <!-- Bootstrap CSS
		============================================ -->
    {!! Html::style("assets2/css/bootstrap.min.css")!!}
    <!-- Bootstrap CSS
		============================================ -->
    {!! Html::style("assets2/css/font-awesome.min.css")!!}
    <!-- owl.carousel CSS
		============================================ -->
    {!! Html::style("assets2/css/owl.carousel.css")!!}
    {!! Html::style("assets2/css/owl.theme.css")!!}
    {!! Html::style("assets2/css/owl.transitions.css")!!}
    <!-- meanmenu CSS
		============================================ -->
    {!! Html::style("assets2/css/meanmenu/meanmenu.min.css")!!}
    <!-- animate CSS
		============================================ -->
    {!! Html::style("assets2/css/animate.css")!!}
    <!-- normalize CSS
		============================================ -->
    {!! Html::style("assets2/css/normalize.css")!!}
    <!-- mCustomScrollbar CSS
		============================================ -->
    {!! Html::style("assets2/css/scrollbar/jquery.mCustomScrollbar.min.css")!!}
    <!-- jvectormap CSS
		============================================ -->
    {!! Html::style("assets2/css/jvectormap/jquery-jvectormap-2.0.3.css")!!}
    <!-- notika icon CSS
		============================================ -->
    {!! Html::style("assets2/css/notika-custom-icon.css")!!}
    <!-- wave CSS
		============================================ -->
    {!! Html::style("assets2/css/wave/waves.min.css")!!}
    <!-- main CSS
		============================================ -->
    {!! Html::style("assets2/css/main.css")!!}
    <!-- style CSS
		============================================ -->
    {!! Html::style("assets2/style.css")!!}
    <!-- responsive CSS
		============================================ -->
    {!! Html::style("assets2/css/responsive.css")!!}
    <!-- modernizr JS
		============================================ -->
    {!! Html::script("assets2/js/vendor/modernizr-2.8.3.min.js")!!}
</head>

<body>
    <!--[if lt IE 8]>
            <p class="browserupgrade">You are using an <strong>outdated</strong> browser. Please <a href="http://browsehappy.com/">upgrade your browser</a> to improve your experience.</p>
        <![endif]-->
    <!-- Start Header Top Area -->
    <div class="header-top-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
                    <div class="logo-area">
                        <a href="#"><img src="assets2/img/logo/logo.png" alt="" /></a>
                    </div>
                </div>
                <div class="col-lg-8 col-md-8 col-sm-12 col-xs-12">
                    <div class="header-top-menu">
                        <ul class="nav navbar-nav notika-top-nav">

                            <li>{{Auth::guard('client')->user()->sms_limit}}</li>

                            <li class="nav-item dropdown">
                                <a href="#" data-toggle="dropdown" role="button" aria-expanded="false" class="nav-link dropdown-toggle"><span><i class="notika-icon notika-search"></i></span></a>
                                <div role="menu" class="dropdown-menu search-dd animated flipInX">
                                    <div class="search-input">
                                        <i class="notika-icon notika-left-arrow"></i>
                                        <input type="text" />
                                    </div>
                                </div>
                            </li>
                            
                            <li class="nav-item dropdown">

                            
                                <a href="#" data-toggle="dropdown" role="button" aria-expanded="false" class="nav-link dropdown-toggle"><span><i class="notika-icon notika-mail"></i></span></a>
                                <div role="menu" class="dropdown-menu message-dd animated zoomIn">
                                    <div class="hd-mg-tt">
                                        <h2>Messages</h2>
                                    </div>
                                    <div class="hd-message-info">
                                        <a href="#">
                                            <div class="hd-message-sn">
                                                <div class="hd-message-img">
                                                    <img src="assets2/img/post/1.jpg" alt="" />
                                                </div>
                                                <div class="hd-mg-ctn">
                                                    <h3>David Belle</h3>
                                                    <p>Cum sociis natoque penatibus et magnis dis parturient montes</p>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="#">
                                            <div class="hd-message-sn">
                                                <div class="hd-message-img">
                                                    <img src="assets2/img/post/2.jpg" alt="" />
                                                </div>
                                                <div class="hd-mg-ctn">
                                                    <h3>Jonathan Morris</h3>
                                                    <p>Cum sociis natoque penatibus et magnis dis parturient montes</p>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="#">
                                            <div class="hd-message-sn">
                                                <div class="hd-message-img">
                                                    <img src="assets2/img/post/4.jpg" alt="" />
                                                </div>
                                                <div class="hd-mg-ctn">
                                                    <h3>Fredric Mitchell</h3>
                                                    <p>Cum sociis natoque penatibus et magnis dis parturient montes</p>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="#">
                                            <div class="hd-message-sn">
                                                <div class="hd-message-img">
                                                    <img src="assets2/img/post/1.jpg" alt="" />
                                                </div>
                                                <div class="hd-mg-ctn">
                                                    <h3>David Belle</h3>
                                                    <p>Cum sociis natoque penatibus et magnis dis parturient montes</p>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="#">
                                            <div class="hd-message-sn">
                                                <div class="hd-message-img">
                                                    <img src="assets2/img/post/2.jpg" alt="" />
                                                </div>
                                                <div class="hd-mg-ctn">
                                                    <h3>Glenn Jecobs</h3>
                                                    <p>Cum sociis natoque penatibus et magnis dis parturient montes</p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="hd-mg-va">
                                        <a href="#">View All</a>
                                    </div>
                                </div>
                            </li>
                            <li class="nav-item nc-al"><a href="#" data-toggle="dropdown" role="button" aria-expanded="false" class="nav-link dropdown-toggle"><span><i class="notika-icon notika-alarm"></i></span><div class="spinner4 spinner-4"></div><div class="ntd-ctn"><span>3</span></div></a>
                                <div role="menu" class="dropdown-menu message-dd notification-dd animated zoomIn">
                                    <div class="hd-mg-tt">
                                        <h2>Notification</h2>
                                    </div>
                                    <div class="hd-message-info">
                                        <a href="#">
                                            <div class="hd-message-sn">
                                                <div class="hd-message-img">
                                                    <img src="assets2/img/post/1.jpg" alt="" />
                                                </div>
                                                <div class="hd-mg-ctn">
                                                    <h3>David Belle</h3>
                                                    <p>Cum sociis natoque penatibus et magnis dis parturient montes</p>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="#">
                                            <div class="hd-message-sn">
                                                <div class="hd-message-img">
                                                    <img src="assets2/img/post/2.jpg" alt="" />
                                                </div>
                                                <div class="hd-mg-ctn">
                                                    <h3>Jonathan Morris</h3>
                                                    <p>Cum sociis natoque penatibus et magnis dis parturient montes</p>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="#">
                                            <div class="hd-message-sn">
                                                <div class="hd-message-img">
                                                    <img src="assets2/img/post/4.jpg" alt="" />
                                                </div>
                                                <div class="hd-mg-ctn">
                                                    <h3>Fredric Mitchell</h3>
                                                    <p>Cum sociis natoque penatibus et magnis dis parturient montes</p>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="#">
                                            <div class="hd-message-sn">
                                                <div class="hd-message-img">
                                                    <img src="assets2/img/post/1.jpg" alt="" />
                                                </div>
                                                <div class="hd-mg-ctn">
                                                    <h3>David Belle</h3>
                                                    <p>Cum sociis natoque penatibus et magnis dis parturient montes</p>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="#">
                                            <div class="hd-message-sn">
                                                <div class="hd-message-img">
                                                    <img src="assets2/img/post/2.jpg" alt="" />
                                                </div>
                                                <div class="hd-mg-ctn">
                                                    <h3>Glenn Jecobs</h3>
                                                    <p>Cum sociis natoque penatibus et magnis dis parturient montes</p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="hd-mg-va">
                                        <a href="#">View All</a>
                                    </div>
                                </div>
                            </li>
                            <li class="nav-item"><a href="#" data-toggle="dropdown" role="button" aria-expanded="false" class="nav-link dropdown-toggle"><span><i class="notika-icon notika-menus"></i></span><div class="spinner4 spinner-4"></div><div class="ntd-ctn"><span>2</span></div></a>
                                <div role="menu" class="dropdown-menu message-dd task-dd animated zoomIn">
                                    <div class="hd-mg-tt">
                                        <h2>Tasks</h2>
                                    </div>
                                    <div class="hd-message-info hd-task-info">
                                        <div class="skill">
                                            <div class="progress">
                                                <div class="lead-content">
                                                    <p>HTML5 Validation Report</p>
                                                </div>
                                                <div class="progress-bar wow fadeInLeft" data-progress="95%" style="width: 95%;" data-wow-duration="1.5s" data-wow-delay="1.2s"> <span>95%</span>
                                                </div>
                                            </div>
                                            <div class="progress">
                                                <div class="lead-content">
                                                    <p>Google Chrome Extension</p>
                                                </div>
                                                <div class="progress-bar wow fadeInLeft" data-progress="85%" style="width: 85%;" data-wow-duration="1.5s" data-wow-delay="1.2s"><span>85%</span> </div>
                                            </div>
                                            <div class="progress">
                                                <div class="lead-content">
                                                    <p>Social Internet Projects</p>
                                                </div>
                                                <div class="progress-bar wow fadeInLeft" data-progress="75%" style="width: 75%;" data-wow-duration="1.5s" data-wow-delay="1.2s"><span>75%</span> </div>
                                            </div>
                                            <div class="progress">
                                                <div class="lead-content">
                                                    <p>Bootstrap Admin</p>
                                                </div>
                                                <div class="progress-bar wow fadeInLeft" data-progress="93%" style="width: 65%;" data-wow-duration="1.5s" data-wow-delay="1.2s"><span>65%</span> </div>
                                            </div>
                                            <div class="progress progress-bt">
                                                <div class="lead-content">
                                                    <p>Youtube App</p>
                                                </div>
                                                <div class="progress-bar wow fadeInLeft" data-progress="55%" style="width: 55%;" data-wow-duration="1.5s" data-wow-delay="1.2s"><span>55%</span> </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="hd-mg-va">
                                        <a href="#">View All</a>
                                    </div>
                                </div>
                            </li>
                            <li class="nav-item"><a href="#" data-toggle="dropdown" role="button" aria-expanded="false" class="nav-link dropdown-toggle"><span><i class="notika-icon notika-chat"></i></span></a>
                                <div role="menu" class="dropdown-menu message-dd chat-dd animated zoomIn">
                                    <div class="hd-mg-tt">
                                        <h2>Chat</h2>
                                    </div>
                                    <div class="search-people">
                                        <i class="notika-icon notika-left-arrow"></i>
                                        <input type="text" placeholder="Search People" />
                                    </div>
                                    <div class="hd-message-info">
                                        <a href="#">
                                            <div class="hd-message-sn">
                                                <div class="hd-message-img chat-img">
                                                    <img src="assets2/img/post/1.jpg" alt="" />
                                                    <div class="chat-avaible"><i class="notika-icon notika-dot"></i></div>
                                                </div>
                                                <div class="hd-mg-ctn">
                                                    <h3>David Belle</h3>
                                                    <p>Available</p>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="#">
                                            <div class="hd-message-sn">
                                                <div class="hd-message-img chat-img">
                                                    <img src="assets2/img/post/2.jpg" alt="" />
                                                </div>
                                                <div class="hd-mg-ctn">
                                                    <h3>Jonathan Morris</h3>
                                                    <p>Last seen 3 hours ago</p>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="#">
                                            <div class="hd-message-sn">
                                                <div class="hd-message-img chat-img">
                                                    <img src="assets2/img/post/4.jpg" alt="" />
                                                </div>
                                                <div class="hd-mg-ctn">
                                                    <h3>Fredric Mitchell</h3>
                                                    <p>Last seen 2 minutes ago</p>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="#">
                                            <div class="hd-message-sn">
                                                <div class="hd-message-img chat-img">
                                                    <img src="assets2/img/post/1.jpg" alt="" />
                                                    <div class="chat-avaible"><i class="notika-icon notika-dot"></i></div>
                                                </div>
                                                <div class="hd-mg-ctn">
                                                    <h3>David Belle</h3>
                                                    <p>Available</p>
                                                </div>
                                            </div>
                                        </a>
                                        <a href="#">
                                            <div class="hd-message-sn">
                                                <div class="hd-message-img chat-img">
                                                    <img src="assets2/img/post/2.jpg" alt="" />
                                                    <div class="chat-avaible"><i class="notika-icon notika-dot"></i></div>
                                                </div>
                                                <div class="hd-mg-ctn">
                                                    <h3>Glenn Jecobs</h3>
                                                    <p>Available</p>
                                                </div>
                                            </div>
                                        </a>
                                    </div>
                                    <div class="hd-mg-va">
                                        <a href="#">View All</a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- End Header Top Area -->
    <!-- Mobile Menu start -->
    <div class="mobile-menu-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="mobile-menu">
                        <nav id="dropdown">
                            <ul class="mobile-menu-nav">
                                <li @if(Request::path()== 'dashboard1') class="active" @endif><a data-toggle="collapse" data-target="#Charts" href="#">Home</a>
                                    <ul class="collapse dropdown-header-top">
                                        <li><a href="index.html">Dashboard</a></li>
                                        {{-- <li><a href="index-2.html">Dashboard Two</a></li>
                                        <li><a href="index-3.html">Dashboard Three</a></li>
                                        <li><a href="index-4.html">Dashboard Four</a></li>
                                        <li><a href="analytics.html">Analytics</a></li>
                                        <li><a href="widgets.html">Widgets</a></li> --}}
                                    </ul>
                                </li>
                                <li  @if(Request::path()== 'user/invoices/all1' OR Request::path()=='user/invoices/pay-invoice'  OR Request::path()=='user/invoices/view/'.view_id() OR Request::path()=='user/invoices/edit/'.view_id() OR Request::path()== 'user/invoices/recurring') class="active" @endif><a data-toggle="collapse" data-target="#demoevent" href="#">Invoices</a>
                                    <ul id="demoevent" class="collapse dropdown-header-top">
                                        <li><a href="{{url('user/invoices/all1')}}">All Orders</a></li>
                                        <li><a href="view-email.html">Paid Invoice</a></li>
                                        <li><a href="compose-email.html">Unpaid Invoices</a></li>
                                    </ul>
                                </li> 
                                <li @if(Request::path()== 'user/sms/purchase-sms-plan' OR Request::path()== 'user/sms/post-purchase-sms-plan' OR Request::path()=='user/sms/sms-plan-feature/'.view_id() OR Request::path()== 'user/sms/buy-unit') class="active" @endif ><a data-toggle="collapse" data-target="#democrou" href="#">Recharge</a>
                                    <ul id="democrou" class="collapse dropdown-header-top">
                                        <li ><a href="animations.html">Purchace Plan</a></li>
                                        <li><a href="google-map.html">Buy Unit</a></li>
                                     
                                    </ul>
                                </li>

                                @if(Auth::guard('client')->user()->api_access=='Yes')

                                <li @if(Request::path()== 'user/sms-api/info') class="active" @endif><a data-toggle="collapse" data-target="#demolibra" href="#">API</a>
                                    <ul id="demolibra" class="collapse dropdown-header-top">
                                        <li><a href="flot-charts.html">API</a></li>
                                        {{-- <li><a href="bar-charts.html">Bar Charts</a></li>
                                        <li><a href="line-charts.html">Line Charts</a></li>
                                        <li><a href="area-charts.html">Area Charts</a></li> --}}
                                    </ul>
                                </li>
                                @endif

                                <li @if(Request::path()== 'user/tickets/all1'  OR Request::path()=='user/tickets/view-ticket/'.view_id() OR Request::path()== 'user/tickets/create-new') class="active" @endif><a data-toggle="collapse" data-target="#demodepart" href="user/tickets/all1">Support</a>
                                    <ul id="demodepart" class="collapse dropdown-header-top">
                                        <li><a href="normal-table.html">Support Tickets</a></li>
                                        <li><a href="data-table.html">Create New Ticket</a></li>
                                    </ul>
                                </li>
                              
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Mobile Menu end -->
    <!-- Main Menu area start-->
           <div class="main-menu-area mg-tb-40">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <ul class="nav nav-tabs notika-menu-wrap menu-it-icon-pro">
                        <li @if(Request::path()== 'dashboard1') class="active" @endif ><a data-toggle="Home" href="/dashboard1"><i class="notika-icon notika-house"></i> Home</a>
                        </li>
                        <li @if(Request::path()== 'user/invoices/all1' OR Request::path()=='user/invoices/paid-invoice' OR Request::path()=='user/invoices/unpaid-invoice' OR Request::path()=='user/invoices/pay-invoice'  OR Request::path()=='user/invoices/view/'.view_id() OR Request::path()=='user/invoices/edit/'.view_id() OR Request::path()== 'user/invoices/recurring') class="active" @endif><a data-toggle="invoices" href="/user/invoices/all1"><i class="notika-icon notika-mail"></i> Invoice</a>
                        </li>
                        <li @if(Request::path()== 'domain' ) class="active" @endif><a data-toggle="invoices" href="/domain"><i class="notika-icon notika-mail"></i> Domain</a>
                        </li>
                        <li @if(Request::path()== 'user/sms/purchase-sms-plan1' OR Request::path()== 'user/sms/post-purchase-sms-plan' OR Request::path()=='user/sms/sms-plan-feature/'.view_id() OR Request::path()== 'user/sms/buy-unit') class="active" @endif><a data-toggle="recharge" href="/user/sms/purchase-sms-plan1"><i class="notika-icon notika-edit"></i> Recharge</a>
                        </li>
                        @if(Auth::guard('client')->user()->api_access=='Yes')
                        <li @if(Request::path()=='user/sms-api/info1') class="active" @endif><a data-toggle="api" href="/user/sms-api/info1"><i class="notika-icon notika-bar-chart"></i> API</a>
                        </li>
                        @endif

                        <li @if(Request::path()== 'user/tickets/all1'  OR Request::path()=='user/tickets/view-ticket1/'.view_id() OR Request::path()== 'user/tickets/create-new1') class="active" @endif><a data-toggle="support" href="/user/tickets/all1"><i class="notika-icon notika-windows"></i> Support</a>
                        </li>
                        {{-- <li><a data-toggle="tab" href="#Forms"><i class="notika-icon notika-form"></i> Forms</a>
                        </li>
                        <li><a data-toggle="tab" href="#Appviews"><i class="notika-icon notika-app"></i> App views</a>
                        </li>
                        <li><a data-toggle="tab" href="#Page"><i class="notika-icon notika-support"></i> Pages</a>
                        </li> --}}
                    </ul>
                    <div class="tab-content custom-menu-content">
                        <div id="Home" class="tab-pane @if(Request::path()== 'dashboard1') in active @endif  notika-tab-menu-bg animated flipInX">
                            <ul class="notika-main-menu-dropdown">
                                <li><a href="/dashboard1">Dashboard</a>
                                </li>
                       
                            </ul>
                        </div>
                        <div id="invoices" class="tab-pane @if(Request::path()== 'user/invoices/all1' OR Request::path()=='user/invoices/paid-invoice' OR Request::path()=='user/invoices/unpaid-invoice' OR  Request::path()=='user/invoices/pay-invoice'  OR Request::path()=='user/invoices/view/'.view_id() OR Request::path()=='user/invoices/edit/'.view_id() OR Request::path()== 'user/invoices/recurring') in active @endif notika-tab-menu-bg animated flipInX">
                            <ul class="notika-main-menu-dropdown">
                                <li><a href="/user/invoices/all1">All Orders</a>
                                </li>
                                <li><a href="/user/invoices/paid-invoice">Paid Invoices</a>
                                </li>
                                <li><a href="/user/invoices/unpaid-invoice">Unpaid Invoices</a>
                                </li>
                            </ul>
                        </div>
                        <div id="recharge" class="tab-pane @if(Request::path()== 'user/sms/purchase-sms-plan1' OR Request::path()== 'user/sms/post-purchase-sms-plan' OR Request::path()=='user/sms/sms-plan-feature/'.view_id() OR Request::path()== 'user/sms/buy-unit') in active @endif notika-tab-menu-bg animated flipInX">
                            <ul class="notika-main-menu-dropdown">
                                <li><a href="/user/sms/purchase-sms-plan1">Purchase Plan</a>
                                </li>
                                {{-- <li><a href="google-map.html">Buy Unit</a>
                                </li> --}}
                                {{-- <li><a href="data-map.html">Data Maps</a>
                                </li>
                                <li><a href="code-editor.html">Code Editor</a>
                                </li>
                                <li><a href="image-cropper.html">Images Cropper</a>
                                </li>
                                <li><a href="wizard.html">Wizard</a>
                                </li> --}}
                            </ul>
                        </div>
                        <div id="api" class="tab-pane @if(Request::path()=='user/sms-api/info1') in active @endif notika-tab-menu-bg animated flipInX">
                            <ul class="notika-main-menu-dropdown">
                                <li><a href="/user/sms-api/info1">API</a>
                                </li>
                             
                            </ul>
                        </div>
                        <div id="support" class="tab-pane @if(Request::path()=='user/tickets/all1' OR Request::path()=='user/tickets/view-ticket1/'.view_id() OR Request::path()=='user/tickets/create-new1') in active @endif notika-tab-menu-bg animated flipInX">
                            <ul class="notika-main-menu-dropdown">
                                <li><a href="/user/tickets/all1">Support Tickets</a>
                                </li>
                                <li><a href="/user/tickets/create-new1">Create New Ticket</a>
                                </li>
                            </ul>
                        </div>

                        

                            </ul>
                        </div> 
                        {{-- <div id="Appviews" class="tab-pane notika-tab-menu-bg animated flipInX">
                            <ul class="notika-main-menu-dropdown">
                                <li><a href="notification.html">Notifications</a>
                                </li>
                                <li><a href="alert.html">Alerts</a>
                                </li>
                                <li><a href="modals.html">Modals</a>
                                </li>
                                <li><a href="buttons.html">Buttons</a>
                                </li>
                                <li><a href="tabs.html">Tabs</a>
                                </li>
                                <li><a href="accordion.html">Accordion</a>
                                </li>
                                <li><a href="dialog.html">Dialogs</a>
                                </li>
                                <li><a href="popovers.html">Popovers</a>
                                </li>
                                <li><a href="tooltips.html">Tooltips</a>
                                </li>
                                <li><a href="dropdown.html">Dropdowns</a>
                                </li>
                            </ul>
                        </div> --}}
                        {{-- <div id="Page" class="tab-pane notika-tab-menu-bg animated flipInX">
                            <ul class="notika-main-menu-dropdown">
                                <li><a href="contact.html">Contact</a>
                                </li>
                                <li><a href="invoice.html">Invoice</a>
                                </li>
                                <li><a href="typography.html">Typography</a>
                                </li>
                                <li><a href="color.html">Color</a>
                                </li>
                                <li><a href="login-register.html">Login Register</a>
                                </li>
                                <li><a href="404.html">404 Page</a>
                                </li>
                            </ul>
                        </div> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Main Menu area End-->

    {{--Content File Start Here--}}
    @yield('content');

    {{--Content File End Here--}}
    <!-- Start Footer area-->
    <div class="footer-copyright-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="footer-copy-right">
                        <p>Copyright © 2018 . All rights reserved. Template by <a href="https://colorlib.com">Colorlib</a>.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <input type="hidden" id="_url" value="{{url('/')}}">
    <input type="hidden" id="_unsubscribe_message" value="{{ app_config('unsubscribe_message') }}">


    <!-- End Footer area-->
    <!-- jquery
		============================================ -->
    {!! Html::script("assets2/js/vendor/jquery-1.12.4.min.js")!!}
    <!-- bootstrap JS
		============================================ -->
    {!! Html::script("assets2/js/bootstrap.min.js")!!}
    <!-- wow JS
		============================================ -->
    {!! Html::script("assets2/js/wow.min.js")!!}
    <!-- price-slider JS
		============================================ -->
    {!! Html::script("assets2/js/jquery-price-slider.js")!!}
    <!-- owl.carousel JS
		============================================ -->
    {!! Html::script("assets2/js/owl.carousel.min.js")!!}
    <!-- scrollUp JS
		============================================ -->
    {!! Html::script("assets2/js/jquery.scrollUp.min.js")!!}
    <!-- meanmenu JS
		============================================ -->
    {!! Html::script("assets2/js/meanmenu/jquery.meanmenu.js")!!}
    <!-- counterup JS
		============================================ -->
    {!! Html::script("assets2/js/counterup/jquery.counterup.min.js")!!}
    {!! Html::script("assets2/js/counterup/waypoints.min.js")!!}
    {!! Html::script("assets2/js/counterup/counterup-active.js")!!}
    <!-- mCustomScrollbar JS
		============================================ -->
    {!! Html::script("assets2/js/scrollbar/jquery.mCustomScrollbar.concat.min.js")!!}
    <!-- jvectormap JS
		============================================ -->
    {!! Html::script("assets2/js/jvectormap/jquery-jvectormap-2.0.2.min.js")!!}
    {!! Html::script("assets2/js/jvectormap/jquery-jvectormap-world-mill-en.js")!!}
    {!! Html::script("assets2/js/jvectormap/jvectormap-active.js")!!}
    <!-- sparkline JS
		============================================ -->
    {!! Html::script("assets2/js/sparkline/jquery.sparkline.min.js")!!}
    {!! Html::script("assets2/js/sparkline/sparkline-active.js")!!}
    <!-- sparkline JS
		============================================ -->
    {!! Html::script("assets2/js/flot/jquery.flot.js")!!}
    {!! Html::script("assets2/js/flot/jquery.flot.resize.js")!!}
    {!! Html::script("assets2/js/flot/curvedLines.js")!!}
    {!! Html::script("assets2/js/flot/flot-active.js")!!}
    <!-- knob JS
		============================================ -->
    {!! Html::script("assets2/js/knob/jquery.knob.js")!!}
    {!! Html::script("assets2/js/knob/jquery.appear.js")!!}
    {!! Html::script("assets2/js/knob/knob-active.js")!!}
    <!--  wave JS
		============================================ -->
    {!! Html::script("assets2/js/wave/waves.min.js")!!}
    {!! Html::script("assets2/js/wave/wave-active.js")!!}
    <!--  todo JS
		============================================ -->
    {!! Html::script("assets2/js/todo/jquery.todo.js")!!}
    <!-- plugins JS
		============================================ -->
    {!! Html::script("assets2/js/plugins.js")!!}
    <!--  Chat JS
		============================================ -->
    {!! Html::script("assets2/js/chat/moment.min.js")!!}
    {!! Html::script("assets2/js/chat/jquery.chat.js")!!}
    <!-- main JS
		============================================ -->
    {!! Html::script("assets2/js/main.js")!!}
    <!-- tawk chat JS
		============================================ -->
    {!! Html::script("assets2/js/tawk-chat.js")!!}


<script>
$.ajaxSetup({
    headers: {
        'X-CSRF-Token': $('input[name="_token"]').val()
    }
});

// var _url=$('#_url').val();

// $('#bar-setting').click(function(e){
//     e.preventDefault();
//     $.post(_url+'/user/menu-open-status');
// });

// var width = $(window).width();
// if (width <= 768 ) {
//     $("body").removeClass('left-bar-open')
// }

</script>

{{--Custom JavaScript Start--}}

@yield('script')

{{--Custom JavaScript End Here--}}
</body>

</html>