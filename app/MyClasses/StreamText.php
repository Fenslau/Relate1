<?php

namespace App\MyClasses;

use \App\MyClasses\Cartesian;
use App\Models\Stream\Projects;
use Illuminate\Support\Facades\DB;

class StreamText {

  public static function text($text, $usertag) {
      $check_trash = 0;
      $user_links = '';
      $array_of_keywords=array();
      $vk_rule = Projects::where(DB::raw('concat(vkid, "", rule)'), $usertag)->first();
      if (empty($vk_rule)) {
        echo 'Пришло событие, правила для которого нет в БД'."\n";
        return FALSE;
      }
      $vk_rule->toArray();
      if (!empty($vk_rule['mode1'])) $array_of_keywords = array_diff(explode("\n", str_replace('?', '', mb_strtolower($vk_rule['mode1']))), array('', NULL, 0));
      if (!empty($vk_rule['mode2'])) {
        $array_of_keywords = array_merge($array_of_keywords, array_diff(explode("\n", str_replace('?', '', mb_strtolower($vk_rule['mode2']))), array('', NULL, 0)));
      }
      if (!empty($vk_rule['mode3'])) $array_of_keywords = array_diff(explode("\n", str_replace('?', '', mb_strtolower($vk_rule['mode3']))), array('', NULL, 0));
      $new_array_of_keywords_ = array();
      $count_of_keywords = count($array_of_keywords);
      $tmp_arr=$array_of_keywords;

      foreach ($array_of_keywords as $array_of_keyword) {
      $new_array_of_keywords = explode('|', str_replace('?', '', mb_strtolower($array_of_keyword)));
      foreach ($new_array_of_keywords as $new_array_of_keyword) $new_array_of_keywords_[] = $new_array_of_keyword;
      }
      $array_of_keywords = $new_array_of_keywords_;

      $filename = rand();
      file_put_contents ('public/temp/stream'.$filename.'.txt', $text, LOCK_EX);
      shell_exec('cd public/temp/ && '.env('MYSTEM').' stream'.$filename.'.txt streamoutput'.$filename.'.txt -nc');
      $value=explode("\n", file_get_contents('public/temp/streamoutput'.$filename.'.txt'));
      if (file_exists('public/temp/stream'.$filename.'.txt')) unlink ('public/temp/stream'.$filename.'.txt');
      if (file_exists('public/temp/streamoutput'.$filename.'.txt')) unlink ('public/temp/streamoutput'.$filename.'.txt');

      $text='';
      $search = ['\r', '\n', '\xAB', '\xBB', '\xA0', '\xB7'];
      $replace = ['', '', '«', '»', ' ', '-'];
      $word_number=0;
      $close=$position=array();

      foreach ($value as $word) {
          $arr=preg_split('/\{|\}(, *)?/', $word, -1);
          if (isset($arr[0])) {
            $arr[0] = str_replace($search, $replace, $arr[0]);
            if (strpos($arr[0], '\_') !== FALSE) $arr[0] = str_replace('\_', '_', $arr[0]);
              else $arr[0] = str_replace('_', ' ', $arr[0]);
            $arr[0] = json_decode('"'.$arr[0].'"');

            foreach ($array_of_keywords as $keyword_)
            foreach (explode('|', str_replace('?', '', mb_strtolower($keyword_))) as $keyword) {
              if (!empty($arr[1]) AND !empty($keyword) AND in_array($keyword, explode('|', str_replace('?', '', mb_strtolower($arr[1]))))
                AND (strpos($arr[0], '<mark>') === FALSE) ) {
                $arr[0]='<mark>'.$arr[0].'</mark>';
                $position[$keyword][] = ($word_number);
              }
            }
            $text .= $arr[0];
          }

        if (isset($arr[1])) {
          $word_number++;
          $lemma = str_replace('?', '', $arr[1]);
          if ($lemma == 'br' OR
          $lemma == 'href' OR
          $lemma == '⠀' OR
          $lemma == 'blank' OR
          $lemma == 'www' OR
          $lemma == 'target' OR
          $lemma == 'rel' OR
          $lemma == 'nofollow' OR
          $lemma == 'quot') $word_number -= 1;
          if ($lemma == 'http' OR $lemma == 'https') $word_number -= 2;
        } else {
          $matches=0;
          preg_match_all('/\d+\D*\d*\s/m', html_entity_decode($arr[0]), $matches);
          if (!empty($matches[0])) {
            $word_number += 1;

          }
        }
      }
      $cartesian = new Cartesian();
      if (!empty($vk_rule['mode1']) AND empty($vk_rule['mode2'])) $close = $cartesian->PosNClose($position, $vk_rule['words1']);
      if (!empty($vk_rule['mode1']) AND !empty($vk_rule['mode2'])) {
      $mode2_all = $close_flag = array();
        $array_of_keywords_1 = array_diff(explode("\n", str_replace('?', '', mb_strtolower($vk_rule['mode1']))), array('', NULL, 0));
        $new_array_of_keywords_ = array();
        foreach ($array_of_keywords_1 as $array_of_keyword) {
        $new_array_of_keywords = explode('|', str_replace('?', '', mb_strtolower($array_of_keyword)));
        foreach ($new_array_of_keywords as $new_array_of_keyword) $new_array_of_keywords_[] = $new_array_of_keyword;
        }
        $array_of_keywords_1 = $new_array_of_keywords_;
        $position_1 = $position_2 = array();
        foreach ($position as $key=>$word)
          if (in_array($key, $new_array_of_keywords_)) $position_1[$key] = $word;
          else $position_2[$key] = $word;

        $close['word1'] = $cartesian->PosNClose($position_1, $vk_rule['words1']);
        $close['word2'] = $cartesian->Pos2Close($position_2, $vk_rule['words2']);

        if (!empty($close['word1']) AND !empty($close['word2'])) {
          foreach ($close['word1'] as $group_of_mode2) {
            foreach ($close['word2'] as $helpwords_of_mode2) {
              $mode2_all = array();
              $mode2_all[]=$group_of_mode2;
              $mode2_all[]=$helpwords_of_mode2;
              $close_flag[] = $cartesian->Pos2Close($mode2_all, $vk_rule['words2']);
            }
          }
          if (!empty($close_flag)) {
            $close = array();
            foreach ($close_flag as $cl_flag)
              if (is_array($cl_flag)) $close = array_merge($close, $cl_flag);
            if (is_array($close)) $close = array_unique($close, SORT_REGULAR);
            if (empty($close)) $close = FALSE;
          }
          else $close = FALSE;
        } else $close = FALSE;
        if ($close !== FALSE) {
          $pairs = '';

          preg_match_all('/[^\+ ]\S+\s?\+\s?\S+[^\+ ]/', $vk_rule['mode2_edit'], $pairs, PREG_SET_ORDER, 0);

          if (!empty($pairs)) {
            foreach ($pairs as $pair) {
              $position_pair=array();
              $array_of_keywords_1 = array_diff(explode("\n", str_replace('?', '', mb_strtolower(shell_exec('cd public/temp/ && echo "'.addslashes($pair[0]).'" | '.env('MYSTEM').' -ln')))), array('', NULL, 0));
              $new_array_of_keywords_ = array();
              foreach ($array_of_keywords_1 as $array_of_keyword) {
                $new_array_of_keywords = explode('|', str_replace('?', '', mb_strtolower($array_of_keyword)));
                foreach ($new_array_of_keywords as $new_array_of_keyword) $new_array_of_keywords_[] = $new_array_of_keyword;
              }
              $array_of_keywords_1 = $new_array_of_keywords_;
              foreach ($position as $key=>$word)
                if (in_array($key, $new_array_of_keywords_)) $position_pair[$key] = $word;

              $pair_close = $cartesian->PosNClose($position_pair, 1);
              if ($pair_close === FALSE) $close=FALSE;
            }
          }
        }
      }
      if (!empty($vk_rule['mode3'])) $close = $cartesian->Pos2Close($position, $vk_rule['words3']);

      $diff = $tmp_array = $pos_array=array();
      foreach ($position as $pkey=>$pvalue) {
        $pos_array[]=$pkey;
      }
      foreach ($tmp_arr as $pkey=>$pvalue) {
        $difference = FALSE;
        foreach (explode('|', str_replace('?', '', mb_strtolower($pvalue))) as $pvalue_word) {
          if (in_array ($pvalue_word, $pos_array)) $difference = TRUE;
        }
        if ($difference === FALSE) $diff[] = $pvalue;
      }

      if ($close === FALSE) $check_trash=1;
      if (!empty($diff)) {
        $user_links = 'Доп.посты';
        $check_trash=0;
    }
    $data['text'] = $text;
    $data['trash'] = $check_trash;
    $data['user_links'] = $user_links;
    return $data;
  }
}
?>
