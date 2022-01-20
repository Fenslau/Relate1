@extends('errors::minimal')

@section('title', __('Ошибка'))
@section('code', '500')
@section('message', __('Ошибка'))

<div class="text-center">
  <input class="btn btn-success" type="button" onclick="history.back();" value="Назад"/>
</div>
