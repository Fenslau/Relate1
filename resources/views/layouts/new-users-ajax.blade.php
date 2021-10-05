<div class="container">
  <div class="row">

    @isset ($info['found'])
      <div class="w-100 alert alert-success">
        {!! $info['found'] !!}
      </div>
    @endisset
    @isset ($info['warning'])
      <div class="w-100 alert alert-warning">
        {!! $info['warning'] !!}
      </div>
    @endisset
    @isset ($info['demo'])
      <div class="w-100 alert alert-warning">
        Демо-режим: максимум <b>1</b> группа; В <a href="{{ route('tarifs') }}">полной версии</a> можно отслеживать до 10 групп.
      </div>
    @endisset

  @include('inc.list-follow-groups')

  @if (!empty($items_new))
    <h4>Вступили в группу:</h4>
    <div class="pb-2 table-responsive">
      <table class="d-table table table-striped table-bordered table-hover table-sm table-responsive">
          <thead class="thead-dark">
            <tr class="text-center">
              <th>№</th>
              <th>id</th>
              <th></th>
              <th>Имя</th>
              <th>Фамилия</th>
              <th>Пол</th>
              <th>Возраст</th>
              <th>Город</th>
              <th>Онлайн</th>
              <th>Можно постить на стене</th>
              <th>Можно писать в личку</th>
            </tr>
          </thead>

          <tbody>
            @foreach ($items_new as $item)
            @break ($loop->iteration > 1000)
              <tr class="lh-md text-center">
                <td>{{ $loop->iteration }}</td>
                <td><a rel="nofollow" target="_blank" href="https://vk.com/{{ $item['domain'] }}">{{ $item['id'] }}</a></td>
                <td class="ava-group"><img loading="lazy" class="ava-group" src="{{ $item['photo_100'] }}" /></td>
                <td>{{ $item['first_name'] }}</td>
                <td>{{ $item['last_name'] }}</td>
                <td>{{ $item['sex'] }}</td>
                <td>{{ $item['bdate'] }}</td>
                <td>{{ $item['city']['title'] }}</td>
                <td>{{ $item['online'] }}</td>
                <td>{{ $item['can_post'] }}</td>
                <td>{{ $item['can_write_private_message'] }}</td>
              </tr>
            @endforeach
          </tbody>
      </table>
    </div>
  @endif

  @if (!empty($items_old))
    <h4>Покинули группу:</h4>
    <div class="pb-2 table-responsive">
      <table class="d-table table table-striped table-bordered table-hover table-sm table-responsive">
          <thead class="thead-dark">
            <tr class="text-center">
              <th>№</th>
              <th>id</th>
              <th></th>
              <th>Имя</th>
              <th>Фамилия</th>
              <th>Пол</th>
              <th>Возраст</th>
              <th>Город</th>
              <th>Онлайн</th>
              <th>Можно постить на стене</th>
              <th>Можно писать в личку</th>
            </tr>
          </thead>

          <tbody>
            @foreach ($items_old as $item)
            @break ($loop->iteration > 1000)
              <tr class="lh-md text-center">
                <td>{{ $loop->iteration }}</td>
                <td><a rel="nofollow" target="_blank" href="https://vk.com/{{ $item['domain'] }}">{{ $item['id'] }}</a></td>
                <td class="ava-group"><img loading="lazy" class="ava-group" src="{{ $item['photo_100'] }}" /></td>
                <td>{{ $item['first_name'] }}</td>
                <td>{{ $item['last_name'] }}</td>
                <td>{{ $item['sex'] }}</td>
                <td>{{ $item['bdate'] }}</td>
                <td>{{ $item['city']['title'] }}</td>
                <td>{{ $item['online'] }}</td>
                <td>{{ $item['can_post'] }}</td>
                <td>{{ $item['can_write_private_message'] }}</td>
              </tr>
            @endforeach
          </tbody>
      </table>
    </div>
  @endif
  </div>
</div>


<script type="text/javascript">

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
