document.addEventListener("DOMContentLoaded", function () {

    // show poll url
    var pollUrl = window.location.protocol + "//" +
        window.location.hostname +
        jswebroot + "polls/" + jspollid;
    var publicUrlField = document.getElementById("public-url-field");
    var adminUrlField = document.getElementById("admin-url-field");
    if (publicUrlField) {
        publicUrlField.value = pollUrl;
    }
    if (adminUrlField) {
        adminUrlField.value = pollUrl + "/" + jsadminid;
    }

    // url clipboard copy feature
    var clipboard = new ClipboardJS(".copy-trigger");
    clipboard.on("success", function (e) {
        var trigger = e.trigger;
        trigger.classList.add("copy-success");
        setTimeout(function () {
            trigger.classList.remove("copy-success");
        }, 600);
    });
    clipboard.on("error", function (e) {
        alert("Error copying URL. Please copy it manually!");
        var trigger = e.trigger;
        trigger.classList.add("copy-fail");
        setTimeout(function () {
            trigger.classList.remove("copy-fail");
        }, 2000);
    });

    // iterate options on click (ugly, but works for now)
    var newEntryBoxes = document.querySelectorAll(".new-entry-box");
    newEntryBoxes.forEach(function (box) {
        box.addEventListener("click", function () {
            var entryValue = box.querySelector(".entry-value");

            if (box.classList.contains("new-entry-choice-maybe")) {
                box.classList.remove("new-entry-choice-maybe");
                box.classList.add("new-entry-choice-no");
                entryValue.value = "0";
                box.title = jsNo;
            } else if (box.classList.contains("new-entry-choice-yes")) {
                box.classList.remove("new-entry-choice-yes");
                box.classList.add("new-entry-choice-maybe");
                entryValue.value = "2";
                box.title = jsMaybe;
            } else if (box.classList.contains("new-entry-choice-no")) {
                box.classList.remove("new-entry-choice-no");
                box.classList.add("new-entry-choice-yes");
                entryValue.value = "1";
                box.title = jsYes;
            }
        });
    });

    // mini-view toggler
    var ctrlMiniView = document.getElementById("ctrl-mini-view");
    var scheduleTable = document.querySelector("table.schedule");
    if (ctrlMiniView) {
        ctrlMiniView.addEventListener("click", function () {
            var isMiniOff = ctrlMiniView.getAttribute("data-miniview") === "off";

            if (isMiniOff) {
                scheduleTable.classList.add("mini");
                ctrlMiniView.setAttribute("data-miniview", "on");
                ctrlMiniView.textContent = jsNormal;
            } else {
                scheduleTable.classList.remove("mini");
                ctrlMiniView.setAttribute("data-miniview", "off");
                ctrlMiniView.textContent = jsMini;
            }
        });
    }
});
