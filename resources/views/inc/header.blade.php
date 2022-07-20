<header>
@include('inc.auth')

<div class="pb-2 container-fluid vk-top-bg text-white">

    <div class="d-flex justify-content-around">
      <div class="mr-auto p-2">
        <a href="{{ route('home') }}"><img class="img-fluid" alt="Logo Toppost" src="/images/logo.jpg"></a>
        <div class="text-uppercase lh-sm mt-n2 text-right">
          <small><small><a class="text-white" href="mailto:vktoppost@sphere-market.ru">vktoppost@sphere-market.ru</a><br />
          техподдержка</small></small>
        </div>
      </div>
      <div class="p-2 mt-4 text-center">
        <h1 class="h3 text-uppercase">МОНИТОРИНГ И АНАЛИЗ СОЦСЕТИ ВКОНТАКТЕ</h1>
        <p>TOPPOST — это площадка широких возможностей для тех, кто хочет лучше понимать свою аудиторию в ВК</p>
      </div>
    </div>



    <nav class="d-sm-none navbar navbar-light">

      <a class="navbar-brand text-white" href="#">Меню</a>
      <button class="navbar-toggler toggler-example text-white" type="button" data-toggle="collapse" data-target="#navbarSupportedContent1"><i class="fas fa-bars fa-1x"></i></button>

      <div class="collapse navbar-collapse" id="navbarSupportedContent1">
        <ul class="navbar-nav mr-auto">

          <li class="nav-item bg-white rounded px-2 my-1">
            <a class="nav-link" href="{{ route('home') }}">Топ-1000 групп ВК</a>
          </li>
          <li class="nav-item bg-white rounded px-2 my-1">
            <a class="nav-link" href="{{ route('toppost') }}">Самые обсуждаемые посты</a>
          </li>
          <li class="nav-item bg-white rounded px-2 my-1">
            <a class="nav-link" href="{{ route('topusers') }}">Топ пользователей ВК</a>
          </li>

          <li class="nav-item bg-white rounded px-2 my-1">
            <a class="nav-link" href="{{ route('groupsearch') }}">Группы с&nbsp;открытой стеной</a>
          </li>
          <li class="nav-item bg-white rounded px-2 my-1">
            <a class="nav-link" href="{{ route('auditoria') }}">Группы с&nbsp;похожей ЦА</a>
          </li>
          <li class="nav-item bg-white rounded px-2 my-1">
            <a class="nav-link" href="{{ route('getusers') }}">Собрать&nbsp;базу подписчиков групп</a>
          </li>
          <li class="nav-item bg-white rounded px-2 my-1">
            <a class="nav-link" href="{{ route('new-users') }}">Мониторинг новых&nbsp;подписчиков в группах</a>
          </li>
          <li class="nav-item bg-white rounded px-2 my-1">
            <a class="nav-link" href="{{ route('stream') }}">Собрать и&nbsp;анализировать посты</a>
          </li>
        </ul>
      </div>
    </nav>



    <nav class="d-none d-sm-flex justify-content-around align-items-start navbar">

      <div class="m-1 service">
        <a class="nav-link bg-white rounded font-weight-bolder vk-top-color lh-m text-center text-uppercase" href="{{ route('home') }}">Топ-1000 групп ВК</a>
        <div class="d-none d-md-block lh-sm"><small><small>количество подписчиков; <br />охват; <br />вовлеченность;</small></small></div>
      </div>
      <div class="m-1 service">
        <a class="nav-link bg-white rounded font-weight-bolder vk-top-color lh-m text-center text-uppercase" href="{{ route('toppost') }}">Самые обсуждаемые посты</a>
        <div class="d-none d-md-block text-center lh-sm"><small><small>из ТОП-1000 групп ВК</small></small></div>
      </div>
      <div class="m-1 service">
        <a class="nav-link bg-white rounded font-weight-bolder vk-top-color lh-m text-center text-uppercase" href="{{ route('topusers') }}">Топ пользователей ВК</a>
        <div class="d-none d-md-block text-center lh-sm"><small><small>по количеству подписчиков</small></small></div>
      </div>

      <div class="m-1 service">
        <a class="nav-link bg-white rounded font-weight-bolder vk-top-color lh-m text-center text-uppercase" href="{{ route('groupsearch') }}">Группы с&nbsp;открытой стеной</a>
        <div class="d-none d-md-block lh-sm"><small><small>Здесь можно найти группы:
          <ul style="padding-left: 1rem;">
            <li>по названию;</li>
            <li>количеству подписчиков;</li>
            <li>группы 18+;</li>
            <li>верифицированные сообщества;</li>
            <li>и по другим фильтрам</li>
          </ul>
        </small></small></div>
      </div>
      <div class="m-1 service">
        <a class="nav-link bg-white rounded font-weight-bolder vk-top-color lh-m text-center text-uppercase" href="{{ route('auditoria') }}">Группы с&nbsp;похожей ЦА</a>
        <p class="d-none d-md-block lh-sm"><small><small>поиск групп с целевой аудиторией, которая интересуется нужной вам темой</small></small></p>
      </div>
      <div class="m-1 service">
        <a class="nav-link bg-white rounded font-weight-bolder vk-top-color lh-m text-center text-uppercase" href="{{ route('getusers') }}">Собрать&nbsp;базу подписчиков групп</a>
        <div class="d-none d-md-block lh-sm"><small><small>Здесь можно собрать пописчиков групп:
          <ul style="padding-left: 1rem;">
            <li>по основным фильтрам (имя, пол, возраст, ID и др.);</li>
            <li>по дополнительным (сайт, отношения, день рождения, соцсети и др.);</li>
          </ul>
        </small></small></div>
      </div>
      <div class="m-1 service">
        <a class="nav-link bg-white rounded font-weight-bolder vk-top-color lh-m text-center text-uppercase" href="{{ route('new-users') }}">Мониторинг новых&nbsp;подписчиков в группах</a>
        <p class="d-none d-md-block lh-sm"><small><small>инструмент, предназначенный наблюдать за ростом конкретной группы и видеть кто вступил в нее</small></small></p>
      </div>
      <div class="m-1 service">
        <a class="nav-link bg-white rounded font-weight-bolder vk-top-color lh-m text-center text-uppercase" href="{{ route('stream') }}">Собрать и&nbsp;анализировать посты</a>
        <div class="d-none d-md-block lh-sm"><small><small>Возможности ресурса:
          <ul style="padding-left: 1rem;">
            <li>найти посты в ВК по ключевым словам;</li>
            <li>следить за репутацией бренда/компании;</li>
            <li>проводить маркетинговые исследования;</li>
            <li>лучше узнать свою аудиторию</li>
          </ul>
        </small></small></div>
      </div>
    </nav>
    <script>
       $('document').ready(function() {
          $('.service a').each(function() {
              if ($(this).attr('href') == window.location.href || $(this).attr('href')+'/' == window.location.href)
              {
                  $(this).addClass('active');
              }
          });
      });
    </script>

    <div class="container">
      <form action="opros" method="post" autocomplete="off">
        @csrf
        <div class="input-group input-group-append">
          <input type="hidden" name="user" value="user">
          <input class="form-control form-control-sm" type="text" name="opros"
            placeholder="ОПРОС: Какие функции нужно добавить на сайт, чтоб он стал для вас более удобным?">
            <div class="input-group-append">
              <button class="btn btn-sm btn-outline-primary text-white" type="submit">Отправить</button>
            </div>
        </div>
      </form>
    </div>
</div>
<div id="start">

</div>
</header>
