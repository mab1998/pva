@extends('client1')

@section('content')

    <section class="wrapper-bottom-sec">
    <!-- Main Menu area End-->
	<!-- Breadcomb area Start-->

    @include('notification.notify')

    <div class="invoice-print">
        @if($inv->status=='Unpaid' || $inv->status=='Partially Paid')

        <a href="#" class="btn waves-effect" data-ma-action="Pay Now"><i class="fa fa-check"></i></a>
    @endif
        {{-- <a href="#" class="btn waves-effect" data-ma-action="print"><i class="notika-icon notika-print"></i></a> --}}
    </div>


	<div class="breadcomb-area">
		<div class="container">
			<div class="row">
				<div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
					<div class="breadcomb-list">
						<div class="row">
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
								<div class="breadcomb-wp">
									<div class="breadcomb-icon">
										<i class="notika-icon notika-support"></i>
									</div>
									<div class="breadcomb-ctn">
										<h2>Invoice</h2>
										<p>Welcome to Notika <span class="bread-ntd">Admin Template</span></p>
									</div>
								</div>
							</div>
							<div class="col-lg-6 col-md-6 col-sm-6 col-xs-3">
								<div class="breadcomb-report">
                                    @if($inv->status=='Unpaid' || $inv->status=='Partially Paid')
									<button data-toggle="modal" data-target="#pay-invoice" data-placement="left" title="Pay Now" class="btn"><i class="fa fa-check"></i></button>
                                    @endif

                                    <button href="{{url('user/invoices/client-iview1/'.$inv->id)}}" target="_blank" data-placement="left" title="Preview" class="btn"><i class="fa fa-paper-plane-o"></i></button>
                                    <button href="{{url('user/invoices/client-iview1/'.$inv->id)}}" target="_blank" data-placement="left" title="Download PDF" class="btn"><i class="fa fa-file-pdf-o"></i></button>
                                    <button href="{{url('user/invoices/iprint1/'.$inv->id)}}" target="_blank" data-placement="left" title="Print" class="btn"><i class="fa fa-print"></i></button>

                                </div>
                                
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<!-- Breadcomb area End-->
    <!-- Invoice area Start-->





    <div class="invoice-area">
        <div class="container">
            <div class="row">
                <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                    <div class="invoice-wrap">
                        <div class="invoice-img">
                            <img src="img/logo/logo.png" alt="" />
                        </div>
                        <div class="invoice-hds-pro">
                            <div class="row">
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="invoice-cmp-ds ivc-frm">
                                        <div class="invoice-frm">
                                            <span>Invoice from</span>
                                        </div>
                                        <div class="comp-tl">
                                            <h2>David Designs LLC</h2>
                                            <p>44, Qube Towers uttara Media City, Dubai, Bangladesh</p>
                                        </div>
                                        <div class="cmp-ph-em">
                                            <span>01962067309</span>
                                            <span>David@notika.com</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 col-xs-12">
                                    <div class="invoice-cmp-ds ivc-to">
                                        <div class="invoice-frm">
                                            <span>Invoice to</span>
                                        </div>
                                        <div class="comp-tl">
                                            <h2>{{$inv->client_name}}</h2>
                                            <p>{{$client->address1}}</p>
                                            <p>{{$client->address2}}</p>
                                            <p>{{$client->state}}, {{$client->city}} - {{$client->postcode}},  {{$client->country}}</p>

                                        </div>
                                        <div class="cmp-ph-em">
                                            <span>{{$client->phone}}</span>
                                            <span>{{$client->email}}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="invoice-hs">
                                    <span>Invoice#</span>
                                    <h2>{{$inv->id}}</h2>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="invoice-hs date-inv sm-res-mg-t-30 tb-res-mg-t-30 tb-res-mg-t-0">
                                    <span>Invoice Status</span>

                                        @if($inv->status=='Unpaid')
                                              <h2>Unpaid</h2>
                                        @elseif($inv->status=='Paid')
                                        <h2>Paid</h2>
                                        <h2>{{get_date_format($inv->datepaid)}}</h2>
                                        @elseif($inv->status=='Partially Paid')
                                        <h2>Partially Paid</h2>
                                        @else
                                        <h2>Cancelled</h2>
                                        @endif
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="invoice-hs wt-inv sm-res-mg-t-30 tb-res-mg-t-30 tb-res-mg-t-0">
                                    <span>Created</span>
                                    <h2>{{get_date_format($inv->created)}}</h2>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="invoice-hs gdt-inv sm-res-mg-t-30 tb-res-mg-t-30 tb-res-mg-t-0">
                                    <span>due date</span>
                                    <h2>{{get_date_format($inv->duedate)}}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="invoice-sp">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>#</th>
                                                <th>Unit Price</th>
                                                <th>Item Title</th>
                                                
                                                <th>Quantity</th>
                                                <th>Total</th>
                                                <th>Subtotal</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                             @foreach($inv_items as $it)
                                            <tr>
                                                <td data-label="{{language_data('Item',Auth::guard('client')->user()->lan_id)}}">{{$loop->iteration}}</td>
                                                <td data-label="{{language_data('Price',Auth::guard('client')->user()->lan_id)}}">{{$it->item}}</td>
                                                <td data-label="{{language_data('Quantity',Auth::guard('client')->user()->lan_id)}}">{{app_config('CurrencyCode')}} {{$it->price}}</td>
                                                <td data-label="{{language_data('Total',Auth::guard('client')->user()->lan_id)}}">{{$it->qty}}</td>
                                                <td data-label="{{language_data('Subtotal',Auth::guard('client')->user()->lan_id)}}">{{app_config('CurrencyCode')}} {{$it->subtotal}}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    <div class="row">
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="invoice-hs">
                                    <span>Subtotal</span>
                                    <h2>{{$inv->subtotal}}</h2>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="invoice-hs date-inv sm-res-mg-t-30 tb-res-mg-t-30 tb-res-mg-t-0">
                                    <span>Tax Sum</span>

                                        
                                        <h2>{{$tax_sum}}</h2>
                                        

                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="invoice-hs wt-inv sm-res-mg-t-30 tb-res-mg-t-30 tb-res-mg-t-0">
                                    <span>Discount</span>
                                    <h2>{{$dis_sum}}</h2>
                                    
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-3 col-sm-3 col-xs-12">
                                <div class="invoice-hs gdt-inv sm-res-mg-t-30 tb-res-mg-t-30 tb-res-mg-t-0">
                                    <span>Grand Total</span>
                                    <h2> {{$inv->total}}</h2>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                                <div class="invoice-ds-int">
                                    <h2>Invoice Note</h2>
                                    <p>{{$inv->note}}</p>
                                </div>

                            
                                <div class="invoice-ds-int">
                                    <h2>Remarks</h2>
                                    <p>Ornare non tortor. Nam quis ipsum vitae dolor porttitor interdum. Curabitur faucibus erat vel ante fermentum lacinia. Integer porttitor laoreet suscipit. Sed cursus cursus massa ut pellentesque. Phasellus vehicula dictum arcu, eu interdum massa bibendum. Ornare non tortor. Nam quis ipsum vitae dolor porttitor interdum. Curabitur faucibus erat vel ante fermentum lacinia. Integer porttitor laoreet suscipit. Sed cursus cursus massa ut pellentesque. Phasellus vehicula dictum arcu, eu interdum massa bibendum. </p>
                                </div>
                                <div class="invoice-ds-int invoice-last">
                                    <h2>Notika For Your Business</h2>
                                    <p class="tab-mg-b-0">Ornare non tortor. Nam quis ipsum vitae dolor porttitor interdum. Curabitur faucibus erat vel ante fermentum lacinia. Integer porttitor laoreet suscipit. Sed cursus cursus massa ut pellentesque. Phasellus vehicula dictum arcu, eu interdum massa bibendum. Ornare non tortor. Nam quis ipsum vitae dolor porttitor interdum. Curabitur faucibus erat vel ante fermentum lacinia. Integer porttitor laoreet suscipit. Sed cursus cursus massa ut pellentesque. Phasellus vehicula dictum arcu, eu interdum massa bibendum. </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- Invoice area End-->
        <div class="col-lg-6 col-md-3 col-sm-3 col-xs-12">


                                    <div class="btn-group pull-right" aria-label="...">



                                        <div class="modal fade" id="pay-invoice" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                            <div class="modal-dialog" role="document">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                                                        <h4 class="modal-title" id="myModalLabel">{{language_data('Pay Invoice',Auth::guard('client')->user()->lan_id)}}</h4>
                                                    </div>
                                                    <div class="modal-body">

                                                        <form class="form-some-up" role="form" action="{{url('user/invoices/pay-invoice1')}}" method="post">

                                                            <div class="form-group">
                                                                <label>{{language_data('Select Payment Method',Auth::guard('client')->user()->lan_id)}}</label>
                                                                <select class="selectpicker form-control" name="gateway">
                                                                    @foreach($payment_gateways as $pg)
                                                                        <option value="{{$pg->settings}}">{{$pg->name}}</option>
                                                                    @endforeach
                                                                </select>
                                                            </div>

                                                            <div class="text-right">
                                                                <input type="hidden" value="{{$inv->id}}" name="cmd">
                                                                <button type="button" class="btn btn-warning btn-sm" data-dismiss="modal">{{language_data('Close',Auth::guard('client')->user()->lan_id)}}</button>
                                                                <button type="submit" class="btn btn-success btn-sm">{{language_data('Pay',Auth::guard('client')->user()->lan_id)}}</button>
                                                            </div>
                                                        </form>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>




    @endsection
