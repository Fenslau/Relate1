<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \Qiwi\Api\BillPayments;

class TarifController extends Controller
{
  public function choose(Request $request) {

    $billPayments = new BillPayments(env('SECRET_KEY'));
    if (isset($request->submit199)) {

      $lifetime = $billPayments->getLifetimeByDay(1);
      $billId = $billPayments->generateId();
      $fields = [
        'amount' => 1.00,
        'currency' => 'RUB',
        'comment' => 'Оплата за доступ на 3 дня. После оплаты, пожалуйста, вернитесь на сайт самостоятельно.',
        'expirationDateTime' => $lifetime,
        'customFields' => ['themeCode'  =>  'Evgenyi-KRvq8Ldyhy'],
        'account' => 123
      ];

      dd($fields);
      $response = $billPayments->createBill($billId, $fields);
      return redirect($response['payUrl']);
    }
  }
}
