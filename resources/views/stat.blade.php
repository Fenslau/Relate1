@extends('layouts.app')

@section('title-block', 'Статистика посещений ВКТОППОСТ')

@section('content')

<div class="container">
  <div class="row">
@if(Session::has('vkid') AND (session('vkid') == 151103777 OR session('vkid') == 409899462))
    <form class="m-3 form-inline" action="{{ route('stat') }}" method="post">
      @csrf
      Собрать <input class="form-control-sm" size = "4" value = "15" type="text" name="count"> записей.
      <button type="submit" class="btn btn-sm btn-primary vk-top-bg">Отправить</button>
    </form>
@isset ($visitors)
<div class="d-flex align-self-center"><a href="temp/stat.xlsx">Скачать данные в формате Excel</a></div>
    <table class="d-table table table-striped table-hover table-sm table-responsive">
        <thead class="thead-dark">
          <tr class="text-center">
            <th scope="col">№</th>
            <th scope="col">Дата</th>
            <th scope="col">Профиль</th>
            <th scope="col">Имя</th>
            <th scope="col">Фамилия</th>
            <th scope="col">Город</th>
            <th scope="col">Фото</th>
            <th scope="col">№ визита</th>
      		  <th scope="col">Скачиваний</th>
            <th scope="col">Аноним.Скачиваний<br />на данный момент всего</th>
            <th scope="col">Платный</th>
          </tr>
        </thead>

        <tbody>
          @foreach ($visitors as $visitor)
            @if((date('U')-12*60*60) < strtotime($visitor->created_at)) <tr class="text-center table-warning">
            @else <tr class="text-center">
            @endif
              <td>{{ $visitor->id }}</td>
              <td>{{ date('d.m.y H:i', strtotime($visitor->created_at)) }}</td>
              <td><a target="_blank" href="https://vk.com/id{{ $visitor->vkid }}">{{ $visitor->vkid }}</a></td>
              <td>{{ $visitor->firstname }}</td>
              <td>{{ $visitor->lastname }}</td>
              <td>{{ $visitor->city }}</td>
              <td><img src="{{ $visitor->photo }}" alt=""></td>
              <td>{{ $visitor->visit_number }}</td>
              <td>{{ $visitor->downloads }}</td>
              <td>{{ $visitor->downloads_anon }}</td>
              <td {{ $visitor->oplata_class }}>{{ date('d.m.y', strtotime($visitor->oplata_date)) }}</td>
              <td {{ $visitor->oplata_class }}> @isset($visitor->oplata_date){{ date('d.m.y', strtotime($visitor->oplata_date)) }} @endisset</td>
            </tr>
          @endforeach
        </tbody>
    </table>
@endisset
@endif
  </div>
</div>
@endsection
