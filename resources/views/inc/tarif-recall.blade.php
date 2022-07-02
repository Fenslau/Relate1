@if (!Session::has('user_profile') || strtotime(session('user_profile')['paid_until']) - date('U') < 0)
  <div class="alert alert-warning">
    <p class="text-uppercase text-center">Подключите полный доступ на 3 дня, 30 дней или 90 дней в разделе <a href="{{ route('tarifs') }}">тарифы</a></p>
  </div>
@endif
