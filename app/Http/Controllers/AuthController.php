<?php

namespace App\Http\Controllers;

use App\Admin;
use App\AdminRolePermission;
use App\AppConfig;
use App\Campaigns;
use App\CampaignSubscriptionList;
use App\Client;
use App\EmailTemplates;
use App\Language;
use App\LanguageData;
use App\Mail\ForgotPassword;
use App\Mail\PasswordToken;
use App\Mail\UserRegistration;
use App\Mail\VerifyUser;
use App\PaymentGateways;
use App\ScheduleSMS;
use App\SMSGatewayCredential;
use App\SMSGateways;
use App\SMSHistory;
use App\SMSInbox;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use ReCaptcha\ReCaptcha;
use Exception;

class AuthController extends Controller
{
    //======================================================================
    // clientLogin Function Start Here
    //======================================================================
    public function clientLogin()
    {
        if (env('APP_TYPE') == 'new') {
            return redirect('install');
        }

        if (Auth::guard('client')->check()) {
            return redirect('dashboard');
        }

        return view('client.login2');
    }

    //======================================================================
    // clientGetLogin Function Start Here
    //======================================================================
    public function clientGetLogin(Request $request)
    {
        $this->validate($request, [
            'username' => 'required', 'password' => 'required'
        ]);

        $check_input = $request->only('username', 'password');
        $remember    = ($request->has('remember')) ? true : false;
		// $fact = array(); 
		// array_push( $fact, $remember ); 
		// array_push( $fact, $check_input ); 
		
		
		// return $fact;


        if (app_config('captcha_in_client') == '1') {
            if (isset($_POST['g-recaptcha-response'])) {
                $getCaptchaResponse = $_POST['g-recaptcha-response'];
                $recaptcha          = new ReCaptcha(app_config('captcha_secret_key'));
                $resp               = $recaptcha->verify($getCaptchaResponse);

                if (!$resp->isSuccess()) {
                    if (array_key_exists('0', $resp->getErrorCodes())) {
                        $error_msg = $resp->getErrorCodes()[0];
                    } else {
                        $error_msg = language_data('Invalid Captcha');
                    }

                    return redirect('/')->with([
                        'message' => $error_msg,
                        'message_important' => true
                    ]);
                }
            } else {
                return redirect('/')->with([
                    'message' => language_data('Invalid Captcha'),
                    'message_important' => true
                ]);
            }
        }

        if (Auth::guard('client')->attempt($check_input, $remember)) {

            if (Auth::guard('client')->user()->status == 'Active') {
                return redirect()->intended('dashboard');
            } else {
                Auth::guard('client')->logout();
                return redirect('/')->withInput($request->only('username'))->withErrors([
                    'username' => language_data('Your are inactive or blocked by system. Please contact with administrator')
                ]);
            }

        } else {
            return redirect('/')->withInput($request->only('username'))->withErrors([
                'username' => language_data('Invalid User name or Password')
            ]);
        }
    }



    //======================================================================
    // clientRegistrationVerification Function Start Here
    //======================================================================
    public function clientRegistrationVerification()
    {
        return view('client.user-verification');
    }


    //======================================================================
    // postVerificationToken Function Start Here
    //======================================================================
    public function postVerificationToken()
    {
        $cmd = Request::get('cmd');

        if ($cmd == '') {
            return redirect('/')->with([
                'message' => language_data('Invalid Request'),
                'message_important' => true
            ]);
        }


        $ef = Client::find($cmd);

        if ($ef) {

            $fprand = substr(str_shuffle(str_repeat('0123456789', '16')), 0, '16');

            $name     = $ef->fname . ' ' . $ef->lname;
            $email    = $ef->email;
            $fpw_link = url('/verify-user/' . $fprand);

            try {

                \Mail::to($email)->send(new VerifyUser($name, $fpw_link));

                return redirect('user/registration-verification')->with([
                    'message' => language_data('Verification code send successfully. Please check your email')
                ]);

            } catch (Exception $ex) {
                return redirect('user/registration-verification')->with([
                    'message' => $ex->getMessage()
                ]);
            }

        } else {
            return redirect('/')->with([
                'message' => language_data('Invalid Request'),
                'message_important' => true
            ]);
        }

    }

    //======================================================================
    // verifyUserAccount Function Start Here
    //======================================================================
    public function verifyUserAccount($token)
    {


        $tfnd = Client::where('pwresetkey', '=', $token)->count();

        if ($tfnd == '1') {
            $d             = Client::where('pwresetkey', '=', $token)->first();
            $d->status     = 'Active';
            $d->pwresetkey = '';
            $d->save();

            return redirect()->intended('dashboard');

        } else {
            return redirect('/')->with([
                'message' => language_data('Verification code not found'),
                'message_important' => true
            ]);
        }

    }


    //======================================================================
    // forgotUserPassword Function Start Here
    //======================================================================
    public function forgotUserPassword()
    {
        return view('client.login2');
    }



    //======================================================================
    // clientSignUp Function Start Here
    //======================================================================
    public function clientSignUp()
    {
        if (app_config('client_registration') != '1') {
            return redirect('/')->with([
                'message' => language_data('Invalid Request'),
                'message_important' => true
            ]);
        }

        return view('client.login2');

    }

    //======================================================================
    // postUserRegistration Function Start Here
    //======================================================================
    public function postUserRegistration(Request $request)
    {

        $v = \Validator::make($request->all(), [
            'first_name' => 'required', 'user_name' => 'required', 'email' => 'required', 'password' => 'required', 'cpassword' => 'required', 'phone' => 'required', 'country' => 'required'
        ]);

        if ($v->fails()) {
            return redirect('signup')->withInput($request->all())->withErrors($v->errors());
        }


        if (app_config('captcha_in_client_registration') == '1') {
            if (isset($_POST['g-recaptcha-response'])) {
                $getCaptchaResponse = $_POST['g-recaptcha-response'];
                $recaptcha          = new ReCaptcha(app_config('captcha_secret_key'));
                $resp               = $recaptcha->verify($getCaptchaResponse);

                if (!$resp->isSuccess()) {
                    if (array_key_exists('0', $resp->getErrorCodes())) {
                        $error_msg = $resp->getErrorCodes()[0];
                    } else {
                        $error_msg = language_data('Invalid Captcha');
                    }

                    return redirect('signup')->withInput($request->all())->with([
                        'message' => $error_msg,
                        'message_important' => true
                    ]);
                }
            } else {
                return redirect('signup')->withInput($request->all())->with([
                    'message' => language_data('Invalid Captcha'),
                    'message_important' => true
                ]);
            }
        }

        $exist_user_name  = Client::where('username', $request->user_name)->first();
        $exist_user_email = Client::where('email', $request->email)->first();

        if ($exist_user_name) {
            return redirect('signup')->withInput($request->all())->with([
                'message' => language_data('User name already exist'),
                'message_important' => true
            ]);
        }

        if ($exist_user_email) {
            return redirect('signup')->withInput($request->all())->with([
                'message' => language_data('Email already exist'),
                'message_important' => true
            ]);
        }

        $password  = $request->password;
        $cpassword = $request->cpassword;

        if ($password !== $cpassword) {
            return redirect('signup')->withInput($request->all())->with([
                'message' => language_data('Both password does not match'),
                'message_important' => true
            ]);
        } else {
            $password = bcrypt($password);
        }

        if (app_config('registration_verification') == '1') {
            $status = 'Inactive';
        } else {
            $status = 'Active';
        }

        $email_notify = $request->email_notify;
        if ($email_notify == 'yes') {
            $email_notify = 'Yes';
        } else {
            $email_notify = 'No';
        }

        $email       = $request->email;
        $sms_gateway = SMSGateways::find(app_config('registration_sms_gateway'));
        if (!$sms_gateway) {
            return redirect('signup')->with([
                'message' => 'SMS Gateway not found. Please contact with administrator',
                'message_important' => true
            ]);
        }

        $sms_gateway     = array(app_config('registration_sms_gateway'));
        $sms_gateways_id = json_encode($sms_gateway, true);

        if (app_config('sms_api_permission') == 1) {
            $api_permission = 'Yes';
        } else {
            $api_permission = 'No';
        }

        $api_key_generate    = $request->user_name . ':' . $cpassword;
        $client              = new Client();
        $client->parent      = '0';
        $client->fname       = $request->first_name;
        $client->lname       = $request->last_name;
        $client->email       = $email;
        $client->username    = $request->user_name;
        $client->password    = $password;
        $client->country     = $request->country;
        $client->phone       = $request->phone;
        $client->image       = 'profile.jpg';
        $client->datecreated = date('Y-m-d');
        $client->sms_limit   = '0';
        $client->api_access  = $api_permission;
        $client->api_key     = base64_encode($api_key_generate);
        $client->status      = $status;
        $client->reseller    = 'No';
        $client->sms_gateway = $sms_gateways_id;
        $client->api_gateway = app_config('registration_sms_gateway');
        $client->emailnotify = $email_notify;
        $client->save();
        $client_id = $client->id;

        /*For Email Confirmation*/
        if (is_int($client_id) && $email_notify == 'Yes' && $email != '') {
            $name = $request->first_name . ' ' . $request->last_name;

            try {
                \Mail::to($email)->send(new UserRegistration($name, $request->user_name, $cpassword));

                return redirect('/')->with([
                    'message' => language_data('Registration Successful')
                ]);

            } catch (Exception $ex) {
                return redirect('/')->with([
                    'message' => $ex->getMessage()
                ]);
            }
        }

        return redirect('/')->with([
            'message' => language_data('Registration Successful')
        ]);

    }



    //======================================================================
    // adminLogin Function Start Here
    //======================================================================
    public function adminLogin()
    {

        if (Auth::check()) {
            return redirect('admin/dashboard');
        }

        return view('admin.login');
    }



    //======================================================================
    // adminGetLogin Function Start Here
    //======================================================================
    public function adminGetLogin(Request $request)
    {

        $this->validate($request, [
            'username' => 'required', 'password' => 'required'
        ]);

        $check_input = $request->only('username', 'password');
        $remember    = ($request->has('remember')) ? true : false;


        if (app_config('captcha_in_admin') == '1') {
            if (isset($_POST['g-recaptcha-response'])) {
                $getCaptchaResponse = $_POST['g-recaptcha-response'];
                $recaptcha          = new ReCaptcha(app_config('captcha_secret_key'));
                $resp               = $recaptcha->verify($getCaptchaResponse);

                if (!$resp->isSuccess()) {
                    if (array_key_exists('0', $resp->getErrorCodes())) {
                        $error_msg = $resp->getErrorCodes()[0];
                    } else {
                        $error_msg = language_data('Invalid Captcha');
                    }

                    return redirect('admin')->with([
                        'message' => $error_msg,
                        'message_important' => true
                    ]);
                }
            } else {
                return redirect('admin')->with([
                    'message' => language_data('Invalid Captcha'),
                    'message_important' => true
                ]);
            }
        }

        if (Auth::attempt($check_input, $remember)) {
            if (Auth::user()->status == 'Active') {
                return redirect()->intended('admin/dashboard');
            } else {
                return redirect('admin')->withInput($request->only('username'))->withErrors([
                    'username' => language_data('Your are inactive or blocked by system. Please contact with administrator')
                ]);
            }
        } else {
            return redirect('admin')->withInput($request->only('username'))->withErrors([
                'username' => language_data('Invalid User name or Password')
            ]);
        }
    }

    //======================================================================
    // permissionError Function Start Here
    //======================================================================
    public function permissionError()
    {
        return view('admin.permission-error');
    }

    //======================================================================
    // forgotPassword Function Start Here
    //======================================================================
    public function forgotPassword()
    {
        return view('admin.forgot-password');
    }


    //======================================================================
    // forgotPasswordToken Function Start Here
    //======================================================================
    public function forgotPasswordToken(Request $request)
    {

        $appStage = app_config('AppStage');
        if ($appStage == 'Demo') {
            return redirect('admin')->with([
                'message' => language_data('This Option is Disable In Demo Mode'),
                'message_important' => true
            ]);
        }

        $v = \Validator::make($request->all(), [
            'email' => 'required'
        ]);

        if ($v->fails()) {
            return redirect('forgot-password')->withErrors($v->errors());
        }

        $email = Input::get('email');

        $d = Admin::where('email', '=', $email)->count();
        if ($d == '1') {
            $fprand         = substr(str_shuffle(str_repeat('0123456789', '16')), 0, '16');
            $ef             = Admin::where('email', '=', $email)->first();
            $name           = $ef->fname . ' ' . $ef->lname;
            $ef->pwresetkey = $fprand;
            $ef->save();

            $fpw_link = url('admin/forgot-password-token-code/' . $fprand);

            try {
                \Mail::to($email)->send(new ForgotPassword($fpw_link, $name));
                return redirect('admin/forgot-password')->with([
                    'message' => language_data('Your Password Already Reset. Please Check your email')
                ]);
            } catch (Exception $ex) {
                return redirect('admin/forgot-password')->with([
                    'message' => $ex->getMessage()
                ]);
            }

        } else {
            return redirect('admin/forgot-password')->with([
                'message' => language_data('Sorry There is no registered user with this email address'),
                'message_important' => true
            ]);
        }

    }

    //======================================================================
    // forgotPasswordTokenCode Function Start Here
    //======================================================================
    public function forgotPasswordTokenCode($token)
    {


        $appStage = app_config('AppStage');
        if ($appStage == 'Demo') {
            return redirect('admin')->with([
                'message' => language_data('This Option is Disable In Demo Mode'),
                'message_important' => true
            ]);
        }

        $tfnd = Admin::where('pwresetkey', '=', $token)->count();

        if ($tfnd == '1') {
            $d        = Admin::where('pwresetkey', '=', $token)->first();
            $name     = $d->fname . ' ' . $d->lname;
            $email    = $d->email;
            $username = $d->username;
            $url      = url('admin');

            $rawpass  = substr(str_shuffle(str_repeat('0123456789', '16')), 0, '16');
            $password = bcrypt($rawpass);

            $d->password   = $password;
            $d->pwresetkey = '';
            $d->save();

            /*For Email Confirmation*/

            try {
                \Mail::to($email)->send(new PasswordToken($name, $username, $rawpass, $url));

                return redirect('admin')->with([
                    'message' => language_data('A New Password Generated. Please Check your email.')
                ]);

            } catch (Exception $ex) {
                return redirect('admin')->with([
                    'message' => $ex->getMessage()
                ]);
            }

        } else {
            return redirect('admin')->with([
                'message' => language_data('Sorry Password reset Token expired or not exist, Please try again.'),
                'message_important' => true
            ]);
        }


    }



    //======================================================================
    // forgotUserPasswordToken Function Start Here
    //======================================================================
    public function forgotUserPasswordToken(Request $request)
    {

        $appStage = app_config('AppStage');
        if ($appStage == 'Demo') {
            return redirect('/')->with([
                'message' => language_data('This Option is Disable In Demo Mode'),
                'message_important' => true
            ]);
        }

        $v = \Validator::make($request->all(), [
            'email' => 'required'
        ]);

        if ($v->fails()) {
            return redirect('forgot-password')->withErrors($v->errors());
        }

        $email = Input::get('email');

        $d = Client::where('email', '=', $email)->count();
        if ($d == '1') {
            $fprand         = substr(str_shuffle(str_repeat('0123456789', '16')), 0, '16');
            $ef             = Client::where('email', '=', $email)->first();
            $name           = $ef->fname . ' ' . $ef->lname;
            $ef->pwresetkey = $fprand;
            $ef->save();

            $fpw_link = url('user/forgot-password-token-code/' . $fprand);

            /*For Email Confirmation*/

            try {
                \Mail::to($email)->send(new ForgotPassword($fpw_link, $name));

                return redirect('forgot-password')->with([
                    'message' => language_data('Your Password Already Reset. Please Check your email')
                ]);
            } catch (Exception $ex) {

                return redirect('forgot-password')->with([
                    'message' => $ex->getMessage()
                ]);
            }


        } else {
            return redirect('forgot-password')->with([
                'message' => language_data('Sorry There is no registered user with this email address'),
                'message_important' => true
            ]);
        }

    }

    //======================================================================
    // forgotUserPasswordTokenCode Function Start Here
    //======================================================================
    public function forgotUserPasswordTokenCode($token)
    {


        $appStage = app_config('AppStage');
        if ($appStage == 'Demo') {
            return redirect('/')->with([
                'message' => language_data('This Option is Disable In Demo Mode'),
                'message_important' => true
            ]);
        }

        $tfnd = Client::where('pwresetkey', '=', $token)->count();

        if ($tfnd == '1') {
            $d        = Client::where('pwresetkey', '=', $token)->first();
            $name     = $d->fname . ' ' . $d->lname;
            $url      = url('/');
            $email    = $d->email;
            $username = $d->username;

            $rawpass  = substr(str_shuffle(str_repeat('0123456789', '16')), 0, '16');
            $password = bcrypt($rawpass);

            $d->password   = $password;
            $d->pwresetkey = '';
            $d->save();

            /*For Email Confirmation*/

            try {
                \Mail::to($email)->send(new PasswordToken($name, $username, $rawpass, $url));

                return redirect('/')->with([
                    'message' => language_data('A New Password Generated. Please Check your email.')
                ]);

            } catch (Exception $ex) {
                return redirect('/')->with([
                    'message' => $ex->getMessage()
                ]);
            }

        } else {
            return redirect('/')->with([
                'message' => language_data('Sorry Password reset Token expired or not exist, Please try again.'),
                'message_important' => true
            ]);
        }
    }

}
