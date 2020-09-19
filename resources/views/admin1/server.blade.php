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
										<h2>Server Manager</h2>
										{{-- <p>Welcome to Notika <span class="bread-ntd">Admin Template</span></p> --}}
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


<div class="normal-table-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="normal-table-list">
                        <div class="basic-tb-hd">
                            <h2>Basic Table</h2>
                            <p>Basic example without any additional modification classes</p>
                        </div>
                        
                        <div class="bsc-tbl">
                        <form action='/server-create'>
                            <button type="submit" class="btn btn-secondary">Create server</button>
                        </form>

                            <table class="table table-sc-ex">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>name</th>
                                        <th>IP</th>
                                        <th>Username</th>
                                        <th>pass</th>
                                        <th>status</th>
                                        <th>Axtions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @if(isset($servers))
                                @foreach($servers as $server)
                                    <tr>
                                        <td>{{$server->id}}</td>
                                        <td>{{$server->name}}</td>
                                        <td>{{$server->ip}}:{{$server->port}}</td>
                                        <td>{{$server->username}}</td>
                                        <td>{{$server->password}}</td>
                                        <td>{{$server->status}}</td>
                                        <td>
                                <i class="fas fa-trash-alt"> </i>
                                                                <i class="fab fa-expeditedssl ssl-click"> </i>


                                        </td>

                                    </tr>
                                @endforeach
                                @endif
                                  
                              
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
         
           
        </div>
    </div>
    </div>


@endsection