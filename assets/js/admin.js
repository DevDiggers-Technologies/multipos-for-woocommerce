/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./node_modules/@babel/runtime/helpers/esm/arrayLikeToArray.js":
/*!*********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/arrayLikeToArray.js ***!
  \*********************************************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ _arrayLikeToArray)
/* harmony export */ });
function _arrayLikeToArray(arr, len) {
  if (len == null || len > arr.length) len = arr.length;

  for (var i = 0, arr2 = new Array(len); i < len; i++) {
    arr2[i] = arr[i];
  }

  return arr2;
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js":
/*!**********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js ***!
  \**********************************************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ _arrayWithoutHoles)
/* harmony export */ });
/* harmony import */ var _arrayLikeToArray_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./arrayLikeToArray.js */ "./node_modules/@babel/runtime/helpers/esm/arrayLikeToArray.js");

function _arrayWithoutHoles(arr) {
  if (Array.isArray(arr)) return (0,_arrayLikeToArray_js__WEBPACK_IMPORTED_MODULE_0__.default)(arr);
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/iterableToArray.js":
/*!********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/iterableToArray.js ***!
  \********************************************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ _iterableToArray)
/* harmony export */ });
function _iterableToArray(iter) {
  if (typeof Symbol !== "undefined" && iter[Symbol.iterator] != null || iter["@@iterator"] != null) return Array.from(iter);
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js":
/*!**********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js ***!
  \**********************************************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ _nonIterableSpread)
/* harmony export */ });
function _nonIterableSpread() {
  throw new TypeError("Invalid attempt to spread non-iterable instance.\nIn order to be iterable, non-array objects must have a [Symbol.iterator]() method.");
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js":
/*!**********************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js ***!
  \**********************************************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ _toConsumableArray)
/* harmony export */ });
/* harmony import */ var _arrayWithoutHoles_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./arrayWithoutHoles.js */ "./node_modules/@babel/runtime/helpers/esm/arrayWithoutHoles.js");
/* harmony import */ var _iterableToArray_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./iterableToArray.js */ "./node_modules/@babel/runtime/helpers/esm/iterableToArray.js");
/* harmony import */ var _unsupportedIterableToArray_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./unsupportedIterableToArray.js */ "./node_modules/@babel/runtime/helpers/esm/unsupportedIterableToArray.js");
/* harmony import */ var _nonIterableSpread_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./nonIterableSpread.js */ "./node_modules/@babel/runtime/helpers/esm/nonIterableSpread.js");




function _toConsumableArray(arr) {
  return (0,_arrayWithoutHoles_js__WEBPACK_IMPORTED_MODULE_0__.default)(arr) || (0,_iterableToArray_js__WEBPACK_IMPORTED_MODULE_1__.default)(arr) || (0,_unsupportedIterableToArray_js__WEBPACK_IMPORTED_MODULE_2__.default)(arr) || (0,_nonIterableSpread_js__WEBPACK_IMPORTED_MODULE_3__.default)();
}

/***/ }),

/***/ "./node_modules/@babel/runtime/helpers/esm/unsupportedIterableToArray.js":
/*!*******************************************************************************!*\
  !*** ./node_modules/@babel/runtime/helpers/esm/unsupportedIterableToArray.js ***!
  \*******************************************************************************/
/***/ ((__unused_webpack___webpack_module__, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ _unsupportedIterableToArray)
/* harmony export */ });
/* harmony import */ var _arrayLikeToArray_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./arrayLikeToArray.js */ "./node_modules/@babel/runtime/helpers/esm/arrayLikeToArray.js");

function _unsupportedIterableToArray(o, minLen) {
  if (!o) return;
  if (typeof o === "string") return (0,_arrayLikeToArray_js__WEBPACK_IMPORTED_MODULE_0__.default)(o, minLen);
  var n = Object.prototype.toString.call(o).slice(8, -1);
  if (n === "Object" && o.constructor) n = o.constructor.name;
  if (n === "Map" || n === "Set") return Array.from(o);
  if (n === "Arguments" || /^(?:Ui|I)nt(?:8|16|32)(?:Clamped)?Array$/.test(n)) return (0,_arrayLikeToArray_js__WEBPACK_IMPORTED_MODULE_0__.default)(o, minLen);
}

/***/ }),

/***/ "./src/admin/admin.less":
/*!******************************!*\
  !*** ./src/admin/admin.less ***!
  \******************************/
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
/*!****************************!*\
  !*** ./src/admin/index.js ***!
  \****************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! @babel/runtime/helpers/toConsumableArray */ "./node_modules/@babel/runtime/helpers/esm/toConsumableArray.js");
/* harmony import */ var _admin_less__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./admin.less */ "./src/admin/admin.less");
/* harmony import */ var _admin_less__WEBPACK_IMPORTED_MODULE_1___default = /*#__PURE__*/__webpack_require__.n(_admin_less__WEBPACK_IMPORTED_MODULE_1__);




var ADMIN_OBJECT = window.ddwcposAdminObj || {};
var AJAX_CONFIG = ADMIN_OBJECT.ajax || {};
var I18N = ADMIN_OBJECT.i18n || {};
var CONFIG = ADMIN_OBJECT.ddwcpos_configuration || {};
var INVALID_HTML_PATTERN = /<\s?[^>]*\/?\s?>/i;
var FORM_SELECTOR = 'form#ddwcpos-payments-container, form#ddwcpos-tables-container';
var PRODUCT_ACTION_SELECTOR = '.ddwcpos-product-action';
var BARCODE_INPUT_SELECTOR = '.ddwcpos-barcode, .ddwcpos-barcode-quantity, .ddwcpos-custom-stock';

var onReady = function onReady(callback) {
  if ('loading' === document.readyState) {
    document.addEventListener('DOMContentLoaded', callback);
    return;
  }

  callback();
};

var removeElements = function removeElements(elements) {
  (0,_babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__.default)(elements).forEach(function (element) {
    return element.remove();
  });
};

var getTemplate = function getTemplate(templateId) {
  return window.wp && wp.template && templateId ? wp.template(templateId) : null;
};

var showValidationNotice = function showValidationNotice(form) {
  var invalidFormFields = getTemplate('ddwcpos_form_data_error');

  if (invalidFormFields) {
    form.insertAdjacentHTML('beforeBegin', invalidFormFields());
  }
};

var shouldValidateInput = function shouldValidateInput(input) {
  return input && !input.disabled && !['hidden', 'submit', 'button'].includes(input.type) && !input.classList.contains('ddwcpos-hide');
};

var setInputInvalid = function setInputInvalid(input) {
  input.style.borderColor = 'red';
};

var resetInputState = function resetInputState(input) {
  input.style.borderColor = '';
};

var isInvalidInputValue = function isInvalidInputValue(input) {
  var inputValue = String(input.value || '').trim();
  return !inputValue || '-1' === inputValue || INVALID_HTML_PATTERN.test(inputValue);
};

var validateForm = function validateForm(form, event) {
  var hasError = false;

  (0,_babel_runtime_helpers_toConsumableArray__WEBPACK_IMPORTED_MODULE_0__.default)(form.elements).forEach(function (input) {
    resetInputState(input);

    if (!shouldValidateInput(input) || !isInvalidInputValue(input)) {
      return;
    }

    event.preventDefault();
    setInputInvalid(input);
    hasError = true;
  });

  if (hasError) {
    showValidationNotice(form);
  }
};

var bindConfigurationForms = function bindConfigurationForms() {
  var forms = document.querySelectorAll(FORM_SELECTOR);

  if (!forms.length) {
    return;
  }

  forms.forEach(function (form) {
    form.addEventListener('submit', function (event) {
      removeElements(document.querySelectorAll('.notice'));
      validateForm(form, event);
    });
  });
};

var getRepeaterTarget = function getRepeaterTarget(button) {
  var invoiceCard = button.closest('.ddwcpos-invoice-card');

  if (invoiceCard) {
    return invoiceCard;
  }

  return button.closest('tr');
};

var addRepeaterRow = function addRepeaterRow(button) {
  var form = button.closest('form');
  var templateId = button.getAttribute('data-template');
  var template = getTemplate(templateId);
  var maxIndexElement = form ? form.querySelector('#ddwcpos-max-index') : null;
  var target = getRepeaterTarget(button);

  if (!template || !maxIndexElement || !target) {
    return;
  }

  var rowIndex = Number.parseInt(maxIndexElement.value, 10) || 0;
  var nextIndex = rowIndex + 1;
  maxIndexElement.value = nextIndex;
  target.insertAdjacentHTML('beforeBegin', template({
    key: nextIndex
  }));
};

var removeRepeaterRow = function removeRepeaterRow(button) {
  var target = getRepeaterTarget(button);

  if (target) {
    target.remove();
  }
};

var bindRepeaterRows = function bindRepeaterRows() {
  document.addEventListener('click', function (event) {
    var addRowButton = event.target.closest('.ddwcpos-add-row');
    var removeRowButton = event.target.closest('.ddwcpos-remove-row');

    if (addRowButton) {
      event.preventDefault();
      addRepeaterRow(addRowButton);
      return;
    }

    if (removeRowButton) {
      event.preventDefault();
      removeRepeaterRow(removeRowButton);
    }
  });
};

var getBarcodePrintStyle = function getBarcodePrintStyle() {
  return "<style type=\"text/css\">\n    @page {\n        size: ".concat(CONFIG.barcode_printer_width, " ").concat(CONFIG.barcode_printer_height, ";\n        margin: ").concat(CONFIG.barcode_printer_margin, ";\n        text-align: center;\n    }\n</style>");
};

var getBarcodePrintContent = function getBarcodePrintContent(row, quantity) {
  var printContentElement = row.querySelector('.ddwcpos-barcode-print-content');
  var printContent = printContentElement ? printContentElement.innerHTML : '';
  return Array.from({
    length: quantity
  }, function () {
    return printContent;
  }).join('');
};

var isMobileDevice = function isMobileDevice() {
  return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
};

var printInPopup = function printInPopup(html) {
  var printWindow = window.open('', 'PRINT', 'height=400,width=600');

  if (!printWindow) {
    return;
  }

  printWindow.document.open();
  printWindow.document.clear();
  printWindow.document.writeln(html);
  printWindow.document.close();
  printWindow.addEventListener('load', function () {
    setTimeout(function () {
      printWindow.focus();
      printWindow.print();
    }, 700);
  }, true);
};

var printInFrame = function printInFrame(html) {
  var frame = document.createElement('iframe');
  frame.name = 'ddwcpos-barcode-print-frame';
  document.body.appendChild(frame);
  var frameDocument = frame.contentWindow ? frame.contentWindow.document : frame.contentDocument;
  frameDocument.open();
  frameDocument.write(html);
  frameDocument.close();
  setTimeout(function () {
    frame.contentWindow.focus();
    frame.contentWindow.print();
    document.body.removeChild(frame);
  }, 700);
};

var handleBarcodePrint = function handleBarcodePrint(row) {
  var quantityElement = row.querySelector('.ddwcpos-barcode-quantity');
  var quantity = quantityElement ? Number.parseInt(quantityElement.value, 10) : 0;

  if (!quantity) {
    alert(I18N.barcodeQuantityError);
    return;
  }

  quantityElement.value = '';
  var html = "<html><head><title>Barcode</title>".concat(getBarcodePrintStyle(), "</head><body>").concat(getBarcodePrintContent(row, quantity), "</body></html>");

  if (isMobileDevice()) {
    printInPopup(html);
    return;
  }

  printInFrame(html);
};

var bindProductActions = function bindProductActions() {
  document.addEventListener('click', function (event) {
    var button = event.target.closest(PRODUCT_ACTION_SELECTOR);

    if (!button) {
      return;
    }

    event.preventDefault();
    var row = button.closest('tr');
    var productAction = button.getAttribute('data-action');

    if (!row) {
      return;
    }

    if ('print_barcode' === productAction) {
      handleBarcodePrint(row);
      return;
    }
  });
};

var bindBarcodeEnterKey = function bindBarcodeEnterKey() {
  document.addEventListener('keydown', function (event) {
    if (!event.target.matches(BARCODE_INPUT_SELECTOR) || 'Enter' !== event.key && 13 !== event.keyCode) {
      return;
    }

    var nextElement = event.target.nextElementSibling;
    event.preventDefault();

    if (nextElement) {
      nextElement.click();
    }
  });
};

onReady(function () {
  bindConfigurationForms();
  bindRepeaterRows();
  bindProductActions();
  bindBarcodeEnterKey();
});
})();

var __webpack_export_target__ = this;
for(var i in __webpack_exports__) __webpack_export_target__[i] = __webpack_exports__[i];
if(__webpack_exports__.__esModule) Object.defineProperty(__webpack_export_target__, "__esModule", { value: true });
/******/ })()
;
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIndlYnBhY2s6Ly9kZC13b29jb21tZXJjZS1tdWx0aXBvcy8uL25vZGVfbW9kdWxlcy9AYmFiZWwvcnVudGltZS9oZWxwZXJzL2VzbS9hcnJheUxpa2VUb0FycmF5LmpzIiwid2VicGFjazovL2RkLXdvb2NvbW1lcmNlLW11bHRpcG9zLy4vbm9kZV9tb2R1bGVzL0BiYWJlbC9ydW50aW1lL2hlbHBlcnMvZXNtL2FycmF5V2l0aG91dEhvbGVzLmpzIiwid2VicGFjazovL2RkLXdvb2NvbW1lcmNlLW11bHRpcG9zLy4vbm9kZV9tb2R1bGVzL0BiYWJlbC9ydW50aW1lL2hlbHBlcnMvZXNtL2l0ZXJhYmxlVG9BcnJheS5qcyIsIndlYnBhY2s6Ly9kZC13b29jb21tZXJjZS1tdWx0aXBvcy8uL25vZGVfbW9kdWxlcy9AYmFiZWwvcnVudGltZS9oZWxwZXJzL2VzbS9ub25JdGVyYWJsZVNwcmVhZC5qcyIsIndlYnBhY2s6Ly9kZC13b29jb21tZXJjZS1tdWx0aXBvcy8uL25vZGVfbW9kdWxlcy9AYmFiZWwvcnVudGltZS9oZWxwZXJzL2VzbS90b0NvbnN1bWFibGVBcnJheS5qcyIsIndlYnBhY2s6Ly9kZC13b29jb21tZXJjZS1tdWx0aXBvcy8uL25vZGVfbW9kdWxlcy9AYmFiZWwvcnVudGltZS9oZWxwZXJzL2VzbS91bnN1cHBvcnRlZEl0ZXJhYmxlVG9BcnJheS5qcyIsIndlYnBhY2s6Ly9kZC13b29jb21tZXJjZS1tdWx0aXBvcy8uL3NyYy9hZG1pbi9hZG1pbi5sZXNzPzE4NTQiLCJ3ZWJwYWNrOi8vZGQtd29vY29tbWVyY2UtbXVsdGlwb3Mvd2VicGFjay9ib290c3RyYXAiLCJ3ZWJwYWNrOi8vZGQtd29vY29tbWVyY2UtbXVsdGlwb3Mvd2VicGFjay9ydW50aW1lL2NvbXBhdCBnZXQgZGVmYXVsdCBleHBvcnQiLCJ3ZWJwYWNrOi8vZGQtd29vY29tbWVyY2UtbXVsdGlwb3Mvd2VicGFjay9ydW50aW1lL2RlZmluZSBwcm9wZXJ0eSBnZXR0ZXJzIiwid2VicGFjazovL2RkLXdvb2NvbW1lcmNlLW11bHRpcG9zL3dlYnBhY2svcnVudGltZS9oYXNPd25Qcm9wZXJ0eSBzaG9ydGhhbmQiLCJ3ZWJwYWNrOi8vZGQtd29vY29tbWVyY2UtbXVsdGlwb3Mvd2VicGFjay9ydW50aW1lL21ha2UgbmFtZXNwYWNlIG9iamVjdCIsIndlYnBhY2s6Ly9kZC13b29jb21tZXJjZS1tdWx0aXBvcy8uL3NyYy9hZG1pbi9pbmRleC5qcyJdLCJuYW1lcyI6WyJBRE1JTl9PQkpFQ1QiLCJ3aW5kb3ciLCJkZHdjcG9zQWRtaW5PYmoiLCJBSkFYX0NPTkZJRyIsImFqYXgiLCJJMThOIiwiaTE4biIsIkNPTkZJRyIsImRkd2Nwb3NfY29uZmlndXJhdGlvbiIsIklOVkFMSURfSFRNTF9QQVRURVJOIiwiRk9STV9TRUxFQ1RPUiIsIlBST0RVQ1RfQUNUSU9OX1NFTEVDVE9SIiwiQkFSQ09ERV9JTlBVVF9TRUxFQ1RPUiIsIm9uUmVhZHkiLCJjYWxsYmFjayIsImRvY3VtZW50IiwicmVhZHlTdGF0ZSIsImFkZEV2ZW50TGlzdGVuZXIiLCJyZW1vdmVFbGVtZW50cyIsImVsZW1lbnRzIiwiZm9yRWFjaCIsImVsZW1lbnQiLCJyZW1vdmUiLCJnZXRUZW1wbGF0ZSIsInRlbXBsYXRlSWQiLCJ3cCIsInRlbXBsYXRlIiwic2hvd1ZhbGlkYXRpb25Ob3RpY2UiLCJmb3JtIiwiaW52YWxpZEZvcm1GaWVsZHMiLCJpbnNlcnRBZGphY2VudEhUTUwiLCJzaG91bGRWYWxpZGF0ZUlucHV0IiwiaW5wdXQiLCJkaXNhYmxlZCIsImluY2x1ZGVzIiwidHlwZSIsImNsYXNzTGlzdCIsImNvbnRhaW5zIiwic2V0SW5wdXRJbnZhbGlkIiwic3R5bGUiLCJib3JkZXJDb2xvciIsInJlc2V0SW5wdXRTdGF0ZSIsImlzSW52YWxpZElucHV0VmFsdWUiLCJpbnB1dFZhbHVlIiwiU3RyaW5nIiwidmFsdWUiLCJ0cmltIiwidGVzdCIsInZhbGlkYXRlRm9ybSIsImV2ZW50IiwiaGFzRXJyb3IiLCJwcmV2ZW50RGVmYXVsdCIsImJpbmRDb25maWd1cmF0aW9uRm9ybXMiLCJmb3JtcyIsInF1ZXJ5U2VsZWN0b3JBbGwiLCJsZW5ndGgiLCJnZXRSZXBlYXRlclRhcmdldCIsImJ1dHRvbiIsImludm9pY2VDYXJkIiwiY2xvc2VzdCIsImFkZFJlcGVhdGVyUm93IiwiZ2V0QXR0cmlidXRlIiwibWF4SW5kZXhFbGVtZW50IiwicXVlcnlTZWxlY3RvciIsInRhcmdldCIsInJvd0luZGV4IiwiTnVtYmVyIiwicGFyc2VJbnQiLCJuZXh0SW5kZXgiLCJrZXkiLCJyZW1vdmVSZXBlYXRlclJvdyIsImJpbmRSZXBlYXRlclJvd3MiLCJhZGRSb3dCdXR0b24iLCJyZW1vdmVSb3dCdXR0b24iLCJnZXRCYXJjb2RlUHJpbnRTdHlsZSIsImJhcmNvZGVfcHJpbnRlcl93aWR0aCIsImJhcmNvZGVfcHJpbnRlcl9oZWlnaHQiLCJiYXJjb2RlX3ByaW50ZXJfbWFyZ2luIiwiZ2V0QmFyY29kZVByaW50Q29udGVudCIsInJvdyIsInF1YW50aXR5IiwicHJpbnRDb250ZW50RWxlbWVudCIsInByaW50Q29udGVudCIsImlubmVySFRNTCIsIkFycmF5IiwiZnJvbSIsImpvaW4iLCJpc01vYmlsZURldmljZSIsIm5hdmlnYXRvciIsInVzZXJBZ2VudCIsInByaW50SW5Qb3B1cCIsImh0bWwiLCJwcmludFdpbmRvdyIsIm9wZW4iLCJjbGVhciIsIndyaXRlbG4iLCJjbG9zZSIsInNldFRpbWVvdXQiLCJmb2N1cyIsInByaW50IiwicHJpbnRJbkZyYW1lIiwiZnJhbWUiLCJjcmVhdGVFbGVtZW50IiwibmFtZSIsImJvZHkiLCJhcHBlbmRDaGlsZCIsImZyYW1lRG9jdW1lbnQiLCJjb250ZW50V2luZG93IiwiY29udGVudERvY3VtZW50Iiwid3JpdGUiLCJyZW1vdmVDaGlsZCIsImhhbmRsZUJhcmNvZGVQcmludCIsInF1YW50aXR5RWxlbWVudCIsImFsZXJ0IiwiYmFyY29kZVF1YW50aXR5RXJyb3IiLCJiaW5kUHJvZHVjdEFjdGlvbnMiLCJwcm9kdWN0QWN0aW9uIiwiYmluZEJhcmNvZGVFbnRlcktleSIsIm1hdGNoZXMiLCJrZXlDb2RlIiwibmV4dEVsZW1lbnQiLCJuZXh0RWxlbWVudFNpYmxpbmciLCJjbGljayJdLCJtYXBwaW5ncyI6Ijs7Ozs7Ozs7Ozs7Ozs7QUFBZTtBQUNmOztBQUVBLHdDQUF3QyxTQUFTO0FBQ2pEO0FBQ0E7O0FBRUE7QUFDQSxDOzs7Ozs7Ozs7Ozs7Ozs7O0FDUnFEO0FBQ3RDO0FBQ2YsaUNBQWlDLDZEQUFnQjtBQUNqRCxDOzs7Ozs7Ozs7Ozs7Ozs7QUNIZTtBQUNmO0FBQ0EsQzs7Ozs7Ozs7Ozs7Ozs7O0FDRmU7QUFDZjtBQUNBLEM7Ozs7Ozs7Ozs7Ozs7Ozs7Ozs7QUNGdUQ7QUFDSjtBQUNzQjtBQUNsQjtBQUN4QztBQUNmLFNBQVMsOERBQWlCLFNBQVMsNERBQWUsU0FBUyx1RUFBMEIsU0FBUyw4REFBaUI7QUFDL0csQzs7Ozs7Ozs7Ozs7Ozs7OztBQ05xRDtBQUN0QztBQUNmO0FBQ0Esb0NBQW9DLDZEQUFnQjtBQUNwRDtBQUNBO0FBQ0E7QUFDQSxzRkFBc0YsNkRBQWdCO0FBQ3RHLEM7Ozs7Ozs7Ozs7QUNSQSx1Qzs7Ozs7O1VDQUE7VUFDQTs7VUFFQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTs7VUFFQTtVQUNBOztVQUVBO1VBQ0E7VUFDQTs7Ozs7V0N0QkE7V0FDQTtXQUNBO1dBQ0E7V0FDQTtXQUNBLGdDQUFnQyxZQUFZO1dBQzVDO1dBQ0EsRTs7Ozs7V0NQQTtXQUNBO1dBQ0E7V0FDQTtXQUNBLHdDQUF3Qyx5Q0FBeUM7V0FDakY7V0FDQTtXQUNBLEU7Ozs7O1dDUEEsd0Y7Ozs7O1dDQUE7V0FDQTtXQUNBO1dBQ0Esc0RBQXNELGtCQUFrQjtXQUN4RTtXQUNBLCtDQUErQyxjQUFjO1dBQzdELEU7Ozs7Ozs7Ozs7Ozs7OztBQ05hOzs7QUFFYjtBQUVBLElBQU1BLFlBQVksR0FBR0MsTUFBTSxDQUFDQyxlQUFQLElBQTBCLEVBQS9DO0FBQ0Es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Q1Q7QUFDSDs7QUFFRFIsU0FBSyxDQUFDRSxjQUFOO0FBRUEsUUFBTTRCLEdBQUcsR0FBR3RCLE1BQU0sQ0FBQ0UsT0FBUCxDQUFlLElBQWYsQ0FBWjtBQUNBLFFBQU15RCxhQUFhLEdBQUczRCxNQUFNLENBQUNJLFlBQVAsQ0FBb0IsYUFBcEIsQ0FBdEI7O0FBRUEsUUFBSSxDQUFDa0IsR0FBTCxFQUFVO0FBQ047QUFDSDs7QUFFRCxRQUFJLG9CQUFvQnFDLGFBQXhCLEVBQXVDO0FBQ25DTCx3QkFBa0IsQ0FBQ2hDLEdBQUQsQ0FBbEI7QUFDQTtBQUNIO0FBQ0osR0FwQkQ7QUFxQkgsQ0F0QkQ7O0FBd0JBLElBQU1zQyxtQkFBbUIsR0FBRyxTQUF0QkEsbUJBQXNCLEdBQU07QUFDOUJ0RyxVQUFRLENBQUNFLGdCQUFULENBQTBCLFNBQTFCLEVBQXFDLFVBQUFnQyxLQUFLLEVBQUk7QUFDMUMsUUFBSSxDQUFDQSxLQUFLLENBQUNlLE1BQU4sQ0FBYXNELE9BQWIsQ0FBcUIxRyxzQkFBckIsQ0FBRCxJQUFrRCxZQUFZcUMsS0FBSyxDQUFDb0IsR0FBbEIsSUFBeUIsT0FBT3BCLEtBQUssQ0FBQ3NFLE9BQTVGLEVBQXNHO0FBQ2xHO0FBQ0g7O0FBRUQsUUFBTUMsV0FBVyxHQUFHdkUsS0FBSyxDQUFDZSxNQUFOLENBQWF5RCxrQkFBakM7QUFFQXhFLFNBQUssQ0FBQ0UsY0FBTjs7QUFFQSxRQUFJcUUsV0FBSixFQUFpQjtBQUNiQSxpQkFBVyxDQUFDRSxLQUFaO0FBQ0g7QUFDSixHQVpEO0FBYUgsQ0FkRDs7QUFnQkE3RyxPQUFPLENBQUMsWUFBTTtBQUNWdUMsd0JBQXNCO0FBQ3RCbUIsa0JBQWdCO0FBQ2hCNEMsb0JBQWtCO0FBQ2xCRSxxQkFBbUI7QUFDdEIsQ0FMTSxDQUFQLEMiLCJmaWxlIjoiLi9hc3NldHMvanMvYWRtaW4uanMiLCJzb3VyY2VzQ29udGVudCI6WyJleHBvcnQgZGVmYXVsdCBmdW5jdGlvbiBfYXJyYXlMaWtlVG9BcnJheShhcnIsIGxlbikge1xuICBpZiAobGVuID09IG51bGwgfHwgbGVuID4gYXJyLmxlbmd0aCkgbGVuID0gYXJyLmxlbmd0aDtcblxuICBmb3IgKHZhciBpID0gMCwgYXJyMiA9IG5ldyBBcnJheShsZW4pOyBpIDwgbGVuOyBpKyspIHtcbiAgICBhcnIyW2ldID0gYXJyW2ldO1xuICB9XG5cbiAgcmV0dXJuIGFycjI7XG59IiwiaW1wb3J0IGFycmF5TGlrZVRvQXJyYXkgZnJvbSBcIi4vYXJyYXlMaWtlVG9BcnJheS5qc1wiO1xuZXhwb3J0IGRlZmF1bHQgZnVuY3Rpb24gX2FycmF5V2l0aG91dEhvbGVzKGFycikge1xuICBpZiAoQXJyYXkuaXNBcnJheShhcnIpKSByZXR1cm4gYXJyYXlMaWtlVG9BcnJheShhcnIpO1xufSIsImV4cG9ydCBkZWZhdWx0IGZ1bmN0aW9uIF9pdGVyYWJsZVRvQXJyYXkoaXRlcikge1xuICBpZiAodHlwZW9mIFN5bWJvbCAhPT0gXCJ1bmRlZmluZWRcIiAmJiBpdGVyW1N5bWJvbC5pdGVyYXRvcl0gIT0gbnVsbCB8fCBpdGVyW1wiQEBpdGVyYXRvclwiXSAhPSBudWxsKSByZXR1cm4gQXJyYXkuZnJvbShpdGVyKTtcbn0iLCJleHBvcnQgZGVmYXVsdCBmdW5jdGlvbiBfbm9uSXRlcmFibGVTcHJlYWQoKSB7XG4gIHRocm93IG5ldyBUeXBlRXJyb3IoXCJJbnZhbGlkIGF0dGVtcHQgdG8gc3ByZWFkIG5vbi1pdGVyYWJsZSBpbnN0YW5jZS5cXG5JbiBvcmRlciB0byBiZSBpdGVyYWJsZSwgbm9uLWFycmF5IG9iamVjdHMgbXVzdCBoYXZlIGEgW1N5bWJvbC5pdGVyYXRvcl0oKSBtZXRob2QuXCIpO1xufSIsImltcG9ydCBhcnJheVdpdGhvdXRIb2xlcyBmcm9tIFwiLi9hcnJheVdpdGhvdXRIb2xlcy5qc1wiO1xuaW1wb3J0IGl0ZXJhYmxlVG9BcnJheSBmcm9tIFwiLi9pdGVyYWJsZVRvQXJyYXkuanNcIjtcbmltcG9ydCB1bnN1cHBvcnRlZEl0ZXJhYmxlVG9BcnJheSBmcm9tIFwiLi91bnN1cHBvcnRlZEl0ZXJhYmxlVG9BcnJheS5qc1wiO1xuaW1wb3J0IG5vbkl0ZXJhYmxlU3ByZWFkIGZyb20gXCIuL25vbkl0ZXJhYmxlU3ByZWFkLmpzXCI7XG5leHBvcnQgZGVmYXVsdCBmdW5jdGlvbiBfdG9Db25zdW1hYmxlQXJyYXkoYXJyKSB7XG4gIHJldHVybiBhcnJheVdpdGhvdXRIb2xlcyhhcnIpIHx8IGl0ZXJhYmxlVG9BcnJheShhcnIpIHx8IHVuc3VwcG9ydGVkSXRlcmFibGVUb0FycmF5KGFycikgfHwgbm9uSXRlcmFibGVTcHJlYWQoKTtcbn0iLCJpbXBvcnQgYXJyYXlMaWtlVG9BcnJheSBmcm9tIFwiLi9hcnJheUxpa2VUb0FycmF5LmpzXCI7XG5leHBvcnQgZGVmYXVsdCBmdW5jdGlvbiBfdW5zdXBwb3J0ZWRJdGVyYWJsZVRvQXJyYXkobywgbWluTGVuKSB7XG4gIGlmICghbykgcmV0dXJuO1xuICBpZiAodHlwZW9mIG8gPT09IFwic3RyaW5nXCIpIHJldHVybiBhcnJheUxpa2VUb0FycmF5KG8sIG1pbkxlbik7XG4gIHZhciBuID0gT2JqZWN0LnByb3RvdHlwZS50b1N0cmluZy5jYWxsKG8pLnNsaWNlKDgsIC0xKTtcbiAgaWYgKG4gPT09IFwiT2JqZWN0XCIgJiYgby5jb25zdHJ1Y3RvcikgbiA9IG8uY29uc3RydWN0b3IubmFtZTtcbiAgaWYgKG4gPT09IFwiTWFwXCIgfHwgbiA9PT0gXCJTZXRcIikgcmV0dXJuIEFycmF5LmZyb20obyk7XG4gIGlmIChuID09PSBcIkFyZ3VtZW50c1wiIHx8IC9eKD86VWl8SSludCg/Ojh8MTZ8MzIpKD86Q2xhbXBlZCk/QXJyYXkkLy50ZXN0KG4pKSByZXR1cm4gYXJyYXlMaWtlVG9BcnJheShvLCBtaW5MZW4pO1xufSIsIi8vIGV4dHJhY3RlZCBieSBtaW5pLWNzcy1leHRyYWN0LXBsdWdpbiIsIi8vIFRoZSBtb2R1bGUgY2FjaGVcbnZhciBfX3dlYnBhY2tfbW9kdWxlX2NhY2hlX18gPSB7fTtcblxuLy8gVGhlIHJlcXVpcmUgZnVuY3Rpb25cbmZ1bmN0aW9uIF9fd2VicGFja19yZXF1aXJlX18obW9kdWxlSWQpIHtcblx0Ly8gQ2hlY2sgaWYgbW9kdWxlIGlzIGluIGNhY2hlXG5cdHZhciBjYWNoZWRNb2R1bGUgPSBfX3dlYnBhY2tfbW9kdWxlX2NhY2hlX19bbW9kdWxlSWRdO1xuXHRpZiAoY2FjaGVkTW9kdWxlICE9PSB1bmRlZmluZWQpIHtcblx0XHRyZXR1cm4gY2FjaGVkTW9kdWxlLmV4cG9ydHM7XG5cdH1cblx0Ly8gQ3JlYXRlIGEgbmV3IG1vZHVsZSAoYW5kIHB1dCBpdCBpbnRvIHRoZSBjYWNoZSlcblx0dmFyIG1vZHVsZSA9IF9fd2VicGFja19tb2R1bGVfY2FjaGVfX1ttb2R1bGVJZF0gPSB7XG5cdFx0Ly8gbm8gbW9kdWxlLmlkIG5lZWRlZFxuXHRcdC8vIG5vIG1vZHVsZS5sb2FkZWQgbmVlZGVkXG5cdFx0ZXhwb3J0czoge31cblx0fTtcblxuXHQvLyBFeGVjdXRlIHRoZSBtb2R1bGUgZnVuY3Rpb25cblx0X193ZWJwYWNrX21vZHVsZXNfX1ttb2R1bGVJZF0obW9kdWxlLCBtb2R1bGUuZXhwb3J0cywgX193ZWJwYWNrX3JlcXVpcmVfXyk7XG5cblx0Ly8gUmV0dXJuIHRoZSBleHBvcnRzIG9mIHRoZSBtb2R1bGVcblx0cmV0dXJuIG1vZHVsZS5leHBvcnRzO1xufVxuXG4iLCIvLyBnZXREZWZhdWx0RXhwb3J0IGZ1bmN0aW9uIGZvciBjb21wYXRpYmlsaXR5IHdpdGggbm9uLWhhcm1vbnkgbW9kdWxlc1xuX193ZWJwYWNrX3JlcXVpcmVfXy5uID0gKG1vZHVsZSkgPT4ge1xuXHR2YXIgZ2V0dGVyID0gbW9kdWxlICYmIG1vZHVsZS5fX2VzTW9kdWxlID9cblx0XHQoKSA9PiAobW9kdWxlWydkZWZhdWx0J10pIDpcblx0XHQoKSA9PiAobW9kdWxlKTtcblx0X193ZWJwYWNrX3JlcXVpcmVfXy5kKGdldHRlciwgeyBhOiBnZXR0ZXIgfSk7XG5cdHJldHVybiBnZXR0ZXI7XG59OyIsIi8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb25zIGZvciBoYXJtb255IGV4cG9ydHNcbl9fd2VicGFja19yZXF1aXJlX18uZCA9IChleHBvcnRzLCBkZWZpbml0aW9uKSA9PiB7XG5cdGZvcih2YXIga2V5IGluIGRlZmluaXRpb24pIHtcblx0XHRpZihfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZGVmaW5pdGlvbiwga2V5KSAmJiAhX193ZWJwYWNrX3JlcXVpcmVfXy5vKGV4cG9ydHMsIGtleSkpIHtcblx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBrZXksIHsgZW51bWVyYWJsZTogdHJ1ZSwgZ2V0OiBkZWZpbml0aW9uW2tleV0gfSk7XG5cdFx0fVxuXHR9XG59OyIsIl9fd2VicGFja19yZXF1aXJlX18ubyA9IChvYmosIHByb3ApID0+IChPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwob2JqLCBwcm9wKSkiLCIvLyBkZWZpbmUgX19lc01vZHVsZSBvbiBleHBvcnRzXG5fX3dlYnBhY2tfcmVxdWlyZV9fLnIgPSAoZXhwb3J0cykgPT4ge1xuXHRpZih0eXBlb2YgU3ltYm9sICE9PSAndW5kZWZpbmVkJyAmJiBTeW1ib2wudG9TdHJpbmdUYWcpIHtcblx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgU3ltYm9sLnRvU3RyaW5nVGFnLCB7IHZhbHVlOiAnTW9kdWxlJyB9KTtcblx0fVxuXHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgJ19fZXNNb2R1bGUnLCB7IHZhbHVlOiB0cnVlIH0pO1xufTsiLCJcInVzZSBzdHJpY3RcIjtcblxuaW1wb3J0ICcuL2FkbWluLmxlc3MnO1xuXG5jb25zdCBBRE1JTl9PQkpFQ1QgPSB3aW5kb3cuZGR3Y3Bvc0FkbWluT2JqIHx8IHt9O1xuY29uc3QgQUpBWF9DT05GSUcgPSBBRE1JTl9PQkpFQ1QuYWpheCB8fCB7fTtcbmNvbnN0IEkxOE4gPSBBRE1JTl9PQkpFQ1QuaTE4biB8fCB7fTtcbmNvbnN0IENPTkZJRyA9IEFETUlOX09CSkVDVC5kZHdjcG9zX2NvbmZpZ3VyYXRpb24gfHwge307XG5jb25zdCBJTlZBTElEX0hUTUxfUEFUVEVSTiA9IC88XFxzP1tePl0qXFwvP1xccz8+L2k7XG5jb25zdCBGT1JNX1NFTEVDVE9SID0gJ2Zvcm0jZGR3Y3Bvcy1wYXltZW50cy1jb250YWluZXIsIGZvcm0jZGR3Y3Bvcy10YWJsZXMtY29udGFpbmVyJztcbmNvbnN0IFBST0RVQ1RfQUNUSU9OX1NFTEVDVE9SID0gJy5kZHdjcG9zLXByb2R1Y3QtYWN0aW9uJztcbmNvbnN0IEJBUkNPREVfSU5QVVRfU0VMRUNUT1IgPSAnLmRkd2Nwb3MtYmFyY29kZSwgLmRkd2Nwb3MtYmFyY29kZS1xdWFudGl0eSwgLmRkd2Nwb3MtY3VzdG9tLXN0b2NrJztcblxuY29uc3Qgb25SZWFkeSA9IGNhbGxiYWNrID0+IHtcbiAgICBpZiAoJ2xvYWRpbmcnID09PSBkb2N1bWVudC5yZWFkeVN0YXRlKSB7XG4gICAgICAgIGRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIoJ0RPTUNvbnRlbnRMb2FkZWQnLCBjYWxsYmFjayk7XG4gICAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICBjYWxsYmFjaygpO1xufTtcblxuY29uc3QgcmVtb3ZlRWxlbWVudHMgPSBlbGVtZW50cyA9PiB7XG4gICAgWy4uLmVsZW1lbnRzXS5mb3JFYWNoKGVsZW1lbnQgPT4gZWxlbWVudC5yZW1vdmUoKSk7XG59O1xuXG5jb25zdCBnZXRUZW1wbGF0ZSA9IHRlbXBsYXRlSWQgPT4gKFxuICAgIHdpbmRvdy53cCAmJiB3cC50ZW1wbGF0ZSAmJiB0ZW1wbGF0ZUlkID8gd3AudGVtcGxhdGUodGVtcGxhdGVJZCkgOiBudWxsXG4pO1xuXG5jb25zdCBzaG93VmFsaWRhdGlvbk5vdGljZSA9IGZvcm0gPT4ge1xuICAgIGNvbnN0IGludmFsaWRGb3JtRmllbGRzID0gZ2V0VGVtcGxhdGUoJ2Rkd2Nwb3NfZm9ybV9kYXRhX2Vycm9yJyk7XG5cbiAgICBpZiAoaW52YWxpZEZvcm1GaWVsZHMpIHtcbiAgICAgICAgZm9ybS5pbnNlcnRBZGphY2VudEhUTUwoJ2JlZm9yZUJlZ2luJywgaW52YWxpZEZvcm1GaWVsZHMoKSk7XG4gICAgfVxufTtcblxuY29uc3Qgc2hvdWxkVmFsaWRhdGVJbnB1dCA9IGlucHV0ID0+IChcbiAgICBpbnB1dFxuICAgICYmICFpbnB1dC5kaXNhYmxlZFxuICAgICYmICFbJ2hpZGRlbicsICdzdWJtaXQnLCAnYnV0dG9uJ10uaW5jbHVkZXMoaW5wdXQudHlwZSlcbiAgICAmJiAhaW5wdXQuY2xhc3NMaXN0LmNvbnRhaW5zKCdkZHdjcG9zLWhpZGUnKVxuKTtcblxuY29uc3Qgc2V0SW5wdXRJbnZhbGlkID0gaW5wdXQgPT4ge1xuICAgIGlucHV0LnN0eWxlLmJvcmRlckNvbG9yID0gJ3JlZCc7XG59O1xuXG5jb25zdCByZXNldElucHV0U3RhdGUgPSBpbnB1dCA9PiB7XG4gICAgaW5wdXQuc3R5bGUuYm9yZGVyQ29sb3IgPSAnJztcbn07XG5cbmNvbnN0IGlzSW52YWxpZElucHV0VmFsdWUgPSBpbnB1dCA9PiB7XG4gICAgY29uc3QgaW5wdXRWYWx1ZSA9IFN0cmluZyhpbnB1dC52YWx1ZSB8fCAnJykudHJpbSgpO1xuXG4gICAgcmV0dXJuICFpbnB1dFZhbHVlIHx8ICctMScgPT09IGlucHV0VmFsdWUgfHwgSU5WQUxJRF9IVE1MX1BBVFRFUk4udGVzdChpbnB1dFZhbHVlKTtcbn07XG5cbmNvbnN0IHZhbGlkYXRlRm9ybSA9IChmb3JtLCBldmVudCkgPT4ge1xuICAgIGxldCBoYXNFcnJvciA9IGZhbHNlO1xuXG4gICAgWy4uLmZvcm0uZWxlbWVudHNdLmZvckVhY2goaW5wdXQgPT4ge1xuICAgICAgICByZXNldElucHV0U3RhdGUoaW5wdXQpO1xuXG4gICAgICAgIGlmICghc2hvdWxkVmFsaWRhdGVJbnB1dChpbnB1dCkgfHwgIWlzSW52YWxpZElucHV0VmFsdWUoaW5wdXQpKSB7XG4gICAgICAgICAgICByZXR1cm47XG4gICAgICAgIH1cblxuICAgICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICBzZXRJbnB1dEludmFsaWQoaW5wdXQpO1xuICAgICAgICBoYXNFcnJvciA9IHRydWU7XG4gICAgfSk7XG5cbiAgICBpZiAoaGFzRXJyb3IpIHtcbiAgICAgICAgc2hvd1ZhbGlkYXRpb25Ob3RpY2UoZm9ybSk7XG4gICAgfVxufTtcblxuY29uc3QgYmluZENvbmZpZ3VyYXRpb25Gb3JtcyA9ICgpID0+IHtcbiAgICBjb25zdCBmb3JtcyA9IGRvY3VtZW50LnF1ZXJ5U2VsZWN0b3JBbGwoRk9STV9TRUxFQ1RPUik7XG5cbiAgICBpZiAoIWZvcm1zLmxlbmd0aCkge1xuICAgICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgZm9ybXMuZm9yRWFjaChmb3JtID0+IHtcbiAgICAgICAgZm9ybS5hZGRFdmVudExpc3RlbmVyKCdzdWJtaXQnLCBldmVudCA9PiB7XG4gICAgICAgICAgICByZW1vdmVFbGVtZW50cyhkb2N1bWVudC5xdWVyeVNlbGVjdG9yQWxsKCcubm90aWNlJykpO1xuICAgICAgICAgICAgdmFsaWRhdGVGb3JtKGZvcm0sIGV2ZW50KTtcbiAgICAgICAgfSk7XG4gICAgfSk7XG59O1xuXG5jb25zdCBnZXRSZXBlYXRlclRhcmdldCA9IGJ1dHRvbiA9PiB7XG4gICAgY29uc3QgaW52b2ljZUNhcmQgPSBidXR0b24uY2xvc2VzdCgnLmRkd2Nwb3MtaW52b2ljZS1jYXJkJyk7XG5cbiAgICBpZiAoaW52b2ljZUNhcmQpIHtcbiAgICAgICAgcmV0dXJuIGludm9pY2VDYXJkO1xuICAgIH1cblxuICAgIHJldHVybiBidXR0b24uY2xvc2VzdCgndHInKTtcbn07XG5cbmNvbnN0IGFkZFJlcGVhdGVyUm93ID0gYnV0dG9uID0+IHtcbiAgICBjb25zdCBmb3JtID0gYnV0dG9uLmNsb3Nlc3QoJ2Zvcm0nKTtcbiAgICBjb25zdCB0ZW1wbGF0ZUlkID0gYnV0dG9uLmdldEF0dHJpYnV0ZSgnZGF0YS10ZW1wbGF0ZScpO1xuICAgIGNvbnN0IHRlbXBsYXRlID0gZ2V0VGVtcGxhdGUodGVtcGxhdGVJZCk7XG4gICAgY29uc3QgbWF4SW5kZXhFbGVtZW50ID0gZm9ybSA/IGZvcm0ucXVlcnlTZWxlY3RvcignI2Rkd2Nwb3MtbWF4LWluZGV4JykgOiBudWxsO1xuICAgIGNvbnN0IHRhcmdldCA9IGdldFJlcGVhdGVyVGFyZ2V0KGJ1dHRvbik7XG5cbiAgICBpZiAoIXRlbXBsYXRlIHx8ICFtYXhJbmRleEVsZW1lbnQgfHwgIXRhcmdldCkge1xuICAgICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgY29uc3Qgcm93SW5kZXggPSBOdW1iZXIucGFyc2VJbnQobWF4SW5kZXhFbGVtZW50LnZhbHVlLCAxMCkgfHwgMDtcbiAgICBjb25zdCBuZXh0SW5kZXggPSByb3dJbmRleCArIDE7XG5cbiAgICBtYXhJbmRleEVsZW1lbnQudmFsdWUgPSBuZXh0SW5kZXg7XG4gICAgdGFyZ2V0Lmluc2VydEFkamFjZW50SFRNTCgnYmVmb3JlQmVnaW4nLCB0ZW1wbGF0ZSh7IGtleTogbmV4dEluZGV4IH0pKTtcbn07XG5cbmNvbnN0IHJlbW92ZVJlcGVhdGVyUm93ID0gYnV0dG9uID0+IHtcbiAgICBjb25zdCB0YXJnZXQgPSBnZXRSZXBlYXRlclRhcmdldChidXR0b24pO1xuXG4gICAgaWYgKHRhcmdldCkge1xuICAgICAgICB0YXJnZXQucmVtb3ZlKCk7XG4gICAgfVxufTtcblxuY29uc3QgYmluZFJlcGVhdGVyUm93cyA9ICgpID0+IHtcbiAgICBkb2N1bWVudC5hZGRFdmVudExpc3RlbmVyKCdjbGljaycsIGV2ZW50ID0+IHtcbiAgICAgICAgY29uc3QgYWRkUm93QnV0dG9uID0gZXZlbnQudGFyZ2V0LmNsb3Nlc3QoJy5kZHdjcG9zLWFkZC1yb3cnKTtcbiAgICAgICAgY29uc3QgcmVtb3ZlUm93QnV0dG9uID0gZXZlbnQudGFyZ2V0LmNsb3Nlc3QoJy5kZHdjcG9zLXJlbW92ZS1yb3cnKTtcblxuICAgICAgICBpZiAoYWRkUm93QnV0dG9uKSB7XG4gICAgICAgICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuICAgICAgICAgICAgYWRkUmVwZWF0ZXJSb3coYWRkUm93QnV0dG9uKTtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmIChyZW1vdmVSb3dCdXR0b24pIHtcbiAgICAgICAgICAgIGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG4gICAgICAgICAgICByZW1vdmVSZXBlYXRlclJvdyhyZW1vdmVSb3dCdXR0b24pO1xuICAgICAgICB9XG4gICAgfSk7XG59O1xuXG5jb25zdCBnZXRCYXJjb2RlUHJpbnRTdHlsZSA9ICgpID0+IGA8c3R5bGUgdHlwZT1cInRleHQvY3NzXCI+XG4gICAgQHBhZ2Uge1xuICAgICAgICBzaXplOiAke0NPTkZJRy5iYXJjb2RlX3ByaW50ZXJfd2lkdGh9ICR7Q09ORklHLmJhcmNvZGVfcHJpbnRlcl9oZWlnaHR9O1xuICAgICAgICBtYXJnaW46ICR7Q09ORklHLmJhcmNvZGVfcHJpbnRlcl9tYXJnaW59O1xuICAgICAgICB0ZXh0LWFsaWduOiBjZW50ZXI7XG4gICAgfVxuPC9zdHlsZT5gO1xuXG5jb25zdCBnZXRCYXJjb2RlUHJpbnRDb250ZW50ID0gKHJvdywgcXVhbnRpdHkpID0+IHtcbiAgICBjb25zdCBwcmludENvbnRlbnRFbGVtZW50ID0gcm93LnF1ZXJ5U2VsZWN0b3IoJy5kZHdjcG9zLWJhcmNvZGUtcHJpbnQtY29udGVudCcpO1xuICAgIGNvbnN0IHByaW50Q29udGVudCA9IHByaW50Q29udGVudEVsZW1lbnQgPyBwcmludENvbnRlbnRFbGVtZW50LmlubmVySFRNTCA6ICcnO1xuXG4gICAgcmV0dXJuIEFycmF5LmZyb20oeyBsZW5ndGg6IHF1YW50aXR5IH0sICgpID0+IHByaW50Q29udGVudCkuam9pbignJyk7XG59O1xuXG5jb25zdCBpc01vYmlsZURldmljZSA9ICgpID0+IC9BbmRyb2lkfHdlYk9TfGlQaG9uZXxpUGFkfGlQb2R8QmxhY2tCZXJyeXxJRU1vYmlsZXxPcGVyYSBNaW5pL2kudGVzdChuYXZpZ2F0b3IudXNlckFnZW50KTtcblxuY29uc3QgcHJpbnRJblBvcHVwID0gaHRtbCA9PiB7XG4gICAgY29uc3QgcHJpbnRXaW5kb3cgPSB3aW5kb3cub3BlbignJywgJ1BSSU5UJywgJ2hlaWdodD00MDAsd2lkdGg9NjAwJyk7XG5cbiAgICBpZiAoIXByaW50V2luZG93KSB7XG4gICAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICBwcmludFdpbmRvdy5kb2N1bWVudC5vcGVuKCk7XG4gICAgcHJpbnRXaW5kb3cuZG9jdW1lbnQuY2xlYXIoKTtcbiAgICBwcmludFdpbmRvdy5kb2N1bWVudC53cml0ZWxuKGh0bWwpO1xuICAgIHByaW50V2luZG93LmRvY3VtZW50LmNsb3NlKCk7XG5cbiAgICBwcmludFdpbmRvdy5hZGRFdmVudExpc3RlbmVyKCdsb2FkJywgKCkgPT4ge1xuICAgICAgICBzZXRUaW1lb3V0KCgpID0+IHtcbiAgICAgICAgICAgIHByaW50V2luZG93LmZvY3VzKCk7XG4gICAgICAgICAgICBwcmludFdpbmRvdy5wcmludCgpO1xuICAgICAgICB9LCA3MDApO1xuICAgIH0sIHRydWUpO1xufTtcblxuY29uc3QgcHJpbnRJbkZyYW1lID0gaHRtbCA9PiB7XG4gICAgY29uc3QgZnJhbWUgPSBkb2N1bWVudC5jcmVhdGVFbGVtZW50KCdpZnJhbWUnKTtcbiAgICBmcmFtZS5uYW1lID0gJ2Rkd2Nwb3MtYmFyY29kZS1wcmludC1mcmFtZSc7XG4gICAgZG9jdW1lbnQuYm9keS5hcHBlbmRDaGlsZChmcmFtZSk7XG5cbiAgICBjb25zdCBmcmFtZURvY3VtZW50ID0gZnJhbWUuY29udGVudFdpbmRvdyA/IGZyYW1lLmNvbnRlbnRXaW5kb3cuZG9jdW1lbnQgOiBmcmFtZS5jb250ZW50RG9jdW1lbnQ7XG5cbiAgICBmcmFtZURvY3VtZW50Lm9wZW4oKTtcbiAgICBmcmFtZURvY3VtZW50LndyaXRlKGh0bWwpO1xuICAgIGZyYW1lRG9jdW1lbnQuY2xvc2UoKTtcblxuICAgIHNldFRpbWVvdXQoKCkgPT4ge1xuICAgICAgICBmcmFtZS5jb250ZW50V2luZG93LmZvY3VzKCk7XG4gICAgICAgIGZyYW1lLmNvbnRlbnRXaW5kb3cucHJpbnQoKTtcbiAgICAgICAgZG9jdW1lbnQuYm9keS5yZW1vdmVDaGlsZChmcmFtZSk7XG4gICAgfSwgNzAwKTtcbn07XG5cbmNvbnN0IGhhbmRsZUJhcmNvZGVQcmludCA9IHJvdyA9PiB7XG4gICAgY29uc3QgcXVhbnRpdHlFbGVtZW50ID0gcm93LnF1ZXJ5U2VsZWN0b3IoJy5kZHdjcG9zLWJhcmNvZGUtcXVhbnRpdHknKTtcbiAgICBjb25zdCBxdWFudGl0eSA9IHF1YW50aXR5RWxlbWVudCA/IE51bWJlci5wYXJzZUludChxdWFudGl0eUVsZW1lbnQudmFsdWUsIDEwKSA6IDA7XG5cbiAgICBpZiAoIXF1YW50aXR5KSB7XG4gICAgICAgIGFsZXJ0KEkxOE4uYmFyY29kZVF1YW50aXR5RXJyb3IpO1xuICAgICAgICByZXR1cm47XG4gICAgfVxuXG4gICAgcXVhbnRpdHlFbGVtZW50LnZhbHVlID0gJyc7XG5cbiAgICBjb25zdCBodG1sID0gYDxodG1sPjxoZWFkPjx0aXRsZT5CYXJjb2RlPC90aXRsZT4ke2dldEJhcmNvZGVQcmludFN0eWxlKCl9PC9oZWFkPjxib2R5PiR7Z2V0QmFyY29kZVByaW50Q29udGVudChyb3csIHF1YW50aXR5KX08L2JvZHk+PC9odG1sPmA7XG5cbiAgICBpZiAoaXNNb2JpbGVEZXZpY2UoKSkge1xuICAgICAgICBwcmludEluUG9wdXAoaHRtbCk7XG4gICAgICAgIHJldHVybjtcbiAgICB9XG5cbiAgICBwcmludEluRnJhbWUoaHRtbCk7XG59O1xuXG5jb25zdCBiaW5kUHJvZHVjdEFjdGlvbnMgPSAoKSA9PiB7XG4gICAgZG9jdW1lbnQuYWRkRXZlbnRMaXN0ZW5lcignY2xpY2snLCBldmVudCA9PiB7XG4gICAgICAgIGNvbnN0IGJ1dHRvbiA9IGV2ZW50LnRhcmdldC5jbG9zZXN0KFBST0RVQ1RfQUNUSU9OX1NFTEVDVE9SKTtcblxuICAgICAgICBpZiAoIWJ1dHRvbikge1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG5cbiAgICAgICAgZXZlbnQucHJldmVudERlZmF1bHQoKTtcblxuICAgICAgICBjb25zdCByb3cgPSBidXR0b24uY2xvc2VzdCgndHInKTtcbiAgICAgICAgY29uc3QgcHJvZHVjdEFjdGlvbiA9IGJ1dHRvbi5nZXRBdHRyaWJ1dGUoJ2RhdGEtYWN0aW9uJyk7XG5cbiAgICAgICAgaWYgKCFyb3cpIHtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIGlmICgncHJpbnRfYmFyY29kZScgPT09IHByb2R1Y3RBY3Rpb24pIHtcbiAgICAgICAgICAgIGhhbmRsZUJhcmNvZGVQcmludChyb3cpO1xuICAgICAgICAgICAgcmV0dXJuO1xuICAgICAgICB9XG4gICAgfSk7XG59O1xuXG5jb25zdCBiaW5kQmFyY29kZUVudGVyS2V5ID0gKCkgPT4ge1xuICAgIGRvY3VtZW50LmFkZEV2ZW50TGlzdGVuZXIoJ2tleWRvd24nLCBldmVudCA9PiB7XG4gICAgICAgIGlmICghZXZlbnQudGFyZ2V0Lm1hdGNoZXMoQkFSQ09ERV9JTlBVVF9TRUxFQ1RPUikgfHwgKCdFbnRlcicgIT09IGV2ZW50LmtleSAmJiAxMyAhPT0gZXZlbnQua2V5Q29kZSkpIHtcbiAgICAgICAgICAgIHJldHVybjtcbiAgICAgICAgfVxuXG4gICAgICAgIGNvbnN0IG5leHRFbGVtZW50ID0gZXZlbnQudGFyZ2V0Lm5leHRFbGVtZW50U2libGluZztcblxuICAgICAgICBldmVudC5wcmV2ZW50RGVmYXVsdCgpO1xuXG4gICAgICAgIGlmIChuZXh0RWxlbWVudCkge1xuICAgICAgICAgICAgbmV4dEVsZW1lbnQuY2xpY2soKTtcbiAgICAgICAgfVxuICAgIH0pO1xufTtcblxub25SZWFkeSgoKSA9PiB7XG4gICAgYmluZENvbmZpZ3VyYXRpb25Gb3JtcygpO1xuICAgIGJpbmRSZXBlYXRlclJvd3MoKTtcbiAgICBiaW5kUHJvZHVjdEFjdGlvbnMoKTtcbiAgICBiaW5kQmFyY29kZUVudGVyS2V5KCk7XG59KTtcbiJdLCJzb3VyY2VSb290IjoiIn0=