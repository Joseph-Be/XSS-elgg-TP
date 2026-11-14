/* CyberRange lab helper. Contains no flag values: it asks the server,
   which answers only for what the database shows was achieved. */
(function () {
  "use strict";

  var LABELS = { task4: "Task 4", task5: "Task 5", task6: "Task 6" };

  function show(flags) {
    var keys = Object.keys(flags || {});
    if (!keys.length || !document.body) { return; }
    var box = document.getElementById("xss-lab-flags");
    if (!box) {
      box = document.createElement("div");
      box.id = "xss-lab-flags";
      box.style.cssText = "position:fixed;right:12px;bottom:12px;z-index:99999;" +
        "background:#14202f;color:#fff;padding:10px 14px;border-radius:6px;" +
        "font:14px/1.5 monospace;box-shadow:0 2px 8px rgba(0,0,0,.4)";
      document.body.appendChild(box);
    }
    box.textContent = "";
    keys.sort().forEach(function (k) {
      var line = document.createElement("div");
      line.id = "xss-" + k + "-flag";
      line.textContent = (LABELS[k] || k) + " verification: " + flags[k];
      box.appendChild(line);
    });
  }

  function check() {
    fetch("/lab_check.php", { credentials: "same-origin", cache: "no-store" })
      .then(function (r) { return r.json(); })
      .then(function (d) { show(d && d.flags); })
      .catch(function () {});
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", check);
  } else {
    check();
  }
})();
