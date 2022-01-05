
$(document).ready(function () {
  $(":submit:not(.enabled)").prop('disabled', false);
});

$('body').tooltip({
        selector: '[data-toggle="tooltip"], [title]:not([data-toggle="popover"])',
        trigger: 'hover',
        container: 'body'
    }).on('click mousedown mouseup', '[data-toggle="tooltip"], [title]:not([data-toggle="popover"])', function () {
        $('[data-toggle="tooltip"], [title]:not([data-toggle="popover"])').tooltip('dispose');
    });

$(document).ready(function () {
  $('[data-toggle="popover"]').popover();
})

function soundClick() {
  var audio = new Audio();
  audio.src = '/sounds/ding.mp3';
  audio.autoplay = true;
}

var old_title = document.title;
var changeTitle = function() {
    this.title = function () {
        var title = document.title;
        document.title = (title == "Сбор окончен" ? old_title : "Сбор окончен");
    }
};
var timerTitle = new changeTitle();

changeTitle.prototype.start = function() {
    this.timer = setInterval(this.title, 1000);
};

changeTitle.prototype.stop = function() {
    clearInterval(this.timer)
};

window.onfocus = function() {
    timerTitle.stop();
    document.title = old_title;
};

var select_options = 0;
$(document).ready(function () {
  $(document).on('click', '.select_options', function (e) {
    if ($(this).prop('checked')) select_options++;
    else select_options--;
    if ($(this).prop('checked') && select_options > 2) {
      $('.toast-header').addClass('bg-danger');
      $('.toast-header').removeClass('bg-success');
      $('.toast-body').html('Если выбрать много опций, которые уточняют подписчиков, их может найтись мало, или не найтись вовсе');
      $('.toast').toast('show');
    }
  });
});

$(document).ready(function () {
  $(document).on('click', '#js-load', function (e) {
        e.preventDefault();
    if (typeof (process1) !== 'undefined') {
        $('#table-search').removeClass('d-none');
        var _this = $(this);

      if (process1 == 'simple_search' && $('#group-name').val().length < 3) {
            var groupname = document.getElementById("group-name");
            groupname.setAttribute('placeholder', 'Минимум 3 символа');
      }
      else {
        $.ajax({
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            type: 'POST',
            url: url,
            data: $('#search-submit').serialize(),
            beforeSend: function () {
              // далем кнопку недоступной и отображаем спиннер
              _this
                .prop('disabled', true)
                .find('.fa-search').addClass('d-none');
                _this.find('.spinner-border-sm').removeClass('d-none');

				var answer = 0;
        var zero_answer = 0;
        var response = 0;
                var elem = document.getElementById("progress");
                var elem2 = document.getElementById("progress-text");
				var width_old = -1;
                var width = 0;
                var info = '';
                var id = setInterval(frame, 500);
               async function frame() {

                   let user = {
                     vkid: vkid,
                     process: process1
                   };
                     answer = await fetch("/progress", {
                       method: 'POST',
                       headers: {
                         'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                         'Content-Type': 'application/json;charset=utf-8'
                       },
                       body: JSON.stringify(user)
                     }),
                     response = await answer.json();
					 width = response.width;
           info = response.info;
					 if (width >= width_old || width == 0) {
						 elem.style.width = response.width + '%';
						 elem.innerHTML = Math.floor(response.width) * 1  + '%';
						 elem2.innerHTML = response.info;
                     }
					 width_old = response.width;
					 if (width == 0 && info == '') zero_answer++; if (zero_answer > 10) {
                        clearInterval(id);
                      }
               }
            },
            success: function (data) {
              //var data = $.parseJSON(data);
                if(data.success == true) {
                    $('#table-search').html(data.html);
                    _this
                      .prop('disabled', false)
                      .find('.fa-search').removeClass('d-none');
                      _this.find('.spinner-border-sm').addClass('d-none');

                      if (process1 == 'groupsearch' || process1 == 'getusers') {
                        $('#new-search').removeClass('d-none');
                        _this.addClass('d-none');
                        $('.search-form').addClass('d-none');
                      }
                      if (process1 == 'new-users') {
                        $('.alert-success:not(.w-100)').addClass('d-none');
                        // $('.search-form').addClass('d-none');
                        $('#table-search .search-form').removeClass('d-none');
                      }
                      if (process1 == 'getusers' || process1 == 'auditoria') {
                        if (document.hidden) {
                          timerTitle.start();
                        }
                        soundClick();
                      }
                    window.location = "#table-search";
                } else {
                  $('.toast-header').addClass('bg-danger');
                  $('.toast-header').removeClass('bg-success');
                  $('.toast-body').html('Что-то пошло не так. Попробуйте ещё раз или сообщите нам');
                  $('.toast').toast('show');
                }
            },

        });
      }
    }});
});


$(document).ready(function () {
  $(document).on('click', '.follow', function (e) {
    e.preventDefault();
    var _this = $(this);
    $.ajax({
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        type: 'POST',
        url: '/follow-group',
        data: $('.follow-form').serialize() + '&' + this.name + '=' + this.value,
        beforeSend: function () {
                _this
                  .prop('disabled', true)
                  .find('.fa-search').addClass('d-none');
                  _this.find('.spinner-border-sm').removeClass('d-none');

              var answer = 0;
              var zero_answer = 0;
              var response = 0;
                      var elem = document.getElementById("progress");
                      var elem2 = document.getElementById("progress-text");
      				var width_old = -1;
              var width = 0;
              var info = '';
              var id = setInterval(frame, 500);
              async function frame() {

                         let user = {
                           vkid: vkid,
                           process: process1
                         };
                           answer = await fetch("/progress", {
                             method: 'POST',
                             headers: {
                               'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                               'Content-Type': 'application/json;charset=utf-8'
                             },
                             body: JSON.stringify(user)
                           }),
                           response = await answer.json();
      					 width = response.width;
                 info = response.info;
      					 if (width >= width_old || width == 0) {
      						 elem.style.width = response.width + '%';
      						 elem.innerHTML = Math.floor(response.width) * 1  + '%';
      						 elem2.innerHTML = response.info;
                           }
      					 width_old = response.width;
      					 if (width == 0 && info == '') zero_answer++; if (zero_answer > 10) {
                              clearInterval(id);
                            }
            }
        },
        success: function (data) {
          //var data = $.parseJSON(data);
            if(data.success == true) {
                $('#table-search').html(data.html);
                _this
                  .prop('disabled', false)
                  .find('.fa-search').removeClass('d-none');
                  _this.find('.spinner-border-sm').addClass('d-none');
                // $('#new-search').removeClass('d-none');
                $('#table-search').removeClass('d-none');
                // $('#js-load').addClass('d-none');
                // $('.search-form').addClass('d-none');
                if (document.hidden) {
                  timerTitle.start();
                }
                soundClick();
                window.location = "#table-search";
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
    $.ajaxSetup({
        statusCode: {
            419: function(){
                    location.reload();
                }
        }
    });
});
