$("#username-input-field").on("input", () => {
    $("#username-error").css("display", "none");
});

let inputFields = [
    {
        input: $("#username-input-field"),
        error: $("#username-error"),
    },
    {
        input: $("#firstname-input-field"),
        error: $("#firstname-error"),
    },
    {
        input: $("#lastname-input-field"),
        error: $("#lastname-error"),
    },
    {
        input: $("#password-input-field"),
        error: $("#password-error"),
    },
];

for (let inputField of inputFields) {
    inputField.input.on("input", () => {
        if (inputField.error.val() == "" &&
            inputField.input.val() == "") {
            inputField.error.css("display", "block");
            inputField.error.text("This field shouldn't be empty!");
        }
    });
}