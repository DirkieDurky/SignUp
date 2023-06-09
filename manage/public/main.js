$("#api-key, #copy-api-key").on("click", function () {
    var target = document.getElementById("api-key");

    var currentFocus = document.activeElement;

    target.focus();
    target.setSelectionRange(0, target.value.length);

    // Copy the selection
    navigator.clipboard.writeText($("#api-key").val());

    // Restore original focus
    if (currentFocus && typeof currentFocus.focus === "function") {
        currentFocus.focus();
    }

    $("#copy-api-key").text("Copied text to clipboard");
    $("#copy-api-key").addClass("copy-btn-active");
});

$("#api-key, #copy-api-key").on("blur", function () {
    $("#copy-api-key").text("Click to copy");
    $("#copy-api-key").removeClass("copy-btn-active");
});