<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Qiwi\Api\BillPayments;

class QiwiRequestController extends Controller
{
  public function confirm(Request $data) {

    $billPayments = new BillPayments(env('SECRET_KEY'));
    $validSignatureFromNotificationServer = $data->header('x-api-signature-sha256');
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
