@extends('layouts.app')

@section('title-block', 'Мониторинг новых подписчиков в группах ВК')

@section('content')

<div class="container-lg">
  <form class="" id="search-submit" action="{{ route('new-users') }}" method="post">
    @csrf
    <h2 class="m-3 text-center text-uppercase" >Мониторинг новых подписчиков в группах ВК</h2>
    @include('inc.buttons-and-progress', ['link' => 'new-users', 'button' => 'Добавить'])

    <div class="search-form_ row pb-2">
      <div class="col-md-6 ">

        <div class="">
          <p>Введите <b>ссылку на группу</b> из ВК или <b>ID группы</b>, в которой вы хотите отследить появление новых, или отток существующих подписчиков, после чего нажмите кнопку "Добавить". Группа добавится в "отслеживаемые", и вы сможете затем собирать информацию через произвольные промежутки времени, например каждый день.</p>
        </div>
      </div>


      <div class="col-md-6">
        <input class="my-3 form-control" id="group-name" type="text" name="group" placeholder="https://vk.com/apiclub">
      </div>
    </div>
  </form>


    @include('inc.list-follow-groups')
  <div class="table-responsive" id="table-search">

  </div>
</div>


@endsection
