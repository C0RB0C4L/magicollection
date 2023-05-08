formSubmissionSpinner();
formBulletsOnRequired();
enableHighlightIfFieldsAreDifferent();

enableMultiSelectInput();
enableDynamicTable();
flashMessageManagement();

/**
 * @description Replaces the text by a spinner gif on the submit button when a form is sent.\
 * Works with bootstrap v5.2.x.
 * 
 * @return void
 */
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


/**
 * @description Removes the flash message(s) 5 seconds after page loading.\
 * Add the .flash class to the flash message container.
 * 
 * @return void
 */
function flashMessageManagement() {
    let flashMessages = document.getElementsByClassName("flash");
    if (flashMessages !== []) {
        for (let message of flashMessages) {
            setTimeout(function () {
                message.remove();
            }, 5000)
        }
    }
}


/**
 * @description Changes the native multiple select by a JS-powered version with built-in search input.\
 * Add the class .js-multiple to the \<select> element.
 * 
 * @return void
 */
function enableMultiSelectInput() {
    $(".js-multiple").select2({
        theme: "bootstrap-5",
        selectionCssClass: "select2--small",
        dropdownCssClass: "select2--small",
    });
}


/**
 * @description Highlights a form input duo if they are supposed to have the same content.\
 * Like "email" and "repeat your email".\
 * Works with the way Symfony sets the IDs on repeated fields.
 * 
 * @return void
 */
function enableHighlightIfFieldsAreDifferent() {

    let forms = document.querySelectorAll("form");

    if (forms !== []) {

        for (let form of forms) {
            let firstInputs = form.querySelectorAll("input[id*='first']");
            let secondInputs = form.querySelectorAll("input[id*='second']");

            firstInputs.forEach(function (firstInput, i) {
                let firstId = firstInput.getAttribute('id');
                let first = firstId.substring(0, firstId.indexOf("_first"));

                let secondInput = secondInputs[i]
                let secondId = secondInput.getAttribute('id');
                let second = secondId.substring(0, secondId.indexOf("_second"));

                if (first.localeCompare(second) === 0) {
                    secondInput.addEventListener("input", function () {
                        secondInput.removeAttribute("style");
                        firstInput.removeAttribute("style");
                        if (firstInput.value !== secondInput.value) {
                            secondInput.style.boxShadow = "0 0 0 0.15rem rgba(255, 25, 25)";
                            firstInput.style.boxShadow = "0 0 0 0.15rem rgba(255, 25, 25)";
                        } else if (firstInput.value === secondInput.value && firstInput.value !== "") {
                            secondInput.style.boxShadow = "0 0 0 0.15rem rgba(0, 150, 0)";
                            firstInput.style.boxShadow = "0 0 0 0.15rem rgba(0, 150, 0)";
                        }
                    });
                    firstInput.addEventListener("input", function () {
                        secondInput.removeAttribute("style");
                        firstInput.removeAttribute("style");
                        if (firstInput.value !== secondInput.value) {
                            secondInput.style.boxShadow = "0 0 0 0.15rem rgba(255, 25, 25)";
                            firstInput.style.boxShadow = "0 0 0 0.15rem rgba(255, 25, 25)";
                        } else if (firstInput.value === secondInput.value && secondInput.value !== "") {
                            secondInput.style.boxShadow = "0 0 0 0.15rem rgba(0, 150, 0)";
                            firstInput.style.boxShadow = "0 0 0 0.15rem rgba(0, 150, 0)";
                        }
                    });
                }

            })

        }


    }
}

/**
 * @description Adds a red bullet next to the label of required inputs in forms.\
 * Handles bootstrap v5.2.x visually hidden labels displays correctly for required radio inputs.
 * 
 * @return void
 */
function formBulletsOnRequired() {
    const forms = document.querySelectorAll("form");

    if (forms.length > 0) {
        let styleTag = document.createElement("style");
        let cssSelectors = "";
        let cssText = '{content:" ";display:inline-block;width:5px;height:5px;background-color:red;vertical-align:top;border-radius:50%;}';
        let mandatorySelector = "span.mandatory-helper";

        for (const form of forms) {
            const mandatoryInputs = form.querySelectorAll("[required]");
            for (const input of mandatoryInputs) {
                let label = document.querySelector('label[for=' + input.getAttribute('id') + ']');
                if (input.getAttribute('type') !== "radio" && label !== null /* && !label.classList.contains("form-check-label") */) {
                    cssSelectors += 'label[for=' + input.getAttribute('id') + ']::after,';
                }
                // manages the bootstrap exception for visually hidden labels
                let legendElements = document.querySelectorAll('legend.required');
                if (legendElements.length > 0) {
                    cssSelectors += 'legend.required::after,';
                }
            }
            if (mandatoryInputs.length > 0 && !form.contains(form.querySelector(mandatorySelector))) {
                let mandatoryReminder = document.createElement('span');
                mandatoryReminder.classList.add("mandatory-helper");
                mandatoryReminder.innerText = "Red dots denote a required field.";
                mandatoryReminder.style = "display:inline-block;color:current;font-size:0.7rem;margin-bottom:15px;font-style:italic";
                form.insertAdjacentElement("afterbegin", mandatoryReminder);
            }
        }
        if (cssSelectors !== "") {
            cssSelectors = cssSelectors.slice(0, cssSelectors.length - 1);
            styleTag.appendChild(document.createTextNode(cssSelectors + cssText));
            document.getElementsByTagName("head")[0].appendChild(styleTag);
        }
    }
}


function enableDynamicTable() {
    $('.js-table').bootstrapTable({
        showToggle:true,
        search: true,
        showSearchClearButton: true,
        pagination: true,
        pageSize:50,
        pageList:[50, 200, 500, 1000],
        showExtendedPagination:true
      })
}