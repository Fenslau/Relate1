<div class="row">
  <div class="col-md-5 align-self-center">
    <div class="mb-3 form-inline justify-content-center">
      <div class="p-2">
        <a href="{{ route('home') }}" type="button" class="btn btn-sm btn-secondary text-white">На&nbsp;главную</a>
      </div>

      <button id="js-load" disabled class="btn btn-sm btn-primary vk-top-bg" type="submit" name="submit"><i class="fa fa-search" aria-hidden="true"></i><span class="spinner-border spinner-border-sm text-light d-none" role="status" aria-hidden="true"></span> {{$button}}</button>
      <button id="new-search" class="d-none btn btn-sm btn-primary vk-top-bg" type="submit" name="new-search"><i class="fa fa-search" aria-hidden="true"></i><span class="spinner-border spinner-border-sm text-light d-none" role="status" aria-hidden="true"></span> Новый поиск</button>
    </div>
  </div>
  <div class="col-md-7">
    @if(Session::has('vkid'))
    <script type="text/javascript">
      var vkid={{ session('vkid') }};
      var url='{{ route($link) }}';
      var process1='{{ $link }}';
    </script>
      <div class="mt-2 progress">
        <div id="progress" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
      </div>
      <div id="progress-text" class="px-2 my-2 bg-secondary text-white"></div>
    @else
      <div class="w-100 alert alert-warning">
        <strong>Зарегистрируйтесь или авторизуйтесь через ВК, чтобы протестировать возможности в демо-режиме или получить полный доступ к ресурсам сайта.</strong>
      </div>
    @endif
  </div>
</div>
