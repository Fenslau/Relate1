@extends('layouts.app')

@section('title-block', 'Сбор упоминаний о бренде ВК')
@section('description-block', 'Вы можете собрать посты или комментарии ВКонтакте о вашей фирме или бренде в реальном времени')

@section('content')
<div class="container">
  @isset($info['tarif'])
    <div class="alert alert-warning">
      {!! $info['tarif'] !!}
    </div>
  @endisset
  <h2 class="m-3 text-center text-uppercase">Собрать и анализировать посты</h2>


    @if (empty($items))
      <div class="d-flex justify-content-between align-items-baseline">
        <a href="{{ route('home') }}" class="btn btn-sm btn-secondary text-white">На&nbsp;главную</a>
        <div class="d-flex flex-grow-1 justify-content-center">
            <div class="text-center m-4"><a
            @if(!Session::has('vkid'))
            data-toggle="tooltip" title="Нажмите кнопку 'Вход' и авторизуйтесь через ВК, чтобы протестировать Демо-версию или получить полный доступ к данному разделу"
            @endif
            class="btn btn-success text-uppercase" href="/streamdemo"><span class="d-md-inline-block d-none">Посмотреть Демо-версию</span><span class="d-md-none">Демо-версия</span></a><br /><small>бесплатно</small></div>
          </div>
      </div>
      <div class="row">
        <img class="col-md-4" src="/images/H01.jpg" alt="Теги, активные авторы, количество запросов">
        <div class="col-md-8 mt-md-3 mt-xl-5">
          <b>Собирайте упоминания с названием бренда или компании в режиме реального времени.</b><br />
          Вы будете в курсе самых свежих комментариев и не пропустите ни одного публичного сообщения (поста), содержащего ключевые фразы.<br /><br />
          <b>Задайте нужный период, и система соберет вам упоминания (посты) начиная с выбранной даты по настоящее время.</b> Вы узнаете, что пользователи писали о компании/бренде, и как менялось их мнение, например, в течение года.<br /><br />
          <b>Облако тэгов и ключевых слов</b> выделит для вас основную мысль всех упоминаний о бренде или компании.
        </div>
      </div>
      <div class="row">
        <div class="col-md-6">
          <br /><br />
          <b>Система сама проанализирует и отобразит весь объем найденных упоминаний по датам.</b> <br />
          Вы увидите, как менялось количество публичных сообщений (постов/комментариев) о бренде/компании, например, в течение года или нескольких месяцев под влиянием различных факторов (событий, рекламных компаний и др.).
        </div>
        <img class="col-md-4" src="/images/H02.jpg" alt="Распределение по регионам">
        <div class="col-md-2">
        </div>
      </div>
      <div class="row">
        <div class="col-md-8">
          <br />
          <b>Система визуализирует статистику по авторам в виде дашборда,</b> которые распределяются <b>по полу, возрасту, региону, количеству подписчиков. Также система покажет вам список самых активных авторов.</b> <br />
          Вы лучше разберетесь, кто целевая аудитория бренда/компании, кто является амбассадором, а кто хейтером. Дашборд отображает статистику как по пользователям, так и по группам.
        </div>
        <img class="col-md-4 mt-md-n5" src="/images/H03.jpg" alt="Статистика по авторам">
      </div>
      <div class="row">
        <img class="col-md-4" src="/images/H0.jpg" alt="Фильтры по критериям">
        <div class="col-md-8">
          <br /><br />
          <b>Фильтры</b> помогут выбрать <b>только те упоминания (посты),</b> которые разместили <b>пользователи с заданным социально-демографическим профилем.</b> <br />
          Вы легко найдете, что пишут о бренде/компании мужчины или женщины определенного возраста из заданного региона/страны. Фильтры настроены как по пользователям, так и по группам.<br /><br />
          <b>Экспорт в Excel</b> поможет вам сохранить данные у себя на компьютере.
        </div>
      </div>

      <h4 class="mt-3 text-center">Создайте свой новый проект <img style="width: 70px;" alt="Create Project" src="/images/create1.png"></h4>
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
    @isset($info['success'])
      <form action="{{ route('stream-add-project') }}" method="post">
        @csrf
        <div class="input-group mb-3">
          <div class="input-group-prepend">
            <button class="btn btn-outline-success" type="submit" id="button-addon1">Добавить&nbsp;проект</button>
          </div>
          <input type="text" class="border-success border-left-0 form-control" name="project_name" placeholder="Название">
        </div>
      </form>
    @endisset
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
                <td class="text-left"><a href="{{ route('post', $item['project_name']) }}">{{ $item['project_name'] }}</a></td>
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
    @if (session('demo'))
      <a class="btn btn-sm btn-warning" href="/end-demo"><i class="far fa-user"></i> Выйти из демо-режима</a>
    @endif
  </div>
</div>

@endsection
