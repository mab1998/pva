<?php
namespace App\library;


class DomainNameAPI_PHPLibrary
{
    // VARIABLES
	public $_VERSION_ = "1.2";
    private $_USERDATA_USERNAME = "ownername";
    private $_USERDATA_PASSWORD = "ownerpass";
    private $_URL_SERVICE = "http://api-ote.domainnameapi.com/DomainAPI.svc";
    private $_CONNECTION_METHOD = "APIConnection_SOAP";
    private $_VERSION = "1.0.0.0";
    private $con = null;
    private $_useTestMode = true;
    private $_useCaching = false;
    private $_cache = array();

    public $__REQUEST = array();
    public $__RESPONSE = array();
        
    // CONSTRUCTORS
    // Default constructors
    function __construct() 
	{ 
		$this->useTestMode(true);
		$this->useCaching(false);
		$this->setConnectionMethod("Auto"); 
	}
    function DomainNameAPI_PHPLibrary() 
	{
		$this->useTestMode(true);
		$this->useCaching(false);
		$this->setConnectionMethod("Auto");
	}
    
    // METHODS  
    
    // USE TEST PLATFORM OR REAL PLATFORM
    // if value equals false, use real platform, otherwise use test platform
    public function useTestMode($value)
    { 
        if($value === false)
        {
            // REAL MODE
            $this->_useTestMode = false;
            $this->_URL_SERVICE = "http://api.domainnameapi.com/DomainAPI.svc";
        }
        else
        {
            // TEST MODE
            $this->_useTestMode = true;
            $this->_URL_SERVICE = "http://api-ote.domainnameapi.com/DomainAPI.svc";
            $this->_USERDATA_USERNAME = "ownername";
            $this->_USERDATA_PASSWORD = "ownerpass";
        }
		
		$this->setConnectionMethod("Auto");
    }

	// CACHING 
	
    // Caching will be enabled or not
    public function useCaching($value)
    { 
        if($value === true)
        { $this->_useCaching = true; }
        else
        { $this->_useCaching = false; }

    }
    
	// Remove domain's value from cache
	public function removeDomainFromCache($DomainName)
	{
		if(isset($this->_cache[$DomainName]))
		{ unset($this->_cache[$DomainName]); }
	}
	
    // Get used mode? TEST => true, REAL => false
    public function isTestMode() 
    { return ($this->_useTestMode === false) ? false : true; }
    
    // SET Username and Password
    public function setUser($UserName, $Password)
    {
        $this->_USERDATA_USERNAME = $UserName;
        $this->_USERDATA_PASSWORD = $Password;
    }
    
    // Get connection method
    public function getConnectionMethod()
    {
        return $this->_CONNECTION_METHOD;
    }
    
    // Set connection method
    public function setConnectionMethod($Method)
    {
        switch(strtoupper(trim($Method)))
        {
            case "SOAP":
                $this->_CONNECTION_METHOD = "APIConnection_SOAP";
                break;
                
            case "CURL";
                $this->_CONNECTION_METHOD = "APIConnection_CURL";
                break;
                
            default:
                if(class_exists("SoapClient"))
                { $this->_CONNECTION_METHOD = "APIConnection_SOAP"; }
                elseif(function_exists("curl_init"))
                { $this->_CONNECTION_METHOD = "APIConnection_CURL"; }
                else
                { 
                    // DUZELT
                    $this->_CONNECTION_METHOD = "ALL_OF_CONNECTION_METHODS_NOT_AVAILABLE"; 
                }
                break; 
        }

        // Prepare connection
        $this->con = new $this->_CONNECTION_METHOD($this->_URL_SERVICE);
        
    }
    
    
    
    
    
    // API METHODS
    
    // Check domain is avaliable? Ex: ('example1', 'example2'), ('com', 'net', 'org')
    public function CheckAvailability($Domains, $TLDs)
    {
        $parameters = array(
            "request" => array(
                "Password" => $this->_USERDATA_PASSWORD,
                "UserName" => $this->_USERDATA_USERNAME,
                "DomainNameList" => $Domains,
                "TldList" => $TLDs,
            )
        );
        
        // Check availability via already prepared connection
        $response = $this->con->CheckAvailability($parameters);

        // Log last request and response
        $this->__REQUEST = $parameters; $this->__RESPONSE = $response;

        return $response;
    }
    
    
	
	
    // Get domain details
    public function GetList()
    {
        $parameters = array(
            "request" => array(
                "Password" => $this->_USERDATA_PASSWORD,
                "UserName" => $this->_USERDATA_USERNAME
            )
        );
        
        // Get domain id via already prepared connection
        $response = $this->con->GetList($parameters);

        // Log last request and response
        $this->__REQUEST = $parameters; $this->__RESPONSE = $response;

        return $response;
    }
    
    
    // Get domain details
    public function GetDetails($DomainName)
    {
	
		// If caching enabled  
		if($this->_useCaching == true)
		{
			
			// If is there any cached value for this domain?
			if(isset($this->_cache[$DomainName]["result"]))
			{
				// Return cached value
				$result = $this->_cache[$DomainName]["result"];
				$result["fromCache"] = true;

				return $result;
			}
			
		}
		
        $parameters = array(
            "request" => array(
                "Password" => $this->_USERDATA_PASSWORD,
                "UserName" => $this->_USERDATA_USERNAME,
                "DomainName" => $DomainName
            )
        );
        
        // Get domain id via already prepared connection
        $response = $this->con->GetDetails($parameters);

        // Log last request and response
        $this->__REQUEST = $parameters; $this->__RESPONSE = $response;

		// If caching enabled  
		if($this->_useCaching == true)
		{
			$this->_cache[$DomainName]["result"] = $response;
			$this->_cache[$DomainName]["date"] = date("Y-m-d H:i:s");
		}
		
        return $response;
    }
    
    
    
    
    // Modify nameservers
    public function ModifyNameServer($DomainName, $NameServers)
    {
        $parameters = array(
            "request" => array(
                "Password" => $this->_USERDATA_PASSWORD,
                "UserName" => $this->_USERDATA_USERNAME,
                "DomainName" => $DomainName,
                "NameServerList" => $NameServers
            )
        );
        
		// We will modify domain, so remove it from cache
		$this->removeDomainFromCache($DomainName);
		
        // Check availability via already prepared connection
        $response = $this->con->ModifyNameServer($parameters);

        // Log last request and response
        $this->__REQUEST = $parameters; $this->__RESPONSE = $response;

        return $response;
    }
    


    // Enable Theft Protection Lock
    public function EnableTheftProtectionLock($DomainName)
    {
        $parameters = array(
            "request" => array(
                "Password" => $this->_USERDATA_PASSWORD,
                "UserName" => $this->_USERDATA_USERNAME,
                "DomainName" => $DomainName
            )
        );
        
		// We will modify domain, so remove it from cache
		$this->removeDomainFromCache($DomainName);
		
        // Enable theft protection lock via already prepared connection
        $response = $this->con->EnableTheftProtectionLock($parameters);

        // Log last request and response
        $this->__REQUEST = $parameters; $this->__RESPONSE = $response;

        return $response;
    }
            

    // Disable Theft Protection Lock
    public function DisableTheftProtectionLock($DomainName)
    {
        $parameters = array(
            "request" => array(
                "Password" => $this->_USERDATA_PASSWORD,
                "UserName" => $this->_USERDATA_USERNAME,
                "DomainName" => $DomainName
            )
        );
        
		// We will modify domain, so remove it from cache
		$this->removeDomainFromCache($DomainName);
		
        // Disable theft protection lock via already prepared connection
        $response = $this->con->DisableTheftProtectionLock($parameters);

        // Log last request and response
        $this->__REQUEST = $parameters; $this->__RESPONSE = $response;

        return $response;
    }
    



    // CHILD NAMESERVER MANAGEMENT
    
    // Add Child Nameserver
    public function AddChildNameServer($DomainName, $NameServer, $IPAdresses)
    {
        $parameters = array(
            "request" => array(
                "Password" => $this->_USERDATA_PASSWORD,
                "UserName" => $this->_USERDATA_USERNAME,
                "DomainName" => $DomainName,
                "ChildNameServer" => $NameServer,
                "IpAddressList" => $IPAdresses
            )
        );
        
		// We will modify domain, so remove it from cache
		$this->removeDomainFromCache($DomainName);
		
        // Add child nameserver via already prepared connection
        $response = $this->con->AddChildNameServer($parameters);

        // Log last request and response
        $this->__REQUEST = $parameters; $this->__RESPONSE = $response;

        return $response;
    }
    
    
    
    // Delete Child Nameserver
    public function DeleteChildNameServer($DomainName, $NameServer)
    {
        $parameters = array(
            "request" => array(
                "Password" => $this->_USERDATA_PASSWORD,
                "UserName" => $this->_USERDATA_USERNAME,
                "DomainName" => $DomainName,
                "ChildNameServer" => $NameServer
            )
        );
        
		// We will modify domain, so remove it from cache
		$this->removeDomainFromCache($DomainName);
		
        // Delete child nameserver via already prepared connection
        $response = $this->con->DeleteChildNameServer($parameters);

        // Log last request and response
        $this->__REQUEST = $parameters; $this->__RESPONSE = $response;

        return $response;
    }
    
    
    // Modify Child Nameserver
    public function ModifyChildNameServer($DomainName, $NameServer, $IPAdresses)
    {
        $parameters = array(
            "request" => array(
                "Password" => $this->_USERDATA_PASSWORD,
                "UserName" => $this->_USERDATA_USERNAME,
                "DomainName" => $DomainName,
                "ChildNameServer" => $NameServer,
                "IpAddressList" => $IPAdresses
            )
        );
        
		// We will modify domain, so remove it from cache
		$this->removeDomainFromCache($DomainName);
		
        // Add child nameserver via already prepared connection
        $response = $this->con->ModifyChildNameServer($parameters);

        // Log last request and response
        $this->__REQUEST = $parameters; $this->__RESPONSE = $response;

        return $response;
    }
    
    
    
    
    // CONTACT MANAGEMENT
    
    // Get Domain Contact informations
    public function GetContacts($DomainName)
    {
        $parameters = array(
            "request" => array(
                "Password" => $this->_USERDATA_PASSWORD,
                "UserName" => $this->_USERDATA_USERNAME,
                "DomainName" => $DomainName
            )
        );
        
        // Get Domain Contact informations via already prepared connection
        $response = $this->con->GetContacts($parameters);

        // Log last request and response
        $this->__REQUEST = $parameters; $this->__RESPONSE = $response;

        return $response;
    }
    
    
    
    // Set domain cantacts
    public function SaveContacts($DomainName, $Contacts)
    {
        $parameters = array(
            "request" => array(
                "Password" => $this->_USERDATA_PASSWORD,
                "UserName" => $this->_USERDATA_USERNAME,
                "DomainName" => $DomainName,
                "AdministrativeContact" => $Contacts["Administrative"],
                "BillingContact" => $Contacts["Billing"],
                "TechnicalContact" => $Contacts["Technical"],
                "RegistrantContact" => $Contacts["Registrant"]
            )
        );
        
		// We will modify domain, so remove it from cache
		$this->removeDomainFromCache($DomainName);
		
        // Register domain via already prepared connection
        $response = $this->con->SaveContacts($parameters);

        // Log last request and response
        $this->__REQUEST = $parameters; $this->__RESPONSE = $response;

        return $response;
    }
    
    
    
    // DOMAIN TRANSFER (INCOMING DOMAIN)
    
    // Start domain transfer (Incoming domain)
    public function Transfer($DomainName, $AuthCode)
    {
        $parameters = array(
            "request" => array(
                "Password" => $this->_USERDATA_PASSWORD,
                "UserName" => $this->_USERDATA_USERNAME,
                "DomainName" => $DomainName,
                "AuthCode" => $AuthCode
            )
        );
        
		// We will modify domain, so remove it from cache
		$this->removeDomainFromCache($DomainName);
		
        // Start domain transfer via already prepared connection
        $response = $this->con->Transfer($parameters);

        // Log last request and response
        $this->__REQUEST = $parameters; $this->__RESPONSE = $response;

        return $response;
    }
    


    
    // Cancel domain transfer (Incoming domain)
    public function CancelTransfer($DomainName)
    {
        $parameters = array(
            "request" => array(
                "Password" => $this->_USERDATA_PASSWORD,
                "UserName" => $this->_USERDATA_USERNAME,
                "DomainName" => $DomainName
            )
        );
        
		// We will modify domain, so remove it from cache
		$this->removeDomainFromCache($DomainName);
		
        // Cancel domain transfer via already prepared connection
        $response = $this->con->CancelTransfer($parameters);

        // Log last request and response
        $this->__REQUEST = $parameters; $this->__RESPONSE = $response;

        return $response;
    }
    


    // Renew domain
    public function Renew($DomainName, $Period)
    {
        $parameters = array(
            "request" => array(
                "Password" => $this->_USERDATA_PASSWORD,
                "UserName" => $this->_USERDATA_USERNAME,
                "DomainName" => $DomainName,
                "Period" => $Period
            )
        );
        
		// We will modify domain, so remove it from cache
		$this->removeDomainFromCache($DomainName);
		
        // Renew domain via already prepared connection
        $response = $this->con->Renew($parameters);

        // Log last request and response
        $this->__REQUEST = $parameters; $this->__RESPONSE = $response;

        return $response;
    }
    
    
    
    // Register domain with contact informations
    public function RegisterWithContactInfo($DomainName, $Period, $Contacts, $NameServers = array("dns.domainnameapi.com", "web.domainnameapi.com"), $TheftProtectionLock = true, $PrivacyProtection = false)
    {
        $parameters = array(
            "request" => array(
                "Password" => $this->_USERDATA_PASSWORD,
                "UserName" => $this->_USERDATA_USERNAME,
                "DomainName" => $DomainName,
                "Period" => $Period,
                "NameServerList" => $NameServers,
                "LockStatus" => $TheftProtectionLock,
                "PrivacyProtectionStatus" => $PrivacyProtection,
                "AdministrativeContact" => $Contacts["Administrative"],
                "BillingContact" => $Contacts["Billing"],
                "TechnicalContact" => $Contacts["Technical"],
                "RegistrantContact" => $Contacts["Registrant"]
            )
        );
		
		// We will modify domain, so remove it from cache
		$this->removeDomainFromCache($DomainName);
		
        // Register domain via already prepared connection
        $response = $this->con->RegisterWithContactInfo($parameters);

        // Log last request and response
        $this->__REQUEST = $parameters; $this->__RESPONSE = $response;

        return $response;
    }
    

    // Modify privacy protection status of domain
    public function ModifyPrivacyProtectionStatus($DomainName, $Status, $Reason = "Owner request")
    {
		if(trim($Reason) == "") { $Reason = "Owner request"; }
		
        $parameters = array(
            "request" => array(
                "Password" => $this->_USERDATA_PASSWORD,
                "UserName" => $this->_USERDATA_USERNAME,
                "DomainName" => $DomainName,
				"ProtectPrivacy" => $Status,
				"Reason" => $Reason
            )
        );
        
		// We will modify domain, so remove it from cache
		$this->removeDomainFromCache($DomainName);
		
        // Modify privacy protection status of domain via already prepared connection
        $response = $this->con->ModifyPrivacyProtectionStatus($parameters);

        // Log last request and response
        $this->__REQUEST = $parameters; $this->__RESPONSE = $response;

        return $response;
    }
    
    
    // Sync domain
    public function SyncFromRegistry($DomainName)
    {
        $parameters = array(
            "request" => array(
                "Password" => $this->_USERDATA_PASSWORD,
                "UserName" => $this->_USERDATA_USERNAME,
                "DomainName" => $DomainName
            )
        );
        
		// We will modify domain, so remove it from cache
		$this->removeDomainFromCache($DomainName);
		
        // Sync domain via already prepared connection
        $response = $this->con->SyncFromRegistry($parameters);

        // Log last request and response
        $this->__REQUEST = $parameters; $this->__RESPONSE = $response;

        return $response;
    }
    
    
};


?>