@if (!empty($items))
  @foreach ($items as $item)
    <div style="max-width: max-content" class="rounded shadow bg-white p-2 m-2 @php if (!empty($item['parent_comment_id'])) echo 'ml-4'; @endphp ">
        <b><a class = "vk-name" rel="nofollow" target="_blank" href="https://vk.com/id{{ $item['from_id'] }}">{{ $item['name'] }}</a></b>:
        @if (!empty($item['data']))
          {!! $item['data'] !!}
        @endif
        @if (!empty($item['note']))
          {!! $item['note'] !!}
        @endif
        @if (!empty($item['sticker']))
          <div class = "photos d-flex align-items-start flex-wrap">
              @foreach (array_diff(explode('9GZVNyidgk', $item['sticker']), array('')) as $sticker1)
                <img loading="lazy" class="m-2 img-fluid" src="{{ $sticker1 }}">
              @endforeach
          </div>
        @endif
        @if (!empty($item['photo']))
          <div class = "photos d-flex align-items-start flex-wrap">
            @foreach (array_diff(explode('9GZVNyidgk', $item['photo']), array('')) as $photo1)
              <img loading="lazy" class="m-2 rounded img-fluid" src="{{ $photo1 }}">
            @endforeach
          </div>
        @endif
        @if (!empty($item['audio']))
          @foreach (array_diff(explode("9GZVNyidgk", $item['audio']), array('')) as $audio1)
            <div class="text-truncate text-info">{{ $audio1 }}</div>
          @endforeach
        @endif
        @if (!empty($item['video_players']))
          @foreach ($item['video_players'] as $video_player)
            <div class="video">
              @if ($video_player != 'FALSE')
                <div class="">
                  <iframe class="embed-responsive-item" src="{{ $video_player }}" allowfullscreen></iframe>
                </div>
              @else <p class="m-0 alert alert-secondary">Видео можно посмотреть только перейдя по ссылке поста</p>
              @endif
            </div>
          @endforeach
        @endif
        @if (!empty($item['linkr']))
          @foreach (array_diff(explode(',', $item['linkr']), array('')) as $link)
            <div class="text-info">
              <p class="text-truncate"><span class="d-none d-md-inline">К комментарию прикреплена</span> ссылка: <a rel="nofollow" target="_blank" href = "{{ $link }}">{{ $link }}</a></p>
            </div>
          @endforeach
        @endif

        @if (!empty($item['doc']))
          @foreach (array_diff(explode(",", $item['doc']), array('')) as $doc1)
            <div class="text-info">
              <p class="text-truncate"><span class="d-none d-md-inline">К комментарию прикреплён</span> документ: <a rel="nofollow" target="_blank" href = "{{ $doc1 }}">{{ $doc1 }}</a></p>
            </div>
          @endforeach
        @endif

        @if(!empty($item['action_time']))
          <div class="text-muted text-right">
            <small>{{ strftime('%d %b %yг.  %R', $item['action_time']) }}</small>
          </div>
        @endif
    </div>
  @if (!empty($item['html']))
    {!! $item['html'] !!}
  @endif
  @endforeach
@endif
