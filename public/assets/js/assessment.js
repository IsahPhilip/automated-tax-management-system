/**
 * assessment.js — Assessment page client-side behaviour
 * Handles assessment breakdown interactions and calculations display.
 */
(function () {
  "use strict";

  // Toggle breakdown table visibility
  function initBreakdownToggle() {
    var toggle = document.querySelector(".js-toggle-breakdown");
    var table = document.querySelector(".js-breakdown-table");
    if (toggle && table) {
      toggle.addEventListener("click", function () {
        var isHidden = table.style.display === "none";
        table.style.display = isHidden ? "" : "none";
        toggle.textContent = isHidden ? "Hide breakdown" : "Show breakdown";
      });
    }
  }

  // Highlight current row in breakdown tables
  function initRowHighlight() {
    var rows = document.querySelectorAll(".js-breakdown-table tbody tr");
    rows.forEach(function (row) {
      row.addEventListener("mouseenter", function () {
        this.style.backgroundColor = "var(--soft)";
      });
      row.addEventListener("mouseleave", function () {
        this.style.backgroundColor = "";
      });
    });
  }

  // Initialize on DOM ready
  function init() {
    initBreakdownToggle();
    initRowHighlight();
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
