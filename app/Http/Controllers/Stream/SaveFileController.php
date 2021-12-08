<?php

namespace App\Http\Controllers\Stream;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Stream\FileXLS;

class SaveFileController extends Controller
{
    public function main($project_name, Request $request) {
      $file = new FileXLS();

      $file->vkid = session('vkid');
      $file->project_name = $project_name;
      $file->get_params = $request->query_string;

      $file->save();

      return back()->with('success', 'Вы заказали файл Excel. <br />Он будет подготовлен для вас в соответствии с той выборкой, которую вы видели на экране, при нажатии кнопки
      "Скачать файл". <br />В нём будут все записи со всех страниц этой выборки. <br />
      В зависимости от размера, подготовка файла может занять некоторое время. <br />Как только он будет готов, ссылка на него появится на
      <a href="'.route('stream').'">главной</a> странице. <br />
      Если вам нужен другой файл, сформируйте выборку записей по вашему проекту, используя фильтры, или как-то иначе, а потом нажмите кнопку
      "Скачать файл Excel"');
    }
}
