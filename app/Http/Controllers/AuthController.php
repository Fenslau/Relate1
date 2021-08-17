<?php

namespace App\Http\Controllers;

//use Illuminate\Http\Request;
use \VK\Client\VKApiClient;
use \VK\OAuth\VKOAuth;
use \VK\OAuth\VKOAuthDisplay;
use \VK\OAuth\Scopes\VKOAuthUserScope;
use \VK\OAuth\VKOAuthResponseType;
use Request;

class AuthController extends Controller
{
  public function authVK() {

    $oauth = new VKOAuth();
    $client_id = env('CLIENT_ID');
    $redirect_uri = 'https://lara.sarby.ru/vk-auth-code';
    $display = VKOAuthDisplay::PAGE;
    $scope = array(VKOAuthUserScope::STATS, VKOAuthUserScope::VIDEO);
    $state = Request::post('url');
    $revoke_auth = false;

    $browser_url = $oauth->getAuthorizeUrl(VKOAuthResponseType::CODE, $client_id, $redirect_uri, $display, $scope, $state);
    return redirect($browser_url);
  }

  public function authVKcode() {

    $oauth = new VKOAuth();
    $client_id = env('CLIENT_ID');
    $client_secret = env('CLIENT_SECRET');
    $redirect_uri = 'https://lara.sarby.ru/vk-auth-code';
    $code = Request::input('code');

    $response = $oauth->getAccessToken($client_id, $client_secret, $redirect_uri, $code);
    $access_token = $response['access_token'];

    session(['token' => $access_token]);

    dd($response);
  }
}
