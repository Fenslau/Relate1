
<div class="">
@isset($info['found'])
<p>{!! $info['found'] !!}</p>
@endisset
{{ $items->onEachSide(5)->links() }}

  @forelse ($items as $item)
    <div class="my-3 card record" id="record{{ $item['id'] }}">
      <div class="px-1 d-flex justify-content-between card-header text-muted">

        @if (!empty($item['dublikat']) AND $item['dublikat'] !='ch' AND strpos($item['dublikat'], 'd:') === FALSE)
          <a class="text-success hider_dublikat" name="{{ $item['id'] }}"
          data-toggle="tooltip" title="Раскрыть дубликаты"><i class="far fa-plus-square"></i></a>
          <div class="active-dublikat" id="dublikat_{{ $item['id'] }}"></div>
        @endif

        <span class="d-none d-md-inline px-1 vk-top-bg rounded text-white text-nowrap">{{ $item['event_type'] }}<span> id:{{ $item['id'] }}</span></span>
        <a class="d-none d-md-inline" rel="nofollow" target="_blank" href="{{ $item['event_url'] }}">Ссылка</a>
        @if (!empty($item['shared_post_author_id']))
          <div class="text-truncate d-none d-md-inline">
            <a href="https://vk.com/wall{{ $item['shared_post_author_id'] }}_{{ $item['shared_post_id'] }}">Оригинал</a>
          </div>
        @endif

        <span class="text-info text-truncate">{{ $item['action_time'] }}</span>
        <div class="d-inline text-truncate">
          {!! $item['author_id'] !!}
        </div>

        @if (!empty($item['shared_post_author_id']))
          <div class="text-truncate d-none d-md-inline">
            @if ($item['shared_post_author_id'] > 0)
    				<a rel="nofollow" target="_blank" href="https://vk.com/id{{ $item['shared_post_author_id'] }}">Автор оригинала</a>
            @else <a rel="nofollow" target="_blank" href="https://vk.com/id{{ -$item['shared_post_author_id'] }}">Автор оригинала</a>
            @endif
          </div>
  			@endif

        @if (!empty($item['platform']))
        <div class="text-info text-truncate d-none d-lg-inline">
          {{ $item['platform'] }}
        </div>
        @endif

        @if (!empty($links))
          <div class="d-none d-md-inline text-truncate">
      			<select class="text-muted border choose-link" name="{{ $item['id'] }}">
      			<option value="">В папку</option>
      			@foreach ($links as $link)
      				<option  {{ ($link == $item['user_links'] ? "selected":"") }} value="{{ $link }}">{{ $link }}</option>
      			@endforeach
      			</select>
          </div>
    		@endif




        <input id="{{ $item['id'] }}_trash" class="d-none check_trash check" type="checkbox"	{{ ($item['check_trash'] == 1 ? "checked":"") }} name="{{ $item['id'] }}" url="trash">
        <label for="{{ $item['id'] }}_trash" data-toggle="tooltip" title="В корзину" class="m-0 check_trash cursor-pointer"><i class="fa fa-trash"></i></label>

				<input id="{{ $item['id'] }}_flag" class="d-none check_flag check" type="checkbox" {{ ($item['check_flag'] == 1 ? "checked":"") }} name="{{ $item['id'] }}" url="flag">
        <label for="{{ $item['id'] }}_flag" data-toggle="tooltip" title="В избранное" class="m-0 check_flag cursor-pointer"><i class="fa fa-star"></i></label>

        <span class="text-danger font-weight-bold">{{ $items->firstItem()+$loop->index }}</span>
      </div>

      <div class="p-0 card-body">
        <div class="p-2 border-left border-3 border-info">
          {!! $item['data'] !!}
        </div>

      </div>

      <div class="d-flex justify-content-between card-footer text-muted">
        <div class="">

        </div>
        <span><span class="d-none d-md-inline">Правило: </span><mark><a class="text-muted" data-toggle="tooltip" title="Показать записи только с этой меткой" href = "{{ route('stream') }}/{{ $info['project_name'] }}/?rule={{ str_replace(session('vkid'), '', $item['user']) }}">{{ str_replace(session('vkid'), '', $item['user']) }}</a></mark></span>
      </div>
    </div>
  @empty
    <p>Не нашлось ни одной записи</p>
  @endforelse
{{ $items->onEachSide(5)->links() }}
</div>
