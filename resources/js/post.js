
$(document).ready(function () {
      $(document).on('click', '.filter-form-submit', function (e) {
        e.preventDefault();
        var _this = $(this);
        var _text = _this.attr('value');
        var data = $('.filter-form').serialize();

        $.ajax({
          type: 'GET',
          data: data + '&apply_filter=' + _text,
          beforeSend: function () {
              _this.prop('disabled', true)
              .find('.icon').addClass('d-none');
              _this.find('.spinner-border-sm').removeClass('d-none');
          },
          success: function (data) {
              if(data.success) {
                $('#posts').html(data.html);
                window.location = "#begin";
                _this.prop('disabled', false)
                .find('.icon').removeClass('d-none');
                _this.find('.spinner-border-sm').addClass('d-none');
              } else {
                $('.toast-header').addClass('bg-danger');
                $('.toast-header').removeClass('bg-success');
                $('.toast-body').html('Что-то пошло не так. Попробуйте ещё раз или сообщите нам');
                $('.toast').toast('show');
              }
          },
      });
    });
});


$(document).ready(function () {
    $(document).on('click', '.page-link, .ajax-post', function (e) {
      e.preventDefault();
      var _this = $(this);
      var url = $(this).attr('href');
      var _text = this.innerHTML;
      $.ajax({
          headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
          type: 'GET',
          url: url,

          beforeSend: function () {
                  _this.addClass('px-2');
                  _this.html('<span class="spinner-border spinner-border-sm"></span>');
          },
          success: function (data) {
              if(data.success) {
                $('#posts').html(data.html);
                window.location = "#begin";
                _this.html(_text);

              } else {
                $('.toast-header').addClass('bg-danger');
                $('.toast-header').removeClass('bg-success');
                $('.toast-body').html('Что-то пошло не так. Попробуйте ещё раз или сообщите нам');
                $('.toast').toast('show');
              }
          },
      });
    });
});


$(document).ready(function () {
  $(document).on('click', '.ajax-comment', function (e) {
    var _this = $(this);
    $('#comment_' + _this.attr('name')).slideToggle();
    if( $('#comment_' + _this.attr('name')).is(':empty')) {
      $('#comment_' + _this.attr('name')).slideToggle();
      $.ajax({
          headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
          type: 'POST',
          url: '/stream/comments',
          data: {'event_url' : this.getAttribute('event_url')},
          beforeSend: function () {
                  _this
                    .prop('disabled', true)
                    .find('.icon').addClass('d-none');
                    _this.find('.spinner-border-sm').removeClass('d-none');
          },
          success: function (data) {
              if(data.success) {
                $('#comment_' + _this.attr('name')).html(data.html);

                _this
                  .prop('disabled', false)
                  .find('.icon').removeClass('d-none');
                  _this.find('.spinner-border-sm').addClass('d-none');
              } else {
                $('.toast-header').addClass('bg-danger');
                $('.toast-header').removeClass('bg-success');
                $('.toast-body').html('Что-то пошло не так. Попробуйте ещё раз или сообщите нам');
                $('.toast').toast('show');
              }
          },
      });
    }
  });
});
