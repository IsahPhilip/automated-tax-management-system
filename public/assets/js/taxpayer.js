/**
 * taxpayer.js — Taxpayer page client-side behaviour
 * Handles taxpayer form interactions and profile page features.
 */
(function () {
  "use strict";

  // Toggle taxpayer type-specific fields
  function initTaxpayerTypeToggle() {
    var typeSelect = document.querySelector("select[name='taxpayer_type']");
    if (typeSelect) {
      typeSelect.addEventListener("change", function () {
        var isIndividual = this.value === "INDIVIDUAL";
        var nameFields = document.querySelectorAll(".field-grid .wide");
        nameFields.forEach(function (field) {
          if (field.querySelector("input[name='business_name']")) {
            field.style.display = isIndividual ? "none" : "";
          }
        });
      });
    }
  }

  // Confirm before deactivating a taxpayer
  function initDeactivateConfirm() {
    var forms = document.querySelectorAll("form[action*='deactivate']");
    forms.forEach(function (form) {
      form.addEventListener("submit", function (e) {
        if (!confirm("Are you sure you want to deactivate this taxpayer?")) {
          e.preventDefault();
        }
      });
    });
  }

  // Initialize on DOM ready
  function init() {
    initTaxpayerTypeToggle();
    initDeactivateConfirm();
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
