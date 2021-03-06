/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./node_modules/@babel/runtime/helpers/regeneratorRuntime.js":
/*!*******************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/regeneratorRuntime.js ***!
  \*******************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var _typeof = __webpack_require__(/*! ./typeof.js */ "./node_modules/@babel/runtime/helpers/typeof.js").default;

function _regeneratorRuntime() {
  "use strict";
  /*! regenerator-runtime -- Copyright (c) 2014-present, Facebook, Inc. -- license (MIT): https://github.com/facebook/regenerator/blob/main/LICENSE */

  module.exports = _regeneratorRuntime = function _regeneratorRuntime() {
    return exports;
  }, module.exports.__esModule = true, module.exports.default = module.exports;
  var exports = {},
      Op = Object.prototype,
      hasOwn = Op.hasOwnProperty,
      $Symbol = "function" == typeof Symbol ? Symbol : {},
      iteratorSymbol = $Symbol.iterator || "@@iterator",
      asyncIteratorSymbol = $Symbol.asyncIterator || "@@asyncIterator",
      toStringTagSymbol = $Symbol.toStringTag || "@@toStringTag";

  function define(obj, key, value) {
    return Object.defineProperty(obj, key, {
      value: value,
      enumerable: !0,
      configurable: !0,
      writable: !0
    }), obj[key];
  }

  try {
    define({}, "");
  } catch (err) {
    define = function define(obj, key, value) {
      return obj[key] = value;
    };
  }

  function wrap(innerFn, outerFn, self, tryLocsList) {
    var protoGenerator = outerFn && outerFn.prototype instanceof Generator ? outerFn : Generator,
        generator = Object.create(protoGenerator.prototype),
        context = new Context(tryLocsList || []);
    return generator._invoke = function (innerFn, self, context) {
      var state = "suspendedStart";
      return function (method, arg) {
        if ("executing" === state) throw new Error("Generator is already running");

        if ("completed" === state) {
          if ("throw" === method) throw arg;
          return doneResult();
        }

        for (context.method = method, context.arg = arg;;) {
          var delegate = context.delegate;

          if (delegate) {
            var delegateResult = maybeInvokeDelegate(delegate, context);

            if (delegateResult) {
              if (delegateResult === ContinueSentinel) continue;
              return delegateResult;
            }
          }

          if ("next" === context.method) context.sent = context._sent = context.arg;else if ("throw" === context.method) {
            if ("suspendedStart" === state) throw state = "completed", context.arg;
            context.dispatchException(context.arg);
          } else "return" === context.method && context.abrupt("return", context.arg);
          state = "executing";
          var record = tryCatch(innerFn, self, context);

          if ("normal" === record.type) {
            if (state = context.done ? "completed" : "suspendedYield", record.arg === ContinueSentinel) continue;
            return {
              value: record.arg,
              done: context.done
            };
          }

          "throw" === record.type && (state = "completed", context.method = "throw", context.arg = record.arg);
        }
      };
    }(innerFn, self, context), generator;
  }

  function tryCatch(fn, obj, arg) {
    try {
      return {
        type: "normal",
        arg: fn.call(obj, arg)
      };
    } catch (err) {
      return {
        type: "throw",
        arg: err
      };
    }
  }

  exports.wrap = wrap;
  var ContinueSentinel = {};

  function Generator() {}

  function GeneratorFunction() {}

  function GeneratorFunctionPrototype() {}

  var IteratorPrototype = {};
  define(IteratorPrototype, iteratorSymbol, function () {
    return this;
  });
  var getProto = Object.getPrototypeOf,
      NativeIteratorPrototype = getProto && getProto(getProto(values([])));
  NativeIteratorPrototype && NativeIteratorPrototype !== Op && hasOwn.call(NativeIteratorPrototype, iteratorSymbol) && (IteratorPrototype = NativeIteratorPrototype);
  var Gp = GeneratorFunctionPrototype.prototype = Generator.prototype = Object.create(IteratorPrototype);

  function defineIteratorMethods(prototype) {
    ["next", "throw", "return"].forEach(function (method) {
      define(prototype, method, function (arg) {
        return this._invoke(method, arg);
      });
    });
  }

  function AsyncIterator(generator, PromiseImpl) {
    function invoke(method, arg, resolve, reject) {
      var record = tryCatch(generator[method], generator, arg);

      if ("throw" !== record.type) {
        var result = record.arg,
            value = result.value;
        return value && "object" == _typeof(value) && hasOwn.call(value, "__await") ? PromiseImpl.resolve(value.__await).then(function (value) {
          invoke("next", value, resolve, reject);
        }, function (err) {
          invoke("throw", err, resolve, reject);
        }) : PromiseImpl.resolve(value).then(function (unwrapped) {
          result.value = unwrapped, resolve(result);
        }, function (error) {
          return invoke("throw", error, resolve, reject);
        });
      }

      reject(record.arg);
    }

    var previousPromise;

    this._invoke = function (method, arg) {
      function callInvokeWithMethodAndArg() {
        return new PromiseImpl(function (resolve, reject) {
          invoke(method, arg, resolve, reject);
        });
      }

      return previousPromise = previousPromise ? previousPromise.then(callInvokeWithMethodAndArg, callInvokeWithMethodAndArg) : callInvokeWithMethodAndArg();
    };
  }

  function maybeInvokeDelegate(delegate, context) {
    var method = delegate.iterator[context.method];

    if (undefined === method) {
      if (context.delegate = null, "throw" === context.method) {
        if (delegate.iterator["return"] && (context.method = "return", context.arg = undefined, maybeInvokeDelegate(delegate, context), "throw" === context.method)) return ContinueSentinel;
        context.method = "throw", context.arg = new TypeError("The iterator does not provide a 'throw' method");
      }

      return ContinueSentinel;
    }

    var record = tryCatch(method, delegate.iterator, context.arg);
    if ("throw" === record.type) return context.method = "throw", context.arg = record.arg, context.delegate = null, ContinueSentinel;
    var info = record.arg;
    return info ? info.done ? (context[delegate.resultName] = info.value, context.next = delegate.nextLoc, "return" !== context.method && (context.method = "next", context.arg = undefined), context.delegate = null, ContinueSentinel) : info : (context.method = "throw", context.arg = new TypeError("iterator result is not an object"), context.delegate = null, ContinueSentinel);
  }

  function pushTryEntry(locs) {
    var entry = {
      tryLoc: locs[0]
    };
    1 in locs && (entry.catchLoc = locs[1]), 2 in locs && (entry.finallyLoc = locs[2], entry.afterLoc = locs[3]), this.tryEntries.push(entry);
  }

  function resetTryEntry(entry) {
    var record = entry.completion || {};
    record.type = "normal", delete record.arg, entry.completion = record;
  }

  function Context(tryLocsList) {
    this.tryEntries = [{
      tryLoc: "root"
    }], tryLocsList.forEach(pushTryEntry, this), this.reset(!0);
  }

  function values(iterable) {
    if (iterable) {
      var iteratorMethod = iterable[iteratorSymbol];
      if (iteratorMethod) return iteratorMethod.call(iterable);
      if ("function" == typeof iterable.next) return iterable;

      if (!isNaN(iterable.length)) {
        var i = -1,
            next = function next() {
          for (; ++i < iterable.length;) {
            if (hasOwn.call(iterable, i)) return next.value = iterable[i], next.done = !1, next;
          }

          return next.value = undefined, next.done = !0, next;
        };

        return next.next = next;
      }
    }

    return {
      next: doneResult
    };
  }

  function doneResult() {
    return {
      value: undefined,
      done: !0
    };
  }

  return GeneratorFunction.prototype = GeneratorFunctionPrototype, define(Gp, "constructor", GeneratorFunctionPrototype), define(GeneratorFunctionPrototype, "constructor", GeneratorFunction), GeneratorFunction.displayName = define(GeneratorFunctionPrototype, toStringTagSymbol, "GeneratorFunction"), exports.isGeneratorFunction = function (genFun) {
    var ctor = "function" == typeof genFun && genFun.constructor;
    return !!ctor && (ctor === GeneratorFunction || "GeneratorFunction" === (ctor.displayName || ctor.name));
  }, exports.mark = function (genFun) {
    return Object.setPrototypeOf ? Object.setPrototypeOf(genFun, GeneratorFunctionPrototype) : (genFun.__proto__ = GeneratorFunctionPrototype, define(genFun, toStringTagSymbol, "GeneratorFunction")), genFun.prototype = Object.create(Gp), genFun;
  }, exports.awrap = function (arg) {
    return {
      __await: arg
    };
  }, defineIteratorMethods(AsyncIterator.prototype), define(AsyncIterator.prototype, asyncIteratorSymbol, function () {
    return this;
  }), exports.AsyncIterator = AsyncIterator, exports.async = function (innerFn, outerFn, self, tryLocsList, PromiseImpl) {
    void 0 === PromiseImpl && (PromiseImpl = Promise);
    var iter = new AsyncIterator(wrap(innerFn, outerFn, self, tryLocsList), PromiseImpl);
    return exports.isGeneratorFunction(outerFn) ? iter : iter.next().then(function (result) {
      return result.done ? result.value : iter.next();
    });
  }, defineIteratorMethods(Gp), define(Gp, toStringTagSymbol, "Generator"), define(Gp, iteratorSymbol, function () {
    return this;
  }), define(Gp, "toString", function () {
    return "[object Generator]";
  }), exports.keys = function (object) {
    var keys = [];

    for (var key in object) {
      keys.push(key);
    }

    return keys.reverse(), function next() {
      for (; keys.length;) {
        var key = keys.pop();
        if (key in object) return next.value = key, next.done = !1, next;
      }

      return next.done = !0, next;
    };
  }, exports.values = values, Context.prototype = {
    constructor: Context,
    reset: function reset(skipTempReset) {
      if (this.prev = 0, this.next = 0, this.sent = this._sent = undefined, this.done = !1, this.delegate = null, this.method = "next", this.arg = undefined, this.tryEntries.forEach(resetTryEntry), !skipTempReset) for (var name in this) {
        "t" === name.charAt(0) && hasOwn.call(this, name) && !isNaN(+name.slice(1)) && (this[name] = undefined);
      }
    },
    stop: function stop() {
      this.done = !0;
      var rootRecord = this.tryEntries[0].completion;
      if ("throw" === rootRecord.type) throw rootRecord.arg;
      return this.rval;
    },
    dispatchException: function dispatchException(exception) {
      if (this.done) throw exception;
      var context = this;

      function handle(loc, caught) {
        return record.type = "throw", record.arg = exception, context.next = loc, caught && (context.method = "next", context.arg = undefined), !!caught;
      }

      for (var i = this.tryEntries.length - 1; i >= 0; --i) {
        var entry = this.tryEntries[i],
            record = entry.completion;
        if ("root" === entry.tryLoc) return handle("end");

        if (entry.tryLoc <= this.prev) {
          var hasCatch = hasOwn.call(entry, "catchLoc"),
              hasFinally = hasOwn.call(entry, "finallyLoc");

          if (hasCatch && hasFinally) {
            if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0);
            if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc);
          } else if (hasCatch) {
            if (this.prev < entry.catchLoc) return handle(entry.catchLoc, !0);
          } else {
            if (!hasFinally) throw new Error("try statement without catch or finally");
            if (this.prev < entry.finallyLoc) return handle(entry.finallyLoc);
          }
        }
      }
    },
    abrupt: function abrupt(type, arg) {
      for (var i = this.tryEntries.length - 1; i >= 0; --i) {
        var entry = this.tryEntries[i];

        if (entry.tryLoc <= this.prev && hasOwn.call(entry, "finallyLoc") && this.prev < entry.finallyLoc) {
          var finallyEntry = entry;
          break;
        }
      }

      finallyEntry && ("break" === type || "continue" === type) && finallyEntry.tryLoc <= arg && arg <= finallyEntry.finallyLoc && (finallyEntry = null);
      var record = finallyEntry ? finallyEntry.completion : {};
      return record.type = type, record.arg = arg, finallyEntry ? (this.method = "next", this.next = finallyEntry.finallyLoc, ContinueSentinel) : this.complete(record);
    },
    complete: function complete(record, afterLoc) {
      if ("throw" === record.type) throw record.arg;
      return "break" === record.type || "continue" === record.type ? this.next = record.arg : "return" === record.type ? (this.rval = this.arg = record.arg, this.method = "return", this.next = "end") : "normal" === record.type && afterLoc && (this.next = afterLoc), ContinueSentinel;
    },
    finish: function finish(finallyLoc) {
      for (var i = this.tryEntries.length - 1; i >= 0; --i) {
        var entry = this.tryEntries[i];
        if (entry.finallyLoc === finallyLoc) return this.complete(entry.completion, entry.afterLoc), resetTryEntry(entry), ContinueSentinel;
      }
    },
    "catch": function _catch(tryLoc) {
      for (var i = this.tryEntries.length - 1; i >= 0; --i) {
        var entry = this.tryEntries[i];

        if (entry.tryLoc === tryLoc) {
          var record = entry.completion;

          if ("throw" === record.type) {
            var thrown = record.arg;
            resetTryEntry(entry);
          }

          return thrown;
        }
      }

      throw new Error("illegal catch attempt");
    },
    delegateYield: function delegateYield(iterable, resultName, nextLoc) {
      return this.delegate = {
        iterator: values(iterable),
        resultName: resultName,
        nextLoc: nextLoc
      }, "next" === this.method && (this.arg = undefined), ContinueSentinel;
    }
  }, exports;
}

module.exports = _regeneratorRuntime, module.exports.__esModule = true, module.exports.default = module.exports;

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/typeof.js":
/*!*******************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/typeof.js ***!
  \*******************************************************/
/***/ ((module) => {

function _typeof(obj) {
  "@babel/helpers - typeof";

  return (module.exports = _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) {
    return typeof obj;
  } : function (obj) {
    return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj;
  }, module.exports.__esModule = true, module.exports.default = module.exports), _typeof(obj);
}

module.exports = _typeof, module.exports.__esModule = true, module.exports.default = module.exports;

/***/ }),

/***/ "./node_modules/@babel/runtime/regenerator/index.js":
/*!**********************************************************!*\
  !*** ./node_modules/@babel/runtime/regenerator/index.js ***!
  \**********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

// TODO(Babel 8): Remove this file.

var runtime = __webpack_require__(/*! ../helpers/regeneratorRuntime */ "./node_modules/@babel/runtime/helpers/regeneratorRuntime.js")();
module.exports = runtime;

// Copied from https://github.com/facebook/regenerator/blob/main/packages/runtime/runtime.js#L736=
try {
  regeneratorRuntime = runtime;
} catch (accidentalStrictMode) {
  if (typeof globalThis === "object") {
    globalThis.regeneratorRuntime = runtime;
  } else {
    Function("r", "regeneratorRuntime = r")(runtime);
  }
}


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be in strict mode.
(() => {
"use strict";
/*!******************************!*\
  !*** ./resources/js/ajax.js ***!
  \******************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/regenerator */ "./node_modules/@babel/runtime/regenerator/index.js");
/* harmony import */ var _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0__);


function asyncGeneratorStep(gen, resolve, reject, _next, _throw, key, arg) { try { var info = gen[key](arg); var value = info.value; } catch (error) { reject(error); return; } if (info.done) { resolve(value); } else { Promise.resolve(value).then(_next, _throw); } }

function _asyncToGenerator(fn) { return function () { var self = this, args = arguments; return new Promise(function (resolve, reject) { var gen = fn.apply(self, args); function _next(value) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "next", value); } function _throw(err) { asyncGeneratorStep(gen, resolve, reject, _next, _throw, "throw", err); } _next(undefined); }); }; }

var audio = new Audio('/sounds/ding.mp3');
$('body').tooltip({
  selector: '[data-toggle="tooltip"], [title]:not([data-toggle="popover"])',
  trigger: 'hover',
  container: 'body'
}).on('click mousedown mouseup', '[data-toggle="tooltip"], [title]:not([data-toggle="popover"])', function () {
  $('[data-toggle="tooltip"], [title]:not([data-toggle="popover"])').tooltip('dispose');
});

function getRandomInt(max) {
  return Math.floor(Math.random() * max);
}

$(document).ready(function () {
  $('[data-toggle="popover"]').popover();
});

function soundClick() {
  audio.play();
}

var old_title = document.title;

var changeTitle = function changeTitle() {
  this.title = function () {
    var title = document.title;
    document.title = title == "???????? ??????????????" ? old_title : "???????? ??????????????";
  };
};

var timerTitle = new changeTitle();

changeTitle.prototype.start = function () {
  this.timer = setInterval(this.title, 1000);
};

changeTitle.prototype.stop = function () {
  clearInterval(this.timer);
};

window.onfocus = function () {
  timerTitle.stop();
  document.title = old_title;
};

var select_options = 0;
$(document).ready(function () {
  $(document).on('click', '.select_options', function (e) {
    if ($(this).prop('checked')) select_options++;else select_options--;

    if ($(this).prop('checked') && select_options > 2) {
      $('.toast-header').addClass('bg-danger');
      $('.toast-header').removeClass('bg-success');
      $('.toast-body').html('VKToppost ???????????????? ???????????? ???? ????????????????????, ?????????????? ???????????????????????? ???????? ?????????????????? ?? ???????????????? ??????????????. ???????? 15% ?????????????????????????? ?????????????????? ?????????????????? ?????????????????? ???????????? ????????????????. ?????????????? 3 ?? ?????????? ????????????, ???? ???????????? ???????????????????? ???????????????????? ????????????????????, ?????????????? ?????????????????? ???????????????? ?????? ???????????????? ????????????. <b>??????????: ?????? ?????????????? ???????????????????? ?????????????????? ???????????????????? ?????????? ?????? ?????????? ???????? ?????????????????????? ?????? ???????????????? ???????????? ???????????????? ???????????? ????????????????</b>');
      $('.toast').toast('show');
    }
  });
});
$(document).ready(function () {
  $(document).on('click', '#js-load', function (e) {
    e.preventDefault();

    if (typeof process1 !== 'undefined') {
      var _this = $(this);

      var rand = getRandomInt(9999);
      var id = 0;

      if (process1 == 'simple_search' && $('#group-name').val().length < 3) {
        var groupname = document.getElementById("group-name");
        groupname.setAttribute('placeholder', '?????????????? 3 ??????????????');
      } else {
        $.ajax({
          headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
          },
          type: 'POST',
          url: url,
          data: $('#search-submit').serialize() + '&' + 'rand' + '=' + rand,
          beforeSend: function beforeSend() {
            window.onbeforeunload = function () {
              // $('.toast-header').addClass('bg-danger');
              // $('.toast-header').removeClass('bg-success');
              // $('.toast-body').html('???????? ?????????? ????????????????????, ???????? ???? ???????????? ???? ???????????? ???????????????? ??????????');
              // $('.toast').toast('show');
              return false;
            };

            _this.prop('disabled', true).find('.fa-search').addClass('d-none');

            _this.find('.spinner-border-sm').removeClass('d-none');

            var answer = 0;
            var zero_answer = 0;
            var response = 0;
            var elem = document.getElementById("progress");
            var elem2 = document.getElementById("progress-text");
            var width_old = -1;
            var width = 0;
            var info = '';
            id = setInterval(frame, 500);

            function frame() {
              return _frame.apply(this, arguments);
            }

            function _frame() {
              _frame = _asyncToGenerator( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().mark(function _callee() {
                var user;
                return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().wrap(function _callee$(_context) {
                  while (1) {
                    switch (_context.prev = _context.next) {
                      case 0:
                        user = {
                          vkid: vkid,
                          process: process1 + rand
                        };

                        if (document.hidden) {
                          _context.next = 14;
                          break;
                        }

                        _context.next = 4;
                        return fetch("/progress", {
                          method: 'POST',
                          headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                            'Content-Type': 'application/json;charset=utf-8'
                          },
                          body: JSON.stringify(user)
                        });

                      case 4:
                        answer = _context.sent;
                        _context.next = 7;
                        return answer.json();

                      case 7:
                        response = _context.sent;
                        width = response.width;
                        info = response.info;

                        if (width >= width_old || width == 0) {
                          elem.style.width = response.width + '%';
                          elem.innerHTML = Math.floor(response.width) * 1 + '%';
                          elem2.innerHTML = response.info;
                        }

                        width_old = response.width;
                        if (width == 0 && info == '') zero_answer++;

                        if (zero_answer > 30) {
                          clearInterval(id);
                        }

                      case 14:
                      case "end":
                        return _context.stop();
                    }
                  }
                }, _callee);
              }));
              return _frame.apply(this, arguments);
            }
          },
          success: function success(data) {
            //var data = $.parseJSON(data);
            window.onbeforeunload = null;

            if (data.success == true) {
              $('#table-search').html(data.html);

              _this.prop('disabled', false).find('.fa-search').removeClass('d-none');

              _this.find('.spinner-border-sm').addClass('d-none');

              if (process1 == 'groupsearch' || process1 == 'getusers' || process1 == 'topusers') {
                $('#new-search').removeClass('d-none');
                $('#table-search').removeClass('d-none');

                _this.addClass('d-none');

                $('.search-form').addClass('d-none');
              }

              if (process1 == 'new-users') {
                $('.alert-success:not(.w-100)').addClass('d-none');
                $('.search-form').addClass('d-none');
                $('#table-search .search-form').removeClass('d-none');
              }

              if (document.hidden) {
                timerTitle.start();
              }

              soundClick();
              window.location = "#table-search";
            } else {
              $('.toast-header').addClass('bg-danger');
              $('.toast-header').removeClass('bg-success');
              $('.toast-body').html('??????-???? ?????????? ???? ??????. ???????????????????? ?????? ?????? ?????? ???????????????? ??????');
              $('.toast').toast('show');
            }
          },
          complete: function complete() {
            clearInterval(id);
            var elem = document.getElementById("progress");
            var elem2 = document.getElementById("progress-text");
            elem.style.width = '0%';
            elem.innerHTML = 0;
            elem2.innerHTML = '';
          }
        });
      }
    }
  });
});
$(document).ready(function () {
  $(document).on('click', '.follow', function (e) {
    e.preventDefault();

    var _this = $(this);

    var rand = getRandomInt(9999);
    var id = 0;
    $.ajax({
      headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
      },
      type: 'POST',
      url: '/follow-group',
      data: $('.follow-form').serialize() + '&' + this.name + '=' + this.value + '&' + 'rand' + '=' + rand,
      beforeSend: function beforeSend() {
        window.onbeforeunload = function () {
          // $('.toast-header').addClass('bg-danger');
          // $('.toast-header').removeClass('bg-success');
          // $('.toast-body').html('???????? ?????????? ????????????????????, ???????? ???? ???????????? ???? ???????????? ???????????????? ??????????');
          // $('.toast').toast('show');
          return false;
        };

        _this.prop('disabled', true).find('.fa-search').addClass('d-none');

        _this.find('.spinner-border-sm').removeClass('d-none');

        var answer = 0;
        var zero_answer = 0;
        var response = 0;
        var elem = document.getElementById("progress");
        var elem2 = document.getElementById("progress-text");
        var width_old = -1;
        var width = 0;
        var info = '';
        id = setInterval(frame, 500);

        function frame() {
          return _frame2.apply(this, arguments);
        }

        function _frame2() {
          _frame2 = _asyncToGenerator( /*#__PURE__*/_babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().mark(function _callee2() {
            var user;
            return _babel_runtime_regenerator__WEBPACK_IMPORTED_MODULE_0___default().wrap(function _callee2$(_context2) {
              while (1) {
                switch (_context2.prev = _context2.next) {
                  case 0:
                    user = {
                      vkid: vkid,
                      process: process1 + rand
                    };

                    if (document.hidden) {
                      _context2.next = 14;
                      break;
                    }

                    _context2.next = 4;
                    return fetch("/progress", {
                      method: 'POST',
                      headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                        'Content-Type': 'application/json;charset=utf-8'
                      },
                      body: JSON.stringify(user)
                    });

                  case 4:
                    answer = _context2.sent;
                    _context2.next = 7;
                    return answer.json();

                  case 7:
                    response = _context2.sent;
                    width = response.width;
                    info = response.info;

                    if (width >= width_old || width == 0) {
                      elem.style.width = response.width + '%';
                      elem.innerHTML = Math.floor(response.width) * 1 + '%';
                      elem2.innerHTML = response.info;
                    }

                    width_old = response.width;
                    if (width == 0 && info == '') zero_answer++;

                    if (zero_answer > 10) {
                      clearInterval(id);
                    }

                  case 14:
                  case "end":
                    return _context2.stop();
                }
              }
            }, _callee2);
          }));
          return _frame2.apply(this, arguments);
        }
      },
      success: function success(data) {
        window.onbeforeunload = null; //var data = $.parseJSON(data);

        if (data.success == true) {
          $('#table-search').html(data.html);

          _this.prop('disabled', false).find('.fa-search').removeClass('d-none');

          _this.find('.spinner-border-sm').addClass('d-none'); // $('#new-search').removeClass('d-none');


          $('#table-search').removeClass('d-none'); // $('#js-load').addClass('d-none');
          // $('.search-form').addClass('d-none');

          if (document.hidden) {
            timerTitle.start();
          }

          soundClick();
          window.location = "#table-search";
        } else {
          $('.toast-header').addClass('bg-danger');
          $('.toast-header').removeClass('bg-success');
          $('.toast-body').html('??????-???? ?????????? ???? ??????. ???????????????????? ?????? ?????? ?????? ???????????????? ??????');
          $('.toast').toast('show');
        }
      },
      complete: function complete() {
        clearInterval(id);
        var elem = document.getElementById("progress");
        var elem2 = document.getElementById("progress-text");
        elem.style.width = '0%';
        elem.innerHTML = 0;
        elem2.innerHTML = '';
      }
    });
  });
});
$(document).ready(function () {
  $.ajaxSetup({
    statusCode: {
      419: function _() {
        location.reload();
      }
    }
  });
});
})();

/******/ })()
;