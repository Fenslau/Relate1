<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Progress;

class ProgressController extends Controller
{
    public function getProgress(Request $request) {

      if(isset($request->vkid) AND isset($request->process)) {

        $progress = new Progress();
        $response = $progress->where([
          ['vkid', '=', $request->vkid],
          ['process', '=', $request->process],
        ])->first();
        if ($response) return response()->json(['width' => $response->width, 'info' => $response->info]);
        else return response()->json(['width' => 0, 'info' => '']);
      }
    }
}
