@extends('layouts.app')

@section('title-block', 'Сбор подписчиков групп ВК')

@section('content')

<div class="container">
  <form class="" id="search-submit" action="{{ route('getusers') }}" method="post">
    @csrf
    <h2 class="m-3 text-center text-uppercase" >Собрать базу подписчиков групп вконтакте</h2>
    @include('inc.buttons-and-progress', ['link' => 'getusers', 'button' => 'Собрать подписчиков'])

    <div class="search-form row pb-2">
      <div class="col-md-6 ">

        <div class="">
          <h5 class="text-center">СОВЕТ</h5> <p>Чтобы найти группы для сбора подписчиков,	воспользуйтесь разделом "Группы с открытой стеной". Соберите список групп в Excel-файле, скопируйте список групп с ID или ссылками на	группы. Вставьте его в поле, размещенное на	этой странице в правой части экрана.</p>
        </div>
      </div>

      <div class="col-md-6">
        <textarea class="form-control" name="groups" rows="5" placeholder="Введите ID или ссылки группы (одна или несколько в столбик)
https://vk.com/apiclub
https://vk.com/club1
19043"></textarea>
      </div>
    </div>

    <div class="search-form row pb-5">
      <div class="col-md-8 ">

        <div class="row">
          <div class="col-md-8 bg-secondary text-white text-right p-3">
            <label for="common_info"><h3 class="h5 font-weight-bold text-uppercase">Общая информация*</h3>
              Имя и фамилия, пол, возраст, страна, город <br />
              сейчас онлайн <br />
              "короткий" адрес страницы <br />
              открытый профиль? <br />
              можно постить на стену? <br />
              можно видеть посты на стене? <br />
              можно ли писать в ЛС? <br />
              дата прошлого визита и устройство входа <br />
              <small class="lh-sm d-inline-flex">*Если в профиле пользователя не указана какая-либо информация, профиль всё равно будет включён в список</small></label>
          </div>
          <div class="col-md-4">
              <input class="mt-3 mb-5 form-control" id="common_info" type="checkbox" name="common_info" value="common_info">
          </div>
        </div>

        <div class="row">
          <div class="col-md-8 bg-secondary text-white text-right p-3">
            <label for="site"><h3 class="h5 font-weight-bold text-uppercase">Сайт</h3></label>
          </div>
          <div class="col-md-4">
              <input class="mt-3 mb-5 form-control" id="site" type="checkbox" name="site" value="site">
          </div>
        </div>

        <div class="row">
          <div class="col-md-8 bg-secondary text-white text-right p-3">
            <label for="contacts"><h3 class="h5 font-weight-bold text-uppercase">Контакты</h3></label>
          </div>
          <div class="col-md-4">
              <input class="mt-3 mb-5 form-control" id="contacts" type="checkbox" name="contacts" value="contacts">
          </div>
        </div>

        <div class="row">
          <div class="col-md-8 bg-secondary text-white text-right p-3">
            <label for="social"><h3 class="h5 font-weight-bold text-uppercase">Профили соц-сетей</h3></label>
          </div>
          <div class="col-md-4">
              <input class="mt-3 mb-5 form-control" id="social" type="checkbox" name="social" value="social">
          </div>
        </div>

        <div class="row">
          <div class="col-md-8 bg-secondary text-white text-right p-3">
            <label for="relation"><h3 class="h5 font-weight-bold text-uppercase">Отношения</h3></label>
          </div>
          <div class="col-md-4">
              <input class="mt-3 mb-5 form-control" id="relation" type="checkbox" name="relation" value="relation">
          </div>
        </div>

        <div class="row">
          <div class="col-md-8 bg-secondary text-white text-right p-3">
            <label for="half2"><h3 class="h5 font-weight-bold text-uppercase">Вторая половина</h3><small class="lh-sm d-inline-flex">Данный параметр собирается, если пользователь на своей стене указал с кем состоит в отношениях</small></label>
          </div>
          <div class="col-md-4">
              <input class="mt-3 mb-5 form-control" id="half2" type="checkbox" name="half2" value="half2">
          </div>
        </div>

        <div class="row">
          <div class="col-md-8 bg-secondary text-white text-right p-3">
            <label for="bday"><h3 class="h5 font-weight-bold text-uppercase">День рождения</h3><small class="lh-sm d-inline-flex">Был или будет в ближайшие 14 дней</small></label>
          </div>
          <div class="col-md-4">
              <input class="mt-3 mb-5 form-control" id="bday" type="checkbox" name="bday" value="bday">
          </div>
        </div>

      </div>


      <div class="col-md-4">
        <div class="font-weight-bold text-info h1 float-left px-3 pt-0 m-0">
          1
        </div>
        <div class="font-weight-light mb-3">
          <span class="h6 font-weight-bold">Способ</span><br />
          Если вам нужно собрать только короткие адреса страниц пользователей, вставьте список групп ВК в поле в правой части этой страницы. Далее не отмечая никаких дополнительных критериев, нажмите кнопку «Начать поиск».
        </div>

        <div class="font-weight-bold text-info h1 float-left px-3 pt-0 m-0">
          2
        </div>
        <div class="font-weight-light mb-3">
          <span class="h6 font-weight-bold">Способ</span><br />
          Если вам нужно собрать подписчиков, которые указали в своем профиле: сайт, контакты, профили соц.сетей, отношения, ссылку на партнера или день рождения, вставьте список групп Вк в поле в правой части этой страницы. Далее выберите нужные критерии и нажмите кнопку «Начать поиск».
        </div>

        <div class="font-weight-bold text-info h1 float-left px-3 pt-0 m-0">
          3
        </div>
        <div class="font-weight-light mb-3">
          <span class="h6 font-weight-bold">Способ</span><br />
          Если вас интересует общая информация обо всех пользователях из определенного списка групп, вставьте список групп ВК, в поле в левой части этой страницы. ОБРАТИТЕ ВНИМАНИЕ! Далее выберите критерий «Общая информация» и нажмите кнопку «Начать поиск».
        </div>
        <hr />
        <div class="font-weight-bold text-info h1 float-left px-3 pt-0 m-0">
          4
        </div>
        <div class="font-weight-light mb-3">
          <span class="h6 font-weight-bold">Результаты поиска</span><br />
          После того как система соберет всех подписчиков групп, в верхней части экрана появится ссылка на скачивание Excel таблицы. ВАЖНО! В бесплатной версии система показывает на экране и в файле только 100 подписчиков из общего количества найденных. Чтобы скачать всех найденных подписчиков, оплатите доступ на 3 дня, 1 месяц или 3 месяца. Информацию о тарифах вы можете найти <a href="{{ route('tarifs') }}">здесь</a> или в самом верху экрана.
        </div>
      </div>
    </div>
  </form>
</div>
<div class="table-responsive" id="table-search"></div>


@endsection
