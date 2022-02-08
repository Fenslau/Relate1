
  <div class="container-fluid">
    <div class="row">
      @isset ($info['found'])
        <div class="w-100 alert alert-success">
          {!! $info['found'] !!}
        </div>
      @elseif (@empty($info['warning']) && @empty($info['token']))
        <div class="w-100 alert alert-warning">
          По вашему запросу не нашлось ни одного подписчика. Может быть вы допустили в нём ошибку?
        </div>
      @endisset

      @include('inc.obsolete-token')

      @isset ($info['demo'])
        <div class="w-100 alert alert-warning">
          Демо-поиск: максимум <b>100</b> подписчиков; В <a href="{{ route('tarifs') }}">полной версии</a> за раз можно собрать до 1 000 000 подписчиков, и даже больше
        </div>
      @endisset

      @isset ($info['found'])
        <div class="w-100 m-0 alert alert-info">
          <strong><a class="alert-link" href="{{ route('download', 'storage%5Cgetusers%5C'.session('vkid').'_getusers') }}">Скачать таблицу результатов сбора подписчиков в формате Excel</a></strong>
        </div>

        @isset ($items)
        <table class="table table-striped table-bordered table-hover table-sm table-responsive">
            <thead class="thead-dark">
              <tr class="text-center">
                <th>№</th>
                <th>User id</th>
                @isset ($request->common_info)
                <th>Имя Фамилия</th>
                <th>Пол</th>
                <th>Возраст</th>
                <th>Страна</th>
                <th>Город</th>
                <th>Онлайн</th>
                <th>Можно постить на стену</th>
                <th>Можно писать в ЛС</th>
                <th>Был онлайн</th>
                <th>Платформа</th>
                @endisset
                @isset ($request->contacts)
                <th>Контакты</th>
                @endisset
                @isset ($request->site)
                <th>Сайт</th>
                @endisset
                @if (isset($request->relation) || isset($request->half2))
                <th>Отношения</th>
                @endif
                @isset ($request->social)
                <th>Соцсети</th>
                @endisset
                @isset ($request->bday)
                <th>День рождения</th>
                @endisset
              </tr>
            </thead>

            <tbody>
              @foreach ($items as $item)
              @break ($loop->iteration > 1000 || $item == 'access vk')
                <tr class="lh-md text-center">
                  <td>{{ $loop->iteration }}</td>
                  <td>{{ $item['id'] }}</td>
                  @isset ($request->common_info)
                  <td><a target="_blank" href="https://vk.com/id{{ $item['id'] }}">{{ @$item['first_name'] }} {{ @$item['last_name'] }}</a></td>
                  <td>{{ @$item['sex'] }}</td>
                  <td>{{ @$item['bdate'] }}</td>
                  <td>{{ @$item['country'] }}</td>
                  <td>{{ @$item['city'] }}</td>
                  <td>{{ @$item['online'] }}</td>
                  <td>{{ @$item['can_post'] }}</td>
                  <td>{{ @$item['can_write_private_message'] }}</td>
                  <td>
                    @if (date('U')- strtotime($item['last_seen']) > 5184000)
                      <div class="text-danger">{{ $item['last_seen'] }}</div>
                    @elseif (date('U')- strtotime($item['last_seen']) < 172800)
                        <div class="text-success">{{ $item['last_seen'] }}</div>
                    @else {{ $item['last_seen'] }}
                    @endif
                  </td>
                  <td>{{ @$item['platform'] }}</td>
                  @endisset
                  @isset ($request->contacts)
                  <td class="group-name text-truncate text-nowrap text-left">{{ @$item['home_phone'] }} {{ @$item['mobile_phone'] }}</td>
                  @endisset
                  @isset ($request->site)
                  <td class="group-name text-truncate text-nowrap text-left">{{ @$item['site'] }}</td>
                  @endisset
                  @if (isset($request->relation) || isset($request->half2))
                  <td>{{ @$item['relation'] }}<br /><a href="{{ @$item['half2'] }}">{{ @$item['half2'] }}</a></td>
                  @endif
                  @isset ($request->social)
                  <td>
                    @isset ($item['instagram']) Инстаграм: {{ @$item['instagram'] }} <br /> @endisset
                    @isset ($item['twitter']) Твиттер: {{ @$item['twitter'] }} <br /> @endisset
                    @isset ($item['skype']) Скайп: {{ @$item['skype'] }}  <br /> @endisset
                    @isset ($item['facebook']) Фэйсбук: {{ @$item['facebook'] }}  <br /> @endisset
                    @isset ($item['facebook_name']) Имя фэйсбук: {{ @$item['facebook_name'] }} @endisset
                  </td>
                  @endisset
                  @isset ($request->bday)
                  <td>{{ @$item['bday'] }}</td>
                  @endisset
                </tr>
              @endforeach
            </tbody>
        </table>
        @endisset
        @endisset
      </div>
    </div>
    <script>

      $(document).ready(function () {
        $('#new-search').on('click', function (e) {
          e.preventDefault();
          $('#table-search').addClass('d-none');
          $('#new-search').addClass('d-none');
          $('#js-load').removeClass('d-none');
          $('.search-form').removeClass('d-none');
        });
      });
    </script>
