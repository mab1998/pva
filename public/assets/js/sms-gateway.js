/**
 * Created by SHAMIM on 02-Mar-17.
 */
$(document).ready(function () {

    var $sms_gateways = $('#sms_gateways');
    var item_remove = $('#item-remove');
    var blank_add = $('#blank-add');
    var gateway_name = $('#gateway_name').val();
    var gateway_custom = $('#gateway_custom').val();
    var gateway_type = $('#gateway_type').val();

    if (gateway_name == 'Telenorcsms') {
        gateway_user_name = 'Msisdn';
    } else if (gateway_name == 'Twilio' || gateway_name == 'Zang') {
        gateway_user_name = 'Account Sid';
    } else if (gateway_name == 'Plivo' || gateway_name == 'PlivoPowerpack' || gateway_name == 'KarixIO') {
        gateway_user_name = 'Auth ID';
    } else if (gateway_name == 'Wavecell') {
        gateway_user_name = 'Sub Account ID';
    } else if (gateway_name == 'MessageBird' || gateway_name == 'AmazonSNS') {
        gateway_user_name = 'Access Key';
    } else if (gateway_name == 'Clickatell_Touch' || gateway_name == 'ViralThrob' || gateway_name == 'CNIDCOM' || gateway_name == 'SmsBump' || gateway_name == 'BSG' || gateway_name == 'Onehop' || gateway_name == 'TigoBeekun' || gateway_name == 'Beepsend' || gateway_name == 'Easy' || gateway_name == 'Mailjet' || gateway_name == 'Smsgatewayhub' || gateway_name == 'MaskSMS' || gateway_name == 'EblogUs' || gateway_name == 'MessageWhiz' || gateway_name == 'GlobalSMS' || gateway_name == 'ElasticEmail' || gateway_name == 'Nexmo' || gateway_name == 'PreciseSMS' || gateway_name == 'Text Local' || gateway_name == 'SMSKitNet' || gateway_name == 'Clockworksms' || gateway_name == 'Mocean' || gateway_name == 'Telnyx' || gateway_name == 'APIWHA' || gateway_name == 'SMSAPIOnline') {
        gateway_user_name = 'API Key';
    } else if (gateway_name == 'Semysms' || gateway_name == 'Tropo') {
        gateway_user_name = 'User Token';
    } else if (gateway_name == 'SendOut') {
        gateway_user_name = 'Phone Number';
    } else if (gateway_name == 'SignalWire') {
        gateway_user_name = 'Project Key';
    } else if (gateway_name == 'Dialog') {
        gateway_user_name = 'API Key For 160 Characters';
    } else if (gateway_name == 'LightSMS' || gateway_name == 'KingTelecom' || gateway_name == 'Tellegroup') {
        gateway_user_name = 'Login';
    } else if (gateway_name == 'CheapSMS') {
        gateway_user_name = 'Login id';
    } else if (gateway_name == 'TxtNation') {
        gateway_user_name = 'Company';
    }else if (gateway_name == 'CMSMS'){
        gateway_user_name = 'Product Token'
    }else if (gateway_name == 'msg91' || gateway_name == 'MsgOnDND'){
        gateway_user_name = 'Auth Key'
    }else if (gateway_name == 'ClxnetworksHTTPRest' || gateway_name == 'SmsGatewayMe' || gateway_name == 'WhatsAppChatApi' || gateway_name == 'Gatewayapi'){
        gateway_user_name = 'API Token'
    }else if (gateway_name == 'Diamondcard'){
        gateway_user_name = 'Account ID'
    }else if (gateway_name == 'BulkGate'){
        gateway_user_name = 'Application ID'
    }else if (gateway_name == 'AccessYou'){
        gateway_user_name = 'Account No'
    }else if (gateway_name == 'Montnets'){
        gateway_user_name = 'User ID'
    }else if (gateway_name == 'CARMOVOIPSHORT' || gateway_name == 'CARMOVOIPLONG'){
        gateway_user_name = 'CPF'
    }else if (gateway_name == 'Ovh'){
        gateway_user_name = 'APP Key'
    } else if (gateway_name == 'Send99' || gateway_name == 'Sendpulse'){
        gateway_user_name = 'API ID'
    }  else {
        gateway_user_name = 'SMS API User name'
    }

    appended_data = null;

    if (gateway_name != 'MessageBird' && gateway_name != 'SmsGatewayMe' && gateway_name != 'Clickatell_Touch' && gateway_name != 'Tropo' && gateway_name != 'SmsBump' && gateway_name != 'BSG' && gateway_name != 'Beepsend' && gateway_name != 'TigoBeekun' && gateway_name != 'Easy' && gateway_name != 'CMSMS' && gateway_name != 'Mailjet' && gateway_name != 'ClxnetworksHTTPRest' && gateway_name != 'MaskSMS' && gateway_name != 'WhatsAppChatApi' && gateway_name != 'Gatewayapi' && gateway_name != 'MessageWhiz' && gateway_name != 'GlobalSMS' && gateway_name != 'ElasticEmail' && gateway_name != 'PreciseSMS' && gateway_name != 'Text Local' && gateway_name != 'Clockworksms' && gateway_name != 'Telnyx' && gateway_name != 'APIWHA') {

        if (gateway_name == 'Twilio' || gateway_name == 'Zang' || gateway_name == 'Plivo' || gateway_name == 'PlivoPowerpack'  || gateway_name == 'KarixIO') {
            gateway_password = 'Auth Token';
        } else if (gateway_name == 'SMSKaufen' || gateway_name == 'NibsSMS' || gateway_name == 'LightSMS' || gateway_name == 'Wavecell' || gateway_name == 'ClickSend' || gateway_name == 'IntelTele') {
            gateway_password = 'SMS Api key';
        } else if (gateway_name == 'Semysms') {
            gateway_password = 'Device ID';
        } else if (gateway_name == 'MsgOnDND' || gateway_name == 'SMSAPIOnline') {
            gateway_password = 'Route ID';
        } else if (gateway_name == 'SendOut' || gateway_name == 'SMSKitNet' || gateway_name == 'SignalWire') {
            gateway_password = 'API Token';
        } else if (gateway_name == 'Ovh' || gateway_name == 'CNIDCOM') {
            gateway_password = 'APP Secret';
        } else if (gateway_name == 'Skebby' || gateway_name == 'KingTelecom') {
            gateway_password = 'Access Token';
        } else if (gateway_name == 'AmazonSNS') {
            gateway_password = 'Secret Access Key';
        } else if (gateway_name == 'ViralThrob') {
            gateway_password = 'SaaS Account';
        } else if (gateway_name == 'TxtNation') {
            gateway_password = 'eKey';
        } else if (gateway_name == 'msg91') {
            gateway_password = 'Route';
        } else if (gateway_name == 'Onehop') {
            gateway_password = 'Label/Route';
        } else if (gateway_name == 'Dialog') {
            gateway_password = 'API Key For 320 Characters';
        } else if (gateway_name == 'Smsgatewayhub') {
            gateway_password = 'Channel';
        } else if (gateway_name == 'Diamondcard') {
            gateway_password = 'Pin code';
        } else if (gateway_name == 'BulkGate') {
            gateway_password = 'Application Token';
        } else if (gateway_name == 'Tellegroup') {
            gateway_password = 'Senha';
        } else if (gateway_name == 'Nexmo' || gateway_name == 'Mocean' || gateway_name == 'Sendpulse') {
            gateway_password = 'API Secret';
        } else if (gateway_name == 'EblogUs' || gateway_name == 'BudgetSMS') {
            gateway_password = 'User ID';
        } else {
            gateway_password = 'SMS Api Password';
        }

        appended_data = appended_data + '<td><div class="form-group"><label>' + gateway_password + ' </label><input type="text" class="form-control" name="gateway_password[]"></div></td>';
    }

    if (gateway_custom == 'Yes' || gateway_name == 'SmsGatewayMe' || gateway_name == 'GlobexCam' || gateway_name == 'Ovh' || gateway_name == '1s2u' || gateway_name == 'SMSPRO' || gateway_name == 'DigitalReach' || gateway_name == 'AmazonSNS' || gateway_name == 'ExpertTexting' || gateway_name == 'Advansystelecom' || gateway_name == 'AlertSMS' || gateway_name == 'Clickatell_Central' || gateway_name == 'Smsgatewayhub' || gateway_name == 'Ayyildiz' || gateway_name == 'TwilioCopilot' || gateway_name == 'BudgetSMS' || gateway_name == 'msg91') {
        if (gateway_name == 'SmsGatewayMe') {
            gateway_extra = 'Device ID';
        } else if (gateway_name == 'GlobexCam' || gateway_name == 'Clickatell_Central' ) {
            gateway_extra = 'SMS Api key';
        } else if (gateway_name == 'Ovh') {
            gateway_extra = 'Consumer Key';
        } else if (gateway_name == '1s2u') {
            gateway_extra = 'IPCL';
        } else if (gateway_name == 'SMSPRO') {
            gateway_extra = 'Customer ID';
        } else if (gateway_name == 'msg91') {
            gateway_extra = 'Country Code';
        } else if (gateway_name == 'DigitalReach') {
            gateway_extra = 'MT Port';
        } else if (gateway_name == 'AmazonSNS') {
            gateway_extra = 'Region';
        } else if (gateway_name == 'Advansystelecom') {
            gateway_extra = 'Operator';
        } else if (gateway_name == 'AlertSMS') {
            gateway_extra = 'Api Token';
        } else if (gateway_name == 'ExpertTexting') {
            gateway_extra = 'SMS Api key';
        } else if (gateway_name == 'Smsgatewayhub') {
            gateway_extra = 'Route';
        } else if (gateway_name == 'Ayyildiz') {
            gateway_extra = 'BayiKodu';
        } else if (gateway_name == 'TwilioCopilot') {
            gateway_extra = 'Service ID';
        } else if (gateway_name == 'BudgetSMS') {
            gateway_extra = 'Handle';
        }
        else {
            gateway_extra = 'Extra Value';
        }
        appended_data = appended_data + '<td><div class="form-group"><label>' + gateway_extra + ' </label><input type="text" class="form-control" name="extra_value[]"></div></td>';
    }

    if (gateway_name == 'Asterisk') {
        appended_data = appended_data + '<td><div class="form-group"><label>Device Name</label><input type="text" class="form-control" name="device_name[]"></div></td>';
    }

    item_remove.on('click', function () {
        $sms_gateways.find('tr.info').fadeOut(300, function () {
            $(this).remove();
            $sms_gateways.find('tr:last').trigger('click').find('td:first input').focus();
        });
    });
    blank_add.on('click', function () {
        $sms_gateways.find('tbody').append(
            '<tr>' +
            '<td><div class="form-group"><label>' + gateway_user_name + ' </label><input type="text" class="form-control" name="gateway_user_name[]"></div></td>' +
            appended_data +
            '<td><div class="form-group"><label>Status</label><select class="selectpicker form-control" name="credential_base_status[]">' +
            '<option value="Active">Active</option>' +
            '<option value="Inactive">Inactive</option>' +
            '</select>' +
          '<span class="help">You can only active one credential information</span></div></td>'+
            '</tr>'
        );
        $sms_gateways.find('tr:last').trigger('click').find('td:first input').focus();
        $('.selectpicker').selectpicker('refresh');
    });
    item_remove.hide();
    $sms_gateways.find('tbody').on('click', 'tr', function () {
        $(this).addClass("info").siblings("tr").removeClass("info").data("focuson", false);
        if ($(this).data('focuson') != true) {
            $(this).data('focuson', true);
        }
        item_remove.show();
    });
});
