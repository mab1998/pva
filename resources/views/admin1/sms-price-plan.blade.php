@extends('admin1')

{{--External Style Section--}}
{{-- @section('style')
    {!! Html::style("assets/libs/data-table/datatables.min.css") !!}
@endsection --}}


@section('content')

    {{-- <section class="wrapper-bottom-sec"> --}}

    	<div class="breadcomb-area">
		<div class="container">
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="breadcomb-list">
						<div class="row">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
								<div class="breadcomb-wp">
									<div class="breadcomb-icon">
										<i class="notika-icon notika-app"></i>
									</div>
									<div class="breadcomb-ctn">
										<h2>PRICE PLAN</h2>
										{{-- <p>Welcome to Notika <span class="bread-ntd">Admin Template</span></p> --}}
									</div>
								</div>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-3">
								{{-- <div class="breadcomb-report">
									<button data-toggle="tooltip" data-placement="left" title="Download Report" class="btn"><i class="notika-icon notika-sent"></i></button>
								</div> --}}
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

    <div class="dialog-area">
        <div class="container">


        
            @foreach($price_plans as $pp)
                <div class="row">
                    <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                        <div class="dialog-inner">
                            <div class="contact-hd dialog-hd">
                                <h2>{{$pp->plan_name}}</h2>
                                <h2>{{us_money_format($pp->price)}}</h2>
                                @foreach($pp->features as $feature)
                                <p>{{$feature->feature_value}}</p>
                                @endforeach
                                {{-- <p>{{$pp->features}}</p> --}}
                            </div>
                            <div class="dialog-pro dialog">
                                <button class="btn btn-info" id="sa-basic"  href="{{url('sms/add-plan-feature1/'.$pp->id)}}">Add Feature</button>
                            </div>
                            <div class="dialog-pro dialog">
                                <button class="btn btn-info" id="sa-basic" href="{{url('sms/manage-price-plan1/'.$pp->id)}}">Manage</button>
                            </div>
                            <div class="dialog-pro dialog">
                                <button class="btn btn-info"  id="{{$pp->id}}" href="{{url('sms/delete-price-plan1/'.$pp->id)}}">Delete</button>
                            </div>
                        </div>
                    </div>
                  
                </div>

            @endforeach

        </div>
    </div>

        {{-- <div class="p-30">
            <h2 class="page-title">{{language_data('SMS Price Plan')}}</h2>
        </div>
        <div class="p-30 p-t-none p-b-none">
            @include('notification.notify')
            <div class="row">

                <div class="col-lg-12">
                    <div class="panel">
                        <div class="panel-heading">
                            <h3 class="panel-title">{{language_data('SMS Price Plan')}}</h3>
                        </div>
                        <div class="panel-body p-none">
                            <table class="table data-table table-hover">
                                <thead>
                                <tr>
                                    <th style="width: 5%;">{{language_data('SL')}}#</th>
                                    <th style="width: 30%;">{{language_data('Plan Name')}}</th>
                                    <th style="width: 10%;">{{language_data('Price')}}</th>
                                    <th style="width: 10%;">{{language_data('Show')}}</th>
                                    <th style="width: 10%;">{{language_data('Popular')}}</th>
                                    <th style="width: 35%;">{{language_data('Action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($price_plans as $pp)
                                    <tr>
                                        <td data-label="SL">{{ $loop->iteration }}</td>
                                        <td data-label="Plan Name"><p>{{$pp->plan_name}}</p></td>
                                        <td data-label="Price"><p>{{us_money_format($pp->price)}}</p></td>
                                        @if($pp->status=='Active')
                                            <td data-label="Status"><p class="label label-success label-xs">{{language_data('Show')}}</p></td>
                                        @else
                                            <td data-label="Status"><p class="label label-warning label-xs">{{language_data('Hide')}}</p></td>
                                        @endif
                                        @if($pp->popular=='Yes')
                                            <td data-label="Popular"><p class="label label-success label-xs">{{language_data('Yes')}}</p></td>
                                        @else
                                            <td data-label="Popular"><p class="label label-primary label-xs">{{language_data('No')}}</p></td>
                                        @endif
                                        <td data-label="Actions">
                                            <a class="btn btn-success btn-xs" href="{{url('sms/add-plan-feature/'.$pp->id)}}" ><i class="fa fa-plus"></i> {{language_data('Add Feature')}}</a>
                                            <a class="btn btn-primary btn-xs" href="{{url('sms/view-plan-feature/'.$pp->id)}}" ><i class="fa fa-eye"></i> {{language_data('View Features')}}</a>
                                            <a class="btn btn-complete btn-xs" href="{{url('sms/manage-price-plan/'.$pp->id)}}" ><i class="fa fa-edit"></i> {{language_data('Manage')}}</a>
                                            <a href="#" class="btn btn-danger btn-xs cdelete" id="{{$pp->id}}"><i class="fa fa-trash"></i> {{language_data('Delete')}}</a>
                                        </td>
                                    </tr>
                                @endforeach

                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>

        </div> --}}
    {{-- </section> --}}

@endsection

{{--External Style Section--}}
{{-- @section('script')
    {!! Html::script("assets/libs/handlebars/handlebars.runtime.min.js")!!}
    {!! Html::script("assets/js/form-elements-page.js")!!}
    {!! Html::script("assets/libs/data-table/datatables.min.js")!!}
    {!! Html::script("assets/js/bootbox.min.js")!!}

    <script>
        $(document).ready(function(){

          $('.data-table').DataTable({
            language: {
              url: '{!! url("assets/libs/data-table/i18n/".get_language_code()->language.".lang") !!}'
            },
            responsive: true
          });

            /*For Delete Price Plan*/
            $( "body" ).delegate( ".cdelete", "click",function (e) {
                e.preventDefault();
                var id = this.id;
              bootbox.confirm("{!! language_data('Are you sure') !!}?", function (result) {
                    if (result) {
                        var _url = $("#_url").val();
                        window.location.href = _url + "/sms/delete-price-plan/" + id;
                    }
                });
            });

        });
    </script>
@endsection --}}