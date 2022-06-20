<div class="row">
  <div class="col-md-5 align-self-center">
    <div class="mb-3 form-inline justify-content-center">
      <div class="p-2">
        <a href="{{ route('home') }}" class="btn btn-sm btn-secondary text-white">На&nbsp;главную</a>
      </div>

      <button id="js-load" disabled class="btn btn-sm btn-primary vk-top-bg" type="submit" name="submit" @auth @else disabled data-toggle="tooltip" title="Авторизуйтесь ВК" @endauth><i class="fa fa-search"></i><span class="spinner-border spinner-border-sm text-light d-none"></span> {{$button}}</button>
      <button id="new-search" class="d-none btn btn-sm btn-primary vk-top-bg" type="submit" name="new-search"><i class="fa fa-search"></i><span class="spinner-border spinner-border-sm text-light d-none"></span> Новый поиск</button>
    </div>
  </div>
  <div class="col-md-7">
    @if(Session::has('vkid'))
    <script>
      var vkid={{ session('vkid') }};
      var url='{{ route($link) }}';
      var process1='{{ $link }}';
    </script>
      <div class="mt-2 progress">
        <div id="progress" class="progress-bar progress-bar-striped progress-bar-animated" style="width: 0%"></div>
      </div>
      <div id="progress-text" class="px-2 my-2 bg-secondary text-white"></div>
    @else
      <div class="w-100 alert alert-warning">
        <strong>Зарегистрируйтесь или авторизуйтесь через ВК, чтобы протестировать возможности в демо-режиме или получить полный доступ к ресурсам сайта.</strong>
      </div>
    @endif
  </div>
</div>
@auth
  <script type="text/javascript">
    $(document).ready(function () {
      $(":submit:not(.enabled)").prop('disabled', false);
    });
  </script>
@endauth
