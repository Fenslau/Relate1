@extends('layouts.app')

@section('title-block', 'Сбор упоминаний о бренде ВК')

@section('content')

<div class="container-lg">
  <h2 class="m-3 text-center text-uppercase">Собрать и анализировать посты</h2>
    <div class="row">
      <div class="col-md-4">
        <div class="font-weight-bold text-pink h1 float-left px-3 pt-0 m-0">
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
        <div class="font-weight-bold text-pink h1 float-left px-3 pt-0 m-0">
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
        <div class="font-weight-bold text-pink h1 float-left px-3 pt-0 m-0">
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


          <button class="m-1 btn btn-lg btn-outline-success no-outline" data-toggle="popover-file"><i class="far fa-file-excel"></i> <span class="d-none d-md-inline">Excel</span></button>
          <script>
            $('body').on('click', '#file_submit', function(e) {
              e.preventDefault();
              var query_string = $('#query_request').attr('query_string');
              var form = $('<form></form>');

              form.attr("method", "post");
              form.attr("action", "{{ route('save-file', $info['project_name']) }}");
                  var field1 = $('<input></input>');
                  var field2 = $('<input></input>');
                  field1.attr("type", "hidden");
                  field1.attr("name", "query_string");
                  field1.attr("value", query_string);
                  form.append(field1);
                  field2.attr("type", "hidden");
                  field2.attr("name", "_token");
                  field2.attr("value", $('meta[name="csrf-token"]').attr('content'));
                  form.append(field2);
              $(document.body).append(form);
              form.submit();
            });
            $(function () {
                $('[data-toggle="popover-file"]').popover({
                container: 'body',
                html: true,
                placement: 'top',
                sanitize: false,
                title: `Что это и как пользоваться:`,
                content:
                `<form class="m-0" action="{{ route('save-file', $info['project_name']) }}" method="post">
                  @csrf
                  <input id="query_file" type="hidden" name="query_string" value="{{ serialize($request->all()) }}">
                  <p>Сначала сформируйте выборку постов (с помощью фильтров или без них), а потом нажмите эту зелёную кнопку — в файле будут все посты из данной выборки (со всех страниц)</p>
                  <input id="file_submit" class="btn btn-sm btn-success" type="submit" value="Получить файл">
                </form>`
                })
            });
          </script>


        <button class="m-1 btn btn-lg btn-outline-warning no-outline" data-toggle="popover-old"><i class="fas fa-history"></i> <span class="d-none d-md-inline">Прошлые посты</span></button>
        <script>
          $(function () {
              $('[data-toggle="popover-old"]').popover({
              container: 'body',
              html: true,
              placement: 'top',
              sanitize: false,
              title: `Что это и как пользоваться:`,
              content:
              `<form action="{{ route('old-post', $info['project_name']) }}" method="post">
                @csrf
                <p>Это посты, которые существовали до момента добавления правила. Посты начнут собираться после нажатия кнопки. Они будут появляться на последних страницах вашего правила или проекта. Если постов будет много, это может занять некоторое время, и количество страниц будет постоянно увеличиваться. Укажите дату в прошлом до которой вы хотите собрать посты</p>
                <div class="input-group">
                  <input class="form-control form-control-sm border border-warning" type="date" name="end_date" max="{{ $mindate }}" value="{{ $mindate }}">
                  <input type="hidden" name="rule" value="{{ $request->rule }}">
                  <input type="hidden" name="start_date" value="{{ $mindate }}">
                  <div class="input-group-append">
                    <input class="btn btn-sm btn-warning" type="submit" name="get_old" value="Начать сбор">
                  </div>
                </div>
                @isset($info['get_old'])
                  <div class="input-group">
                    <input class="w-100 mt-2 btn btn-sm btn-outline-danger" type="submit" name="get_old_stop" value="Остановить сбор">
                  </div>
                @endisset
              </form>`
              })
          });
        </script>

        <form class="m-0" action="{{ route('statistic', $info['project_name']) }}" method="post">
            @csrf
            <input type="hidden" name="query_string" value="{{ serialize($request->all()) }}">
            <button disabled query_string="{{ serialize($request->all()) }}" class="m-1 btn btn-lg btn-outline-info statistic" type="submit"><i class="icon fas fa-chart-pie"></i><span style="width: 1.2rem; height: 1.2rem" class="mb-1 spinner-border spinner-border-sm d-none"></span> <span class="d-none d-md-inline">Статистика</span></button>
            <script>
                $(document).ready( function () {
                  $(".statistic").click(function() {
                    _this = $(this);
                    var query_string = $('#query_request').attr('query_string');
                    $.ajax({
                      headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                      type: 'POST',
                      url: '{{ route('statistic', $info['project_name']) }}',
                      data: {'query_string' : query_string},
                      beforeSend: function () {
                                _this
                                .prop('disabled', true)
                                .find('.icon').addClass('d-none');
                                _this.find('.spinner-border-sm').removeClass('d-none');
                                $('#progress-text').removeClass('d-none');
                                $('#progress').removeClass('d-none');
                                var answer = 0;
                                var zero_answer = 0;
                                var response = 0;
                                        var elem = document.getElementById("progress");
                                        var elem2 = document.getElementById("progress-text");
                        				var width_old = -1;
                                        var width = 0;
                                        var info = '';
                                        var id = setInterval(frame, 500);
                                       async function frame() {
                                           let user = {
                                             vkid: {{ session('vkid') }},
                                             process: 'stream'
                                           };
                                             answer = await fetch("/progress", {
                                               method: 'POST',
                                               headers: {
                                                 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                                                 'Content-Type': 'application/json;charset=utf-8'
                                               },
                                               body: JSON.stringify(user)
                                             }),
                                             response = await answer.json();
                        					 width = response.width;
                                   info = response.info;
                        					 if (width >= width_old || width == 0) {
                        						 elem.style.width = response.width + '%';
                        						 elem.innerHTML = Math.floor(response.width) * 1  + '%';
                        						 elem2.innerHTML = response.info;
                                             }
                        					 width_old = response.width;
                        					 if (width == 0 && info == '') zero_answer++; if (zero_answer > 10) {
                                                clearInterval(id);
                                              }
                                       }
                      },
                      success: function(data){
                        if (data.success) {
                            _this
                            .prop('disabled', false)
                            .find('.icon').removeClass('d-none');
                            _this.find('.spinner-border-sm').addClass('d-none');
                          $(".main").html(data.html);
                        } else {
                          $('.toast-header').addClass('bg-danger');
                          $('.toast-header').removeClass('bg-success');
                          $('.toast-body').html('Что-то пошло не так. Попробуйте ещё раз или сообщите нам');
                          $('.toast').toast('show');
                        }}
                    });
                  });
                } );
            </script>
        </form>
        <input hidden type="checkbox" id="filters" name="filters" {{ $request->stat ? ' checked' : '' }}>
        <label class="m-1 btn btn-lg btn-outline-secondary" for="filters">
          <i class="fas fa-filter"></i> <span class="d-none d-md-inline">Фильтры</span>
        </label>

        <div class="filter-form">
          <form class="bg-light mt-3 border rounded p-3 d-flex flex-wrap align-items-center filter-form" method="get">
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
                  @foreach ($cities as $city_id => $city)
                    <option {{ (is_array($request->city) && in_array($city_id, $request->city)) ? ' selected' : '' }} value ="{{ $city_id }}">{{ $city }}</option>
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
                  @foreach ($cities as $city_id => $city)
                    <option {{ (is_array($request->not_city) && in_array($city_id, $request->not_city)) ? ' selected' : '' }} value ="{{ $city_id }}">{{ $city }}</option>
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
              <input class="form-control form-control-sm" type="number" min="0" name="followers_from" value="{{ $request->followers_from }}">
              до <input class="form-control form-control-sm" type="number" min="0" name="followers_to" value="{{ $request->followers_to }}">
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
                <button class="my-2 d-inline-block btn btn-sm btn-primary vk-top-bg text-white filter-form-submit text-nowrap" type="submit" name="apply_filter" value = "Показать записи"><i class="icon far fa-newspaper"></i><span class="spinner-border spinner-border-sm d-none"></span> Показать посты</button>
                <button class="my-2 d-inline-block btn btn-sm btn-info filter-form-submit text-nowrap" type="submit" name="apply_filter" value = "Собрать авторов"><i class="icon fas fa-user-edit"></i><span class="spinner-border spinner-border-sm d-none"></span> Авторов</button>
                <p class="lh-md"><small>Фильтры применяются либо ко всему проекту, либо к отдельному правилу (смотря где вы находитесь, когда их включаете), за исключением папки "Доп.посты". Записи оттуда не входят ни в какие выборки</small></p>
            </div>
          </form>
        </div>
    </div>

    <div class="d-none mt-2 progress">
      <div id="progress" class="progress-bar progress-bar-striped progress-bar-animated" style="width: 0%"></div>
    </div>
    <div id="progress-text" class="d-none px-2 my-2 bg-secondary text-white"></div>

</div>

<div class="my-3 container-xl main">

  <div class="row">
    <div class="col-md-9">

      @include('inc.toast')

      <div id="posts" class="">
        @include('inc.posts')
      </div>

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
<script>
    if (window.location.hash) {}
    else
    $('#start') [0].scrollIntoView (1)

    $(document).ready(function () {
      $(".chosen-select").chosen();
    });
</script>
<script type="text/javascript">
  $(document).ready(function () {
    $(":submit:not(.enabled)").prop('disabled', false);
  });
</script>
<script src="{{ mix('/js/post.js') }}"></script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.jquery.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/chosen/1.8.7/chosen.proto.js"></script>

<script src="//code.highcharts.com/highcharts.src.js"></script>
<script src="//code.highcharts.com/modules/data.src.js"></script>
<script src="//code.highcharts.com/modules/wordcloud.src.js"></script>
<script src="//code.highcharts.com/modules/drilldown.src.js"></script>
<script src="//code.highcharts.com/modules/annotations.src.js"></script>
<script src="//code.highcharts.com/modules/exporting.src.js"></script>
<script src="//code.highcharts.com/modules/export-data.src.js"></script>
<script src="//code.highcharts.com/modules/accessibility.src.js"></script>
<script src="//code.highcharts.com/maps/modules/map.js"></script>
<script src="//code.highcharts.com/mapdata/custom/world-robinson-highres.js"></script>

@endsection
