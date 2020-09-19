<?php

namespace App\Http\Controllers;

use App\Client;
use App\Invoices;
use App\Language;
use App\SMSHistory;
use App\SMSInbox;
use App\SupportTickets;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Input;

class ClientDashboardController2 extends Controller
{

    public function __construct()
    {
        $this->middleware('client');
    }


    //======================================================================
    // dashboard Function Start Here
    //======================================================================
    public function dashboard()
    {
        //For Invoice chart

        $inv_unpaid         = Invoices::where('status', 'Unpaid')->where('cl_id', Auth::guard('client')->user()->id)->count();
        $inv_paid           = Invoices::where('status', 'Paid')->where('cl_id', Auth::guard('client')->user()->id)->count();
        $inv_cancelled      = Invoices::where('status', 'Cancelled')->where('cl_id', Auth::guard('client')->user()->id)->count();
        $inv_partially_paid = Invoices::where('status', 'Partially Paid')->where('cl_id', Auth::guard('client')->user()->id)->count();


        //For Support Ticket Chart

        $st_pending  = SupportTickets::where('status', 'Pending')->where('cl_id', Auth::guard('client')->user()->id)->count();
        $st_answered = SupportTickets::where('status', 'Answered')->where('cl_id', Auth::guard('client')->user()->id)->count();
        $st_replied  = SupportTickets::where('status', 'Customer Reply')->where('cl_id', Auth::guard('client')->user()->id)->count();
        $st_closed   = SupportTickets::where('status', 'Closed')->where('cl_id', Auth::guard('client')->user()->id)->count();



        $sms_count   = SMSHistory::where('userid', Auth::guard('client')->user()->id)->count();
        $sms_success = SMSHistory::where('userid', Auth::guard('client')->user()->id)->where('status', 'like', '%Success%')->count();
        $sms_failed  = $sms_count - $sms_success;


        $recent_five_invoices = Invoices::orderBy('id', 'desc')->where('cl_id', Auth::guard('client')->user()->id)->take(5)->get();
        $recent_five_tickets  = SupportTickets::orderBy('id', 'desc')->where('cl_id', Auth::guard('client')->user()->id)->take(5)->get();

        return view('client1.dashboard',compact('inv_unpaid','inv_paid','inv_cancelled','inv_partially_paid'));
    }

    //======================================================================
    // menuOpenStatus Function Start Here
    //======================================================================
    public function menuOpenStatus()
    {
        $client = Client::find(Auth::guard('client')->user()->id);
        if ($client->menu_open == 0) {
            $client->menu_open = '1';
        } else {
            $client->menu_open = '0';
        }
        $client->save();
    }

    //======================================================================
    // logout Function Start Here
    //======================================================================
    public function logout()
    {
        Auth::guard('client')->logout();
        return redirect('/')->with([
            'message' => language_data('Logout Successfully')
        ]);

    }

    //======================================================================
    // editProfile Function Start Here
    //======================================================================
    public function editProfile()
    {
        $client = client_info(Auth::guard('client')->user()->id);
        return view('client.edit-profile', compact('client'));
    }


    /* postPersonalInfo  Function Start Here */
    public function postPersonalInfo(Request $request)
    {
        $appStage = app_config('AppStage');
        if ($appStage == 'Demo') {
            return redirect('user/edit-profile')->with([
                'message' => language_data('This Option is Disable In Demo Mode', Auth::guard('client')->user()->lan_id),
                'message_important' => true
            ]);
        }

        $v = \Validator::make($request->all(), [
            'first_name' => 'required', 'phone' => 'required', 'country' => 'required',
        ]);

        if ($v->fails()) {
            return redirect('user/edit-profile')->withInput($request->all())->withErrors($v->errors());
        }

        $client = Client::find(Auth::guard('client')->user()->id);

        if ($client->email != $request->email) {
            $exist_user_email = Client::where('email', $request->email)->first();
            if ($exist_user_email) {
                return redirect('user/edit-profile')->withInput($request->all())->with([
                    'message' => language_data('Email already exist', Auth::guard('client')->user()->lan_id),
                    'message_important' => true
                ]);
            }
        }

        $client->fname    = $request->first_name;
        $client->lname    = $request->last_name;
        $client->company  = $request->company;
        $client->website  = $request->website;
        $client->email    = $request->email;
        $client->address1 = $request->address;
        $client->address2 = $request->more_address;
        $client->state    = $request->state;
        $client->city     = $request->city;
        $client->postcode = $request->postcode;
        $client->country  = $request->country;
        $client->phone    = $request->phone;
        $client->save();

        return redirect('user/edit-profile')->with([
            'message' => language_data('Profile Updated Successfully', Auth::guard('client')->user()->lan_id)
        ]);

    }

//======================================================================
// updateAvatar Function Start Here
//======================================================================
    public function updateAvatar(Request $request)
    {

        $appStage = app_config('AppStage');
        if ($appStage == 'Demo') {
            return redirect('user/edit-profile')->with([
                'message' => language_data('This Option is Disable In Demo Mode', Auth::guard('client')->user()->lan_id),
                'message_important' => true
            ]);
        }

        $v = \Validator::make($request->all(), [
            'image' => 'required|image|mimes:jpeg,jpg,png,gif'
        ]);

        if ($v->fails()) {
            return redirect('user/edit-profile')->withErrors($v->errors());
        }

        $image  = Input::file('image');
        $client = Client::find(Auth::guard('client')->user()->id);

        if ($client) {
            if ($image != '') {

                if (isset($image) && in_array($image->getClientOriginalExtension(), array("png", "jpeg", "gif", 'jpg'))) {
                    $destinationPath = public_path() . '/assets/client_pic/';
                    $image_name      = $image->getClientOriginalName();
                    Input::file('image')->move($destinationPath, $image_name);

                    $client->image = $image_name;
                    $client->save();

                    return redirect('user/edit-profile')->with([
                        'message' => language_data('Image updated successfully', Auth::guard('client')->user()->lan_id)
                    ]);
                } else {
                    return redirect('user/edit-profile')->with([
                        'message' => language_data('Upload .png or .jpeg or .jpg or .gif file', Auth::guard('client')->user()->lan_id),
                        'message_important' => true
                    ]);
                }

            } else {
                return redirect('user/edit-profile')->with([
                    'message' => language_data('Upload an Image', Auth::guard('client')->user()->lan_id),
                    'message_important' => true
                ]);
            }
        } else {
            return $this->logout();
        }
    }

    /* changePassword  Function Start Here */
    public function changePassword()
    {
        return view('client.change-password');
    }

//======================================================================
// updatePassword Function Start Here
//======================================================================
    public function updatePassword(Request $request)
    {

        $appStage = app_config('AppStage');
        if ($appStage == 'Demo') {
            return redirect('user/change-password')->with([
                'message' => language_data('This Option is Disable In Demo Mode', Auth::guard('client')->user()->lan_id),
                'message_important' => true
            ]);
        }

        $v = \Validator::make($request->all(), [
            'current_password' => 'required', 'new_password' => 'required', 'confirm_password' => 'required'
        ]);

        if ($v->fails()) {
            return redirect('user/change-password')->withErrors($v->errors());
        }

        $user = Client::find(Auth::guard('client')->user()->id);

        $current_password = Input::get('current_password');
        $new_password     = Input::get('new_password');
        $confirm_password = Input::get('confirm_password');

        if (Hash::check($current_password, $user->password)) {

            if ($new_password == $confirm_password) {
                $user->password = bcrypt($new_password);
                $user->save();

                return redirect('user/change-password')->with([
                    'message' => language_data('Password Change Successfully', Auth::guard('client')->user()->lan_id)
                ]);

            } else {
                return redirect('user/change-password')->with([
                    'message' => language_data('Both password does not match', Auth::guard('client')->user()->lan_id),
                    'message_important' => true
                ]);
            }

        } else {
            return redirect('user/change-password')->with([
                'message' => language_data('Current Password Does Not Match', Auth::guard('client')->user()->lan_id),
                'message_important' => true
            ]);
        }
    }

    //======================================================================
    // changeLanguage Function Start Here
    //======================================================================
    public function changeLanguage($id)
    {
        $appStage = app_config('AppStage');
        if ($appStage == 'Demo') {
            return redirect('dashboard')->with([
                'message' => 'This Option Is Disable In Demo Mode',
                'message_important' => true
            ]);
        }

        $lang = Language::find($id);

        if ($lang) {
            Client::where('id', Auth::guard('client')->user()->id)->update(['lan_id' => $id]);
            return redirect('dashboard')->with([
                'message' => language_data('Language updated Successfully', Auth::guard('client')->user()->lan_id)
            ]);
        } else {
            return redirect('admin/dashboard')->with([
                'message' => language_data('Language not found', Auth::guard('client')->user()->lan_id),
                'message_important' => true
            ]);
        }
    }


}
