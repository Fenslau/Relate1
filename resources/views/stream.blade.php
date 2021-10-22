@extends('layouts.app')

@section('title-block', 'Сбор упоминаний о бренде ВК')

@section('content')

<div class="container">
  <h2 class="m-3 text-center text-uppercase">Этот раздел находится в разработке</h2>
  <h2 class="m-3 text-center text-uppercase">Собрать и анализировать посты</h2>


    @if (empty($items))
      <h4 class="text-center">Создайте свой новый проект <img style="width: 70px;" src="/images/create1.png"></h4>
      <div class="text-center m-5"><a class="btn btn-success" href="{{ route('stream') }}?project=Demo">ДЕМО-ВЕРСИЯ</a></div>
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
          <input type="text" class="form-control" name="project_name" placeholder="Название" aria-label="Пример текста с надстройкой кнопки" aria-describedby="button-addon1">
        </div>
      </form>

    @if (!empty($items))
      <h4>Мои проекты:</h4>
      <div class="pb-5 table-responsive">
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
                <td class="text-left"><a href="{{ route('stream') }}?project={{ $item['project_name'] }}">{{ $item['project_name'] }}</a></td>
                <td>{{ $item['rules_count'] }}</td>
                <td>{{ $item['count_stream_records'] }}</td>
                <td><button class="btn btn-sm btn-outline-danger" type="submit" name="del" value="{{ $item['id'] }}"><i class="fa fa-trash" aria-hidden="true"></i> Удалить</button></td>
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

  </div>


  <!-- <div class="row">
    <div class="col-md-10">








    </div>
    <div class="col-md-2">
      @include('inc.aside')
    </div>
  </div> -->
</div>

@endsection
