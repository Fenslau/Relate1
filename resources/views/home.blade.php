@extends('layouts.app')

@section('title-block', 'Поиск ТОП-групп ВК')
@section('description-block', 'Топ группы ВК + поиск по группам из разных городов. Поиск групп ВК по открытой стене. Статистика посетителей групп ВКонтакте')

@section('content')

<div class="container-fluid">
  @include('inc.toast')
  <div class="row">

    @if(Session::has('vkid'))
    <script type="text/javascript">
      var vkid={{ session('vkid') }};
      var url='{{ route('search') }}';
      var process1='simple_search';
    </script>

    <div class="m-3 p-2 border border-primary">
      <form class="needs-validation" id="search-submit" action="{{ route('search') }}" method="post">
        @csrf
          <div class="form-inline">
            <label class="" for="city">Город</label>
            <input data-toggle="tooltip" title="Если название города должно содержаться в названии группы, то вводите его в названии группы, а это поле оставьте пустым. Это поле для поиска групп, принадлежащих только определенному городу." class="form-control form-control-sm" type="text" name="city" id="city" value="{{ old('city') }}">
          </div>
          <div class="form-inline">
            <label class="" for="group-name">Часть названия группы</label>
            <input class="form-control form-control-sm" type="text" name="group_name" id="group-name" required value="{{ old('group_name') }}">
          </div>
          <div class="form-inline">
            <label class="col-form-label" for="sort">Сортировать по</label>
            <select class="form-control form-control-sm" name="sort" id="sort">
              <option value="0">умолчанию (аналогично результатам поиска vk.com)</option>
              <option value="1">скорости роста</option>
              <option value="2">отношению дневной посещаемости к количеству пользователе</option>
              <option value="3">отношению количества лайков к количеству пользователей</option>
              <option value="4">отношению количества комментариев к количеству пользователей</option>
              <option value="5">отношению количества записей в обсуждениях к количеству пользователей</option>
              <option selected value="6">количеству участников</option>
            </select>
          </div>
          <div class="form-inline">
            <div class="form-check form-check-inline" data-toggle="tooltip" title="Будет собираться дольше">
              <input class="form-check-input" name="stat" type="checkbox" id="inlineCheckbox1" value="stat" {{ (is_array(old('stat')) and in_array(1, old('stat'))) ? ' checked' : '' }}>
              <label class="form-check-label" for="inlineCheckbox1">Со статистикой</label>
            </div>
            <div class="form-check form-check-inline" data-toggle="tooltip" title="Ещё дольше!">
              <input class="form-check-input" name="date" type="checkbox" id="inlineCheckbox2" value="date" {{ (is_array(old('date')) and in_array(1, old('date'))) ? ' checked' : '' }}>
              <label class="form-check-label" for="inlineCheckbox2">С датами последних постов</label>
            </div>

            <button id="js-load" disabled class="btn btn-sm btn-primary vk-top-bg" type="submit" name="submit"><i class="fa fa-search"></i><span class="spinner-border spinner-border-sm text-light d-none"></span> Найти группы</button>
          </div>
      </form>

      <div class="mt-2 progress">
        <div id="progress" class="progress-bar progress-bar-striped progress-bar-animated" style="width: 0%"></div>
      </div>
      <div id="progress-text" class="px-2 mt-2 bg-secondary text-white"></div>
    </div>
    @else
    <div class="w-100 alert alert-warning">
      <strong>Зарегистрируйтесь или авторизуйтесь через ВК, чтобы протестировать возможности в демо-режиме или получить полный доступ к ресурсам сайта.</strong>
    </div>
    @endif
    <div class="table-responsive" id="table-search">


    @isset ($info['found'])
      <div class="w-100 alert alert-success">
        {!! $info['found'] !!}
      </div>
    @endisset
    @isset ($info['demo'])
      <div class="w-100 alert alert-warning">
        Демо-поиск: максимум <b>10</b> групп; для просмотра 1000 групп, оплатите полный доступ. В разделе <a href="{{ route('groupsearch') }}">"Группы с открытой стеной"</a> можно скачивать до 100 000 групп.
      </div>
    @endisset

    <div class="w-100 m-0 alert alert-info">
      @isset ($info['search'])
      <strong><a class="alert-link" href="{{ route('download', 'storage%5Csimple_search%5C'.session('vkid').'_simple_search') }}">Скачать таблицу результатов поиска групп в формате Excel</a></strong>
      @else
        @if(Session::has('vkid'))
          <strong><a class="alert-link" href="{{ route('download', 'temp\top1000') }}">Скачать таблицу топ1000групп в формате Excel</a></strong>
        @else
          <strong><a class="alert-link" href="{{ route('download', 'temp\top1000') }}">Скачать таблицу топ1000групп в формате Excel</a></strong>
        @endif
      @endisset
    </div>

    @isset ($items)

      <table class="lh-sm table table-striped table-bordered table-hover table-sm table-responsive">
          <thead class="thead-dark">
            <tr class="text-center">
              <th>№</th>
              <th>id группы</th>
              <th> </th>
              <th>Название</th>
              <th>Подписчики</th>
              <th>Прирост<br/><small>(за текущие сутки начиная с 0:00)</small></th>
              <th>Охват <br/><small>(отношение полного охвата к охвату подписчиков)</small></th>
              <th>Охват<br />подписч.<br/><small>(% от полного охвата)</small></th>
              <th>Жен<br/><small>(% от посетителей)</small></th>
              <th>Муж<br/><small>(% от посетителей)</small></th>
              <th>Посетители<br /><small>(кол-во просмотров на посетителя)</small></th>
              <th>Старше<br /> 18 лет<br/><small>(% от посетителей) </small></th>
              <th><small>Наибольшее<br />за сутки</small><br/>Из города</th>
              <th>Стена<br/><small>Открытая /<br />только комментарии</small></th>
              <th>Сообщество<br/><small>закрытое/открытое<br />тип сообщества</small></th>
              <th>Дата<br/><small>последнего поста</small></th>
            </tr>
          </thead>

          <tbody>
            @foreach ($items as $item)
              <tr class="lh-md text-center">
                <td>{{ $item['num'] }}</td>
                <td>{{ $item['id'] }}</td>
                <td class="ava-group"><img loading="lazy" class="ava-group" src="{{ $item['photo_50'] }}" /></td>
                <td class="group-name text-truncate text-nowrap text-left"><a rel="nofollow" target="_blank" href="https://vk.com/public{{ $item['id'] }}">{{ $item['name'] }}</a></td>
                <td>{{ $item['members_count'] }}</td>
                <td>
                  @if($item['grouth'] > 0)
                  <div class="text-success">+{{ $item['grouth'] }}
                  </div>
                  @elseif ($item['grouth'] < 0)
                  <div class="text-danger">{{ $item['grouth'] }}
                  </div>
                  @endif
                </td>
                <td>{{ $item['reach'] }}<br /><small>{{ $item['reach_to_sub'] }}</small></td>
                <td>{{ $item['reach_subscribers'] }}<br /><small>{{ $item['sub_to_reach'] }}</small></td>
                <td>{{ $item['female'] }}<br /><small>{{ $item['female_proc'] }}</small></td>
                <td>{{ $item['male'] }}<br /><small>{{ $item['male_proc'] }}</small></td>
                <td>{{ $item['visitors'] }}<br /><small>{{ $item['views_to_visit'] }}</small></td>
                <td>{{ $item['over_18'] }}<br /><small>{{ $item['over_18_procent'] }}</small></td>
                <td>{{ $item['cities'] }}<br /><small>{{ $item['cities_count'] }}{{ $item['max_visitors'] }}</small></td>
                <td>
                  @if($item['wall'] == 'открытая')
                  <div class="text-success">открытая</div>
                  @else
                  {{ $item['wall'] }}
                  @endif
                  <em>{{ $item['can_post'] }}</em></td>
                <td>{{ $item['is_closed'] }}<br /><em>{{ $item['type'] }}</em></td>
                <td>

                  @if (!empty($item['date']))
                    @if (date('U')- strtotime($item['date']) > 5184000)
                      <div class="text-danger">{{ $item['date'] }}</div>
                    @elseif (date('U')- strtotime($item['date']) < 172800)
                        <div class="text-success">{{ $item['date'] }}</div>
                    @else {{ $item['date'] }}
                    @endif
                  @endif
                </td>
              </tr>
            @endforeach
          </tbody>
      </table>
    </div>
    @endisset

  </div>
</div>

<div class="container-fluid bg-dark text-white py-2 px-5">
  <p class="mb-0"><a target="_blank" class="text-white" href="https://sphere-market.ru"> Маркетинговое агентство "СФЕРА"</a></p>
</div>
@endsection
