$(document).ready(function () {
    $("#btnLess").css("cursor", "default");

    //add new choice field
    $("#btnMore").click(function () {
        if ($(".choiceInput").length >= jsmaxoptions) return;
        $(".choiceInput").last().after($(".choiceInput").last().clone());
        $(".choiceInput").last().val($(".choiceInput:nth-last-child(2)").val());
        $(".choiceInput").last().focus();
        $(".choiceInput").last().select();
        $(".maxEntryInput").last().after($(".maxEntryInput").last().clone());
        $(".maxEntryInput").last().val($(".maxEntryInput:nth-last-child(2)").val());
        $("#btnLess").prop("disabled", false);
        $("#btnLess").css("cursor", "pointer");
    });

    //remove one choice field
    $("#btnLess").click(function () {
        $(".choiceInput").not(":first").last().detach();
        $(".choiceInput").last().focus();
        $(".choiceInput").last().select();
        $(".maxEntryInput").not(":first").last().detach();
        if ($(".choiceInput").length == 1) {
            $("#btnLess").prop("disabled", true);
            $("#btnLess").css("cursor", "default");
        }
    });
});
