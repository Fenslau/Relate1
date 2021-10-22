<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Download;

class DownloadController extends Controller
{
    public function download($filename) {

        $filename .= '.xlsx';
              $file_extension = strtolower(substr(strrchr($filename,"."),1));
			        $filename=str_replace('\\', '/', $filename);
              if ( !file_exists( $filename ) ) return back()->with('warning', 'ОШИБКА: данного файла не существует.');

              switch( $file_extension ) {
                          case "pdf": $ctype="application/pdf"; break;
                          case "exe": $ctype="application/octet-stream"; break;
                          case "zip": $ctype="application/zip"; break;
                          case "doc": $ctype="application/msword"; break;
                          case "xlsx": $ctype="application/vnd.ms-excel"; break;
                          case "ppt": $ctype="application/vnd.ms-powerpoint"; break;
                          case "mp3": $ctype="audio/mp3"; break;
                          case "gif": $ctype="image/gif"; break;
                          case "png": $ctype="image/png"; break;
                          case "jpeg":
                          case "jpg": $ctype="image/jpg"; break;
                          default: $ctype="application/force-download";
                }
          $dl = new Download();
          if (!empty(session('vkid'))) $dl->vkid = session('vkid'); else $dl->vkid = 'anon';
          $dl->download = 1;
          $dl->save();


          header("Pragma: public");
          header("Expires: 0");
          header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
          header("Cache-Control: private",false); // нужен для Explorer
          header("Content-Type: $ctype");
          header("Content-Disposition: attachment; filename=\"".basename($filename)."\";" );
          header("Content-Transfer-Encoding: binary");
          header("Content-Length: ".filesize($filename));
          readfile("$filename");
          }
}
