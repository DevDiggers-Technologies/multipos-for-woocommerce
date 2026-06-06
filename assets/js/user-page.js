/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./src/user-page/user-page.less":
/*!**************************************!*\
  !*** ./src/user-page/user-page.less ***!
  \**************************************/
/***/ (() => {

// extracted by mini-css-extract-plugin

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
/*!********************************!*\
  !*** ./src/user-page/index.js ***!
  \********************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _user_page_less__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./user-page.less */ "./src/user-page/user-page.less");
/* harmony import */ var _user_page_less__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_user_page_less__WEBPACK_IMPORTED_MODULE_0__);



var ddwcpos = jQuery.noConflict();
document.addEventListener('DOMContentLoaded', function () {
  var userRoleSelectElements = document.querySelectorAll('select[name="role"]');

  if (userRoleSelectElements.length) {
    userRoleSelectElements.forEach(function (userRoleSelectElement) {
      if (ddwcposUserPageObj.siteReferer) {
        userRoleSelectElement.value = 'ddwcpos_cashier';
      }

      if ('ddwcpos_cashier' === userRoleSelectElement.value) {
        var selectOutletTemplate = wp.template('ddwcpos_assigned_outlets');
        userRoleSelectElement.closest('table').insertAdjacentHTML('afterend', selectOutletTemplate());
        ddwcpos('.ddwcpos-assigned-outlets').select2();
      }

      userRoleSelectElement.addEventListener('change', function (e) {
        if ('ddwcpos_cashier' === e.target.value) {
          var _selectOutletTemplate = wp.template('ddwcpos_assigned_outlets');

          e.target.closest('table').insertAdjacentHTML('afterend', _selectOutletTemplate());
          ddwcpos('.ddwcpos-assigned-outlets').select2();
        } else {
          if (document.querySelector('#ddwcpos-assigned-outlets-row')) {
            document.querySelector('#ddwcpos-assigned-outlets-row').remove();
          }
        }
      });
    });
  }
});
})();

var __webpack_export_target__ = this;
for(var i in __webpack_exports__) __webpack_export_target__[i] = __webpack_exports__[i];
if(__webpack_exports__.__esModule) Object.defineProperty(__webpack_export_target__, "__esModule", { value: true });
/******/ })()
;
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly9kZC13b29jb21tZXJjZS1tdWx0aXBvcy8uL3NyYy91c2VyLXBhZ2UvdXNlci1wYWdlLmxlc3M/NGNlZCIsIndlYnBhY2s6Ly9kZC13b29jb21tZXJjZS1tdWx0aXBvcy93ZWJwYWNrL2Jvb3RzdHJhcCIsIndlYnBhY2s6Ly9kZC13b29jb21tZXJjZS1tdWx0aXBvcy93ZWJwYWNrL3J1bnRpbWUvY29tcGF0IGdldCBkZWZhdWx0IGV4cG9ydCIsIndlYnBhY2s6Ly9kZC13b29jb21tZXJjZS1tdWx0aXBvcy93ZWJwYWNrL3J1bnRpbWUvZGVmaW5lIHByb3BlcnR5IGdldHRlcnMiLCJ3ZWJwYWNrOi8vZGQtd29vY29tbWVyY2UtbXVsdGlwb3Mvd2VicGFjay9ydW50aW1lL2hhc093blByb3BlcnR5IHNob3J0aGFuZCIsIndlYnBhY2s6Ly9kZC13b29jb21tZXJjZS1tdWx0aXBvcy93ZWJwYWNrL3J1bnRpbWUvbWFrZSBuYW1lc3BhY2Ugb2JqZWN0Iiwid2VicGFjazovL2RkLXdvb2NvbW1lcmNlLW11bHRpcG9zLy4vc3JjL3VzZXItcGFnZS9pbmRleC5qcyJdLCJuYW1lcyI6WyJkZHdjcG9zIiwialF1ZXJ5Iiwibm9Db25mbGljdCIsImRvY3VtZW50IiwiYWRkRXZlbnRMaXN0ZW5lciIsInVzZXJSb2xlU2VsZWN0RWxlbWVudHMiLCJxdWVyeVNlbGVjdG9yQWxsIiwibGVuZ3RoIiwiZm9yRWFjaCIsInVzZXJSb2xlU2VsZWN0RWxlbWVudCIsImRkd2Nwb3NVc2VyUGFnZU9iaiIsInNpdGVSZWZlcmVyIiwidmFsdWUiLCJzZWxlY3RPdXRsZXRUZW1wbGF0ZSIsIndwIiwidGVtcGxhdGUiLCJjbG9zZXN0IiwiaW5zZXJ0QWRqYWNlbnRIVE1MIiwic2VsZWN0MiIsImUiLCJ0YXJnZXQiLCJxdWVyeVNlbGVjdG9yIiwicmVtb3ZlIl0sIm1hcHBpbmdzIjoiOzs7Ozs7Ozs7QUFBQSx1Qzs7Ozs7O1VDQUE7VUFDQTs7VUFFQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTs7VUFFQTtVQUNBOztVQUVBO1VBQ0E7VUFDQTs7Ozs7V0N0QkE7V0FDQTtXQUNBO1dBQ0E7V0FDQTtXQUNBLGdDQUFnQyxZQUFZO1dBQzVDO1dBQ0EsRTs7Ozs7V0NQQTtXQUNBO1dBQ0E7V0FDQTtXQUNBLHdDQUF3Qyx5Q0FBeUM7V0FDakY7V0FDQTtXQUNBLEU7Ozs7O1dDUEEsd0Y7Ozs7O1dDQUE7V0FDQTtXQUNBO1dBQ0Esc0RBQXNELGtCQUFrQjtXQUN4RTtXQUNBLCtDQUErQyxjQUFjO1dBQzdELEU7Ozs7Ozs7Ozs7Ozs7O0FDTmE7O0FBRWI7QUFFQSxJQUFJQSxPQUFPLEdBQUdDLE1BQU0sQ0FBQ0MsVUFBUCxFQUFkO0FBRUFDLFFBQVEsQ0FBQ0MsZ0JBQVQsQ0FBMEIsa0JBQTFCLEVBQThDLFlBQU07QUFDaEQsTUFBTUMsc0JBQXNCLEdBQUdGLFFBQVEsQ0FBQ0csZ0JBQVQsQ0FBMEIscUJBQTFCLENBQS9COztBQUNBLE1BQUlELHNCQUFzQixDQUFDRSxNQUEzQixFQUFtQztBQUMvQkYsMEJBQXNCLENBQUNHLE9BQXZCLENBQStCLFVBQUFDLHFCQUFxQixFQUFJO0FBQ3BELFVBQUlDLGtCQUFrQixDQUFDQyxXQUF2QixFQUFvQztBQUNoQ0YsNkJBQXFCLENBQUNHLEtBQXRCLEdBQThCLGlCQUE5QjtBQUNIOztBQUNELFVBQUksc0JBQXNCSCxxQkFBcUIsQ0FBQ0csS0FBaEQsRUFBdUQ7QUFDbkQsWUFBSUMsb0JBQW9CLEdBQUdDLEVBQUUsQ0FBQ0MsUUFBSCxDQUFZLDBCQUFaLENBQTNCO0FBQ0FOLDZCQUFxQixDQUFDTyxPQUF0QixDQUE4QixPQUE5QixFQUF1Q0Msa0JBQXZDLENBQTBELFVBQTFELEVBQXNFSixvQkFBb0IsRUFBMUY7QUFDQWIsZUFBTyxDQUFDLDJCQUFELENBQVAsQ0FBcUNrQixPQUFyQztBQUNIOztBQUNEVCwyQkFBcUIsQ0FBQ0wsZ0JBQXRCLENBQXVDLFFBQXZDLEVBQWlELFVBQUFlLENBQUMsRUFBSTtBQUNsRCxZQUFJLHNCQUFzQkEsQ0FBQyxDQUFDQyxNQUFGLENBQVNSLEtBQW5DLEVBQTBDO0FBQ3RDLGNBQUlDLHFCQUFvQixHQUFHQyxFQUFFLENBQUNDLFFBQUgsQ0FBWSwwQkFBWixDQUEzQjs7QUFDQUksV0FBQyxDQUFDQyxNQUFGLENBQVNKLE9BQVQsQ0FBaUIsT0FBakIsRUFBMEJDLGtCQUExQixDQUE2QyxVQUE3QyxFQUF5REoscUJBQW9CLEVBQTdFO0FBQ0FiLGlCQUFPLENBQUMsMkJBQUQsQ0FBUCxDQUFxQ2tCLE9BQXJDO0FBQ0gsU0FKRCxNQUlPO0FBQ0gsY0FBSWYsUUFBUSxDQUFDa0IsYUFBVCxDQUF1QiwrQkFBdkIsQ0FBSixFQUE2RDtBQUN6RGxCLG9CQUFRLENBQUNrQixhQUFULENBQXVCLCtCQUF2QixFQUF3REMsTUFBeEQ7QUFDSDtBQUNKO0FBQ0osT0FWRDtBQVdILEtBcEJEO0FBcUJIO0FBQ0osQ0F6QkQsRSIsImZpbGUiOiIuL2Fzc2V0cy9qcy91c2VyLXBhZ2UuanMiLCJzb3VyY2VzQ29udGVudCI6WyIvLyBleHRyYWN0ZWQgYnkgbWluaS1jc3MtZXh0cmFjdC1wbHVnaW4iLCIvLyBUaGUgbW9kdWxlIGNhY2hlXG52YXIgX193ZWJwYWNrX21vZHVsZV9jYWNoZV9fID0ge307XG5cbi8vIFRoZSByZXF1aXJlIGZ1bmN0aW9uXG5mdW5jdGlvbiBfX3dlYnBhY2tfcmVxdWlyZV9fKG1vZHVsZUlkKSB7XG5cdC8vIENoZWNrIGlmIG1vZHVsZSBpcyBpbiBjYWNoZVxuXHR2YXIgY2FjaGVkTW9kdWxlID0gX193ZWJwYWNrX21vZHVsZV9jYWNoZV9fW21vZHVsZUlkXTtcblx0aWYgKGNhY2hlZE1vZHVsZSAhPT0gdW5kZWZpbmVkKSB7XG5cdFx0cmV0dXJuIGNhY2hlZE1vZHVsZS5leHBvcnRzO1xuXHR9XG5cdC8vIENyZWF0ZSBhIG5ldyBtb2R1bGUgKGFuZCBwdXQgaXQgaW50byB0aGUgY2FjaGUpXG5cdHZhciBtb2R1bGUgPSBfX3dlYnBhY2tfbW9kdWxlX2NhY2hlX19bbW9kdWxlSWRdID0ge1xuXHRcdC8vIG5vIG1vZHVsZS5pZCBuZWVkZWRcblx0XHQvLyBubyBtb2R1bGUubG9hZGVkIG5lZWRlZFxuXHRcdGV4cG9ydHM6IHt9XG5cdH07XG5cblx0Ly8gRXhlY3V0ZSB0aGUgbW9kdWxlIGZ1bmN0aW9uXG5cdF9fd2VicGFja19tb2R1bGVzX19bbW9kdWxlSWRdKG1vZHVsZSwgbW9kdWxlLmV4cG9ydHMsIF9fd2VicGFja19yZXF1aXJlX18pO1xuXG5cdC8vIFJldHVybiB0aGUgZXhwb3J0cyBvZiB0aGUgbW9kdWxlXG5cdHJldHVybiBtb2R1bGUuZXhwb3J0cztcbn1cblxuIiwiLy8gZ2V0RGVmYXVsdEV4cG9ydCBmdW5jdGlvbiBmb3IgY29tcGF0aWJpbGl0eSB3aXRoIG5vbi1oYXJtb255IG1vZHVsZXNcbl9fd2VicGFja19yZXF1aXJlX18ubiA9IChtb2R1bGUpID0+IHtcblx0dmFyIGdldHRlciA9IG1vZHVsZSAmJiBtb2R1bGUuX19lc01vZHVsZSA/XG5cdFx0KCkgPT4gKG1vZHVsZVsnZGVmYXVsdCddKSA6XG5cdFx0KCkgPT4gKG1vZHVsZSk7XG5cdF9fd2VicGFja19yZXF1aXJlX18uZChnZXR0ZXIsIHsgYTogZ2V0dGVyIH0pO1xuXHRyZXR1cm4gZ2V0dGVyO1xufTsiLCIvLyBkZWZpbmUgZ2V0dGVyIGZ1bmN0aW9ucyBmb3IgaGFybW9ueSBleHBvcnRzXG5fX3dlYnBhY2tfcmVxdWlyZV9fLmQgPSAoZXhwb3J0cywgZGVmaW5pdGlvbikgPT4ge1xuXHRmb3IodmFyIGtleSBpbiBkZWZpbml0aW9uKSB7XG5cdFx0aWYoX193ZWJwYWNrX3JlcXVpcmVfXy5vKGRlZmluaXRpb24sIGtleSkgJiYgIV9fd2VicGFja19yZXF1aXJlX18ubyhleHBvcnRzLCBrZXkpKSB7XG5cdFx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywga2V5LCB7IGVudW1lcmFibGU6IHRydWUsIGdldDogZGVmaW5pdGlvbltrZXldIH0pO1xuXHRcdH1cblx0fVxufTsiLCJfX3dlYnBhY2tfcmVxdWlyZV9fLm8gPSAob2JqLCBwcm9wKSA9PiAoT2JqZWN0LnByb3RvdHlwZS5oYXNPd25Qcm9wZXJ0eS5jYWxsKG9iaiwgcHJvcCkpIiwiLy8gZGVmaW5lIF9fZXNNb2R1bGUgb24gZXhwb3J0c1xuX193ZWJwYWNrX3JlcXVpcmVfXy5yID0gKGV4cG9ydHMpID0+IHtcblx0aWYodHlwZW9mIFN5bWJvbCAhPT0gJ3VuZGVmaW5lZCcgJiYgU3ltYm9sLnRvU3RyaW5nVGFnKSB7XG5cdFx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsIFN5bWJvbC50b1N0cmluZ1RhZywgeyB2YWx1ZTogJ01vZHVsZScgfSk7XG5cdH1cblx0T2JqZWN0LmRlZmluZVByb3BlcnR5KGV4cG9ydHMsICdfX2VzTW9kdWxlJywgeyB2YWx1ZTogdHJ1ZSB9KTtcbn07IiwiXCJ1c2Ugc3RyaWN0XCI7XG5cbmltcG9ydCAnLi91c2VyLXBhZ2UubGVzcyc7XG5cbnZhciBkZHdjcG9zID0galF1ZXJ5Lm5vQ29uZmxpY3QoKTtcblxuZG9jdW1lbnQuYWRkRXZlbnRMaXN0ZW5lcignRE9NQ29udGVudExvYWRlZCcsICgpID0+IHtcbiAgICBjb25zdCB1c2VyUm9sZVNlbGVjdEVsZW1lbnRzID0gZG9jdW1lbnQucXVlcnlTZWxlY3RvckFsbCgnc2VsZWN0W25hbWU9XCJyb2xlXCJdJyk7XG4gICAgaWYgKHVzZXJSb2xlU2VsZWN0RWxlbWVudHMubGVuZ3RoKSB7XG4gICAgICAgIHVzZXJSb2xlU2VsZWN0RWxlbWVudHMuZm9yRWFjaCh1c2VyUm9sZVNlbGVjdEVsZW1lbnQgPT4ge1xuICAgICAgICAgICAgaWYgKGRkd2Nwb3NVc2VyUGFnZU9iai5zaXRlUmVmZXJlcikge1xuICAgICAgICAgICAgICAgIHVzZXJSb2xlU2VsZWN0RWxlbWVudC52YWx1ZSA9ICdkZHdjcG9zX2Nhc2hpZXInO1xuICAgICAgICAgICAgfVxuICAgICAgICAgICAgaWYgKCdkZHdjcG9zX2Nhc2hpZXInID09PSB1c2VyUm9sZVNlbGVjdEVsZW1lbnQudmFsdWUpIHtcbiAgICAgICAgICAgICAgICBsZXQgc2VsZWN0T3V0bGV0VGVtcGxhdGUgPSB3cC50ZW1wbGF0ZSgnZGR3Y3Bvc19hc3NpZ25lZF9vdXRsZXRzJyk7XG4gICAgICAgICAgICAgICAgdXNlclJvbGVTZWxlY3RFbGVtZW50LmNsb3Nlc3QoJ3RhYmxlJykuaW5zZXJ0QWRqYWNlbnRIVE1MKCdhZnRlcmVuZCcsIHNlbGVjdE91dGxldFRlbXBsYXRlKCkpO1xuICAgICAgICAgICAgICAgIGRkd2Nwb3MoJy5kZHdjcG9zLWFzc2lnbmVkLW91dGxldHMnKS5zZWxlY3QyKCk7XG4gICAgICAgICAgICB9XG4gICAgICAgICAgICB1c2VyUm9sZVNlbGVjdEVsZW1lbnQuYWRkRXZlbnRMaXN0ZW5lcignY2hhbmdlJywgZSA9PiB7XG4gICAgICAgICAgICAgICAgaWYgKCdkZHdjcG9zX2Nhc2hpZXInID09PSBlLnRhcmdldC52YWx1ZSkge1xuICAgICAgICAgICAgICAgICAgICBsZXQgc2VsZWN0T3V0bGV0VGVtcGxhdGUgPSB3cC50ZW1wbGF0ZSgnZGR3Y3Bvc19hc3NpZ25lZF9vdXRsZXRzJyk7XG4gICAgICAgICAgICAgICAgICAgIGUudGFyZ2V0LmNsb3Nlc3QoJ3RhYmxlJykuaW5zZXJ0QWRqYWNlbnRIVE1MKCdhZnRlcmVuZCcsIHNlbGVjdE91dGxldFRlbXBsYXRlKCkpO1xuICAgICAgICAgICAgICAgICAgICBkZHdjcG9zKCcuZGR3Y3Bvcy1hc3NpZ25lZC1vdXRsZXRzJykuc2VsZWN0MigpO1xuICAgICAgICAgICAgICAgIH0gZWxzZSB7XG4gICAgICAgICAgICAgICAgICAgIGlmIChkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcjZGR3Y3Bvcy1hc3NpZ25lZC1vdXRsZXRzLXJvdycpKSB7XG4gICAgICAgICAgICAgICAgICAgICAgICBkb2N1bWVudC5xdWVyeVNlbGVjdG9yKCcjZGR3Y3Bvcy1hc3NpZ25lZC1vdXRsZXRzLXJvdycpLnJlbW92ZSgpO1xuICAgICAgICAgICAgICAgICAgICB9XG4gICAgICAgICAgICAgICAgfVxuICAgICAgICAgICAgfSk7XG4gICAgICAgIH0pO1xuICAgIH1cbn0pOyJdLCJzb3VyY2VSb290IjoiIn0=