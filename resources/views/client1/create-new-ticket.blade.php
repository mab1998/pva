@extends('client1')




@section('content')
    <section class="wrapper-bottom-sec">


<div class="breadcomb-area">
		<div class="container">
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="breadcomb-list">
						<div class="row">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
								<div class="breadcomb-wp">
									<div class="breadcomb-icon">
										<i class="notika-icon notika-form"></i>
									</div>
									<div class="breadcomb-ctn">
										<h2>Create ticket</h2>
										<p>Create ticket<span class="bread-ntd">Create ticket</span></p>
									</div>
								</div>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-3">
								<div class="breadcomb-report">
									<button data-toggle="tooltip" data-placement="left" title="" class="btn waves-effect" data-original-title="Download Report"><i class="notika-icon notika-sent"></i></button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>



            @include('notification.notify')
            {{-- <div class="row"> --}}
<div class="form-element-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="form-element-list">
                       
                   
                            <div class="row">
                                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">

                                    <div class="form-element-list mg-t-30">


             
                            <form method="POST" action="{{ url('user/tickets/post-ticket1') }}">
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                                <div class="form-group">
                                <div class="basic-tb-hd">
                                    <h2>Subject</h2>
                                    {{-- <p>Form control which supports multiple lines of text. Change 'rows' attribute as necessary.</p> --}}
                                </div>
                                    {{-- <label for="subject">{{language_data('Subject',Auth::guard('client')->user()->lan_id)}}</label> --}}
                                    {{-- <input type="text" class="form-control" id="subject" name="subject"> --}}
                                    <textarea  id="subject" name="subject" class="form-control auto-size" rows="2" placeholder="Subject..." style="overflow: hidden; overflow-wrap: break-word; height: 41px;"></textarea>
                                </div>

                                <div class="basic-tb-hd">
                                    <h2>Message</h2>
                                    {{-- <p>Form control which supports multiple lines of text. Change 'rows' attribute as necessary.</p> --}}
                                </div>

                                <div class="form-group">
                                    <div class="nk-int-st">
                                        <textarea class="form-control" rows="5" name="message" placeholder="Type your ticket message...."></textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="nk-int-mk sl-dp-mn">
                                    <h2>Departement</h2>
                                </div>
                                    {{-- <label for="did">{{language_data('Department',Auth::guard('client')->user()->lan_id)}}</label> --}}
                                    <select name="did" class="selectpicker form-control" data-live-search="true">
                                        @foreach($sd as $d)
                                            <option value="{{$d->id}}">{{$d->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <button type="submit" name="add" class="btn btn-primary btn-sm hec-button waves-effect"><i class="fa fa-plus"></i> {{language_data('Create Ticket',Auth::guard('client')->user()->lan_id)}}</button>
                            </form>


                        </div>
                    </div>
                </div>

            {{-- </div> --}}

        </div>

                                    
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
    {!! Html::script("assets/libs/wysihtml5x/wysihtml5x-toolbar.min.js")!!}
    {!! Html::script("assets/libs/bootstrap3-wysihtml5-bower/bootstrap3-wysihtml5.min.js")!!}
@endsection
