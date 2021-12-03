/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!******************************!*\
  !*** ./resources/js/post.js ***!
  \******************************/
$(document).ready(function () {
  $(document).on('click', '.ajax-comment', function (e) {
    var _this = $(this);

    $('#comment_' + _this.attr('name')).slideToggle();

    if ($('#comment_' + _this.attr('name')).is(':empty')) {
      $('#comment_' + _this.attr('name')).slideToggle();
      $.ajax({
        headers: {
          'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
        type: 'POST',
        url: '/stream/comments',
        data: {
          'event_url': this.getAttribute('event_url')
        },
        beforeSend: function beforeSend() {
          _this.prop('disabled', true).find('.icon').addClass('d-none');

          _this.find('.spinner-border-sm').removeClass('d-none');
        },
        success: function success(data) {
          if (data.success) {
            $('#comment_' + _this.attr('name')).html(data.html);

            _this.prop('disabled', false).find('.icon').removeClass('d-none');

            _this.find('.spinner-border-sm').addClass('d-none');
          } else {
            $('.toast-header').addClass('bg-danger');
            $('.toast-header').removeClass('bg-success');
            $('.toast-body').html('Что-то пошло не так. Попробуйте ещё раз или сообщите нам');
            $('.toast').toast('show');
          }
        }
      });
    }
  });
});
/******/ })()
;