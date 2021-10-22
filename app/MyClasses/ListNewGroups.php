<?php
namespace App\MyClasses;

use App\Models\NewUsers;

class ListNewGroups {

  public static function getFollowList() {
    if (empty(session('vkid'))) return FALSE;
    $vkid = session('vkid');
    $list = new NewUsers();
    $all_follow_groups = $list->where('vkid', $vkid)->get(['id','vkid','group_id','name','updated_at'])->toArray();
    foreach($all_follow_groups as &$item) {
      $item['updated_at'] = date('d.m.Y H:i', strtotime($item['updated_at']));
      if (file_exists("storage/new-users/{$vkid}_{$item['group_id']}.xlsx"))  $item['file'] = "{$vkid}_{$item['group_id']}";
      else $item['file'] = '';
    }
    return $all_follow_groups;
  }
}
?>
