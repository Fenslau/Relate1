@extends('layouts.app')

@section('title-block', 'Группы ВК с похожей ЦА')

@section('content')

<div class="container">
  <form class="" id="search-submit" action="{{ route('auditoria') }}" method="post">
    @csrf
    <h2 class="m-3 text-center text-uppercase" >Группы с похожей целевой аудиторией</h2>
    @include('inc.buttons-and-progress', ['link' => 'auditoria', 'button' => 'Найти похожие группы'])

    <div class="search-form row pb-5">
      <div class="col-md-6 ">

        <div class="">
          <p>Введите <b>ссылку на группу</b> из ВК или <b>ID группы</b>, в которой есть ваша целевая аудитория, после чего нажмите кнопку "Начать поиск". Далее система проанализирует базу групп ВК и найдет список групп, где есть похожая целевая аудитория. Когда поиск будет закончен, на экране появится ссылка на скачивание Excel-файла.</p>
        </div>
      </div>


      <div class="col-md-6">
        <input class="my-3 form-control" id="group-name" type="text" name="group" placeholder="https://vk.com/apiclub">
        <div data-toggle="tooltip" title="Это наиболее подходящие значения, рекомендованные для сбора групп с похожей аудиторией" class="form-inline justify-content-around d-flex">
          <label for="from">от</label>
          <input class="form-control" id="from" type="text" name="from" value="100">
          <label for="to">до</label>
          <input class="form-control" id="to" type="text" name="to" value="30000">
        </div>
      </div>
    </div>
  </form>
</div>
<div class="table-responsive" id="table-search"></div>
@endsection
