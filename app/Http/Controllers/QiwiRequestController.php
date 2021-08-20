<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Qiwi\Api\BillPayments;

class QiwiRequestController extends Controller
{
  public function confirm(Request $data) {

    $billPayments = new BillPayments(env('SECRET_KEY'));
    if (!function_exists('getallheaders')){
      function getallheaders(){
        $headers = [];
        foreach ($_SERVER as $name => $value){
          if (substr($name, 0, 5) == 'HTTP_') {
            $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
          }
        }
        return $headers;
      }
    }
    
    dump ($headers);
    dump ($data->headers);

    header('Content-Type: application/json');
    $head = array_change_key_case(getallheaders(), CASE_LOWER);
    $validSignatureFromNotificationServer = $head[mb_strtolower('x-api-signature-SHA256')];
    $merchantSecret = env('SECRET_KEY');
    $notificationData = [
      'bill' => [
        'siteId' => $data->bill->siteId,
        'billId' => $data->bill->billId,
        'amount' => $data->bill->amount->value,
        'currency' => $data->bill->amount->currency,
        'status' => ['value' => $data->bill->status->value]
      ],
      'version' => $data->version
    ];


    if ($billPayments->checkNotificationSignature($validSignatureFromNotificationServer, $notificationData, $merchantSecret) === TRUE) {
  		$vkid = $data->bill->customer->account;
  		  file_put_contents('oplata.txt', $data);
  	  echo '{ "error":"0" }';
  	}
  }
}
