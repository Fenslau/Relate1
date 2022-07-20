<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \App\MyClasses\VKUser;

class FilesController extends Controller
{
    public function main() {
      $path = public_path('storage');
      $files = \File::allFiles($path);
      $i = -1;
      foreach ($files as &$file) {
        $i++;
        $file = str_replace($path, '', $file);
        if (strpos($file, session('vkid')) !== FALSE) continue;
        else unset($files[$i]);
      }
      $user = new VKUser(session('vkid'));
      if ($user->demo === NULL OR strtotime($user->date) < date('U')) {
        $info['demo'] = TRUE;
      } else $info['demo'] = FALSE;
      return view('files', ['files' => $files, 'info' => $info]);
    }
}
