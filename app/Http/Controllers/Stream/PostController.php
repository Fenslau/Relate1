<?php

namespace App\Http\Controllers\Stream;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\MyClasses\MyRules;

class PostController extends Controller
{
    public function main($project_name, Request $request) {

      $items = $info = array();

      $info['project_name'] = $project_name;
      return view('streaming.posts', ['cut' => MyRules::getCut($project_name), 'projects' => MyRules::getProjects(), 'rules' => MyRules::getRules($project_name), 'old_rules' => MyRules::getOldRules($project_name), 'links' => MyRules::getLinks($project_name), 'info' => $info, 'items' => $items]);
    }
}
