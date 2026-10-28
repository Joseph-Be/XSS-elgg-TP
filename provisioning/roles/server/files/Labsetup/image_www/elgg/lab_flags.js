(function () {
  "use strict";

  function addFlag(id, value) {
    var el = document.getElementById(id);
    if (!el) {
      el = document.createElement("div");
      el.id = id;
      el.style.display = "none";
      document.body.appendChild(el);
    }
    el.textContent = value;
  }

  if (window.elgg && elgg.session && elgg.session.user) {
    var user = elgg.session.user.username || "";

    /* Task 1-3: victim Alice has the per-sandbox values in cookies.
       Students must use the XSS context to read them. */
    if (user === "alice") {
      document.cookie = "xss_task1={{ task1_flag }}; path=/";
      document.cookie = "xss_task2={{ task2_flag }}; path=/";
      document.cookie = "xss_task3={{ task3_flag }}; path=/";
    }

    /* Task 4: once Samy is visible in the user's friends page,
       expose the task-specific confirmation value. */
    if (user === "alice" && /friends/.test(location.pathname)) {
      if (document.body && document.body.innerText.indexOf("Samy") !== -1) {
        addFlag("xss-task4-flag", "{{ task4_flag }}");
      }
    }

    /* Task 5: a marker in Alice's About Me demonstrates that the
       profile-modification request reached the victim account. */
    if (user === "alice" && document.body &&
        document.body.innerText.indexOf("LAB5_PROFILE_UPDATED") !== -1) {
      addFlag("xss-task5-flag", "{{ task5_flag }}");
    }

    /* Task 6: the DOM worm is required to carry this marker. */
    if (document.body &&
        document.body.innerHTML.indexOf("LAB6_XSS_WORM") !== -1) {
      addFlag("xss-task6-flag", "{{ task6_flag }}");
    }
  }
})();
