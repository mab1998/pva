@extends('admin')

{{--External Style Section--}}
@section('style')
    {!! Html::style("assets/libs/data-table/datatables.min.css") !!}
@endsection


@section('content')

    <section class="wrapper-bottom-sec">
        <div class="p-30">
            <h2 class="page-title">{{language_data('Administrator Roles')}}</h2>
        </div>
        <div class="p-30 p-t-none p-b-none">
            @include('notification.notify')
            <div class="row">

                <div class="col-lg-4">
                    <div class="panel">
                        <div class="panel-heading">
                            <h3 class="panel-title"> {{language_data('Add Administrator Role')}}</h3>
                        </div>
                        <div class="panel-body">
                            <form class="" role="form" method="post" action="{{url('administrators/add-role')}}">
                                <div class="form-group">
                                    <label>{{language_data('Role Name')}}</label>
                                    <input type="text" class="form-control" required name="role_name">
                                </div>

                                <div class="form-group">
                                    <label>{{language_data('Status')}}</label>
                                    <select class="selectpicker form-control" name="status">
                                        <option value="Active">{{language_data('Active')}}</option>
                                        <option value="Inactive">{{language_data('Inactive')}}</option>
                                    </select>
                                </div>

                                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                <button type="submit" class="btn btn-success btn-sm pull-right"><i class="fa fa-plus"></i> {{language_data('Add')}} </button>
                            </form>
                        </div>
                    </div>
                </div>

                <div class="col-lg-8">
                    <div class="panel">
                        <div class="panel-heading">
                            <h3 class="panel-title">{{language_data('Administrator Roles')}}</h3>
                        </div>
                        <div class="panel-body p-none">
                            <table class="table data-table table-hover">
                                <thead>
                                <tr>
                                    <th style="width: 10%;">{{language_data('SL')}}#</th>
                                    <th style="width: 35%;">{{language_data('Role Name')}}</th>
                                    <th style="width: 15%;">{{language_data('Status')}}</th>
                                    <th style="width: 40%;">{{language_data('Action')}}</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($admin_roles as $er)
                                    <tr>
                                        <td data-label="{{language_data('SL')}}">{{$loop->iteration}}</td>
                                        <td data-label="{{language_data('Role Name')}}"><p>{{$er->role_name}}</p></td>
                                        @if($er->status=='Active')
                                            <td data-label="{{language_data('Status')}}"><span class="label label-success">{{language_data('Active')}}</span></td>
                                        @else
                                            <td data-label="{{language_data('Status')}}"><span class="label label-danger">{{language_data('Inactive')}}</span></td>
                                        @endif
                                        <td data-label="{{language_data('Action')}}">

                                            <a class="btn btn-success btn-xs" href="#" data-toggle="modal" data-target=".modal_edit_administrator_roles_{{$er->id}}"><i class="fa fa-edit"></i> {{language_data('Edit')}}</a>
                                            @include('admin.modal-edit-administrator-roles')
                                            <a class="btn btn-complete btn-xs" href="{{url('administrators/set-role/'.$er->id)}}"><i class="fa fa-list"></i> {{language_data('Set Roles')}}</a>

                                            <a href="#" class="btn btn-danger btn-xs cdelete" id="{{$er->id}}"><i class="fa fa-trash"></i> {{language_data('Delete')}}</a>
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
        $(document).ready(function(){
            $('.data-table').DataTable({
              language: {
                url: '{!! url("assets/libs/data-table/i18n/".get_language_code()->language.".lang") !!}'
              },
              responsive: true
            });


            /*For Delete role*/
            $( "body" ).delegate( ".cdelete", "click",function (e) {
                e.preventDefault();
                var id = this.id;
                bootbox.confirm("{!! language_data('Are you sure') !!}?", function (result) {
                    if (result) {
                        var _url = $("#_url").val();
                        window.location.href = _url + "/administrators/delete-role/" + id;
                    }
                });
            });

        });
    </script>
@endsection
