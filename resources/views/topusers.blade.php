@extends('layouts.app')

@section('title-block', 'Топ пользователей ВК')
@section('description-block', 'Популярные люди, лидеры мнений, топ ВК по количеству подписчиков')

@section('content')

<div class="container">
  @include('inc.toast')
  @include('inc.tarif-recall')
  <form class="" id="search-submit" action="{{ route('topusers') }}" method="post">
    @csrf
    <h2 itemprop="headline" class="m-3 text-center text-uppercase" >Топ пользователей ВК</h2>
    @include('inc.buttons-and-progress', ['link' => 'topusers', 'button' => 'Найти'])

    <div  class="search-form row pb-5">
      <meta itemprop="identifier" content="topusers">
      <div class="col-md-8 ">

        <div class="row">
          <div itemprop="articleBody" class="lh-m col-md-8 bg-secondary text-white text-right p-3">
            <label for="name"><span class="h5 d-block font-weight-bold text-uppercase">Имя</span>
            Введите имя, фамилию или её часть в виде ключевого слова для поиска</label>
          </div>
          <div class="col-md-4 form-inline w-100 flex-nowrap align-items-baseline">
              <input class="w-100 mt-3 mb-5 form-control" id="name" type="text" name="name" value="{{ old('name') }}">
          </div>
        </div>

        <div class="row">
          <div class="lh-m col-md-8 bg-secondary text-white text-right p-3">
            <label for="keyword"><span class="h5 d-block font-weight-bold text-uppercase">Ключевое слово</span>
            По этому слову будет вестись поиск среди таких полей профиля, как деятельность, место работы, о себе</label>
          </div>
          <div class="col-md-4 form-inline w-100 flex-nowrap align-items-baseline">
              <input class="w-100 mt-3 mb-5 form-control" id="keyword" type="text" name="keyword" value="{{ old('keyword') }}">
          </div>
        </div>

        <div class="row">
          <div itemprop="articleBody" class="lh-m col-md-8 bg-secondary text-white text-right p-3">
            <label for="city"><span class="h5 d-block font-weight-bold text-uppercase">Город</span>
            Название города, где вы хотите искать популярных людей</label>
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
            <label for="sex"><span class="h5 d-block font-weight-bold text-uppercase">Пол</span>
            </label>
          </div>
          <div class="col-md-4">
              <input class="mt-3 mb-5 form-control" id="sex" type="checkbox" name="sex" value="sex">
          </div>
        </div>

        <div class="row">
          <div  class="lh-m col-md-8 bg-secondary text-white text-right p-3">
            <label for="can_write_private_message"><span class="h5 d-block font-weight-bold text-uppercase">Можно написать ЛС</span>
            </label>
          </div>
          <div class="col-md-4">
              <input class="mt-3 mb-5 form-control" id="can_write_private_message" type="checkbox" name="can_write_private_message" value="can_write_private_message">
          </div>
        </div>

        <div class="row">
          <div  class="lh-m col-md-8 bg-secondary text-white text-right p-3">
            <label for="can_send_friend_request"><span class="h5 d-block font-weight-bold text-uppercase">Можно позвать в друзья</span>
            </label>
          </div>
          <div class="col-md-4">
              <input class="mt-3 mb-5 form-control" id="can_send_friend_request" type="checkbox" name="can_send_friend_request" value="can_send_friend_request">
          </div>
        </div>

        <div class="row">
          <div class="lh-m col-md-8 bg-secondary text-white text-right p-3">
            <label for="can_post"><span class="h5 d-block font-weight-bold text-uppercase">Можно постить</span>
            Страницы, на стене которых можно разместить свой пост</label>
          </div>
          <div class="col-md-4">
              <input class="mt-3 mb-5 form-control" id="can_post" type="checkbox" name="can_post" value="can_post">
          </div>
        </div>

        <div class="row">
          <div  class="lh-m col-md-8 bg-secondary text-white text-right p-3">
            <label for="verify"><span class="h5 d-block font-weight-bold text-uppercase">Верифицированный пользователь</span>
            Это официальные страницы известых личностей, подтвердившие свой статус и помеченные специальным символом "V"</label>
          </div>
          <div class="col-md-4">
              <input class="mt-3 mb-5 form-control" id="verify" type="checkbox" name="verify" value="verify">
          </div>
        </div>

        <div class="row">
          <div  class="lh-m col-md-8 bg-secondary text-white text-right p-3">
            <label for="status"><span class="h5 d-block font-weight-bold text-uppercase">Отношения</span>
            Если пользователь указал, в каких отношениях он состоит, то этот пункт поможет выбрать нужных</label>
          </div>
          <div class="col-md-4">
            <select id="status" class="form-control form-control-sm w-auto overflow-auto" name="status[]" size="8" multiple>
              <option value="1">не женат (не замужем)</option>
              <option value="2">встречается</option>
              <option value="3">помолвлен(-а)</option>
              <option value="4">женат (замужем)</option>
              <option value="5">всё сложно</option>
              <option value="6">в активном поиске</option>
              <option value="7">влюблен(-а)</option>
              <option value="8">в гражданском браке</option>
            </select>
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
