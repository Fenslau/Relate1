
<div class="container-fluid auth">

  <div class="d-flex">
    <div class="mr-auto p-2">
      <form action="{{ route('auth-vk') }}" method="post">
        @csrf
        <input name="url" type="hidden" value="{{ url()->current() }}">
        <button type="submit" class="btn-sm btn-secondary"><i class="fab fa-vk"></i> Вход / Регистрация через ВКонтакте</button>
      </form>
    </div>
    <div class="p-2">
      Сейчас у вас бесплатный доступ к сайту. Действуют ограничения.
    </div>
    <div class="p-2">
      <button type="button" class="btn-sm btn-primary vk-top-bg text-white">Оплатить доступ</button>
    </div>
  </div>
</div>
