@forelse ($ignore_list as $id => $name)
  <div class="m-1 d-flex justify-content-between align-items-center"><span class="group-name text-truncate text-nowrap">{{ $name }}</span> <i data-toggle="tooltip" title="Убрать из игнор-листа" ignoreid="{{ $id }}" class="text-danger cursor-pointer fa fa-times"></i></div>
@empty
<p>Вы пока никого не заигнорили</p>
@endforelse
