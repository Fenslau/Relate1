@extends('layouts.app')

@section('title-block',  __('Not Found'))

@section('content')
<h2 class="m-3 text-center h4">Страница не найдена</h2>
<div class="text-center">
  <input class="btn btn-success" type="button" onclick="history.back();" value="Назад"/>
</div>
@endsection
