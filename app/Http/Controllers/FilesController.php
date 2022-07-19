<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

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

      return view('files', ['files' => $files]);
    }
}
