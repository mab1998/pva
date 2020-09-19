<?php

/*
|--------------------------------------------------------------------------
| AuthController
|--------------------------------------------------------------------------
|
| All authentication like client login, admin login, forgot password, Every functions
| are described in this controller
|
*/

//======================================================================
// Client Login
//======================================================================
Route::get('/', 'AuthController@clientLogin');
Route::get('/1', 'AuthController2@clientLogin');

Route::post('client/get-login', 'AuthController@clientGetLogin');
Route::post('client/get-login1', 'AuthController2@clientGetLogin');

Route::get('signup', 'AuthController@clientSignUp');
Route::get('signup1', 'AuthController2@clientSignUp');

Route::post('user/post-registration', 'AuthController@postUserRegistration');
Route::post('user/post-registration1', 'AuthController2@postUserRegistration');

Route::get('user/registration-verification', 'AuthController@clientRegistrationVerification');
Route::post('user/post-verification-token', 'AuthController@postVerificationToken');
Route::get('verify-user/{token}', 'AuthController@verifyUserAccount');
Route::get('forgot-password', 'AuthController@forgotUserPassword');
Route::post('user/forgot-password-token', 'AuthController@forgotUserPasswordToken');
Route::get('user/forgot-password-token-code/{token}', 'AuthController@forgotUserPasswordTokenCode');


//======================================================================
// Admin Login
//======================================================================
Route::get('admin', 'AuthController@adminLogin');
Route::post('admin/get-login', 'AuthController@adminGetLogin');
Route::get('admin/forgot-password', 'AuthController@forgotPassword');
Route::post('admin/forgot-password-token', 'AuthController@forgotPasswordToken');
Route::get('admin/forgot-password-token-code/{token}', 'AuthController@forgotPasswordTokenCode');

//======================================================================
// Client proxy setting
//======================================================================

Route::get('user/proxy-setting', 'UserProxyController@getProxies');
Route::post('sms/post-sms-bundles','SMSController@postPriceBundles');

//======================================================================
// Domain Regestration
//======================================================================
Route::get('registrars', 'DomainController@get_provider');
Route::post('update-registrar', 'DomainController@update_provider');

Route::get('searche-domain/{domain}', 'DomainController@search_domain');

Route::get('purchase-domain', 'DomainController@post_provider');

Route::get('domain', 'DomainController@SearchDomain');

Route::post('domain', 'DomainController@post_search_domain');

Route::get('domain-add-to-card/{domain}', 'DomainController@DomainAddToCard');

Route::get('remove-domain-from-card/{domain}', 'DomainController@DomainRemoveFromCard');

Route::get('ssh', 'DomainController@ssh');

//======================================================================
// Server
//======================================================================
Route::get('server', 'ServersController@index');
Route::get('server-create', 'ServersController@CreatNewServer');
Route::post('server-create', 'ServersController@CreatNewServer_Post');

//======================================================================
// Pusher
//======================================================================
Route::get('test', function () {
    // event(new App\Events\StatusLiked('bbbbbbbbbb'));
    event(new App\Events\StatusLiked('Hello Mabouk bbbbbb'));
    return "Event has been sent!";
});
Route::post('message', 'BroadcastController@SendMessage');


Route::get('demo', function () {
    return view('pusher');
});



//======================================================================
// Permission Check
//======================================================================

/*Permission Check*/
Route::get('permission-error','AuthController@permissionError');



//======================================================================
// Update Application
//======================================================================

// Route::get('update','AuthController@verifyProductUpdate');
// Route::post('update/post-verify-product','AuthController@updateApplication');
// //Route::get('update','AuthController@updateApplication');
// Route::get('admin/check-available-update','AuthController@checkAvailableUpdate');




/*
|--------------------------------------------------------------------------
| ClientDashboardController
|--------------------------------------------------------------------------
|
| Maintain application summery like Total accounts, support tickets, invoice created etc.
| Finally we will find a whole application overview in this controller
|
*/

Route::get('dashboard', 'ClientDashboardController@dashboard');
// Route::get('dashboard', 'ClientDashboardController@dashboard');
Route::get('dashboard1', 'ClientDashboardController2@dashboard');

Route::get('logout', 'ClientDashboardController@logout');
Route::post('user/menu-open-status', 'ClientDashboardController@menuOpenStatus');




Route::get('admin/edit-profile', 'DashboardController@editProfile');
Route::post('admin/post-personal-info', 'DashboardController@postPersonalInfo');
Route::post('admin/update-avatar', 'DashboardController@updateAvatar');
Route::get('admin/change-password', 'DashboardController@changePassword');
Route::post('admin/update-password', 'DashboardController@updatePassword');
Route::post('admin/menu-open-status', 'DashboardController@menuOpenStatus');

/*
|--------------------------------------------------------------------------
| DashboardController
|--------------------------------------------------------------------------
|
| Maintain application summery like Total accounts, support tickets, invoice created etc.
| Finally we will find a whole application overview in this controller
|
*/

Route::get('admin/dashboard', 'DashboardController@dashboard');
Route::get('admin/dashboard1', 'DashboardController2@dashboard');


Route::get('admin/logout', 'DashboardController@logout');

//======================================================================
// Update Application (Version 2.3)
//======================================================================
// Route::get('admin/update-application', 'DashboardController@updateApplication');
// Route::post('admin/post-update-application', 'DashboardController@postUpdateApplication');
Route::get('admin/backup-database', 'DashboardController@backupDatabase');

/*
|--------------------------------------------------------------------------
| ClientController
|--------------------------------------------------------------------------
|
| Store all clients/users information, edit, update, delete these in this controller.
|
*/

//======================================================================
// Client Manage
//======================================================================
Route::get('clients/all', 'ClientController@allClients');
Route::get('clients/all1', 'ClientController2@allClients');


Route::any('clients/get-all-clients-data', 'ClientController@getAllClients');
Route::any('clients/get-all-clients-data1', 'ClientController2@getAllClients');


Route::get('clients/add', 'ClientController@addClient');
Route::get('clients/add1', 'ClientController2@addClient');


Route::post('clients/post-new-client', 'ClientController@addClientPost');
Route::post('clients/post-new-client1', 'ClientController2@addClientPost');

Route::get('clients/send-email', 'ClientController@sendEmail');
Route::get('clients/send-email1', 'ClientController2@sendEmail');

Route::post('clients/post-send-bulk-email', 'ClientController@postSendEmail');
Route::post('clients/post-send-bulk-email1', 'ClientController2@postSendEmail');

//======================================================================
// Profile Manage
//======================================================================
Route::get('clients/view/{id}', 'ClientController@viewClient');
Route::get('clients/view1/{id}', 'ClientController2@viewClient');



Route::post('clients/update-limit', 'ClientController@updateLimit');
Route::post('clients/update-limit1', 'ClientController2@updateLimit');

Route::post('clients/update-image', 'ClientController@updateImage');
Route::post('clients/update-image1', 'ClientController2@updateImage');


Route::post('clients/update-client-post', 'ClientController@updateClient');
Route::post('clients/update-client-post', 'ClientController2@updateClient');


Route::post('clients/send-sms', 'ClientController@sendSMS');

/*Export N Import wit CSV*/
Route::get('clients/export-n-import', 'ClientController@exportImport');
Route::get('clients/export-clients', 'ClientController@exportClients');
Route::get('clients/download-sample-csv', 'ClientController@downloadSampleCSV');
Route::post('clients/post-new-client-csv', 'ClientController@addNewClientCSV');

//======================================================================
// Client Delete
//======================================================================
Route::get('clients/delete-client/{id}', 'ClientController@deleteClient');


//======================================================================
// Client Group Manage
//======================================================================
Route::get('clients/groups', 'ClientController@clientGroups');
Route::post('clients/add-new-group', 'ClientController@addNewClientGroup');
Route::post('clients/update-group', 'ClientController@updateClientGroup');
Route::get('clients/export-client-group/{id}', 'ClientController@exportClientGroup');
Route::get('clients/delete-group/{id}', 'ClientController@deleteClientGroup');


/*
|--------------------------------------------------------------------------
| InvoiceController
|--------------------------------------------------------------------------
|
| Discuss in future
|
*/

Route::get('invoices/all', 'InvoiceController@allInvoices');
Route::get('invoices/all1', 'InvoiceController2@allInvoices');

Route::get('invoices/recurring', 'InvoiceController@recurringInvoices');


Route::get('invoices/add', 'InvoiceController@addInvoice');
Route::get('invoices/add1', 'InvoiceController2@addInvoice');


Route::post('invoices/post-new-invoice', 'InvoiceController@postInvoice');
Route::post('invoices/post-new-invoice1', 'InvoiceController2@postInvoice');

Route::get('invoices/view/{id}', 'InvoiceController@viewInvoice');
Route::get('invoices/view1/{id}', 'InvoiceController2@viewInvoice');


Route::get('invoices/edit/{id}', 'InvoiceController@editInvoice');
Route::get('invoices/edit1/{id}', 'InvoiceController2@editInvoice');


Route::post('invoices/post-edit-invoice', 'InvoiceController@postEditInvoice');
Route::post('invoices/post-edit-invoice1', 'InvoiceController2@postEditInvoice');


Route::get('invoices/client-iview/{id}', 'InvoiceController@clientIView');
Route::get('invoices/client-iview1/{id}', 'InvoiceController2@clientIView');


Route::get('invoices/iprint/{id}', 'InvoiceController@printView');
Route::get('invoices/iprint1/{id}', 'InvoiceController2@printView');

Route::get('invoices/download-pdf/{id}', 'InvoiceController@downloadPdf');
Route::get('invoices/download-pdf/{id}', 'InvoiceController2@downloadPdf');


Route::get('invoices/mark-paid/{id}', 'InvoiceController@markInvoicePaid');
Route::get('invoices/mark-paid1/{id}', 'InvoiceController2@markInvoicePaid');



Route::get('invoices/mark-unpaid/{id}', 'InvoiceController@markInvoiceUnpaid');
Route::get('invoices/mark-unpaid1/{id}', 'InvoiceController2@markInvoiceUnpaid');


Route::get('invoices/mark-partially-paid/{id}', 'InvoiceController@markInvoicePartiallyPaid');
Route::get('invoices/mark-partially-paid1/{id}', 'InvoiceController2@markInvoicePartiallyPaid');


Route::get('invoices/mark-cancelled/{id}', 'InvoiceController@markInvoiceCancelled');
Route::get('invoices/mark-cancelled1/{id}', 'InvoiceController2@markInvoiceCancelled');


Route::get('invoices/iprint/{id}', 'InvoiceController@printView');
Route::get('invoices/iprint1/{id}', 'InvoiceController2@printView');


Route::post('invoices/update-invoice', 'InvoiceController@updateInvoice');

Route::post('invoices/invoice-paid', 'InvoiceController@paidInvoice');

Route::post('invoices/invoice-unpaid', 'InvoiceController@unpaidInvoice');

Route::post('invoices/invoice-cancelled', 'InvoiceController@cancelledInvoice');

Route::post('invoices/invoice-partially-paid', 'InvoiceController@partiallyPaidInvoice');


Route::get('invoices/delete-invoice/{id}','InvoiceController@deleteInvoice');
Route::get('invoices/delete-invoice1/{id}','InvoiceController2@deleteInvoice');

Route::get('invoices/stop-recurring-invoice/{id}','InvoiceController@stopRecurringInvoice');
Route::get('invoices/stop-recurring-invoice1/{id}','InvoiceController2@stopRecurringInvoice');

Route::post('invoices/send-invoice-email','InvoiceController@sendInvoiceEmail');
Route::post('invoices/send-invoice-email1','InvoiceController2@sendInvoiceEmail');


/*
|--------------------------------------------------------------------------
| AdministratorController
|--------------------------------------------------------------------------
|
| Discuss in future
|
*/

//======================================================================
// Administrator Manage
//======================================================================
Route::get('administrators/all','AdministratorController@allAdministrator');
Route::post('administrators/add-new','AdministratorController@addAdministrator');
Route::get('administrators/manage/{id}','AdministratorController@manageAdministrator');
Route::post('administrators/post-update-admin','AdministratorController@postUpdateAdministrator');
Route::get('administrators/delete-admin/{id}','AdministratorController@deleteAdministrator');

//======================================================================
// Administrator Role Manage
//======================================================================
Route::get('administrators/role','AdministratorController@administratorRole');
Route::post('administrators/add-role','AdministratorController@addAdministratorRole');
Route::post('administrators/update-role','AdministratorController@updateAdministratorRole');
Route::get('administrators/set-role/{id}','AdministratorController@setAdministratorRole');
Route::get('administrators/delete-role/{id}','AdministratorController@deleteAdministratorRole');
Route::post('administrators/update-admin-set-roles','AdministratorController@updateAdministratorSetRole');



/*
|--------------------------------------------------------------------------
| SupportTicketController
|--------------------------------------------------------------------------
|
| Discuss in later
|
*/

Route::get('support-tickets/all','SupportTicketController@all');
Route::get('support-tickets/all1','SupportTicketController2@all');

Route::get('support-tickets/create-new','SupportTicketController@createNew');
Route::get('support-tickets/create-new1','SupportTicketController2@createNew');

Route::get('support-tickets/view-ticket/{id}','SupportTicketController@viewTicket');
Route::get('support-tickets/view-ticket1/{id}','SupportTicketController2@viewTicket');


Route::get('support-tickets/department','SupportTicketController@department');
Route::get('support-tickets/department1','SupportTicketController2@department');


Route::get('support-tickets/view-department/{id}','SupportTicketController@viewDepartment');
Route::get('support-tickets/view-department1/{id}','SupportTicketController2@viewDepartment');

// not fount rout
// Route::get('support-tickets/ticket-department/{id}','SupportTicketController@ticketDepartment');
// Route::get('support-tickets/ticket-status/{id}','SupportTicketController@ticketStatus');
// Route::post('support-tickets/ticket-update-department','SupportTicketController@updateTicketDepartment');
// Route::post('support-tickets/ticket-update-status','SupportTicketController@updateTicketStatus');

Route::post('support-tickets/post-department','SupportTicketController@postDepartment');
Route::post('support-tickets/post-department1','SupportTicketController2@postDepartment');

Route::post('support-tickets/update-department','SupportTicketController@updateDepartment');
Route::post('support-tickets/update-department1','SupportTicketController2@updateDepartment');

Route::post('support-tickets/post-ticket','SupportTicketController@postTicket');
Route::post('support-tickets/post-ticket1','SupportTicketController2@postTicket');



Route::post('support-tickets/replay-ticket','SupportTicketController@replayTicket');
Route::post('support-tickets/replay-ticket1','SupportTicketController2@replayTicket');

Route::get('support-tickets/delete-ticket/{id}','SupportTicketController@deleteTicket');
Route::get('support-tickets/delete-ticket1/{id}','SupportTicketController2@deleteTicket');

Route::get('support-tickets/delete-department/{id}','SupportTicketController@deleteDepartment');
Route::get('support-tickets/delete-department1/{id}','SupportTicketController2@deleteDepartment');


Route::post('support-ticket/basic-info-post','SupportTicketController@postBasicInfo');
Route::post('support-ticket/basic-info-post1','SupportTicketController2@postBasicInfo');

Route::post('support-ticket/post-ticket-files','SupportTicketController@postTicketFiles');
Route::post('support-ticket/post-ticket-files1','SupportTicketController2@postTicketFiles');

Route::get('support-ticket/download-file/{id}','SupportTicketController@downloadTicketFile');
Route::get('support-ticket/download-file1/{id}','SupportTicketController2@downloadTicketFile');

Route::get('support-ticket/delete-ticket-file/{id}','SupportTicketController@deleteTicketFile');
Route::get('support-ticket/delete-ticket-file1/{id}','SupportTicketController2@deleteTicketFile');


/*
|--------------------------------------------------------------------------
| SystemSetting Controller
|--------------------------------------------------------------------------
|
| Discuss in future
|
*/

//======================================================================
// General Setting
//======================================================================
Route::get('settings/general','SettingController@general');
Route::get('settings/general1','SettingController2@general');

Route::post('settings/post-general-setting','SettingController@postGeneralSetting');
Route::post('settings/post-general-setting1','SettingController2@postGeneralSetting');

Route::post('settings/post-system-email-setting','SettingController@postSystemEmailSetting');
Route::post('settings/post-system-email-setting1','SettingController2@postSystemEmailSetting');


Route::post('settings/post-system-sms-setting','SettingController@postSystemSMSSetting');
Route::post('settings/post-system-sms-setting1','SettingController2@postSystemSMSSetting');


Route::post('settings/post-system-auth-setting','SettingController@postSystemAuthSetting');
Route::post('settings/post-system-auth-setting1','SettingController2@postSystemAuthSetting');


//======================================================================
// Localization
//======================================================================
Route::get('settings/localization','SettingController@localization');
Route::post('settings/localization-post','SettingController@localizationPost');



/*Email Template Module*/
Route::get('settings/email-templates','SettingController@emailTemplates');
Route::get('settings/email-templates1','SettingController2@emailTemplates');

Route::get('settings/email-template-manage/{id}','SettingController@manageTemplate');
Route::get('settings/email-template-manage1/{id}','SettingController2@manageTemplate');

Route::post('settings/email-templates-update','SettingController@updateTemplate');
Route::post('settings/email-templates-update1','SettingController2@updateTemplate');


//======================================================================
// Language Settings
//======================================================================
Route::get('settings/language-settings','SettingController@languageSettings');
Route::post('settings/language-settings/add','SettingController@addLanguage');
Route::get('settings/language-settings-translate/{lid}','SettingController@translateLanguage');
Route::post('settings/language-settings-translate-post','SettingController@translateLanguagePost');
Route::get('settings/language-settings-manage/{lid}','SettingController@languageSettingsManage');
Route::post('settings/language-settings-manage-post','SettingController@languageSettingManagePost');
Route::get('settings/language-settings/delete/{lid}','SettingController@deleteLanguage');

/*Language Change*/
Route::get('language/change/{id}','SettingController@languageChange');

//======================================================================
// Payment Gateway Setting
//======================================================================
Route::get('settings/payment-gateways','SettingController@paymentGateways');
Route::get('settings/payment-gateways1','SettingController2@paymentGateways');

Route::get('settings/payment-gateway-manage/{id}','SettingController@paymentGatewayManage');
Route::get('settings/payment-gateway-manage1/{id}','SettingController2@paymentGatewayManage');

Route::post('settings/post-payment-gateway-manage','SettingController@postPaymentGatewayManage');
Route::post('settings/post-payment-gateway-manage1','SettingController2@postPaymentGatewayManage');


//======================================================================
// Background jobs
//======================================================================
Route::get('settings/background-jobs','SettingController@backgroundJobs');



/*
|--------------------------------------------------------------------------
| SMSController
|--------------------------------------------------------------------------
|
| discuss in future
|
*/

//======================================================================
// Coverage
//======================================================================
Route::get('sms/coverage','SMSController@coverage');
Route::get('sms/manage-coverage/{id}','SMSController@manageCoverage');
Route::post('sms/post-manage-coverage','SMSController@postManageCoverage');
Route::get('sms/add-operator/{id}','SMSController@addOperator');
Route::post('sms/post-add-operator','SMSController@postAddOperator');
Route::get('sms/view-operator/{id}','SMSController@viewOperator');
Route::get('sms/manage-operator/{id}','SMSController@manageOperator');
Route::post('sms/post-manage-operator','SMSController@postManageOperator');
Route::get('sms/delete-operator/{id}','SMSController@deleteOperator');

//======================================================================
// SenderID Management
//======================================================================
Route::get('sms/sender-id-management','SMSController@senderIdManagement');
Route::get('sms/add-sender-id','SMSController@addSenderID');
Route::post('sms/post-new-sender-id','SMSController@postNewSenderID');
Route::get('sms/view-sender-id/{id}','SMSController@viewSenderID');
Route::post('sms/post-update-sender-id','SMSController@postUpdateSenderID');
Route::get('sms/delete-sender-id/{id}','SMSController@deleteSenderID');

//======================================================================
// SMS Price Plan
//======================================================================
Route::get('sms/price-plan','SMSController@pricePlan');
Route::get('sms/price-plan1','SMSController2@pricePlan');


Route::get('sms/add-price-plan','SMSController@addPricePlan');
Route::get('sms/add-price-plan1','SMSController2@addPricePlan');

Route::post('sms/post-new-price-plan','SMSController@postNewPricePlan');
Route::post('sms/post-new-price-plan1','SMSController2@postNewPricePlan');

Route::get('sms/manage-price-plan/{id}','SMSController@managePricePlan');
Route::get('sms/manage-price-plan1/{id}','SMSController2@managePricePlan');

Route::post('sms/post-manage-price-plan','SMSController@postManagePricePlan');
Route::post('sms/post-manage-price-plan1','SMSController2@postManagePricePlan');

Route::get('sms/add-plan-feature/{id}','SMSController@addPlanFeature');
Route::get('sms/add-plan-feature1/{id}','SMSController2@addPlanFeature');

Route::post('sms/post-new-plan-feature','SMSController@postNewPlanFeature');
Route::post('sms/post-new-plan-feature1','SMSController2@postNewPlanFeature');

Route::get('sms/view-plan-feature/{id}','SMSController@viewPlanFeature');

Route::get('sms/delete-plan-feature/{id}','SMSController@deletePlanFeature');
Route::get('sms/delete-plan-feature1/{id}','SMSController2@deletePlanFeature');


Route::get('sms/manage-plan-feature/{id}','SMSController@managePlanFeature');
Route::post('sms/post-manage-plan-feature','SMSController@postManagePlanFeature');

Route::get('sms/delete-price-plan/{id}','SMSController@deletePricePlan');
Route::get('sms/delete-price-plan1/{id}','SMSController2@deletePricePlan');


/*Version 1.3*/

//======================================================================
// SMS Price Bundles
//======================================================================
Route::get('sms/price-bundles','SMSController@priceBundles');
Route::post('sms/post-sms-bundles','SMSController@postPriceBundles');



//======================================================================
// SMS Gateway Manage
//======================================================================
Route::get('sms/http-sms-gateway','SMSController@httpSmsGateways');
Route::get('sms/smpp-sms-gateway','SMSController@smppSmsGateways');
Route::any('sms/get-all-gateways-data','SMSController@getAllGatewaysData');
Route::any('sms/get-all-smpp-gateways-data','SMSController@getAllSMPPGatewaysData');
Route::get('sms/add-sms-gateways','SMSController@addSmsGateway');
Route::get('sms/add-smpp-sms-gateways','SMSController@addSMPPSmsGateway');
Route::post('sms/post-new-sms-gateway','SMSController@postNewSmsGateway');
Route::post('sms/post-new-smpp-sms-gateway','SMSController@postNewSMPPGateway');
Route::get('sms/gateway-manage/{id}','SMSController@smsGatewayManage');
Route::get('sms/custom-gateway-manage/{id}','SMSController@customSmsGatewayManage');
Route::post('sms/post-manage-sms-gateway','SMSController@postManageSmsGateway');
Route::post('sms/post-custom-sms-gateway','SMSController@postCustomSmsGateway');
Route::get('sms/delete-sms-gateway/{id}','SMSController@deleteSmsGateway');

//======================================================================
// Version 2.4 (Two way communication)
//======================================================================
Route::get('sms/custom-gateway-two-way/{id}','SMSController@customGatewayTwoWay');
Route::post('sms/post-update-two-way-communication','SMSController@postCustomGatewayTwoWay');
Route::any('sms/receive-message/{id}','PublicAccessController@replyCustomGatewayMessage');

//======================================================================
// Send Quick SMS (Version 2.2)
//======================================================================
Route::get('sms/quick-sms','SMSController@sendQuickSMS');
Route::post('sms/post-quick-sms','SMSController@postQuickSMS');



//======================================================================
// Send Bulk SMS
//======================================================================
Route::get('sms/send-sms','SMSController@sendBulkSMS');
Route::get('sms/get-contact-list-ids','SMSController@getRecipientsData');
Route::post('sms/post-bulk-sms','SMSController@postSendBulkSMS');
Route::post('sms/get-template-info','SMSController@postGetTemplateInfo');

//======================================================================
// Send SMS From File
//======================================================================
Route::get('sms/send-sms-file','SMSController@sendBulkSMSFile');
Route::get('sms/download-sample-sms-file','SMSController@downloadSampleSMSFile');
Route::post('sms/post-sms-from-file','SMSController@postSMSFromFile');

//======================================================================
// Send Schedule SMS
//======================================================================
Route::get('sms/send-schedule-sms','SMSController@sendScheduleSMS');
Route::post('sms/post-schedule-sms','SMSController@postScheduleSMS');
Route::get('sms/send-schedule-sms-file','SMSController@sendScheduleSMSFile');
Route::post('sms/post-schedule-sms-from-file','SMSController@postScheduleSMSFile');
Route::get('sms/update-schedule-sms','SMSController@updateScheduleSMS');
Route::any('sms/get-all-schedule-sms','SMSController@getAllScheduleSMS');
Route::get('sms/manage-update-schedule-sms/{id}','SMSController@manageUpdateScheduleSMS');
Route::post('sms/post-update-schedule-sms','SMSController@postUpdateScheduleSMS');
Route::get('sms/delete-schedule-sms/{id}','SMSController@deleteScheduleSMS');
Route::post('sms/delete-bulk-schedule-sms','SMSController@deleteBulkScheduleSMS');


//======================================================================
// SMS Templates
//======================================================================
Route::get('sms/sms-templates','SMSController@smsTemplates');
Route::get('sms/create-sms-template','SMSController@createSmsTemplate');
Route::post('sms/post-sms-template','SMSController@postSmsTemplate');
Route::get('sms/manage-sms-template/{id}','SMSController@manageSmsTemplate');
Route::post('sms/post-manage-sms-template','SMSController@postManageSmsTemplate');
Route::get('sms/delete-sms-template/{id}','SMSController@deleteSmsTemplate');

//======================================================================
// API Information
//======================================================================
Route::get('sms-api/info','SMSController@apiInfo');
Route::get('sms-api/sdk','SMSController@sdkInfo');
Route::post('sms-api/update-info','SMSController@updateApiInfo');
Route::any('sms/api','PublicAccessController@ultimateSMSApi');

//======================================================================
// Two Way Gateway
//======================================================================
Route::any('sms/reply-twilio','PublicAccessController@replyTwilio');
Route::any('sms/reply-txtlocal','PublicAccessController@replyTxtLocal');
Route::any('sms/reply-smsglobal','PublicAccessController@replySmsGlobal');
Route::any('sms/reply-bulk-sms','PublicAccessController@replyBulkSMS');
Route::any('sms/reply-nexmo','PublicAccessController@replyNexmo');
Route::any('sms/reply-plivo','PublicAccessController@replyPlivo');
Route::any('sms/delivery-report-bulk-sms','PublicAccessController@deliveryReportBulkSMS');
Route::any('sms/reply-message-bird','PublicAccessController@replyMessageBird');
Route::any('sms/reply-infobip','PublicAccessController@replyInfoBip');
Route::any('sms/reply-diafaan','PublicAccessController@replyDiafaan');
Route::any('sms/reply-whatsapp','PublicAccessController@replyWhatsApp');
Route::any('sms/reply-easysendsms','PublicAccessController@replyEasySendSMS');
Route::any('sms/reply-gatewayapi','PublicAccessController@replyGatewayAPI');
Route::any('sms/reply-46elks','PublicAccessController@reply46ELKS');

Route::any('sms/delivery-report-46elks','PublicAccessController@deliveryReport46ELKS');
Route::any('sms/delivery-report-smpp','PublicAccessController@deliveryReportSMPP');

//======================================================================
// SMS History
//======================================================================
Route::get('sms/history','ReportsController@smsHistory');
Route::any('sms/get-sms-history-data/','ReportsController@getSmsHistoryData');
Route::get('sms/view-inbox/{id}','ReportsController@smsViewInbox');
Route::get('sms/post-reply-sms/{id}/{message}','ReportsController@postReplySMS');
Route::get('sms/delete-sms/{id}','ReportsController@deleteSMS');
Route::post('sms/bulk-sms-delete','ReportsController@bulkDeleteSMS');


//======================================================================
// For Client Portal
//======================================================================
/*
|--------------------------------------------------------------------------
| User Controller
|--------------------------------------------------------------------------
|
| Maintain user from client portal
|
*/

//======================================================================
// Client Manage
//======================================================================
Route::get('user/all','UserController@allUsers');
Route::any('user/get-all-clients-data/','UserController@getAllClients');
Route::get('user/add', 'UserController@addUser');
Route::post('user/post-new-user', 'UserController@addUserPost');
Route::get('user/delete-user/{id}', 'UserController@deleteUser');

//======================================================================
// Profile Manage
//======================================================================
Route::get('user/view/{id}', 'UserController@viewUser');
Route::post('user/update-limit', 'UserController@updateLimit');
Route::post('user/update-image', 'UserController@updateImage');
Route::post('user/update-user-post', 'UserController@updateUser');
Route::post('user/send-sms', 'UserController@sendSMS');

/*Export N Import wit CSV*/
Route::get('user/export-n-import', 'UserController@exportImport');
Route::get('user/export-user', 'UserController@exportUsers');
Route::get('user/download-sample-csv', 'UserController@downloadSampleCSV');
Route::post('user/post-new-user-csv', 'UserController@addNewUserCSV');

//======================================================================
// Client Group Manage
//======================================================================
Route::get('users/groups', 'UserController@userGroups');
Route::post('users/add-new-group', 'UserController@addNewUserGroup');
Route::post('users/update-group', 'UserController@updateUserGroup');
Route::get('users/export-user-group/{id}', 'UserController@exportUserGroup');
Route::get('users/delete-group/{id}', 'UserController@deleteUserGroup');


/*
|--------------------------------------------------------------------------
| ClientInvoiceController
|--------------------------------------------------------------------------
|
| Discuss in future
|
*/

Route::get('user/invoices/all', 'ClientInvoiceController@allInvoices');
Route::get('user/invoices/all1', 'ClientInvoiceController2@allInvoices');

Route::get('user/invoices/paid-invoice', 'ClientInvoiceController2@paidInvoice');
Route::get('user/invoices/unpaid-invoice', 'ClientInvoiceController2@unpaidInvoice');


Route::get('user/invoices/recurring', 'ClientInvoiceController@recurringInvoices');

Route::get('user/invoices/view/{id}', 'ClientInvoiceController@viewInvoice');
Route::get('user/invoices/view1/{id}', 'ClientInvoiceController2@viewInvoice');



Route::get('user/invoices/client-iview/{id}', 'ClientInvoiceController@clientIView');
Route::get('user/invoices/client-iview1/{id}', 'ClientInvoiceController2@clientIView');

Route::get('user/invoices/iprint/{id}', 'ClientInvoiceController@printView');
Route::get('user/invoices/iprint1/{id}', 'ClientInvoiceController2@printView');



Route::get('user/invoices/download-pdf/{id}', 'ClientInvoiceController@downloadPdf');
Route::get('user/invoices/download-pdf1/{id}', 'ClientInvoiceController2@downloadPdf');

Route::get('user/invoices/iprint/{id}', 'ClientInvoiceController@printView');
Route::get('user/invoices/iprint1/{id}', 'ClientInvoiceController2@printView');

Route::get('user/invoices/iprint/{id}', 'ClientInvoiceController@printView');
Route::get('user/invoices/iprint1/{id}', 'ClientInvoiceController2@printView');

Route::any('user/invoices/pay-invoice', 'PaymentController@payInvoice');
Route::any('user/invoices/pay-invoice1', 'PaymentController2@payInvoice');

Route::any('user/invoice/success/{token}/{id}', 'PaymentController@successInvoice');
Route::any('user/invoice/success1/{token}/{id}', 'PaymentController2@successInvoice');

Route::any('user/invoice/cancel/{id}', 'PaymentController@cancelledInvoice');
Route::any('user/invoice/cancel1/{id}', 'PaymentController2@cancelledInvoice');

Route::any('user/slydepay/receive-callback', 'PaymentController@slydepayReceiveCallback');
Route::any('user/slydepay/receive-callback1', 'PaymentController2@slydepayReceiveCallback');

Route::post('user/invoices/pay-with-stripe', 'PaymentController@payWithStripe');
Route::post('user/invoices/pay-with-stripe1', 'PaymentController2@payWithStripe');



/*
|--------------------------------------------------------------------------
| UserTicketController
|--------------------------------------------------------------------------
|
|
|
*/

Route::get('user/tickets/all','UserTicketController@allSupportTickets');
Route::get('user/tickets/all1','UserTicketController2@allSupportTickets');



Route::get('user/tickets/create-new','UserTicketController@createNewTicket');
Route::get('user/tickets/create-new1','UserTicketController2@createNewTicket');



Route::post('user/tickets/post-ticket','UserTicketController@postTicket');
Route::post('user/tickets/post-ticket1','UserTicketController2@postTicket');

Route::get('user/tickets/view-ticket/{id}','UserTicketController@viewTicket');
Route::get('user/tickets/view-ticket1/{id}','UserTicketController2@viewTicket');


Route::post('user/tickets/replay-ticket','UserTicketController@replayTicket');
Route::post('user/tickets/replay-ticket1','UserTicketController2@replayTicket');

Route::post('user/tickets/post-ticket-files','UserTicketController@postTicketFiles');
Route::post('user/tickets/post-ticket-files1','UserTicketController2@postTicketFiles');


Route::get('user/tickets/download-file/{id}','UserTicketController@downloadTicketFile');
Route::get('user/tickets/download-file1/{id}','UserTicketController2@downloadTicketFile');


/*
|--------------------------------------------------------------------------
| UserSMSController
|--------------------------------------------------------------------------
|
|
|
*/

//======================================================================
// Sender ID Management
//======================================================================
Route::get('user/sms/sender-id-management','UserSMSController@senderIdManagement');
Route::post('user/sms/post-sender-id','UserSMSController@postSenderID');

//======================================================================
// Send Quick SMS (Version 2.2)
//======================================================================
Route::get('user/sms/quick-sms','UserSMSController@sendQuickSMS');
Route::post('user/sms/post-quick-sms','UserSMSController@postQuickSMS');

//======================================================================
// Get SMS Template (Version 1.3)
//======================================================================
Route::post('user/sms/get-template-info','UserSMSController@postGetTemplateInfo');

//======================================================================
// Send SMS
//======================================================================
Route::get('user/sms/send-sms','UserSMSController@sendBulkSMS');
Route::post('user/sms/post-bulk-sms','UserSMSController@postSendBulkSMS');
Route::get('user/sms/get-contact-list-ids','UserSMSController@getRecipientsData');

//======================================================================
// Send SMS From File
//======================================================================
Route::get('user/sms/send-sms-file','UserSMSController@sendSMSFromFile');
Route::get('user/sms/download-sample-sms-file','UserSMSController@downloadSampleSMSFile');
Route::post('user/sms/post-sms-from-file','UserSMSController@postSMSFromFile');


//======================================================================
// Send Schedule SMS
//======================================================================
Route::get('user/sms/send-schedule-sms','UserSMSController@sendScheduleSMS');
Route::post('user/sms/post-schedule-sms','UserSMSController@postScheduleSMS');
Route::get('user/sms/send-schedule-sms-file','UserSMSController@sendScheduleSMSFromFile');
Route::post('user/sms/post-schedule-sms-from-file','UserSMSController@postScheduleSMSFromFile');

/*Version 1.1*/
Route::get('user/sms/update-schedule-sms','UserSMSController@updateScheduleSMS');
Route::any('user/sms/get-all-schedule-sms','UserSMSController@getAllScheduleSMS');
Route::get('user/sms/manage-update-schedule-sms/{id}','UserSMSController@manageUpdateScheduleSMS');
Route::post('user/sms/post-update-schedule-sms','UserSMSController@postUpdateScheduleSMS');
Route::get('user/sms/delete-schedule-sms/{id}','UserSMSController@deleteScheduleSMS');
Route::post('user/sms/delete-bulk-schedule-sms','UserSMSController@deleteBulkScheduleSMS');


//======================================================================
// SMS History
//======================================================================
Route::get('user/sms/history','UserSMSController@smsHistory');
Route::get('user/sms/get-sms-history-data','UserSMSController@getSmsHistoryData');
Route::get('user/sms/view-inbox/{id}','UserSMSController@smsViewInbox');
Route::get('user/sms/post-reply-sms/{id}/{message}','UserSMSController@postReplySMS');
Route::post('user/sms/bulk-sms-delete/','UserSMSController@deleteBulkSMS');
Route::get('user/sms/delete-sms/{id}','UserSMSController@deleteSMS');

//======================================================================
// Purchase SMS Plan
//======================================================================
Route::get('user/sms/purchase-sms-plan','UserSMSController@purchaseSMSPlan');
Route::get('user/sms/purchase-sms-plan1','UserSMSController2@purchaseSMSPlan');

Route::get('user/sms/purchase_custom_plan/{id}','UserSMSController@purchase_custom_plan');
Route::get('user/sms/purchase_custom_plan1/{id}','UserSMSController2@purchase_custom_plan');

Route::get('user/sms/sms-plan-feature/{id}','UserSMSController@smsPlanFeature');
Route::get('user/sms/sms-plan-feature1/{id}','UserSMSController2@smsPlanFeature');

Route::post('user/sms/post-purchase-sms-plan','PaymentController@purchaseSMSPlanPost');

Route::post('user/sms/post-purchase-sms-plan1','PaymentController2@purchaseSMSPlanPost');

Route::any('user/sms/purchase-plan/success/{token}/{id}','PaymentController@successPurchase');
Route::any('user/sms/purchase-plan/success1','PaymentController2@successPurchase');

Route::any('user/sms/purchase-plan/cancel/{id}','PaymentController@cancelledPurchase');
Route::post('user/sms/purchase-with-stripe','PaymentController@purchaseWithStripe');

/*Version 1.3*/
Route::get('user/sms/buy-unit','UserSMSController@buyUnit');
Route::post('user/get-transaction','UserSMSController@getTransaction');
Route::post('users/post-buy-unit','PaymentController@postBuyUnit');
Route::any('user/paystack/callback','PaymentController@payStackCallback');
Route::any('user/sms/buy-unit/success/{token}/{id}','PaymentController@buyUnitSuccess');
Route::any('user/sms/buy-unit/cancel','PaymentController@buyUnitCancel');
Route::post('user/sms/buy-unit-with-stripe','PaymentController@buyUnitWithStripe');

//======================================================================
// API Information
//======================================================================
Route::get('user/sms-api/info','UserSMSController@apiInfo');
Route::get('user/sms-api/info1','UserSMSController2@apiInfo');



Route::get('user/sms-api/sdk','UserSMSController@sdkInfo');

Route::post('user/sms-api/update-info','UserSMSController@updateApiInfo');
Route::post('user/sms-api/update-info1','UserSMSController2@updateApiInfo');

//======================================================================
// User Information
//======================================================================
Route::get('user/edit-profile','ClientDashboardController@editProfile');
Route::post('user/post-personal-info', 'ClientDashboardController@postPersonalInfo');
Route::post('user/update-avatar', 'ClientDashboardController@updateAvatar');
Route::get('user/change-password', 'ClientDashboardController@changePassword');
Route::post('user/update-password', 'ClientDashboardController@updatePassword');
Route::get('user/language/change/{id}', 'ClientDashboardController@changeLanguage');

//======================================================================
// SMS Templates
//======================================================================
Route::get('user/sms/sms-templates','UserSMSController@smsTemplates');
Route::get('user/sms/create-sms-template','UserSMSController@createSmsTemplate');
Route::post('user/sms/post-sms-template','UserSMSController@postSmsTemplate');
Route::get('user/sms/manage-sms-template/{id}','UserSMSController@manageSmsTemplate');
Route::post('user/sms/post-manage-sms-template','UserSMSController@postManageSmsTemplate');
Route::get('user/sms/delete-sms-template/{id}','UserSMSController@deleteSmsTemplate');


/*
|--------------------------------------------------------------------------
| Start Version 2.0 Work from here
|--------------------------------------------------------------------------
|
| Contact Module for version 2.0
|
*/

//======================================================================
// Contact Module
//======================================================================
Route::get('sms/phone-book','ContactController@phoneBook');
Route::post('sms/post-phone-book','ContactController@postPhoneBook');
Route::get('sms/add-contact/{id}','ContactController@addContact');
Route::post('sms/post-new-contact','ContactController@postNewContact');
Route::post('sms/update-single-contact','ContactController@postSingleContact');
Route::post('sms/update-phone-book','ContactController@updatePhoneBook');
Route::get('sms/view-contact/{id}','ContactController@viewContact');
Route::get('sms/edit-contact/{id}','ContactController@editContact');
Route::any('sms/get-all-contact/{id}','ContactController@getAllContact');
Route::get('sms/delete-contact/{id}','ContactController@deleteContact');
Route::post('sms/delete-bulk-contact','ContactController@deleteBulkContact');
Route::get('sms/import-contacts','ContactController@importContacts');
Route::get('sms/download-contact-sample-file','ContactController@downloadContactSampleFile');
Route::post('sms/post-import-file-contact','ContactController@postImportContact');
Route::post('sms/post-multiple-contact','ContactController@postMultipleContact');
Route::post('sms/get-recipients','ContactController@getRecipients');
Route::get('sms/delete-import-phone-number/{id}','ContactController@deleteImportPhoneNumber');

//======================================================================
// User Contact Module
//======================================================================
Route::get('user/phone-book','UserContactController@phoneBook');
Route::post('user/post-phone-book','UserContactController@postPhoneBook');
Route::post('user/update-phone-book','UserContactController@updatePhoneBook');
Route::get('user/add-contact/{id}','UserContactController@addContact');
Route::get('user/view-contact/{id}','UserContactController@viewContact');
Route::get('user/edit-contact/{id}','UserContactController@editContact');
Route::post('user/update-single-contact','UserContactController@postSingleContact');
Route::any('user/get-all-contact/{id}','UserContactController@getAllContact');
Route::get('user/delete-contact/{id}','UserContactController@deleteContact');
Route::post('user/sms/delete-bulk-contact','UserContactController@deleteBulkContact');
Route::get('user/sms/import-contacts','UserContactController@importContacts');
Route::get('user/sms/download-contact-sample-file','UserContactController@downloadContactSampleFile');
Route::post('user/post-import-file-contact','UserContactController@postImportContact');
Route::post('user/post-multiple-contact','UserContactController@postMultipleContact');
Route::post('user/post-new-contact','UserContactController@postNewContact');
Route::post('user/update-single-contact','UserContactController@postSingleContact');
Route::post('user/sms/get-recipients','UserSMSController@getRecipients');
Route::get('user/sms/delete-import-phone-number/{id}','UserContactController@deleteImportPhoneNumber');


//======================================================================
// BlackList Contacts Module For Admin
//======================================================================
Route::get('sms/blacklist-contacts','SMSController@blacklistContacts');
Route::any('sms/get-blacklist-contact','SMSController@getBlacklistContacts');
Route::post('sms/post-blacklist-contact','SMSController@postBlacklistContact');
Route::get('sms/delete-blacklist-contact/{id}','SMSController@deleteBlacklistContact');
Route::post('sms/delete-bulk-blacklist-contact','SMSController@deleteBulkBlacklistContact');

//======================================================================
// BlackList Contacts Module For User
//======================================================================
Route::get('user/sms/blacklist-contacts','UserSMSController@blacklistContacts');
Route::post('user/sms/post-blacklist-contact','UserSMSController@postBlacklistContact');
Route::get('user/sms/delete-blacklist-contact/{id}','UserSMSController@deleteBlacklistContact');
Route::any('user/sms/get-blacklist-contact','UserSMSController@getBlacklistContacts');
Route::post('user/sms/delete-bulk-blacklist-contact','UserSMSController@deleteBulkBlacklistContact');

//======================================================================
// Dynamic file upload
//======================================================================
Route::post('sms/get-csv-file-info','CommonDataController@getCsvFileInfo');
Route::post('client/get-csv-file-info','CommonDataController@getClientCsvFileInfo');
Route::get('custom-update','CommonDataController@customUpdate');


//======================================================================
// Paynow Payment Gateway
//======================================================================
Route::any('user/invoice/paynow/{id}', 'PaymentController@payNowInvoice');
Route::any('user/invoice/paynow1/{id}', 'PaymentController2@payNowInvoice');

Route::any('user/sms/purchase-plan/paynow/{id}','PaymentController@payNowPurchasePlan');
Route::any('user/sms/purchase-plan/paynow1/{id}','PaymentController2@payNowPurchasePlan');

Route::any('user/sms/buy-unit/paynow/{id}','PaymentController@buyUnitByPayNow');
Route::any('user/sms/buy-unit/paynow1/{id}','PaymentController2@buyUnitByPayNow');

Route::any('user/keywords/buy-keyword/paynow/{id}','PaymentController@buyKeywordByPayNow');

//======================================================================
// Purchase code
//======================================================================
Route::get('settings/purchase-code','SettingController@purchaseCode');
Route::post('settings/update-purchase-key','SettingController@updatePurchaseCode');



//======================================================================
// Filter Spam or Fraud word
//======================================================================
Route::get('sms/spam-words','ContactController@spamWords');
Route::any('sms/get-spam-words','ContactController@getSpamWords');
Route::post('sms/post-spam-word','ContactController@postSpamWord');
Route::get('sms/delete-spam-word/{id}','ContactController@deleteSpamWord');


//======================================================================
// Verify Block Message
//======================================================================
Route::get('sms/block-message','ReportsController@blockMessage');
Route::any('sms/get-block-message-data','ReportsController@getBlockMessageData');
Route::get('sms/view-block-message/{id}','ReportsController@viewBlockMessage');
Route::get('sms/release-block-message/{id}','ReportsController@releaseBlockMessage');
Route::get('sms/delete-block-message/{id}','ReportsController@deleteBlockMessage');


//======================================================================
// Recurring SMS
//======================================================================
Route::get('sms/recurring-sms','SMSController@recurringSMS');
Route::any('sms/get-recurring-sms-data','SMSController@getRecurringSMSData');
Route::post('sms/bulk-recurring-sms-delete','SMSController@bulkDeleteRecurringSMS');
Route::post('sms/bulk-recurring-sms-contact-delete','SMSController@bulkDeleteRecurringSMSContact');
Route::get('sms/delete-recurring-sms/{id}','SMSController@deleteRecurringSMS');
Route::get('sms/delete-recurring-sms-contact/{id}','SMSController@deleteRecurringSMSContact');

Route::get('sms/send-recurring-sms','SMSController@sendRecurringSMS');
Route::post('sms/post-recurring-sms','SMSController@postRecurringSMS');

Route::get('sms/stop-recurring-sms/{id}','SMSController@stopRecurringSMS');
Route::get('sms/start-recurring-sms/{id}','SMSController@startRecurringSMS');
Route::get('sms/update-recurring-sms/{id}','SMSController@updateRecurringSMS');
Route::get('sms/add-recurring-sms-contact/{id}','SMSController@addRecurringSMSContact');
Route::post('sms/post-recurring-sms-contact','SMSController@postRecurringSMSContact');
Route::get('sms/update-recurring-sms-contact/{id}','SMSController@updateRecurringSMSContact');
Route::get('sms/update-recurring-sms-contact-data/{id}','SMSController@updateRecurringSMSContactData');
Route::any('sms/get-recurring-sms-contact-data/{id}','SMSController@getRecurringSMSContactData');
Route::post('sms/post-update-recurring-sms','SMSController@postUpdateRecurringSMS');
Route::post('sms/post-update-recurring-sms-contact-data','SMSController@postUpdateRecurringSMSContactData');
Route::get('sms/send-recurring-sms-file','SMSController@sendRecurringSMSFile');
Route::post('sms/post-recurring-sms-file','SMSController@postRecurringSMSFile');



//======================================================================
//User Recurring SMS
//======================================================================

Route::get('user/sms/recurring-sms','UserSMSController@recurringSMS');
Route::any('user/sms/get-recurring-sms-data','UserSMSController@getRecurringSMSData');
Route::post('user/sms/bulk-recurring-sms-delete','UserSMSController@bulkDeleteRecurringSMS');
Route::post('user/sms/bulk-recurring-sms-contact-delete','UserSMSController@bulkDeleteRecurringSMSContact');
Route::get('user/sms/delete-recurring-sms/{id}','UserSMSController@deleteRecurringSMS');
Route::get('user/sms/delete-recurring-sms-contact/{id}','UserSMSController@deleteRecurringSMSContact');

Route::get('user/sms/send-recurring-sms','UserSMSController@sendRecurringSMS');
Route::post('user/sms/post-recurring-sms','UserSMSController@postRecurringSMS');

Route::get('user/sms/stop-recurring-sms/{id}','UserSMSController@stopRecurringSMS');
Route::get('user/sms/start-recurring-sms/{id}','UserSMSController@startRecurringSMS');
Route::get('user/sms/update-recurring-sms/{id}','UserSMSController@updateRecurringSMS');
Route::get('user/sms/add-recurring-sms-contact/{id}','UserSMSController@addRecurringSMSContact');
Route::post('user/sms/post-recurring-sms-contact','UserSMSController@postRecurringSMSContact');
Route::get('user/sms/update-recurring-sms-contact/{id}','UserSMSController@updateRecurringSMSContact');
Route::get('user/sms/update-recurring-sms-contact-data/{id}','UserSMSController@updateRecurringSMSContactData');
Route::any('user/sms/get-recurring-sms-contact-data/{id}','UserSMSController@getRecurringSMSContactData');
Route::post('user/sms/post-update-recurring-sms','UserSMSController@postUpdateRecurringSMS');
Route::post('user/sms/post-update-recurring-sms-contact-data','UserSMSController@postUpdateRecurringSMSContactData');
Route::get('user/sms/send-recurring-sms-file','UserSMSController@sendRecurringSMSFile');
Route::post('user/sms/post-recurring-sms-file','UserSMSController@postRecurringSMSFile');

//======================================================================
// Webxpay Payment gateway
//======================================================================
Route::any('user/webxpay/receive-callback', 'PaymentController@webxpayReceiveCallback');

//======================================================================
// Subscription api for wordpress plugin
//======================================================================
Route::any('contacts/api','PublicAccessController@ultimateSMSContactApi');



//======================================================================
// Version 2.4.0
//======================================================================

//======================================================================
// Keyword Settings
//======================================================================
Route::get('keywords/settings','SMSController@keywordSettings');
Route::post('keywords/post-keyword-setting','SMSController@postKeywordSettings');

Route::get('keywords/add','SMSController@addKeyword');
Route::post('keywords/post-new-keyword','SMSController@postNewKeyword');
Route::get('keywords/all','SMSController@allKeywords');
Route::any('keywords/get-keywords','SMSController@getKeywordsData');
Route::get('keywords/view/{id}','SMSController@viewKeyword');
Route::post('keywords/post-manage-keyword','SMSController@postManageKeyword');
Route::get('keywords/remove-mms-file/{id}','SMSController@removeKeywordMMSFile');
Route::get('keywords/delete-keyword/{id}','SMSController@deleteKeyword');
Route::get('sms/campaign-reports','SMSController@campaignReports');
Route::any('sms/get-campaign-history','SMSController@getCampaignReports');
Route::get('sms/manage-campaign/{id}','SMSController@manageCampaign');
Route::any('sms/get-campaign-recipients/{id}','SMSController@getCampaignRecipients');
Route::post('sms/post-update-campaign','SMSController@postUpdateCampaign');
Route::post('sms/bulk-campaign-recipients-delete','SMSController@deleteBulkCampaignRecipients');
Route::get('sms/delete-campaign-recipient/{id}','SMSController@deleteCampaignRecipient');
Route::post('sms/bulk-campaign-delete','SMSController@deleteBulkCampaign');
Route::get('sms/delete-campaign/{id}','SMSController@deleteCampaign');


Route::get('user/keywords','UserSMSController@allKeywords');
Route::any('user/keywords/get-keywords','UserSMSController@getAllKeywords');
Route::get('user/keywords/purchase/{id}','UserSMSController@purchaseKeyword');
Route::post('users/keywords/post-purchase-keyword','PaymentController@postPurchaseKeyword');
Route::any('user/keywords/buy-keyword/success/{token}/{id}','PaymentController@buyKeywordSuccess');
Route::any('user/keywords/buy-keyword/cancel','PaymentController@buyKeywordCancel');
Route::post('user/keywords/buy-keyword-with-stripe','PaymentController@buyKeywordWithStripe');


Route::get('user/keywords/view/{id}','UserSMSController@viewKeyword');
Route::post('user/keywords/post-manage-keyword','UserSMSController@postManageKeyword');
Route::get('user/keywords/remove-mms-file/{id}','UserSMSController@removeKeywordMMSFile');


Route::get('user/sms/campaign-reports','UserSMSController@campaignReports');
Route::any('user/sms/get-campaign-history','UserSMSController@getCampaignReports');
Route::get('user/sms/manage-campaign/{id}','UserSMSController@manageCampaign');
Route::any('user/sms/get-campaign-recipients/{id}','UserSMSController@getCampaignRecipients');
Route::post('user/sms/post-update-campaign','UserSMSController@postUpdateCampaign');
Route::post('user/sms/bulk-campaign-recipients-delete','UserSMSController@deleteBulkCampaignRecipients');
Route::get('user/sms/delete-campaign-recipient/{id}','UserSMSController@deleteCampaignRecipient');
Route::post('user/sms/bulk-campaign-delete','UserSMSController@deleteBulkCampaign');
Route::get('user/sms/delete-campaign/{id}','UserSMSController@deleteCampaign');


/*Coverage*/
Route::get('user/coverage','UserSMSController@getCoverage');
Route::get('user/sms/view-operator/{id}','UserSMSController@viewOperator');

//======================================================================
// Coverage api for wordpress plugin
//======================================================================
Route::any('coverage/api','PublicAccessController@ultimateSMSCoverageApi');
Route::any('coverage/get-operator-price/{country}','PublicAccessController@UltimateSMSOperatorPrice');



//======================================================================
// Chat box
//======================================================================
Route::get('sms/chat-box','ReportsController@chatBox');
Route::post('sms/view-reports','ReportsController@viewChatReports');
Route::post('sms/reply-chat-sms','ReportsController@replyChatSMS');
Route::post('sms/add-to-blacklist','ReportsController@addToBlacklist');
Route::post('sms/remove-chat-history','ReportsController@removeChatHistory');

Route::get('user/sms/chat-box','UserSMSController@chatBox');
Route::post('user/sms/view-reports','UserSMSController@viewChatReports');
Route::post('user/sms/reply-chat-sms','UserSMSController@replyChatSMS');
Route::post('user/sms/add-to-blacklist','UserSMSController@addToBlacklist');
Route::post('user/sms/remove-chat-history','UserSMSController@removeChatHistory');
