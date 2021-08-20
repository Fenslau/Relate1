<header>
@include('inc.auth')

<div class="pb-2 container-fluid vk-top-bg text-white">

    <div class="d-flex justify-content-around">
      <div class="mr-auto p-2">
        <a href="{{ route('home') }}"><img class="img-fluid" src="/images/logo.jpg"></a>
        <div class="text-uppercase lh-sm mt-n2 text-right">
          <small><small><a class="text-white" href="mailto:vktoppost@sphere-market.ru">vktoppost@sphere-market.ru</a><br />
          техподдержка</small></small>
        </div>
      </div>
      <div class="p-2 mt-4 text-center">
        <h1 class="h3 text-uppercase">Мониторинг соцсети вконтакте</h1>
        <p>TOPPOST — площадка для smm-специалистов и всех, кто работает с группами и подписчиками в соцсетях.</p>
      </div>
    </div>

    <nav class="d-flex justify-content-around align-items-start navbar">
      <div class="m-1 service">
        <a class="nav-link bg-white rounded font-weight-bolder vk-top-color lh-m text-center text-uppercase" href="{{ route('groupsearch') }}">Группы с&nbsp;открытой стеной</a>
        <div class="text-center lh-sm"><small><small>поможет вам найти группы Вк по различным критериям (город, название, количество подписчиков и пр.) и скачать в файле</small></small></div>
      </div>
      <div class="m-1 service">
        <a class="nav-link bg-white rounded font-weight-bolder vk-top-color lh-m text-center text-uppercase" href="{{ route('auditoria') }}">Группы с&nbsp;похожей ЦА</a>
        <p class="text-center lh-sm"><small><small>поиск групп с целевой аудиторией, которая интересуется нужной вам темой</small></small></p>
      </div>
      <div class="m-1 service">
        <a class="nav-link bg-white rounded font-weight-bolder vk-top-color lh-m text-center text-uppercase" href="{{ route('getusers') }}">Собрать&nbsp;базу подписчиков групп</a>
        <p class="text-center lh-sm"><small><small>поможет найти подписчиков в группах по важным для вас характеристикам (пол, возраст, открытый профиль и пр.) и скачать их в файле</small></small></p>
      </div>
      <div class="m-1 service">
        <a class="nav-link bg-white rounded font-weight-bolder vk-top-color lh-m text-center text-uppercase" href="{{ route('new-users') }}">Мониторинг новых&nbsp;подписчиков в группах</a>
        <p class="text-center lh-sm"><small><small>инструмент, предназначенный наблюдать за ростом конкретной группы и видеть кто вступил в нее</small></small></p>
      </div>
      <div class="m-1 service">
        <a class="nav-link bg-white rounded font-weight-bolder vk-top-color lh-m text-center text-uppercase" href="{{ route('stream') }}">Собрать и&nbsp;анализировать посты</a>
        <p class="text-center lh-sm"><small><small>инструмент помогает собрать посты Вк по ключевым словам, следить за упоминаниями о компании в соцсети, собрать активных авторов, провести анализ постов по количеству упоминаний, следить за репутацией компании и многое другое</small></small></p>
      </div>
    </nav>

    <div class="conainer">
      <form action="opros" method="post" autocomplete="off">
        @csrf
        <div class="input-group">
          <input class="form-control form-control-sm" type="text" name="opros"
            placeholder="ОПРОС: Какие функции нужно добавить на сайт, чтоб он стал для вас более удобным?" aria-describedby="opros">
          <button class="btn btn-sm btn-outline-primary text-white" type="submit">Отправить</button>
        </div>
      </form>
    </div>
</div>
</header>
