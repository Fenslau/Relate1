<button id="ignored_authors_btn" data-toggle="popover-ignore" class="btn btn-sm btn-outline-info no-outline">Игнор-лист</button>
<script>
  $(function () {
      $('[data-toggle="popover-ignore"]').popover({
      container: 'body',
      html: true,
      placement: 'top',
      sanitize: false,
      title: `Список игнорируемых авторов:`,
      content:
      `<div id="ignore-list"></div>`
      })
  });

  $('body').on('click', '#ignored_authors_btn', function() {
      $.ajax({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        type: 'POST',
        url: '{{ route('ignore-list', $info['project_name']) }}',

        success: function(data){
          if (data.success) {
            $('#ignore-list').html(data.html);
            var body = document.getElementsByTagName('body')[0];
              var event = new CustomEvent("scroll", {
                detail: {
                  scrollTop: 1
                }
              });
              window.dispatchEvent(event);

          } else {
            $('.toast-header').addClass('bg-danger');
            $('.toast-header').removeClass('bg-success');
            $('.toast-body').html('Что-то пошло не так. Попробуйте ещё раз или сообщите нам');
            $('.toast').toast('show');
          }
        }
      });
  });

  $('body').on('click', '.fa-times', function() {
    var clickId = $(this).attr("ignoreid");
    var _this = $(this);
      $.ajax({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        type: 'POST',
        url: '{{ route('del-from-ignore', $info['project_name']) }}',
        data: {'vkid' : {{ session('vkid') }}, 'id' : clickId},
        success: function(data){
          if (data.success) {
            _this.parent().remove();
            $('.toast-header').addClass('bg-success');
            $('.toast-header').removeClass('bg-danger');
            $('.toast-body').html(data.success);
            $('.toast').toast('show');
          } else {
            $('.toast-header').addClass('bg-danger');
            $('.toast-header').removeClass('bg-success');
            $('.toast-body').html('Что-то пошло не так. Попробуйте ещё раз или сообщите нам');
            $('.toast').toast('show');
          }
        }
      });
  });

  $("#del-btn").click(function(){
    let author_ids = "";
    let ignore_author = "";
    var _this = $(this);
    $("input[id*=_del]:checkbox:checked").each(function(){
      if ($(this).val()=="on") author_ids+=($(this).attr("id").replace("_del", "") + ",");
    });
    $("input[id*=ignore_author]:checkbox:checked").each(function(){
      if ($(this).val()=="on") ignore_author="on";
    });
    if (author_ids) {
      author_array = author_ids.split(",");
              $.ajax({
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type: 'POST',
                url: '{{ route('add-to-ignore', $info['project_name']) }}',
                data: {'vkid' : {{ session('vkid') }}, 'ignore' : ignore_author, 'author_id' : author_ids},
                beforeSend: function () {
                        _this
                          .prop('disabled', true)
                          .find('.icon').addClass('d-none');
                          _this.find('.spinner-border-sm').removeClass('d-none');
                },
                success: function(data){
                  if (data.success) {
                    author_array.forEach(function(item, i, arr) {
                      if (item) {
                        item = item.replace("_del", "");
                        var elem = document.getElementById(item);
                        while (elem.firstChild) {
                          elem.removeChild(elem.firstChild);
                        }
                      }
                    });
                    $('.toast-header').addClass('bg-success');
                    $('.toast-header').removeClass('bg-danger');
                    $('.toast-body').html(data.success);
                    $('.toast').toast('show');
                  $("#all_checkbox").prop("checked", false);
                  _this
                    .prop('disabled', false)
                    .find('.icon').removeClass('d-none');
                    _this.find('.spinner-border-sm').addClass('d-none');
                } else {
                    $('.toast-header').addClass('bg-danger');
                    $('.toast-header').removeClass('bg-success');
                    $('.toast-body').html('Что-то пошло не так. Попробуйте ещё раз или сообщите нам');
                    $('.toast').toast('show');
                  }}
              });
    }
  });
</script>
