<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use \VK\Client\VKApiClient;
use \XLSXWriter;


class MainController extends Controller
{
    public function main(Request $request) {


      return view('home');
    }
}
