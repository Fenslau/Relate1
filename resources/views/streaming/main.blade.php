@extends('layouts.app')

@section('title-block', 'Сбор упоминаний о бренде ВК')

@section('content')

<div class="container-lg">
  <h2 class="m-3 text-center text-uppercase">Собрать и анализировать посты</h2>
    <div class="row">
      <div class="col-md-4">
        <div class="font-weight-bold text-info h1 float-left px-3 pt-0 m-0">
          1
        </div>
        <div class="lh-m font-weight-light mb-3 text-justify">
          <span class="h6 font-weight-bold">Формат правил</span><br />
            Правило — это набор ключевых слов, наличие которых в тексте объекта означает, что объект попадёт в поток. <br />
            • Если слова указаны без двойных кавычек, поиск ведётся с упрощением (все словоформы, без учёта регистра). <br />
            • Для поиска по точному вхождению (с учётом регистра, словоформы и т.п.) каждое слово должно быть указано в двойных кавычках. <br />
            • Минус (-) перед ключевым словом исключит из выборки тексты, содержащие это слово. Правило не может состоять только из ключевых слов с минусом.
        </div>
      </div>

      <div class="col-md-4">
        <div class="font-weight-bold text-info h1 float-left px-3 pt-0 m-0">
          2
        </div>
        <div class="lh-m font-weight-light mb-3 text-justify">
          <span class="h6 font-weight-bold">Пример</span><br />
            Например, правилу кот будут соответствовать объекты с текстом "кот", "кОт", "Котик".
            Правилу "кот" из вышеперечисленных будет соответствовать только объект с текстом "кот".
            Если указать минус-слово -"кот", то правилу будут соответствовать объекты, которые не содержат точную словоформу «кот».
            Правилу с минус-словом -собака будут соответствовать объекты, которые не содержат слово «собака» в любой форме.
            Чтобы искать записи с хэштегом, используйте правило вида "#хэштег".
        </div>
      </div>

      <div class="col-md-4">
        <div class="font-weight-bold text-info h1 float-left px-3 pt-0 m-0">
          3
        </div>
        <div class="lh-m font-weight-light mb-3 text-justify">
          <span class="h6 font-weight-bold">Минус-слова</span><br />
            Всегда указывайте минус-слова. Иначе, будет приходить много постов, вроде-бы с ключевыми словами, но абсолютно не по теме. Мы собрали наиболее актуальные из таких слов-исключений: -кот -собака -щенок -найден -аренда -квартира -комната -работа -вакансия -зарплата -резюме -требуется ... <br />Используйте их в своих правилах и добавляйте другие.
        </div>
      </div>
    </div>

    <div id="begin" class="row justify-content-around align-items-center">

        <a href="{{ route('post', $info['project_name']) }}" class="m-1 btn btn-lg btn-outline-primary"><i class="fab fa-windows"></i> <span class="d-none d-md-inline">Проект {{ $info['project_name'] }}</span></a>

        <form class="m-0" action="{{ route('save-file', $info['project_name']) }}" method="post">
          @csrf
          <input type="hidden" name="query_string" value="{{ serialize($request->all()) }}">
          <button class="m-1 btn btn-lg btn-outline-success enabled position-relative overflow-hidden" type="submit" disabled><i class="far fa-file-excel"></i> <span class="d-none d-md-inline">Скачать файл Excel</span> <a class="popover-label" tabindex="0" data-toggle="popover" data-placement="top" data-trigger="focus" title="Как пользоваться:" data-content="Сначала сформируйте выборку постов (с помощью фильтров или без них), а потом нажмите эту зелёную кнопку — в файле будут все посты из данной выборки."><i class="far fa-question-circle"></i></a></button>
        </form>

        <button class="m-1 btn btn-lg btn-outline-warning" type="submit"><i class="fas fa-history"></i> <span class="d-none d-md-inline">Прошлые посты</span></button>

        <button class="m-1 btn btn-lg btn-outline-info" type="submit"><i class="fas fa-chart-pie"></i> <span class="d-none d-md-inline">Статистика</span></button>

        <input hidden type="checkbox" id="filters" name="filters" {{ $request->stat ? ' checked' : '' }}>
        <label class="m-1 btn btn-lg btn-outline-secondary" for="filters">
          <i class="fas fa-filter"></i> <span class="d-none d-md-inline">Фильтры</span>
        </label>

        <div class="filter-form">
          <form class="bg-light mt-3 border rounded p-3 d-flex flex-wrap align-items-center" method="get">
            <div class="form-group col">
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="man" value="1" {{ $request->man ? ' checked' : '' }} id="men">
                  <label class="form-check-label" for="men">
                    Мужчины
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="woman" value="1" {{ $request->woman ? ' checked' : '' }} id="woman">
                  <label class="form-check-label" for="woman">
                    Женщины
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="group" value="1" {{ $request->group ? ' checked' : '' }} id="group">
                  <label class="form-check-label" for="group">
                    Группы
                  </label>
                </div>
            </div>

            <div class="form-group col">
                <select class="form-control form-control-sm w-auto overflow-auto" name="type[]" size="4" multiple>
                  <option {{ (is_array($request->type) && in_array('comment', $request->type)) ? ' selected' : '' }} value="comment">Комментарии</option>
                  <option {{ (is_array($request->type) && in_array('post', $request->type)) ? ' selected' : '' }} value="post">Посты</option>
                  <option {{ (is_array($request->type) && in_array('share', $request->type)) ? ' selected' : '' }} value="share">Репосты</option>
                  <option {{ (is_array($request->type) && in_array('topic_post', $request->type)) ? ' selected' : '' }} value="topic_post">Обсуждения</option>
                </select>
            </div>

            <div class="form-group col">
              <link rel="stylesheet" href="//code.jquery.com/ui/1.13.0/themes/base/jquery-ui.css">
              <script src="//code.jquery.com/ui/1.13.0/jquery-ui.js"></script>
                <script>
                  $( function() {
                  $( "#slider-range" ).slider({
                    range: true,
                    min: 13,
                    max: 118,
                    values: [ {{ $request->age ? explode(' - ', $request->age)[0] : "13" }}, {{ $request->age ? explode(' - ', $request->age)[1] : "118" }} ],
                    slide: function( event, ui ) {
                    $( "#age" ).val( "" + ui.values[ 0 ] + " - " + ui.values[ 1 ] );
                    }
                  });
                  $( "#age" ).val( "" + $( "#slider-range" ).slider( "values", 0 ) +
                    " - " + $( "#slider-range" ).slider( "values", 1 ) );
                  } );
                 </script>

                <label class="w-100 text-center text-nowrap" for="age">Возраст авторов:</label>
                <input type="text" name="age" id="age" readonly class="text-center form-control">
                <div id="slider-range"></div>
            </div>

            <div class="form-group col">
              Страна: <br />
              <select name="country[]" size="2" multiple data-placeholder="Выберите страну" class="form-control form-control-sm chosen-select">
                @if (!empty($countries))
                  @foreach ($countries as $countrie)
                    <option {{ (is_array($request->country) && in_array($countrie, $request->country)) ? ' selected' : '' }} value ="{{ $countrie }}">{{ $countrie }}</option>
                  @endforeach
                @endif
              </select>

              <br />Город: <br />
              <select name="city[]" size="2" multiple data-placeholder="Выберите город" class="form-control form-control-sm chosen-select">
                @if (!empty($cities))
                  @foreach ($cities as $city)
                    <option {{ (is_array($request->city) && in_array($city, $request->city)) ? ' selected' : '' }} value ="{{ $city }}">{{ $city }}</option>
                  @endforeach
                @endif
              </select>
              <div class="form-check">
                <input class="form-check-input" id="in_region" type="checkbox" name="in_region" {{ $request->in_region ? ' checked' : '' }}>
                <label class="form-check-label" for="in_region"><small class="text-nowrap">включая регион или область</small></label>
              </div>

              Исключая: <br />
              <select name="not_city[]" size="2" multiple data-placeholder="Выберите город" class="form-control form-control-sm chosen-select">
                @if (!empty($cities))
                  @foreach ($cities as $city)
                    <option {{ (is_array($request->not_city) && in_array($city, $request->not_city)) ? ' selected' : '' }} value ="{{ $city }}">{{ $city }}</option>
                  @endforeach
                @endif
              </select>
              <div class="form-check">
                <input class="form-check-input" id="in_region_not" type="checkbox" name="in_region_not" {{ $request->in_region_not ? ' checked' : '' }}>
                <label class="form-check-label" for="in_region_not"><small class="text-nowrap">включая регион или область</small></label>
              </div>
            </div>

            <div class="form-group col">
              Подписчиков от
              <input class="form-control form-control-sm" type="text" name="followers_from" value="{{ $request->followers_from }}">
              до <input class="form-control form-control-sm" type="text" name="followers_to" value="{{ $request->followers_to }}">
            </div>

            <div class="form-group col">
              Дата от
              <input class="form-control form-control-sm" type="date" name="calendar_from" value="{{ $request->calendar_from }}"	max="{{ $maxdate }}" min="{{ $mindate }}">
              до <input class="form-control form-control-sm" type="date" name="calendar_to" value="{{ $request->calendar_to }}" max="{{ $maxdate }}" min="{{ $mindate }}">
            </div>

            <div class="form-group col">
              Пользовательские папки
              <div class="form-check d-table" data-toggle="tooltip" title="В выдаче будут все посты">
                <input class="form-check-input" name="user_links" type="radio" value="all" id="all" {{ $request->user_links == 'all' ? ' checked' : '' }}>
                <label class="form-check-label" for="all">
                  Все
                </label>
              </div>
              <div class="form-check d-table" data-toggle="tooltip" title="Только посты из пользовательских папок">
                <input class="form-check-input" name="user_links" type="radio" value="on" id="on" {{ $request->user_links == 'on' ? ' checked' : '' }}>
                <label class="form-check-label" for="on">
                  Вкл.
                </label>
              </div>
              <div class="form-check d-table" data-toggle="tooltip" title="Только посты НЕ из пользовательских папок">
                <input class="form-check-input" name="user_links" type="radio" value="off" id="off" {{ $request->user_links == 'off' ? ' checked' : '' }}>
                <label class="form-check-label" for="off">
                  Выкл.
                </label>
              </div>
            </div>

            <div class="form-group col">
                <input type="hidden" name="stat" value="1">
                <input type="hidden" name="rule" value="{{ $request->rule }}">
                <input class="my-2 d-inline-block btn btn-sm btn-primary vk-top-bg text-white" type="submit" name="apply_filter" value = "Показать записи">
                <input class="my-2 d-inline-block btn btn-sm btn-info" type="submit" name="apply_filter" value = "Собрать авторов">
                <p class="lh-md"><small>Фильтры применяются либо ко всему проекту, либо к отдельному правилу (смотря где вы находитесь, когда их включаете), за исключением папки "Доп.посты". Записи оттуда не входят ни в какие выборки</small></p>
            </div>
          </form>
        </div>
    </div>
</div>

<div class="my-3 container-xl">

  <div class="row">
    <div class="col-md-9">

      @include('inc.toast')


      <nav aria-label="breadcrumb">
          <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('stream') }}">Главная</a></li>
            @isset($info['project_name'])
            <li class="breadcrumb-item"><a href="{{ route('post', $info['project_name']) }}">{{ $info['project_name'] }}</a></li>
            @endisset
            @isset($info['rule'])
            <li class="breadcrumb-item active" aria-current="page">{{ $info['rule'] }}</li>
            @endisset
          </ol>
      </nav>


      @include('inc.posts')

    </div>
    <div class="col-md-3">
      @include('inc.aside')
    </div>
  </div>

</div>

<div class="mb-5 pb-5">

</div>
<section>
    @include('inc.modal-aside')
</section>
<script type="text/javascript">
    if (window.location.hash) {}
    else
    $('#start') [0].scrollIntoView (1)
</script>
<script src="/js/post.js" type="text/javascript"></script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.js" type="text/javascript"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.proto.js" type="text/javascript"></script>



@endsection
