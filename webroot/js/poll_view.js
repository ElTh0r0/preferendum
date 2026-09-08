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

// url clipboard copy feature
async function copyToClipboard(text) {
    // Modern Clipboard API (requires HTTPS or localhost)
    if (navigator.clipboard && window.isSecureContext) {
        try {
            await navigator.clipboard.writeText(text);
            return true;
        } catch (e) {
            //console.warn("navigator.clipboard.writeText() failed!");
        }
    }

    // Fallback using execCommand (deprecated)
    try {
        const textarea = document.createElement("textarea");
        textarea.value = text;
        textarea.style.position = "fixed";
        textarea.style.left = "-9999px";
        textarea.style.top = "0";
        document.body.appendChild(textarea);
        textarea.focus();
        textarea.select();

        const copySuccess = document.execCommand("copy");
        document.body.removeChild(textarea);
        if (copySuccess) {
            return true;
        }
    } catch (e) {
        //console.warn("document.execCommand('copy') failed!");
    }

    return false;
}

// Find all buttons with data-clipboard-target
const buttons = document.querySelectorAll("[data-clipboard-target]");
buttons.forEach((button) => {
    button.addEventListener("click", async () => {
        const targetSelector = button.getAttribute("data-clipboard-target");
        const targetElement = document.querySelector(targetSelector);
        if (!targetElement) {
            console.warn("Target element not found: " + targetSelector);
            button.classList.add("copy-fail");
            setTimeout(() => {
                button.classList.remove("copy-fail");
            }, 2000);
            return;
        }

        const text = "value" in targetElement ? targetElement.value : targetElement.textContent;
        const ok = await copyToClipboard(text);
        if (ok) {
            //console.log("Copy to clipboard successful!");
            button.classList.add("copy-success");
            setTimeout(() => {
                button.classList.remove("copy-success");
            }, 600);
        } else {
            //console.warn("Copy to clipboard failed!");
            button.classList.add("copy-fail");
            setTimeout(() => {
                button.classList.remove("copy-fail");
            }, 2000);
        }
    });
});