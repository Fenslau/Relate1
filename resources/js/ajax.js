
$(document).ready(function () {
  $(":submit").prop('disabled', false);
});

$(document).ready(function () {
    $('#js-load').on('click', function (e) {
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
                        $('.search-form').addClass('d-none');
                        $('#table-search .search-form').removeClass('d-none');

                      }

                } else {
                  alert('Что-то пошло не так. Попробуйте ещё раз или сообщите нам.');
                }
            },

        });
      }
    }});
});


$(document).ready(function () {
  $('.follow').on('click', function (e) {
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
                $('#new-search').removeClass('d-none');
                $('#table-search').removeClass('d-none');
                $('#js-load').addClass('d-none');
                $('.search-form').addClass('d-none');

            } else {
              alert('Что-то пошло не так. Попробуйте ещё раз или сообщите нам.');
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
