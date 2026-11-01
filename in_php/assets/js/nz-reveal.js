(function () {
  "use strict";

  var REDUCE = window.matchMedia("(prefers-reduced-motion: reduce)").matches;
  if (REDUCE || !("IntersectionObserver" in window)) {
    return;
  }

  var SKIP = "header, footer, form, nav, .nz-preloader, .paginacontainer, .optech-sidemenu-wraper, .nz-sidemenu, .offcanves-menu, .floating-contact-actions, .menu-block, .menu-block-wrapper, .mobile-menu-head";
  var UNIT = [
    "article",
    ".nz-tool-card",
    ".nz-industry-item",
    ".nz-offer-card",
    ".nz-team-card",
    ".nz-job-card",
    ".nz-about-step",
    ".nz-about-card",
    ".nz-process-card",
    ".nz-svc-card",
    ".nz-about-panel",
    ".nz-about-cta-card",
    ".nz-page-cta",
    ".nz-consult-card",
    ".nz-trust-card",
    ".nz-price-card",
    ".nz-work-card",
    ".nz-svc-fit-list article",
    ".nz-about-values article",
    ".nz-about-stats-grid > div",
    ".faq-item",
    ".optech-iconbox-wrap",
    ".optech-iconbox-wrap2",
    ".optech-pricing-wrap",
    ".optech-portfolio-wrap",
    ".optech-blog-wrap"
  ].join(",");

  var TEXT = "h1, h2, h3, .nz-kicker, .nz-title, .nz-lead, .nz-about-lead";
  var seen = new WeakSet();

  function skipped(el) {
    return !el || el.nodeType !== 1 || !!el.closest(SKIP) || !!el.closest("[data-aos]") || el.hasAttribute("data-aos");
  }

  function inView(el) {
    var rect = el.getBoundingClientRect();
    var view = window.innerHeight || document.documentElement.clientHeight;
    return rect.top < view * 0.92 && rect.bottom > 0;
  }

  function collect(root) {
    var scope = root && root.querySelectorAll ? root : document;
    var nodes = [];

    function push(el) {
      if (skipped(el) || seen.has(el)) return;
      if (el.closest(".nz-reveal, .nz-reveal-group")) return;
      nodes.push(el);
    }

    if (scope !== document && scope.matches && scope.matches(UNIT)) {
      push(scope);
    }

    scope.querySelectorAll(UNIT).forEach(push);
    scope.querySelectorAll(TEXT).forEach(function (el) {
      if (el.closest(UNIT)) return;
      push(el);
    });
    scope.querySelectorAll("img").forEach(function (el) {
      if (el.closest(UNIT + ", " + TEXT)) return;
      if (el.offsetWidth < 64 || el.offsetHeight < 64) return;
      push(el);
    });

    return nodes.filter(function (el, index) {
      return !nodes.some(function (other, otherIndex) {
        return index !== otherIndex && other.contains(el);
      });
    });
  }

  function arm(nodes) {
    var groups = new Map();

    nodes.forEach(function (el) {
      if (seen.has(el) || inView(el)) {
        seen.add(el);
        return;
      }
      seen.add(el);
      var parent = el.parentElement || document.body;
      if (!groups.has(parent)) groups.set(parent, []);
      groups.get(parent).push(el);
    });

    groups.forEach(function (list) {
      list.forEach(function (el, index) {
        if (list.length > 1) {
          el.style.setProperty("--nz-reveal-delay", Math.min(index, 6) * 90 + "ms");
        }
        el.classList.add("nz-reveal");
        observer.observe(el);
      });
    });
  }

  var observer = new IntersectionObserver(function (entries) {
    entries.forEach(function (entry) {
      if (!entry.isIntersecting) return;
      entry.target.classList.add("is-shown");
      observer.unobserve(entry.target);
    });
  }, {
    root: null,
    rootMargin: "0px 0px -8% 0px",
    threshold: 0.14
  });

  function scan(root) {
    arm(collect(root || document));
  }

  function start() {
    scan(document);
    var pending = false;
    var mo = new MutationObserver(function (mutations) {
      if (pending) return;
      var hasNodes = mutations.some(function (mutation) {
        return mutation.addedNodes && mutation.addedNodes.length;
      });
      if (!hasNodes) return;
      pending = true;
      window.requestAnimationFrame(function () {
        pending = false;
        mutations.forEach(function (mutation) {
          mutation.addedNodes.forEach(function (node) {
            if (node.nodeType === 1) scan(node);
          });
        });
      });
    });
    mo.observe(document.body, { childList: true, subtree: true });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", start);
  } else {
    start();
  }
})();
