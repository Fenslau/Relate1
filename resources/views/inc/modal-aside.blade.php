<form class="" action="{{ route('stream-add-project') }}" method="post">
@csrf
<div class="modal fade" id="add_project" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="add_projectLabel">Добавить проект</h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Создание нового проекта для новой серии правил. Разные проекты удобны тем, что в них можно размещать правила на разную тематику.</p>
        <input class="form-control" type="text" name="project_name" placeholder="Любое название">
      </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
          <button type="submit" class="btn btn-primary">Сохранить</button>
        </div>
      </div>
    </div>
  </div>
  </form>


<form class="" action="{{ route('link-add') }}" method="post">
@csrf
<div class="modal fade" id="add_link" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="add_linkLabel">Добавить папку</h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <p>Пользовательские папки нужны для сортировки записей для вашего удобства. После создания такой папки, рядом с каждым постом появится возможность переместить его в эту или другую папку.</p>
        <input class="form-control" type="text" name="link_name" placeholder="Любое название">
        <input type="hidden" name="project_name" value="{{ $info['project_name'] }}">
      </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
          <button type="submit" class="btn btn-primary">Сохранить</button>
        </div>
      </div>
    </div>
  </div>
  </form>


<form class="" action="{{ route('rule-add') }}" method="post">
@csrf
<div class="modal fade" id="add_rule" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="add_ruleLabel">Добавить правило</h5>
        <button type="button" class="close" data-dismiss="modal">
          <span>&times;</span>
        </button>
      </div>
      <div class="lh-m modal-body">


          <h6>1. Придумайте название правила:</h6>
          <input class="form-control form-control-sm" type="text" name="rule_tag" placeholder="Любое название">
          <h6>2. Выберите метод сбора постов/упоминаний:</h6>
          <label><input type="radio" name="mode" value="1"> По названию бренда/устойчивому выражению (Пример: "Красная Москва" или "взлетная полоса")</label>
          <label><input type="radio" name="mode" value="3"> По ключевым словам, которые одновременно присутствуют в тексте, задают
          тематику, но не связаны между собой (Пример: COVID, безопасный, путешествие, ограничения)</label>
            <div class="block-text" id="block-1">
              <h6>3. Введите название бренда или напишите устойчивое выражение</h6>
              от 1 до 5 слов:
              <input class="form-control form-control-sm" id="brand" type="text" name="brand" placeholder="Например, Красная Москва">
              <h6>4. Добавить вспомогательные слова для создания тематики?</h6>
                <div class="custom-control custom-switch">
                  <input type="checkbox" name="add_help_words" class="custom-control-input" id="add_help_words">
                  <label class="custom-control-label" for="add_help_words"></label>
                </div>
                <div class="block-text_help_words" id="block-true">
                  <h6>5. Введите вспомогательные слова</h6>
                  От 1 до 5 слов. Все слова будут встречаться в каждом посте. Если слова должны стоять рядом, пишите через "+" (новый+год):
                  <input class="form-control form-control-sm" id="help_words" type="text" name="help_words" placeholder="парфюм элитный">
                </div>
            </div>
          <div class="block-text" id="block-3">
            <h6>3. Введите ключевые слова для поиска постов/упоминаний</h6>
            До 5 слов:
            <input class="form-control form-control-sm" id="key_words" type="text" name="key_words" placeholder="COVID безопасный путешествие ограничения">
          </div>
          <h6>Введите минус-слова:</h6>
          <input class="form-control form-control-sm" id="minus_words" type="text" name="minus_words" placeholder="-кот -кошка -собака">
          <input type="hidden" name="project_name" value="{{ $info['project_name'] }}">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
        <button type="submit" class="btn btn-primary">Сохранить</button>
      </div>
    </div>
  </div>
</div>
</form>

  <script>
    mode = 0;
    $("input[name=mode]").click(function(){
      var target = $("#block-" + $(this).val());
      $(".block-text").not(target).hide(0);
      target.fadeIn(500);
      mode = $(this).val();
    });
    $("input[name=add_help_words]").change(function(){
      var target = $("#block-" + $(this).prop('checked'));
      $(".block-text_help_words").not(target).hide(0);
      target.fadeIn(500);
    });
  </script>



@if (!empty($rules))

  @foreach ($rules as $rule)
  <form class="" action="{{ route('rule-edit') }}" method="post">
  @csrf
  <div class="modal fade" id="edit_rule_{{ $rule['id'] }}" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="edit_rule_{{ $rule['id'] }}Label">Редактировать правило {{ $rule['rule'] }}</h5>
          <button type="button" class="close" data-dismiss="modal">
            <span>&times;</span>
          </button>
        </div>
        <div class="lh-m modal-body">


            <h6>1. Придумайте название правила:</h6>
            <input class="form-control form-control-sm" type="text" name="rule_tag" value="{{ $rule['rule'] }}" readonly>
            <h6>2. Выберите метод сбора постов/упоминаний:</h6>
            <label><input type="radio" name="mode_{{ $rule['id'] }}" value="1"> По названию бренда/устойчивому выражению (Пример: "Красная Москва" или "взлетная полоса")</label>
            <label><input type="radio" name="mode_{{ $rule['id'] }}" value="3"> По ключевым словам, которые одновременно присутствуют в тексте, задают
            тематику, но не связаны между собой (Пример: COVID, безопасный, путешествие, ограничения)</label>
              <div class="block-text" id="block-1{{ $rule['id'] }}">
            		<h6>3. Введите название бренда или напишите устойчивое выражение</h6>
            		от 1 до 5 слов:
            		<input class="form-control form-control-sm" id="brand_{{ $rule['id'] }}" type="text" name="brand" value="{{ $rule['mode1_edit'] }}" placeholder="Например, Красная Москва">
            		<h6>4. Добавить вспомогательные слова для создания тематики?</h6>
                  <div class="custom-control custom-switch">
                    <input type="checkbox" name="add_help_words_{{ $rule['id'] }}" class="custom-control-input" id="add_help_words_{{ $rule['id'] }}">
                    <label class="custom-control-label" for="add_help_words_{{ $rule['id'] }}"></label>
                  </div>
            			<div class="block-text_help_words" id="block-true{{ $rule['id'] }}">
            				<h6>5. Введите вспомогательные слова</h6>
            				От 1 до 5 слов. Все слова будут встречаться в каждом посте. Если слова должны стоять рядом, пишите через "+" (новый+год):
            				<input class="form-control form-control-sm" id="help_words_{{ $rule['id'] }}" type="text" name="help_words" value="{{ $rule['mode2_edit'] }}" placeholder="парфюм элитный">
            			</div>
            	</div>
          	<div class="block-text" id="block-3{{ $rule['id'] }}">
          		<h6>3. Введите ключевые слова для поиска постов/упоминаний</h6>
          		До 5 слов:
          		<input class="form-control form-control-sm" id="key_words_{{ $rule['id'] }}" type="text" name="key_words" value="{{ $rule['mode3_edit'] }}" placeholder="COVID безопасный путешествие ограничения">
          	</div>
          	<h6>Введите минус-слова:</h6>
          	<input class="form-control form-control-sm" id="minus_words_{{ $rule['id'] }}" type="text" name="minus_words" value="{{ $rule['minus_words'] }}" placeholder="-кот -кошка -собака">
            <input type="hidden" name="project_name" value="{{ $info['project_name'] }}">
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Закрыть</button>
          <button type="submit" class="btn btn-primary">Сохранить</button>
        </div>
      </div>
    </div>
  </div>
  </form>

    <script>
      mode_{{ $rule['id'] }} = 0;
      $("input[name=mode_{{ $rule['id'] }}]").click(function(){
        var target_{{ $rule['id'] }} = $("#block-" + $(this).val() + {{ $rule['id'] }});
        $(".block-text").not(target_{{ $rule['id'] }}).hide(0);
        target_{{ $rule['id'] }}.fadeIn(500);
        mode_{{ $rule['id'] }} = $(this).val();
      });
      $("input[name=add_help_words_{{ $rule['id'] }}]").change(function(){
        var target_{{ $rule['id'] }} = $("#block-" + $(this).prop('checked') + {{ $rule['id'] }});
        $(".block-text_help_words").not(target_{{ $rule['id'] }}).hide(0);
        target_{{ $rule['id'] }}.fadeIn(500);
      });
    </script>
    <script>
      document.getElementById("edit_{{ $rule['id'] }}").onclick = function() {
        if ($("#brand_{{ $rule['id'] }}").val().length) {
          $("input[name=mode_{{ $rule['id'] }}][value=1]").trigger("click");
        }
        if ($("#key_words_{{ $rule['id'] }}").val().length) {
          $("input[name=mode_{{ $rule['id'] }}][value=3]").trigger("click");
        }
        if ($("#help_words_{{ $rule['id'] }}").val().length) {
          $("input[name=add_help_words_{{ $rule['id'] }}]").prop("checked", true);
          $("#block-true" + {{ $rule['id'] }}).fadeIn(500);
        }
      }
    </script>
  @endforeach
@endif
