<?php

namespace App\Http\Controllers\Stream;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \App\MyClasses\VKUser;
use \App\MyClasses\num;
use App\Models\Stream\Projects;
use App\Models\Stream\StreamKey;

class RuleController extends Controller
{
    public function add(Request $request) {
      $stream = new Streamkey();
      $stream = $stream->find(1);
      if (empty($request->project_name) OR $request->project_name == '#') return back()->with('error', 'Сначала выберите проект из списка или создайте новый');
      if (empty($request->rule_tag)) return back()->with('error', 'Придумайте название для правила');

      $project = $request->project_name;
      $rule_tag	= $request->rule_tag;
      if (!empty($request->brand)) $brand = $request->brand; else $brand = NULL;
      if (!empty($request->key_words)) $key_words = $request->key_words; else $key_words = NULL;
      if (!empty($request->help_words) AND $request->add_help_words == 'on') $help_words = $request->help_words; else $help_words = NULL;
      if (!empty($request->minus_words)) $minus_words = $request->minus_words; else $minus_words = NULL;
      $mode = $request->mode;
      $vkid	= session('vkid');

      switch ($mode) {
        case 1: if (!empty($help_words)) $rule_value = $brand .' '. $help_words; else $rule_value = $brand; break;
        case 3: $rule_value = $key_words; break;
        default: return back()->with('error', 'Выберите метод сбора постов/упоминаний'); break;
      }

      $rule_value1 = explode(' ', $rule_value);
      $rule_value1 = array_unique($rule_value1);
      $rule_value = implode(' ', $rule_value1);

      if (!empty($minus_words)) $rule_value .= ' '.$minus_words;
      $rule_value = str_replace('+', ' ', $rule_value);

      if (session('demo')) return back()->with('error', 'В демо-режиме нельзя создавать правила');
      $user = new VKUser($vkid);
      if (empty($user->rules_limit)) return back()->with('error', 'Ваш тариф не допускает создания правил');
      $projects = new Projects();
      $rules_count = $projects->where('vkid', $vkid)->where('project_name', $project)->whereNull('old')->whereNotNull('rule')->count();
      if ($user->rules_limit > $rules_count) {
      	if ($user->date > date('U')) {

      				$rule_json = array('rule' => array ('value' => $rule_value, 'tag' => $vkid.$rule_tag));
      				$rule_json = json_encode ($rule_json, JSON_UNESCAPED_UNICODE);

      				$ch = curl_init();

      				curl_setopt($ch, CURLOPT_URL, 'https://'.$stream->endpoint.'/rules?key='.$stream->streamkey);
      				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      				curl_setopt($ch, CURLOPT_POST, true);
              curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
              curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      				curl_setopt($ch, CURLOPT_POSTFIELDS, $rule_json);
      				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));

      				$out1 = json_decode(curl_exec($ch), true);
      				curl_close($ch);

      				if (!empty($out1['code']) AND $out1['code'] == 200) {
      					$cut = $projects->where('vkid', $vkid)->where('project_name', $project)->distinct()->pluck('cut');
      					if ($mode == 1) {
      						$key_words = NULL;
      						$brand_for_edit = $brand;
      						$help_words_for_edit = $help_words;

      						if (!empty($brand)) {
                    $brand = preg_replace('/&#\d+;(*SKIP)(*F)|\d+/', '', $brand);
                    file_put_contents('temp/stream_rule.txt', $brand);
                    $brand=str_replace('?', '', mb_strtolower(shell_exec('cd temp/ && '.env('MYSTEM').' stream_rule.txt -ln')));
                  }
      						if (!empty($help_words) AND $request->add_help_words == 'on') {
                    $help_words = preg_replace('/&#\d+;(*SKIP)(*F)|\d+/', '', $help_words);
                    file_put_contents('temp/stream_rule.txt', $help_words);
                    $help_words=str_replace('?', '', mb_strtolower(shell_exec('cd temp/ && '.env('MYSTEM').' stream_rule.txt -ln')));
                  }
      							else $help_words_for_edit = $help_words = NULL;

                  $data['cut'] = $cut[0];
                  $data['old'] = NULL;
                  $data['mode1'] = $brand;
                  $data['mode1_edit'] = $brand_for_edit;
                  $data['mode2'] = $help_words;
                  $data['mode2_edit'] = $help_words_for_edit;
                  $data['mode3'] = NULL;
                  $data['mode3_edit'] = NULL;
                  $data['minus_words'] = $minus_words;

      						$projects->updateOrCreate(['vkid' => $vkid, 'project_name' => $project, 'rule' => $rule_tag], $data);
      					}
      					if ($mode == 3) {
      						$brand = $help_words = NULL;
      						$key_words_for_edit = $key_words;
      						if (!empty($key_words)) {
                    $key_words = preg_replace('/&#\d+;(*SKIP)(*F)|\d+/', '', $key_words);
                    file_put_contents('temp/stream_rule.txt', $key_words);
                    $key_words=str_replace('?', '', mb_strtolower(shell_exec('cd temp/ && '.env('MYSTEM').' stream_rule.txt -ln')));
                  }
                  $data['cut'] = $cut[0];
                  $data['old'] = NULL;
                  $data['mode1'] = NULL;
                  $data['mode1_edit'] = NULL;
                  $data['mode2'] = NULL;
                  $data['mode2_edit'] = NULL;
                  $data['mode3'] = $key_words;
                  $data['mode3_edit'] = $key_words_for_edit;
                  $data['minus_words'] = $minus_words;

      						$projects->updateOrCreate(['vkid' => $vkid, 'project_name' => $project, 'rule' => $rule_tag], $data);
      					}
                return back()->with('success', 'Правило <b>'.$rule_tag.'</b> успешно добавлено');
      				}
      				elseif (!empty($out1['error']['error_code'])) {
      					switch($out1['error']['error_code']) {
      					case 2000: return back()->with('error', 'Не удалось распарсить JSON в теле запроса'); break;
      					case 2001: return back()->with('error', 'Правило с такой меткой уже существует'); break;
      					case 2003: return back()->with('error', 'Не удалось распарсить содержимое правила'); break;
      					case 2004: return back()->with('error', 'Слишком много фильтров в одном правиле'); break;
      					case 2005: return back()->with('error', 'Непарные кавычки'); break;
      					case 2006: return back()->with('error', 'Слишком много правил в этом потоке'); break;
      					case 2008: return back()->with('error', 'Должно быть хотя бы одно ключевое слово без минуса'); break;

      					default: return back()->with('error', 'произошла какая-то ошибка '); break;
      				}}

      	} else return back()->with('error', 'Срок действия тарифа истёк');
      } else return back()->with('error', 'Вашим тарифом предусмотрено не более '.num::declension ($limit['rules_limit'], array('правила в проекте', 'правил в проекте', 'правил в проекте')));

      return back()->with('error', 'Что-то пошло не так и правило не добавилось');
    }



    public function edit(Request $request) {
      if (session('demo')) return back()->with('error', 'В демо-режиме нельзя изменять правила');
      $projects = new Projects();
      $stream = new Streamkey();
      $stream = $stream->find(1);
      $arrContextOptions=array(
          "ssl"=>array(
              "verify_peer"=>true,
              "verify_peer_name"=>true,
          ),
      );
      $rules = json_decode(file_get_contents("https://$stream->endpoint/rules?key=$stream->streamkey", false, stream_context_create($arrContextOptions)), true);

      $project = $request->project_name;
      $rule_tag	= $request->rule_tag;
        $array_request = $request->toArray();
        $keys = array_keys($array_request);
        foreach ($keys as $key) {
          if (strpos($key, 'mode_') !== FALSE) $id = substr($key, 4, 100000);
        }
      if (!empty($request->brand)) $brand = $request->brand; else $brand = NULL;
      if (!empty($request->key_words)) $key_words = $request->key_words; else $key_words = NULL;
      if (!empty($request->help_words) AND $array_request['add_help_words'.$id] == 'on') $help_words = $request->help_words; else $help_words = NULL;
      if (!empty($request->minus_words)) $minus_words = $request->minus_words; else $minus_words = NULL;
      $mode = $array_request['mode'.$id];
      $vkid	= session('vkid');
      $search_tag = $vkid.$rule_tag;

    	if (!empty($rules) AND $rules['code'] == 200) {
          $array_tags = array_column($rules['rules'], 'tag');
          $found_rules = array_search($search_tag, $array_tags);
          $old_rule_value = $rules['rules'][$found_rules]['value'];
    	}

      switch ($mode) {
        case 1: if (!empty($help_words)) $rule_value = $brand .' '. $help_words; else $rule_value = $brand; break;
        case 3: $rule_value = $key_words; break;
        default: return back()->with('error', 'Выберите метод сбора постов/упоминаний'); break;
      }

      $rule_value1 = explode(' ', $rule_value);
      $rule_value1 = array_unique($rule_value1);
      $rule_value = implode(' ', $rule_value1);

      if (!empty($minus_words)) $rule_value .= ' '.$minus_words;
      $rule_value = str_replace('+', ' ', $rule_value);

      $ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://'.$stream->endpoint.'/rules?key='.$stream->streamkey);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "DELETE");
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
			curl_setopt($ch, CURLOPT_POSTFIELDS, '{"tag":"'.$vkid.$rule_tag.'"}');
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
			$out = json_decode(curl_exec($ch), true);
			curl_close($ch);

      $rule_json = array('rule' => array ('value' => $rule_value, 'tag' => $vkid.$rule_tag));
      $rule_json = json_encode ($rule_json, JSON_UNESCAPED_UNICODE);

      $ch = curl_init();

      curl_setopt($ch, CURLOPT_URL, 'https://'.$stream->endpoint.'/rules?key='.$stream->streamkey);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_POST, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_POSTFIELDS, $rule_json);
      curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
      $out1 = json_decode(curl_exec($ch), true);

      curl_close($ch);

      if (!empty($out1['code']) AND $out1['code'] == 200) {
      //  $cut = $projects->where('vkid', $vkid)->where('project_name', $project)->distinct()->pluck('cut');
        if ($mode == 1) {
          $key_words = NULL;
          $brand_for_edit = $brand;
          $help_words_for_edit = $help_words;

          if (!empty($brand)) {
            $brand = preg_replace('/&#\d+;(*SKIP)(*F)|\d+/', '', $brand);
            file_put_contents('temp/stream_rule.txt', $brand);
            $brand=str_replace('?', '', mb_strtolower(shell_exec('cd temp/ && '.env('MYSTEM').' stream_rule.txt -ln')));
          }
          if (!empty($help_words) AND $array_request['add_help_words'.$id] == 'on') {
            $help_words = preg_replace('/&#\d+;(*SKIP)(*F)|\d+/', '', $help_words);
            file_put_contents('temp/stream_rule.txt', $help_words);
            $help_words=str_replace('?', '', mb_strtolower(shell_exec('cd temp/ && '.env('MYSTEM').' stream_rule.txt -ln')));
          }
            else $help_words_for_edit = $help_words = NULL;

          $data['old'] = NULL;
          $data['mode1'] = $brand;
          $data['mode1_edit'] = $brand_for_edit;
          $data['mode2'] = $help_words;
          $data['mode2_edit'] = $help_words_for_edit;
          $data['mode3'] = NULL;
          $data['mode3_edit'] = NULL;
          $data['minus_words'] = $minus_words;

          $projects->updateOrCreate(['vkid' => $vkid, 'project_name' => $project, 'rule' => $rule_tag], $data);
        }
        if ($mode == 3) {
          $brand = $help_words = NULL;
          $key_words_for_edit = $key_words;
          if (!empty($key_words)) {
            $key_words = preg_replace('/&#\d+;(*SKIP)(*F)|\d+/', '', $key_words);
            file_put_contents('temp/stream_rule.txt', $key_words);
            $key_words=str_replace('?', '', mb_strtolower(shell_exec('cd temp/ && '.env('MYSTEM').' stream_rule.txt -ln')));
          }
          $data['old'] = NULL;
          $data['mode1'] = NULL;
          $data['mode1_edit'] = NULL;
          $data['mode2'] = NULL;
          $data['mode2_edit'] = NULL;
          $data['mode3'] = $key_words;
          $data['mode3_edit'] = $key_words_for_edit;
          $data['minus_words'] = $minus_words;

          $projects->updateOrCreate(['vkid' => $vkid, 'project_name' => $project, 'rule' => $rule_tag], $data);
        }
        return back()->with('success', 'Правило <b>'.$rule_tag.'</b> отредактировано');
      }
      elseif (!empty($out1['error']['error_code'])) {
        $rule_json = array('rule' => array ('value' => $old_rule_value, 'tag' => $vkid.$rule_tag));
      				$rule_json = json_encode ($rule_json,JSON_UNESCAPED_UNICODE);

      				$ch = curl_init();

      				curl_setopt($ch, CURLOPT_URL, 'https://'.$stream->endpoint.'/rules?key='.$stream->streamkey);
      				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      				curl_setopt($ch, CURLOPT_POST, true);
              curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
              curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      				curl_setopt($ch, CURLOPT_POSTFIELDS, $rule_json);
      				curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
      				$out = json_decode(curl_exec($ch), true);
      				curl_close($ch);

              switch($out1['error']['error_code']) {
                case 2000: return back()->with('error', 'Не удалось распарсить JSON в теле запроса'); break;
                case 2001: return back()->with('error', 'Правило с такой меткой уже существует'); break;
                case 2003: return back()->with('error', 'Не удалось распарсить содержимое правила'); break;
                case 2004: return back()->with('error', 'Слишком много фильтров в одном правиле'); break;
                case 2005: return back()->with('error', 'Непарные кавычки'); break;
                case 2006: return back()->with('error', 'Слишком много правил в этом потоке'); break;
                case 2008: return back()->with('error', 'Должно быть хотя бы одно ключевое слово без минуса'); break;

                default: return back()->with('error', 'произошла какая-то ошибка '); break;
              }
      }
      return back()->with('error', 'Что-то пошло не так и правило не отредактировалось');
    }
}
