@extends('layouts.app')

@section('title-block', 'ТОП-посты ВК')
@section('description-block', 'Лента самых популярных постов ВКонтакте')

@section('content')

<div class="container-lg pb-5">
  @include('inc.toast')
@if(!Session::has('token'))
  <h4 class="h5 text-center alert alert-warning">Авторизуйтесь ВК для полноценного просмотра ленты</h4>
@endif
  <div id="begin" class="form-group d-flex flex-wrap justify-content-around align-items-center">

      <button type="button" class="my-2 btn btn-sm btn-info text-white mode no-outline" name="period" mode = "new"><i class="icon fas fa-hourglass-half"></i><span class="spinner-border spinner-border-sm d-none"></span> Свежие</button>
      <button type="button" class="my-2 btn btn-sm btn-danger text-white mode no-outline"  name="period" mode = "hot"><i class="icon fab fa-hotjar"></i><span class="spinner-border spinner-border-sm d-none"></span> Горячие</button>
      <button type="button" class="my-2 btn btn-sm btn-success text-white mode no-outline"  name="period" mode = "best"><i class="icon fas fa-thumbs-up"></i><span class="spinner-border spinner-border-sm d-none"></span> Лучшие</button>
  </div>
  <script type="text/javascript">
      $(document).ready( function () {
        $(document).on('click', '.mode', function (e) {
          e.preventDefault();
          _this = $(this);
          var mode = $(this).attr("mode");
          $.ajax({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            type: 'GET',
            url: '{{ route('toppost') }}',
            data: { 'mode' : mode },
            beforeSend: function () {
                      _this
                      .prop('disabled', true)
                      .find('.icon').addClass('d-none');
                      _this.find('.spinner-border-sm').removeClass('d-none');
            },
            success: function(data){
              if (data.success) {
                  _this
                  .prop('disabled', false)
                  .find('.icon').removeClass('d-none');
                  _this.find('.spinner-border-sm').addClass('d-none');
                $("#posts").html(data.html);
              } else {
                $('.toast-header').addClass('bg-danger');
                $('.toast-header').removeClass('bg-success');
                $('.toast-body').html('Что-то пошло не так. Попробуйте ещё раз или сообщите нам');
                $('.toast').toast('show');
              }}
          });
        });
      } );
  </script>
  <div id="posts">@include('inc.posts')</div>
</div>

<script src="{{ mix('/js/post.js') }}" type="text/javascript"></script>
@endsection
