@extends('layouts.app')

@section('title-block', 'Сбор упоминаний о бренде ВК')

@section('content')

<div class="container">
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
            Правилу -"кот" будут соответствовать объекты, которые не содержат точную словоформу «кот».
            Правилу -собака будут соответствовать объекты, которые не содержат слово «собака» в любой форме.
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
    <div class="row justify-content-around">
      <form class="m-1" action="{{ route('stream') }}/{{ $info['project_name'] }}" method="get">
        <button class="btn btn-lg btn-outline-primary" type="submit"><i class="fab fa-windows"></i> <span class="d-none d-md-inline">Проект {{ $info['project_name'] }}</span></button>
      </form>

      <form class="m-1" action="{{ route('save-file') }}" method="post">
        @csrf
        <button class="btn btn-lg btn-outline-success" type="submit"><i class="far fa-file-excel"></i> <span class="d-none d-md-inline">Скачать файл Excel</span></button>
      </form>

      <form class="m-1" action="" method="get">
        <button class="btn btn-lg btn-outline-warning" type="submit"><i class="fas fa-history"></i> <span class="d-none d-md-inline">Прошлые посты</span></button>
      </form>

      <form class="m-1" action="" method="get">
        <button class="btn btn-lg btn-outline-info" type="submit"><i class="fas fa-chart-pie"></i> <span class="d-none d-md-inline">Статистика</span></button>
      </form>

      <form class="m-1" action="" method="get">
        <button class="btn btn-lg btn-outline-secondary" type="submit"><i class="fas fa-filter"></i> <span class="d-none d-md-inline">Фильтры</span></button>
      </form>
    </div>
</div>

<div class="my-5 container-xl">

  <div class="row">
    <div class="col-md-9">








      @include('inc.toast')
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
@endsection
