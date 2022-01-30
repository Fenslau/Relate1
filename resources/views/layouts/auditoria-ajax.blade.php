
  <div class="container-xl">
    <div class="row">
      @isset ($info['found'])
        <div class="w-100 alert alert-success">
          {!! $info['found'] !!}
        </div>
      @elseif (@empty($info['warning']) && @empty($info['token']))
        <div class="w-100 alert alert-warning">
          По вашему запросу не нашлось ни одной группы. Может быть вы допустили в нём ошибку?
        </div>
      @endisset

      @include('inc.obsolete-token')

      @isset ($info['demo'])
        <div class="w-100 alert alert-warning">
          Демо-поиск: максимум <b>10</b> групп; В <a href="{{ route('tarifs') }}">полной версии</a> за раз можно собрать до 500 групп.
        </div>
      @endisset

      @isset ($info['found'])
        <div class="w-100 m-0 alert alert-info">
          <strong><a class="alert-link" href="{{ route('download', 'storage%5Cauditoria%5C'.session('vkid').'_auditoria') }}">Скачать таблицу результатов поиска похожих групп в формате Excel</a></strong>
        </div>

        @isset ($items)
        <table class="table table-striped table-bordered table-hover table-sm table-responsive">
            <thead class="thead-dark">
              <tr class="text-center">
                <th>№</th>
                <th>id группы</th>
                <th></th>
                <th>Название</th>
                <th>Город</th>
                <th>Подписчики</th>
                <th>Сайт</th>
                <th>Стена</th>
                <th>Вериф.</th>
                <th>Частота</th>
              </tr>
            </thead>

            <tbody>
              @foreach ($items as $item)
              @break ($loop->iteration > 1000)
                <tr class="lh-md text-center">
                  <td>{{ $loop->iteration }}</td>
                  <td>{{ $item['group_id'] }}</td>
                  <td class="ava-group"><img loading="lazy" class="ava-group" src="{{ $item['photo'] }}" /></td>
                  <td class="group-name text-truncate text-nowrap text-left"><a target="_blank" href="https://vk.com/public{{ $item['group_id'] }}">{{ $item['name'] }}</a></td>
                  <td>{{ $item['city'] }}</td>
                  <td>@dec($item['members_count'])</td>
                  <td class="group-name text-truncate text-nowrap text-left">{{ $item['site'] }}</td>
                  <td>
                    @if($item['wall'] == 'открытая')
                    <div class="text-success">открытая</div>
                    @else
                    {{ $item['wall'] }}
                    @endif</td>
                  <td>{{ $item['verified'] }}</td>
                  <td>{{ $item['freq'] }}</td>
                </tr>
              @endforeach
            </tbody>
        </table>
        @endisset
        @endisset
      </div>
    </div>
