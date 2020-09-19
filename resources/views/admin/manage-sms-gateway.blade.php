@extends('admin')

@section('content')

    <section class="wrapper-bottom-sec">
        <div class="p-30">
            <h2 class="page-title">{{language_data('SMS Gateway Manage')}}</h2>
        </div>
        <div class="p-30 p-t-none p-b-none">
            @include('notification.notify')
            <div class="row">
                <div class="col-lg-12">
                    <form class="" role="form" method="post" action="{{url('sms/post-manage-sms-gateway')}}">

                        <div class="row">
                            <div class="col-lg-3">
                                <div class="panel">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">{{language_data('Basic Information')}}</h3>
                                    </div>
                                    <div class="panel-body">
                                        @if($gateway->custom=='Yes')
                                            <div class="form-group">
                                                <label>{{language_data('Gateway Name')}}</label>
                                                <input type="text" class="form-control" required name="gateway_name" value="{{$gateway->name}}">
                                            </div>
                                        @else
                                            <div class="form-group">
                                                <label>{{language_data('Gateway Name')}}</label>
                                                <input type="text" class="form-control" name="gateway_name" value="{{$gateway->name}}" required>
                                            </div>
                                        @endif

                                            @if($gateway->settings!='Twilio' && $gateway->settings!='Zang' && $gateway->settings!='Plivo' && $gateway->settings!='PlivoPowerpack' && $gateway->settings!='AmazonSNS' && $gateway->settings!='TeleSign' && $gateway->settings!='TwilioCopilot' && $gateway->settings!='Ovh' && $gateway->settings!='Sendpulse')
                                                <div class="form-group">
                                                    <label>{{language_data('Gateway API Link')}}</label>
                                                    <input type="text" class="form-control" required name="gateway_link" value="{{$gateway->api_link}}">
                                                </div>
                                            @endif
                                            @if($gateway->settings=='Asterisk' || $gateway->settings=='JasminSMS' || $gateway->settings=='Diafaan' || $gateway->settings=='Ovh' || $gateway->type=='smpp' || $gateway->settings=='Send99')
                                                <div class="form-group">
                                                    @if($gateway->settings=='Ovh')
                                                        <label>API End Point</label>
                                                        <input type="text" class="form-control" name="port" value="{{$gateway->port}}">
                                                    @elseif($gateway->settings=='Send99')
                                                        <label>SMS Type </label>
                                                        <select class="selectpicker form-control" name="port">
                                                            <option value="promotional" @if($gateway->port=='promotional') selected @endif>Promotional</option>
                                                            <option value="transactional" @if($gateway->port=='transactional') selected @endif>Transactional</option>
                                                        </select>

                                                    @else
                                                        <label>Port</label>
                                                        <input type="text" class="form-control" name="port" value="{{$gateway->port}}">
                                                    @endif
                                                </div>
                                            @endif
                                            <div class="form-group">
                                                <label>{{language_data('Schedule SMS')}}</label>
                                                <select class="selectpicker form-control" name="schedule">
                                                    <option value="Yes" @if($gateway->schedule=='Yes') selected @endif>{{language_data('Yes')}}</option>
                                                    <option value="No" @if($gateway->schedule=='No') selected @endif>{{language_data('No')}}</option>
                                                </select>
                                            </div>

                                        <div class="form-group">
                                            <label>Global {{language_data('Status')}}</label>
                                            <select class="selectpicker form-control" name="global_status">
                                                <option value="Active" @if($gateway->status=='Active') selected @endif>{{language_data('Active')}}</option>
                                                <option value="Inactive" @if($gateway->status=='Inactive') selected @endif>{{language_data('Inactive')}}</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>


                            <div class="col-lg-9">
                                <div class="panel">
                                    <div class="panel-heading">
                                        <h3 class="panel-title">{{language_data('Credential Setup')}}</h3>
                                    </div>
                                    <div class="panel-body">
                                        <table class="table table-hover" id="sms_gateways">
                                            <tbody>
                                            @foreach($credentials as $credential)
                                                <tr class="info">
                                                <td>
                                                    <div class="form-group">
                                                        <label>
                                                            @if($gateway->settings=='Telenorcsms')
                                                                {{language_data('Msisdn')}}
                                                            @elseif($gateway->settings=='Twilio' || $gateway->settings=='Zang')
                                                                {{language_data('Account Sid')}}
                                                            @elseif($gateway->settings=='Plivo' || $gateway->settings=='PlivoPowerpack' || $gateway->settings=='KarixIO')
                                                                {{language_data('Auth ID')}}
                                                            @elseif($gateway->settings=='Wavecell')
                                                                Sub Account ID
                                                            @elseif($gateway->settings=='Ovh')
                                                                APP Key
                                                            @elseif($gateway->settings=='MessageBird' || $gateway->settings=='AmazonSNS')
                                                                Access Key
                                                            @elseif($gateway->settings=='Clickatell_Touch' || $gateway->settings=='ViralThrob' || $gateway->settings=='CNIDCOM' || $gateway->settings=='SmsBump' || $gateway->settings=='BSG' || $gateway->settings=='Onehop' || $gateway->settings=='TigoBeekun' || $gateway->settings=='Beepsend' || $gateway->settings=='Easy' || $gateway->settings=='Mailjet' || $gateway->settings=='Smsgatewayhub' || $gateway->settings=='MaskSMS'  || $gateway->settings == 'EblogUs' || $gateway->settings == 'MessageWhiz' || $gateway->settings == 'GlobalSMS' || $gateway->settings == 'ElasticEmail'  || $gateway->settings == 'Nexmo' || $gateway->settings == 'PreciseSMS' || $gateway->settings == 'Text Local' || $gateway->settings == 'SMSKitNet' || $gateway->settings == 'Clockworksms' || $gateway->settings == 'Mocean' || $gateway->settings == 'Telnyx' || $gateway->settings == 'APIWHA' || $gateway->settings == 'SMSAPIOnline')
                                                                API Key
                                                            @elseif($gateway->settings=='Semysms' || $gateway->settings=='Tropo')
                                                                User Token
                                                            @elseif($gateway->settings=='SendOut')
                                                                Phone Number
                                                            @elseif($gateway->settings=='SignalWire')
                                                                Project Key
                                                            @elseif($gateway->settings=='Dialog')
                                                                API Key For 160 Characters
                                                            @elseif($gateway->settings=='LightSMS' || $gateway->name=='KingTelecom' || $gateway->name=='Tellegroup')
                                                                Login
                                                            @elseif($gateway->settings=='CheapSMS')
                                                                Login ID
                                                            @elseif($gateway->settings=='TxtNation')
                                                                Company
                                                            @elseif($gateway->settings=='CMSMS')
                                                                Product Token
                                                            @elseif($gateway->settings=='ClxnetworksHTTPRest' || $gateway->settings=='SmsGatewayMe' || $gateway->settings=='WhatsAppChatApi' || $gateway->settings=='Gatewayapi')
                                                                API Token
                                                            @elseif($gateway->settings=='Diamondcard')
                                                                Account ID
                                                            @elseif($gateway->settings=='BulkGate')
                                                                Application ID
                                                            @elseif($gateway->settings=='msg91' || $gateway->settings=='MsgOnDND')
                                                                Auth Key
                                                            @elseif($gateway->settings=='AccessYou')
                                                                Account No
                                                            @elseif($gateway->settings=='Montnets')
                                                                User ID
                                                            @elseif($gateway->settings=='Send99' || $gateway->settings=='Sendpulse')
                                                                API ID
                                                            @elseif($gateway->settings=='CARMOVOIPSHORT' || $gateway->settings=='CARMOVOIPLONG')
                                                                CPF
                                                            @else
                                                                {{language_data('SMS Api User name')}}
                                                            @endif
                                                        </label>
                                                        <input type="text" class="form-control" name="gateway_user_name[]" value="{{$credential->username}}">
                                                    </div>
                                                </td>
                                                @if($gateway->settings!='MessageBird' && $gateway->settings!='SmsGatewayMe' && $gateway->settings!='Clickatell_Touch' && $gateway->settings!='Tropo' && $gateway->settings!='SmsBump' && $gateway->settings!='BSG' && $gateway->settings!='Beepsend' && $gateway->settings!='TigoBeekun' && $gateway->settings!='Easy' && $gateway->settings!='CMSMS' && $gateway->settings != 'Mailjet' && $gateway->settings != 'ClxnetworksHTTPRest' && $gateway->settings != 'MaskSMS' && $gateway->settings!='WhatsAppChatApi' && $gateway->settings!='Gatewayapi' && $gateway->settings!='MessageWhiz' && $gateway->settings!='GlobalSMS' && $gateway->settings != 'ElasticEmail' && $gateway->settings != 'PreciseSMS' && $gateway->settings != 'Text Local' && $gateway->settings != 'Clockworksms' && $gateway->settings != 'Telnyx' && $gateway->settings != 'APIWHA')
                                                    <td>

                                                        <div class="form-group">
                                                            <label>
                                                                @if($gateway->settings=='Twilio' || $gateway->settings=='Zang' || $gateway->settings=='Plivo' || $gateway->settings=='PlivoPowerpack' || $gateway->settings=='KarixIO')
                                                                    {{language_data('Auth Token')}}
                                                                @elseif($gateway->settings=='SMSKaufen' || $gateway->settings=='NibsSMS' || $gateway->settings=='LightSMS' || $gateway->settings=='Wavecell' || $gateway->settings == 'ClickSend' || $gateway->settings == 'IntelTele')
                                                                    {{language_data('SMS Api key')}}
                                                                @elseif($gateway->settings=='Semysms')
                                                                    Device ID
                                                                @elseif($gateway->name=='Skebby' || $gateway->name=='KingTelecom')
                                                                     Access Token
                                                                @elseif($gateway->settings=='SendOut'  || $gateway->settings == 'SMSKitNet' || $gateway->settings == 'SignalWire')
                                                                    API Token
                                                                @elseif($gateway->settings=='Ovh'  || $gateway->settings=='CNIDCOM')
                                                                    APP Secret
                                                                @elseif($gateway->settings=='AmazonSNS')
                                                                    Secret Access Key
                                                                @elseif($gateway->settings=='ViralThrob')
                                                                    SaaS Account
                                                                @elseif($gateway->settings=='TxtNation')
                                                                    eKey
                                                                @elseif($gateway->settings=='MsgOnDND' || $gateway->settings=='SMSAPIOnline')
                                                                    Route ID
                                                                @elseif($gateway->settings=='Onehop')
                                                                    Label/Route
                                                                @elseif($gateway->settings=='Dialog')
                                                                    API Key For 320 Characters
                                                                @elseif($gateway->settings=='Smsgatewayhub')
                                                                    Channel
                                                                @elseif($gateway->settings=='Diamondcard')
                                                                    Pin code
                                                                @elseif($gateway->settings=='BulkGate')
                                                                    Application Token
                                                                @elseif($gateway->settings=='Tellegroup')
                                                                    Senha
                                                                @elseif($gateway->settings == 'Nexmo' || $gateway->settings == 'Mocean' || $gateway->settings == 'Sendpulse')
                                                                    API Secret
                                                                @elseif($gateway->settings == 'EblogUs' || $gateway->settings == 'BudgetSMS')
                                                                    User ID
                                                                @elseif($gateway->settings=='msg91')
                                                                    Route
                                                                @else
                                                                    {{language_data('SMS Api Password')}}
                                                                @endif
                                                            </label>
                                                            <input type="text" class="form-control" name="gateway_password[]" value="{{$credential->password}}">
                                                        </div>
                                                    </td>
                                                @endif

                                                @if($gateway->custom=='Yes' || $gateway->settings=='SmsGatewayMe' || $gateway->settings=='GlobexCam' || $gateway->settings=='Ovh' || $gateway->settings=='1s2u' || $gateway->settings=='SMSPRO' || $gateway->settings=='DigitalReach' || $gateway->settings=='AmazonSNS' || $gateway->settings=='ExpertTexting' || $gateway->settings == 'Advansystelecom' || $gateway->settings == 'AlertSMS' || $gateway->settings == 'Clickatell_Central' || $gateway->settings == 'Smsgatewayhub' || $gateway->settings == 'Ayyildiz' || $gateway->settings == 'TwilioCopilot' || $gateway->settings == 'BudgetSMS' || $gateway->settings=='msg91')
                                                    <td>

                                                        <div class="form-group">
                                                            @if($gateway->settings=='SmsGatewayMe')
                                                                <label>Device ID</label>
                                                            @elseif($gateway->settings=='GlobexCam' || $gateway->settings == 'Clickatell_Central')
                                                                <label>{{language_data('SMS Api key')}}</label>
                                                            @elseif($gateway->settings=='Ovh')
                                                                <label>Consumer Key</label>
                                                            @elseif($gateway->settings=='1s2u')
                                                                <label>IPCL</label>
                                                            @elseif($gateway->settings=='SMSPRO')
                                                                <label>Customer ID</label>
                                                            @elseif($gateway->settings=='msg91')
                                                                Country Code
                                                            @elseif($gateway->settings=='DigitalReach')
                                                                <label>MT Port</label>
                                                            @elseif($gateway->settings=='AmazonSNS')
                                                                <label>Region</label>
                                                            @elseif($gateway->settings == 'Advansystelecom')
                                                                <label>Operator</label>
                                                            @elseif($gateway->settings == 'Smsgatewayhub')
                                                                <label>Route</label>
                                                            @elseif($gateway->settings == 'AlertSMS')
                                                                <label>Api Token</label>
                                                            @elseif($gateway->settings=='ExpertTexting')
                                                                <label> {{language_data('SMS Api key')}}</label>
                                                            @elseif($gateway->settings=='Ayyildiz')
                                                                <label> BayiKodu</label>
                                                            @elseif($gateway->settings=='TwilioCopilot')
                                                                <label> Service ID</label>
                                                            @elseif($gateway->settings == 'BudgetSMS')
                                                                <label>Handle</label>
                                                            @else
                                                                <label>{{language_data('Extra Value')}}</label>
                                                            @endif
                                                            <input type="text" class="form-control" name="extra_value[]" value="{{$credential->extra}}">
                                                        </div>
                                                    </td>
                                                @endif
                                                @if($gateway->settings=='Asterisk' )
                                                    <td>
                                                        <div class="form-group">
                                                            <label>Device Name</label>
                                                            <input type="text" class="form-control" name="device_name" value="{{env('SC_DEVICE')}}">
                                                        </div>
                                                    </td>
                                                @endif

                                                <td>
                                                    <div class="form-group">
                                                        <label>{{language_data('Credential Base Status')}}</label>
                                                        <select class="selectpicker form-control" name="credential_base_status[]">
                                                            <option value="Active" @if($credential->status=='Active') selected @endif>{{language_data('Active')}}</option>
                                                            <option value="Inactive" @if($credential->status=='Inactive') selected @endif>{{language_data('Inactive')}}</option>
                                                        </select>
                                                        <span class="help">{{language_data('You can only active one credential information')}}</span>
                                                    </div>
                                                </td>
                                            </tr>
                                            @endforeach
                                            </tbody>
                                        </table>


                                        <div class="row bottom-inv-con">
                                            <div class="col-md-6">
                                                <button type="button" class="btn btn-success" id="blank-add"><i
                                                            class="fa fa-plus"></i> {{language_data('Add New')}}
                                                </button>
                                                <button type="button" class="btn btn-danger" id="item-remove"><i
                                                            class="fa fa-minus-circle"></i> {{language_data('Delete')}}
                                                </button>
                                            </div>
                                            <div class="col-md-6"></div>
                                        </div>

                                        <div class="text-right">
                                            <input type="hidden" value="{{$gateway->id}}" name="cmd">
                                            <input type="hidden" value="{{$gateway->settings}}" id="gateway_name">
                                            <input type="hidden" value="{{$gateway->custom}}" id="gateway_custom">
                                            <input type="hidden" value="{{$gateway->type}}" id="gateway_type">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <button type="submit" class="btn btn-success btn-sm pull-right"><i class="fa fa-save"></i> {{language_data('Update')}} </button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                    </form>
                </div>

            </div>

        </div>
    </section>

@endsection

{{--External Script Section--}}
@section('script')
    {!! Html::script("assets/libs/handlebars/handlebars.runtime.min.js")!!}
    {!! Html::script("assets/js/form-elements-page.js")!!}
    {!! Html::script("assets/js/sms-gateway.js")!!}


@endsection
