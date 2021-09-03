
$(document).ready(function () {
  $('#js-load').prop('disabled', false);
});
$(document).ready(function () {
    $('#js-load').on('click', function (e) {
        e.preventDefault();
        var _this = $(this);
        $_token = "{{ csrf_token() }}";
      if ($('#group-name').val().length > 2) {
        $.ajax({
            headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') },
            type: 'POST',
            url: '/',
            data: $('#search-submit').serialize(),
            beforeSend: function () {
              // далем кнопку недоступной и отображаем спиннер
              _this
                .prop('disabled', true)
                .find('.fa-search').addClass('d-none');
                _this.find('.spinner-border-sm').removeClass('d-none');

                var elem = document.getElementById("progress");
                var elem2 = document.getElementById("progress-text");
                var width = 0;
                var id = setInterval(frame, 500);
               async function frame() { 

                   $_token = "{{ csrf_token() }}";
                   let user = {
                     vkid: vkid,
                     process: "simple_search"
                   };
                     answer = await fetch("/progress", {
                       method: 'POST',
                       headers: {
                         'X-CSRF-Token' : $('meta[name=_token]').attr('content'),
                         'Content-Type': 'application/json;charset=utf-8'
                       },
                       body: JSON.stringify(user)
                     }),
                     response = await answer.json();
                     elem.style.width = response.width + '%';
                     elem.innerHTML = response.width * 1  + '%';
                     $('#progress-text').addClass('bg-secondary');
                     elem2.innerHTML = response.info;
                     width = response.width;
                     if (width >= 100) {
                        $('#progress-text').removeClass('bg-secondary');
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
                } else {
                    alert('Что-то пошло не так... Попробуйте позже, или сообщите нам.');
                }
            },

        });
      }
      else {
        var groupname = document.getElementById("group-name");
        groupname.setAttribute('placeholder', 'Минимум 3 символа');
      }
    });
});
