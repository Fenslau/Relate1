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
use App\Models\Visitors;
use App\Models\Top;
use App\Models\Stream\OldPosts;
use \App\MyClasses\VKUser;

class AuthController extends Controller
{
  public function authVK() {

    $oauth = new VKOAuth();
    $client_id = env('CLIENT_ID');
    $redirect_uri = env('APP_URL').'/vk-auth-code';
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
    $redirect_uri = env('APP_URL').'/vk-auth-code';
    $code = Request::input('code');
    if (empty($code)) return redirect()->route(Request::input('state'));
    try {
      $response = $oauth->getAccessToken($client_id, $client_secret, $redirect_uri, $code);
      if (!empty($response['access_token'])) {
        $vk = new VKApiClient();
          $user_profile = $vk->users()->get($response['access_token'], array(
            'fields' => 'photo_50, city'
          ));

        $visitor = New Visitors();
        $visitor->vkid=$response['user_id'];
        $visitor->firstname = $user_profile['0']['first_name'];
        $visitor->lastname = $user_profile['0']['last_name'];
        if(isset($user_profile['0']['city']['title'])) $visitor->city = $user_profile['0']['city']['title'];
        if(isset($user_profile['0']['photo_50'])) $visitor->photo = $user_profile['0']['photo_50'];
        $visitor->save();

        if (strlen($response['access_token']) <= 255) {
          $top = New Top;
            if ($top1000 = $top->find(1)) {
        			$top1000->token = $response['access_token'];
        			$top1000->save();
        		} else {
        			$top->token = $response['access_token'];
        			$top->save();
        		}
          OldPosts::where('vkid', $response['user_id'])->update(['token' => $response['access_token']]);
        }
      }
    } catch (\VK\Exceptions\Api\VKApiAuthException $exception) {
        Session::flush();
        return redirect()->route(Request::input('state'))->with('warning', '??????-???? ?? ?????????????????????? ?????????? ???? ??????, ???????????????????? ??????????.');
    } catch (\VK\Exceptions\VKClientException $exception) {
        Session::flush();
        return redirect()->route(Request::input('state'))->with('warning', '??????-???? ?? ?????????????????????? ?????????? ???? ??????, ???????????????????? ??????????.');
    }
    session(['token' => $response['access_token'], 'vkid' => $response['user_id']]);
    return redirect()->route(Request::input('state'));
  }

  public static function getUser() {
    $token=session('token');
    if (!empty($token)) {
      $vk = new VKApiClient();
      $user = new VKUser(session('vkid'));
retry:
      try {
        $user_profile = $vk->users()->get($token, array(
          'fields' => 'photo_50, city'
        ));
        if (!empty($user->demo)) {
          $user_profile[0]['paid_until'] = $user->date;
          session(['user_profile' => $user_profile[0]]);
        }
      } catch (\VK\Exceptions\Api\VKApiAuthException $exception) {
          Session::flush();
          return back()->with('danger', '???????? ???????????? ????????????????. ???????????????????????? ????????????');
      }
      catch (\VK\Exceptions\Api\VKApiRateLimitException $exception) {
          sleep(1);
          goto retry;
      }
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
