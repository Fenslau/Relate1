
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
          Демо-поиск: максимум <b>10</b> пользователей; В <a href="{{ route('tarifs') }}">полной версии</a> за раз можно собрать до 50 000
        </div>
      @endisset

      @isset ($info['found'])
        <div class="w-100 m-0 alert alert-info">
          <strong><a class="alert-link" href="{{ route('download', 'storage%5Ctopusers_search%5C'.session('vkid').'_topusers_search_'.$info['filetime']) }}">Скачать таблицу результатов сбора пользователей в формате Excel</a></strong>
        </div>

        @isset ($items)
        <table class="table table-striped table-bordered table-hover table-sm table-responsive">
            <thead class="thead-dark">
              <tr class="text-center">
                <th>№</th>
                <th>Имя Фамилия</th>
                <th></th>
                <th>Страна</th>
                <th>Город</th>
                <th>Возраст</th>
                <th>Подписчики</th>
                <th>О себе</th>
                <th>Деятельность</th>
                <th>Место работы</th>
                <th>Можно писать в ЛС</th>
                <th>Можно добавить в друзья</th>
                <th>Можно постить на стене</th>
                <th>Вериф-ый польз-ль</th>
                <th>Отношения</th>
                <th>Контакты</th>
              </tr>
            </thead>

            <tbody>
              @foreach ($items as $item)
                <tr class="lh-md text-center">
                  <td>{{ $loop->iteration }}</td>
                  <td><a target="_blank" href="https://vk.com/id{{ $item['vkid'] }}">{{ @$item['first_name'] }} {{ @$item['last_name'] }}</a></td>
                  <td class="ava-group"><img loading="lazy" class="ava-group" src="{{ $item['photo_100'] }}" /></td>
                  <td>{{ @$item['country'] }}</td>
                  <td>{{ @$item['city'] }}</td>
                  <td>{{ @$item['age'] }}</td>
                  <td>{{ @$item['followers_count'] }}</td>
                  <td class="max-td">{{ @$item['about'] }}</td>
                  <td class="max-td">{{ @$item['activities'] }}</td>
                  <td class="max-td">{{ @$item['occupation'] }}</td>
                  <td>{{ @$item['can_write_private_message'] }}</td>
                  <td>{{ @$item['can_send_friend_request'] }}</td>
                  <td>{{ @$item['can_post'] }}</td>
                  <td>{{ @$item['verified'] }}</td>
                  <td>{{ @$item['status'] }}</td>

                  <td>
                    @if (!empty($item['livejournal']) || !empty($item['twitter']) || !empty($item['skype']))
                      @isset ($item['livejournal']) LiveJournal: {{ @$item['livejournal'] }} <br /> @endisset
                      @isset ($item['twitter']) Твиттер: {{ @$item['twitter'] }} <br /> @endisset
                      @isset ($item['skype']) Скайп: {{ @$item['skype'] }}  <br /> @endisset
                    @endif
                  </td>

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
