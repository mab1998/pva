@extends('client1')

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
										<h2>Domain Search</h2>
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



        <div class="data-table-area">
        <div class="container">
        <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="form-example-wrap mg-t-30">
                        <div class="cmp-tb-hd cmp-int-hd">
                            <h2>Searche</h2>
                        </div>
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                            <form action="/domain" method="post">

                                <div class="form-example-int form-example-st">
                                    <div class="form-group">
                                        <div class="nk-int-st">
                                            <input type="text" name="domain" class="form-control input-sm" @if(isset($domain)) value={{$domain}} @endif placeholder="Searche Domain">
                                        </div>
                                    </div>
                                </div>
                            </div>
                           
                            
                            <div class="col-lg-4 col-md-4 col-sm-3 col-xs-12">
                                <div class="form-example-int">
                                    <button class="btn btn-success notika-btn-success waves-effect">Searche</button>
                                </div>
                            </div>

                            </form>

                        </div>
                    </div>
                </div>
            </div>
@isset($results)

            <div class="row">

            
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="data-table-list">
                        <div class="basic-tb-hd">
                            <h2>Domain searche </h2>
                            <p>It's just that simple. Turn your simple table into a sophisticated data table and offer your users a nice experience and great features without any effort.</p>
                        </div>
                        <div class="table-responsive">
                            <table id="data-table-basic" class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>domainName</th>
                                        <th>purchasable</th>
                                        <th>purchasePrice</th>
                                        <th>renewalPrice</th>
                                        <th>Add to cart</th>
                                        {{-- <th>Salary</th> --}}
                                    </tr>
                                </thead>
                                <tbody>
                                @if(isset($results))

                                @foreach($results as $result)
                                {{-- <form action="/domain-add-to-card/{{$result->domainName}}" method="post"> --}}
                                    <tr>
                                        <td>{{$result->domainName}}</td>
                                        @if(isset($result->purchasable))
                                        <td>{{$result->purchasable}}</td>
                                        @else
                                        <td>//</td>
                                        @endif

                                        @if(isset($result->purchasePrice))
                                        <td>{{$result->purchasePrice}}</td>
                                        @else
                                        <td>//</td>
                                        @endif
                                        @if(isset($result->renewalPrice))
                                        <td>{{$result->renewalPrice}}</td>
                                        @else
                                        <td>//</td>
                                        @endif

                                        <td>
                                        {{check_if_exist($result->domainName)}}
                                        @if(!check_if_exist($result->domainName))
                                        {{-- <div class="form-example-int"> --}}
                                        <form action="/domain-add-to-card/{{$result->domainName}}">
                                            <input type="submit" value="Add To cart" />
                                        </form>
                                        {{-- <button href="/domain-add-to-card/{{$result->domainName}}" class="btn btn-success notika-btn-success waves-effect">Add To cart</button> --}}
                                        {{-- </div> --}}
                                        @else
                                            <button class="btn btn-success ">Added</button>
                                            <button class="btn btn-success " href="/remove-domain-from-card/{{$result->domainName}}">Remove</button>

                                        @endif

                                        </td>

                                @endforeach

                                @endif

                                        
                                        {{-- <td>$320,800</td> --}}
                                    </tr>

                                {{-- </form> --}}
                                
                                </tbody>
                                </tbody>
                              
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            @endisset

        </div>
    </div>
@endsection