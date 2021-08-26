<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Oplata;
use App\Models\Download;
use App\Models\Visitors;
use Illuminate\Support\Facades\DB;
use \XLSXWriter;


class VisitorsStatController extends Controller
{
  public function getStat(Request $request) {
    $writer = new XLSXWriter();
    $header = array(
      '№'=>'integer',
      'Дата'=>'string',
      'Профиль'=>'string',
      'Имя'=>'string',
      'Фамилия'=>'string',
      'Город'=>'string',
      '№ визита'=>'string',
      'Скачиваний'=>'string',
      'Анонимных скачиваний всего'=>'string',
      'Платный'=>'string',
    );
    $writer->writeSheetHeader('Sheet1', $header );
    if (is_numeric($request->count)) $limit = $request->count; else $limit = 100;
    $visit = New Visitors();
    $downloads = New Download();
    $oplata = New Oplata();
    $visitors = $visit->orderBy('id', 'desc')->take($limit)->get();

    foreach ($visitors as $visitor) {

      $visitor->visit_number = $visit->where([
        ['vkid', '=', $visitor->vkid],
        ['created_at', '<=', $visitor->created_at],
      ])->count();

      $visitor->downloads = $downloads->where([
        ['vkid', '=', $visitor->vkid],
        ['created_at', '<=', $visitor->created_at],
      ])->count();

      $visitor->downloads_anon = $downloads->where([
        ['vkid', '=', 'anon'],
        ['created_at', '<=', $visitor->created_at],
      ])->count();

      $visitor->oplata_date = $oplata->where([
        ['vkid', '=', $visitor->vkid],
        ['date', '>=', $visitor->created_at],
      ])->whereNotNull('demo')->first()->date;
      if (strtotime($visitor->oplata_date)>date('U')) $visitor->oplata_class='class=text-success';
      else $visitor->oplata_class='class=text-danger';

      $infox = array();
      $infox['id']=$visitor->id;
  		$infox['date']=$visitor->created_at->day.'.'.$visitor->created_at->month.'.'.$visitor->created_at->year.' '.$visitor->created_at->hour.':'.$visitor->created_at->minute;
  		$infox['vkid']=$visitor->vkid;
  		$infox['firstname']=$visitor->firstname;
  		$infox['lastname']=$visitor->lastname;
  		$infox['city']=$visitor->city;
  		$infox['visit_number']=$visitor->visit_number;
  		$infox['downloads']=$visitor->downloads;
  		$infox['downloads_anon']=$visitor->downloads_anon;
      $infox['oplata_date']=$visitor->oplata_date;
      $writer->writeSheetRow('Sheet1', $infox );

    }
    $writer->writeToFile("temp/stat.xlsx");
    return view('stat', ['visitors' => $visitors]);
  }
}
