formSubmissionSpinner();

function formSubmissionSpinner() {
    let forms = document.querySelectorAll("form");

    if (forms !== []) {
        for (let form of forms) {
            form.addEventListener('submit', function (e) {
                let submitBtn = form.querySelector("button[type='submit']");
                let currentWidth = submitBtn.clientWidth;
                submitBtn.classList.add("disabled");
                submitBtn.style.minWidth = currentWidth.toString() + 'px'
                submitBtn.innerHTML = '<div class="spinner-border spinner-border-sm text-light" role="status"><span class="visually-hidden">Loading...</span></div>';
            })
        }
    }
}



$(".js-multiple").select2({
    theme: "bootstrap-5",
    selectionCssClass: "select2--small",
    dropdownCssClass: "select2--small",
});