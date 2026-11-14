/* CyberRange lab helper for the CSP experiment. Holds no flag value. */
window.addEventListener("load", function () {
  fetch("lab_csp_check.php", { cache: "no-store" })
    .then(function (r) { return r.json(); })
    .then(function (d) {
      if (!d || !d.ok) { return; }
      var p = document.createElement("p");
      p.id = "csp-lab-flag";
      p.textContent = "Task 7 verification: " + d.flag;
      document.body.appendChild(p);
    })
    .catch(function () {});
});
