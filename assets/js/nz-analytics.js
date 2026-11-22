(function () {
  "use strict";

  var cfg = window.NZ_ANALYTICS || {};
  var ID = String(cfg.id || "");
  if (!/^G-[A-Z0-9]{4,16}$/.test(ID) || window.__nzAnalyticsBound) {
    return;
  }
  window.__nzAnalyticsBound = true;

  var KEY = "nz_analytics_consent";
  var DEDUPE_MS = 2000;
  var banner = document.getElementById("nz-consent");
  var loaded = false;
  var lastSent = {};

  function readConsent() {
    try {
      return window.localStorage.getItem(KEY);
    } catch (e) {
      return null;
    }
  }

  function saveConsent(value) {
    try {
      window.localStorage.setItem(KEY, value);
    } catch (e) {}
  }

  function clearGaCookies() {
    var host = window.location.hostname;
    var domains = ["", host, "." + host.replace(/^www\./, "")];
    document.cookie.split(";").forEach(function (part) {
      var name = part.split("=")[0].trim();
      if (name === "_ga" || name.indexOf("_ga_") === 0 || name === "_gid") {
        domains.forEach(function (d) {
          document.cookie = name + "=; expires=Thu, 01 Jan 1970 00:00:00 GMT; path=/" + (d ? "; domain=" + d : "");
        });
      }
    });
  }

  function loadGa() {
    if (loaded) return;
    loaded = true;
    window.dataLayer = window.dataLayer || [];
    window.gtag = function () {
      window.dataLayer.push(arguments);
    };
    window.gtag("consent", "default", {
      analytics_storage: "granted",
      ad_storage: "denied",
      ad_user_data: "denied",
      ad_personalization: "denied"
    });
    window.gtag("js", new Date());
    window.gtag("config", ID, {
      allow_google_signals: false,
      allow_ad_personalization_signals: false
    });
    var s = document.createElement("script");
    s.async = true;
    s.src = "https://www.googletagmanager.com/gtag/js?id=" + encodeURIComponent(ID);
    document.head.appendChild(s);
  }

  // Only coarse, non-personal labels are ever sent: no names, numbers, emails, or URLs with contact data
  function track(name, params, dedupeKey) {
    if (!loaded || typeof window.gtag !== "function") return;
    var key = name + "|" + (dedupeKey || "");
    var now = Date.now();
    if (lastSent[key] && now - lastSent[key] < DEDUPE_MS) return;
    lastSent[key] = now;
    window.gtag("event", name, params || {});
  }

  function linkLocation(el) {
    if (el.closest(".floating-contact-actions")) return "floating_button";
    if (el.closest("header, .nz-sidemenu")) return "header";
    if (el.closest("footer")) return "footer";
    return "page_content";
  }

  document.addEventListener("click", function (e) {
    var a = e.target && e.target.closest ? e.target.closest("a[href]") : null;
    if (!a) return;
    var href = a.getAttribute("href") || "";
    var name = null;
    if (/^https:\/\/(wa\.me|api\.whatsapp\.com)\//i.test(href)) {
      name = "whatsapp_click";
    } else if (/^tel:/i.test(href)) {
      name = "phone_click";
    } else if (/^mailto:/i.test(href)) {
      name = "email_click";
    }
    if (!name) return;
    var where = linkLocation(a);
    track(name, { link_location: where }, where);
  });

  document.addEventListener("nz:lead", function (e) {
    var d = (e && e.detail) || {};
    var formId = String(d.formId || "contact").replace(/[^a-z0-9_]/gi, "").slice(0, 40);
    var service = String(d.service || "").replace(/[^a-z0-9 &\-]/gi, "").slice(0, 60);
    track("generate_lead", { form_id: formId, service_type: service }, formId);
  });

  function showBanner() {
    if (banner) banner.hidden = false;
  }

  function hideBanner() {
    if (banner) banner.hidden = true;
  }

  function choose(value) {
    var previous = readConsent();
    saveConsent(value);
    hideBanner();
    if (value === "granted") {
      loadGa();
    } else {
      clearGaCookies();
      if (previous === "granted" && loaded) {
        window.location.reload();
      }
    }
  }

  document.addEventListener("click", function (e) {
    var btn = e.target && e.target.closest ? e.target.closest("[data-consent], [data-consent-open]") : null;
    if (!btn) return;
    if (btn.hasAttribute("data-consent-open")) {
      e.preventDefault();
      showBanner();
      return;
    }
    choose(btn.getAttribute("data-consent") === "granted" ? "granted" : "denied");
  });

  var consent = readConsent();
  if (consent === "granted") {
    loadGa();
  } else if (consent !== "denied") {
    showBanner();
  }
})();
