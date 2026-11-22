(function () {
  "use strict";

  if (!document.body.classList.contains("nazora-home")) {
    return;
  }

  var reduce = window.matchMedia("(prefers-reduced-motion: reduce)").matches;

  function ready(fn) {
    if (document.readyState === "loading") {
      document.addEventListener("DOMContentLoaded", fn);
    } else {
      fn();
    }
  }

  function initDashNav() {
    var nav = document.querySelector("[data-nz-dash-nav]");
    if (!nav) return;
    var items = Array.prototype.slice.call(nav.querySelectorAll("li"));
    if (!items.length) return;

    var index = Math.max(0, items.findIndex(function (item) {
      return item.classList.contains("active");
    }));
    var timer = null;

    function setActive(next) {
      index = (next + items.length) % items.length;
      items.forEach(function (item, i) {
        item.classList.toggle("active", i === index);
      });
    }

    function start() {
      if (reduce || timer) return;
      timer = window.setInterval(function () {
        setActive(index + 1);
      }, 2800);
    }

    function stop() {
      if (!timer) return;
      window.clearInterval(timer);
      timer = null;
    }

    items.forEach(function (item, i) {
      item.addEventListener("click", function () {
        setActive(i);
        stop();
        start();
      });
    });

    var frame = nav.closest(".nz-dash");
    if (frame) {
      frame.addEventListener("mouseenter", stop);
      frame.addEventListener("mouseleave", start);
      frame.addEventListener("focusin", stop);
      frame.addEventListener("focusout", function (event) {
        if (!frame.contains(event.relatedTarget)) start();
      });
    }

    start();
  }

  function initAddonCards() {
    var cards = document.querySelectorAll(".nz-addon-card");
    if (!cards.length) return;

    cards.forEach(function (card) {
      card.addEventListener("mouseenter", function () {
        cards.forEach(function (other) {
          other.classList.toggle("is-active", other === card);
        });
      });
    });
  }

  function initSmoothAnchors() {
    document.querySelectorAll('a[href^="#"]').forEach(function (link) {
      var id = link.getAttribute("href");
      if (!id || id === "#") return;
      var target = document.querySelector(id);
      if (!target) return;
      link.addEventListener("click", function (event) {
        event.preventDefault();
        target.scrollIntoView({ behavior: reduce ? "auto" : "smooth", block: "start" });
      });
    });
  }

  function initToolTilt() {
    if (reduce || window.matchMedia("(pointer: coarse)").matches) return;
    var cards = document.querySelectorAll(".nz-tool-card, .nz-scale-card");
    cards.forEach(function (card) {
      card.addEventListener("pointermove", function (event) {
        var rect = card.getBoundingClientRect();
        var x = (event.clientX - rect.left) / rect.width - 0.5;
        var y = (event.clientY - rect.top) / rect.height - 0.5;
        card.style.transform = "translateY(-8px) rotateX(" + (y * -4) + "deg) rotateY(" + (x * 5) + "deg)";
      });
      card.addEventListener("pointerleave", function () {
        card.style.transform = "";
      });
    });
  }

  ready(function () {
    initDashNav();
    initAddonCards();
    initSmoothAnchors();
    initToolTilt();
  });
})();
