<?php
namespace App\MyClasses;

use App\Models\Stream\Projects;
use App\Models\Stream\Links;

class MyRules {

  public static function getProjects($vkid = NULL) {
    if ($vkid === NULL) $vkid = session('vkid');
    $projects = new Projects();
    $my_projects = $projects->where('vkid', $vkid)->distinct()->pluck('project_name')->toArray();
    return $my_projects;
  }

  public static function getRules($project_name, $vkid = NULL) {
    if ($vkid === NULL) $vkid = session('vkid');
    $projects = new Projects();
    $my_rules = $projects->where('vkid', $vkid)->where('project_name', $project_name)->whereNull('old')->whereNotNull('rule')->get()->toArray();
    return $my_rules;
  }

  public static function getOldRules($project_name, $vkid = NULL) {
    if ($vkid === NULL) $vkid = session('vkid');
    $projects = new Projects();
    $my_rules = $projects->where('vkid', $vkid)->where('project_name', $project_name)->whereNotNull('old')->pluck('rule')->toArray();
    return $my_rules;
  }

  public static function getLinks($project_name, $vkid = NULL) {
    if ($vkid === NULL) $vkid = session('vkid');
    $links = new Links();
    $my_links = $links->where('vkid', $vkid)->where('project_name', $project_name)->pluck('link_name')->toArray();
    return $my_links;
  }

  public static function getCut($project_name, $vkid = NULL) {
    if ($vkid === NULL) $vkid = session('vkid');
    $projects = new Projects();
    $my_cut = $projects->where('vkid', $vkid)->where('project_name', $project_name)->pluck('cut')->first();
    return $my_cut;
  }
}
?>
