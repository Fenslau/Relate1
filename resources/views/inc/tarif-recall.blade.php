@if (Session::has('user_profile') && strtotime(session('user_profile')->paid_until) - date('U') < 0)
  <div class="alert alert-warning">
    <p class="text-uppercase text-center">Подключите полный доступ ко всем иструментам с <a href="{{ route('tarifs') }}">тарифами</a> от 3 до 90 дней</p>
  </div>
@endif
