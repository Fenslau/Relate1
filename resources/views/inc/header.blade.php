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
        <p>TOPPOST — площадка для smm-специалистов и всех, кто работает с группами и подписчиками в соцсетях.</p>
      </div>
    </div>



    <nav class="d-sm-none navbar navbar-light">

      <a class="navbar-brand text-white" href="#">Меню</a>
      <button class="navbar-toggler toggler-example text-white" type="button" data-toggle="collapse" data-target="#navbarSupportedContent1"><i class="fas fa-bars fa-1x"></i></button>

      <div class="collapse navbar-collapse" id="navbarSupportedContent1">
        <ul class="navbar-nav mr-auto">

          <li class="nav-item bg-white rounded px-2 my-1">
            <a class="nav-link" href="{{ route('toppost') }}">Самые обсуждаемые посты</a>
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
        <a class="nav-link bg-white rounded font-weight-bolder vk-top-color lh-m text-center text-uppercase" href="{{ route('toppost') }}">Самые обсуждаемые посты</a>
        <div class="d-none d-md-block text-center lh-sm"><small><small>из ТОП-1000 групп ВК</small></small></div>
      </div>

      <div class="m-1 service">
        <a class="nav-link bg-white rounded font-weight-bolder vk-top-color lh-m text-center text-uppercase" href="{{ route('groupsearch') }}">Группы с&nbsp;открытой стеной</a>
        <div class="d-none d-md-block text-center lh-sm"><small><small>поможет вам найти группы ВК по различным критериям (город, название, количество подписчиков и пр.) и скачать в файле</small></small></div>
      </div>
      <div class="m-1 service">
        <a class="nav-link bg-white rounded font-weight-bolder vk-top-color lh-m text-center text-uppercase" href="{{ route('auditoria') }}">Группы с&nbsp;похожей ЦА</a>
        <p class="d-none d-md-block text-center lh-sm"><small><small>поиск групп с целевой аудиторией, которая интересуется нужной вам темой</small></small></p>
      </div>
      <div class="m-1 service">
        <a class="nav-link bg-white rounded font-weight-bolder vk-top-color lh-m text-center text-uppercase" href="{{ route('getusers') }}">Собрать&nbsp;базу подписчиков групп</a>
        <p class="d-none d-md-block text-center lh-sm"><small><small>поможет найти подписчиков в группах по важным для вас характеристикам (пол, возраст, открытый профиль и пр.) и скачать их в файле</small></small></p>
      </div>
      <div class="m-1 service">
        <a class="nav-link bg-white rounded font-weight-bolder vk-top-color lh-m text-center text-uppercase" href="{{ route('new-users') }}">Мониторинг новых&nbsp;подписчиков в группах</a>
        <p class="d-none d-md-block text-center lh-sm"><small><small>инструмент, предназначенный наблюдать за ростом конкретной группы и видеть кто вступил в нее</small></small></p>
      </div>
      <div class="m-1 service">
        <a class="nav-link bg-white rounded font-weight-bolder vk-top-color lh-m text-center text-uppercase" href="{{ route('stream') }}">Собрать и&nbsp;анализировать посты</a>
        <p class="d-none d-md-block text-center lh-sm"><small><small>инструмент помогает собрать посты ВК по ключевым словам, следить за упоминаниями о компании в соцсети, собрать активных авторов, провести анализ постов по количеству упоминаний, следить за репутацией компании и многое другое</small></small></p>
      </div>
    </nav>

    <div class="conainer">
      <form action="opros" method="post" autocomplete="off">
        @csrf
        <div class="input-group">
          <input class="form-control form-control-sm" type="text" name="opros"
            placeholder="ОПРОС: Какие функции нужно добавить на сайт, чтоб он стал для вас более удобным?">
          <button class="btn btn-sm btn-outline-primary text-white input-group-append" type="submit">Отправить</button>
        </div>
      </form>
    </div>
</div>
<div id="start">

</div>
</header>
