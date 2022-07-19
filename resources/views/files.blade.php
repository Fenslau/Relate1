@extends('layouts.app')

@section('title-block', 'Мои файлы')
@section('description-block', 'Архив запросов к сайту в виде файлов Excel')

@section('content')

<div class="container">
  @include('inc.toast')
  @include('inc.tarif-recall')
  <h2 class="m-3 text-center text-uppercase" >Мои файлы</h2>
  @foreach ($files as $file)
    <div class="alert alert-primary">
      <a class="alert-link" href="/storage{{ $file }}">{{ $file }}</a>
    </div>
  @endforeach
</div>

@endsection
