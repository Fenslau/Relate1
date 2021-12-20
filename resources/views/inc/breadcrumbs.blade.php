<nav aria-label="breadcrumb">
    <ol class="breadcrumb">
      <li class="breadcrumb-item"><a href="{{ route('stream') }}">Главная</a></li>
      @isset($info['project_name'])
      <li class="breadcrumb-item"><a href="{{ route('post', $info['project_name']) }}">{{ $info['project_name'] }}</a></li>
      @endisset
      @isset($info['rule'])
      <li class="breadcrumb-item active" aria-current="page">{{ $info['rule'] }}</li>
      @endisset
    </ol>
</nav>
