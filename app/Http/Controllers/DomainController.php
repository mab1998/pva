<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\DomainProviders;

use phpseclib\Net\SSH2 as SSH;



class DomainController extends Controller
{
    public function ssh()
    {
 
        $ssh = New SSH("35.194.15.243", "22");
        $ssh->login("root", "b");
        $response = $ssh->exec('ls ');
        return $response ;



    }

    public function DomainRemoveFromCard($domain)
    {
 
        $cart = session()->get('cart');

        if(isset($cart[$domain])) {

            unset($cart[$domain]);

            session()->put('cart', $cart);
        }

        session()->flash('success', 'Product removed successfully');

    }
    public function DomainAddToCard($domain)
    {
        // return $request;
        $cart = session()->get('cart');
        // return $cart;

        // $domain_name=$request->domain_name;
        // return($domain_name);
        $results = self::FindDomain($domain);
        foreach ($results as  $result) {
            if ($result->domainName==$domain) {
                
                // if cart is empty then this the first product
                if(!$cart) {
        
                    $cart = [
                            $result->domainName => [
                                "domain name" => $result->domainName,
                                "quantity" => 1,
                                "price" => $result->purchasePrice
                            ]
                    ];
        
                    session()->put('cart', $cart);
        
                    return redirect()->back()->with('success', 'Product added to cart successfully!');
                }
                
                // if cart not empty then check if this product exist then increment quantity
                if(isset($cart[$result->domainName])) {
        
                    $cart[$result->domainName]['quantity']++;
        
                    session()->put('cart', $cart);
        
                    return redirect()->back()->with('success', 'You have been purchase this domain!');
        
                }
                
                        // if item not exist in cart then add to cart with quantity = 1
                $cart[$result->domainName] = [
                    "domain name" => $result->domainName,
                    "quantity" => 1,
                    "price" => $result->purchasePrice
                ];
        
                session()->put('cart', $cart);
        
                return redirect()->back()->with('success', 'Product added to cart successfully!');

                break;
            }
        }
         

        // return $response;
        // return view('client1.domain-search');
        

    }
    public function SearchDomain()
    {

        
        // return $response;
        return view('client1.domain-search');
        

    }

    public function FindDomain($domain){
        $API_SECRET = 'mab1998-test';
        $API_KEY = '4b5ac29fa91f4b233031a542575c19921685f28a';
        $AUTH="$API_SECRET:$API_KEY";
        $AUTH= base64_encode($AUTH);

        // $Base=base64_encode($API_KEY:$API_SECRET)
        



        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.dev.name.com/v4/domains:search",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS =>"{\"keyword\":\".'$domain'.\"}",
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json",
            "Authorization:Basic $AUTH"
            // "Cookie: REG_IDT=ec775c36c015a3843f1c2236427fc90c"
        ),
        ));

        $results = curl_exec($curl);

        $results = json_decode($results)->results;
        // return $results;

        curl_close($curl);
        return $results;
    }
    public function post_search_domain(Request $request)
    {
        $domain=$request->domain;
        $API_SECRET = 'mab1998-test';
        $API_KEY = '4b5ac29fa91f4b233031a542575c19921685f28a';
        $AUTH="$API_SECRET:$API_KEY";
        $AUTH= base64_encode($AUTH);

        // $Base=base64_encode($API_KEY:$API_SECRET)
        



        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.dev.name.com/v4/domains:search",
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => "",
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => "POST",
        CURLOPT_POSTFIELDS =>"{\"keyword\":\".'$domain'.\"}",
        CURLOPT_HTTPHEADER => array(
            "Content-Type: application/json",
            "Authorization:Basic $AUTH"
            // "Cookie: REG_IDT=ec775c36c015a3843f1c2236427fc90c"
        ),
        ));

        $results = curl_exec($curl);

        $results = json_decode($results)->results;
        // return $results;

        curl_close($curl);

        // return $response;
        return view('client1.domain-search', compact('results','domain'));
        

    }

    public function get_provider(Request $request)
    {
        $Providers = DomainProviders::all();
        // return $Providers;
        // $name = $request->input('name');

        return view('admin1.domain-provider', compact('Providers'));

    }

    public function update_provider(Request $request)
    {
        $Providers = DomainProviders::all();

        // return $request;

        $Provider = DomainProviders::find($request->get("pid"));
        $Provider->status=$request->get("status");
        $Provider ->username=$request->get("username");
        $Provider ->user_id=$request->get("user_id");
        $Provider ->api_key=$request->get("api_key");
        $Provider->save();

        return redirect('registrars')->with([
            'message' => language_data('Succeful updated')
        ]);

        // return $Providers;
        // $name = $request->input('name');

        return view('admin1.domain-provider', compact('Providers'));

    }
    public function purchase_domain(Request $request)
    {

        // GoDaddy API Credentials
        $API_SECRET = '2XrqPQBRi4GNGgxii9dbcv';
        $API_KEY = '3mM44UbBuALhTg_7reZfCwjMjWPSQzALRKKdb';

        // The domain name you would like to purchase
        $domain = "ambsmart22.com";

        // The value for agreement keys found in $bodyContent variable - further down page
        $dnra = "DNRA";

        // Whether or not to auto renew: true or false
        $autoRenew = 'true';

        // Period of years 1-10, default value: 2
        $period = 2;

        // Whether or not to include privacy in purchase: true or false
        $privacy = 'false';

        // Agreed to datetime of purchase
        $agreeAtTime = "2020-8-22T18:50:13Z";

        // IP address from wich purchase is being made
        $agreedByIP = "197.207.35.6";

        // Nameservers to point domain too
        $nameserver_one = "ns-155.awsdns-22.com";
        $nameserver_two = "ns-326-awsdns-15.net";


        // address information for domain
        $addressOne = "12345 Test Blvd";
        $addressTwo = " ";
        $city = "Austin";
        $country = "US";
        $postalCode = "78910";
        $state = "Texas";

        // contact information for domain
        $email = "info@dnstrategies.com";
        $fax = "+1.5121234567";
        $jobTitle = "Owner";
        $nameFirst = "Joe";
        $nameLast = "Doe";
        $nameMiddle = "B";
        $organization = "JBD Strategies, LLC";
        $phone = "+1.5121234567";



        $bodyContent = '
        {
        "consent": {
            "agreedAt": "'.$agreeAtTime.'",
            "agreedBy": "'.$agreedByIP.'",
            "agreementKeys": [
            "'.$dnra.'"
            ]
        },
        "contactAdmin": {
            "address1": "'.$addressOne.'",
            "address2": "'.$addressTwo.'",
            "city": "'.$city.'",
            "country": "'.$country.'",
            "postalCode": "'.$postalCode.'",
            "state": "'.$state.'"
            },
            "email": "'.$email.'",
            "fax": "'.$fax.'",
            "jobTitle": "'.$jobTitle.'",
            "nameFirst": "'.$nameFirst.'",
            "nameLast": "'.$nameLast.'",
            "nameMiddle": "'.$nameMiddle.'",
            "organization": "'.$organization.'",
            "phone": "'.$phone.'"
        },
        "contactBilling": {
            "addressMailing": {
            "address1": "'.$addressOne.'",
            "address2": "'.$addressTwo.'",
            "city": "'.$city.'",
            "country": "'.$country.'",
            "postalCode": "'.$postalCode.'",
            "state": "'.$state.'"
            },
            "email": "'.$email.'",
            "fax": "'.$fax.'",
            "jobTitle": "'.$jobTitle.'",
            "nameFirst": "'.$nameFirst.',
            "nameLast": "'.$nameLast.'",
            "nameMiddle": "'.$nameMiddle.'",
            "organization": "'.$organization.'",
            "phone": "'.$phone.'"
        },
        "contactRegistrant": {
            "addressMailing": {
            "address1": "'.$addressOne.'",
            "address2": "'.$addressTwo.'",
            "city": "'.$city.'",
            "country": "'.$country.'",
            "postalCode": "'.$postalCode.'",
            "state": "'.$state.'"
            },
            "email": "'.$email.'",
            "fax": "'.$fax.'",
            "jobTitle": "'.$jobTitle.'",
            "nameFirst": "'.$nameFirst.',
            "nameLast": "'.$nameLast.'",
            "nameMiddle": "'.$nameMiddle.'",
            "organization": "'.$organization.'",
            "phone": "'.$phone.'"
        },
        "contactTech": {
            "addressMailing": {
            "address1": "'.$addressOne.'",
            "address2": "'.$addressTwo.'",
            "city": "'.$city.'",
            "country": "'.$country.'",
            "postalCode": "'.$postalCode.'",
            "state": "'.$state.'"
            },
            "email": "'.$email.'",
            "fax": "'.$fax.'",
            "jobTitle": "'.$jobTitle.'",
            "nameFirst": "'.$nameFirst.',
            "nameLast": "'.$nameLast.'",
            "nameMiddle": "'.$nameMiddle.'",
            "organization": "'.$organization.'",
            "phone": "'.$phone.'"
        },
        "domain": "'.$domain.'",
        "nameServers": [
            "'.$nameserver_one.'","'.$nameserver_two.'"
        ],
        "period": '.$period.',
        "privacy": '.$privacy.',
        "renewAuto": '.$autoRenew.'
        }';





        // url for GoDaddy API
        $url = "https://api.ote-godaddy.com/v1/domains/purchase";

        //echo $url;
        //die();

        // set your key and secret
        $header = array(
                "Authorization: sso-key $API_KEY:$API_SECRET",
                'Content-Type: application/json',
                'Accept: application/json'
        );

        //open connection
        $ch = curl_init();
        $timeout=60;

        //set the url and other options for curl
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);  
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST'); // Values: GET, POST, PUT, DELETE, PATCH, UPDATE 
        curl_setopt($ch, CURLOPT_POSTFIELDS, $bodyContent);
        //curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        // return $bodyContent;

        //execute call and return response data.
        $result = curl_exec($ch);

        return $result;

        //close curl connection
        curl_close($ch);

        // decode the json response
        $dn = json_decode($result, true);


        $errmsg = '';

        // check if error code
        if(isset($dn['code'])){

        $errmsg = explode(":",$dn['message']);

        $errmsg = '<h2 style="text-align:center;">'.$errmsg[0].'</h2>';
        
        } else {

        $errmsg = 'Domain purchased.';

        }

    return $errmsg;

    }
}
