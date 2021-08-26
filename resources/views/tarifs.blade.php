@extends('layouts.app')

@section('title-block', 'Тарифы ВКТОППОСТ')

@section('content')


<h2 class="text-center p-5">Тарифы</h2>
  <div class="container-fluid">
      <div class="row">
        <div class="col-xl-6 text-center">
          <div class="p-2">
            <h3 class="h4">Работа с парсерами</h3>
            <small><b>Что входит в тариф:</b> собрать группы с открытой стеной, группы с похожей ЦА, собрать подписчиков групп, мониторинг новых подписчиков в группах</small>
          </div>
            <div class="d-flex flex-md-nowrap flex-wrap justify-content-center align-content-around text-center">
                <div class="tarif">
                    <div class="tarif-name">
                        3 Дня
                    </div>
                    <div class="tarif-text">
                        <ul>
                            <li>Доступ к фильтрам и парсерам на 3 дня</li>
                            <li>Техподдержка на любом этапе работы с сайтом</li>
                        </ul>
                    </div>
                    <div class="tarif-bottom">
                        <form action="{{ route('tarif-choose') }}" accept-charset="utf-8" method="POST">
                          @csrf
                          <input type="hidden" name="amount" value="194">
                          <input type="hidden" name="comment" value="Оплата за доступ на три дня. После оплаты вернитесь на сайт https://vktoppost.ru самостоятельно">
                          <input type="hidden" name="vkid" value="{{ session('vkid') }}">
                          @if(Session::has('token'))
                          <input class="kupit" type="submit" name="submit199" value="Купить">
                          @else
                          <span class="d-block" tabindex="0" data-toggle="tooltip" title="Авторизуйтесь ВК">
                          <input style="pointer-events: none;" class="kupit" disabled type="submit" name="submit199" value="Купить">
                          </span>
                          @endif
                        </form>
                        <div class="price">
                            199₽
                        </div>
                    </div>
                </div>
                <div class="tarif popular">
                    <div class="popular-label" style="margin-left: 14%;">
                        Популярный
                    </div>
                    <div class="tarif-name">
                        30 Дней
                    </div>
                    <div class="tarif-text">
                        <ul>
                            <li>Доступ к фильтрам и парсерам на 1 месяц</li>
                            <li>Приоритетная поддержка</li>
                            <li>Отслеживание новичков в 10 группах</li>
                        </ul>
                    </div>
                    <div class="tarif-bottom">
                      <form action="{{ route('tarif-choose') }}" accept-charset="utf-8" method="POST">
                        @csrf
                        <input type="hidden" name="amount" value="538">
                        <input type="hidden" name="comment" value="Оплата за доступ на 30 дней. После оплаты вернитесь на сайт https://vktoppost.ru самостоятельно">
                        <input type="hidden" name="vkid" value="{{ session('vkid') }}">
                        @if(Session::has('token'))
                        <input class="kupit" type="submit" name="submit549" value="Купить">
                        @else
                        <span class="d-block" tabindex="0" data-toggle="tooltip" title="Авторизуйтесь ВК">
                        <input style="pointer-events: none;" class="kupit" disabled type="submit" name="submit549" value="Купить">
                        </span>
                        @endif
                      </form>
                        <div class="price">
                            549₽
                        </div>
                    </div>
                </div>
                <div class="tarif">
                    <div class="tarif-name">
                        90 Дней
                    </div>
                    <div class="tarif-text">
                        <ul>
                            <li>Доступ к фильтрам и парсерам на 3 месяца</li>
                            <li>Приоритетная поддержка</li>
                            <li>Отслеживание новичков в 10 группах</li>
                            <li>7 дней доступа в подарок</li>
                        </ul>
                    </div>
                    <div class="tarif-bottom">
                      <form action="{{ route('tarif-choose') }}" accept-charset="utf-8" method="POST">
                        @csrf
                        <input type="hidden" name="amount" value="1273">
                        <input type="hidden" name="comment" value="Оплата за доступ на 90 дней. После оплаты вернитесь на сайт https://vktoppost.ru самостоятельно">
                        <input type="hidden" name="vkid" value="{{ session('vkid') }}">
                        @if(Session::has('token'))
                        <input class="kupit" type="submit" name="submit1299" value="Купить">
                        @else
                        <span class="d-block" tabindex="0" data-toggle="tooltip" title="Авторизуйтесь ВК">
                        <input style="pointer-events: none;" class="kupit" disabled type="submit" name="submit1299" value="Купить">
                        </span>
                        @endif
                      </form>
                        <div class="price">
                            1299₽
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-6 text-center">
          <div class="p-2">
            <h3 class="h5">Управление репутацией/исследования потребителей, конкурентов, трендов</h3>
            <small><b>Что входит в тариф:</b> собрать упоминания и посты в реальном времени, собрать упоминания и посты за нужный период, анализировать посты по различным критериям</small>
          </div>
            <div class="d-flex flex-md-nowrap flex-wrap justify-content-center align-content-around text-center">
                <div class="tarif post">
                    <div class="tarif-name">
                        Post 1
                        <br>
                        <small>(7 дней)</small>
                    </div>
                    <div class="tarif-text">
                        <ul>
                            <li>1 проект (тема) по ключевому запросу</li>
                            <li>2 уточняющих правила внутри проекта (темы)</li>
                            <li>100 упоминаний по теме</li>
                            <li>Аналитический отчет по собранным данным</li>
                        </ul>
                    </div>
                    <div class="tarif-bottom">
                      <form action="{{ route('tarif-choose') }}" accept-charset="utf-8" method="POST">
                        @csrf
                        <input type="hidden" name="amount" value="342">
                        <input type="hidden" name="comment" value="Оплата за тариф POST1 (7 дней). После оплаты вернитесь на сайт https://vktoppost.ru самостоятельно">
                        <input type="hidden" name="vkid" value="{{ session('vkid') }}">
                        @if(Session::has('token'))
                        <input class="kupit" type="submit" name="submit349" value="Купить">
                        @else
                        <span class="d-block" tabindex="0" data-toggle="tooltip" title="Авторизуйтесь ВК">
                        <input style="pointer-events: none;" class="kupit" disabled type="submit" name="submit349" value="Купить">
                        </span>
                        @endif
                      </form>
                        <div class="price">
                            349₽
                        </div>
                    </div>
                </div>
                <div class="tarif post popular">
                    <div class="popular-label" style="margin-left: 22%">
                        Удобный
                    </div>
                    <div class="tarif-name">
                        Post 2
                        <br>
                        <small>(7 дней)</small>
                    </div>
                    <div class="tarif-text">
                        <ul>
                            <li>2 проекта (темы) по ключевому запросу</li>
                            <li>5 уточняющих правил внутри проекта (темы)</li>
                            <li>500 упоминаний по теме в реальном времени</li>
                            <li>Поиск упоминаний темы по прошедшим датам</li>
                            <li>Аналитический отчет по собранным данным</li>
                        </ul>
                    </div>
                    <div class="tarif-bottom">
                      <form action="{{ route('tarif-choose') }}" accept-charset="utf-8" method="POST">
                        @csrf
                        <input type="hidden" name="amount" value="539">
                        <input type="hidden" name="comment" value="Оплата за тариф POST2 (7 дней). После оплаты вернитесь на сайт https://vktoppost.ru самостоятельно">
                        <input type="hidden" name="vkid" value="{{ session('vkid') }}">
                        @if(Session::has('token'))
                        <input class="kupit" type="submit" name="submit549" value="Купить">
                        @else
                        <span class="d-block" tabindex="0" data-toggle="tooltip" title="Авторизуйтесь ВК">
                        <input style="pointer-events: none;" class="kupit" disabled type="submit" name="submit549" value="Купить">
                        </span>
                        @endif
                      </form>
                        <div class="price">
                            549₽
                        </div>
                    </div>
                </div>
                <div class="tarif post">
                    <div class="tarif-name">
                        Post 3
                        <br>
                        <small>(30 дней)</small>
                    </div>
                    <div class="tarif-text">
                        <ul>
                            <li>2 проекта (темы) по ключевому запросу</li>
                            <li>5 уточняющих правил внутри проекта (темы)</li>
                            <li>2000 упоминаний по теме в реальном времени</li>
                            <li>Поиск упоминаний темы по прошедшим датам</li>
                            <li>Аналитический отчет по собранным данным</li>
                        </ul>
                    </div>
                    <div class="tarif-bottom">
                      <form action="{{ route('tarif-choose') }}" accept-charset="utf-8" method="POST">
                        @csrf
                        <input type="hidden" name="amount" value="979">
                        <input type="hidden" name="comment" value="Оплата за тариф POST3 (30 дней). После оплаты вернитесь на сайт https://vktoppost.ru самостоятельно">
                        <input type="hidden" name="vkid" value="{{ session('vkid') }}">
                        @if(Session::has('token'))
                        <input class="kupit" type="submit" name="submit999" value="Купить">
                        @else
                        <span class="d-block" tabindex="0" data-toggle="tooltip" title="Авторизуйтесь ВК">
                        <input style="pointer-events: none;" class="kupit" disabled type="submit" name="submit999" value="Купить">
                        </span>
                        @endif
                      </form>
                        <div class="price">
                            999₽
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>



@endsection
