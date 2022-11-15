import "./style.scss";

import * as bootstrap from "bootstrap";
import jQuery from "jquery";

window.jQuery = window.$ = jQuery;

var tooltipTriggerList = [].slice?.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));

tooltipTriggerList.map(function (tooltipElement) {
  new bootstrap.Tooltip(tooltipElement);
});
