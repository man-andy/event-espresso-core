(this.webpackJsonproot=this.webpackJsonproot||[]).push([[23],{2807:function(e,t,n){},2808:function(e,t,n){"use strict";var a=n(0),c=n.n(a),l=n(4),r=n(18),i=n(15),s={placement:"top"};t.a=function(e){var t=e.ticket,n=Object(i.useRegistrationsLink)({ticket_id:t.dbId}),a=Object(l.__)("total registrations."),o=Object(l.__)("view ALL registrations for this ticket.");return c.a.createElement(r.ItemCount,{count:t.registrationCount,emphasizeZero:!1,title:a},c.a.createElement(r.RegistrationsLink,{href:n,tooltip:o,tooltipProps:s}))}},2809:function(e,t,n){"use strict";var a=n(0),c=n.n(a),l=n(4),r=n(24),i=n(18),s=n(15);t.a=function(e){var t=e.entity,n=Object(s.useTicketMutator)(t.id).updateEntity,o=Object(a.useCallback)((function(e){var a=Object(r.parseInfinity)(e);a!==t.quantity&&n({quantity:a})}),[t.quantity,n]);return c.a.createElement(i.InlineEditInfinity,{onChange:o,value:"".concat(t.quantity),tooltip:Object(l.__)("edit quantity of tickets available\u2026")})}},2810:function(e,t,n){"use strict";n.d(t,"a",(function(){return s})),n.d(t,"b",(function(){return j}));var a=n(0),c=n.n(a),l=n(4),r=n(15),i=n(18),s=function(e){var t=e.className,n=e.entity,s=e.view,o=void 0===s?"card":s,u=Object(r.useTicketMutator)(n.id).updateEntity,b="card"===o&&2,m=Object(a.useCallback)((function(e){e!==n.name&&u({name:e})}),[n.name,u]),p=Object(l.__)("edit title\u2026"),d=n.name||p;return c.a.createElement(i.InlineEditText,{className:t,fitText:"card"===o,lineCount:b,onChange:m,tag:"table"===o?"div":"h4",tooltip:p,value:d})},o=n(30),u=n(250),b=n(43),m=n.n(b),p=n(6),d=n.n(p),O=n(13);function f(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var a=Object.getOwnPropertySymbols(e);t&&(a=a.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,a)}return n}function y(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?f(Object(n),!0).forEach((function(t){d()(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):f(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}var k=function(e){var t=Object(u.useInitialState)({ticketId:e}),n=Object(u.useDefaultBasePrice)(),c=Object(u.useMutatePrices)(),l=Object(r.useTicketMutator)(e).updateEntity;return Object(a.useCallback)((function(e){var a,r,i=t(null),s=y(y({},null===(a=i)||void 0===a?void 0:a.ticket),{},{price:e});if(i=y(y({},i),{},{ticket:s}),!Object(O.getBasePrice)(null===(r=i)||void 0===r?void 0:r.prices)){var o,b=[y(y({},n),{},{order:1,isNew:!0})].concat(m()(null===(o=i)||void 0===o?void 0:o.prices));i=y(y({},i),{},{prices:b})}var p=Object(u.calculateBasePrice)(i);c(p).then((function(t){l({price:e,reverseCalculate:!0,prices:t})}))}),[n,t,c,l])},j=function(e){var t=e.entity,n=e.className,r=k(t.id),s=Object(a.useCallback)((function(e){var n=e.amount,a=parseFloat(n);a!==t.price&&r(a)}),[r,t.price]),b=Object(o.useMemoStringify)({className:n}),m=Boolean(t.sold),p=m?u.SOLD_TICKET_ERROR_MESSAGE:Object(l.__)("edit ticket total\u2026");return c.a.createElement(i.InlineEditCurrency,{id:t.id,amount:t.price,isEditDisabled:m,placeholder:Object(l.__)("set price\u2026"),wrapperProps:b,onChange:s,tag:"h3",tooltip:p})}},2813:function(e,t,n){"use strict";var a=n(3),c=n.n(a),l=n(8),r=n.n(l),i=n(0),s=n.n(i),o=n(18),u=n(756),b=function(e){return Object(u.b)("ticket",e)};t.a=function(e){var t=e.entity,n=r()(e,["entity"]),a=b(t);return s.a.createElement(o.EntityActionsMenu,c()({},n,{menuItems:a}))}},2815:function(e,t,n){"use strict";n.r(t);var a=n(0),c=n.n(a),l=n(4),r=n(18),i=n(15),s=n(3),o=n.n(s),u=n(20),b=Object(u.withFeature)("use_bulk_edit")((function(e){var t=Object(i.useEdtrState)().visibleTicketIds;return c.a.createElement(r.ActionCheckbox,o()({},e,{visibleEntityIds:t}))})),m=function(){return Object(a.useCallback)((function(e){var t=e.displayStartOrEndDate;return{cells:[{key:"stripe",type:"cell",className:"ee-ticket-list-col-hdr ee-entity-list-status-stripe ee-rspnsv-table-column-nano",value:""},{key:"checkbox",type:"cell",className:"ee-date-list-col-hdr ee-date-list-col-checkbox ee-rspnsv-table-column-micro",value:c.a.createElement("div",{className:"ee-rspnsv-table-hide-on-mobile"},c.a.createElement(b,null))},{key:"id",type:"cell",className:"ee-ticket-list-col-hdr ee-ticket-list-col-id ee-number-column ee-rspnsv-table-column-nano",value:Object(l.__)("ID")},{key:"name",type:"cell",className:"ee-ticket-list-col-hdr ee-ticket-list-col-name ee-rspnsv-table-column-bigger",value:Object(l.__)("Name")},{key:"start",type:"cell",className:"ee-ticket-list-col-hdr ee-ticket-list-col-name-start ee-rspnsv-table-column-default",value:c.a.createElement(c.a.Fragment,null,c.a.createElement("span",{className:"ee-rspnsv-table-long-label"},Object(l.__)("Goes on Sale")),c.a.createElement("span",{className:"ee-rspnsv-table-short-label"},Object(l.__)("On Sale")))},{key:"end",type:"cell",className:"ee-ticket-list-col-hdr ee-ticket-list-col-end ee-rspnsv-table-column-default",value:c.a.createElement(c.a.Fragment,null,c.a.createElement("span",{className:"ee-rspnsv-table-long-label"},Object(l.__)("Sale Ends")),c.a.createElement("span",{className:"ee-rspnsv-table-short-label"},Object(l.__)("Ends")))},{key:"price",type:"cell",className:"ee-ticket-list-col-hdr ee-ticket-list-col-price ee-rspnsv-table-column-tiny ee-number-column",value:Object(l.__)("Price")},{key:"quantity",type:"cell",className:"ee-ticket-list-col-hdr ee-ticket-list-col-quantity ee-rspnsv-table-column-tiny ee-number-column",value:Object(l.__)("Quantity")},{key:"sold",type:"cell",className:"ee-ticket-list-col-hdr ee-ticket-list-col-sold ee-rspnsv-table-column-tiny ee-number-column",value:Object(l.__)("Sold")},{key:"registrations",type:"cell",className:"ee-ticket-list-col-hdr ee-ticket-list-col-registrations ee-rspnsv-table-column-smaller ee-centered-column",value:c.a.createElement(c.a.Fragment,null,c.a.createElement("span",{className:"ee-rspnsv-table-long-label"},Object(l.__)("Registrations")),c.a.createElement("span",{className:"ee-rspnsv-table-short-label"},Object(l.__)("Regs")))},{key:"actions",type:"cell",className:"ee-ticket-list-col-hdr ee-actions-column ee-rspnsv-table-column-big ee-centered-column",value:c.a.createElement("span",{className:"ee-rspnsv-table-long-label"},Object(l.__)("Actions"))}].filter(Object(i.filterCellByStartOrEndDate)(t)),className:"ee-editor-ticket-list-items-header-row",key:"ticket-header-row",primary:!0,type:"row"}}),[])},p=n(26),d=n(751),O=n.n(d),f=n(315),y=n.n(f),k=n(49),j=n(898),v=n(24),E=n(13),_=n(2813),h=n(2809),g=n(2810),T=n(2808),C=(n(2807),function(){var e=Object(i.useTickets)(),t=Object(a.useMemo)((function(){return Object(E.idToEntityMap)(e)}),[e]);return Object(a.useCallback)((function(e){var n=e.entityId,a=e.filterState,l=null===t||void 0===t?void 0:t[n],s=a.displayStartOrEndDate,o=a.sortingEnabled,u=Object(j.getTicketBackgroundColorClassName)(l),m=l.dbId||Object(v.shortenGuid)(l.id),d=Object(j.ticketStatus)(l),f={key:"name",type:"cell",className:"ee-ticket-list-cell ee-ticket-list-col-name ee-col-name ee-rspnsv-table-column-bigger ee-rspnsv-table-hide-on-mobile",value:o?l.name:c.a.createElement(g.a,{className:"ee-entity-list-text ee-focus-priority-5",entity:l,view:"table"})},E={key:"quantity",type:"cell",className:"ee-ticket-list-cell ee-ticket-list-col-quantity ee-rspnsv-table-column-tiny ee-number-column",value:o?l.quantity:c.a.createElement(h.a,{entity:l})},C=[{key:"stripe",type:"cell",className:"ee-ticket-list-cell ee-entity-list-status-stripe ".concat(u," ee-rspnsv-table-column-nano"),value:c.a.createElement("div",{className:"ee-rspnsv-table-show-on-mobile"},l.name)},{key:"checkbox",type:"cell",className:"ee-date-list-cell ee-date-list-col-checkbox ee-rspnsv-table-column-micro",value:c.a.createElement(b,{dbId:l.dbId,id:l.id})},{key:"id",type:"cell",className:"ee-ticket-list-cell ee-ticket-list-col-id ee-rspnsv-table-column-nano ee-number-column",value:m},f,{key:"start",type:"cell",className:"ee-ticket-list-cell ee-ticket-list-col-start ee-rspnsv-table-column-default",value:Object(p.format)(new Date(l.startDate),k.ENTITY_LIST_DATE_TIME_FORMAT)},{key:"end",type:"cell",className:"ee-ticket-list-cell ee-ticket-list-col-end ee-rspnsv-table-column-default",value:Object(p.format)(new Date(l.endDate),k.ENTITY_LIST_DATE_TIME_FORMAT)},{key:"price",type:"cell",className:"ee-ticket-list-col-hdr ee-ticket-list-col-price ee-rspnsv-table-column-tiny ee-number-column",value:c.a.createElement(r.CurrencyDisplay,{value:l.price})},E,{key:"sold",type:"cell",className:"ee-ticket-list-cell ee-ticket-list-col-sold ee-rspnsv-table-column-tiny ee-number-column",value:l.sold},{key:"registrations",type:"cell",className:"ee-ticket-list-cell ee-ticket-list-col-registrations ee-rspnsv-table-column-smaller ee-centered-column",value:o?"-":c.a.createElement(T.a,{ticket:l})},{key:"actions",type:"cell",className:"ee-ticket-list-cell ee-actions-column ee-rspnsv-table-column-big",value:o?"-":c.a.createElement(_.a,{entity:l})}];return{cells:O()(y()(Object(i.filterCellByStartOrEndDate)(s)),Object(r.addZebraStripesOnMobile)(["row","stripe","name","actions"]))(C),className:"ee-editor-date-list-view-row ".concat(d),id:"ee-editor-date-list-view-row-".concat(l.id),key:"row-".concat(l.id),type:"row"}}),[t])}),w=n(27),S=n.n(w),N=n(155),P=n(30),D=n(250),x=n(6),I=n.n(x),B=n(886),A=n.n(B),M=n(2802),R=n(1721),F=n(2803),L=n(78),q=n(97),G=n.n(q),Q=n(156),Y=n.n(Q),z=n(506),J=n(223),K=function(){var e=Y()(G.a.mark((function e(t){return G.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return e.next=2,Object(J.yupToFinalFormErrors)(U,t);case 2:return e.abrupt("return",e.sent);case 3:case"end":return e.stop()}}),e)})));return function(t){return e.apply(this,arguments)}}(),U=z.object({name:z.string().min(3,(function(){return Object(l.__)("Name must be at least three characters")}))});function Z(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var a=Object.getOwnPropertySymbols(e);t&&(a=a.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,a)}return n}function V(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?Z(Object(n),!0).forEach((function(t){I()(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):Z(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}var W=Object(L.intervalsToOptions)(A()(["months","weeks","days","hours","minutes"],L.DATE_INTERVALS),!0),H=function(e){var t=e.onSubmit,n=Object(a.useCallback)((function(e,n){for(var a=arguments.length,c=new Array(a>2?a-2:0),l=2;l<a;l++)c[l-2]=arguments[l];return t.apply(void 0,[e,n].concat(c))}),[t]),c=Object(P.useMemoStringify)({className:"ee-form-item-pair"});return Object(a.useMemo)((function(){return V(V({},e),{},{onSubmit:n,validate:K,layout:"horizontal",debugFields:["values","errors"],sections:[{name:"basics",icon:M.a,title:Object(l.__)("Basics"),fields:[{name:"name",label:Object(l.__)("Name"),fieldType:"text",min:3},{name:"description",label:Object(l.__)("Description"),fieldType:"rich-text-editor"}]},{name:"dates",icon:R.a,title:Object(l.__)("Dates"),fields:[{name:"shiftDates",label:Object(l.__)("Shift dates"),fieldType:"group",formControlProps:{className:"shift-dates"},subFields:[{name:"value",fieldType:"number",min:1},{name:"unit",fieldType:"select",options:W},{name:"type",fieldType:"select",options:[{label:"",value:""},{label:Object(l.__)("earlier"),value:"earlier"},{label:Object(l.__)("later"),value:"later"}]}]}]},{name:"details",icon:F.a,title:Object(l.__)("Details"),fields:[{name:"quantity",label:Object(l.__)("Quantity For Sale"),fieldType:"number",formControlProps:c,parseAsInfinity:!0,max:1e6,min:-1},{name:"uses",label:Object(l.__)("Number of Uses"),fieldType:"number",parseAsInfinity:!0,formControlProps:c,min:0},{name:"min",label:Object(l.__)("Minimum Quantity"),fieldType:"number",formControlProps:c,max:1e6,min:0},{name:"max",label:Object(l.__)("Maximum Quantity"),fieldType:"number",parseAsInfinity:!0,formControlProps:c,max:1e6,min:-1},{name:"isRequired",label:Object(l.__)("Required Ticket"),fieldType:"switch",formControlProps:c}]}]})}),[c,e,n])},X=function(e){var t=Object(u.useBulkEdit)(),n=t.getSelected,c=t.unSelectAll,l=Object(i.useTickets)(),r=Object(i.useBulkEditTickets)().updateEntities;return Object(a.useCallback)((function(t){e(),c();var a=Object(i.formToBulkUpdateInput)(t,l,n());r(a)}),[l,n,e,c,r])},$=function(e){var t=e.onClose,n=e.isOpen,a=X(t),i=H({onSubmit:a});return c.a.createElement(r.BulkEditDetails,{formConfig:i,isOpen:n,onClose:t,title:Object(l.__)("Bulk edit ticket details"),warning:Object(l.__)("any changes will be applied to ALL of the selected tickets.")})},ee=function(e){var t=e.areTrashedTickets,n=e.onClose,c=Object(u.useBulkEdit)(),l=c.getSelected,r=c.unSelectAll,s=Object(i.useBulkDeleteTickets)();return Object(a.useCallback)((function(){n(),r(),s(l(),t)}),[t,s,l,n,r])},te=function(e){var t=e.areTrashedTickets,n=e.onClose,i=ee({areTrashedTickets:t,onClose:n}),s=Object(r.useConfirmationDialog)({message:t?Object(l.__)("Are you sure you want to permanently delete these tickets? This action can NOT be undone!"):Object(l.__)("Are you sure you want to trash these tickets?"),title:t?Object(l.__)("Delete tickets permanently"):Object(l.__)("Trash tickets"),onConfirm:i,onCancel:n}),o=s.confirmationDialog,u=s.onOpen;return Object(a.useEffect)((function(){return u()}),[]),c.a.createElement(c.a.Fragment,null,o)},ne=n(23),ae=function(e){var t=e.setEditMode,n=Object(a.useCallback)((function(){return t("together")}),[t]),i=Object(a.useCallback)((function(){return t("separate")}),[t]);return c.a.createElement(ne.Box,{display:"flex",alignItems:"center",justifyContent:"center",height:"100%"},c.a.createElement(ne.Box,null,c.a.createElement(r.Button,{onClick:n,buttonText:Object(l.__)("Edit all prices together")}),c.a.createElement("p",null,Object(l.__)("Edit all the selected ticket prices dynamically"))),c.a.createElement(r.Divider,{orientation:"vertical"}),c.a.createElement(ne.Box,null,c.a.createElement(r.Button,{onClick:i,buttonText:Object(l.__)("Edit prices individually")}),c.a.createElement("p",null,Object(l.__)("Edit prices for each ticket individually"))))},ce=function(e){var t=e.onSubmit,n=e.onReset,a=e.onCancel;return c.a.createElement(r.ButtonRow,{align:"right",topBordered:!0},n&&c.a.createElement(r.Button,{buttonText:Object(l.__)("Reset"),onClick:n,type:"reset"}),a&&c.a.createElement(r.Button,{buttonText:Object(l.__)("Cancel"),onClick:a}),c.a.createElement(r.Button,{buttonText:Object(l.__)("Submit"),buttonType:r.ButtonType.PRIMARY,onClick:t,type:"submit"}))},le=function(e){var t=Object(D.useDataState)(),n=t.prices,c=t.ticket,l=Object(u.useBulkEdit)().getSelected,r=Object(i.useTicketMutator)().updateEntity,s=Object(D.useMutatePrices)(),o=Object(i.useTicketPrices)(l()),b=Object(i.useBulkDeletePrices)(),m=o.filter(E.isNotDefault);return Object(a.useCallback)(Y()(G.a.mark((function t(){return G.a.wrap((function(t){for(;;)switch(t.prev=t.next){case 0:return e(),t.next=3,b(Object(E.getGuids)(m));case 3:return t.next=5,Promise.all(l().map(function(){var e=Y()(G.a.mark((function e(t){var a;return G.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return e.next=2,s(n);case 2:return a=e.sent,e.next=5,r({id:t,price:Object(v.parsedAmount)(c.price||0),reverseCalculate:Object(v.toBoolean)(c.reverseCalculate),isTaxable:Object(v.toBoolean)(c.isTaxable),prices:a});case 5:case"end":return e.stop()}}),e)})));return function(t){return e.apply(this,arguments)}}()));case 5:case"end":return t.stop()}}),t)}))),[b,l,s,m,e,n,c,r])},re=Object(D.withContext)((function(e){var t=e.onClose,n=Object(D.useAddDefaultPrices)();Object(a.useEffect)((function(){n()}),[]);var l=le(t);return c.a.createElement(c.a.Fragment,null,c.a.createElement(D.TicketPriceCalculator,null),c.a.createElement(ce,{onSubmit:l,onReset:n}))}),{ticketId:""}),ie=function(e){var t=e.setTPCState,n=Object(D.useDataState)(),r=n.ticket,i=n.getData;return Object(a.useEffect)((function(){t(i())}),[i]),c.a.createElement(c.a.Fragment,null,c.a.createElement("header",null,Object(l.sprintf)(Object(l.__)("Edit prices for Ticket: %s"),r.name)),c.a.createElement(D.TicketPriceCalculator,null))};function se(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var a=Object.getOwnPropertySymbols(e);t&&(a=a.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,a)}return n}function oe(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?se(Object(n),!0).forEach((function(t){I()(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):se(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}var ue=function(e,t){var n=Object(D.useOnSubmitPrices)();return Object(a.useCallback)(Y()(G.a.mark((function a(){return G.a.wrap((function(a){for(;;)switch(a.prev=a.next){case 0:return e(),a.next=3,Promise.all(Object.values(t()).map(function(){var e=Y()(G.a.mark((function e(t){return G.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return e.next=2,n(t);case 2:case"end":return e.stop()}}),e)})));return function(t){return e.apply(this,arguments)}}()));case 3:case"end":return a.stop()}}),a)}))),[t,e,n])},be=function(e){var t=e.onClose,n=Object(u.useBulkEdit)().getSelected,l=function(){var e=Object(a.useRef)({}),t=Object(a.useCallback)((function(t){var n;e.current=oe(oe({},e.current),{},I()({},null===t||void 0===t||null===(n=t.ticket)||void 0===n?void 0:n.id,t))}),[e]),n=Object(a.useCallback)((function(){return e.current}),[e]);return Object(a.useMemo)((function(){return{setTPCState:t,getDataStates:n}}),[t,n])}(),r=l.getDataStates,i=l.setTPCState,s=ue(t,r),o=n();return c.a.createElement(c.a.Fragment,null,o.map((function(e){var t=Object(D.withContext)(ie,{ticketId:e});return c.a.createElement(t,{key:e,setTPCState:i})})),c.a.createElement(ce,{onSubmit:s,onCancel:t}))},me=function(e){var t=e.onClose,n=e.isOpen,i=Object(a.useState)(),s=S()(i,2),o=s[0],u=s[1];return c.a.createElement(r.EntityEditModal,{isOpen:n,onClose:t,closeOnOverlayClick:!0,title:Object(l.__)("Bulk edit ticket prices")},!o&&c.a.createElement(ae,{setEditMode:u}),"together"===o&&c.a.createElement(re,{onClose:t}),"separate"===o&&c.a.createElement(be,{onClose:t}))},pe=Object(u.withFeature)("use_bulk_edit")((function(){var e=Object(a.useState)(""),t=S()(e,2),n=t[0],s=t[1],o=Object(N.useDisclosure)(),m=o.isOpen,p=o.onOpen,d=o.onClose,O=Object(i.useTicketsListFilterState)().status,f=Object(u.useBulkEdit)().getSelected,y=Object(i.useTickets)(),k=Object(a.useMemo)((function(){return Object(E.entitiesWithGuIdInArray)(y,f()).some((function(e){return Boolean(e.sold)}))}),[y,f]),j=O===E.TicketsStatus.trashedOnly,v=Object(P.useMemoStringify)([{value:"",label:Object(l.__)("bulk actions")},{value:"edit-details",label:Object(l.__)("edit ticket details")},{value:"delete",label:j?Object(l.__)("delete tickets"):Object(l.__)("trash tickets")},{value:"edit-prices",label:Object(l.__)("edit ticket prices"),disabled:k}]),_=Object(a.useCallback)((function(e){s(e),p()}),[p]);return c.a.createElement(c.a.Fragment,null,c.a.createElement(r.BulkActions,{Checkbox:b,options:v,onApply:k?null:_,defaultAction:""}),m&&c.a.createElement(c.a.Fragment,null,"edit-details"===n&&c.a.createElement($,{isOpen:!0,onClose:d}),"delete"===n&&c.a.createElement(te,{areTrashedTickets:j,onClose:d}),"edit-prices"===n&&c.a.createElement(me,{isOpen:!0,onClose:d})),k&&c.a.createElement(r.Banner,{description:D.SOLD_TICKET_ERROR_MESSAGE,status:"error",title:Object(l.__)("Error")}))}));t.default=Object(u.withBulkEdit)((function(){var e=Object(i.useTicketsListFilterState)(),t=Object(i.useFilteredTicketIds)(),n=Object(i.useReorderTickets)(t).sortResponder,a=C(),s=m();return c.a.createElement(c.a.Fragment,null,c.a.createElement(pe,null),c.a.createElement(r.EntityTable,{bodyRowGenerator:a,domain:i.domain,entityIds:t,filterState:e,headerRowGenerator:s,listId:i.ticketsList,onSort:n,tableCaption:Object(l.__)("Tickets"),tableId:"ticket-entities-table-view"}))}))}}]);
//# sourceMappingURL=tickets-table-view.54933ddf.chunk.js.map