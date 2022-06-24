@extends('layouts.app')

@section('title-block', 'Поиск ТОП-групп ВК')
@section('description-block', 'Топ группы ВК + поиск по группам из разных городов. Поиск групп ВК по открытой стене. Статистика посетителей групп ВКонтакте')

@section('content')

<div class="container-fluid">
  @include('inc.toast')
  <div class="row">

    @if(Session::has('vkid'))
    <script>
      var vkid={{ session('vkid') }};
      var url='{{ route('search') }}';
      var process1='simple_search';
    </script>

    <div class="m-3 p-2 border border-primary">
      <form class="needs-validation" id="search-submit" action="{{ route('search') }}" method="post">
        @csrf
          <div class="form-inline">
            <label class="" for="city">Город</label>
            <input data-toggle="tooltip" title="Если название города должно содержаться в названии группы, то вводите его в названии группы, а это поле оставьте пустым. Это поле для поиска групп, принадлежащих только определенному городу." class="form-control form-control-sm" type="text" name="city" id="city" value="{{ old('city') }}">
          </div>
          <div class="form-inline">
            <label class="" for="group-name">Часть названия группы</label>
            <input class="form-control form-control-sm" type="text" name="group_name" id="group-name" required value="{{ old('group_name') }}">
          </div>
          <div class="form-inline">
            <label class="col-form-label" for="sort">Сортировать по</label>
            <select class="form-control form-control-sm" name="sort" id="sort">
              <option value="0">умолчанию (аналогично результатам поиска vk.com)</option>
              <option value="1">скорости роста</option>
              <option value="2">отношению дневной посещаемости к количеству пользователе</option>
              <option value="3">отношению количества лайков к количеству пользователей</option>
              <option value="4">отношению количества комментариев к количеству пользователей</option>
              <option value="5">отношению количества записей в обсуждениях к количеству пользователей</option>
              <option selected value="6">количеству участников</option>
            </select>
          </div>
          <div class="form-inline">
            <div class="form-check form-check-inline" data-toggle="tooltip" title="Будет собираться дольше">
              <input class="form-check-input" name="stat" type="checkbox" id="inlineCheckbox1" value="stat" {{ (is_array(old('stat')) and in_array(1, old('stat'))) ? ' checked' : '' }}>
              <label class="form-check-label" for="inlineCheckbox1">Со статистикой</label>
            </div>
            <div class="form-check form-check-inline" data-toggle="tooltip" title="Ещё дольше!">
              <input class="form-check-input" name="date" type="checkbox" id="inlineCheckbox2" value="date" {{ (is_array(old('date')) and in_array(1, old('date'))) ? ' checked' : '' }}>
              <label class="form-check-label" for="inlineCheckbox2">С датами последних постов</label>
            </div>

            <button id="js-load" disabled class="btn btn-sm btn-primary vk-top-bg" type="submit" name="submit"><i class="fa fa-search"></i><span class="spinner-border spinner-border-sm text-light d-none"></span> Найти группы</button>
          </div>
      </form>

      <div class="mt-2 progress">
        <div id="progress" class="progress-bar progress-bar-striped progress-bar-animated" style="width: 0%"></div>
      </div>
      <div id="progress-text" class="px-2 mt-2 bg-secondary text-white"></div>
    </div>
    <script type="text/javascript">
  		$(document).ready(function () {
  		  $(":submit:not(.enabled)").prop('disabled', false);
  		});
	  </script>
    @else
    <div class="w-100 alert alert-warning">
      <strong>Зарегистрируйтесь или авторизуйтесь через ВК, чтобы протестировать возможности в демо-режиме или получить полный доступ к ресурсам сайта.</strong>
    </div>
    @endif
    <div class="table-responsive position-relative" id="table-search">

    @include('layouts.home-ajax')

    </div>
  </div>
</div>

<div class="container-fluid bg-dark text-white py-2 px-5">
  <p class="mb-0"><a target="_blank" class="text-white" href="https://sphere-market.ru"> Маркетинговое агентство "СФЕРА"</a></p>
</div>
@endsection
