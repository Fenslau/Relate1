<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Qiwi\Api\BillPayments;

class TarifController extends Controller
{
  public function choose(Request $request) {
      $billPayments = new BillPayments(env('SECRET_KEY'));
      $lifetime = $billPayments->getLifetimeByDay(1);
      $billId = $billPayments->generateId();
      $fields = [
        'amount' => $request->amount,
        'currency' => 'RUB',
        'comment' => $request->comment,
        'expirationDateTime' => $lifetime,
        'customFields' => ['themeCode'  =>  'Evgenyi-KRvq8Ldyhy'],
        'account' => $request->vkid
      ];
      $response = $billPayments->createBill($billId, $fields);
      return redirect($response['payUrl']);
  }
}
