$(".tab-button").click(function() {
    $(".tab-button").removeClass("active");
    $(this).addClass("active");

    $(".tab").removeClass("active");
    let tabId = $(this).attr("id");
    tabId = tabId.replace("tabBtn", "tab");
    $("#" + tabId).addClass("active");

    if (!window.location.href.includes("stage=")) {
        window.history.pushState("", "", window.location.href + "?stage=" + tabId.replace("tab", ""));
    }
    const newUrl = window.location.href.replace(/stage=\d/, "stage=" + tabId.replace("tab", ""));
    window.history.pushState("", "", newUrl);
});