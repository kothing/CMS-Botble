(()=>{function t(n){return t="function"==typeof Symbol&&"symbol"==typeof Symbol.iterator?function(t){return typeof t}:function(t){return t&&"function"==typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},t(n)}function n(n,o){for(var e=0;e<o.length;e++){var r=o[e];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(n,(i=r.key,u=void 0,u=function(n,o){if("object"!==t(n)||null===n)return n;var e=n[Symbol.toPrimitive];if(void 0!==e){var r=e.call(n,o||"default");if("object"!==t(r))return r;throw new TypeError("@@toPrimitive must return a primitive value.")}return("string"===o?String:Number)(n)}(i,"string"),"symbol"===t(u)?u:String(u)),r)}var i,u}var o=function(){function t(){!function(t,n){if(!(t instanceof n))throw new TypeError("Cannot call a class as a function")}(this,t)}var o,e,r;return o=t,(e=[{key:"init",value:function(){$(document).find(".js-phone-number-mask").each((function(t,n){window.intlTelInput(n,{geoIpLookup:function(t){$.get("https://ipinfo.io",(function(){}),"jsonp").always((function(n){t(n&&n.country?n.country:"")}))},initialCountry:"auto",utilsScript:"/vendor/core/base/libraries/intl-tel-input/js/utils.js"})}))}}])&&n(o.prototype,e),r&&n(o,r),Object.defineProperty(o,"prototype",{writable:!1}),t}();$(document).ready((function(){(new o).init(),document.addEventListener("payment-form-reloaded",(function(){(new o).init()}))}))})();