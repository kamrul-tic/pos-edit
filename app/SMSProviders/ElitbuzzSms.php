<?php 

namespace App\SMSProviders;

use App\Contracts\Sms\SendSmsInterface;
use App\Contracts\Sms\CheckBalanceInterface;
use App\Models\ExternalService;

class ElitbuzzSms implements SendSmsInterface, CheckBalanceInterface
{
    public function send($data)
    {
        $data['details'] = json_decode($data['details']);
        $data['api_key'] = $data['details']->api_key;
        $data['sender_id'] = $data['details']->sender_id;
     

        
        
  $params = [
    "api_key" => $data['api_key'],
    "type" => "text",
    "contacts" => $data['recipent'],
    "senderid" => $data['sender_id'],
    "msg" => $data['message'],
  ];
  
  $url = "https://www.880sms.com/smsapi";
  $ch = curl_init();
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_POST, 1);
  curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
  curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
  $response = curl_exec($ch);
  curl_close($ch);
  return $response;
    // return dd($response);
     
     
                // $headers = array(
        //     "Authorization: Bearer ".$data['api_key']."",
        //     "Accept: application/json",
        // );
        // $params = [
        //     "recipient" => $data['recipent'],
        //     "sender_id" => $data['sender_id'],
        //     "type" => 'plain',
        //     "message" => $data['message'],
        // ];
        
        // $params = json_encode($params);
        // $url = "https://sms.elitbuzz.com/api/v3/sms/send";
        // $curl = curl_init($url);
        // curl_setopt($curl, CURLOPT_URL, $url);
        // curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, TRUE);
        // curl_setopt($curl, CURLOPT_POST, TRUE);
        // curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        // curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        // curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
        // $resp = curl_exec($curl);
        // curl_close($curl);
        // // return dd($resp);

    }

    public function balance()
    {
        $elitbuzz = ExternalService::where('name','elitbuzz')->first();

        if(empty($elitbuzz))
        {
            return 0;
        }    
    
        $details = json_decode($elitbuzz->details);
        $api_key = $details->api_key  ?? '';  
       
        $headers = [
            "Authorization: Bearer ".$api_key."",
            'Content-Type: application/json',
            'Accept: application/json',
        ];
        $url = "https://sms.elitbuzz.com/api/v3/balance";
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url); 
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true); 
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers); 
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true); 
        $response = curl_exec($curl);
        curl_close($curl);
        $responseData = json_decode($response, true);
        $responseData = $responseData['data']['remaining_balance'] ?? 0;
        
        $responseData = preg_replace("/[^0-9]/", "", $responseData);
        // $responseData = intval($responseData);
        return $responseData;
    }
}