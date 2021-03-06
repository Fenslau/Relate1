
  <aside class="aside sticky-top">
    <div class="bg-secondary rounded p-2 text-white">
        <div class="">
          Проекты:
          <form id="project_form" name="project">
            <select class="text-truncate w-100" id="choose_project_name" name="choose_project_name" onchange="if (this.value) window.location.href = this.value">
              <option value="#">Выберите проект</option>
              @forelse ($projects as $project)
                <option {{ ($project == $info['project_name'] ? "selected":"") }} value="{{ $project }}">{{ $project }}</option>
              @empty
                <option value="#">Проектов не найдено</option>
              @endforelse
            </select>
          </form>

          <input class='check check_cut' id='project_cut' type='checkbox' {{ ($cut == 1 ? "checked":"") }} name="{{ $info['project_name'] }}" url="cut">
				  <label class="p-1 m-1 check_cut" data-toggle="tooltip" title="Прятать длинные тексты под кат для этого проекта" for="project_cut" ><i class="fas fa-cut"></i><span class="spinner-border spinner-border-sm d-none"></span></label>
          <div class="d-inline float-right" data-toggle="tooltip" title="Добавить проект">
              <button class="my-1 btn btn-sm btn-light text-success border border-dark shadow-none" type="button" id="add_project_button" data-toggle="modal" data-target="#add_project"><i class="fa fa-plus"></i></button>
          </div>
        </div>
        <hr class="m-1 bg-white" />
        <div class="my-2">
          <div class="text-truncate">Добавить правило:</div>
          <button class="my-1 btn btn-sm btn-light text-success border border-dark shadow-none" type="button" id="add_rule_button" data-toggle="modal" data-target="#add_rule"><i class="fa fa-plus"></i></button>
          @if (!empty($rules))
            <div class="text-truncate">Актуальные правила:</div>
            <form class="" action="{{ route('stream-button', 'ruleDelete') }}" method="post">
              @csrf
              @foreach ($rules as $rule)
                <div class="d-flex">
                  <a href="?rule={{ $rule['rule'] }}"
                    @if (!empty($info['old_post'][session('vkid').$rule['rule']]['retry']))
                      data-toggle="tooltip" title="Сбор старых постов будет продолжен в {{ $info['old_post'][session('vkid').$rule['rule']]['retry'] }}"
                    @endif
                    @if (!empty($info['old_post'][session('vkid').$rule['rule']]['last_date']))
                      data-toggle="tooltip" title="Старые посты собраны до {{ $info['old_post'][session('vkid').$rule['rule']]['last_date'] }}"
                    @endif
                    @if (!empty($info['old_post'][session('vkid').$rule['rule']]['progress']))
                      style="background: linear-gradient(90deg, #fff5d7 0%, #fff5d7 {{ $info['old_post'][session('vkid').$rule['rule']]['progress']-1 }}%, white {{ $info['old_post'][session('vkid').$rule['rule']]['progress'] }}%, white 100%)"
                    @endif
                  class="text-left w-100 text-truncate border border-dark btn btn-sm btn-light ajax-post" type="button">{{ $rule['rule'] }}</a>

                  <div id = "edit_{{ $rule['id'] }}" data-toggle="tooltip" title="Редактировать правило"><button class="border border-dark btn btn-sm btn-light text-info shadow-none" type="button" data-toggle="modal" data-target="#edit_rule_{{ $rule['id'] }}"><i class="far fa-edit"></i></button></div>
                  <button class="border border-dark btn btn-sm btn-light text-warning ajax-aside" type="button" data-toggle="tooltip" title="Стереть посты" name = "{{ $info['project_name'] }}" value="{{ $rule['rule'] }}" url="ruleErasePosts"><i class="icon fas fa-eraser"></i><span class="spinner-border spinner-border-sm d-none"></span></button>
                  <button class="border border-dark btn btn-sm btn-light text-danger" type="submit" name="rule_tag" value="{{ $rule['rule'] }}" data-toggle="tooltip" title="Удалить правило"><i class="far fa-trash-alt"></i></button>
                </div>
              @endforeach
            </form>
          @endif
        </div>

        <div class="my-2">
          @if (!empty($old_rules))
            <div class="text-truncate">Старые правила:</div>
            <form class="" action="{{ route('stream-button', 'oldRuleDelete') }}" method="post">
              @csrf
              @foreach ($old_rules as $rule)
                <div class="d-flex">
                  <a href="?rule={{ $rule }}" class="text-left w-100 text-truncate border border-dark btn btn-sm btn-light ajax-post" type="button">{{ $rule }}</a>
                  <button class="border border-dark btn btn-sm btn-light text-danger" type="submit" data-toggle="tooltip" title="Удалить окончательно" name="rule_tag" value="{{ $rule }}"><i class="far fa-trash-alt"></i></button>
                </div>
              @endforeach
            </form>
          @endif
        </div>

        <div class="my-2">

            <div class="text-truncate">Пользовательские папки:</div>
            <div class="d-inline" data-toggle="tooltip" title="Добавить папку">
              <button class="my-1 btn btn-sm btn-light text-success border border-dark" type="button" id="add_link_button" data-toggle="modal" data-target="#add_link"><i class="fa fa-plus"></i></button>
            </div>
          @if (!empty($links))
            <form class="" action="{{ route('stream-button', 'userLinksDelete') }}" method="post">
              @csrf
              @foreach ($links as $rule)
                <div class="d-flex">
                  <a href="?user_link={{ $rule }}" class="text-left w-100 text-truncate border border-dark btn btn-sm btn-light ajax-post" type="button">{{ $rule }}</a>
                  <button class="border border-dark btn btn-sm btn-light text-warning ajax-aside" type="button" data-toggle="tooltip" title="Стереть посты" name="{{ $info['project_name'] }}" value="{{ $rule }}" url="userLinksErasePosts"><i class="icon fas fa-eraser"></i><span class="spinner-border spinner-border-sm d-none"></span></button>
                  <button class="border border-dark btn btn-sm btn-light text-danger" type="submit" data-toggle="tooltip" title="Удалить папку" name="user_link" value="{{ $rule }}"><i class="far fa-trash-alt"></i></button>
                  <input type="hidden" name="project" value="{{ $info['project_name'] }}">
                </div>
              @endforeach
            </form>
          @endif
        </div>

        <div class="my-2">
            <div class="text-truncate">Ссылки:</div>
              <div class="d-flex">
                <a href="?flag=1" class="text-left w-100 text-truncate border border-dark btn btn-sm btn-light ajax-post" type="button">Избранное <i class="fas fa-star text-warning"></i></a>
                <button class="border border-dark btn btn-sm btn-light text-warning ajax-aside" type="button" data-toggle="tooltip" title="Стереть посты" name = "{{ $info['project_name'] }}" url="flagErase"><i class="icon fas fa-eraser"></i><span class="spinner-border spinner-border-sm d-none"></span></button>
              </div>
              <div class="d-flex">
                <a href="?trash=1" class="text-left w-100 text-truncate border border-dark btn btn-sm btn-light ajax-post" type="button">Корзина <i class="fas fa-trash-alt text-danger"></i></a>
                <button class="border border-dark btn btn-sm btn-light text-danger ajax-aside" type="button" data-toggle="tooltip" title="Очистить" name = "{{ $info['project_name'] }}" url="trashErase"><i class="icon far fa-trash-alt"></i><span class="spinner-border spinner-border-sm d-none"></span></button>
              </div>
        </div>

    </div>
  </aside>
