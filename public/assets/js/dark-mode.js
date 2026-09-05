(function () {
  "use strict";

  var STORAGE_KEY = "biu-theme";
  var root = document.documentElement;

  function current() {
    return root.getAttribute("data-theme") === "dark" ? "dark" : "light";
  }

  function apply(theme) {
    root.setAttribute("data-theme", theme);
    updateToggles(theme);
    document.dispatchEvent(
      new CustomEvent("themechange", { detail: { theme: theme } })
    );
  }

  function updateToggles(theme) {
    var isDark = theme === "dark";
    var label = isDark ? "Switch to light mode" : "Switch to dark mode";

    document.querySelectorAll(".theme-toggle").forEach(function (btn) {
      btn.setAttribute("aria-label", label);
      btn.setAttribute("title", label);
      btn.setAttribute("aria-pressed", String(isDark));
    });
  }

  function toggle() {
    var next = current() === "dark" ? "light" : "dark";

    try {
      localStorage.setItem(STORAGE_KEY, next);
    } catch (e) {
      /* private browsing / storage disabled — theme still applies for this page */
    }

    apply(next);
  }

  document.addEventListener("click", function (event) {
    var target = event.target;

    if (!target || typeof target.closest !== "function") {
      return;
    }

    if (!target.closest(".theme-toggle")) {
      return;
    }

    event.preventDefault();
    toggle();
  });

  if (window.matchMedia) {
    var query = window.matchMedia("(prefers-color-scheme: dark)");
    var onChange = function (event) {
      var stored = null;

      try {
        stored = localStorage.getItem(STORAGE_KEY);
      } catch (e) {
        /* ignore */
      }

      if (!stored) {
        apply(event.matches ? "dark" : "light");
      }
    };

    if (query.addEventListener) {
      query.addEventListener("change", onChange);
    } else if (query.addListener) {
      query.addListener(onChange);
    }
  }

  function styleCharts(theme) {
    if (typeof window.Chart === "undefined" || !window.Chart.defaults) {
      return;
    }

    var isDark = theme === "dark";
    window.Chart.defaults.color = isDark ? "#94a3b8" : "#666666";
    window.Chart.defaults.borderColor = isDark
      ? "rgba(255, 255, 255, 0.08)"
      : "rgba(0, 0, 0, 0.1)";
  }

  document.addEventListener("themechange", function (event) {
    styleCharts(event.detail.theme);
  });

  updateToggles(current());
  styleCharts(current());
})();
