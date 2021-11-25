@if (!empty($items_groups))
  <div class="search-form pb-5 table-responsive">
    <form class="follow-form" action="{{ route('del-follow-group') }}" method="post">
      <table class="d-table table table-striped table-bordered table-hover table-sm table-responsive">
        @csrf
        <thead class="thead-dark">
          <tr class="text-center">
            <th>№</th>
            <th>id группы</th>
            <th>Название</th>
            <th></th>
            <th>Последний сбор</th>
            <th>Файл</th>
            <th></th>
          </tr>
        </thead>

        <tbody>
          @foreach ($items_groups as $item)
          @break ($loop->iteration > 1000)
            <tr class="lh-md text-center">
              <td>{{ $loop->iteration }}</td>
              <td>{{ $item['group_id'] }}</td>
              <td class="group-name text-truncate text-nowrap text-left"><a target="_blank" href="https://vk.com/public{{ $item['group_id'] }}">{{ $item['name'] }}</a></td>
              <td><button class="follow btn btn-sm btn-primary vk-top-bg" type="submit" name="id" value="{{ $item['group_id'] }}"><i class="fa fa-search"></i><span class="spinner-border spinner-border-sm text-light d-none"></span> Собрать новичков</button>
              <td>{{ $item['updated_at'] }}</td>
              <td>
                @if (!empty($item['file']))
                  <a class="alert-link" href="{{ route('download', 'storage%5Cnew-users%5C'.$item['file']) }}"><i class="fa fa-file-excel"></i></a>
                @endif
              </td>
              <td><button data-toggle="tooltip" title="Больше не отслеживать" class="not-follow btn btn-sm btn-outline-danger" type="submit" name="del" value="{{ $item['group_id'] }}"><i class="fa fa-trash"></i> Удалить</button></td>
            </tr>
          @endforeach
        </tbody>
      </table>
    </form>
  </div>


@endif
