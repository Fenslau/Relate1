
<div class="container-fluid auth">

  <div class="d-flex align-items-center">
    <div class="mr-auto p-2">
      @if(Session::has('token'))
        <form class="d-inline" action="{{ route('auth-vk-destroy') }}" method="get">
          <input name="url" type="hidden" value="{{ Route::currentRouteName() }}">
          <button type="submit" class="btn btn-sm btn-secondary"><i class="fa fa-sign-out fa-flip-horizontal"></i> Выйти</button>
        </form>
        <div class="d-none text-truncate d-md-inline p-2">
          <a style="background-color: #ee3ec9;" class="border-0 btn btn-sm btn-info" href="{{ route('files') }}">Личный кабинет</a>
        </div>
      @else
        <form class="d-inline" action="{{ route('auth-vk') }}" method="get">
          <input name="url" type="hidden" value="{{ Route::currentRouteName() }}">
          <button type="submit" class="btn btn-sm btn-secondary"><i class="fab fa-vk"></i> Вход / Регистрация <span class="d-none d-md-inline"> через ВКонтакте</span></button>
        </form>
        <div data-toggle="tooltip" title="Авторизуйтесь ВК" class="d-none text-truncate d-md-inline p-2">
          <a style="background-color: #ee3ec9;" class="border-0 btn btn-sm btn-info" href="#">Личный кабинет</a>
        </div>
      @endif
    </div>
    @if(!Session::has('token'))
      <div class="d-none text-truncate d-md-block p-2">
        Сейчас у вас бесплатный доступ к сайту. Действуют ограничения.
      </div>
    @elseif (!empty($user_profile->paid_until))
    <div class="d-none text-truncate d-md-block p-2">
      Доступ оплачен до
      @if (strtotime($user_profile->paid_until) - date('U') < (3600*24) AND strtotime($user_profile->paid_until) - date('U') > 0)<span class="text-warning">{{ date('d.m.y H:i', strtotime($user_profile->paid_until)) }}</span> @endif
      @if (strtotime($user_profile->paid_until) - date('U') > (3600*24))<span class="text-success">{{ date('d.m.y', strtotime($user_profile->paid_until)) }}</span> @endif
      @if (strtotime($user_profile->paid_until) - date('U') < 0)<span class="text-danger">{{ date('d.m.y H:i', strtotime($user_profile->paid_until)) }}</span> @endif
    </div>
    @endif
    <div class="p-2">
      <a href="{{ route('tarifs') }}" class="btn btn-sm btn-primary vk-top-bg text-white">Оплатить&nbsp;доступ</a>
    </div>
    @if(Session::has('token'))
      <div class="p-2 d-flex align-items-center">
        <div class="px-1 d-inline-block">
          <a target="_blank" href="https://vk.com/id{{ $user_profile->id }}">
            <img class="rounded" src="{{ $user_profile->photo_50 }}" width="32" alt="">
          </a>
        </div>

        <div class="d-none d-sm-inline-block">
            <a target="_blank" href="https://vk.com/id{{ $user_profile->id }}">
              {{ $user_profile->first_name }} {{ $user_profile->last_name }}</a>
            @if($user_profile->id == 151103777 OR $user_profile->id == 409899462)
              <a target="_blank" href="{{ route('stat') }}">Статистика</a>
            @endif
        </div>
      </div>
    @endif
  </div>
</div>
