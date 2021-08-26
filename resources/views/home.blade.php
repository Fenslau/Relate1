@extends('layouts.app')

@section('title-block', 'Поиск ТОП-групп ВК')

@section('content')

<div class="container">
  <div class="row">
    <div class="alert alert-info">
      @if(Session::has('vkid'))
        <strong><a class="alert-link" href="{{ route('download', 'temp\top1000') }}">Скачать таблицу топ1000групп в формате Excel</a></strong>
      @else
        <strong><a class="alert-link" href="{{ route('download', 'temp\top1000') }}">Скачать таблицу топ1000групп в формате Excel</a></strong>
      @endif
      
    </div>
  </div>
</div>


@endsection
