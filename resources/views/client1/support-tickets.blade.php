@extends('client1')

@section('content')
    <section class="wrapper-bottom-sec">

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
                                    <th>Email</th>
                                    <th>Subject</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Action</th>

                                </tr>
                            </thead>
                            <tbody>
                                @foreach($st as $in)
                                <tr>
                                    <td>{{$loop->iteration}}</td>
                                    <td>{{$in->email}}</td>
                                    <td>{{$in->subject}}</td>
                                    <td>{{get_date_format($in->date)}}</td>
                                    <td>
                                        @if($in->status=='Pending')
                                                <span class="label label-danger">{{language_data('Pending',Auth::guard('client')->user()->lan_id)}}</span>
                                            @elseif($in->status=='Answered')
                                                <span class="label label-success">{{language_data('Answered',Auth::guard('client')->user()->lan_id)}}</span>
                                            @elseif($in->status=='Customer Reply')
                                                <span class="label label-info">{{language_data('Customer Reply',Auth::guard('client')->user()->lan_id)}}</span>
                                            @else
                                                <span class="label label-primary">{{language_data('Closed',Auth::guard('client')->user()->lan_id)}}</span>
                                            @endif
                                    </td>
                                    <td>    <a href="{{url('user/tickets/view-ticket1/'.$in->id)}}" class="btn btn-success btn-xs"><i class="fa fa-eye"></i> {{language_data('View',Auth::guard('client')->user()->lan_id)}}</a> </td>
                                    
                                </tr>
                                @endforeach

                            </tbody>
                            
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    </section>

@endsection