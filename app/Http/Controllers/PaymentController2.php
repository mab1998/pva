<?php

namespace App\Http\Controllers;

use App\Classes\Paynow;
use App\Classes\Paypal;
use App\Classes\TwoCheckout;
use App\Client;
use App\InvoiceItems;
use App\Invoices;
use App\Keywords;
use App\PaymentGateways;
use App\SMSBundles;
use App\SMSPlanFeature;
use App\SMSPricePlan;
use Cartalyst\Stripe\Exception\StripeException;
use Cartalyst\Stripe\Stripe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PayPal\Api\Amount;
use PayPal\Api\Item;
use PayPal\Api\ItemList;
use PayPal\Api\Payer;
use PayPal\Api\Payment;
use PayPal\Api\PaymentExecution;
use PayPal\Api\RedirectUrls;
use PayPal\Api\Transaction;
use PayPal\Auth\OAuthTokenCredential;
use PayPal\Exception\PayPalConnectionException;
use PayPal\Rest\ApiContext;
use Slydepay\Exception\ProcessPaymentException;
use Slydepay\Order\Order;
use Slydepay\Order\OrderItem;
use Slydepay\Order\OrderItems;
use Slydepay\Slydepay;
use Unicodeveloper\Paystack\Paystack;

class PaymentController2 extends Controller
{


    private $_api_context;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        /** PayPal api context **/
        $paypal_conf        = \Config::get('paypal');
        $this->_api_context = new ApiContext(new OAuthTokenCredential(
                $paypal_conf['client_id'],
                $paypal_conf['secret'])
        );
        $this->_api_context->setConfig($paypal_conf['settings']);
    }

    //======================================================================
    // payInvoice Function Start Here
    //======================================================================
    public function payInvoice(Request $request)
    {
        $cmd = $request->get('cmd');
        if ($request->gateway == '') {
            return redirect('user/invoices/view1/' . $cmd)->with([
                'message' => language_data('Payment gateway required', Auth::guard('client')->user()->lan_id),
                'message_important' => true
            ]);
        }

        $gateway       = $request->get('gateway');
        $gat_info      = PaymentGateways::where('settings', $gateway)->first();
        $invoice_items = InvoiceItems::where('inv_id', $cmd)->get();
        $invoice       = Invoices::find($cmd);

        $token = date('Ymds');

        Client::find(Auth::guard('client')->user()->id)->update([
            'pwresetexpiry' => $token
        ]);

        if ($gateway == 'paypal') {
            $payer = new Payer();
            $payer->setPaymentMethod('paypal');

            $items = [];
            foreach ($invoice_items as $key => $item) {
                $key = new Item();
                $key->setName($item->item)
                    ->setCurrency(app_config('Currency'))
                    ->setQuantity($item->qty)
                    ->setPrice($item->price);

                array_push($items, $key);
            }

            $item_list = new ItemList();
            $item_list->setItems($items);

            $amount = new Amount();
            $amount->setCurrency(app_config('Currency'))
                ->setTotal($invoice->total);

            $transaction = new Transaction();
            $transaction->setAmount($amount)
                ->setItemList($item_list)
                ->setDescription('Invoice No#' . $invoice->id);

            $redirect_urls = new RedirectUrls();
            $redirect_urls->setReturnUrl(url('/user/invoice/success1/' . $token . '/' . $cmd))/** Specify return URL **/
            ->setCancelUrl(url('/user/invoice/cancel1/' . $cmd));

            $payment = new Payment();
            $payment->setIntent('Sale')
                ->setPayer($payer)
                ->setRedirectUrls($redirect_urls)
                ->setTransactions(array($transaction));

            try {
                $payment->create($this->_api_context);
            } catch (PayPalConnectionException $ex) {

                return redirect('user/invoices/view1/' . $cmd)->with([
                    'message' => $ex->getMessage(),
                    'message_important' => true
                ]);
            }
            foreach ($payment->getLinks() as $link) {
                if ($link->getRel() == 'approval_url') {
                    $redirect_url = $link->getHref();
                    break;
                }
            }
            /** add payment ID to session **/
            \Session::put('paypal_payment_id', $payment->getId());
            if (isset($redirect_url)) {
                /** redirect to paypal **/
                return \Redirect::away($redirect_url);
            }

            return redirect('user/invoices/view1/' . $cmd)->with([
                'message' => 'Something went wrong. Please try again',
                'message_important' => true
            ]);

        }

        if ($gateway == '2checkout') {

            require_once app_path('Classes/TwoCheckout.php');

            $checkout = new TwoCheckout();

            $checkout->param('sid', $gat_info->value);
            $checkout->param('return_url', url('/user/invoice/success1/' . $token . '/' . $cmd));

            $i = 1;
            foreach ($invoice_items as $item) {
                $checkout->param('li_' . $i . '_name', $item->item);
                $checkout->param('li_' . $i . '_price', $item->price);
                $checkout->param('li_' . $i . '_quantity', $item->qty);
            }
            $checkout->param('card_holder_name', $invoice->client_name);
            $checkout->param('country', Auth::guard('client')->user()->country);
            $checkout->param('email', Auth::guard('client')->user()->email);
            $checkout->param('currency_code', app_config('Currency'));
            $checkout->gw_submit();
        }

        if ($gateway == 'payu') {

            $signature = "$gat_info->extra_value~$gat_info->value~invoiceId$invoice->id~$invoice->total~" . app_config('Currency');
            $signature = md5($signature);

            $order = array(
                'merchantId' => $gat_info->value,
                'ApiKey' => $gat_info->extra_value,
                'referenceCode' => 'invoiceId' . $invoice->id,
                'description' => 'Invoice No#' . $invoice->id,
                'amount' => $invoice->total,
                'tax' => '0',
                'taxReturnBase' => '0',
                'currency' => app_config('Currency'),
                'buyerEmail' => Auth::guard('client')->user()->email,
                'test' => '0',
                'signature' => $signature,
                'confirmationUrl' => url('/user/invoice/success1/' . $token . '/' . $cmd),
                'responseUrl' => url('/user/invoice/cancel1/' . $cmd),
            );
            ?>

            <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
            <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                <title>Please wait while you're redirected</title>
                <style type="text/css">
                    #redirect {
                        background: #f1f1f1;
                        font-family: Helvetica, Arial, sans-serif
                    }

                    #redirect-container {
                        width: 410px;
                        margin: 130px auto 0;
                        background: #fff;
                        border: 1px solid #b5b5b5;
                        -moz-border-radius: 5px;
                        -webkit-border-radius: 5px;
                        border-radius: 5px;
                        text-align: center
                    }

                    #redirect-container h1 {
                        font-size: 22px;
                        color: #5f5f5f;
                        font-weight: normal;
                        margin: 22px 0 26px 0;
                        padding: 0
                    }

                    #redirect-container p {
                        font-size: 13px;
                        color: #454545;
                        margin: 0 0 12px 0;
                        padding: 0
                    }

                    #redirect-container img {
                        margin: 0 0 35px 0;
                        padding: 0
                    }

                    .ajaxLoader {
                        margin: 80px 153px
                    }
                </style>
                <script type="text/javascript">
                    function timedText() {
                        setTimeout('msg1()', 2000)
                        setTimeout('msg2()', 4000)
                        setTimeout('document.MetaRefreshForm.submit()', 4000)
                    }

                    function msg1() {
                        document.getElementById('redirect-message').firstChild.nodeValue = 'Preparing Data...'
                    }

                    function msg2() {
                        document.getElementById('redirect-message').firstChild.nodeValue = 'Redirecting...'
                    }
                </script>
            </head>
            <body>
            <?php echo "<body onLoad=\"document.forms['gw'].submit();\">\n"; ?>
            <div id="redirect-container">
                <h1>Please wait while you&rsquo;re redirected</h1>
                <p class="redirect-message" id="redirect-message">Loading Data...</p>
                <script type="text/javascript">timedText()</script>
            </div>
            <form method="post" action="https://gateway.payulatam.com/ppp-web-gateway" name="gw">
                <?php
                foreach ($order as $name => $value) {
                    echo "<input type=\"hidden\" name=\"$name\" value=\"$value\"/>\n";
                }

                ?>
            </form>
            </body>
            </html>
            <?php
        }
        if ($gateway == 'coinpayments') {


            $order = array(
                'merchant' => $gat_info->value,
                'cmd' => '_pay',
                'reset' => '1',
                'item_name' => 'Invoice No#' . $invoice->id,
                'amountf' => $invoice->total,
                'allow_extra' => '1',
                'currency' => app_config('Currency'),
                'want_shipping' => '0',
                'success_url' => url('/user/invoice/success1/' . $token . '/' . $cmd),
                'cancel_url' => url('/user/invoice/cancel1/' . $cmd),
            );
            ?>

            <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
            <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                <title>Please wait while you're redirected</title>
                <style type="text/css">
                    #redirect {
                        background: #f1f1f1;
                        font-family: Helvetica, Arial, sans-serif
                    }

                    #redirect-container {
                        width: 410px;
                        margin: 130px auto 0;
                        background: #fff;
                        border: 1px solid #b5b5b5;
                        -moz-border-radius: 5px;
                        -webkit-border-radius: 5px;
                        border-radius: 5px;
                        text-align: center
                    }

                    #redirect-container h1 {
                        font-size: 22px;
                        color: #5f5f5f;
                        font-weight: normal;
                        margin: 22px 0 26px 0;
                        padding: 0
                    }

                    #redirect-container p {
                        font-size: 13px;
                        color: #454545;
                        margin: 0 0 12px 0;
                        padding: 0
                    }

                    #redirect-container img {
                        margin: 0 0 35px 0;
                        padding: 0
                    }

                    .ajaxLoader {
                        margin: 80px 153px
                    }
                </style>
                <script type="text/javascript">
                    function timedText() {
                        setTimeout('msg1()', 2000)
                        setTimeout('msg2()', 4000)
                        setTimeout('document.MetaRefreshForm.submit()', 4000)
                    }

                    function msg1() {
                        document.getElementById('redirect-message').firstChild.nodeValue = 'Preparing Data...'
                    }

                    function msg2() {
                        document.getElementById('redirect-message').firstChild.nodeValue = 'Redirecting...'
                    }
                </script>
            </head>
            <body>
            <?php echo "<body onLoad=\"document.forms['gw'].submit();\">\n"; ?>
            <div id="redirect-container">
                <h1>Please wait while you&rsquo;re redirected</h1>
                <p class="redirect-message" id="redirect-message">Loading Data...</p>
                <script type="text/javascript">timedText()</script>
            </div>
            <form method="post" action="https://www.coinpayments.net/index.php" name="gw">
                <?php
                foreach ($order as $name => $value) {
                    echo "<input type=\"hidden\" name=\"$name\" value=\"$value\"/>\n";
                }

                ?>
            </form>
            </body>
            </html>
            <?php
        }

        if ($gateway == 'stripe') {

            $stripe_amount = $invoice->total * 100;
            $plan_name     = 'Invoice No#' . $invoice->id;
            $post_url      = 'user/invoices/pay-with-stripe';
            return view('client1.stripe', compact('gat_info', 'stripe_amount', 'cmd', 'plan_name', 'post_url'));

        }

        if ($gateway == 'slydepay') {

            require_once(app_path('libraray/vendor/autoload.php'));

            $slydepay = new Slydepay($gat_info->value, $gat_info->extra_value);

            $total = number_format((float)$invoice->total, '2', '.', '');

            $orderItems   = new OrderItems([
                new OrderItem($invoice->id, "Invoice NO# $invoice->id", $total, 1)
            ]);
            $shippingCost = 0;
            $tax          = 0;
            $order_id     = _raid(5);

            $order = Order::createWithId($orderItems, $order_id, $shippingCost, $tax, $invoice->id);

            try {
                $response = $slydepay->processPaymentOrder($order);
                return redirect($response->redirectUrl());
            } catch (ProcessPaymentException $e) {
                return redirect('user/invoices/view1/' . $invoice->id)->with([
                    'message' => $e->getMessage(),
                    'message_important' => true
                ]);
            }
        }

        if ($gateway == 'paystack') {

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.paystack.co/transaction/initialize",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode([
                    'amount' => $invoice->total * 100,
                    'email' => Auth::guard('client')->user()->email,
                    'metadata' => [
                        'invoice_id' => $invoice->id,
                        'request_type' => 'invoice_payment',
                    ]
                ]),
                CURLOPT_HTTPHEADER => [
                    "authorization: Bearer " . getenv('PAYSTACK_SECRET_KEY'),
                    "content-type: application/json",
                    "cache-control: no-cache"
                ],
            ));

            $response = curl_exec($curl);
            $err      = curl_error($curl);

            curl_close($curl);

            if ($response === false) {
                return redirect('user/invoices/view1/' . $invoice->id)->with([
                    'message' => 'Php curl show false value. Please contact with your provider',
                    'message_important' => true
                ]);
            }

            if ($err) {
                return redirect('user/invoices/view1/' . $invoice->id)->with([
                    'message' => $err,
                    'message_important' => true
                ]);
            }

            $tranx = json_decode($response);

            if ($tranx->status != 1) {
                return redirect('user/invoices/view1/' . $invoice->id)->with([
                    'message' => $tranx->message,
                    'message_important' => true
                ]);
            }

            return redirect($tranx->data->authorization_url);

        }

        if ($gateway == 'webxpay') {
            require_once(app_path('libraray/webxpay/Crypt/RSA.php'));

            //initialize RSA
            $rsa = new \Crypt_RSA();
            // unique_order_id|total_amount
            $plaintext = "$invoice->id|$invoice->total";
            $publickey = $gat_info->extra_value;

            $rsa->loadKey($publickey);

            $encrypt = $rsa->encrypt($plaintext);
            //encode for data passing
            $payment = base64_encode($encrypt);

            //custom fields
            //cus_1|cus_2|cus_3|cus_4
            $custom_fields = base64_encode("invoice|$invoice->id");

            $order = array(
                'first_name' => Auth::guard('client')->user()->fname,
                'last_name' => Auth::guard('client')->user()->lname,
                'email' => Auth::guard('client')->user()->email,
                'contact_number' => Auth::guard('client')->user()->phone,
                'address_line_one' => Auth::guard('client')->user()->address1,
                'address_line_two' => Auth::guard('client')->user()->address2,
                'city' => Auth::guard('client')->user()->city,
                'state' => Auth::guard('client')->user()->state,
                'postal_code' => Auth::guard('client')->user()->postcode,
                'country' => Auth::guard('client')->user()->country,
                'process_currency' => app_config('Currency'),
                'cms' => 'PHP',
                'secret_key' => $gat_info->value,
                'payment' => $payment,
                'custom_fields' => $custom_fields,
            );

            ?>

            <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
            <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                <title>Please wait while you're redirected</title>
                <style type="text/css">
                    #redirect {
                        background: #f1f1f1;
                        font-family: Helvetica, Arial, sans-serif
                    }

                    #redirect-container {
                        width: 410px;
                        margin: 130px auto 0;
                        background: #fff;
                        border: 1px solid #b5b5b5;
                        -moz-border-radius: 5px;
                        -webkit-border-radius: 5px;
                        border-radius: 5px;
                        text-align: center
                    }

                    #redirect-container h1 {
                        font-size: 22px;
                        color: #5f5f5f;
                        font-weight: normal;
                        margin: 22px 0 26px 0;
                        padding: 0
                    }

                    #redirect-container p {
                        font-size: 13px;
                        color: #454545;
                        margin: 0 0 12px 0;
                        padding: 0
                    }

                    #redirect-container img {
                        margin: 0 0 35px 0;
                        padding: 0
                    }

                    .ajaxLoader {
                        margin: 80px 153px
                    }
                </style>
                <script type="text/javascript">
                    function timedText() {
                        setTimeout('msg1()', 2000)
                        setTimeout('msg2()', 4000)
                        setTimeout('document.MetaRefreshForm.submit()', 4000)
                    }

                    function msg1() {
                        document.getElementById('redirect-message').firstChild.nodeValue = 'Preparing Data...'
                    }

                    function msg2() {
                        document.getElementById('redirect-message').firstChild.nodeValue = 'Redirecting...'
                    }
                </script>
            </head>
            <body>
            <?php echo "<body onLoad=\"document.forms['gw'].submit();\">\n"; ?>
            <div id="redirect-container">
                <h1>Please wait while you&rsquo;re redirected</h1>
                <p class="redirect-message" id="redirect-message">Loading Data...</p>
                <script type="text/javascript">timedText()</script>
            </div>
            <form method="post" action="https://webxpay.com/index.php?route=checkout/billing" name="gw">
                <?php
                foreach ($order as $name => $value) {
                    echo "<input type=\"hidden\" name=\"$name\" value=\"$value\"/>\n";
                }

                ?>
            </form>
            </body>
            </html>
            <?php

        }

        if ($gateway == 'paynow') {
            require_once app_path('Classes/Paynow.php');

            $paynow = new Paynow();

            //set POST variables
            $values = array(
                'resulturl' => url('/user/invoice/paynow1/' . $cmd),
                'returnurl' => url('/user/invoice/paynow1/' . $cmd),
                'reference' => _raid(10),
                'amount' => $invoice->total,
                'id' => $gat_info->value,
                'status' => 'Invoice No#' . $invoice->id
            );

            $fields_string = $paynow->CreateMsg($values, $gat_info->extra_value);

            //open connection
            $ch  = curl_init();
            $url = 'https://www.paynow.co.zw/interface/initiatetransaction';

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $result = curl_exec($ch);

            //close connection
            curl_close($ch);

            if ($result) {
                $msg = $paynow->ParseMsg($result);

                //first check status, take appropriate action
                if (is_array($msg) && array_key_exists('status', $msg) && $msg["status"] == 'Error') {
                    return redirect('/user/invoice/cancel1/' . $cmd)->with([
                        'message' => $msg['error'],
                        'message_important' => true
                    ]);

                } else if (is_array($msg) && array_key_exists('status', $msg) && $msg["status"] == 'Ok') {

                    //second, check hash
                    $validateHash = $paynow->CreateHash($msg, $gat_info->extra_value);
                    if ($validateHash != $msg["hash"]) {
                        $error = "Paynow reply hashes do not match : " . $validateHash . " - " . $msg["hash"];
                        return redirect('/user/invoice/cancel1/' . $cmd)->with([
                            'message' => $error,
                            'message_important' => true
                        ]);

                    } else {

                        if (is_array($msg) && array_key_exists('browserurl', $msg)) {
                            $theProcessUrl = $msg["browserurl"];

                            $orders_data_file = storage_path('PayNowTransaction.ini');
                            //1. Saving mine to a PHP.INI type of file, you should save it to a db etc
                            $orders_array = array();
                            if (file_exists($orders_data_file)) {
                                $orders_array = parse_ini_file($orders_data_file, true);
                            }

                            $orders_array['InvoiceNo_' . $cmd] = $msg;

                            $paynow->write_php_ini($orders_array, $orders_data_file, true);


                            return redirect($theProcessUrl);

                        } else {
                            return redirect('/user/invoice/cancel1/' . $cmd)->with([
                                'message' => language_data('Invalid transaction URL, cannot continue'),
                                'message_important' => true
                            ]);
                        }
                    }
                } else {
                    $error = "Invalid status in from Paynow, cannot continue.";
                    return redirect('/user/invoice/cancel1/' . $cmd)->with([
                        'message' => $error,
                        'message_important' => true
                    ]);
                }

            } else {
                $error = curl_error($ch);
                return redirect('/user/invoice/cancel1/' . $cmd)->with([
                    'message' => $error,
                    'message_important' => true
                ]);
            }
        }


        if ($gateway == 'manualpayment') {
            $details = $gat_info->value;

            return view('client1.bank-details', compact('details'));
        }
    }

//======================================================================
// cancelledInvoice Function Start Here
//======================================================================
    public function cancelledInvoice($id = '')
    {
        return redirect('user/invoices/view1/' . $id)->with([
            'message' => language_data('Cancelled the Payment')
        ]);
    }

//======================================================================
// successInvoice Function Start Here
//======================================================================
    public function successInvoice($token, $id)
    {

        $get_token = Auth::guard('client')->user()->pwresetexpiry;

        if ($get_token != $token) {
            return redirect('user/invoices/view1/' . $id)->with([
                'message' => language_data('Cancelled the Payment')
            ]);
        }

        $invoice = Invoices::find($id);

        if ($invoice) {

            $payment_id = \Session::get('paypal_payment_id');

            if ($payment_id) {
                \Session::forget('paypal_payment_id');
                if (empty($request->get('PayerID')) || empty($request->get('token'))) {
                    return redirect('user/invoices/view1/' . $id)->with([
                        'message' => 'Payment failed',
                        'message_important' => true
                    ]);
                }

                $payment   = Payment::get($payment_id, $this->_api_context);
                $execution = new PaymentExecution();
                $execution->setPayerId($request->get('PayerID'));

                $result = $payment->execute($execution, $this->_api_context);
                if ($result->getState() == 'approved') {
                    $invoice->datepaid = date('Y-m-d');
                    $invoice->status   = 'Paid';
                    $invoice->save();
                    return redirect('user/invoices/view1/' . $id)->with([
                        'message' => language_data('Invoice paid successfully')
                    ]);
                }
                return redirect('user/invoices/view1/' . $id)->with([
                    'message' => 'Payment failed',
                    'message_important' => true
                ]);
            }

            $invoice->datepaid = date('Y-m-d');
            $invoice->status   = 'Paid';
            $invoice->save();
            return redirect('user/invoices/view1/' . $id)->with([
                'message' => language_data('Invoice paid successfully')
            ]);

        } else {
            return redirect('user/invoices/all1')->with([
                'message' => language_data('Invoice paid successfully')
            ]);
        }
    }

//======================================================================
// payWithStripe Function Start Here
//======================================================================
    public function payWithStripe(Request $request)
    {

        $cmd      = $request->get('cmd');
        $invoice  = Invoices::find($cmd);
        $gat_info = PaymentGateways::where('settings', 'stripe')->first();
        $stripe   = Stripe::make($gat_info->extra_value, '2016-07-06');
        $client   = client_info($invoice->cl_id);
        $email    = $client->email;

        try {
            $customer = $stripe->customers()->create([
                'email' => $email,
                'source' => $request->stripeToken
            ]);

            $customer_id = $customer['id'];

            $meta_data = [
                'customer_name' => $client->fname . ' ' . $client->lname,
                'country' => $client->country,
                'ip_address' => \request()->ip()
            ];

            if ($client->address1) {
                $meta_data['address'] = $client->address1 . ' ' . $client->address2;
            }
            if ($client->city) {
                $meta_data['city'] = $client->city;
            }
            if ($client->postcode) {
                $meta_data['postcode'] = $client->postcode;
            }

            $stripe->charges()->create([
                'customer' => $customer_id,
                'currency' => app_config('Currency'),
                'amount' => $invoice->total,
                'receipt_email' => $email,
                'metadata' => $meta_data
            ]);

            $invoice->datepaid = date('Y-m-d');
            $invoice->status   = 'Paid';
            $invoice->save();

            return redirect('user/invoices/view1/' . $cmd)->with([
                'message' => language_data('Invoice paid successfully')
            ]);

        } catch (StripeException $e) {
            return redirect('user/invoices/view1/' . $cmd)->with([
                'message' => $e->getMessage(),
                'message_important' => true
            ]);
        }
    }

//======================================================================
// purchaseSMSPlanPost Function Start Here
//======================================================================
    public function purchaseSMSPlanPost(Request $request)
    {

        $cmd = $request->get('cmd');
        if ($request->gateway == '') {
            return redirect('user/sms/purchase_custom_plan1/' . $cmd)->with([
                'message' => language_data('Payment gateway required', Auth::guard('client')->user()->lan_id),
                'message_important' => true
            ]);
        }

        $gateway  = $request->get('gateway');
        $gat_info = PaymentGateways::where('settings', $gateway)->first();
        $sms_plan = SMSPricePlan::find($cmd);

        $token = date('Ymds');

        Client::find(Auth::guard('client')->user()->id)->update([
            'pwresetexpiry' => $token
        ]);

        if ($gateway == 'paypal') {
            $payer = new Payer();
            $payer->setPaymentMethod('paypal');

            $item = new Item();
            $item->setName($sms_plan->plan_name)
                ->setCurrency(app_config('Currency'))
                ->setQuantity('1')
                ->setPrice($sms_plan->price);

            $item_list = new ItemList();
            $item_list->setItems(array($item));

            $amount = new Amount();
            $amount->setCurrency(app_config('Currency'))
                ->setTotal($sms_plan->price);

            $transaction = new Transaction();
            $transaction->setAmount($amount)
                ->setItemList($item_list)
                ->setDescription('Purchase ' . $sms_plan->plan_name . ' Plan');

            $redirect_urls = new RedirectUrls();
            $redirect_urls->setReturnUrl(url('/user/sms/purchase-plan/success1/' . $token . '/' . $cmd))/** Specify return URL **/
            ->setCancelUrl(url('/user/sms/purchase-plan/cancel1/' . $cmd));

            $payment = new Payment();
            $payment->setIntent('Sale')
                ->setPayer($payer)
                ->setRedirectUrls($redirect_urls)
                ->setTransactions(array($transaction));

            try {
                $payment->create($this->_api_context);
            } catch (PayPalConnectionException $ex) {

                return redirect('user/sms/sms-plan-feature1/' . $cmd)->with([
                    'message' => $ex->getMessage(),
                    'message_important' => true
                ]);
            }
            foreach ($payment->getLinks() as $link) {
                if ($link->getRel() == 'approval_url') {
                    $redirect_url = $link->getHref();
                    break;
                }
            }
            /** add payment ID to session **/
            \Session::put('paypal_payment_id', $payment->getId());
            if (isset($redirect_url)) {
                /** redirect to paypal **/
                return \Redirect::away($redirect_url);
            }

            return redirect('user/sms/purchase_custom_plan1/' . $cmd)->with([
                'message' => 'Something went wrong. Please try again',
                'message_important' => true
            ]);

        }

        if ($gateway == '2checkout') {
            require_once app_path('Classes/TwoCheckout.php');

            $checkout = new TwoCheckout();

            $checkout->param('sid', $gat_info->value);
            $checkout->param('return_url', url('/user/sms/purchase-plan/success1/' . $token . '/' . $cmd));
            $checkout->param('li_0_name', $sms_plan->plan_name);
            $checkout->param('li_0_price', $sms_plan->price);
            $checkout->param('li_0_quantity', 1);
            $checkout->param('card_holder_name', Auth::guard('client')->user()->fname . ' ' . Auth::guard('client')->user()->lname);
            $checkout->param('country', Auth::guard('client')->user()->country);
            $checkout->param('email', Auth::guard('client')->user()->email);
            $checkout->param('currency_code', app_config('Currency'));
            $checkout->gw_submit();
        }

        if ($gateway == 'payu') {

            $signature = "$gat_info->extra_value~$gat_info->value~smsplan$sms_plan->id~$sms_plan->price~" . app_config('Currency');
            $signature = md5($signature);

            $order = array(
                'merchantId' => $gat_info->value,
                'ApiKey' => $gat_info->extra_value,
                'referenceCode' => 'smsplan' . $sms_plan->id,
                'description' => 'Purchase ' . $sms_plan->plan_name . ' Plan',
                'amount' => $sms_plan->price,
                'tax' => '0',
                'taxReturnBase' => '0',
                'currency' => app_config('Currency'),
                'buyerEmail' => Auth::guard('client')->user()->email,
                'test' => '0',
                'signature' => $signature,
                'confirmationUrl' => url('/user/sms/purchase-plan/success1/' . $token . '/' . $cmd),
                'responseUrl' => url('/user/sms/purchase-plan/cancel1/' . $cmd),
            );
            ?>

            <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
            <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                <title>Please wait while you're redirected</title>
                <style type="text/css">
                    #redirect {
                        background: #f1f1f1;
                        font-family: Helvetica, Arial, sans-serif
                    }

                    #redirect-container {
                        width: 410px;
                        margin: 130px auto 0;
                        background: #fff;
                        border: 1px solid #b5b5b5;
                        -moz-border-radius: 5px;
                        -webkit-border-radius: 5px;
                        border-radius: 5px;
                        text-align: center
                    }

                    #redirect-container h1 {
                        font-size: 22px;
                        color: #5f5f5f;
                        font-weight: normal;
                        margin: 22px 0 26px 0;
                        padding: 0
                    }

                    #redirect-container p {
                        font-size: 13px;
                        color: #454545;
                        margin: 0 0 12px 0;
                        padding: 0
                    }

                    #redirect-container img {
                        margin: 0 0 35px 0;
                        padding: 0
                    }

                    .ajaxLoader {
                        margin: 80px 153px
                    }
                </style>
                <script type="text/javascript">
                    function timedText() {
                        setTimeout('msg1()', 2000)
                        setTimeout('msg2()', 4000)
                        setTimeout('document.MetaRefreshForm.submit()', 4000)
                    }

                    function msg1() {
                        document.getElementById('redirect-message').firstChild.nodeValue = 'Preparing Data...'
                    }

                    function msg2() {
                        document.getElementById('redirect-message').firstChild.nodeValue = 'Redirecting...'
                    }
                </script>
            </head>
            <?php echo "<body onLoad=\"document.forms['gw'].submit();\">\n"; ?>
            <div id="redirect-container">
                <h1>Please wait while you&rsquo;re redirected</h1>
                <p class="redirect-message" id="redirect-message">Loading Data...</p>
                <script type="text/javascript">timedText()</script>
            </div>
            <form method="post" action="https://gateway.payulatam.com/ppp-web-gateway" name="gw">
                <?php
                foreach ($order as $name => $value) {
                    echo "<input type=\"hidden\" name=\"$name\" value=\"$value\"/>\n";
                }
                ?>
            </form>
            </body>
            </html>
            <?php
        }

        if ($gateway == 'stripe') {
            $plan_name     = $sms_plan->plan_name;
            $stripe_amount = $sms_plan->price * 100;
            $post_url      = 'user/sms/purchase-with-stripe';

            return view('client.stripe', compact('gat_info', 'stripe_amount', 'cmd', 'plan_name', 'post_url'));

        }

        if ($gateway == 'slydepay') {

            require_once(app_path('libraray/vendor/autoload.php'));

            $slydepay     = new Slydepay($gat_info->value, $gat_info->extra_value);
            $orderItems   = new OrderItems([
                new OrderItem($sms_plan->id, "SMS Plan Name# $sms_plan->plan_name", $sms_plan->price, 1)
            ]);
            $shippingCost = 0;
            $tax          = 0;
            $order_id     = _raid(5);

            $order = Order::createWithId($orderItems, $order_id, $shippingCost, $tax, $sms_plan->id);

            try {
                $response = $slydepay->processPaymentOrder($order);
                return redirect($response->redirectUrl());
            } catch (ProcessPaymentException $e) {
                return redirect('user/sms/sms-plan-feature1/' . $cmd)->with([
                    'message' => $e->getMessage(),
                    'message_important' => true
                ]);
            }
        }

        if ($gateway == 'paystack') {

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.paystack.co/transaction/initialize",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode([
                    'amount' => $sms_plan->price * 100,
                    'email' => Auth::guard('client')->user()->email,
                    'metadata' => [
                        'plan_id' => $cmd,
                        'request_type' => 'purchase_plan',
                    ]
                ]),
                CURLOPT_HTTPHEADER => [
                    "authorization: Bearer " . getenv('PAYSTACK_SECRET_KEY'),
                    "content-type: application/json",
                    "cache-control: no-cache"
                ],
            ));

            $response = curl_exec($curl);
            $err      = curl_error($curl);


            curl_close($curl);

            if ($response === false) {
                return redirect('user/sms/purchase_custom_plan1/' . $cmd)->with([
                    'message' => 'Php curl show false value. Please contact with your provider',
                    'message_important' => true
                ]);
            }

            if ($err) {
                return redirect('user/sms/purchase_custom_plan1/' . $cmd)->with([
                    'message' => $err,
                    'message_important' => true
                ]);
            }

            $tranx = json_decode($response);

            if ($tranx->status != 1) {
                return redirect('user/sms/sms-plan-feature1/' . $cmd)->with([
                    'message' => $tranx->message,
                    'message_important' => true
                ]);
            }

            return redirect($tranx->data->authorization_url);

        }

        if ($gateway == 'paynow') {
            require_once app_path('Classes/Paynow.php');

            $paynow = new Paynow();

            //set POST variables
            $values = array(
                'resulturl' => url('/user/sms/purchase-plan/paynow1/' . $cmd),
                'reference' => _raid(10),
                'amount' => $sms_plan->price,
                'id' => $gat_info->value,
                'status' => $sms_plan->plan_name
            );

            $fields_string = $paynow->CreateMsg($values, $gat_info->extra_value);

            //open connection
            $ch  = curl_init();
            $url = 'https://www.paynow.co.zw/interface/initiatetransaction';

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $result = curl_exec($ch);

            //close connection
            curl_close($ch);

            if ($result) {
                $msg = $paynow->ParseMsg($result);

                //first check status, take appropriate action
                if (is_array($msg) && array_key_exists('status', $msg) && $msg["status"] == 'Error') {
                    return redirect('user/sms/purchase_custom_plan/' . $cmd)->with([
                        'message' => $msg['error'],
                        'message_important' => true
                    ]);

                } else if (is_array($msg) && array_key_exists('status', $msg) && $msg["status"] == 'Ok') {

                    //second, check hash
                    $validateHash = $paynow->CreateHash($msg, $gat_info->extra_value);
                    if ($validateHash != $msg["hash"]) {
                        $error = "Paynow reply hashes do not match : " . $validateHash . " - " . $msg["hash"];
                        return redirect('user/sms/purchase_custom_plan/' . $cmd)->with([
                            'message' => $error,
                            'message_important' => true
                        ]);

                    } else {

                        if (is_array($msg) && array_key_exists('browserurl', $msg)) {
                            $theProcessUrl = $msg["browserurl"];

                            $orders_data_file = storage_path('PayNowTransaction.ini');
                            //1. Saving mine to a PHP.INI type of file, you should save it to a db etc
                            $orders_array = array();
                            if (file_exists($orders_data_file)) {
                                $orders_array = parse_ini_file($orders_data_file, true);
                            }

                            $orders_array['PurchasePlanID_' . $cmd] = $msg;

                            $paynow->write_php_ini($orders_array, $orders_data_file, true);


                            return redirect($theProcessUrl);

                        } else {
                            return redirect('user/sms/purchase_custom_plan1/' . $cmd)->with([
                                'message' => language_data('Invalid transaction URL, cannot continue'),
                                'message_important' => true
                            ]);
                        }
                    }
                } else {
                    $error = "Invalid status in from Paynow, cannot continue.";
                    return redirect('user/sms/purchase_custom_plan1/' . $cmd)->with([
                        'message' => $error,
                        'message_important' => true
                    ]);
                }

            } else {
                $error = curl_error($ch);
                return redirect('user/sms/purchase_custom_plan1/' . $cmd)->with([
                    'message' => $error,
                    'message_important' => true
                ]);
            }
        }

        if ($gateway == 'webxpay') {
            require_once(app_path('libraray/webxpay/Crypt/RSA.php'));

            //initialize RSA
            $rsa = new \Crypt_RSA();
            // unique_order_id|total_amount
            $plaintext = "$sms_plan->id|$sms_plan->price";
            $publickey = $gat_info->extra_value;

            $rsa->loadKey($publickey);

            $encrypt = $rsa->encrypt($plaintext);
            //encode for data passing
            $payment = base64_encode($encrypt);

            //custom fields
            //cus_1|cus_2|cus_3|cus_4
            $custom_fields = base64_encode("purchase_plan|$sms_plan->id");

            $order = array(
                'first_name' => Auth::guard('client')->user()->fname,
                'last_name' => Auth::guard('client')->user()->lname,
                'email' => Auth::guard('client')->user()->email,
                'contact_number' => Auth::guard('client')->user()->phone,
                'address_line_one' => Auth::guard('client')->user()->address1,
                'address_line_two' => Auth::guard('client')->user()->address2,
                'city' => Auth::guard('client')->user()->city,
                'state' => Auth::guard('client')->user()->state,
                'postal_code' => Auth::guard('client')->user()->postcode,
                'country' => Auth::guard('client')->user()->country,
                'process_currency' => app_config('Currency'),
                'cms' => 'PHP',
                'secret_key' => $gat_info->value,
                'payment' => $payment,
                'custom_fields' => $custom_fields,
            );

            ?>

            <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
            <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                <title>Please wait while you're redirected</title>
                <style type="text/css">
                    #redirect {
                        background: #f1f1f1;
                        font-family: Helvetica, Arial, sans-serif
                    }

                    #redirect-container {
                        width: 410px;
                        margin: 130px auto 0;
                        background: #fff;
                        border: 1px solid #b5b5b5;
                        -moz-border-radius: 5px;
                        -webkit-border-radius: 5px;
                        border-radius: 5px;
                        text-align: center
                    }

                    #redirect-container h1 {
                        font-size: 22px;
                        color: #5f5f5f;
                        font-weight: normal;
                        margin: 22px 0 26px 0;
                        padding: 0
                    }

                    #redirect-container p {
                        font-size: 13px;
                        color: #454545;
                        margin: 0 0 12px 0;
                        padding: 0
                    }

                    #redirect-container img {
                        margin: 0 0 35px 0;
                        padding: 0
                    }

                    .ajaxLoader {
                        margin: 80px 153px
                    }
                </style>
                <script type="text/javascript">
                    function timedText() {
                        setTimeout('msg1()', 2000)
                        setTimeout('msg2()', 4000)
                        setTimeout('document.MetaRefreshForm.submit()', 4000)
                    }

                    function msg1() {
                        document.getElementById('redirect-message').firstChild.nodeValue = 'Preparing Data...'
                    }

                    function msg2() {
                        document.getElementById('redirect-message').firstChild.nodeValue = 'Redirecting...'
                    }
                </script>
            </head>
            <body>
            <?php echo "<body onLoad=\"document.forms['gw'].submit();\">\n"; ?>
            <div id="redirect-container">
                <h1>Please wait while you&rsquo;re redirected</h1>
                <p class="redirect-message" id="redirect-message">Loading Data...</p>
                <script type="text/javascript">timedText()</script>
            </div>
            <form method="post" action="https://webxpay.com/index.php?route=checkout/billing" name="gw">
                <?php
                foreach ($order as $name => $value) {
                    echo "<input type=\"hidden\" name=\"$name\" value=\"$value\"/>\n";
                }

                ?>
            </form>
            </body>
            </html>
            <?php

        }

        if ($gateway == 'coinpayments') {


            $order = array(
                'merchant' => $gat_info->value,
                'cmd' => '_pay',
                'reset' => '1',
                'item_name' => "Purchase " . $sms_plan->plan_name,
                'amountf' => $sms_plan->price,
                'allow_extra' => '1',
                'currency' => app_config('Currency'),
                'want_shipping' => '0',
                'success_url' => url('/user/sms/purchase-plan/success/' . $token . '/' . $cmd),
                'cancel_url' => url('/user/sms/purchase-plan/cancel/' . $cmd),
            );
            ?>

            <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
            <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                <title>Please wait while you're redirected</title>
                <style type="text/css">
                    #redirect {
                        background: #f1f1f1;
                        font-family: Helvetica, Arial, sans-serif
                    }

                    #redirect-container {
                        width: 410px;
                        margin: 130px auto 0;
                        background: #fff;
                        border: 1px solid #b5b5b5;
                        -moz-border-radius: 5px;
                        -webkit-border-radius: 5px;
                        border-radius: 5px;
                        text-align: center
                    }

                    #redirect-container h1 {
                        font-size: 22px;
                        color: #5f5f5f;
                        font-weight: normal;
                        margin: 22px 0 26px 0;
                        padding: 0
                    }

                    #redirect-container p {
                        font-size: 13px;
                        color: #454545;
                        margin: 0 0 12px 0;
                        padding: 0
                    }

                    #redirect-container img {
                        margin: 0 0 35px 0;
                        padding: 0
                    }

                    .ajaxLoader {
                        margin: 80px 153px
                    }
                </style>
                <script type="text/javascript">
                    function timedText() {
                        setTimeout('msg1()', 2000)
                        setTimeout('msg2()', 4000)
                        setTimeout('document.MetaRefreshForm.submit()', 4000)
                    }

                    function msg1() {
                        document.getElementById('redirect-message').firstChild.nodeValue = 'Preparing Data...'
                    }

                    function msg2() {
                        document.getElementById('redirect-message').firstChild.nodeValue = 'Redirecting...'
                    }
                </script>
            </head>
            <body>
            <?php echo "<body onLoad=\"document.forms['gw'].submit();\">\n"; ?>
            <div id="redirect-container">
                <h1>Please wait while you&rsquo;re redirected</h1>
                <p class="redirect-message" id="redirect-message">Loading Data...</p>
                <script type="text/javascript">timedText()</script>
            </div>
            <form method="post" action="https://www.coinpayments.net/index.php" name="gw">
                <?php
                foreach ($order as $name => $value) {
                    echo "<input type=\"hidden\" name=\"$name\" value=\"$value\"/>\n";
                }

                ?>
            </form>
            </body>
            </html>
            <?php
        }

        if ($gateway == 'manualpayment') {
            $details = $gat_info->value;
            return view('client.bank-details', compact('details'));
        }
    }


//======================================================================
// cancelledPurchase Function Start Here
//======================================================================
    public function cancelledPurchase($id = '')
    {
        return redirect('user/sms/purchase_custom_plan/' . $id)->with([
            'message' => language_data('Cancelled the Payment')
        ]);
    }

//======================================================================
// successPurchase Function Start Here
//======================================================================
    public function successPurchase(Request $request)
    {
        $id      = $request->get('cmd');
        if ($id) {
            $sms_plan = SMSPricePlan::find($id);
            $client = Client::find(Auth::guard('client')->user()->id);


            $inv               = new Invoices();
            $inv->cl_id        = $client->id;
            $inv->client_name  = $client->fname . ' ' . $client->lname;
            $inv->created_by   = 1;
            $inv->created      = date('Y-m-d');
            $inv->duedate      = date('Y-m-d');
            $inv->datepaid     = date('Y-m-d');
            $inv->subtotal     = $sms_plan->price;
            $inv->total        = $sms_plan->price;
            $inv->status       = 'Unpaid';
            $inv->pmethod      = '';
            $inv->recurring    = '0';
            $inv->bill_created = 'yes';
            $inv->note         = '';
            $inv->save();
            $inv_id = $inv->id;

            $d           = new InvoiceItems();
            $d->inv_id   = $inv_id;
            $d->cl_id    = $client->id;
            $d->item     = $sms_plan->plan_name . ' Plan';
            $d->qty      = '1';
            $d->price    = $sms_plan->price;
            $d->tax      = '0';
            $d->discount = '0';
            $d->subtotal = $sms_plan->price;
            $d->total    = $sms_plan->price;
            $d->save();
            // return  $inv_id;

            return redirect('user/invoices/view1/'.$inv_id);
                  

            

             }
        } 
    

//======================================================================
// purchaseWithStripe Function Start Here
//======================================================================
    public function purchaseWithStripe(Request $request)
    {
        $cmd      = $request->get('cmd');
        $sms_plan = SMSPricePlan::find($cmd);

        $get_balance = SMSPlanFeature::where('pid', $cmd)->first();
        $sms_balance = $get_balance->feature_value;

        $gat_info = PaymentGateways::where('settings', 'stripe')->first();
        $stripe   = Stripe::make($gat_info->extra_value, '2016-07-06');

        $client = Client::find(Auth::guard('client')->user()->id);

        $email = $client->email;

        try {
            $customer = $stripe->customers()->create([
                'email' => $email,
                'source' => $request->stripeToken
            ]);

            $customer_id = $customer['id'];

            $meta_data = [
                'customer_name' => $client->fname . ' ' . $client->lname,
                'country' => $client->country,
                'ip_address' => \request()->ip()
            ];

            if ($client->address1) {
                $meta_data['address'] = $client->address1 . ' ' . $client->address2;
            }
            if ($client->city) {
                $meta_data['city'] = $client->city;
            }
            if ($client->postcode) {
                $meta_data['postcode'] = $client->postcode;
            }

            $stripe->charges()->create([
                'customer' => $customer_id,
                'currency' => app_config('Currency'),
                'amount' => $sms_plan->price,
                'receipt_email' => $email,
                'metadata' => $meta_data
            ]);


            $total_balance     = $client->sms_limit + $sms_balance;
            $client->sms_limit = $total_balance;
            $client->save();

            $inv               = new Invoices();
            $inv->cl_id        = $client->id;
            $inv->client_name  = $client->fname . ' ' . $client->lname;
            $inv->created_by   = 1;
            $inv->created      = date('Y-m-d');
            $inv->duedate      = date('Y-m-d');
            $inv->datepaid     = date('Y-m-d');
            $inv->subtotal     = $sms_plan->price;
            $inv->total        = $sms_plan->price;
            $inv->status       = 'Paid';
            $inv->pmethod      = '';
            $inv->recurring    = '0';
            $inv->bill_created = 'yes';
            $inv->note         = '';
            $inv->save();
            $inv_id = $inv->id;

            $d           = new InvoiceItems();
            $d->inv_id   = $inv_id;
            $d->cl_id    = $client->id;
            $d->item     = $sms_plan->plan_name . ' Plan';
            $d->qty      = '1';
            $d->price    = $sms_plan->price;
            $d->tax      = '0';
            $d->discount = '0';
            $d->subtotal = $sms_plan->price;
            $d->total    = $sms_plan->price;
            $d->save();

            return redirect('user/invoices/all')->with([
                'message' => language_data('Purchase successfully.Wait for administrator response', Auth::guard('client')->user()->lan_id)
            ]);

        } catch (StripeException $e) {
            return redirect('user/sms/sms-plan-feature/' . $cmd)->with([
                'message' => $e->getMessage(),
                'message_important' => true
            ]);
        }
    }

//======================================================================
// slydepayReceiveCallback Function Start Here
//======================================================================
    public function slydepayReceiveCallback()
    {
        return redirect('dashboard1')->with([
            'message' => language_data('Purchase successfully.Wait for administrator response', Auth::guard('client')->user()->lan_id)
        ]);
    }


//======================================================================
// purchaseSMSPlanPost Function Start Here
//======================================================================
    public function postBuyUnit(Request $request)
    {

        if ($request->gateway == '') {
            return redirect('user/sms/buy-unit')->with([
                'message' => language_data('Payment gateway required', Auth::guard('client')->user()->lan_id),
                'message_important' => true
            ]);
        }

        $number_unit = $request->input('number_unit');

        $gateway  = $request->get('gateway');
        $gat_info = PaymentGateways::where('settings', $gateway)->first();

        $token = date('Ymds');

        Client::find(Auth::guard('client')->user()->id)->update([
            'pwresetexpiry' => $token
        ]);

        if ($gateway == 'paypal') {
            $payer = new Payer();
            $payer->setPaymentMethod('paypal');

            $item = new Item();
            $item->setName('Purchase SMS Unit')
                ->setCurrency(app_config('Currency'))
                ->setQuantity('1')
                ->setPrice($request->total);

            $item_list = new ItemList();
            $item_list->setItems(array($item));


            $amount = new Amount();
            $amount->setCurrency(app_config('Currency'))
                ->setTotal($request->total);

            $transaction = new Transaction();
            $transaction->setAmount($amount)
                ->setItemList($item_list)
                ->setDescription('Purchase SMS Unit');

            $redirect_urls = new RedirectUrls();
            $redirect_urls->setReturnUrl(url('/user/sms/buy-unit/success/' . $token . '/' . $number_unit))/** Specify return URL **/
            ->setCancelUrl(url('/user/sms/buy-unit/cancel'));

            $payment = new Payment();
            $payment->setIntent('Sale')
                ->setPayer($payer)
                ->setRedirectUrls($redirect_urls)
                ->setTransactions(array($transaction));

            try {
                $payment->create($this->_api_context);
            } catch (PayPalConnectionException $ex) {

                return redirect('user/sms/buy-unit')->with([
                    'message' => $ex->getMessage(),
                    'message_important' => true
                ]);
            }
            foreach ($payment->getLinks() as $link) {
                if ($link->getRel() == 'approval_url') {
                    $redirect_url = $link->getHref();
                    break;
                }
            }
            /** add payment ID to session **/
            \Session::put('paypal_payment_id', $payment->getId());
            if (isset($redirect_url)) {
                /** redirect to paypal **/
                return \Redirect::away($redirect_url);
            }

            return redirect('user/sms/buy-unit')->with([
                'message' => 'Something went wrong. Please try again',
                'message_important' => true
            ]);

        }


        if ($gateway == 'coinpayments') {


            $order = array(
                'merchant' => $gat_info->value,
                'cmd' => '_pay',
                'reset' => '1',
                'item_name' => 'Purchase unit',
                'amountf' => $request->total,
                'allow_extra' => '1',
                'currency' => app_config('Currency'),
                'want_shipping' => '0',
                'success_url' => url('/user/sms/buy-unit/success/' . $token . '/' . $number_unit),
                'cancel_url' => url('/user/sms/buy-unit/cancel'),
            );
            ?>

            <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
            <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                <title>Please wait while you're redirected</title>
                <style type="text/css">
                    #redirect {
                        background: #f1f1f1;
                        font-family: Helvetica, Arial, sans-serif
                    }

                    #redirect-container {
                        width: 410px;
                        margin: 130px auto 0;
                        background: #fff;
                        border: 1px solid #b5b5b5;
                        -moz-border-radius: 5px;
                        -webkit-border-radius: 5px;
                        border-radius: 5px;
                        text-align: center
                    }

                    #redirect-container h1 {
                        font-size: 22px;
                        color: #5f5f5f;
                        font-weight: normal;
                        margin: 22px 0 26px 0;
                        padding: 0
                    }

                    #redirect-container p {
                        font-size: 13px;
                        color: #454545;
                        margin: 0 0 12px 0;
                        padding: 0
                    }

                    #redirect-container img {
                        margin: 0 0 35px 0;
                        padding: 0
                    }

                    .ajaxLoader {
                        margin: 80px 153px
                    }
                </style>
                <script type="text/javascript">
                    function timedText() {
                        setTimeout('msg1()', 2000)
                        setTimeout('msg2()', 4000)
                        setTimeout('document.MetaRefreshForm.submit()', 4000)
                    }

                    function msg1() {
                        document.getElementById('redirect-message').firstChild.nodeValue = 'Preparing Data...'
                    }

                    function msg2() {
                        document.getElementById('redirect-message').firstChild.nodeValue = 'Redirecting...'
                    }
                </script>
            </head>
            <body>
            <?php echo "<body onLoad=\"document.forms['gw'].submit();\">\n"; ?>
            <div id="redirect-container">
                <h1>Please wait while you&rsquo;re redirected</h1>
                <p class="redirect-message" id="redirect-message">Loading Data...</p>
                <script type="text/javascript">timedText()</script>
            </div>
            <form method="post" action="https://www.coinpayments.net/index.php" name="gw">
                <?php
                foreach ($order as $name => $value) {
                    echo "<input type=\"hidden\" name=\"$name\" value=\"$value\"/>\n";
                }

                ?>
            </form>
            </body>
            </html>
            <?php
        }

        if ($gateway == 'payu') {

            $signature = "$gat_info->extra_value~$gat_info->value~buyunit" . _raid(5) . "~$request->total~" . app_config('Currency');
            $signature = md5($signature);

            $order = array(
                'merchantId' => $gat_info->value,
                'ApiKey' => $gat_info->extra_value,
                'referenceCode' => 'buyunit' . _raid(5),
                'description' => 'Purchase SMS Unit',
                'amount' => $request->total,
                'tax' => '0',
                'taxReturnBase' => '0',
                'currency' => app_config('Currency'),
                'buyerEmail' => Auth::guard('client')->user()->email,
                'test' => '0',
                'signature' => $signature,
                'confirmationUrl' => url('/user/sms/buy-unit/success/' . $token . '/' . $number_unit),
                'responseUrl' => url('/user/sms/buy-unit/success/' . $token . '/' . $number_unit),
            );
            ?>

            <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
            <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                <title>Please wait while you're redirected</title>
                <style type="text/css">
                    #redirect {
                        background: #f1f1f1;
                        font-family: Helvetica, Arial, sans-serif
                    }

                    #redirect-container {
                        width: 410px;
                        margin: 130px auto 0;
                        background: #fff;
                        border: 1px solid #b5b5b5;
                        -moz-border-radius: 5px;
                        -webkit-border-radius: 5px;
                        border-radius: 5px;
                        text-align: center
                    }

                    #redirect-container h1 {
                        font-size: 22px;
                        color: #5f5f5f;
                        font-weight: normal;
                        margin: 22px 0 26px 0;
                        padding: 0
                    }

                    #redirect-container p {
                        font-size: 13px;
                        color: #454545;
                        margin: 0 0 12px 0;
                        padding: 0
                    }

                    #redirect-container img {
                        margin: 0 0 35px 0;
                        padding: 0
                    }

                    .ajaxLoader {
                        margin: 80px 153px
                    }
                </style>
                <script type="text/javascript">
                    function timedText() {
                        setTimeout('msg1()', 2000)
                        setTimeout('msg2()', 4000)
                        setTimeout('document.MetaRefreshForm.submit()', 4000)
                    }

                    function msg1() {
                        document.getElementById('redirect-message').firstChild.nodeValue = 'Preparing Data...'
                    }

                    function msg2() {
                        document.getElementById('redirect-message').firstChild.nodeValue = 'Redirecting...'
                    }
                </script>
            </head>
            <?php echo "<body onLoad=\"document.forms['gw'].submit();\">\n"; ?>
            <div id="redirect-container">
                <h1>Please wait while you&rsquo;re redirected</h1>
                <p class="redirect-message" id="redirect-message">Loading Data...</p>
                <script type="text/javascript">timedText()</script>
            </div>
            <form method="post" action="https://gateway.payulatam.com/ppp-web-gateway" name="gw">
                <?php
                foreach ($order as $name => $value) {
                    echo "<input type=\"hidden\" name=\"$name\" value=\"$value\"/>\n";
                }
                ?>
            </form>
            </body>
            </html>
            <?php
        }

        if ($gateway == 'stripe') {
            $cmd           = $number_unit;
            $plan_name     = 'Purchase SMS Unit';
            $stripe_amount = $request->total * 100;
            $post_url      = 'user/sms/buy-unit-with-stripe';
            return view('client.stripe', compact('gat_info', 'stripe_amount', 'cmd', 'plan_name', 'post_url'));

        }

        if ($gateway == '2checkout') {
            require_once app_path('Classes/TwoCheckout.php');

            $checkout = new TwoCheckout();

            $checkout->param('sid', $gat_info->value);
            $checkout->param('return_url', url('/user/sms/buy-unit/success/' . $token . '/' . $number_unit));
            $checkout->param('li_0_name', 'Purchase SMS Unit');
            $checkout->param('li_0_price', $request->total);
            $checkout->param('li_0_quantity', 1);
            $checkout->param('card_holder_name', Auth::guard('client')->user()->fname . ' ' . Auth::guard('client')->user()->lname);
            $checkout->param('country', Auth::guard('client')->user()->country);
            $checkout->param('email', Auth::guard('client')->user()->email);
            $checkout->param('currency_code', app_config('Currency'));
            $checkout->gw_submit();
        }

        if ($gateway == 'slydepay') {

            require_once(app_path('libraray/vendor/autoload.php'));

            $slydepay     = new Slydepay($gat_info->value, $gat_info->extra_value);
            $total        = number_format((float)$request->total, '2', '.', '');
            $orderItems   = new OrderItems([
                new OrderItem(_raid(5), "Purchase SMS Unit", $total, 1)
            ]);
            $shippingCost = 0;
            $tax          = 0;
            $order_id     = _raid(5);

            $order = Order::createWithId($orderItems, $order_id, $shippingCost, $tax, $order_id);

            try {
                $response = $slydepay->processPaymentOrder($order);
                return redirect($response->redirectUrl());
            } catch (ProcessPaymentException $e) {
                return redirect('/user/sms/buy-unit/cancel')->with([
                    'message' => $e->getMessage(),
                    'message_important' => true
                ]);
            }
        }

        if ($gateway == 'manualpayment') {
            $details = $gat_info->value;
            return view('client.bank-details', compact('details'));
        }


        if ($gateway == 'paystack') {

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.paystack.co/transaction/initialize",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode([
                    'amount' => $request->total * 100,
                    'email' => Auth::guard('client')->user()->email,
                    'metadata' => [
                        'unit_number' => $request->number_unit,
                        'unit_price' => $request->unit_price,
                        'pay_amount' => $request->pay_amount,
                        'trans_fee' => $request->trans_fee,
                        'request_type' => 'buy_unit',
                    ]
                ]),
                CURLOPT_HTTPHEADER => [
                    "authorization: Bearer " . getenv('PAYSTACK_SECRET_KEY'),
                    "content-type: application/json",
                    "cache-control: no-cache"
                ],
            ));

            $response = curl_exec($curl);
            $err      = curl_error($curl);

            curl_close($curl);


            if ($response === false) {
                return redirect('user/sms/buy-unit')->with([
                    'message' => 'Php curl show false value. Please contact with your provider',
                    'message_important' => true
                ]);
            }

            if ($err) {
                return redirect('user/sms/buy-unit')->with([
                    'message' => $err,
                    'message_important' => true
                ]);
            }

            $tranx = json_decode($response);

            if ($tranx->status != 1) {
                return redirect('user/sms/buy-unit')->with([
                    'message' => $tranx->message,
                    'message_important' => true
                ]);
            }

            return redirect($tranx->data->authorization_url);

        }

        if ($gateway == 'paynow') {
            require_once app_path('Classes/Paynow.php');

            $paynow = new Paynow();

            $ref         = _raid(10);
            $number_unit = $ref . $number_unit;

            //set POST variables
            $values = array(
                'resulturl' => url('/user/sms/buy-unit/paynow/' . $number_unit),
                'reference' => $ref,
                'amount' => $request->total,
                'id' => $gat_info->value,
                'status' => 'Purchase sms unit'
            );

            $fields_string = $paynow->CreateMsg($values, $gat_info->extra_value);

            //open connection
            $ch  = curl_init();
            $url = 'https://www.paynow.co.zw/interface/initiatetransaction';

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $result = curl_exec($ch);

            //close connection
            curl_close($ch);

            if ($result) {
                $msg = $paynow->ParseMsg($result);

                //first check status, take appropriate action
                if (is_array($msg) && array_key_exists('status', $msg) && $msg["status"] == 'Error') {
                    return redirect('user/sms/buy-unit')->with([
                        'message' => $msg['error'],
                        'message_important' => true
                    ]);

                } else if (is_array($msg) && array_key_exists('status', $msg) && $msg["status"] == 'Ok') {

                    //second, check hash
                    $validateHash = $paynow->CreateHash($msg, $gat_info->extra_value);
                    if ($validateHash != $msg["hash"]) {
                        $error = "Paynow reply hashes do not match : " . $validateHash . " - " . $msg["hash"];
                        return redirect('user/sms/buy-unit')->with([
                            'message' => $error,
                            'message_important' => true
                        ]);

                    } else {

                        if (is_array($msg) && array_key_exists('browserurl', $msg)) {
                            $theProcessUrl = $msg["browserurl"];

                            $orders_data_file = storage_path('PayNowTransaction.ini');
                            //1. Saving mine to a PHP.INI type of file, you should save it to a db etc
                            $orders_array = array();
                            if (file_exists($orders_data_file)) {
                                $orders_array = parse_ini_file($orders_data_file, true);
                            }

                            $orders_array['BuyUnitID_' . $number_unit] = $msg;

                            $paynow->write_php_ini($orders_array, $orders_data_file, true);


                            return redirect($theProcessUrl);

                        } else {
                            return redirect('user/sms/buy-unit')->with([
                                'message' => language_data('Invalid transaction URL, cannot continue'),
                                'message_important' => true
                            ]);
                        }
                    }
                } else {
                    $error = "Invalid status in from Paynow, cannot continue.";
                    return redirect('user/sms/buy-unit')->with([
                        'message' => $error,
                        'message_important' => true
                    ]);
                }

            } else {
                $error = curl_error($ch);
                return redirect('user/sms/buy-unit')->with([
                    'message' => $error,
                    'message_important' => true
                ]);
            }
        }


        if ($gateway == 'webxpay') {
            require_once(app_path('libraray/webxpay/Crypt/RSA.php'));

            //initialize RSA
            $rsa = new \Crypt_RSA();
            // unique_order_id|total_amount
            $plaintext = "$number_unit|$request->total";
            $publickey = $gat_info->extra_value;

            $rsa->loadKey($publickey);

            $encrypt = $rsa->encrypt($plaintext);
            //encode for data passing
            $payment = base64_encode($encrypt);

            //custom fields
            //cus_1|cus_2|cus_3|cus_4
            $custom_fields = base64_encode("buy_unit|$number_unit");

            $order = array(
                'first_name' => Auth::guard('client')->user()->fname,
                'last_name' => Auth::guard('client')->user()->lname,
                'email' => Auth::guard('client')->user()->email,
                'contact_number' => Auth::guard('client')->user()->phone,
                'address_line_one' => Auth::guard('client')->user()->address1,
                'address_line_two' => Auth::guard('client')->user()->address2,
                'city' => Auth::guard('client')->user()->city,
                'state' => Auth::guard('client')->user()->state,
                'postal_code' => Auth::guard('client')->user()->postcode,
                'country' => Auth::guard('client')->user()->country,
                'process_currency' => app_config('Currency'),
                'cms' => 'PHP',
                'secret_key' => $gat_info->value,
                'payment' => $payment,
                'custom_fields' => $custom_fields,
            );

            ?>

            <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
            <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                <title>Please wait while you're redirected</title>
                <style type="text/css">
                    #redirect {
                        background: #f1f1f1;
                        font-family: Helvetica, Arial, sans-serif
                    }

                    #redirect-container {
                        width: 410px;
                        margin: 130px auto 0;
                        background: #fff;
                        border: 1px solid #b5b5b5;
                        -moz-border-radius: 5px;
                        -webkit-border-radius: 5px;
                        border-radius: 5px;
                        text-align: center
                    }

                    #redirect-container h1 {
                        font-size: 22px;
                        color: #5f5f5f;
                        font-weight: normal;
                        margin: 22px 0 26px 0;
                        padding: 0
                    }

                    #redirect-container p {
                        font-size: 13px;
                        color: #454545;
                        margin: 0 0 12px 0;
                        padding: 0
                    }

                    #redirect-container img {
                        margin: 0 0 35px 0;
                        padding: 0
                    }

                    .ajaxLoader {
                        margin: 80px 153px
                    }
                </style>
                <script type="text/javascript">
                    function timedText() {
                        setTimeout('msg1()', 2000)
                        setTimeout('msg2()', 4000)
                        setTimeout('document.MetaRefreshForm.submit()', 4000)
                    }

                    function msg1() {
                        document.getElementById('redirect-message').firstChild.nodeValue = 'Preparing Data...'
                    }

                    function msg2() {
                        document.getElementById('redirect-message').firstChild.nodeValue = 'Redirecting...'
                    }
                </script>
            </head>
            <body>
            <?php echo "<body onLoad=\"document.forms['gw'].submit();\">\n"; ?>
            <div id="redirect-container">
                <h1>Please wait while you&rsquo;re redirected</h1>
                <p class="redirect-message" id="redirect-message">Loading Data...</p>
                <script type="text/javascript">timedText()</script>
            </div>
            <form method="post" action="https://webxpay.com/index.php?route=checkout/billing" name="gw">
                <?php
                foreach ($order as $name => $value) {
                    echo "<input type=\"hidden\" name=\"$name\" value=\"$value\"/>\n";
                }

                ?>
            </form>
            </body>
            </html>
            <?php

        }

    }


//======================================================================
// postPurchaseKeyword Function Start Here
//======================================================================
    public function postPurchaseKeyword(Request $request)
    {

        if ($request->gateway == '') {
            return redirect('user/keywords')->with([
                'message' => language_data('Payment gateway required', Auth::guard('client')->user()->lan_id),
                'message_important' => true
            ]);
        }

        $pay_amount = $request->pay_amount;
        $keyword_id = $request->keyword_id;

        if ($pay_amount == '' && $keyword_id == '') {
            return redirect('user/keywords')->with([
                'message' => 'Payment amount required',
                'message_important' => true
            ]);
        }

        $token = date('Ymds');

        Client::find(Auth::guard('client')->user()->id)->update([
            'pwresetexpiry' => $token
        ]);

        $gateway  = $request->get('gateway');
        $gat_info = PaymentGateways::where('settings', $gateway)->first();

        if ($gateway == 'paypal') {
            $payer = new Payer();
            $payer->setPaymentMethod('paypal');

            $item = new Item();
            $item->setName('Purchase keyword')
                ->setCurrency(app_config('Currency'))
                ->setQuantity('1')
                ->setPrice($pay_amount);

            $item_list = new ItemList();
            $item_list->setItems(array($item));

            $amount = new Amount();
            $amount->setCurrency(app_config('Currency'))
                ->setTotal($pay_amount);

            $transaction = new Transaction();
            $transaction->setAmount($amount)
                ->setItemList($item_list)
                ->setDescription('Purchase keyword');

            $redirect_urls = new RedirectUrls();
            $redirect_urls->setReturnUrl(url('/user/keywords/buy-keyword/success/' . $token . '/' . $keyword_id))/** Specify return URL **/
            ->setCancelUrl(url('/user/keywords/buy-keyword/cancel'));

            $payment = new Payment();
            $payment->setIntent('Sale')
                ->setPayer($payer)
                ->setRedirectUrls($redirect_urls)
                ->setTransactions(array($transaction));

            try {
                $payment->create($this->_api_context);
            } catch (PayPalConnectionException $ex) {

                return redirect('user/keywords')->with([
                    'message' => $ex->getMessage(),
                    'message_important' => true
                ]);
            }
            foreach ($payment->getLinks() as $link) {
                if ($link->getRel() == 'approval_url') {
                    $redirect_url = $link->getHref();
                    break;
                }
            }
            /** add payment ID to session **/
            \Session::put('paypal_payment_id', $payment->getId());
            if (isset($redirect_url)) {
                /** redirect to paypal **/
                return \Redirect::away($redirect_url);
            }

            return redirect('user/keywords')->with([
                'message' => 'Something went wrong. Please try again',
                'message_important' => true
            ]);

        }


        if ($gateway == 'coinpayments') {


            $order = array(
                'merchant' => $gat_info->value,
                'cmd' => '_pay',
                'reset' => '1',
                'item_name' => 'Purchase unit',
                'amountf' => $pay_amount,
                'allow_extra' => '1',
                'currency' => app_config('Currency'),
                'want_shipping' => '0',
                'success_url' => url('/user/keywords/buy-keyword/success/' . $token . '/' . $keyword_id),
                'cancel_url' => url('/user/keywords/buy-keyword/cancel'),
            );
            ?>

            <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
            <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                <title>Please wait while you're redirected</title>
                <style type="text/css">
                    #redirect {
                        background: #f1f1f1;
                        font-family: Helvetica, Arial, sans-serif
                    }

                    #redirect-container {
                        width: 410px;
                        margin: 130px auto 0;
                        background: #fff;
                        border: 1px solid #b5b5b5;
                        -moz-border-radius: 5px;
                        -webkit-border-radius: 5px;
                        border-radius: 5px;
                        text-align: center
                    }

                    #redirect-container h1 {
                        font-size: 22px;
                        color: #5f5f5f;
                        font-weight: normal;
                        margin: 22px 0 26px 0;
                        padding: 0
                    }

                    #redirect-container p {
                        font-size: 13px;
                        color: #454545;
                        margin: 0 0 12px 0;
                        padding: 0
                    }

                    #redirect-container img {
                        margin: 0 0 35px 0;
                        padding: 0
                    }

                    .ajaxLoader {
                        margin: 80px 153px
                    }
                </style>
                <script type="text/javascript">
                    function timedText() {
                        setTimeout('msg1()', 2000)
                        setTimeout('msg2()', 4000)
                        setTimeout('document.MetaRefreshForm.submit()', 4000)
                    }

                    function msg1() {
                        document.getElementById('redirect-message').firstChild.nodeValue = 'Preparing Data...'
                    }

                    function msg2() {
                        document.getElementById('redirect-message').firstChild.nodeValue = 'Redirecting...'
                    }
                </script>
            </head>
            <body>
            <?php echo "<body onLoad=\"document.forms['gw'].submit();\">\n"; ?>
            <div id="redirect-container">
                <h1>Please wait while you&rsquo;re redirected</h1>
                <p class="redirect-message" id="redirect-message">Loading Data...</p>
                <script type="text/javascript">timedText()</script>
            </div>
            <form method="post" action="https://www.coinpayments.net/index.php" name="gw">
                <?php
                foreach ($order as $name => $value) {
                    echo "<input type=\"hidden\" name=\"$name\" value=\"$value\"/>\n";
                }

                ?>
            </form>
            </body>
            </html>
            <?php
        }

        if ($gateway == 'payu') {

            $signature = "$gat_info->extra_value~$gat_info->value~buykeyword" . _raid(5) . "~$pay_amount~" . app_config('Currency');
            $signature = md5($signature);

            $order = array(
                'merchantId' => $gat_info->value,
                'ApiKey' => $gat_info->extra_value,
                'referenceCode' => 'buykeyword' . _raid(5),
                'description' => 'Purchase Keyword',
                'amount' => $pay_amount,
                'tax' => '0',
                'taxReturnBase' => '0',
                'currency' => app_config('Currency'),
                'buyerEmail' => Auth::guard('client')->user()->email,
                'test' => '0',
                'signature' => $signature,
                'confirmationUrl' => url('/user/keywords/buy-keyword/success/' . $token . '/' . $keyword_id),
                'responseUrl' => url('/user/keywords/buy-keyword/success/' . $token . '/' . $keyword_id),
            );
            ?>

            <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
            <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                <title>Please wait while you're redirected</title>
                <style type="text/css">
                    #redirect {
                        background: #f1f1f1;
                        font-family: Helvetica, Arial, sans-serif
                    }

                    #redirect-container {
                        width: 410px;
                        margin: 130px auto 0;
                        background: #fff;
                        border: 1px solid #b5b5b5;
                        -moz-border-radius: 5px;
                        -webkit-border-radius: 5px;
                        border-radius: 5px;
                        text-align: center
                    }

                    #redirect-container h1 {
                        font-size: 22px;
                        color: #5f5f5f;
                        font-weight: normal;
                        margin: 22px 0 26px 0;
                        padding: 0
                    }

                    #redirect-container p {
                        font-size: 13px;
                        color: #454545;
                        margin: 0 0 12px 0;
                        padding: 0
                    }

                    #redirect-container img {
                        margin: 0 0 35px 0;
                        padding: 0
                    }

                    .ajaxLoader {
                        margin: 80px 153px
                    }
                </style>
                <script type="text/javascript">
                    function timedText() {
                        setTimeout('msg1()', 2000)
                        setTimeout('msg2()', 4000)
                        setTimeout('document.MetaRefreshForm.submit()', 4000)
                    }

                    function msg1() {
                        document.getElementById('redirect-message').firstChild.nodeValue = 'Preparing Data...'
                    }

                    function msg2() {
                        document.getElementById('redirect-message').firstChild.nodeValue = 'Redirecting...'
                    }
                </script>
            </head>
            <?php echo "<body onLoad=\"document.forms['gw'].submit();\">\n"; ?>
            <div id="redirect-container">
                <h1>Please wait while you&rsquo;re redirected</h1>
                <p class="redirect-message" id="redirect-message">Loading Data...</p>
                <script type="text/javascript">timedText()</script>
            </div>
            <form method="post" action="https://gateway.payulatam.com/ppp-web-gateway" name="gw">
                <?php
                foreach ($order as $name => $value) {
                    echo "<input type=\"hidden\" name=\"$name\" value=\"$value\"/>\n";
                }
                ?>
            </form>
            </body>
            </html>
            <?php
        }

        if ($gateway == 'stripe') {
            $cmd           = $keyword_id;
            $plan_name     = 'Purchase Keyword';
            $stripe_amount = $pay_amount * 100;
            $post_url      = 'user/keywords/buy-keyword-with-stripe';
            return view('client.stripe', compact('gat_info', 'stripe_amount', 'cmd', 'plan_name', 'post_url'));

        }

        if ($gateway == '2checkout') {
            require_once app_path('Classes/TwoCheckout.php');

            $checkout = new TwoCheckout();

            $checkout->param('sid', $gat_info->value);
            $checkout->param('return_url', url('/user/keywords/buy-keyword/success/' . $token . '/' . $keyword_id));
            $checkout->param('li_0_name', 'Purchase Keyword');
            $checkout->param('li_0_price', $pay_amount);
            $checkout->param('li_0_quantity', 1);
            $checkout->param('card_holder_name', Auth::guard('client')->user()->fname . ' ' . Auth::guard('client')->user()->lname);
            $checkout->param('country', Auth::guard('client')->user()->country);
            $checkout->param('email', Auth::guard('client')->user()->email);
            $checkout->param('currency_code', app_config('Currency'));
            $checkout->gw_submit();
        }

        if ($gateway == 'slydepay') {

            require_once(app_path('libraray/vendor/autoload.php'));

            $slydepay     = new Slydepay($gat_info->value, $gat_info->extra_value);
            $total        = number_format((float)$pay_amount, '2', '.', '');
            $orderItems   = new OrderItems([
                new OrderItem(_raid(5), "Purchase Keyword", $total, 1)
            ]);
            $shippingCost = 0;
            $tax          = 0;
            $order_id     = _raid(5);

            $order = Order::createWithId($orderItems, $order_id, $shippingCost, $tax, $order_id);

            try {
                $response = $slydepay->processPaymentOrder($order);
                return redirect($response->redirectUrl());
            } catch (ProcessPaymentException $e) {
                return redirect('/user/keywords/buy-keyword/cancel')->with([
                    'message' => $e->getMessage(),
                    'message_important' => true
                ]);
            }
        }

        if ($gateway == 'manualpayment') {
            $details = $gat_info->value;
            return view('client.bank-details', compact('details'));
        }


        if ($gateway == 'paystack') {

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => "https://api.paystack.co/transaction/initialize",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode([
                    'amount' => $pay_amount * 100,
                    'email' => Auth::guard('client')->user()->email,
                    'metadata' => [
                        'keyword_id' => $keyword_id,
                        'request_type' => 'buy_keyword',
                    ]
                ]),
                CURLOPT_HTTPHEADER => [
                    "authorization: Bearer " . getenv('PAYSTACK_SECRET_KEY'),
                    "content-type: application/json",
                    "cache-control: no-cache"
                ],
            ));

            $response = curl_exec($curl);
            $err      = curl_error($curl);


            curl_close($curl);


            if ($response === false) {
                return redirect('user/keywords')->with([
                    'message' => 'Php curl show false value. Please contact with your provider',
                    'message_important' => true
                ]);
            }

            if ($err) {
                return redirect('user/keywords')->with([
                    'message' => $err,
                    'message_important' => true
                ]);
            }

            $tranx = json_decode($response);

            if ($tranx->status != 1) {
                return redirect('user/keywords')->with([
                    'message' => $tranx->message,
                    'message_important' => true
                ]);
            }

            return redirect($tranx->data->authorization_url);

        }

        if ($gateway == 'paynow') {
            require_once app_path('Classes/Paynow.php');

            $paynow = new Paynow();

            $ref        = _raid(10);
            $keyword_id = $ref . $keyword_id;

            //set POST variables
            $values = array(
                'resulturl' => url('/user/keywords/buy-keyword/paynow/' . $keyword_id),
                'reference' => $ref,
                'amount' => $pay_amount,
                'id' => $gat_info->value,
                'status' => 'Purchase Keyword'
            );

            $fields_string = $paynow->CreateMsg($values, $gat_info->extra_value);

            //open connection
            $ch  = curl_init();
            $url = 'https://www.paynow.co.zw/interface/initiatetransaction';

            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fields_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

            $result = curl_exec($ch);

            //close connection
            curl_close($ch);

            if ($result) {
                $msg = $paynow->ParseMsg($result);

                //first check status, take appropriate action
                if (is_array($msg) && array_key_exists('status', $msg) && $msg["status"] == 'Error') {
                    return redirect('user/keywords')->with([
                        'message' => $msg['error'],
                        'message_important' => true
                    ]);

                } else if (is_array($msg) && array_key_exists('status', $msg) && $msg["status"] == 'Ok') {

                    //second, check hash
                    $validateHash = $paynow->CreateHash($msg, $gat_info->extra_value);
                    if ($validateHash != $msg["hash"]) {
                        $error = "Paynow reply hashes do not match : " . $validateHash . " - " . $msg["hash"];
                        return redirect('user/keywords')->with([
                            'message' => $error,
                            'message_important' => true
                        ]);

                    } else {

                        if (is_array($msg) && array_key_exists('browserurl', $msg)) {
                            $theProcessUrl = $msg["browserurl"];

                            $orders_data_file = storage_path('PayNowTransaction.ini');
                            //1. Saving mine to a PHP.INI type of file, you should save it to a db etc
                            $orders_array = array();
                            if (file_exists($orders_data_file)) {
                                $orders_array = parse_ini_file($orders_data_file, true);
                            }

                            $orders_array['BuyKeywordID_' . $keyword_id] = $msg;

                            $paynow->write_php_ini($orders_array, $orders_data_file, true);


                            return redirect($theProcessUrl);

                        } else {
                            return redirect('user/keywords')->with([
                                'message' => language_data('Invalid transaction URL, cannot continue'),
                                'message_important' => true
                            ]);
                        }
                    }
                } else {
                    $error = "Invalid status in from Paynow, cannot continue.";
                    return redirect('user/keywords')->with([
                        'message' => $error,
                        'message_important' => true
                    ]);
                }

            } else {
                $error = curl_error($ch);
                return redirect('user/keywords')->with([
                    'message' => $error,
                    'message_important' => true
                ]);
            }
        }


        if ($gateway == 'webxpay') {
            require_once(app_path('libraray/webxpay/Crypt/RSA.php'));

            //initialize RSA
            $rsa = new \Crypt_RSA();
            // unique_order_id|total_amount
            $plaintext = "$keyword_id|$pay_amount";
            $publickey = $gat_info->extra_value;

            $rsa->loadKey($publickey);

            $encrypt = $rsa->encrypt($plaintext);
            //encode for data passing
            $payment = base64_encode($encrypt);

            //custom fields
            //cus_1|cus_2|cus_3|cus_4
            $custom_fields = base64_encode("buy_keyword|$keyword_id");

            $order = array(
                'first_name' => Auth::guard('client')->user()->fname,
                'last_name' => Auth::guard('client')->user()->lname,
                'email' => Auth::guard('client')->user()->email,
                'contact_number' => Auth::guard('client')->user()->phone,
                'address_line_one' => Auth::guard('client')->user()->address1,
                'address_line_two' => Auth::guard('client')->user()->address2,
                'city' => Auth::guard('client')->user()->city,
                'state' => Auth::guard('client')->user()->state,
                'postal_code' => Auth::guard('client')->user()->postcode,
                'country' => Auth::guard('client')->user()->country,
                'process_currency' => app_config('Currency'),
                'cms' => 'PHP',
                'secret_key' => $gat_info->value,
                'payment' => $payment,
                'custom_fields' => $custom_fields,
            );

            ?>

            <!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
            <html>
            <head>
                <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
                <title>Please wait while you're redirected</title>
                <style type="text/css">
                    #redirect {
                        background: #f1f1f1;
                        font-family: Helvetica, Arial, sans-serif
                    }

                    #redirect-container {
                        width: 410px;
                        margin: 130px auto 0;
                        background: #fff;
                        border: 1px solid #b5b5b5;
                        -moz-border-radius: 5px;
                        -webkit-border-radius: 5px;
                        border-radius: 5px;
                        text-align: center
                    }

                    #redirect-container h1 {
                        font-size: 22px;
                        color: #5f5f5f;
                        font-weight: normal;
                        margin: 22px 0 26px 0;
                        padding: 0
                    }

                    #redirect-container p {
                        font-size: 13px;
                        color: #454545;
                        margin: 0 0 12px 0;
                        padding: 0
                    }

                    #redirect-container img {
                        margin: 0 0 35px 0;
                        padding: 0
                    }

                    .ajaxLoader {
                        margin: 80px 153px
                    }
                </style>
                <script type="text/javascript">
                    function timedText() {
                        setTimeout('msg1()', 2000)
                        setTimeout('msg2()', 4000)
                        setTimeout('document.MetaRefreshForm.submit()', 4000)
                    }

                    function msg1() {
                        document.getElementById('redirect-message').firstChild.nodeValue = 'Preparing Data...'
                    }

                    function msg2() {
                        document.getElementById('redirect-message').firstChild.nodeValue = 'Redirecting...'
                    }
                </script>
            </head>
            <body>
            <?php echo "<body onLoad=\"document.forms['gw'].submit();\">\n"; ?>
            <div id="redirect-container">
                <h1>Please wait while you&rsquo;re redirected</h1>
                <p class="redirect-message" id="redirect-message">Loading Data...</p>
                <script type="text/javascript">timedText()</script>
            </div>
            <form method="post" action="https://webxpay.com/index.php?route=checkout/billing" name="gw">
                <?php
                foreach ($order as $name => $value) {
                    echo "<input type=\"hidden\" name=\"$name\" value=\"$value\"/>\n";
                }

                ?>
            </form>
            </body>
            </html>
            <?php

        }

    }


    //======================================================================
    // buyUnitSuccess Function Start Here
    //======================================================================
    public function buyUnitSuccess($token, $id)
    {
        $get_token = Auth::guard('client')->user()->pwresetexpiry;

        if ($get_token != $token) {
            return redirect('user/sms/buy-unit')->with([
                'message' => language_data('Cancelled the Payment')
            ]);
        }

        $data = SMSBundles::where('unit_from', '<=', $id)->where('unit_to', '>=', $id)->first();

        if ($data) {
            $unit_price      = $data->price;
            $amount_to_pay   = $id * $unit_price;
            $transaction_fee = ($amount_to_pay * $data->trans_fee) / 100;
            $total           = $amount_to_pay + $transaction_fee;

            $client = Client::find(Auth::guard('client')->user()->id);

            $payment_id = \Session::get('paypal_payment_id');

            if ($payment_id) {
                \Session::forget('paypal_payment_id');
                if (empty($request->get('PayerID')) || empty($request->get('token'))) {
                    return redirect('user/sms/buy-unit')->with([
                        'message' => 'Payment failed',
                        'message_important' => true
                    ]);
                }

                $payment   = Payment::get($payment_id, $this->_api_context);
                $execution = new PaymentExecution();
                $execution->setPayerId($request->get('PayerID'));

                $result = $payment->execute($execution, $this->_api_context);
                if ($result->getState() == 'approved') {

                    $total_balance     = $client->sms_limit + $id;
                    $client->sms_limit = $total_balance;
                    $client->save();

                    $inv               = new Invoices();
                    $inv->cl_id        = $client->id;
                    $inv->client_name  = $client->fname . ' ' . $client->lname;
                    $inv->created_by   = 1;
                    $inv->created      = date('Y-m-d');
                    $inv->duedate      = date('Y-m-d');
                    $inv->datepaid     = date('Y-m-d');
                    $inv->subtotal     = $amount_to_pay;
                    $inv->total        = $total;
                    $inv->status       = 'Paid';
                    $inv->pmethod      = '';
                    $inv->recurring    = '0';
                    $inv->bill_created = 'yes';
                    $inv->note         = '';
                    $inv->save();
                    $inv_id = $inv->id;

                    $d           = new InvoiceItems();
                    $d->inv_id   = $inv_id;
                    $d->cl_id    = $client->id;
                    $d->item     = 'Purchase SMS Unit';
                    $d->qty      = $id;
                    $d->price    = $unit_price;
                    $d->tax      = $transaction_fee;
                    $d->discount = '0';
                    $d->subtotal = $amount_to_pay;
                    $d->total    = $total;
                    $d->save();

                    return redirect('user/invoices/all')->with([
                        'message' => language_data('Purchase successfully.Wait for administrator response', Auth::guard('client')->user()->lan_id)
                    ]);
                }
                return redirect('user/sms/buy-unit')->with([
                    'message' => 'Payment failed',
                    'message_important' => true
                ]);
            }

            $total_balance     = $client->sms_limit + $id;
            $client->sms_limit = $total_balance;
            $client->save();

            $inv               = new Invoices();
            $inv->cl_id        = $client->id;
            $inv->client_name  = $client->fname . ' ' . $client->lname;
            $inv->created_by   = 1;
            $inv->created      = date('Y-m-d');
            $inv->duedate      = date('Y-m-d');
            $inv->datepaid     = date('Y-m-d');
            $inv->subtotal     = $amount_to_pay;
            $inv->total        = $total;
            $inv->status       = 'Paid';
            $inv->pmethod      = '';
            $inv->recurring    = '0';
            $inv->bill_created = 'yes';
            $inv->note         = '';
            $inv->save();
            $inv_id = $inv->id;

            $d           = new InvoiceItems();
            $d->inv_id   = $inv_id;
            $d->cl_id    = $client->id;
            $d->item     = 'Purchase SMS Unit';
            $d->qty      = $id;
            $d->price    = $unit_price;
            $d->tax      = $transaction_fee;
            $d->discount = '0';
            $d->subtotal = $amount_to_pay;
            $d->total    = $total;
            $d->save();

            return redirect('user/invoices/all')->with([
                'message' => language_data('Purchase successfully.Wait for administrator response', Auth::guard('client')->user()->lan_id)
            ]);

        } else {
            return redirect('user/sms/buy-unit')->with([
                'message' => 'Data not found',
                'message_important' => true
            ]);
        }
    }


    //======================================================================
    // buyUnitCancel Function Start Here
    //======================================================================
    public function buyUnitCancel()
    {
        return redirect('user/sms/buy-unit')->with([
            'message' => language_data('Cancelled the Payment')
        ]);
    }


    //======================================================================
    // buyUnitWithStripe Function Start Here
    //======================================================================
    public function buyUnitWithStripe(Request $request)
    {

        $cmd  = $request->get('cmd');
        $data = SMSBundles::where('unit_from', '<=', $cmd)->where('unit_to', '>=', $cmd)->first();

        if (!$data) {
            return redirect('user/sms/buy-unit')->with([
                'message' => 'Data not found',
                'message_important' => true
            ]);
        }

        $gat_info = PaymentGateways::where('settings', 'stripe')->first();
        $stripe   = Stripe::make($gat_info->extra_value, '2016-07-06');
        $client   = Client::find(Auth::guard('client')->user()->id);
        $email    = $client->email;


        $total_balance     = $client->sms_limit + $cmd;
        $client->sms_limit = $total_balance;
        $client->save();

        $unit_price      = $data->price;
        $amount_to_pay   = $cmd * $unit_price;
        $transaction_fee = ($amount_to_pay * $data->trans_fee) / 100;
        $total           = $amount_to_pay + $transaction_fee;

        try {
            $customer = $stripe->customers()->create([
                'email' => $email,
                'source' => $request->stripeToken
            ]);

            $customer_id = $customer['id'];

            $meta_data = [
                'customer_name' => $client->fname . ' ' . $client->lname,
                'country' => $client->country,
                'ip_address' => \request()->ip()
            ];

            if ($client->address1) {
                $meta_data['address'] = $client->address1 . ' ' . $client->address2;
            }
            if ($client->city) {
                $meta_data['city'] = $client->city;
            }
            if ($client->postcode) {
                $meta_data['postcode'] = $client->postcode;
            }


            $stripe->charges()->create([
                'customer' => $customer_id,
                'currency' => app_config('Currency'),
                'amount' => $total,
                'receipt_email' => $email,
                'metadata' => $meta_data
            ]);


            $inv               = new Invoices();
            $inv->cl_id        = $client->id;
            $inv->client_name  = $client->fname . ' ' . $client->lname;
            $inv->created_by   = 1;
            $inv->created      = date('Y-m-d');
            $inv->duedate      = date('Y-m-d');
            $inv->datepaid     = date('Y-m-d');
            $inv->subtotal     = $amount_to_pay;
            $inv->total        = $total;
            $inv->status       = 'Paid';
            $inv->pmethod      = 'Stripe';
            $inv->recurring    = '0';
            $inv->bill_created = 'yes';
            $inv->note         = '';
            $inv->save();
            $inv_id = $inv->id;

            $d           = new InvoiceItems();
            $d->inv_id   = $inv_id;
            $d->cl_id    = $client->id;
            $d->item     = 'Purchase SMS Unit';
            $d->qty      = $cmd;
            $d->price    = $unit_price;
            $d->tax      = $transaction_fee;
            $d->discount = '0';
            $d->subtotal = $amount_to_pay;
            $d->total    = $total;
            $d->save();

            return redirect('user/invoices/all')->with([
                'message' => language_data('Purchase successfully.Wait for administrator response', Auth::guard('client')->user()->lan_id)
            ]);

        } catch (StripeException $e) {
            return redirect('user/sms/buy-unit')->with([
                'message' => $e->getMessage(),
                'message_important' => true
            ]);
        }
    }



//======================================================================
// payStackCallback Function Start Here
//======================================================================
    public function payStackCallback()
    {
        $curl      = curl_init();
        $reference = isset($_GET['reference']) ? $_GET['reference'] : '';
        if (!$reference) {
            return redirect('dashboard')->with([
                'message' => 'No reference supplied',
                'message_important' => true
            ]);
        }

        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/transaction/verify/" . rawurlencode($reference),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                "accept: application/json",
                "authorization: Bearer " . getenv('PAYSTACK_SECRET_KEY'),
                "cache-control: no-cache"
            ],
        ));

        $response = curl_exec($curl);
        $err      = curl_error($curl);


        curl_close($curl);

        if ($response === false) {
            return redirect('dashboard')->with([
                'message' => 'Php curl show false value. Please contact with your provider',
                'message_important' => true
            ]);
        }

        if ($err) {
            return redirect('dashboard')->with([
                'message' => $err,
                'message_important' => true
            ]);
        }

        $tranx = json_decode($response);

        if (!$tranx->status) {
            // there was an error from the API
            return redirect('dashboard')->with([
                'message' => $tranx->message,
                'message_important' => true
            ]);
        }

        if ('success' == $tranx->data->status) {

            $request_type = $tranx->data->metadata->request_type;

            if ($request_type == 'invoice_payment') {
                $id      = $tranx->data->metadata->invoice_id;
                $invoice = Invoices::find($id);

                if ($invoice) {
                    $invoice->status = 'Paid';
                    $invoice->save();
                    return redirect('user/invoices/view/' . $id)->with([
                        'message' => language_data('Invoice paid successfully', Auth::guard('client')->user()->lan_id)
                    ]);
                } else {
                    return redirect('user/invoices/all')->with([
                        'message' => language_data('Invoice paid successfully', Auth::guard('client')->user()->lan_id)
                    ]);
                }
            }


            if ($request_type == 'buy_unit') {

                $unit_number = $tranx->data->metadata->unit_number;

                $client = Client::find(Auth::guard('client')->user()->id);

                $total_balance     = $client->sms_limit + $unit_number;
                $client->sms_limit = $total_balance;
                $client->save();

                $inv               = new Invoices();
                $inv->cl_id        = $client->id;
                $inv->client_name  = $client->fname . ' ' . $client->lname;
                $inv->created_by   = 1;
                $inv->created      = date('Y-m-d');
                $inv->duedate      = date('Y-m-d');
                $inv->datepaid     = date('Y-m-d');
                $inv->subtotal     = ($unit_number * $tranx->data->metadata->unit_price);
                $inv->total        = ($tranx->data->amount / 100);
                $inv->status       = 'Paid';
                $inv->pmethod      = 'Paystack';
                $inv->recurring    = '0';
                $inv->bill_created = 'yes';
                $inv->note         = '';
                $inv->save();
                $inv_id = $inv->id;

                $d           = new InvoiceItems();
                $d->inv_id   = $inv_id;
                $d->cl_id    = $client->id;
                $d->item     = 'Purchase SMS Unit';
                $d->qty      = $unit_number;
                $d->price    = $tranx->data->metadata->unit_price;
                $d->tax      = $tranx->data->metadata->trans_fee;
                $d->discount = '0';
                $d->subtotal = ($unit_number * $tranx->data->metadata->unit_price);
                $d->total    = ($tranx->data->amount / 100);
                $d->save();

                return redirect('user/invoices/all')->with([
                    'message' => language_data('Purchase successfully.Wait for administrator response', Auth::guard('client')->user()->lan_id)
                ]);

            }
            if ($request_type == 'buy_keyword') {

                $keyword_id = $tranx->data->metadata->keyword_id;
                $keyword    = Keywords::find($keyword_id);

                if ($keyword) {

                    $client = Client::find(Auth::guard('client')->user()->id);


                    $validity = $keyword->validity;

                    $current_date = strtotime(date('Y-m-d'));
                    if ($validity == 'month1') {
                        $nd = date('Y-m-d', strtotime('+1 month', $current_date));
                    } elseif ($validity == 'months2') {
                        $nd = date('Y-m-d', strtotime('+2 months', $current_date));
                    } elseif ($validity == 'months3') {
                        $nd = date('Y-m-d', strtotime('+3 months', $current_date));
                    } elseif ($validity == 'months6') {
                        $nd = date('Y-m-d', strtotime('+6 months', $current_date));
                    } elseif ($validity == 'year1') {
                        $nd = date('Y-m-d', strtotime('+1 year', $current_date));
                    } elseif ($validity == 'years2') {
                        $nd = date('Y-m-d', strtotime('+2 years', $current_date));
                    } elseif ($validity == 'years3') {
                        $nd = date('Y-m-d', strtotime('+3 years', $current_date));
                    } else {
                        $nd = null;
                    }

                    $keyword->user_id       = $client->id;
                    $keyword->status        = 'assigned';
                    $keyword->validity      = $validity;
                    $keyword->validity_date = $nd;

                    $keyword->save();


                    $inv               = new Invoices();
                    $inv->cl_id        = $client->id;
                    $inv->client_name  = $client->fname . ' ' . $client->lname;
                    $inv->created_by   = 1;
                    $inv->created      = date('Y-m-d');
                    $inv->duedate      = date('Y-m-d');
                    $inv->datepaid     = date('Y-m-d');
                    $inv->subtotal     = $keyword->price;
                    $inv->total        = $keyword->price;
                    $inv->status       = 'Paid';
                    $inv->pmethod      = 'Paystack';
                    $inv->recurring    = '0';
                    $inv->bill_created = 'yes';
                    $inv->note         = '';
                    $inv->save();
                    $inv_id = $inv->id;

                    $d           = new InvoiceItems();
                    $d->inv_id   = $inv_id;
                    $d->cl_id    = $client->id;
                    $d->item     = 'Purchase Keyword: ' . $keyword->title;
                    $d->qty      = 1;
                    $d->price    = $keyword->price;
                    $d->tax      = '0';
                    $d->discount = '0';
                    $d->subtotal = $keyword->price;
                    $d->total    = $keyword->price;
                    $d->save();

                    return redirect('user/invoices/all')->with([
                        'message' => language_data('Purchase successfully.Wait for administrator response', Auth::guard('client')->user()->lan_id)
                    ]);
                } else {
                    return redirect('user/keywords')->with([
                        'message' => 'Data not found',
                        'message_important' => true
                    ]);
                }


            }

            if ($request_type == 'purchase_plan') {

                $plan_id  = $tranx->data->metadata->plan_id;
                $sms_plan = SMSPricePlan::find($plan_id);

                $get_balance = SMSPlanFeature::where('pid', $plan_id)->first();
                $sms_balance = $get_balance->feature_value;

                $client = Client::find(Auth::guard('client')->user()->id);

                $total_balance     = $client->sms_limit + $sms_balance;
                $client->sms_limit = $total_balance;
                $client->save();

                $inv               = new Invoices();
                $inv->cl_id        = $client->id;
                $inv->client_name  = $client->fname . ' ' . $client->lname;
                $inv->created_by   = 1;
                $inv->created      = date('Y-m-d');
                $inv->duedate      = date('Y-m-d');
                $inv->datepaid     = date('Y-m-d');
                $inv->subtotal     = $sms_plan->price;
                $inv->total        = $sms_plan->price;
                $inv->status       = 'Paid';
                $inv->pmethod      = '';
                $inv->recurring    = '0';
                $inv->bill_created = 'yes';
                $inv->note         = '';
                $inv->save();
                $inv_id = $inv->id;

                $d           = new InvoiceItems();
                $d->inv_id   = $inv_id;
                $d->cl_id    = $client->id;
                $d->item     = $sms_plan->plan_name . ' Plan';
                $d->qty      = '1';
                $d->price    = $sms_plan->price;
                $d->tax      = '0';
                $d->discount = '0';
                $d->subtotal = $sms_plan->price;
                $d->total    = $sms_plan->price;
                $d->save();

                return redirect('user/invoices/all')->with([
                    'message' => language_data('Purchase successfully.Wait for administrator response', Auth::guard('client')->user()->lan_id)
                ]);

            }

        } else {
            return redirect('dashboard')->with([
                'message' => 'Unknown error',
                'message_important' => true
            ]);
        }
    }


    //======================================================================
    // PayNow Payment Gateway Integration
    //======================================================================

    //======================================================================
    // getPaymentGatewayInfo Function Start Here
    //======================================================================
    public function getPaymentGatewayInfo($gateway = '')
    {
        $gat_info = PaymentGateways::where('settings', $gateway)->first();
        if ($gat_info) {
            return $gat_info;
        } else {
            return false;
        }
    }


//======================================================================
// payNowInvoice Function Start Here
//======================================================================
    public function payNowInvoice($id)
    {

        $gat_info = $this->getPaymentGatewayInfo('paynow');

        if ($gat_info) {

            $orders_data_file = storage_path('PayNowTransaction.ini');

            //Lets get our locally saved settings for this order
            $orders_array = array();
            if (file_exists($orders_data_file)) {
                $orders_array = parse_ini_file($orders_data_file, true);
            }

            $order_data = $orders_array['InvoiceNo_' . $id];

            if (is_array($order_data) && array_key_exists('pollurl', $order_data)) {

                $ch = curl_init();

                //set the url, number of POST vars, POST data
                curl_setopt($ch, CURLOPT_URL, $order_data['pollurl']);
                curl_setopt($ch, CURLOPT_POST, 0);
                curl_setopt($ch, CURLOPT_POSTFIELDS, '');
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                //execute post
                $result = curl_exec($ch);

                if ($result) {
                    require_once app_path('Classes/Paynow.php');
                    $paynow = new Paynow();

                    //close connection
                    $msg = $paynow->ParseMsg($result);

                    $validateHash = $paynow->CreateHash($msg, $gat_info->extra_value);

                    if ($validateHash != $msg["hash"]) {
                        $this->cancelledInvoice($id);
                    } else {
                        $orders_array['InvoiceNo_' . $id]                         = $msg;
                        $orders_array['InvoiceNo_' . $id]['returned_from_paynow'] = 'yes';

                        $paynow->write_php_ini($orders_array, $orders_data_file, true);

                        if ($msg['status'] == 'Paid') {
                            $invoice = Invoices::find($id);

                            if ($invoice) {
                                $invoice->status = 'Paid';
                                $invoice->save();
                                return redirect('user/invoices/view1/' . $id)->with([
                                    'message' => language_data('Invoice paid successfully')
                                ]);
                            } else {
                                return redirect('user/invoices/all')->with([
                                    'message' => language_data('Invoice paid successfully')
                                ]);
                            }
                        } else {
                            return redirect('user/invoices/view1/' . $id)->with([
                                'message' => 'Invoice ' . $msg['status']
                            ]);
                        }

                    }
                } else {
                    $this->cancelledInvoice($id);
                }

            } else {
                $this->cancelledInvoice($id);
            }
        } else {
            $this->cancelledInvoice($id);
        }
    }


//======================================================================
// payNowPurchasePlan Function Start Here
//======================================================================
    public function payNowPurchasePlan($id)
    {
        if ($id) {

            $gat_info = $this->getPaymentGatewayInfo('paynow');

            if ($gat_info) {

                $orders_data_file = storage_path('PayNowTransaction.ini');

                //Lets get our locally saved settings for this order
                $orders_array = array();
                if (file_exists($orders_data_file)) {
                    $orders_array = parse_ini_file($orders_data_file, true);
                }

                $order_data = $orders_array['PurchasePlanID_' . $id];

                if (is_array($order_data) && array_key_exists('pollurl', $order_data)) {

                    $ch = curl_init();

                    //set the url, number of POST vars, POST data
                    curl_setopt($ch, CURLOPT_URL, $order_data['pollurl']);
                    curl_setopt($ch, CURLOPT_POST, 0);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, '');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                    //execute post
                    $result = curl_exec($ch);

                    if ($result) {
                        require_once app_path('Classes/Paynow.php');
                        $paynow = new Paynow();

                        //close connection
                        $msg = $paynow->ParseMsg($result);

                        $validateHash = $paynow->CreateHash($msg, $gat_info->extra_value);

                        if ($validateHash != $msg["hash"]) {
                            $this->cancelledInvoice($id);
                        } else {
                            $orders_array['PurchasePlanID_' . $id]                         = $msg;
                            $orders_array['PurchasePlanID_' . $id]['returned_from_paynow'] = 'yes';

                            $paynow->write_php_ini($orders_array, $orders_data_file, true);

                            $invoice_status = 'Unpaid';

                            if ($msg['status'] == 'Paid' || $msg['status'] == 'Awaiting Delivery' || $msg['status'] == 'Delivered') {

                                if ($msg['status'] == 'Awaiting Delivery' || $msg['status'] == 'Delivered') {
                                    $invoice_status = 'Unpaid';
                                }


                                $sms_plan = SMSPricePlan::find($id);

                                $get_balance = SMSPlanFeature::where('pid', $id)->first();
                                $sms_balance = $get_balance->feature_value;

                                $client = Client::find(Auth::guard('client')->user()->id);
                                if ($msg['status'] == 'Paid') {
                                    $invoice_status = 'Paid';

                                    $total_balance     = $client->sms_limit + $sms_balance;
                                    $client->sms_limit = $total_balance;
                                    $client->save();
                                }


                                $inv               = new Invoices();
                                $inv->cl_id        = $client->id;
                                $inv->client_name  = $client->fname . ' ' . $client->lname;
                                $inv->created_by   = 1;
                                $inv->created      = date('Y-m-d');
                                $inv->duedate      = date('Y-m-d');
                                $inv->datepaid     = date('Y-m-d');
                                $inv->subtotal     = $sms_plan->price;
                                $inv->total        = $sms_plan->price;
                                $inv->status       = $invoice_status;
                                $inv->pmethod      = '';
                                $inv->recurring    = '0';
                                $inv->bill_created = 'yes';
                                $inv->note         = '';
                                $inv->save();
                                $inv_id = $inv->id;

                                $d           = new InvoiceItems();
                                $d->inv_id   = $inv_id;
                                $d->cl_id    = $client->id;
                                $d->item     = $sms_plan->plan_name . ' Plan';
                                $d->qty      = '1';
                                $d->price    = $sms_plan->price;
                                $d->tax      = '0';
                                $d->discount = '0';
                                $d->subtotal = $sms_plan->price;
                                $d->total    = $sms_plan->price;
                                $d->save();

                                return redirect('user/invoices/all1')->with([
                                    'message' => language_data('Purchase successfully.Wait for administrator response', Auth::guard('client')->user()->lan_id)
                                ]);

                            } else {
                                return redirect('user/sms/sms-plan-feature1/' . $id)->with([
                                    'message' => 'Purchase sms plan ' . $msg['status']
                                ]);
                            }

                        }
                    } else {
                        $this->cancelledPurchase($id);
                    }

                } else {
                    $this->cancelledPurchase($id);
                }
            } else {
                $this->cancelledPurchase($id);
            }

        } else {
            return redirect('user/sms/purchase-sms-plan1')->with([
                'message' => language_data('Invalid request', Auth::guard('client')->user()->lan_id),
                'message_important' => true
            ]);
        }
    }

    //======================================================================
    // buyUnitByPayNow Function Start Here
    //======================================================================
    public function buyUnitByPayNow($id)
    {

        $number_unit = substr($id, 10);
        $data        = SMSBundles::where('unit_from', '<=', $number_unit)->where('unit_to', '>=', $number_unit)->first();

        if ($data) {

            $gat_info = $this->getPaymentGatewayInfo('paynow');

            if ($gat_info) {

                $orders_data_file = storage_path('PayNowTransaction.ini');

                //Lets get our locally saved settings for this order
                $orders_array = array();
                if (file_exists($orders_data_file)) {
                    $orders_array = parse_ini_file($orders_data_file, true);
                }

                $order_data = $orders_array['BuyUnitID_' . $id];

                if (is_array($order_data) && array_key_exists('pollurl', $order_data)) {

                    $ch = curl_init();

                    //set the url, number of POST vars, POST data
                    curl_setopt($ch, CURLOPT_URL, $order_data['pollurl']);
                    curl_setopt($ch, CURLOPT_POST, 0);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, '');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                    //execute post
                    $result = curl_exec($ch);

                    if ($result) {
                        require_once app_path('Classes/Paynow.php');
                        $paynow = new Paynow();

                        //close connection
                        $msg = $paynow->ParseMsg($result);

                        $validateHash = $paynow->CreateHash($msg, $gat_info->extra_value);

                        if ($validateHash != $msg["hash"]) {
                            $this->buyUnitCancel();
                        } else {
                            $orders_array['BuyUnitID_' . $id]                         = $msg;
                            $orders_array['BuyUnitID_' . $id]['returned_from_paynow'] = 'yes';

                            $paynow->write_php_ini($orders_array, $orders_data_file, true);

                            if ($msg['status'] == 'Paid' || $msg['status'] == 'Awaiting Delivery' || $msg['status'] == 'Delivered') {


                                $unit_price      = $data->price;
                                $amount_to_pay   = $number_unit * $unit_price;
                                $transaction_fee = ($amount_to_pay * $data->trans_fee) / 100;
                                $total           = $amount_to_pay + $transaction_fee;
                                $invoice_status  = 'Unpaid';

                                $client = Client::find(Auth::guard('client')->user()->id);

                                if ($msg['status'] == 'Awaiting Delivery' || $msg['status'] == 'Delivered') {
                                    $invoice_status = 'Unpaid';
                                }

                                if ($msg['status'] == 'Paid') {
                                    $invoice_status = 'Paid';

                                    $total_balance     = $client->sms_limit + $number_unit;
                                    $client->sms_limit = $total_balance;
                                    $client->save();
                                }


                                $inv               = new Invoices();
                                $inv->cl_id        = $client->id;
                                $inv->client_name  = $client->fname . ' ' . $client->lname;
                                $inv->created_by   = 1;
                                $inv->created      = date('Y-m-d');
                                $inv->duedate      = date('Y-m-d');
                                $inv->datepaid     = date('Y-m-d');
                                $inv->subtotal     = $amount_to_pay;
                                $inv->total        = $total;
                                $inv->status       = $invoice_status;
                                $inv->pmethod      = '';
                                $inv->recurring    = '0';
                                $inv->bill_created = 'yes';
                                $inv->note         = '';
                                $inv->save();
                                $inv_id = $inv->id;

                                $d           = new InvoiceItems();
                                $d->inv_id   = $inv_id;
                                $d->cl_id    = $client->id;
                                $d->item     = 'Purchase SMS Unit';
                                $d->qty      = $number_unit;
                                $d->price    = $unit_price;
                                $d->tax      = $transaction_fee;
                                $d->discount = '0';
                                $d->subtotal = $amount_to_pay;
                                $d->total    = $total;
                                $d->save();

                                return redirect('user/invoices/all1')->with([
                                    'message' => language_data('Purchase successfully.Wait for administrator response', Auth::guard('client')->user()->lan_id)
                                ]);
                            } else {
                                return redirect('user/sms/buy-unit1')->with([
                                    'message' => 'Purchase buy unit ' . $msg['status']
                                ]);
                            }

                        }
                    } else {
                        $this->buyUnitCancel();
                    }

                } else {
                    $this->buyUnitCancel();
                }
            } else {
                $this->buyUnitCancel();
            }
        } else {
            return redirect('user/sms/buy-unit1')->with([
                'message' => language_data('Data not found', Auth::guard('client')->user()->lan_id),
                'message_important' => true
            ]);
        }
    }

    //======================================================================
    // buyKeywordByPayNow Function Start Here
    //======================================================================
    public function buyKeywordByPayNow($id)
    {

        $keyword_id = substr($id, 10);

        $keyword = Keywords::find($keyword_id);

        if ($keyword) {

            $gat_info = $this->getPaymentGatewayInfo('paynow');

            if ($gat_info) {

                $orders_data_file = storage_path('PayNowTransaction.ini');

                //Lets get our locally saved settings for this order
                $orders_array = array();
                if (file_exists($orders_data_file)) {
                    $orders_array = parse_ini_file($orders_data_file, true);
                }

                $order_data = $orders_array['BuyKeywordID_' . $id];

                if (is_array($order_data) && array_key_exists('pollurl', $order_data)) {

                    $ch = curl_init();

                    //set the url, number of POST vars, POST data
                    curl_setopt($ch, CURLOPT_URL, $order_data['pollurl']);
                    curl_setopt($ch, CURLOPT_POST, 0);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, '');
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

                    //execute post
                    $result = curl_exec($ch);

                    if ($result) {
                        require_once app_path('Classes/Paynow.php');
                        $paynow = new Paynow();

                        //close connection
                        $msg = $paynow->ParseMsg($result);

                        $validateHash = $paynow->CreateHash($msg, $gat_info->extra_value);

                        if ($validateHash != $msg["hash"]) {
                            $this->buyUnitCancel();
                        } else {
                            $orders_array['BuyKeywordID_' . $id]                         = $msg;
                            $orders_array['BuyKeywordID_' . $id]['returned_from_paynow'] = 'yes';

                            $paynow->write_php_ini($orders_array, $orders_data_file, true);

                            if ($msg['status'] == 'Paid' || $msg['status'] == 'Awaiting Delivery' || $msg['status'] == 'Delivered') {

                                $client = Client::find(Auth::guard('client')->user()->id);

                                if ($msg['status'] == 'Awaiting Delivery' || $msg['status'] == 'Delivered') {
                                    $invoice_status = 'Unpaid';
                                }

                                if ($msg['status'] == 'Paid') {
                                    $invoice_status = 'Paid';


                                    $validity = $keyword->validity;

                                    $current_date = strtotime(date('Y-m-d'));
                                    if ($validity == 'month1') {
                                        $nd = date('Y-m-d', strtotime('+1 month', $current_date));
                                    } elseif ($validity == 'months2') {
                                        $nd = date('Y-m-d', strtotime('+2 months', $current_date));
                                    } elseif ($validity == 'months3') {
                                        $nd = date('Y-m-d', strtotime('+3 months', $current_date));
                                    } elseif ($validity == 'months6') {
                                        $nd = date('Y-m-d', strtotime('+6 months', $current_date));
                                    } elseif ($validity == 'year1') {
                                        $nd = date('Y-m-d', strtotime('+1 year', $current_date));
                                    } elseif ($validity == 'years2') {
                                        $nd = date('Y-m-d', strtotime('+2 years', $current_date));
                                    } elseif ($validity == 'years3') {
                                        $nd = date('Y-m-d', strtotime('+3 years', $current_date));
                                    } else {
                                        $nd = null;
                                    }

                                    $keyword->user_id       = $client->id;
                                    $keyword->status        = 'assigned';
                                    $keyword->validity      = $validity;
                                    $keyword->validity_date = $nd;

                                    $keyword->save();
                                }


                                $inv               = new Invoices();
                                $inv->cl_id        = $client->id;
                                $inv->client_name  = $client->fname . ' ' . $client->lname;
                                $inv->created_by   = 1;
                                $inv->created      = date('Y-m-d');
                                $inv->duedate      = date('Y-m-d');
                                $inv->datepaid     = date('Y-m-d');
                                $inv->subtotal     = $keyword->price;
                                $inv->total        = $keyword->price;
                                $inv->status       = $invoice_status;
                                $inv->pmethod      = 'PayNow';
                                $inv->recurring    = '0';
                                $inv->bill_created = 'yes';
                                $inv->note         = '';
                                $inv->save();
                                $inv_id = $inv->id;

                                $d           = new InvoiceItems();
                                $d->inv_id   = $inv_id;
                                $d->cl_id    = $client->id;
                                $d->item     = 'Purchase SMS Unit';
                                $d->qty      = 1;
                                $d->price    = $keyword->price;
                                $d->tax      = '0';
                                $d->discount = '0';
                                $d->subtotal = $keyword->price;
                                $d->total    = $keyword->price;
                                $d->save();

                                return redirect('user/invoices/all')->with([
                                    'message' => language_data('Purchase successfully.Wait for administrator response', Auth::guard('client')->user()->lan_id)
                                ]);
                            } else {
                                return redirect('user/keywords')->with([
                                    'message' => 'Purchase Keyword ' . $msg['status']
                                ]);
                            }

                        }
                    } else {
                        $this->buyUnitCancel();
                    }

                } else {
                    $this->buyUnitCancel();
                }
            } else {
                $this->buyUnitCancel();
            }
        } else {
            return redirect('user/keywords')->with([
                'message' => language_data('Data not found', Auth::guard('client')->user()->lan_id),
                'message_important' => true
            ]);
        }
    }


    //======================================================================
    // webxpayReceiveCallback Function Start Here
    //======================================================================
    public function webxpayReceiveCallback(Request $request)
    {
        require_once(app_path('libraray/webxpay/Crypt/RSA.php'));
        //initialize RSA
        $rsa = new \Crypt_RSA();
        //decode & get POST parameters
        $payment       = base64_decode($request->payment);
        $signature     = base64_decode($request->signature);
        $custom_fields = base64_decode($request->custom_fields);

        if ($payment && $signature && $custom_fields) {
            $gat_info = PaymentGateways::where('settings', 'webxpay')->where('status', 'Active')->first();
            if ($gat_info) {

                $rsa->loadKey($gat_info->extra_value);
                $signature_status = $rsa->verify($payment, $signature) ? TRUE : FALSE;
                if ($signature_status) {
                    //get payment response in segments
//payment format: order_id|order_refference_number|date_time_transaction|payment_gateway_used|status_code|comment;
                    $responseVariables      = explode('|', $payment);
                    $custom_fields_variable = explode('|', $custom_fields);

                    if (is_array($custom_fields_variable)) {
                        if ($custom_fields_variable['0'] == 'invoice') {
                            if (is_array($responseVariables) && $responseVariables['3'] == 00) {
                                $status = Invoices::where('cl_id', Auth::guard('client')->user()->id)->where('id', $custom_fields_variable['1'])->update([
                                    'status' => 'Paid',
                                    'datepaid' => date('Y-m-d')
                                ]);

                                if ($status) {
                                    return redirect('user/invoices/view/' . $custom_fields_variable['1'])->with([
                                        'message' => language_data('Invoice paid successfully', Auth::guard('client')->user()->lan_id)
                                    ]);
                                } else {
                                    return redirect('user/invoices/view/' . $custom_fields_variable['1'])->with([
                                        'message' => language_data('Invoice not paid. Please try again', Auth::guard('client')->user()->lan_id),
                                        'message_important' => true
                                    ]);
                                }
                            } else {
                                return redirect('user/invoices/view/' . $custom_fields_variable['1'])->with([
                                    'message' => $responseVariables['4'],
                                    'message_important' => true
                                ]);
                            }
                        } elseif ($custom_fields_variable['0'] == 'purchase_plan') {
                            if (is_array($responseVariables) && $responseVariables['3'] == 00) {
                                $id = $custom_fields_variable['1'];

                                if ($id) {

                                    $sms_plan = SMSPricePlan::find($id);

                                    $get_balance = SMSPlanFeature::where('pid', $id)->first();
                                    $sms_balance = (int)$get_balance->feature_value;

                                    $client = Client::find(Auth::guard('client')->user()->id);

                                    $total_balance     = $client->sms_limit + $sms_balance;
                                    $client->sms_limit = $total_balance;
                                    $client->save();

                                    $inv               = new Invoices();
                                    $inv->cl_id        = $client->id;
                                    $inv->client_name  = $client->fname . ' ' . $client->lname;
                                    $inv->created_by   = 1;
                                    $inv->created      = date('Y-m-d');
                                    $inv->duedate      = date('Y-m-d');
                                    $inv->datepaid     = date('Y-m-d');
                                    $inv->subtotal     = $sms_plan->price;
                                    $inv->total        = $sms_plan->price;
                                    $inv->status       = 'Paid';
                                    $inv->pmethod      = '';
                                    $inv->recurring    = '0';
                                    $inv->bill_created = 'yes';
                                    $inv->note         = '';
                                    $inv->save();
                                    $inv_id = $inv->id;

                                    $d           = new InvoiceItems();
                                    $d->inv_id   = $inv_id;
                                    $d->cl_id    = $client->id;
                                    $d->item     = $sms_plan->plan_name . ' Plan';
                                    $d->qty      = '1';
                                    $d->price    = $sms_plan->price;
                                    $d->tax      = '0';
                                    $d->discount = '0';
                                    $d->subtotal = $sms_plan->price;
                                    $d->total    = $sms_plan->price;
                                    $d->save();

                                    return redirect('user/invoices/all')->with([
                                        'message' => language_data('Purchase successfully.Wait for administrator response', Auth::guard('client')->user()->lan_id)
                                    ]);

                                } else {
                                    return redirect('user/sms/purchase-sms-plan')->with([
                                        'message' => language_data('Purchase successfully.Wait for administrator response', Auth::guard('client')->user()->lan_id)
                                    ]);
                                }
                            } else {
                                return redirect('user/sms/purchase-sms-plan')->with([
                                    'message' => $responseVariables['4'],
                                    'message_important' => true
                                ]);
                            }
                        } elseif ($custom_fields_variable['0'] == 'buy_unit') {
                            if (is_array($responseVariables) && $responseVariables['3'] == 00) {
                                $id   = $custom_fields_variable['1'];
                                $data = SMSBundles::where('unit_from', '<=', $id)->where('unit_to', '>=', $id)->first();

                                if ($data) {
                                    $unit_price      = $data->price;
                                    $amount_to_pay   = $id * $unit_price;
                                    $transaction_fee = ($amount_to_pay * $data->trans_fee) / 100;
                                    $total           = $amount_to_pay + $transaction_fee;

                                    $client = Client::find(Auth::guard('client')->user()->id);

                                    $total_balance     = $client->sms_limit + $id;
                                    $client->sms_limit = $total_balance;
                                    $client->save();

                                    $inv               = new Invoices();
                                    $inv->cl_id        = $client->id;
                                    $inv->client_name  = $client->fname . ' ' . $client->lname;
                                    $inv->created_by   = 1;
                                    $inv->created      = date('Y-m-d');
                                    $inv->duedate      = date('Y-m-d');
                                    $inv->datepaid     = date('Y-m-d');
                                    $inv->subtotal     = $amount_to_pay;
                                    $inv->total        = $total;
                                    $inv->status       = 'Paid';
                                    $inv->pmethod      = '';
                                    $inv->recurring    = '0';
                                    $inv->bill_created = 'yes';
                                    $inv->note         = '';
                                    $inv->save();
                                    $inv_id = $inv->id;

                                    $d           = new InvoiceItems();
                                    $d->inv_id   = $inv_id;
                                    $d->cl_id    = $client->id;
                                    $d->item     = 'Purchase SMS Unit';
                                    $d->qty      = $id;
                                    $d->price    = $unit_price;
                                    $d->tax      = $transaction_fee;
                                    $d->discount = '0';
                                    $d->subtotal = $amount_to_pay;
                                    $d->total    = $total;
                                    $d->save();

                                    return redirect('user/invoices/all')->with([
                                        'message' => language_data('Purchase successfully.Wait for administrator response', Auth::guard('client')->user()->lan_id)
                                    ]);

                                } else {
                                    return redirect('user/sms/buy-unit')->with([
                                        'message' => language_data('Data not found', Auth::guard('client')->user()->lan_id),
                                        'message_important' => true
                                    ]);
                                }
                            } else {
                                return redirect('user/sms/buy-unit')->with([
                                    'message' => $responseVariables['4'],
                                    'message_important' => true
                                ]);
                            }
                        } elseif ($custom_fields_variable['0'] == 'buy_keyword') {
                            if (is_array($responseVariables) && $responseVariables['3'] == 00) {
                                $id = $custom_fields_variable['1'];

                                $keyword = Keywords::find($id);

                                if ($keyword) {

                                    $client = Client::find(Auth::guard('client')->user()->id);

                                    $validity = $keyword->validity;

                                    $current_date = strtotime(date('Y-m-d'));
                                    if ($validity == 'month1') {
                                        $nd = date('Y-m-d', strtotime('+1 month', $current_date));
                                    } elseif ($validity == 'months2') {
                                        $nd = date('Y-m-d', strtotime('+2 months', $current_date));
                                    } elseif ($validity == 'months3') {
                                        $nd = date('Y-m-d', strtotime('+3 months', $current_date));
                                    } elseif ($validity == 'months6') {
                                        $nd = date('Y-m-d', strtotime('+6 months', $current_date));
                                    } elseif ($validity == 'year1') {
                                        $nd = date('Y-m-d', strtotime('+1 year', $current_date));
                                    } elseif ($validity == 'years2') {
                                        $nd = date('Y-m-d', strtotime('+2 years', $current_date));
                                    } elseif ($validity == 'years3') {
                                        $nd = date('Y-m-d', strtotime('+3 years', $current_date));
                                    } else {
                                        $nd = null;
                                    }

                                    $keyword->user_id       = $client->id;
                                    $keyword->status        = 'assigned';
                                    $keyword->validity      = $validity;
                                    $keyword->validity_date = $nd;

                                    $keyword->save();


                                    $inv               = new Invoices();
                                    $inv->cl_id        = $client->id;
                                    $inv->client_name  = $client->fname . ' ' . $client->lname;
                                    $inv->created_by   = 1;
                                    $inv->created      = date('Y-m-d');
                                    $inv->duedate      = date('Y-m-d');
                                    $inv->datepaid     = date('Y-m-d');
                                    $inv->subtotal     = $keyword->price;
                                    $inv->total        = $keyword->price;
                                    $inv->status       = 'Paid';
                                    $inv->pmethod      = 'webxpay';
                                    $inv->recurring    = '0';
                                    $inv->bill_created = 'yes';
                                    $inv->note         = '';
                                    $inv->save();
                                    $inv_id = $inv->id;

                                    $d           = new InvoiceItems();
                                    $d->inv_id   = $inv_id;
                                    $d->cl_id    = $client->id;
                                    $d->item     = 'Purchase Keyword: ' . $keyword->title;
                                    $d->qty      = 1;
                                    $d->price    = $keyword->price;
                                    $d->tax      = '0';
                                    $d->discount = '0';
                                    $d->subtotal = $keyword->price;
                                    $d->total    = $keyword->price;
                                    $d->save();

                                    return redirect('user/invoices/all')->with([
                                        'message' => language_data('Purchase successfully.Wait for administrator response', Auth::guard('client')->user()->lan_id)
                                    ]);

                                } else {
                                    return redirect('user/sms/buy-unit')->with([
                                        'message' => language_data('Data not found', Auth::guard('client')->user()->lan_id),
                                        'message_important' => true
                                    ]);
                                }
                            } else {
                                return redirect('user/sms/buy-unit')->with([
                                    'message' => $responseVariables['4'],
                                    'message_important' => true
                                ]);
                            }
                        } else {
                            return redirect('dashboard')->with([
                                'message' => language_data('Unauthorized payment', Auth::guard('client')->user()->lan_id),
                                'message_important' => true
                            ]);
                        }
                    } else {
                        return redirect('dashboard')->with([
                            'message' => language_data('Unauthorized payment', Auth::guard('client')->user()->lan_id),
                            'message_important' => true
                        ]);
                    }
                } else {
                    return redirect('dashboard')->with([
                        'message' => language_data('Unauthorized payment', Auth::guard('client')->user()->lan_id),
                        'message_important' => true
                    ]);
                }

            } else {
                return redirect('dashboard')->with([
                    'message' => language_data('Payment gateway not active', Auth::guard('client')->user()->lan_id),
                    'message_important' => true
                ]);
            }

        } else {
            return redirect('dashboard')->with([
                'message' => language_data('Data not found', Auth::guard('client')->user()->lan_id),
                'message_important' => true
            ]);
        }

    }


    //======================================================================
    // buyKeywordSuccess Function Start Here
    //======================================================================
    public function buyKeywordSuccess($token, $id)
    {

        $get_token = Auth::guard('client')->user()->pwresetexpiry;

        if ($get_token != $token) {
            return redirect('user/keywords')->with([
                'message' => language_data('Cancelled the Payment')
            ]);
        }

        $keyword = Keywords::find($id);

        if ($keyword) {

            $client = Client::find(Auth::guard('client')->user()->id);

            $validity = $keyword->validity;

            $current_date = strtotime(date('Y-m-d'));
            if ($validity == 'month1') {
                $nd = date('Y-m-d', strtotime('+1 month', $current_date));
            } elseif ($validity == 'months2') {
                $nd = date('Y-m-d', strtotime('+2 months', $current_date));
            } elseif ($validity == 'months3') {
                $nd = date('Y-m-d', strtotime('+3 months', $current_date));
            } elseif ($validity == 'months6') {
                $nd = date('Y-m-d', strtotime('+6 months', $current_date));
            } elseif ($validity == 'year1') {
                $nd = date('Y-m-d', strtotime('+1 year', $current_date));
            } elseif ($validity == 'years2') {
                $nd = date('Y-m-d', strtotime('+2 years', $current_date));
            } elseif ($validity == 'years3') {
                $nd = date('Y-m-d', strtotime('+3 years', $current_date));
            } else {
                $nd = null;
            }

            $keyword->user_id       = $client->id;
            $keyword->status        = 'assigned';
            $keyword->validity      = $validity;
            $keyword->validity_date = $nd;

            $payment_id = \Session::get('paypal_payment_id');

            if ($payment_id) {
                \Session::forget('paypal_payment_id');
                if (empty($request->get('PayerID')) || empty($request->get('token'))) {
                    return redirect('user/keywords')->with([
                        'message' => 'Payment failed',
                        'message_important' => true
                    ]);
                }

                $payment   = Payment::get($payment_id, $this->_api_context);
                $execution = new PaymentExecution();
                $execution->setPayerId($request->get('PayerID'));

                $result = $payment->execute($execution, $this->_api_context);
                if ($result->getState() == 'approved') {

                    $keyword->save();

                    $inv               = new Invoices();
                    $inv->cl_id        = $client->id;
                    $inv->client_name  = $client->fname . ' ' . $client->lname;
                    $inv->created_by   = 1;
                    $inv->created      = date('Y-m-d');
                    $inv->duedate      = date('Y-m-d');
                    $inv->datepaid     = date('Y-m-d');
                    $inv->subtotal     = $keyword->price;
                    $inv->total        = $keyword->price;
                    $inv->status       = 'Paid';
                    $inv->pmethod      = '';
                    $inv->recurring    = '0';
                    $inv->bill_created = 'yes';
                    $inv->note         = '';
                    $inv->save();
                    $inv_id = $inv->id;

                    $d           = new InvoiceItems();
                    $d->inv_id   = $inv_id;
                    $d->cl_id    = $client->id;
                    $d->item     = 'Purchase Keyword: ' . $keyword->title;
                    $d->qty      = 1;
                    $d->price    = $keyword->price;
                    $d->tax      = '0';
                    $d->discount = '0';
                    $d->subtotal = '0';
                    $d->total    = $keyword->price;
                    $d->save();

                    return redirect('user/invoices/all')->with([
                        'message' => language_data('Purchase successfully.Wait for administrator response', Auth::guard('client')->user()->lan_id)
                    ]);
                }
                return redirect('user/keywords')->with([
                    'message' => 'Payment failed',
                    'message_important' => true
                ]);
            }


            $keyword->save();

            $inv               = new Invoices();
            $inv->cl_id        = $client->id;
            $inv->client_name  = $client->fname . ' ' . $client->lname;
            $inv->created_by   = 1;
            $inv->created      = date('Y-m-d');
            $inv->duedate      = date('Y-m-d');
            $inv->datepaid     = date('Y-m-d');
            $inv->subtotal     = $keyword->price;
            $inv->total        = $keyword->price;
            $inv->status       = 'Paid';
            $inv->pmethod      = '';
            $inv->recurring    = '0';
            $inv->bill_created = 'yes';
            $inv->note         = '';
            $inv->save();
            $inv_id = $inv->id;

            $d           = new InvoiceItems();
            $d->inv_id   = $inv_id;
            $d->cl_id    = $client->id;
            $d->item     = 'Purchase Keyword: ' . $keyword->title;
            $d->qty      = 1;
            $d->price    = $keyword->price;
            $d->tax      = '0';
            $d->discount = '0';
            $d->subtotal = '0';
            $d->total    = $keyword->price;
            $d->save();

            return redirect('user/invoices/all')->with([
                'message' => language_data('Purchase successfully.Wait for administrator response', Auth::guard('client')->user()->lan_id)
            ]);

        } else {
            return redirect('user/keywords')->with([
                'message' => 'Data not found',
                'message_important' => true
            ]);
        }
    }


    //======================================================================
    // buyKeywordCancel Function Start Here
    //======================================================================
    public function buyKeywordCancel()
    {
        return redirect('user/keywords')->with([
            'message' => language_data('Cancelled the Payment')
        ]);
    }


    //======================================================================
    // buyKeywordWithStripe Function Start Here
    //======================================================================
    public function buyKeywordWithStripe(Request $request)
    {

        $cmd     = $request->get('cmd');
        $keyword = Keywords::find($cmd);

        if (!$keyword) {
            return redirect('user/keywords')->with([
                'message' => 'Data not found',
                'message_important' => true
            ]);
        }

        $gat_info = PaymentGateways::where('settings', 'stripe')->first();
        $stripe   = Stripe::make($gat_info->extra_value, '2016-07-06');
        $client   = Client::find(Auth::guard('client')->user()->id);
        $email    = $client->email;

        try {
            $customer = $stripe->customers()->create([
                'email' => $email,
                'source' => $request->stripeToken
            ]);

            $customer_id = $customer['id'];


            $meta_data = [
                'customer_name' => $client->fname . ' ' . $client->lname,
                'country' => $client->country,
                'ip_address' => \request()->ip()
            ];

            if ($client->address1) {
                $meta_data['address'] = $client->address1 . ' ' . $client->address2;
            }
            if ($client->city) {
                $meta_data['city'] = $client->city;
            }
            if ($client->postcode) {
                $meta_data['postcode'] = $client->postcode;
            }


            $stripe->charges()->create([
                'customer' => $customer_id,
                'currency' => app_config('Currency'),
                'amount' => $keyword->price,
                'receipt_email' => $email,
                'metadata' => $meta_data
            ]);


            $validity = $keyword->validity;

            $current_date = strtotime(date('Y-m-d'));
            if ($validity == 'month1') {
                $nd = date('Y-m-d', strtotime('+1 month', $current_date));
            } elseif ($validity == 'months2') {
                $nd = date('Y-m-d', strtotime('+2 months', $current_date));
            } elseif ($validity == 'months3') {
                $nd = date('Y-m-d', strtotime('+3 months', $current_date));
            } elseif ($validity == 'months6') {
                $nd = date('Y-m-d', strtotime('+6 months', $current_date));
            } elseif ($validity == 'year1') {
                $nd = date('Y-m-d', strtotime('+1 year', $current_date));
            } elseif ($validity == 'years2') {
                $nd = date('Y-m-d', strtotime('+2 years', $current_date));
            } elseif ($validity == 'years3') {
                $nd = date('Y-m-d', strtotime('+3 years', $current_date));
            } else {
                $nd = null;
            }

            $keyword->user_id       = $client->id;
            $keyword->status        = 'assigned';
            $keyword->validity      = $validity;
            $keyword->validity_date = $nd;

            $keyword->save();


            $inv               = new Invoices();
            $inv->cl_id        = $client->id;
            $inv->client_name  = $client->fname . ' ' . $client->lname;
            $inv->created_by   = 1;
            $inv->created      = date('Y-m-d');
            $inv->duedate      = date('Y-m-d');
            $inv->datepaid     = date('Y-m-d');
            $inv->subtotal     = $keyword->price;
            $inv->total        = $keyword->price;
            $inv->status       = 'Paid';
            $inv->pmethod      = 'Stripe';
            $inv->recurring    = '0';
            $inv->bill_created = 'yes';
            $inv->note         = '';
            $inv->save();
            $inv_id = $inv->id;

            $d           = new InvoiceItems();
            $d->inv_id   = $inv_id;
            $d->cl_id    = $client->id;
            $d->item     = 'Purchase Keyword: ' . $keyword->title;
            $d->qty      = $cmd;
            $d->price    = $keyword->price;
            $d->tax      = '0';
            $d->discount = '0';
            $d->subtotal = $keyword->price;
            $d->total    = $keyword->price;
            $d->save();

            return redirect('user/invoices/all')->with([
                'message' => language_data('Purchase successfully.Wait for administrator response', Auth::guard('client')->user()->lan_id)
            ]);

        } catch (StripeException $e) {
            return redirect('user/keywords')->with([
                'message' => $e->getMessage(),
                'message_important' => true
            ]);
        }
    }


}
