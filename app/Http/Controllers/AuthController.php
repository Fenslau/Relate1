<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Request;
use \VK\Client\VKApiClient;
use \VK\OAuth\VKOAuth;
use \VK\OAuth\VKOAuthDisplay;
use \VK\OAuth\Scopes\VKOAuthUserScope;
use \VK\OAuth\VKOAuthResponseType;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Session;

class AuthController extends Controller
{
  public function authVK() {

    $oauth = new VKOAuth();
    $client_id = env('CLIENT_ID');
    $redirect_uri = 'http://lara.sarby.ru/vk-auth-code';
    $display = VKOAuthDisplay::PAGE;
    $scope = array(VKOAuthUserScope::STATS, VKOAuthUserScope::VIDEO);
    if (null !== Request::input('url')) $state = Request::input('url'); else $state = 'home';
    $revoke_auth = false;
    $browser_url = $oauth->getAuthorizeUrl(VKOAuthResponseType::CODE, $client_id, $redirect_uri, $display, $scope, $state);
    return redirect($browser_url);
  }

  public function authVKcode() {

    $oauth = new VKOAuth();
    $client_id = env('CLIENT_ID');
    $client_secret = env('CLIENT_SECRET');
    $redirect_uri = 'http://lara.sarby.ru/vk-auth-code';
    $code = Request::input('code');

    $response = $oauth->getAccessToken($client_id, $client_secret, $redirect_uri, $code);
    //проверить, нет ли какой, сука, ошибки в авторизации
    session(['token' => $response['access_token'], 'vkid' => $response['user_id']]);
    /*
    |--------------------------------------------------------------------------
    | где то здесь нужна запись в базу залогиненного чела
    |--------------------------------------------------------------------------
    |
    */
    return redirect()->route(Request::input('state'));
  }

  public static function getUser($token) {

    if (!empty($token)) {
      $vk = new VKApiClient();
      $user_profile = $vk->users()->get($token, array(
        'fields' => 'photo_50, city'
      ));
      return (object)($user_profile[0]);
    }
    else return ($user_profile=0);

  }

  public function authVKdestroy () {
    Session::flush();
    if (null !== Request::input('url')) $state = Request::input('url'); else $state = 'home';
    return back();
  }
}
