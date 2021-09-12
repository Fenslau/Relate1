@isset ($info['found'])
<div class="container-fluid">
  <div class="row">

      <div class="w-100 alert alert-success">
        {!! $info['found'] !!}
      </div>
    @else
      <div class="w-100 alert alert-warning">
        По вашему запросу не нашлось ни одной групы. Может быть вы допустили в нём ошибку?
      </div>
    @endisset
    @isset ($info['demo'])
      <div class="w-100 alert alert-warning">
        Демо-поиск: максимум <b>10</b> групп; В <a href="{{ route('tarifs') }}">полной версии</a> за раз можно собрать до 100 000 групп.
      </div>
    @endisset

    @isset ($info['found'])
      <div class="w-100 m-0 alert alert-info">
        <strong><a class="alert-link" href="{{ route('download', 'storage%5Copen_wall_search%5C'.session('vkid').'_open_wall_search') }}">Скачать таблицу результатов поиска групп в формате Excel</a></strong>
      </div>

      @isset ($items)
      <table class="lh-sm table table-striped table-bordered table-hover table-sm table-responsive">
          <thead class="thead-dark">
            <tr class="text-center">
              <th>№</th>
              <th>id группы</th>
              <th>Название</th>
              <th>Город</th>
              <th>Подписчики</th>
              <th>Тип сообщества</th>
              <th>Стена</th>
              <th>Сайт</th>
              <th>Вериф.</th>
              <th>Магазин</th>
              <th>Закрытое/ открытое</th>
              <th>Контакты</th>
            </tr>
          </thead>

          <tbody>
            @foreach ($items as $item)
            @break ($loop->iteration > 1000)
              <tr class="lh-md text-center">
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item['group_id'] }}</td>
                <td class="group-name text-truncate text-nowrap text-left"><a target="_blank" href="https://vk.com/public{{ $item['group_id'] }}">{{ $item['name'] }}</a></td>
                <td>{{ $item['city'] }}</td>
                <td>{{ $item['members_count'] }}</td>
                <td>{{ $item['type'] }}</td>
                <td>{{ $item['wall'] }}</td>
                <td class="group-name text-truncate text-nowrap text-left">{{ $item['site'] }}</td>
                <td>{{ $item['verified'] }}</td>
                <td>{{ $item['market'] }}</td>
                <td>{{ $item['is_closed'] }}</td>
                <td data-toggle="tooltip" title="{{ $item['contacts'] }}" class="group-name text-truncate text-nowrap text-left">{{ $item['contacts'] }}</td>
              </tr>
            @endforeach
          </tbody>
      </table>
    </div>
  </div>
  @endisset
@endisset
<script type="text/javascript">
  $(function () {
    $('[data-toggle="tooltip"]').tooltip()
  })

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
