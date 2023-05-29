ajaxFormSubmission("form[name='password_edit_form']", "staticBackdropEditPassword");
ajaxFormSubmission("form[name='email_edit_form']", "staticBackdropEditEmail");

/**
 * @description Handles all the edit-password process on the account page.
 * 
 * @return void
 */
function ajaxFormSubmission(querySelector, modalParentId) {

    let form = document.querySelector(querySelector);

    if (form !== null) {

        let submitBtn = form.querySelector("button[type='submit']");
        let parentDom = document.getElementById(modalParentId);

        if (submitBtn !== null && parentDom !== null) {

            form.addEventListener('submit', function (e) {
                e.preventDefault();

                ajaxForm = new FormData(form);

                fetch(submitBtn.getAttribute('formaction'), {
                    method: 'POST',
                    headers: new Headers({ "X-Requested-With": "XMLHttpRequest" }),
                    body: ajaxForm
                })
                    .then(response => response.json())
                    .then(response => {

                        if (response.status === 0) {
                            let DOMResponse = new DOMParser().parseFromString(response.body, "text/html");
                            parentDom.innerHTML = DOMResponse.getElementById(modalParentId).innerHTML;
                            ajaxProfileEditPassword();
                            formSubmissionSpinner();
                        }

                        if (response.status === 1) {
                            window.location.href = response.url;
                        }
                    })
            })
        }
    }
}