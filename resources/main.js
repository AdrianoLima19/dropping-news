import "./style.scss";

import * as bootstrap from "bootstrap";
import jQuery from "jquery";

window.jQuery = window.$ = jQuery;

var tooltipTriggerList = [].slice?.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));

tooltipTriggerList.map(function (tooltipElement) {
  new bootstrap.Tooltip(tooltipElement);
});

const popoverTriggerList = [].slice?.call(document.querySelectorAll('[data-bs-toggle="popover"]'));

popoverTriggerList.map(function (popoverTriggerElement) {
  new bootstrap.Popover(popoverTriggerElement);
});

// const iconTriggerList = [].slice?.call(document.querySelectorAll("[data-ico-provider]"));

// iconTriggerList.map(function (iconTriggerElement) {
//   console.log(iconTriggerElement);
// });
