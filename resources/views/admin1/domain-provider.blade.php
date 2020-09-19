@extends('admin1')

@section('content')
	<div class="breadcomb-area">
		<div class="container">
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="breadcomb-list">
						<div class="row">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
								<div class="breadcomb-wp">
									<div class="breadcomb-icon">
										<i class="notika-icon notika-windows"></i>
									</div>
									<div class="breadcomb-ctn">
										<h2>Regestrar Domain</h2>
										<p>Welcome to Notika <span class="bread-ntd">Admin Template</span></p>
									</div>
								</div>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-3">
								<div class="breadcomb-report">
									<button data-toggle="tooltip" data-placement="left" title="Download Report" class="btn"><i class="notika-icon notika-sent"></i></button>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>





<div class="data-table-area">
        <div class="container">
                    

            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="data-table-list">
                        <div class="basic-tb-hd">
                        @include('notification.notify')
                            <h2>Basic Example</h2>
                            <p>It's just that simple. Turn your simple table into a sophisticated data table and offer your users a nice experience and great features without any effort.</p>
                        </div>
                        <div class="table-responsive">
                            <table id="data-table-basic" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>username</th>
                                        <th>id user</th>
                                        <th>api key</th>
                                        <th>stutus</th>
                                        <th>Salary</th>
                                        <th>update</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @foreach($Providers as $Provider)
                                    <tr>
                                    <form action="/update-registrar" method="post">

                                        <td>{{$Provider->name}}
                                          {{-- <label for="fname">First name:</label><br> --}}

                                        
                                        </td>
                                        <td>
                                          <input type="text"  name="username" value="{{$Provider->username}}">
                                        
                                        </td>
                                        <td>
                                        <input type="text"  name="user_id" value="{{$Provider->user_id}}">

                                        </td>
                                        <td>
                                        <input type="text"  name="api_key" value="{{$Provider->api_key}}">

                                        </td>
                                        
                                        <td>
                                        @if($Provider->status=='active')
                                          <input type="radio" checked=true id="active" name="status" value="male">
                                            <label for="active">Active</label><br>
                                              <input type="radio" id="disactive" name="status" value="disactive">
                                    <label for="disactive">Disable</label><br>
                                    @else
                                    <input type="radio" id="active" name="status" value="active">
                                            <label for="active">Active</label><br>
                                              <input type="radio" checked=true id="disactive" name="status" value="disactive">
                                    <label for="disactive">Disable</label><br>
                                    @endif
                                        </td>

                                        <td>{{$Provider->status}}</td>

                                        <td>
                                          <input type="submit" value="Update">

</td>
  <input type="text" type="hidden" name="pid" value="{{$Provider->id}}">


                                        </form>

                                    </tr>
                                @endforeach
                               
                                </tbody>
                                {{-- <tfoot>
                                    <tr>
                                        <th>Name</th>
                                        <th>Position</th>
                                        <th>Office</th>
                                        <th>Age</th>
                                        <th>Start date</th>
                                        <th>Salary</th>
                                    </tr>
                                </tfoot> --}}
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection