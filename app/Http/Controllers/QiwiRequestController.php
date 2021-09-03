<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Qiwi\Api\BillPayments;
use App\Models\Oplata;

class QiwiRequestController extends Controller
{
  public function confirm(Request $request) {

	file_put_contents ('temp/oplata.txt', $request);
	$data = $request->json()->all();

    $billPayments = new BillPayments(env('SECRET_KEY'));
	header('Content-Type: application/json');
    $validSignatureFromNotificationServer = $request->header('x-api-signature-sha256');
    $merchantSecret = env('SECRET_KEY');
$notificationData = [
  'bill' => [
    'siteId' => $data['bill']['siteId'],
    'billId' => $data['bill']['billId'],
    'amount' => ['value' => $data['bill']['amount']['value'],
	'currency' => $data['bill']['amount']['currency']],
    'status' => ['value' => $data['bill']['status']['value']]
  ],
  'version' => $data['version']
];


    if ($billPayments->checkNotificationSignature($validSignatureFromNotificationServer, $notificationData, $merchantSecret) === TRUE) {
  		$vkid = $data['bill']['customer']['account'];
      $oplata = new Oplata();
      $result = $oplata->where('vkid', $vkid)->first()->toArray();
      $project_limit = $rules_limit = $old_post_limit = 0;
  		$demo = 1;
      switch($data['bill']['amount']['value']) {
    	case '1.00': if (!empty($result['date']) AND $result['date'] > date ('U')) $date = ($result['date']+1296);
    					else $date = (date ('U')+1296); break;
    	case '194.00': if (!empty($result['date']) AND $result['date'] > date ('U')) $date = ($result['date']+259200);
    					else $date = (date ('U')+259200); break;
    	case '538.00': if (!empty($result['date']) AND $result['date'] > date ('U')) $date = ($result['date']+2764800);
    					else $date = (date ('U')+2764800); break;
    	case '1273.00': if (!empty($result['date']) AND $result['date'] > date ('U')) $date = ($result['date']+8380800);
    					else $date = (date ('U')+8380800); break;

    	case '342.00': if ((strpos($result['demo'], 'streaming') !== FALSE) AND !empty($result['date']) AND $result['date'] > date ('U')) $date = ($result['date']+60*60*24*7);
    					else $date = (date ('U')+60*60*24*7);
    					$project_limit = 1;
    					$rules_limit = 2;
    					$old_post_limit = 100;
    					$demo = 'streaming1';
    					break;
    	case '539.00': if ((strpos($result['demo'], 'streaming') !== FALSE) AND !empty($result['date']) AND $result['date'] > date ('U')) $date = ($result['date']+60*60*24*7);
    					else $date = (date ('U')+60*60*24*7);
    					$project_limit = 2;
    					$rules_limit = 5;
    					$old_post_limit = 500;
    					$demo = 'streaming2';
    					break;
    	case '979.00': if ((strpos($result['demo'], 'streaming') !== FALSE) AND !empty($result['date']) AND $result['date'] > date ('U')) $date = ($result['date']+60*60*24*30);
    					else $date = (date ('U')+60*60*24*30);
    					$project_limit = 2;
    					$rules_limit = 5;
    					$old_post_limit = 2000;
    					$demo = 'streaming3';
    					break;



    	default: if (!empty($result['date']) AND $result['date'] > date ('U')) $date = ($result['date']+86400);
    					else $date = (date ('U')+86400); break;
    	}

      $result = new Oplata();

        $result->vkid = $vkid;
        $result->date = $date;
        $result->billId = $data['bill']['billId'];
        $result->project_limit = $project_limit;
        $result->rules_limit = $rules_limit;
        $result->old_post_limit = $old_post_limit;

    		if ($data['bill']['status']['value'] == 'PAID') {
          $result->demo = $demo;
    		} else {
          $result->demo = NULL;
    		}
        $result->save();
		echo '{ "error":"0" }';

  	}
  }
}
