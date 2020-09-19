@extends('admin')

{{--External Style Section--}}
@section('style')
    {!! Html::style("assets/libs/bootstrap3-wysihtml5-bower/bootstrap3-wysihtml5.min.css") !!}
@endsection


@section('content')

    <section class="wrapper-bottom-sec">
        <div class="p-30">
            <h2 class="page-title">{{language_data('Manage')}} {{language_data('Support Tickets')}}</h2>
        </div>
        <div class="p-30 p-t-none p-b-none">

            @include('notification.notify')
            <div class="row">
                <div class="col-lg-4">
                    <div class="panel">
                        <div class="panel-heading">
                            <h3 class="panel-title">{{language_data('Change')}} {{language_data('Basic Info')}}</h3>
                        </div>
                        <div class="panel-body">
                            <form class="" role="form" action="{{url('support-ticket/basic-info-post')}}" method="post">

                                <div class="form-group">
                                    <label for="e20">{{language_data('Change')}} {{language_data('Department')}}</label>
                                    <select name="department" class="form-control selectpicker">
                                        @foreach($department as $d)
                                        <option value="{{$d->id}}" @if($d->id==$td->id) selected @endif>{{$d->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="e20">{{language_data('Status')}}</label>
                                    <select name="status" class="form-control selectpicker">
                                        <option value="Pending" @if($st->status=='Pending') selected @endif>{{language_data('Pending')}}</option>
                                        <option value="Answered" @if($st->status=='Answered') selected @endif>{{language_data('Answered')}}</option>
                                        <option value="Customer Reply"  @if($st->status=='Customer Reply') selected @endif>{{language_data('Customer Reply')}}</option>
                                        <option value="Closed"  @if($st->status=='Closed') selected @endif>{{language_data('Closed')}}</option>
                                    </select>
                                </div>
                                <br>
                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <input type="hidden" value="{{$st->id}}" name="cmd">
                                <button type="submit" class="btn btn-success btn-sm pull-right"><i class="fa fa-edit"></i> {{language_data('Update')}}</button>

                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="panel">
                        <div class="panel-heading">
                            <h3 class="panel-title">{{language_data('Ticket Management')}}</h3>
                        </div>

                        <div class="p-30 p-t-none p-b-none">
                            <div class="row">
                                <div class="col-lg-12">
                                    <!-- Nav tabs -->
                                    <ul class="nav nav-tabs" role="tablist">
                                        <li role="presentation" class="active"><a href="#ticket_details" aria-controls="home" role="tab" data-toggle="tab">{{language_data('Ticket Details')}}</a></li>
                                        <li role="presentation"><a href="#ticket_discussion" aria-controls="profile" role="tab" data-toggle="tab">{{language_data('Ticket Discussion')}}</a></li>
                                        <li role="presentation"><a href="#ticket_files" aria-controls="messages" role="tab" data-toggle="tab">{{language_data('Ticket Files')}}</a></li>
                                    </ul>

                                    <!-- Tab panes -->
                                    <div class="tab-content p-20">


                                        {{--Personal Details--}}
                                        <div role="tabpanel" class="tab-pane active" id="ticket_details">

                                                <div class="clearfix ticket-de-pane">
                                                    <span class="ticket-status-title">{{language_data('Ticket For Client')}}:</span>
                                                    <span class="ticket-status-content">{{$st->name}}</span>
                                                </div>

                                                <div class="clearfix ticket-de-pane">
                                                    <span class="ticket-status-title">{{language_data('Email')}}:</span>
                                                    <span class="ticket-status-content">{{$st->email}}</span>
                                                </div>

                                                <div class="clearfix ticket-de-pane">
                                                    <span class="ticket-status-title">{{language_data('Created Date')}}:</span>
                                                    <span class="ticket-status-content">{{get_date_format($st->date)}}</span>
                                                </div>

                                                <div class="clearfix ticket-de-pane">
                                                    <span class="ticket-status-title">{{language_data('Created By')}}:</span>
                                                    @if($st->admin=='0')
                                                        <span class="ticket-status-content">{{$st->name}}</span>
                                                    @else
                                                        <span class="ticket-status-content">{{$st->admin}}</span>
                                                    @endif
                                                </div>

                                                <div class="clearfix ticket-de-pane">
                                                    <span class="ticket-status-title">{{language_data('Department')}}:</span>
                                                    <span class="ticket-status-content">{{$td->name}}</span>
                                                </div>

                                                <div class="clearfix ticket-de-pane">
                                                    <span class="ticket-status-title">Status:</span>
                                                    @if($st->status=='Pending')
                                                        <span class="label label-danger">{{language_data('Pending')}}</span>
                                                    @elseif($st->status=='Answered')
                                                        <span class="label label-success">{{language_data('Answered')}}</span>
                                                    @elseif($st->status=='Customer Reply')
                                                        <span class="label label-info">{{language_data('Customer Reply')}}</span>
                                                    @else
                                                        <span class="label label-primary">{{language_data('Closed')}}</span>
                                                    @endif
                                                </div>

                                                @if($st->status=='Closed')
                                                    <div class="clearfix ticket-de-pane">
                                                        <span class="ticket-status-title">{{language_data('Closed By')}}:</span>
                                                        <span class="ticket-status-content">{{$st->closed_by}}</span>
                                                    </div>
                                                @endif

                                                <div class="m-t-30"></div>

                                                <div class="clearfix">
                                                    <span class="ticket-status-title">{{language_data('Subject')}}:</span>
                                                    <span class="ticket-status-content">{{$st->subject}}</span>
                                                </div>
                                                <div class="clearfix">
                                                    <span class="ticket-status-title">{{language_data('Message')}}:</span>
                                                    <div class="ticket-status-content">{!!$st->message!!}</div>
                                                </div>



                                        </div>


                                        <div role="tabpanel" class="tab-pane" id="ticket_discussion">
                                            <form method="POST" action="{{ url('support-tickets/replay-ticket') }}">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}">

                                                <div class="form-group">
                                                    <label for="message">{{language_data('Message')}}</label>
                                                    <textarea class="textarea-wysihtml5 form-control"  name="message"></textarea>
                                                </div>


                                                <div class="hr-line-dashed"></div>
                                                <input type="hidden" value="{{$st->id}}" name="cmd">
                                                <button type="submit" name="add" class="btn btn-success"> {{language_data('Reply Ticket')}} <i class="fa fa-reply"></i></button>
                                            </form>
                                            <div class="m-t-30"></div>

                                            <div class="support-replies">
                                                @foreach($trply as $tr)
                                                    @if($tr->cl_id=='0')

                                                        <div class="single-support-reply clearfix admin">
                                                            <div class="reply-info">
                                                                @if($tr->image=='')
                                                                <img class="reply-user-thumb" src="<?php echo asset('assets/admin_pic/user.png'); ?>" height="80px" width="80px">

                                                                @else
                                                                    <img class="reply-user-thumb" src="<?php echo asset('assets/admin_pic/'.$tr->image); ?>" height="80px" width="80px">
                                                                @endif

                                                                <div class="reply-info-text">
                                                                    <h4 class="reply-user-name">{{$tr->admin}}</h4>
                                                                    <h5 class="reply-date"> - {{get_date_format($tr->date)}}</h5>
                                                                    <h5 class="reply-user-type"><span class="label label-success">{{language_data('Admin')}}</span></h5>
                                                                    <div class="reply-message">{!!$tr->message!!}</div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    @else

                                                        <div class="single-support-reply clearfix client">
                                                            <div class="reply-info">
                                                                @if($tr->image=='')
                                                                    <img class="reply-user-thumb" src="<?php echo asset('assets/client_pic/user.png'); ?>" height="80px" width="80px">
                                                                @else
                                                                    <img class="reply-user-thumb" src="<?php echo asset('assets/client_pic/'.$tr->image); ?>" height="80px" width="80px">
                                                                @endif
                                                                <div class="reply-info-text">
                                                                    <h4 class="reply-user-name">{{$tr->name}}</h4>
                                                                    <h5 class="reply-date">{{get_date_format($tr->date)}}</h5>
                                                                    <h5 class="reply-user-type"><span class="label label-success">{{language_data('Client')}}</span></h5>
                                                                    <div class="reply-message">{!!$tr->message!!}</div>
                                                                </div>
                                                            </div>
                                                        </div>

                                                    @endif

                                                @endforeach
                                            </div>
                                        </div>

                                        <div role="tabpanel" class="tab-pane" id="ticket_files">
                                            <form role="form" method="post" action="{{url('support-ticket/post-ticket-files')}}" enctype="multipart/form-data">

                                                <div class="row">
                                                    <div class="form-group">
                                                        <label>{{language_data('File Title')}}</label>
                                                        <input type="text" name="file_title" class="form-control">
                                                    </div>

                                                    <div class="form-group">
                                                        <label>{{language_data('Select File')}}</label>
                                                        <div class="input-group input-group-file">
                                                            <span class="input-group-btn">
                                                                <span class="btn btn-primary btn-file">
                                                                    {{language_data('Browse')}} <input type="file" class="form-control" name="file" accept="image/*">
                                                                </span>
                                                            </span>
                                                            <input type="text" class="form-control" readonly="">
                                                        </div>
                                                    </div>


                                                    <div class="col-md-12">
                                                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                                        <input type="hidden" value="{{$st->id}}" name="cmd">
                                                        <input type="submit" value="{{language_data('Upload')}}" class="btn btn-success pull-right">

                                                    </div>
                                                </div>

                                            </form>
                                            <br>
                                            <hr>

                                            <table class="table table-hover table-ultra-responsive">
                                                <thead>
                                                <tr>
                                                    <th style="width: 20%;">{{language_data('Files')}}</th>
                                                    <th style="width: 15%;">{{language_data('Size')}}</th>
                                                    <th style="width: 20%;">{{language_data('Date')}}</th>
                                                    <th style="width: 25%;">{{language_data('Upload By')}}</th>
                                                    <th style="width: 20%;">{{language_data('Action')}}</th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                @foreach($ticket_file as $tf)
                                                    <tr>
                                                        <td data-label="{{language_data('Files')}}"><p>{{$tf->file_title}}</p></td>
                                                        <td data-label="{{language_data('Size')}}"><p>{{round($tf->file_size/1000,2)}} KB</p></td>
                                                        <td data-label="{{language_data('Date')}}"><p>{{get_date_format($tf->updated_at)}}</p></td>

                                                        @if($tf->cl_id=='0')
                                                            <td data-label="{{language_data('Upload By')}}"> <p>{{admin_info($tf->admin_id)->fname}}</p></td>
                                                        @else
                                                            <td data-label="{{language_data('Upload By')}}"><p>{{client_info($tf->cl_id)->fname}}</p></td>
                                                        @endif
                                                        <td data-label="{{language_data('Action')}}">
                                                            <a href="{{url('support-ticket/download-file/'.$tf->id)}}" class="btn btn-success btn-xs"><i class="fa fa-download"></i> </a>
                                                            <a href="#" class="btn btn-danger btn-xs tFileDelete" id="{{$tf->id}}"><i class="fa fa-trash"></i></a>
                                                        </td>
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


                </div>
            </div>
        </div>
    </section>

@endsection

{{--External Style Section--}}
@section('script')
    {!! Html::script("assets/libs/wysihtml5x/wysihtml5x-toolbar.min.js")!!}
    {!! Html::script("assets/libs/handlebars/handlebars.runtime.min.js")!!}
    {!! Html::script("assets/libs/bootstrap3-wysihtml5-bower/bootstrap3-wysihtml5.min.js")!!}
    {!! Html::script("assets/js/form-elements-page.js")!!}
    {!! Html::script("assets/js/bootbox.min.js")!!}

    <script>
        $(document).ready(function () {

            /*For Delete Application Info*/
            $( "body" ).delegate( ".tFileDelete", "click",function (e) {
                e.preventDefault();
                var id = this.id;
              bootbox.confirm("{!! language_data('Are you sure') !!}?", function (result) {
                    if (result) {
                        var _url = $("#_url").val();
                        window.location.href = _url + "/support-ticket/delete-ticket-file/" + id;
                    }
                });
            });


        });
    </script>

@endsection
