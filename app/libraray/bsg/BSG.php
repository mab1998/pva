<?php

require_once 'BSG/SmsApiClient.php';
require_once 'BSG/HLRApiClient.php';
require_once 'BSG/ViberApiClient.php';

class BSG
{
    private $apiKey;
    private $sender;
    private $tariff;
    private $viberSender;
    private $apiSource;

    public function __construct($apiKey, $sender = null, $viberSender = null, $tariff = null, $apiSource = null) {
        $this->apiKey = $apiKey;
        $this->sender = $sender;
        $this->tariff = $tariff;
        $this->viberSender = $viberSender;
        $this->apiSource = $apiSource;
    }

    /**
     * @return SmsApiClient
     */
    public function getSmsClient() {
        return new SmsApiClient($this->apiKey, $this->sender, $this->tariff, $this->apiSource);
    }

    /**
     * @return HLRApiClient
     */
    public function getHLRClient() {
        return new HLRApiClient($this->apiKey, $this->tariff, $this->apiSource);
    }

    /**
     * @return ViberApiClient
     */
    public function getViberClient() {
        return new ViberApiClient($this->apiKey, $this->viberSender, $this->apiSource);
    }

}