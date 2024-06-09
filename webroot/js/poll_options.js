//enable/disable email input depending on checkboxes
var chkEmailEntry = document.getElementById("emailentryinput");
var chkEmailComment = document.getElementById("emailcommentinput");
var chkEmailPoll = document.getElementById("emailpollinput");
var inpEmail = document.getElementById("emailinput");
var chkComment = document.getElementById("commentinput");
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
    var isCheckedMailPoll = false;
    if (chkEmailPoll) {
        isCheckedMailPoll = chkEmailPoll.checked;
    }

    inpEmail.disabled = !isCheckedMailEntry && !isCheckedMailComment && !isCheckedMailPoll;

    if (inpEmail.disabled) {
        inpEmail.value = "";
        inpEmail.required = false;
    } else {
        inpEmail.required = "required";
    }
}

//enable/disable options depending on AdminLink checkbox
var chkAdminLink = document.getElementById("admininput");
var chkHideVotes = document.getElementById("hidevotesinput");
var chkEditEntry = document.getElementById("editentryinput");
var chkUserInfo = document.getElementById("userinfoinput");
var emailWarning = document.getElementById("emailwarn");
var chkAnonymous = document.getElementById("anonymousinput");
function toggleAdminLinkInput() {
    if (chkHideVotes) {
        chkHideVotes.disabled = !chkAdminLink.checked;

        if (chkHideVotes.disabled) {
            chkHideVotes.checked = false;
        }
    }

    if (chkEditEntry) {
        chkEditEntry.disabled = !chkAdminLink.checked;

        if (chkEditEntry.disabled) {
            chkEditEntry.checked = false;
        }
    }

    if (chkUserInfo) {
        chkUserInfo.disabled = !chkAdminLink.checked;

        if (chkUserInfo.disabled) {
            chkUserInfo.checked = false;
        }
        if (chkAnonymous) {
            if (!chkUserInfo.checked) {
                chkAnonymous.disabled = false;
            }
        }
    }

    if (emailWarning) {
        if (!chkAdminLink.checked) {
            emailWarning.style.display = "block"
        } else {
            emailWarning.style.display = "none"
        }
    }
}

//enable/disable anonymous poll if collect user info is checked
function toggleUserinfoInput() {
    if (chkAnonymous) {
        chkAnonymous.disabled = chkUserInfo.checked;

        if (chkAnonymous.disabled) {
            chkAnonymous.checked = false;
        }
    }
}

//enable/disable max entry input
var inpLimit = document.getElementById("limitinput");
function toggleMaxEntryInput() {
    if (inpLimit) {
        var inpMaxEntryBoxes = document.getElementsByClassName("maxEntryInput");
        for (var i = 0; i < inpMaxEntryBoxes.length; i++) { 
            inpMaxEntryBoxes[i].disabled = !inpLimit.checked;
            if (!inpLimit.checked) {
                inpMaxEntryBoxes[i].value = "0";
            }
        }
    }
}

//enable/disable password input
var chkPwProtect = document.getElementById("pwprotectinput");
var inpPassword = document.getElementById("passwordinput");
function togglePasswordInput() {
    if (chkPwProtect) {
        inpPassword.disabled = !chkPwProtect.checked;

        if (inpPassword.disabled) {
            inpPassword.value = "";
            inpPassword.required = false;
        } else {
            inpPassword.required = "required";
        }
    }
}

//enable/disable expiry input
var chkExpiry = document.getElementById("hasexpinput");
var inpExpiry = document.getElementById("expinput");
function toggleExpiryInput() {
    if (chkExpiry) {
        inpExpiry.disabled = !chkExpiry.checked;
    }
}
