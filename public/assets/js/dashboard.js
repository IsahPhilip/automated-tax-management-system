/**
 * dashboard.js — Dashboard-specific client-side behaviour
 * Handles metric card animations and dashboard interactivity.
 */
(function () {
  "use strict";

  // Animate metric cards on load
  function animateMetrics() {
    var metrics = document.querySelectorAll(".metric");
    metrics.forEach(function (el, i) {
      el.style.opacity = "0";
      el.style.transform = "translateY(10px)";
      setTimeout(function () {
        el.style.transition = "opacity 0.3s ease, transform 0.3s ease";
        el.style.opacity = "1";
        el.style.transform = "translateY(0)";
      }, i * 80);
    });
  }

  // Initialize on DOM ready
  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", animateMetrics);
  } else {
    animateMetrics();
  }
})();
