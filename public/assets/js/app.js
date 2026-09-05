/**
 * app.js — Main client-side bundle (README §17 frontend convention)
 * Public/assets/ entry point loaded across all app screens.
 */
(function () {
  "use strict";

  // --- CSV Export Helper ---
  // Triggered by elements with class `js-export-csv` that carry
  // a `data-csv-url` attribute pointing at the export endpoint.
  function initCsvExport() {
    var els = document.querySelectorAll(".js-export-csv[data-csv-url]");
    els.forEach(function (el) {
      el.addEventListener("click", function (e) {
        e.preventDefault();
        var url = el.getAttribute("data-csv-url");
        window.location.href = url;
      });
    });
  }

  // --- Print Report Helper ---
  document.querySelectorAll(".js-print").forEach(function (el) {
    el.addEventListener("click", function () {
      window.print();
    });
  });

  // --- Auto-dismiss flash messages after 6 seconds ---
  function initFlashAutoDismiss() {
    var flashes = document.querySelectorAll(".flash");
    flashes.forEach(function (el) {
      setTimeout(function () {
        if (el.parentNode) {
          el.parentNode.removeChild(el);
        }
      }, 6000);
    });
  }

  // --- Tooltip hints for disabled buttons ---
  document.querySelectorAll("[data-tooltip]").forEach(function (el) {
    el.setAttribute("title", el.getAttribute("data-tooltip"));
  });

  // Initialize on DOM ready
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }

  function init() {
    initCsvExport();
    initFlashAutoDismiss();
  }
})();
