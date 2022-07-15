@extends('layouts.app')

@section('title-block', 'Поиск групп ВК с открытой стеной')
@section('description-block', 'Поиск групп ВК по открытой стене, количеству подписчиков, городу и названию')

@section('content')

<div class="container">
  @include('inc.toast')
  @include('inc.tarif-recall')
  <form class="" id="search-submit" action="{{ route('groupsearch') }}" method="post">
    @csrf
    <h2 itemprop="headline" class="m-3 text-center text-uppercase" >Поиск групп вконтакте</h2>
    @include('inc.buttons-and-progress', ['link' => 'groupsearch', 'button' => 'Найти группы'])

    <div  class="search-form row pb-5">
      <meta itemprop="identifier" content="groupsearch">
      <div class="col-md-8 ">

        <div class="row">
          <div itemprop="articleBody" class="lh-m col-md-8 bg-secondary text-white text-right p-3">
            <label for="name"><span class="h5 d-block font-weight-bold text-uppercase">Название группы</span>
            Введите название группы или его часть в виде ключевого слова для поиска, Если вы хотите найти группы, указавшие город в своем назваии, то вводите название города в этой строке</label>
          </div>
          <div class="col-md-4 form-inline w-100 flex-nowrap align-items-baseline">
              <input class="w-100 mt-3 mb-5 form-control" id="name" type="text" name="name" value="{{ old('name') }}">
              <a class="mx-1 cursor-pointer text-secondary" tabindex="0" data-toggle="popover" data-placement="top" data-trigger="focus" title="Как пользоваться:" data-content="Этот поиск работает по точному соответствию символов в части навания. Здесь не работают звёздочки (*) или автоматические склонения. Пример: если вам нужны результаты, которые включают (работа, работой, работе), то пишите просто 'работ'"><i class="far fa-question-circle"></i></a>
          </div>
        </div>

        <div class="row">
          <div itemprop="articleBody" class="lh-m col-md-8 bg-secondary text-white text-right p-3">
            <label for="city"><span class="h5 d-block font-weight-bold text-uppercase">Город</span>
            Название города нужно вводить, если вы ищете группы, ЗАРЕГИСТРИРОВАННЫЕ в каком-то конкретном городе. Если город - это часть названия группы, при необходимости введите его в строку "название группы", а это поле оставьте пустым</label>
          </div>
          <div class="col-md-4">
              <input class="mt-3 mb-5 form-control" id="city" type="text" name="city" value="{{ old('city') }}">
          </div>
        </div>

        <div class="row">
          <div class="lh-m col-md-8 bg-secondary text-white text-right p-3">
            <label for="members_count_from"><span class="h5 d-block font-weight-bold text-uppercase">Количество подписчиков</span></label>
          </div>
          <div class="col-md-4">
            <div class="row">
              <input class="col mx-2 mt-3 mb-5 form-control" id="members_count_from" type="number" min="0" name="members_count_from" placeholder="от" value="{{ old('members_count_from') }}">
              <input class="col mt-3 mb-5 form-control" id="members_count_to" type="number" min="0" name="members_count_to" placeholder="до" value="{{ old('members_count_to') }}">
            </div>
          </div>
        </div>

        <div class="row">
          <div  class="lh-m col-md-8 bg-secondary text-white text-right p-3">
            <label for="wall"><span class="h5 d-block font-weight-bold text-uppercase">Группы с открытой стеной</span>
            Поиск групп, на стене которых любой пользователь может создавать посты и осталять комментарии</label>
          </div>
          <div class="col-md-4">
              <input class="mt-3 mb-5 form-control" id="wall" type="checkbox" name="wall" value="wall">
          </div>
        </div>

        <div class="row">
          <div  class="lh-m col-md-8 bg-secondary text-white text-right p-3">
            <label for="comments"><span class="h5 d-block font-weight-bold text-uppercase">Группы с открытыми комментариями к постам</span>
            Поиск групп, на стенах которых можно осталять только комментарии</label>
          </div>
          <div class="col-md-4">
              <input class="mt-3 mb-5 form-control" id="comments" type="checkbox" name="comments" value="comments">
          </div>
        </div>

        <div class="row">
          <div  class="lh-m col-md-8 bg-secondary text-white text-right p-3">
            <label for="verify"><span class="h5 d-block font-weight-bold text-uppercase">Верифицированное сообщество</span>
            Это официальные группы известых личностей, музыкантов и компаний, подтвердившие свой статус и помеченные специальным символом "V"</label>
          </div>
          <div class="col-md-4">
              <input class="mt-3 mb-5 form-control" id="verify" type="checkbox" name="verify" value="verify">
          </div>
        </div>

        <div class="row">
          <div  class="lh-m col-md-8 bg-secondary text-white text-right p-3">
            <label for="market"><span class="h5 d-block font-weight-bold text-uppercase">Группы с магазином</span>
            Поиск групп, на стене которых есть раздел с продажей товаров или услуг</label>
          </div>
          <div class="col-md-4">
              <input class="mt-3 mb-5 form-control" id="market" type="checkbox" name="market" value="market">
          </div>
        </div>

        <div class="row">
          <div class="lh-m col-md-8 bg-secondary text-white text-right p-3">
            <label for="open"><span class="h5 d-block font-weight-bold text-uppercase">Открытое сообщество</span>
            Группы, в которые можно вступать всем	</label>
          </div>
          <div class="col-md-4">
              <input class="mt-3 mb-5 form-control" id="open" type="checkbox" name="open" value="open">
          </div>
        </div>

          <div class="row">
            <div class="lh-m col-md-8 bg-secondary text-white text-right p-3">
              <label for="age_18"><span class="h5 d-block font-weight-bold text-uppercase">Группа 18+</span>
              Группы с возрастным ограничением
              @if (!Session::has('user_profile') || strtotime(session('user_profile')['paid_until']) - date('U') < 0)
                <br />Доступно только для <a class="text-warning" href="{{ route('tarifs') }}">платных</a> тарифов
              @endif
              </label>
            </div>
            <div
            @if (!Session::has('user_profile') || strtotime(session('user_profile')['paid_until']) - date('U') < 0)
              data-toggle="tooltip" title="Доступно в платной версии"
            @endif
            class="col-md-4">
                <input class="mt-3 mb-5 form-control"
                @if (!Session::has('user_profile') || strtotime(session('user_profile')['paid_until']) - date('U') < 0)
                  disabled
                @endif
                 id="age_18" type="checkbox" name="age_18" value="age_18">
            </div>
          </div>

      </div>


      <div  class="col-md-4">
        <div class="font-weight-bold text-pink h1 float-left px-3 pt-0 m-0">
          1
        </div>
        <div itemprop="articleBody" class="font-weight-light mb-3">
          <span class="h6 d-block font-weight-bold">Сформулируйте запрос</span>
          Определите ключевое слово и введите его в поле «Название группы», если в названиях групп присутствует название города, то укажите его здесь. Если вам требуется найти группы ВК, указавшие в настройках профиля название города, то введите его в поле «Город», во всех других случаях оставьте это поле пустым.
        </div>

        <div class="font-weight-bold text-pink h1 float-left px-3 pt-0 m-0">
          2
        </div>
        <div  class="font-weight-light mb-3">
          <span class="h6 d-block font-weight-bold">Задайте критерии для поиска групп</span>
          Укажите количество подписчиков от и/или до, поставьте галочки напротив важных для вас характеристик поиска. Если вы хотите собрать все группы по ключевому слову или по принадлежности к городу, то другие поля оставьте пустыми.
        </div>

        <div class="font-weight-bold text-pink h1 float-left px-3 pt-0 m-0">
          3
        </div>
        <div itemscope itemtype="http://schema.org/Article" itemprop="articleBody" class="font-weight-light mb-3">
          <span class="h6 d-block font-weight-bold">Поиск групп</span>
          Нажмите кнопку «Поиск групп». После того как система соберет все группы, в верхней части экрана появится ссылка на скачивание Excel-таблицы, а внизу экрана отобразится первая 1000 групп из файла. ВАЖНО! В бесплатной версии система показывает на экране и в файле только 10 групп из общего количества найденных. Чтобы скачать все группы, оплатите доступ на 3 дня, 1 месяц или 3 месяца. Информацию о тарифах вы можете найти <a href="{{ route('tarifs') }}">здесь</a> или в самом верху экрана. Помните, что для работы данной услуги нужно авторизоваться ВК.
        </div>
      </div>
    </div>
  </form>
</div>
<div class="table-responsive" id="table-search"></div>
@endsection
