@extends('layouts.app')

@section('title-block', 'Группы ВК с похожей ЦА')
@section('description-block', 'В каких еще группах сидит ваша целевая аудитория? Поиск групп похожих на заданные')

@section('content')

<div class="container">
  @include('inc.toast')
  @include('inc.tarif-recall')
  <form class="" id="search-submit" action="{{ route('auditoria') }}" method="post">
    @csrf
    <h2 class="m-3 text-center text-uppercase" >Группы с похожей целевой аудиторией</h2>
    @include('inc.buttons-and-progress', ['link' => 'auditoria', 'button' => 'Найти похожие группы'])

    <div class="search-form row pb-5">
      <div class="col-md-6 ">

        <div class="">
          <p>Введите <b>ссылку на группу</b> из ВК или <b>ID группы</b>, в которой есть ваша целевая аудитория, после чего нажмите кнопку "Начать поиск". Далее система проанализирует базу групп ВК и найдет список групп, где есть похожая целевая аудитория. Когда поиск будет закончен, на экране появится ссылка на скачивание Excel-файла. <br /><b>Важно:</b> Если вы указываете группу больше 30 000 подписчиков, то время поиска может увеличиться до 2,5 часов и более. Это происходит, т.к. Вконтакте ограничивает скорость передачи данных. <b>Рекомендуется указывать группы не более 30 000 подписчиков, для более точного и уникального результата.</b></p>
        </div>
      </div>


      <div class="col-md-6">
        <input class="my-3 form-control" id="group-name" type="text" name="group" placeholder="https://vk.com/apiclub">
        <p class="text-center">Искать сообщества с количеством подписчиков:</p>
        <div data-toggle="tooltip" title="Это наиболее подходящие значения, рекомендованные для сбора групп с похожей аудиторией" class="form-inline justify-content-around d-flex">

          <label for="from">от</label>
          <input class="form-control" id="from" type="number" min="0" max="100000" name="from" value="100">
          <label for="to">до</label>
          <input class="form-control" id="to" type="number" min="0" max="100000" name="to" value="30000">
        </div>
      </div>
    </div>
  </form>
</div>
<div class="table-responsive" id="table-search"></div>
@endsection
