@extends('client1')

@section('content')

    <section class="wrapper-bottom-sec">
        <div class="p-30">
            <h2 class="page-title">{{$sms_plan->plan_name}}</h2>
        </div>

@include('notification.notify')

                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                            <h4 class="modal-title" id="myModalLabel">{{language_data('Purchase SMS Plan',Auth::guard('client')->user()->lan_id)}}</h4>
                        </div>
                        <div class="modal-body">

                            <form class="form-some-up" role="form" action="{{url('user/sms/post-purchase-sms-plan1')}}" method="post">

                                <div class="form-group">
                                    <label>{{language_data('Select Payment Method',Auth::guard('client')->user()->lan_id)}}</label>
                                    <select class="selectpicker form-control" name="gateway">
                                        @foreach($payment_gateways as $pg)
                                            <option value="{{$pg->settings}}">{{$pg->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="text-right">
                                    <input type="hidden" value="{{$sms_plan->id}}" name="cmd">
                                    <button type="button" class="btn btn-warning btn-sm" data-dismiss="modal">{{language_data('Close',Auth::guard('client')->user()->lan_id)}}</button>
                                    <button type="submit" class="btn btn-success btn-sm">{{language_data('Purchase Now',Auth::guard('client')->user()->lan_id)}}</button>
                                </div>
                            </form>

                        </div>
                    </div>
            </div>

        </div>
    </section>

@endsection

{{--External Style Section--}}
@section('script')
    {!! Html::script("assets/libs/handlebars/handlebars.runtime.min.js")!!}
    {!! Html::script("assets/js/form-elements-page.js")!!}
@endsection