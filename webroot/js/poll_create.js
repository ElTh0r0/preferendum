$(document).ready(function() {

    $("#title").focus();
    $("#btnLess").css("cursor", "default");

    var datepickerOptions = {
        format: jsdateformat,
        autoHide: "true",
        weekStart: 1,
        language: jslocale,
        days: [jssunday,
            jsmonday,
            jstuesday,
            jswednesday,
            jsthursday,
            jsfriday,
            jssaturday]
    };

    //init first datepicker
    $(".dateInput").datepicker(datepickerOptions);

    //add new date field
    $("#btnMore").click(function() {
        if ($(".dateInput").length >= jsmaxoptions) return;
        $(".dateInput").last().after($(".dateInput").last().clone());
        $(".dateInput").last().val($(".dateInput:nth-last-child(2)").val());
        $(".dateInput").last().focus();
        $(".dateInput").last().select();
        $(".dateInput").last().datepicker(datepickerOptions);
        $("#btnLess").prop("disabled", false);
        $("#btnLess").css("cursor", "pointer");
    });

    //remove one date field
    $("#btnLess").click(function() {
        $(".dateInput").not(":first").last().datepicker("destroy");
        $(".dateInput").not(":first").last().detach();
        $(".dateInput").last().focus();
        $(".dateInput").last().select();
        $(".dateInput").last().datepicker(datepickerOptions);
        if ($(".dateInput").length == 1) {
            $("#btnLess").prop("disabled", true);
            $("#btnLess").css("cursor", "default");
        }
    });

});
