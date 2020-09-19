<!-- Data Table area Start-->
<div class="data-table-area">
    <div class="container">
        <div class="row">
            <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
                <div class="data-table-list">
                    <div class="basic-tb-hd">
                        <h2>Basic Example</h2>
                        <p>It's just that simple. Turn your simple table into a sophisticated data table and offer your users a nice experience and great features without any effort.</p>
                    </div>
                    <div class="table-responsive">
                        <table id="data-table-basic" class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Amount</th>
                                    <th>Invoice Date</th>
                                    <th>Due Dat</th>
                                    <th>Status</th>
                                    <th>Type</th>
                                    <th>Manage</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach($invoices as $in)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{app_config('CurrencyCode')}} {{$in->total}}</td>
                                    <td>{{get_date_format($in->created)}}</td>
                                    <td>{{get_date_format($in->duedate)}}</td>
                                    <td>
                                        @if($in->status=='Unpaid')
                                        <span class="label label-warning">{{language_data('Unpaid',Auth::guard('client')->user()->lan_id)}}</span> @elseif($in->status=='Paid')
                                        <span class="label label-success">{{language_data('Paid',Auth::guard('client')->user()->lan_id)}}</span> @elseif($in->status=='Cancelled')
                                        <span class="label label-danger">{{language_data('Cancelled',Auth::guard('client')->user()->lan_id)}}</span> @else
                                        <span class="label label-info">{{language_data('Partially Paid',Auth::guard('client')->user()->lan_id)}}</span> @endif
                                    </td>
                                    <td>
                                        @if($in->recurring=='0')
                                        <span class="label label-success"> {{language_data('Onetime',Auth::guard('client')->user()->lan_id)}}</span> @else
                                        <span class="label label-info"> {{language_data('Recurring',Auth::guard('client')->user()->lan_id)}}</span> @endif
                                    </td>
                                    <td>
                                        <a href="{{url('user/invoices/view1/'.$in->id)}}" class="btn btn-success btn-xs"><i class="fa fa-eye"></i> {{language_data('View',Auth::guard('client')->user()->lan_id)}}</a>
                                    </td>
                                </tr>
                                @endforeach

                            </tbody>
                            <tfoot>
                                <tr>
                                    <th>Name</th>
                                    <th>Position</th>
                                    <th>Office</th>
                                    <th>Age</th>
                                    <th>Start date</th>
                                    <th>Salary</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>