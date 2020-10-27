this.eventespresso=this.eventespresso||{},this.eventespresso.events=function(t){var e={};function n(r){if(e[r])return e[r].exports;var o=e[r]={i:r,l:!1,exports:{}};return t[r].call(o.exports,o,o.exports,n),o.l=!0,o.exports}return n.m=t,n.c=e,n.d=function(t,e,r){n.o(t,e)||Object.defineProperty(t,e,{enumerable:!0,get:r})},n.r=function(t){"undefined"!==typeof Symbol&&Symbol.toStringTag&&Object.defineProperty(t,Symbol.toStringTag,{value:"Module"}),Object.defineProperty(t,"__esModule",{value:!0})},n.t=function(t,e){if(1&e&&(t=n(t)),8&e)return t;if(4&e&&"object"===typeof t&&t&&t.__esModule)return t;var r=Object.create(null);if(n.r(r),Object.defineProperty(r,"default",{enumerable:!0,value:t}),2&e&&"string"!=typeof t)for(var o in t)n.d(r,o,function(e){return t[e]}.bind(null,o));return r},n.n=function(t){var e=t&&t.__esModule?function(){return t.default}:function(){return t};return n.d(e,"a",e),e},n.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},n.p="/",n(n.s=2469)}({112:function(t,e){function n(e){return"function"===typeof Symbol&&"symbol"===typeof Symbol.iterator?t.exports=n=function(t){return typeof t}:t.exports=n=function(t){return t&&"function"===typeof Symbol&&t.constructor===Symbol&&t!==Symbol.prototype?"symbol":typeof t},n(e)}t.exports=n},135:function(t,e,n){var r=n(483);t.exports=function(t,e){if("function"!==typeof e&&null!==e)throw new TypeError("Super expression must either be null or a function");t.prototype=Object.create(e&&e.prototype,{constructor:{value:t,writable:!0,configurable:!0}}),e&&r(t,e)}},144:function(t,e,n){var r=n(112),o=n(88);t.exports=function(t,e){return!e||"object"!==r(e)&&"function"!==typeof e?o(t):e}},1458:function(t,e,n){"use strict";var r=n(63),o=n(179),u=this&&this.__importDefault||function(t){return t&&t.__esModule?t:{default:t}};Object.defineProperty(e,"__esModule",{value:!0}),e.EventEmitter=void 0;var i=u(n(1459)),f=u(n(2472)),c=function(){function t(){r(this,t),this._events=Object.create(null)}return o(t,[{key:"on",value:function(t,e){this._addEventListener(t,new i.default(this,e))}},{key:"once",value:function(t,e){this._addEventListener(t,new f.default(this,e,1))}},{key:"off",value:function(t,e){var n=this._events[t];(null===n||void 0===n?void 0:n.length)&&(this._events[t]=n.filter((function(t){return t.getHandler()!==e})))}},{key:"offByName",value:function(t){var e=this._events[t];void 0!==e&&(e.length=0,delete this._events[t])}},{key:"offAll",value:function(){var t=this;Object.keys(this._events).forEach((function(e){var n=t._events[e];n&&(n.length=0)})),this._events=Object.create(null)}},{key:"emit",value:function(t){for(var e=arguments.length,n=new Array(e>1?e-1:0),r=1;r<e;r++)n[r-1]=arguments[r];var o=this._events[t];o&&o.forEach((function(e){return e.run.apply(e,[t].concat(n))}))}},{key:"has",value:function(t,e){var n=this._events[t];if(!n)return!1;for(var r=0;r<n.length;r++)if(n[r].getHandler()===e)return!0;return!1}},{key:"_addEventListener",value:function(t,e){var n=this._events[t];Array.isArray(n)?n.push(e):this._events[t]=[e]}}]),t}();e.EventEmitter=c},1459:function(t,e,n){"use strict";var r=n(63),o=n(179),u=n(135),i=n(439),f=this&&this.__importDefault||function(t){return t&&t.__esModule?t:{default:t}};Object.defineProperty(e,"__esModule",{value:!0});var c=function(t){u(n,t);var e=i(n);function n(){return r(this,n),e.apply(this,arguments)}return o(n,[{key:"run",value:function(t){for(var e=arguments.length,n=new Array(e>1?e-1:0),r=1;r<e;r++)n[r-1]=arguments[r];this.handler.apply(null,n)}}]),n}(f(n(2471)).default);e.default=c},179:function(t,e){function n(t,e){for(var n=0;n<e.length;n++){var r=e[n];r.enumerable=r.enumerable||!1,r.configurable=!0,"value"in r&&(r.writable=!0),Object.defineProperty(t,r.key,r)}}t.exports=function(t,e,r){return e&&n(t.prototype,e),r&&n(t,r),t}},2469:function(t,e,n){t.exports=n(2470)},2470:function(t,e,n){"use strict";n.r(e);var r=n(1458);for(var o in r)["default"].indexOf(o)<0&&function(t){n.d(e,t,(function(){return r[t]}))}(o)},2471:function(t,e,n){"use strict";var r=n(63),o=n(179);Object.defineProperty(e,"__esModule",{value:!0});var u=function(){function t(e,n){r(this,t),this.emitter=e,this.handler=n}return o(t,[{key:"getHandler",value:function(){return this.handler}}]),t}();e.default=u},2472:function(t,e,n){"use strict";var r=n(63),o=n(179),u=n(2473),i=n(98),f=n(135),c=n(439),a=this&&this.__importDefault||function(t){return t&&t.__esModule?t:{default:t}};Object.defineProperty(e,"__esModule",{value:!0});var s=function(t){f(n,t);var e=c(n);function n(t,o){var u,i=arguments.length>2&&void 0!==arguments[2]?arguments[2]:1;if(r(this,n),(u=e.call(this,t,o)).total=i,u.i=0,u.total<1)throw new Error("{total} can not be less than 1");return u}return o(n,[{key:"run",value:function(t){for(var e,r=arguments.length,o=new Array(r>1?r-1:0),f=1;f<r;f++)o[f-1]=arguments[f];(e=u(i(n.prototype),"run",this)).call.apply(e,[this,t].concat(o)),++this.i>=this.total&&this.emitter.off(t,this.handler)}}]),n}(a(n(1459)).default);e.default=s},2473:function(t,e,n){var r=n(2474);function o(e,n,u){return"undefined"!==typeof Reflect&&Reflect.get?t.exports=o=Reflect.get:t.exports=o=function(t,e,n){var o=r(t,e);if(o){var u=Object.getOwnPropertyDescriptor(o,e);return u.get?u.get.call(n):u.value}},o(e,n,u||e)}t.exports=o},2474:function(t,e,n){var r=n(98);t.exports=function(t,e){for(;!Object.prototype.hasOwnProperty.call(t,e)&&null!==(t=r(t)););return t}},439:function(t,e,n){var r=n(98),o=n(515),u=n(144);t.exports=function(t){var e=o();return function(){var n,o=r(t);if(e){var i=r(this).constructor;n=Reflect.construct(o,arguments,i)}else n=o.apply(this,arguments);return u(this,n)}}},483:function(t,e){function n(e,r){return t.exports=n=Object.setPrototypeOf||function(t,e){return t.__proto__=e,t},n(e,r)}t.exports=n},515:function(t,e){t.exports=function(){if("undefined"===typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"===typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(t){return!1}}},63:function(t,e){t.exports=function(t,e){if(!(t instanceof e))throw new TypeError("Cannot call a class as a function")}},88:function(t,e){t.exports=function(t){if(void 0===t)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return t}},98:function(t,e){function n(e){return t.exports=n=Object.setPrototypeOf?Object.getPrototypeOf:function(t){return t.__proto__||Object.getPrototypeOf(t)},n(e)}t.exports=n}});
//# sourceMappingURL=events.6587f1b2.js.map