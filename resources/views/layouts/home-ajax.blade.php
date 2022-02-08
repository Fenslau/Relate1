@isset ($info['found'])
  <div class="w-100 alert alert-success">
    {!! $info['found'] !!}
  </div>
@elseif (@empty($info['token']) &&  @isset ($info['search']))
  <div class="w-100 alert alert-warning">
    По вашему запросу не нашлось ни одной групы. Может быть вы допустили в нём ошибку?
  </div>
@endisset
@isset ($info['demo'])
  <div class="w-100 alert alert-warning">
    Демо-поиск: максимум <b>10</b> групп; для просмотра 1000 групп, оплатите полный доступ. В разделе <a href="{{ route('groupsearch') }}">"Группы с открытой стеной"</a> можно скачивать до 100 000 групп.
  </div>
@endisset

@include('inc.obsolete-token')


  @isset ($info['search'])
    @isset($info['found'])
      <div class="w-100 m-0 alert alert-info"><strong><a class="alert-link" href="{{ route('download', 'storage%5Csimple_search%5C'.session('vkid').'_simple_search') }}">Скачать таблицу результатов поиска групп в формате Excel</a></strong></div>
    @endisset
  @else
    @if(Session::has('vkid'))
      <div class="w-100 m-0 alert alert-info"><strong><a class="alert-link" href="{{ route('download', 'temp\top1000') }}">Скачать таблицу топ1000групп в формате Excel</a></strong></div>
    @else
      <div class="w-100 m-0 alert alert-info"><strong><a class="alert-link" href="{{ route('download', 'temp\top1000') }}">Скачать таблицу топ1000групп в формате Excel</a></strong></div>
    @endif
  @endisset


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
          <td class="ava-group"><img loading="lazy" class="ava-group" alt="Ava" src="{{ $item['photo_50'] }}" /></td>
          <td class="group-name text-truncate text-nowrap text-left"><a rel="nofollow" target="_blank" href="https://vk.com/public{{ $item['id'] }}">{{ $item['name'] }}</a></td>
          <td>@if(!empty($item['members_count'])) @dec($item['members_count'])@endif</td>
          <td>
            @if($item['grouth'] > 0)
            <div class="text-success">+@dec($item['grouth'])
            </div>
            @elseif ($item['grouth'] < 0)
            <div class="text-danger">@if(!empty($item['grouth'])) @dec($item['grouth']) @endif
            </div>
            @endif  
          </td>
          <td>@if(!empty($item['reach'])) @dec($item['reach'])@endif<br /><small>{{ $item['reach_to_sub'] }}</small></td>
          <td>@if(!empty($item['reach_subscribers'])) @dec($item['reach_subscribers'])@endif<br /><small>{{ $item['sub_to_reach'] }}</small></td>
          <td>@if(!empty($item['female'])) @dec($item['female'])@endif<br /><small>{{ $item['female_proc'] }}</small></td>
          <td>@if(!empty($item['male'])) @dec($item['male'])@endif<br /><small>{{ $item['male_proc'] }}</small></td>
          <td>@if(!empty($item['visitors'])) @dec($item['visitors'])@endif<br /><small>{{ $item['views_to_visit'] }}</small></td>
          <td>@if(!empty($item['over_18'])) @dec($item['over_18'])@endif<br /><small>{{ $item['over_18_procent'] }}</small></td>
          <td>{{ $item['cities'] }}<br /><small>@if(!empty($item['cities_count'])) @dec($item['cities_count']) @endif{{ $item['max_visitors'] }}</small></td>
          <td>
            @if($item['wall'] == 'открытая')
            <div class="text-success">открытая</div>
            @else
            {{ $item['wall'] }}
            @endif
            <em>
              @if($item['can_post'] == 'посты и комменты')
              <div class="text-success">посты и комменты</div>
              @else
              {{ $item['can_post'] }}
              @endif
            </em>
          </td>
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
@endisset
