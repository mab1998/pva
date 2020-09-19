@extends('admin')

{{--External Style Section--}}
@section('style')
    {!! Html::style("assets/libs/data-table/datatables.min.css") !!}
@endsection


@section('content')

    <section class="wrapper-bottom-sec">
        <div class="p-30">
            <h2 class="page-title">{{language_data('Support Tickets')}}</h2>
        </div>
        <div class="p-30 p-t-none p-b-none">
            @include('notification.notify')

            <div class="row">

                <div class="col-lg-12">
                    <div class="panel">
                        <div class="panel-heading">
                            <h3 class="panel-title">{{language_data('Support Tickets')}}</h3>
                        </div>
                        <div class="panel-body p-none">
                            <table class="table data-table table-hover">
                                <thead>
                                <tr>
                                    <th style="width: 5%;">#</th>
                                    <th style="width: 15%;">{{language_data('Client Name')}}</th>
                                    <th style="width: 15%;">{{language_data('Email')}}</th>
                                    <th style="width: 20%;">{{language_data('Subject')}}</th>
                                    <th style="width: 10%;">{{language_data('Date')}}</th>
                                    <th style="width: 10%;">{{language_data('Status')}}</th>
                                    <th style="width: 25%;">{{language_data('Action')}}</th>
                                </tr>
                                </thead>
                                <tbody>

                                @foreach($st as $in)
                                    <tr>
                                        <td>{{$loop->iteration}} </td>
                                        <td>{{$in->name}}</td>
                                        <td>{{$in->email}}</td>
                                        <td>{{$in->subject}}</td>
                                        <td>{{get_date_format($in->date)}}</td>
                                        <td>
                                            @if($in->status=='Pending')
                                                <span class="label label-danger">{{language_data('Pending')}}</span>
                                            @elseif($in->status=='Answered')
                                                <span class="label label-success">{{language_data('Answered')}}</span>
                                            @elseif($in->status=='Customer Reply')
                                                <span class="label label-info">{{language_data('Customer Reply')}}</span>
                                            @else
                                                <span class="label label-primary">{{language_data('Closed')}}</span>
                                            @endif
                                        </td>

                                        <td>
                                            <a href="{{url('support-tickets/view-ticket/'.$in->id)}}" class="btn btn-success btn-xs"><i class="fa fa-edit"></i> {{language_data('Manage')}}</a>
                                            <a href="#" class="btn btn-danger btn-xs cdelete" id="{{$in->id}}"><i class="fa fa-trash"></i> {{language_data('Delete')}}</a>
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
    </section>

@endsection

{{--External Style Section--}}
@section('script')
    {!! Html::script("assets/libs/handlebars/handlebars.runtime.min.js")!!}
    {!! Html::script("assets/js/form-elements-page.js")!!}
    {!! Html::script("assets/libs/data-table/datatables.min.js")!!}
    {!! Html::script("assets/js/bootbox.min.js")!!}
    <script>
        $(document).ready(function () {

            /*For DataTable*/
          $('.data-table').DataTable({
            language: {
              url: '{!! url("assets/libs/data-table/i18n/".get_language_code()->language.".lang") !!}'
            },
            responsive: true
          });

            /*For Delete Support Tickets*/

            $( "body" ).delegate( ".cdelete", "click",function (e) {
                e.preventDefault();
                var id = this.id;
              bootbox.confirm("{!! language_data('Are you sure') !!}?", function (result) {
                    if(result){
                        var _url = $("#_url").val();
                        window.location.href = _url + "/support-tickets/delete-ticket/" + id;
                    }
                });
            });

        });
    </script>


@endsection
