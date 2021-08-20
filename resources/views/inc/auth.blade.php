
<div class="container-fluid auth">

  <div class="d-flex align-items-center">
    <div class="mr-auto p-2">
      @if(Session::has('token'))
        <form action="{{ route('auth-vk-destroy') }}" method="get">
          <input name="url" type="hidden" value="{{ Route::currentRouteName() }}">
          <button type="submit" class="btn btn-sm btn-secondary"><i class="fa fa-sign-out fa-flip-horizontal" aria-hidden="true"></i> Выйти</button>
        </form>
      @else
        <form action="{{ route('auth-vk') }}" method="get">
          <input name="url" type="hidden" value="{{ Route::currentRouteName() }}">
          <button type="submit" class="btn btn-sm btn-secondary"><i class="fab fa-vk"></i> Вход / Регистрация через ВКонтакте</button>
        </form>
      @endif
    </div>
    @if(!Session::has('token'))
      <div class="d-none text-truncate d-md-block p-2">
        Сейчас у вас бесплатный доступ к сайту. Действуют ограничения.
      </div>
    @endif
    <div class="p-2">
      <a href="{{ route('tarifs') }}" type="button" class="btn btn-sm btn-primary vk-top-bg text-white">Оплатить&nbsp;доступ</a>
    </div>
    @if(Session::has('token'))
      <div class="p-2 d-flex align-items-center">
        <div class="px-1 d-inline-block">
          <a target="_blank" href="https://vk.com/id{{ $user_profile->id }}">
            <img class="rounded" src="{{ $user_profile->photo_50 }}" alt="">
          </a>
        </div>

        <div class="d-inline-block">
            <a target="_blank" href="https://vk.com/id{{ $user_profile->id }}">
              {{ $user_profile->first_name }} {{ $user_profile->last_name }}</a>
            @if($user_profile->id = 151103777 OR $user_profile->id = 409899462)
              <a target="_blank" href="{{ route('stat') }}">Статистика</a>
            @endif
        </div>
      </div>
    @endif
  </div>
</div>
