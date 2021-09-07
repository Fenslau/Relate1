
$(document).ready(function () {
  $(":submit").prop('disabled', false);
});

$(document).ready(function () {
    $('#js-load').on('click', function (e) {
        e.preventDefault();
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
        var cloneNode = 'd';
        var zero_answer = 0;
        var response = 0;
                var elem = document.getElementById("progress");
                var elem2 = document.getElementById("progress-text");
				var width_old = -1;
                var width = 0;
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
					 if (width >= width_old || width == 0) {
						 elem.style.width = response.width + '%';
						 elem.innerHTML = Math.floor(response.width) * 1  + '%';
						 elem2.innerHTML = response.info;
                     }
					 width_old = response.width;
					 if (width == 0) zero_answer++; if (zero_answer > 10) {
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

                      if (process1 == 'open_wall_search') {
                        $('#new-search').removeClass('d-none');
                        _this.addClass('d-none');
                        $('.search-form').addClass('d-none');
                      }

                } else {
                  alert('Что-то пошло не так. Попробуйте ещё раз или сообщите нам.');
                }
            },

        });
      }
    });
});
