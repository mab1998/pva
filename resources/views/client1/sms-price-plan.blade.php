@extends('client1')



@section('content')

    <section class="wrapper-bottom-sec">
        @include('notification.notify')
        <div class="dropdown-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="dropdown-list">
                        <div class="dropdown-trig-hd">
                            <h2>Dropdown Basic Previews (After triggering)</h2>
                            <p>Toggleable, contextual menu for displaying lists of links. Please refer the Colors page for all the available color options </p>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="dropdown-trig-list">

                                @foreach($price_plans as $pp)
                                 
                                
                                   
                                    <div class="dropdown-trig-sing sm-res-mg-t-30">
                                        <ul class="dropdown-menu nk-light-blue" role="menu">
                                            <li role="presentation"><a role="menuitem" tabindex="-1" href="#">{{$pp->plan_name}}</a></li>
                                            <li role="presentation"><a role="menuitem" tabindex="-1" href="#">{{$pp->price}}</a></li>

                                            @foreach($pp->features as $feature)
                                                <li role="presentation"><a role="menuitem" tabindex="-1" href="#">{{$feature->feature_value}}</a>
                                                </li>
                                            @endforeach
                                        <form class="form-some-up" role="form" action="{{url('user/sms/purchase-plan/success1/')}}" method="post">

                                            <div class="text-right">
                                                <input type="hidden" value="{{$pp->id}}" name="cmd">
                                                {{-- <button type="button" class="btn btn-warning btn-sm" data-dismiss="modal">{{language_data('Close',Auth::guard('client')->user()->lan_id)}}</button> --}}
                                                <button type="submit" class="btn btn-success btn-sm">Purchase Now</button>

                                            </div>
                                        </form>
                                            {{-- <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Another action</a>
                                            </li>
                                            <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Something else
													here</a></li>
                                            <li class="divider"></li>
                                            <li role="presentation"><a role="menuitem" tabindex="-1" href="#">Separated link</a> --}}
                                            </li>
                                        </ul>
                                    </div>



                        </div>
                    </div>
                </div>
            </div>
                                 @endforeach
                                  
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
            </div>

        </div>
    </div>
       


    </section>

@endsection

