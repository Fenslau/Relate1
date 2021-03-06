<?php
namespace App\MyClasses;

use \VK\Client\VKApiClient;
use \XLSXWriter;
use \App\MyClasses\GetProgress;
use \App\MyClasses\num;
use \App\MyClasses\GetAge;
use \App\MyClasses\VKUser;

class GetUsers {

  public $members_count = 0;
  public $filetime = '';
  public function __construct() {
    $this->members_count = 0;
    $this->filetime = '';
  }

  public function groupId($groups = NULL) {
    $access_token = session('token');
    $vk = new VKApiClient();
    $groups_all = array();
    if (empty($groups)) return NULL;
    $groups_ = array_diff(explode("\n", str_replace(array("\r\n", "\n\r"), "\n", $groups)), array(NULL, 0, ''));
    foreach ($groups_  as $groups)
      if (!is_numeric($groups)) {
        $groupid = trim($groups);
          $groupid = str_replace('https://vk.com/', '', $groupid);
          $groupid = str_replace('http://vk.com/', '', $groupid);
          if (strpos($groupid, 'public') === 0) $groupid = str_replace('public', 'club', $groupid);
          if (strpos($groupid, 'event') === 0) $groupid = str_replace('event', 'club', $groupid);
          $groups_all[] = $groupid;
      } else $groups_all[] = $groups;

      try {
                  $group_get = $vk->groups()->getById($access_token, array(
                      'group_ids'		 => implode(',', array_slice($groups_all, 0, 500)),
                      'lang'   		   => 'ru',
                      'fields'       => 'members_count',
                      'v' 			     => '5.95'
                  ));
            } catch (\VK\Exceptions\Api\VKApiParamException $exception) {
                return NULL;
            } catch (\VK\Exceptions\Api\VKApiAuthException $exception) {
                return 'auth vk';
            }

      if (!empty($group_get[0]['id'])) {
        $this->members_count = array_sum(array_column($group_get, 'members_count'));
        return (array_column($group_get, 'id'));
      }
      else return NULL;
  }

  public function fromGroup($groupids, $fields, $mode, $request = NULL) {
    $access_token = session('token');
    $users = array();
    $items_users = array();
    $count = 0;
    $users_all = '';
    $vk = new VKApiClient();
    if (isset($request->rand)) $rand = $request->rand; else $rand = NULL;
  if (!is_array($groupids) AND is_numeric($groupids)) {
    $tmp = $groupids;
    $groupids = array();
    $groupids[] = $tmp;
  }

  if ($mode == 'getusers') $writer = new XLSXWriter();
  if (!empty($fields)) {
                $header = array();
                        $header['id']='integer';
                        $header['????????????']='string';
                  if (strpos($fields, 'domain') !== FALSE)	  $header['"????????????????" ??????????']='string';
                        $header['??????']='string';
                        $header['??????????????']='string';
                        $header['???????????????? ???????????????']='string';
                  if (strpos($fields, 'sex') !== FALSE)		  $header['??????']='string';
                  if (strpos($fields, 'bdate') !== FALSE)	  $header['??????????????']='string';
                  if(!empty($request->bday))  $header['???????? ???????????????? ??????????']='string';
                  if (strpos($fields, 'city') !== FALSE)	 $header['??????????']='string';
                  if (strpos($fields, 'country') !== FALSE)	 $header['????????????']='string';
                  if (strpos($fields, 'site') !== FALSE)	 $header['????????']='string';
                  if (strpos($fields, 'contacts') !== FALSE) {
                      $header['??????????????????']='string';
                      $header['??????????????']='string';
                  }
                  if (strpos($fields, 'online') !== FALSE)	{
                      $header['???????????? ????????????']='string';
                      $header['?????????????????? ????????????']='string';
                  }
                  if (strpos($fields, 'can_post') !== FALSE)	$header['?????????? ?????????????? ???? ???????????']='string';
                  if (strpos($fields, 'can_see_all_posts') !== FALSE)	$header['?????????? ???????????? ?????????? ???? ???????????']='string';
                  if (strpos($fields, 'can_write_private_message') !== FALSE)	$header['?????????? ???????????? ?? ?????']='string';
                  if (strpos($fields, 'last_seen') !== FALSE) {
                      $header['???????? ???????????????? ????????????']='string';
                      $header['???????????????????? ??????????']='string';
                  }
                  if (strpos($fields, 'relation') !== FALSE)	$header['??????????????????']='string';
                  if(!empty($request->half2))	$header['???????????? ????????????????']='string';
                  if (strpos($fields, 'connections') !== FALSE)	{
                        $header['instagram']='string';
                        $header['twitter']='string';
                        $header['skype']='string';
                        $header['facebook']='string';
                        $header['?????? ?? facebook']='string';
                  }
            $writer->writeSheetHeader('Sheet1', $header );
  }

  $user = new VKUser(session('vkid'));
  if ($user->demo === NULL OR strtotime($user->date) < date('U')) {
    //$limit=10;
    $info['demo'] = TRUE;
  } else $info['demo'] = NULL;

  foreach ($groupids as $groupid) {
    $sheet=(intdiv(($count-1), 1000000)+1);
    if ($mode == 'getusers') $writer->writeSheetRow('Sheet'.$sheet, ['???????????????????? ???????????? '.$groupid]);
retry:
    try {
      $list_users = $vk->groups()->getMembers($access_token, array(
          'group_id'		 => $groupid,
          'v' 			     => '5.101'
      ));
    }
    catch (\VK\Exceptions\Api\VKApiAccessException $exception) {
        $items_users[1001] = 'access vk';
        if ($mode == 'getusers') {
          $writer->writeSheetRow('Sheet'.$sheet, ['?????????????????????? ???????????? '.$groupid.' ???? ?????????????? ???????????? ?? ???????????? ??????????????????????. ???????????? ?????????????? ???? ??????????????????']);
          unset($progress);
          continue;
        }
        return $items_users;
    }
    catch (\VK\Exceptions\Api\VKApiAuthException $exception) {
        $items_users[1001] = 'auth vk';
        if ($mode == 'getusers') {
          goto ex;
        }
        return $items_users;
    }
    catch (\VK\Exceptions\Api\VKApiRateLimitException $exception) {
          $items_users[1001] = 'limit vk';
          if ($mode == 'getusers') {
            goto ex;
          }
          return $items_users;
    }
    catch (\VK\Exceptions\Api\VKApiTooManyException $exception) {
      sleep(1);
      goto retry;
    }
    catch (\VK\Exceptions\Api\VKApiPermissionException $exception) {
      $items_users[1001] = 'access vk';
      if ($mode == 'getusers') {
        $writer->writeSheetRow('Sheet'.$sheet, [$groupid.' ??? ?????? ?????????????? ????????????????????. ???????????? ???????????? ???? ???????????????????????? ??????????????????????????????.']);
        unset($progress);
        continue;
      }
      return $items_users;
    }

      if (empty($list_users['count']))
        if ($mode != 'getusers') return FALSE;
        else {
          unset($progress);
          continue;
        }


    if (empty($fields)) $per_time = 25000; else  $per_time = 10000;

    $count_25000 = intdiv ($list_users['count'], $per_time);

    $progress = new GetProgress(session('vkid'), $mode.$rand, '???????? ???????? ?????????????????????? ???????????? '.$groupid, $count_25000+1, 1);

    for ($j=0; $j <= $count_25000; $j++) {

        $code_1 = 'return[';
          for ($k=0; $k<($per_time/1000); $k++) {
          if (!empty($groupid)) $code_1 = $code_1.'API.groups.getMembers({"group_id":'.$groupid.',"fields":"'.$fields.'","v":5.95,"offset":'.($j*$per_time+1000*$k).'}),';
          }
        $code_1 = $code_1.'];';
        if ($code_1 == 'return[];') break;
retrys:
        try {
          $list_users = $vk->getRequest()->post('execute', $access_token, array(
            'code' 			    => $code_1,
            'v' 			      => '5.126'
          ));
        } catch (\VK\Exceptions\Api\VKApiRateLimitException $exception) {
              $items_users[1001] = 'limit vk';
              if ($mode == 'getusers') {
                goto ex;
              }
              return $items_users;
        }
        catch (\VK\Exceptions\Api\VKApiAuthException $exception) {
            $items_users[1001] = 'auth vk';
            if ($mode == 'getusers') {
              goto ex;
            }
            return $items_users;
        }
        catch (\VK\Exceptions\Api\VKApiTooManyException $exception) {
          //sleep(1);
          goto retrys;
        }

            if (!empty($list_users)) foreach ($list_users as $exec) {

              if (!empty($exec['items']) AND is_array($exec['items'])) {

                if ($mode == 'getusers') {
                  foreach ($exec['items'] as $items) {

                    $users=array();
      							$bday='';
      							if (!isset($items['deactivated'])) {
      									if (!empty($fields)) @$users['id']=$items['id']; else $users['id']=$items;
      									if (!empty($fields)) @$users['link']='https://vk.com/id'.$items['id'];
                          else $users['link']='https://vk.com/id'.$items;
      								if (strpos($fields, 'domain'))	@$users['domain']=$items['domain'];

      									@$users['first_name']=$items['first_name'];
      									@$users['last_name']=$items['last_name'];
      									if (@$items['is_closed']==1) $users['is_closed']='??????????????'; else $users['is_closed']='';

      								if (strpos($fields, 'sex') !== FALSE)	if (isset ($items['sex'])) {
      										switch($items['sex']) {
        										case 1: $users['sex']='??????'; break;
        										case 2: $users['sex']='??????'; break;

        										default: $users['sex']=''; break;
      										}
      									} else $users['sex']='';
      								if (strpos($fields, 'bdate') !== FALSE) {
      								if (isset ($items['bdate'])) {
      									if (count(explode(".", $items['bdate'])) == 3)	{
      										$age = explode(".", $items['bdate']);
      										$age = GetAge::age($age[2], $age[1], $age[0]);
      									} else $age = '';
      												if (count(explode(".", $items['bdate'])) == 2) {
      													$bday = (strtotime($items['bdate'].'.'.date('Y'))-date('U'))/(3600 * 24);
      												}
      												elseif (count(explode(".", $items['bdate'])) == 3) {
      													$bday = explode(".", $items['bdate']);
      													$bday = (strtotime($bday[0].'.'.$bday[1].'.'.date('Y'))-date('U'))/(3600 * 24);
      												}

      								} else $age = '';
      								@$users['bdate']=$age;
      								}
      								if(!empty($request->bday) AND is_numeric($bday) AND abs($bday)<15) {
      									$bday=round($bday, 0);
      									if ($bday<0) @$users['bday']=num::declension ((abs($bday)-1), array('????????', '??????', '????????')).' ??????????';
      									if ($bday>=0) @$users['bday']='?????????? '.num::declension (($bday+1), array('????????', '??????', '????????'));
      									if ($bday==-1) @$users['bday']='??????????????';
      								}
      								if (strpos($fields, 'city') !== FALSE)	@$users['city']=$items['city']['title'];
      								if (strpos($fields, 'country') !== FALSE)	@$users['country']=$items['country']['title'];
      								if (strpos($fields, 'site') !== FALSE)	@$users['site']=$items['site'];
      								if (strpos($fields, 'contacts') !== FALSE) {
      									@$users['mobile_phone']=$items['mobile_phone'];
      									@$users['home_phone']=$items['home_phone'];
      								}
      								if (strpos($fields, 'online') !== FALSE) {
      									if (@$items['online']==1) $users['online'] = '????'; else $users['online'] = '';
      									if (isset ($items['online_mobile'])) $users['online_mobile'] = '????'; else $users['online_mobile'] = '';
      								}
      								if (strpos($fields, 'can_post') !== FALSE)	if (@$items['can_post']==1) $users['can_post'] = '????'; else $users['can_post'] = '';
      								if (strpos($fields, 'can_see_all_posts') !== FALSE)	if (@$items['can_see_all_posts']==1) $users['can_see_all_posts'] = '????'; else $users['can_see_all_posts'] = '';
      								if (strpos($fields, 'can_write_private_message') !== FALSE)	if (@$items['can_write_private_message']==1) $users['can_write_private_message'] = '????'; else $users['can_write_private_message'] = '';
      								if (strpos($fields, 'last_seen') !== FALSE)	{
      									if (isset($items['last_seen']['time'])) $users['last_seen']=date('d.m.Y', $items['last_seen']['time']); else $users['last_seen'] = '';

      									if (isset ($items['last_seen']['platform'])) {
      										switch($items['last_seen']['platform']) {
        										case 1: $users['platform']='?????????????????? ????????????'; break;
        										case 2: $users['platform']='iPhone'; break;
        										case 3: $users['platform']='iPad'; break;
        										case 4: $users['platform']='Android'; break;
        										case 5: $users['platform']='Windows Phone'; break;
        										case 6: $users['platform']='???????????????????? ?????? Windows 10'; break;
        										case 7: $users['platform']='???????????? ???????????? ??????????'; break;

        										default: $users['platform']=''; break;
      										}
      									} else $users['platform']='';
      								}
      								if (strpos($fields, 'relation') !== FALSE)	if (isset ($items['relation'])) {
      										switch($items['relation']) {
      										case 1: $users['relation']='???? ??????????/???? ??????????????'; break;
      										case 2: $users['relation']='???????? ????????/???????? ??????????????'; break;
      										case 3: $users['relation']='??????????????????/????????????????????'; break;
      										case 4: $users['relation']='??????????/??????????????'; break;
      										case 5: $users['relation']='?????? ????????????'; break;
      										case 6: $users['relation']='?? ???????????????? ????????????'; break;
      										case 7: $users['relation']='??????????????/????????????????'; break;
      										case 8: $users['relation']='?? ?????????????????????? ??????????'; break;
      										case 0: $users['relation']=''; break;
      										default: $users['relation']=''; break;
      										}
      									} else $users['relation']='';
      								if (!empty($request->half2) AND isset ($items['relation_partner']))	@$users['half2']=
      								"https://vk.com/id".$items['relation_partner']['id'];

      								if (strpos($fields, 'connections') !== FALSE)	{
      									@$users['instagram']=$items['instagram'];
      									@$users['twitter']=$items['twitter'];
      									@$users['skype']=$items['skype'];
      									@$users['facebook']=$items['facebook'];
      									@$users['facebook_name']=$items['facebook_name'];
      								}

                		if 	((strpos($fields, 'site') !== FALSE AND empty($users['site']))
                			OR (strpos($fields, 'contacts') !== FALSE AND (empty($users['mobile_phone']) AND empty($users['home_phone'])))
                			OR (strpos($fields, 'connections') !== FALSE AND (empty($users['instagram']) AND empty($users['twitter']) AND empty($users['skype']) AND empty($users['facebook']) AND empty($users['facebook_name'])))
                			OR (strpos($fields, 'relation') !== FALSE AND empty($users['relation']))
                			OR (!empty($request->half2) AND empty($users['half2']))
                			OR (!empty($request->bday) AND empty($users['bday']))) goto no_write;
      									$count++;
                        if ($count <= 1000) $items_users[$count-1] = $users;
                        $sheet=(intdiv(($count-1), 1000000)+1);
      									$writer->writeSheetRow('Sheet'.$sheet, $users);
                        if ($info['demo'] === TRUE AND $count > 99) goto ex;
                        no_write:
          							}
          						}

                  }

                  if ($mode == 'new-users' OR $mode == 'auditoria') {
                      $users_all .= implode(',', $exec['items']).',';
                  }

                } elseif ($j < $count_25000) {
                  $items_users[1001] = 'limit vk';
                  if ($mode == 'getusers') {
                    goto ex;
                  }
                  return $items_users;
                }
              }
          $progress->step();
      }
      unset($progress);
    }
    ex:
    if ($mode == 'getusers') {
      if (isset($progress)) unset($progress);
      $progress = new GetProgress(session('vkid'), $mode.$rand, '???????????????????????? ???????? Excel', 1, 1);
      $this->filetime = date('d_m_y_H_i_s');
      $writer->writeToFile('storage/getusers/'.session('vkid').'_getusers_'.$this->filetime.'.xlsx');
      ksort($items_users);
      return $items_users;
    }
    if ($mode == 'auditoria' OR $mode == 'new-users') {
      return substr($users_all, 0, -1);
    }
  }

}
?>
