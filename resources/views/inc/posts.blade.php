<div class="">
@if (!empty($info['rule']) AND $info['rule'] == 'Доп.посты')
<p class="alert alert-info">В этой папке собираются записи, в которых не удалось достоверно определить наличие ключевых слов. Однако они должны где-то там быть. Например, они могут быть в описании фотографий, или находиться внутри ссылок, названий видео/аудио и т.п.</p>
@endif
@if (!empty($info['rule']) AND $info['rule'] == 'Корзина')
<p class="alert alert-info">В корзине могут лежать не только те записи, которые вы сами удалили. Иногда система помещает сюда посты, в которых есть ключевые слова, но они находятся на большом расстоянии друг от друга, поэтому скорее всего не являются целевыми, однако посматривать сюда бывает полезно. Корзина самоочищается раз в сутки</p>
@endif
@isset($info['found'])
<p>{!! $info['found'] !!}</p>
@endisset
@if (empty($dublikat_render))
{{ $items->onEachSide(4)->links() }}
@endif

  @forelse ($items as $item)
    <div class="my-3 card record" id="record{{ $item['id'] }}">
      <div class="px-1 d-flex justify-content-between card-header text-muted">

        @if (!empty($item['dublikat']) AND $item['dublikat'] != 'ch' AND strpos($item['dublikat'], 'd:') === FALSE)
          <button class="p-0 btn border-0 text-success hider_dublikat shadow-none" name="{{ $item['id'] }}"
          data-toggle="tooltip" title="Раскрыть дубликаты"><i class="icon far fa-plus-square"></i><span class="spinner-border spinner-border-sm d-none"></span></button>
        @endif

        <span class="mx-1 d-none d-md-inline px-1 vk-top-bg rounded text-white text-nowrap">{{ $item['event_type'] }}<span> id:{{ $item['id'] }}</span></span>
        <a class="mx-1 d-none d-md-inline" rel="nofollow" target="_blank" href="{{ $item['event_url'] }}">Ссылка</a>
        @if (!empty($item['shared_post_author_id']))
          <div class="mx-1 text-truncate d-none d-md-inline">
            <a href="https://vk.com/wall{{ $item['shared_post_author_id'] }}_{{ $item['shared_post_id'] }}">Оригинал</a>
          </div>
        @endif

        <span class="mx-1 text-info text-truncate">{{ strftime ('%d %b %yг.  %R', $item['action_time']) }}</span>

        <div class="mx-1 d-inline text-truncate">
          {!! $item['author_id'] !!}
        </div>

        @if (!empty($item['shared_post_author_id']))
          <div class="mx-1 text-truncate d-none d-md-inline">
            @if ($item['shared_post_author_id'] > 0)
    				<a rel="nofollow" target="_blank" href="https://vk.com/id{{ $item['shared_post_author_id'] }}">Автор оригинала</a>
            @else <a rel="nofollow" target="_blank" href="https://vk.com/id{{ -$item['shared_post_author_id'] }}">Автор оригинала</a>
            @endif
          </div>
  			@endif

        @if (!empty($item['platform']))
        <div class="mx-1 text-info text-truncate d-none d-lg-inline">
          {{ $item['platform'] }}
        </div>
        @endif

        @if (!empty($links))
          <div class="mx-1 d-none d-md-inline text-truncate">
      			<select class="text-muted border choose-link" name="{{ $item['id'] }}">
      			<option value="">В папку</option>
      			@foreach ($links as $link)
      				<option  {{ ($link == $item['user_links'] ? "selected":"") }} value="{{ $link }}">{{ $link }}</option>
      			@endforeach
      			</select>
          </div>
    		@endif




        <input id="{{ $item['id'] }}_trash" class="d-none check_trash check" type="checkbox"	{{ ($item['check_trash'] == 1 ? "checked":"") }} name="{{ $item['id'] }}" url="trash">
        <label for="{{ $item['id'] }}_trash" data-toggle="tooltip" title="В корзину" class="mx-1 m-0 check_trash cursor-pointer"><i class="fa fa-trash"></i></label>

				<input id="{{ $item['id'] }}_flag" class="d-none check_flag check" type="checkbox" {{ ($item['check_flag'] == 1 ? "checked":"") }} name="{{ $item['id'] }}" url="flag">
        <label for="{{ $item['id'] }}_flag" data-toggle="tooltip" title="В избранное" class="mx-1 m-0 check_flag cursor-pointer"><i class="fa fa-star"></i></label>
        @if (empty($dublikat_render))
        <span class="text-danger font-weight-bold">{{ $items->firstItem()+$loop->index }}</span>
        @else
        <span class="text-danger font-weight-bold">{{ $loop->iteration }}</span>
        @endif
      </div>
      <div class="border-left border-warning border-3" id="dublikat_{{ $item['id'] }}"></div>
      <div class="p-0 card-body">
        @if (!empty($item['data']))
          <div class="p-2 border-left border-3 border-info textdata">
            {!! $item['data'] !!}
          </div>
        @endif
        @if (!empty($item['note']))
          <div class="p-2 border-right text-right border-3 border-info textdata">
            {!! $item['note'] !!}
          </div>
        @endif

        @if (!empty($item['photo']))
          <div class="my-2">
    				@foreach (explode(",https:", $item['photo']) as $photo)
              <div class="mx-1 my-2">
              	<img loading="lazy" class="rounded img-fluid" src="{{ $photo }}" />
              </div>
    				@endforeach
          </div>
        @endif

        @if (!empty($item['video_player']) && !empty($video[$item['id']]))
          <div class="p-2 d-flex flex-wrap justify-content-between">
            @foreach ($video[$item['id']] as $video1)
              <div class="my-2 border border-gray" style="width: 375px">
                <h6 class="text-muted">К записи прикреплено видео:</h6>
                <h6 class="">{{ $video1['title'] }}</h6>
                @if (!empty($video1['player']))
                  <div class="embed-responsive embed-responsive-16by9">
                    <iframe class="embed-responsive-item" src="{{ $video1['player'] }}" allowfullscreen></iframe>
                  </div>
                @endif
                <p class="lh-m text-muted">{{ $video1['description'] }}</p>
              </div>
            @endforeach
          </div>
        @endif

        @if (!empty($item['audio']))
          <div class="p-2 text-info">
            @foreach (explode("9GZVNyidgk", $item['audio']) as $audio)
    					@if (!empty($audio)) <p class="text-truncate">В записи используется аудио: {{ $audio }}</p>
              @endif
    				@endforeach
          </div>
        @endif

        @if (!empty($item['link']))
          <div class="p-2 text-info">
            @foreach (explode(",", $item['link']) as $link)
    					@if (!empty($link)) <p class="text-truncate">К записи прикреплена ссылка: <a rel="nofollow" target="_blank" href = "{{ $link }}">{{ $link }}</a></p>
              @endif
    				@endforeach
          </div>
        @endif

        @if (!empty($item['doc']))
          <div class="p-2 text-info">
            @foreach (explode(",", $item['doc']) as $doc)
    					@if (!empty($doc)) <p class="text-truncate">К записи прикреплён документ: <a rel="nofollow" target="_blank" href = "{{ $doc }}">{{ $doc }}</a></p>
              @endif
    				@endforeach
          </div>
        @endif
      </div>

      @if (!empty($item['geo_place_title']) || !empty($item['geo_place_country']) || !empty($item['geo_place_city']))
        <div class="d-flex justify-content-between card-footer text-muted">
          <div class="">
            @if (!empty($item['geo_place_icon']))
            <img src="{{ $item['geo_place_icon'] }}">
            @endif
          </div>
          <div class="">
            @if (!empty($item['geo_place_country']))
            {{ $item['geo_place_country'] }}
            @endif
          </div>
          <div class="">
            @if (!empty($item['geo_place_city']))
            {{ $item['geo_place_city'] }}
            @endif
          </div>
          <div class="">
            @if (!empty($item['geo_place_title']))
            {{ $item['geo_place_title'] }}
            @endif
          </div>
        </div>
      @endif
      <div class="p-1 d-flex justify-content-between card-footer text-muted">
        <div class="d-flex">
          @if ($item['event_type'] == 'post' || $item['event_type'] == 'share')
            @if (!empty($post[$item['id']]['comments']))
              <button data-toggle="tooltip" title="Комментарии" style="color: #004aad" class="btn p-0 mt-0 text-nowrap mx-2 ajax-comment shadow-none" event_url="{{ $item['event_url'] }}" name="{{ $item['id'] }}">
                <i class="icon far fa-comments"></i><span class="spinner-border spinner-border-sm d-none"></span>
                {{ number_format($post[$item['id']]['comments'], 0, ',', ' ') }}
              </button>
            @endif

            @if (!empty($post[$item['id']]['likes']))
              <div data-toggle="tooltip" title="Лайки" class="text-nowrap mx-2">
                <i class="far fa-heart"></i>
                {{ number_format($post[$item['id']]['likes'], 0, ',', ' ') }}
              </div>
            @endif

            @if (!empty($post[$item['id']]['reposts']))
              <div data-toggle="tooltip" title="Репосты" class="text-nowrap mx-2">
                <i class="fas fa-bullhorn"></i>
                {{ number_format($post[$item['id']]['reposts'], 0, ',', ' ') }}
              </div>
            @endif

            @if (!empty($post[$item['id']]['views']))
              <div data-toggle="tooltip" title="Просмотры" class="text-nowrap mx-2">
                <i class="far fa-eye"></i>
                {{ number_format($post[$item['id']]['views'], 0, ',', ' ') }}
              </div>
            @endif
          @endif
        </div>
        <div class="text-truncate">
          <span class="d-none d-md-inline">Правило: </span><mark><a class="text-muted" data-toggle="tooltip" title="Показать записи только с этой меткой" href = "{{ route('stream') }}/{{ $info['project_name'] }}/?rule={{ str_replace(session('vkid'), '', $item['user']) }}">{{ str_replace(session('vkid'), '', $item['user']) }}</a></mark>
        </div>
      </div>
      @if (!empty($post[$item['id']]['comments']))
        <div class="bg-light" id="comment_{{ $item['id'] }}"></div>
      @endif
    </div>
  @empty
    @if (empty($dublikat_render))
      <p>Не нашлось ни одной записи</p>
    @endif
  @endforelse

  @if (empty($dublikat_render))
  {{ $items->onEachSide(4)->links() }}
  @endif

</div>
@if (!empty($cut))
  <script src="/js/readmore.js" type="text/javascript"></script>
  <script type="text/javascript">
  		$(".textdata").readmore ({
  				maxHeight: 200,
  				heightMargin: 200,
  				moreLink: "<a class='cursor-pointer pl-3'>Развернуть текст</a>",
  				lessLink: "<a class='cursor-pointer pl-3'>Свернуть текст</a>"
  		});
  </script>
@endif
