@extends('layouts.app')

@section('title-block', 'Мои файлы')
@section('description-block', 'Архив запросов к сайту в виде файлов Excel')

@section('content')

<div class="container">
  @include('inc.toast')
  @include('inc.tarif-recall')
  <h2 class="m-3 text-center text-uppercase" >Мои файлы</h2>
  <div class="alert alert-info">
    Здесь будут храниться ваши файлы с реультатами запросов к сайту <br />
    Файлы хранятся +1 день после окончания действия тарифа. Чтобы не потерять их, продлите <a href="{{ route('tarifs') }}">доступ</a>
  </div>
  @if($info['demo'] == TRUE)
    <div class="alert alert-primary">
      <a class="alert-link" href="#">/simple_search/151103777_simple_search_20_07_22_07_30_53.xlsx</a>
    </div>
    <div class="alert alert-primary">
      <a class="alert-link" href="#">/topusers_search/151103777_topusers_search_20_07_22_07_31_05.xlsx</a>
    </div>
    <div class="alert alert-primary">
      <a class="alert-link" href="#">/open_wall_search/151103777_open_wall_search_20_07_22_07_30_29.xlsx</a>
    </div>
    <div class="alert alert-primary">
      <a class="alert-link" href="#">/auditoria/151103777_auditoria_20_07_22_07_30_43.xlsx</a>
    </div>
  @else
    @foreach ($files as $file)
      <div class="alert alert-primary">
        <a class="alert-link" href="/storage{{ $file }}">{{ $file }}</a>
      </div>
    @endforeach
    <form class="mb-5" action="opros" method="post" autocomplete="off">
      @csrf
      <div class="input-group">
        <input class="form-control form-control-sm border border-success" type="text" name="opros"
          placeholder="Если у вас возникли проблемы, или вы нашли ошибку, напишите нам об этом">
          <div class="input-group-append">
            <button class="btn btn-sm btn-outline-success " type="submit">Отправить</button>
          </div>
      </div>
    </form>
  @endif

</div>

@endsection
