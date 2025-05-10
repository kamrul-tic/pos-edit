<?php

namespace App\Services;

use App\SMSProviders\BdBulkSms;
use App\SMSProviders\ReveSms;
use App\SMSProviders\TonkraSms;
use App\SMSProviders\ElitbuzzSms;

class SmsService
{
    private $_tonkraSms;
    private $_elitbuzzSms;
    private $_reveSms;
    private $_bdbulkSms;

    public function __construct(TonkraSms $tonkraSms, ElitbuzzSms $elitbuzzSms, ReveSms $reveSms, BdBulkSms $bdBulkSms)
    {
        $this->_tonkraSms = $tonkraSms;
        $this->_elitbuzzSms = $elitbuzzSms;
        $this->_reveSms = $reveSms;
        $this->_bdbulkSms = $bdBulkSms;
    }

    public function initialize($data)
    {
        $smsServiceProviderName = $data['sms_provider_name'];
        
        switch ($smsServiceProviderName) {
            case 'tonkra':
                return $this->_tonkraSms->send($data);
            case 'elitbuzz':
                return $this->_elitbuzzSms->send($data);
            case 'revesms':
                return $this->_reveSms->send($data);
            case 'bdbulksms':
                return $this->_bdbulkSms->send($data);
            default:
                break;
        }
    }
}