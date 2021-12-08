@extends('layouts.app')

@section('title-block', 'Сбор упоминаний о бренде ВК')

@section('content')

<div class="container">
  <h2 class="m-3 text-center text-uppercase">Этот раздел находится в разработке</h2>
  <h2 class="m-3 text-center text-uppercase">Собрать и анализировать посты</h2>


    @if (empty($items))
      <h4 class="text-center">Создайте свой новый проект <img style="width: 70px;" src="/images/create1.png"></h4>
      <div class="text-center m-5"><a class="btn btn-success" href="{{ route('stream') }}/Demo">ДЕМО-ВЕРСИЯ</a></div>
    @endif

@if(Session::has('vkid'))
    @isset($info['success'])
      <div class="alert alert-info">
        {!! $info['success'] !!}
      </div>
    @endisset
    @isset($info['warning'])
      <div class="alert alert-warning">
        {!! $info['warning'] !!}
      </div>
    @endisset

      <form action="{{ route('stream-add-project') }}" method="post">
        @csrf
        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <button class="btn btn-outline-success" type="submit" id="button-addon1">Добавить&nbsp;проект</button>
          </div>
          <input type="text" class="border-success border-left-0 form-control" name="project_name" placeholder="Название">
        </div>
      </form>

    @if (!empty($items))
      <h4>Мои проекты:</h4>
      <div class="pb-3 table-responsive">
        <form action="{{ route('stream-del-project') }}" method="post">
          @csrf
        <table class="d-table table table-striped table-hover table-sm table-responsive">
            <thead class="thead-dark">
              <tr class="text-center">
                <th scope="col">№</th>
                <th class="text-left" scope="col">Название</th>
                <th scope="col">Количество правил</th>
                <th scope="col">Количество записей</th>
                <th scope="col"></th>
              </tr>
            </thead>

            <tbody>
            @foreach ($items as $item)
              <tr class="lh-md text-center">
                <td>{{ $loop->iteration }}</td>
                <td class="text-left"><a href="{{ route('stream') }}/{{ $item['project_name'] }}">{{ $item['project_name'] }}</a></td>
                <td>{{ $item['rules_count'] }}</td>
                <td>{{ $item['count_stream_records'] }}</td>
                <td><button onclick="return confirm('Вы уверены?')" class="btn btn-sm btn-outline-danger" type="submit" name="del" value="{{ $item['id'] }}"><i class="fa fa-trash"></i> Удалить</button></td>
              </tr>
            @endforeach
            </tbody>
        </table>
        </form>
      </div>
    @endif
    @if (!empty($files))
      <h4>Мои файлы:</h4>
      <div class="pb-5 table-responsive">
        <form action="{{ route('stream-del-file') }}" method="post">
          @csrf
        <table class="d-table table table-striped table-hover table-sm table-responsive">
            <thead class="thead-dark">
              <tr class="text-center">
                <th scope="col">№</th>
                <th class="text-left" scope="col">Название</th>
                <th scope="col"></th>
              </tr>
            </thead>

            <tbody>
            @foreach ($files as $file)
              <tr class="lh-md text-center">
                <td>{{ $loop->iteration }}</td>
                @if (!empty($file['link']))
                  <td class="text-left"><a href="{{ route('download', 'storage%5Cstream%5C'.$file['link']) }}">{{ $file['link'] }}</a></td>
                @else
                  <td class="text-left text-info">файл подготавливается...</td>
                @endif
                <td>
                  @if (!empty($file['link']))
                    <button class="btn btn-sm btn-outline-danger" type="submit" name="del" value="{{ $file['id'] }}"><i class="fa fa-trash"></i> Удалить</button>
                  @endif
                </td>
              </tr>
            @endforeach
            </tbody>
        </table>
        </form>
      </div>
    @endif

@else <h5 class="text-center">Авторизуйтесь ВК, чтобы посмотреть Демо-версию</h5>
@endif

  <div class="pb-5">
    @isset($info['admin'])
      <div class="row">
        <form class="m-3" action="{{ route('stream-gen-key') }}" method="post">
          @csrf
          <button data-toggle="tooltip" title="Ключ для доступа к сервису стриминга для всего приложения ВКТОППОСТ (генерируется один раз)" class="btn btn-sm btn-outline-info" type="submit" name="gen" value="gen"><i class="fa fa-key"></i> Сгенерировать новый ключ</button>
        </form>

        <form class="m-3" action="{{ route('stream-fake-vkid') }}" method="post">
          @csrf
          <div class="input-group input-group-sm" data-toggle="tooltip" title="Позволяет посмотреть сервис от имени другого пользователя (вместе с его правилами, проектами и т.д.)">
            <select name="fakevkid" class="form-control">
              <option selected>Выберите ВК id для подмены</option>
              @foreach ($vkids as $vkid)
                <option value="{{ $vkid }}">{{ $vkid }}</option>
              @endforeach
            </select>
            <div class="input-group-append">
              <button class="btn btn-sm btn-outline-secondary" type="submit"><i class="far fa-user"></i> Подменить</button>
            </div>
          </div>
        </form>
        <form class="m-3" action="{{ route('stream-button', 'ruleDelete') }}" method="post">
          @csrf
          <div class="input-group input-group-sm" data-toggle="tooltip" title="Для удаления любых правил из Streaming API, например тех, которые были созданы ошибочно и не попали в интерфейс пользователя">
            <select name="rule_tag" class="form-control border-danger border-right-0">
              <option selected>Выберите правило</option>
                @if (!empty($vk_rules))
                  @foreach ($vk_rules as $rule)
                    <option value="{{ $rule }}">{{ $rule }}</option>
                  @endforeach
                @endif
            </select>
            <div class="input-group-append">
              <input type="hidden" name="admin" value="true">
              <button class="btn btn-sm btn-outline-danger" type="submit"><i class="fas fa-trash-alt"></i> Удалить</button>
            </div>
          </div>
        </form>
      </div>
    @endisset
  </div>
</div>

@endsection
