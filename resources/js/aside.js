$(document).ready(function () {
  $('.check').on('click', function (e) {
    var _this = $(this);
    var url = this.getAttribute('url');
    $.ajax({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        type: 'POST',
        url: '/stream/checkbox/' + this.getAttribute('url'),
        data: {'name' : this.name, 'checked' : _this.prop('checked')},

        success: function (data) {
            if(data.success) {
              $('.toast-header').addClass('bg-success');
              $('.toast-header').removeClass('bg-danger');
              $('.toast-body').html(data.success);
              $('.toast').toast('show');
              if (url == 'trash')  _this.parent().parent().remove();


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
  $('.choose-link').on('change', function (e) {
    var _this = $(this);
    $.ajax({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        type: 'POST',
        url: '/stream/checkbox/user-link',
        data: {'name' : this.name, 'value' : this.value},

        success: function (data) {
            if(data.success) {
              $('.toast-header').addClass('bg-success');
              $('.toast-header').removeClass('bg-danger');
              $('.toast-body').html(data.success);
              $('.toast').toast('show');
              _this.parent().parent().parent().remove();

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
  $('.ajax-aside').on('click', function (e) {
    e.preventDefault();
    var _this = $(this);
    $.ajax({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        type: 'POST',
        url: '/stream/buttons/' + this.getAttribute('url'),
        data: {'project' : this.name, 'tag' : this.value},
        beforeSend: function () {
                _this
                  .prop('disabled', true)
                  .find('.icon').addClass('d-none');
                  _this.find('.spinner-border-sm').removeClass('d-none');
        },
        success: function (data) {
            if(data.success) {
              $('.toast-header').addClass('bg-success');
              $('.toast-header').removeClass('bg-danger');
              $('.toast-body').html(data.success);
              $('.toast').toast('show');
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
  });
});
