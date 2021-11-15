
@isset ($info['token'])
{{ $info['warning'] = NULL }}
<div class="mb-5 w-100 alert alert-warning">
  Время действия токена закончилось, необходимо
    <form class="d-inline" action="{{ route('auth-vk') }}" method="get">
      <input name="url" type="hidden" value="{{ session('previous-route') }}">
      <button type="submit" class="btn btn-sm btn-secondary"><i class="fab fa-vk"></i> авторизоваться</button>
    </form>
  заново
</div>
@endisset

@isset ($info['warning'])
  <div class="w-100 alert alert-warning">
    {!! $info['warning'] !!}
  </div>
@endisset
