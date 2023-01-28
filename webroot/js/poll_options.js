//enable/disable email input depending on checkboxes
var chkEmailEntry = document.getElementById("emailentryInput");
var chkEmailComment = document.getElementById("emailcommentInput");
var inpEmail = document.getElementById("emailInput");
var chkComment = document.getElementById("commentInput");
function toggleEmailInput() {
    if (chkComment && chkEmailComment) {
        if (chkComment.checked) {
            chkEmailComment.disabled = false;
        } else {
            chkEmailComment.disabled = true;
            chkEmailComment.checked = false;
        }
    }

    var isCheckedMailEntry = false;
    if (chkEmailEntry) {
        isCheckedMailEntry = chkEmailEntry.checked;
    }
    var isCheckedMailComment = false;
    if (chkEmailComment) {
        isCheckedMailComment = chkEmailComment.checked;
    }

    inpEmail.disabled = !isCheckedMailEntry && !isCheckedMailComment;

    if (inpEmail.disabled) {
        inpEmail.value = "";
        inpEmail.required = false;
    } else {
        inpEmail.required = "required";
    }
}

//enable/disable hideresult option depending on checkboxes
function toggleHideResultInput() {
    document.getElementById("hideresultInput").disabled =
        !document.getElementById("adminInput").checked;

    if (document.getElementById("hideresultInput").disabled) {
        document.getElementById("hideresultInput").checked = false;
    }
}
