/**
 * validation.js — Form validation helpers
 * Provides client-side validation for forms across the application.
 */
(function () {
  "use strict";

  // Validate email format
  function isValidEmail(email) {
    var re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return re.test(email);
  }

  // Validate required fields
  function validateRequired(form) {
    var valid = true;
    var required = form.querySelectorAll("[required]");
    required.forEach(function (field) {
      if (!field.value.trim()) {
        valid = false;
        field.style.borderColor = "#b32638";
      } else {
        field.style.borderColor = "";
      }
    });
    return valid;
  }

  // Validate email fields
  function validateEmails(form) {
    var valid = true;
    var emails = form.querySelectorAll("input[type='email']");
    emails.forEach(function (field) {
      if (field.value && !isValidEmail(field.value)) {
        valid = false;
        field.style.borderColor = "#b32638";
      } else {
        field.style.borderColor = "";
      }
    });
    return valid;
  }

  // Validate password confirmation
  function validatePasswordConfirm(form) {
    var password = form.querySelector("input[name='password']");
    var confirm = form.querySelector("input[name='password_confirmation']");
    if (password && confirm && confirm.value) {
      if (password.value !== confirm.value) {
        confirm.style.borderColor = "#b32638";
        return false;
      }
    }
    return true;
  }

  // Attach validation to all forms with data-validate attribute
  function init() {
    var forms = document.querySelectorAll("form[data-validate]");
    forms.forEach(function (form) {
      form.addEventListener("submit", function (e) {
        var valid = true;
        valid = validateRequired(form) && valid;
        valid = validateEmails(form) && valid;
        valid = validatePasswordConfirm(form) && valid;
        if (!valid) {
          e.preventDefault();
        }
      });
    });

    // Clear error styling on input
    var inputs = document.querySelectorAll("form[data-validate] input");
    inputs.forEach(function (input) {
      input.addEventListener("input", function () {
        input.style.borderColor = "";
      });
    });
  }

  // Initialize on DOM ready
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", init);
  } else {
    init();
  }
})();
