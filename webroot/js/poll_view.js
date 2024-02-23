$(document).ready(function() {

    // show poll url
    var pollUrl = window.location.protocol + "/" +
                  window.location.hostname +
                  jswebroot + "polls/" + jspollid;
    $("#public-url-field").val(pollUrl);
    $("#admin-url-field").val(pollUrl + "/" + jsadminid);

    // url clipboard copy feature
    var clipboard = new ClipboardJS(".copy-trigger");
    clipboard.on("success", function(e) {
        $(e.trigger).addClass("copy-success").addClass("copy-success").delay(600).queue(function(){
            $(this).removeClass("copy-success").dequeue();
        });
    });
    clipboard.on("error", function(e) {
        alert("Error copying URL. Please copy it manually!");
        $(e.trigger).addClass("copy-fail").delay(2000).queue(function(){
            $(this).removeClass("copy-fail").dequeue();
        });
    });

    // iterate options on click (ugly, but works for now)
    $(".new-entry-box").click(function(){
        if ($(this).hasClass("new-entry-choice-maybe")) {
            $(this).removeClass("new-entry-choice-maybe");
            $(this).addClass("new-entry-choice-no");
            $(this).children(".entry-value").attr("value", "0");
            $(this).attr('title', jsNo);
        } else if ($(this).hasClass("new-entry-choice-yes")) {
            $(this).removeClass("new-entry-choice-yes");
            $(this).addClass("new-entry-choice-maybe");
            $(this).children(".entry-value").attr("value", "2");
            $(this).attr('title', jsMaybe);
        } else if ($(this).hasClass("new-entry-choice-no")) {
            $(this).removeClass("new-entry-choice-no");
            $(this).addClass("new-entry-choice-yes");
            $(this).children(".entry-value").attr("value", "1");
            $(this).attr('title', jsYes);
        }
    });
    
    // mini-view toggler
    $("#ctrl-mini-view").click(function(){
        if ($("#ctrl-mini-view").attr("data-miniview") == "off") {
            $("table.schedule").addClass("mini");
            $("#ctrl-mini-view").attr("data-miniview", "on");
            $("#ctrl-mini-view").text(jsNormal);
        } else {
            $("table.schedule").removeClass("mini");
            $("#ctrl-mini-view").attr("data-miniview", "off");
            $("#ctrl-mini-view").text(jsMini);
        }
    });
    
});
