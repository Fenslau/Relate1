<div id="query_request"
@if ($request instanceof Illuminate\Http\Request)
query_string="{{ serialize($request->all()) }}"
@endif
class="">
@if (empty($dublikat_render))
@include('inc.breadcrumbs')
@endif
<div id="ajax"> </div>
@if (!empty($info['rule']) AND $info['rule'] == 'Доп.посты')
<p class="alert alert-info">В этой папке собираются записи, в которых не удалось достоверно определить наличие ключевых слов. Однако они должны где-то там быть. Например, они могут быть в описании фотографий, или находиться внутри ссылок, названий видео/аудио и т.п.</p>
@endif
@if (!empty($info['rule']) AND $info['rule'] == 'Корзина')
<p class="alert alert-info">В корзине могут лежать не только те записи, которые вы сами удалили. Иногда система помещает сюда посты, в которых есть ключевые слова, но они находятся на большом расстоянии друг от друга, поэтому скорее всего не являются целевыми, однако посматривать сюда бывает полезно. Корзина самоочищается раз в сутки</p>
@endif
@isset($info['old_rule'])
  @if($info['old_rule'] === TRUE)
  <p class="alert alert-info">Старые правила — это правила, по которым больше не собираются посты, но прошлые записи ещё остались в базе</p>
  @endif
@endisset
@isset($info['found'])
<p>{!! $info['found'] !!}</p>
@endisset

@if (!empty($request->man) || !empty($request->woman) || !empty($request->group) || !empty($request->type) || (!empty($request->age) && $request->age != '13 - 118') || !empty($request->country) || !empty($request->city) || !empty($request->not_city) || !empty($request->followers_to) || !empty($request->followers_from) || !empty($request->calendar_from) || !empty($request->calendar_to) || !empty($request->author_id))
  <div class="rounded mb-3 px-2 pb-1 bg-dark filter-panel">
      <i class="mx-3 fas fa-filter">:</i>
    @isset ($request->man)
      <i class="mx-1 fas fa-male"></i>
    @endisset
    @isset ($request->woman)
      <i class="mx-1 fas fa-female"></i>
    @endisset
    @isset ($request->group)
      <i class="mx-1 fas fa-users"></i>
    @endisset
    @isset ($request->type)
      @if(in_array('comment', $request->type))<i class="mx-1 far fa-comments"></i>@endif
      @if(in_array('share', $request->type))<i class="mx-1 far fa-bullhorn"></i>@endif
      @if(in_array('post', $request->type))<span class="badge">Post</span>@endif
      @if(in_array('topic_post', $request->type))<span class="badge">topic-post</span>@endif
    @endisset
    @isset ($request->age)
      @if($request->age != '13 - 118')<span class="badge">Age</span>@endif
    @endisset
    @isset ($request->country)
      <span class="badge">{{ $request->country[0] }}...</span>
    @endisset
  @isset ($cities)
    @isset ($request->city)
      <span class="badge">{{ $cities[$request->city[0]] }}...</span>
    @endisset
    @isset ($request->not_city)
      <span class="badge"><s>{{ $cities[$request->not_city[0]] }}...</s></span>
    @endisset
  @endisset
    @if (!empty($request->followers_to) || !empty($request->followers_from))
      <span class="badge">Подписч.</span>
    @endif
    @if (!empty($request->calendar_from) || !empty($request->calendar_to))
      <i class="mx-1 far fa-calendar-alt"></i>
    @endif
    @if (!empty($request->author_id))
      <i class="mx-1 fas fa-user-edit"></i>
    @endif
  </div>
@endif

@if ($request->apply_filter == 'Показать записи' || empty($request->apply_filter))

  {{ $items->onEachSide(4)->links() }}

    @forelse ($items as $item)
      <div class="my-3 card record" id="record{{ $item['id'] }}">
        <div class="px-1 d-flex justify-content-between card-header text-muted">

          @if (!empty($item['dublikat']) AND $item['dublikat'] != 'ch' AND empty($dublikat_render))
            <button class="p-0 btn border-0 text-success hider_dublikat shadow-none" name="{{ $item['id'] }}"
            data-toggle="tooltip" title="Раскрыть дубликаты"><i class="icon far fa-plus-square"></i><span class="spinner-border spinner-border-sm d-none"></span></button>
          @endif

          <span class="mx-1 d-none d-md-inline px-1 vk-top-bg rounded text-white text-nowrap">{{ $item['event_type'] }}<span> id:{{ $item['id'] }}</span></span>
          <a class="mx-1 d-none d-md-inline" rel="nofollow" target="_blank" href="{{ $item['event_url'] }}">Ссылка</a>
          @if (!empty($item['shared_post_author_id']))
            <div class="mx-1 text-truncate d-none d-md-inline">
              <a rel="nofollow" target="_blank" href="https://vk.com/wall{{ $item['shared_post_author_id'] }}_{{ $item['shared_post_id'] }}">Оригинал</a>
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

          @if (!empty($item['video_player']))
            <div class="p-2 d-flex flex-wrap justify-content-between">
              @foreach (array_diff(explode(',', $item['video_player']), ['']) as $video1)
                <div id="video{{ $loop->index }}_{{ $item['id'] }}" class="my-2 border border-gray" style="width: 375px">
                  <h6 class="text-muted">К записи прикреплено видео:</h6>
                  <h6 class="video-title"></h6>

                    <div class="embed-responsive embed-responsive-16by9">
                      <iframe class="embed-responsive-item" src="" allowfullscreen></iframe>
                    </div>

                  <p class="lh-m text-muted video-description"></p>
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

                <div data-toggle="tooltip" title="Комментарии" style="color: #004aad" class="p-0 mt-0 text-nowrap cursor-pointer mx-2 ajax-comment shadow-none comment" event_url="{{ $item['event_url'] }}" name="{{ $item['id'] }}"><i class="icon far fa-comments comments"></i><span class="spinner-border spinner-border-sm mx-2 d-none"> </span></div>

                <div data-toggle="tooltip" title="Лайки" class="text-nowrap mx-2 like">
                  <i class="far fa-heart likes"></i>
                </div>

                <div data-toggle="tooltip" title="Репосты" class="text-nowrap mx-2 repost">
                  <i class="far fa-bullhorn reposts"></i>
                </div>

                <div data-toggle="tooltip" title="Просмотры" class="text-nowrap mx-2 view">
                  <i class="far fa-eye views"></i>
                </div>

            @endif
          </div>
          <div class="text-truncate">
            <span class="d-none d-md-inline">Правило: </span><mark><a class="text-muted ajax-post" data-toggle="tooltip" title="Показать записи только с этой меткой" href = "{{ route('stream') }}/{{ $info['project_name'] }}/?rule={{ str_replace(session('vkid'), '', $item['user']) }}">{{ str_replace(session('vkid'), '', $item['user']) }}</a></mark>
          </div>
        </div>

        <div class="bg-light" id="comment_{{ $item['id'] }}"></div>

      </div>
    @empty
      @if (empty($dublikat_render))
        <p class="alert alert-warning">Не нашлось ни одной записи</p>
      @endif
    @endforelse

    {{ $items->onEachSide(4)->links() }}

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
@elseif (!empty($items))
<div class="table-responsive">
  <link rel="stylesheet" type="text/css" href="//cdn.datatables.net/1.10.21/css/jquery.dataTables.min.css" >
	<script src="//cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
	<script>
	$(document).ready( function () {

    $("#table-authors").DataTable({
		"language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.21/i18n/Russian.json"
        },
		"lengthMenu": [ 100, 250, 500, 750, 1000 ],
		"pageLength": 100,
	});
	} );
	</script>
  <table id="table-authors" class="lh-m d-table sortable table table-striped table-hover table-sm">
    <thead class="thead-dark">
      <tr>
        <th>№</th>
        <th>Имя/Название</th>
        <th>Страна</th>
        <th>Город</th>
        <th>Подписч</th>
        <th>Пол</th>
        <th>Возраст</th>
        <th>Активн</th>
      </tr>
    </thead>
    <tbody>
      @foreach ($items as $item)
        <tr>
          <td class="">{{ $loop->iteration }}</td>
          <td class="">{!! $item['author_id'] !!}</td>
          <td class="">{{ $item['country'] }}</td>
          <td class="">{{ $item['city'] }}</td>
          <td class="text-center"> @if($item['members_count'] >= 0) {{ $item['members_count'] }} @endif</td>
          <td class="">{{ $item['sex'] }}</td>
          <td class="text-center"> @if (!empty($item['age'])) {{ $item['age'] }} @endif</td>
          <td class="text-center">{{ $item['cnt'] }}</td>
        </tr>
      @endforeach
    <tbody>
  </table>
</div>
@else <p class="alert alert-warning">Не нашлось ни одного автора по фильтрам</p>
@endif
</div>

@if (($request->apply_filter == 'Показать записи' || empty($request->apply_filter)))
  <div id_id="{{ serialize(array_column($items->items(), 'id')) }}" author_id="{{ serialize(array_column($items->items(), 'original_author_id')) }}" post_id="{{ serialize(array_column($items->items(), 'post_id')) }}" event_type="{{ serialize(array_column($items->items(), 'event_type')) }}" event_url="{{ serialize(array_column($items->items(), 'event_url')) }}" video_player="{{ serialize(array_column($items->items(), 'video_player')) }}" id="serialize"></div>
  <script type="text/javascript">
      $(document).ready(function () {

        var id_id = $('#serialize').attr('id_id');
        var event_url = $('#serialize').attr('event_url');
        var author_id = $('#serialize').attr('author_id');
        var post_id = $('#serialize').attr('post_id');
        var video_player = $('#serialize').attr('video_player');
        var event_type = $('#serialize').attr('event_type');
        $.ajax({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            type: 'POST',
            url: "{{ route('video-likes', $info['project_name']) }}",
            data: {'event_url' :  event_url, 'video_player' : video_player, 'event_type' : event_type, 'post_id' : post_id, 'author_id' : author_id, 'id' : id_id},
            beforeSend: function () {

            },
            success: function (data) {
              //var data = $.parseJSON(data);
                if(data.success == true) {
                  $.each(JSON.parse(data.post), function(key, value) {
                      const entries = Object.entries(value)
                      entries.forEach(function(entry) {
                          $('#record'+key+' .'+entry[0]).html('<span style="font-family: system-ui;"> '+entry[1].toLocaleString('ru-RU')+'</span>');
                      });
                  })
                  $.each(JSON.parse(data.video), function(key, value) {
                      const entries = Object.entries(value)
                      entries.forEach(function(entry) {
                          $('#video'+entry[0]+'_'+key+' .video-title').text(entry[1].title);
                          $('#video'+entry[0]+'_'+key+' .video-description').text(entry[1].description);
                          if (entry[1].player) $('#video'+entry[0]+'_'+key+' .embed-responsive-item').prop('src', entry[1].player);
                      });
                  })

                } else {
                  $('.toast-header').addClass('bg-danger');
                  $('.toast-header').removeClass('bg-success');
                  $('.toast-body').html('Не удалось получить информацию о Видео, лайках, комментариях, репостах и просмотрах');
                  $('.toast').toast('show');
                }
            },
        });
      });
  </script>
@endif
