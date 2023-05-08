ajaxProfileEditPassword();
ajaxProfileEditEmail();


/**
 * @description Handles all the edit-password process on the account page.
 * 
 * @return void
 */
function ajaxProfileEditPassword() {

    let form = document.querySelector("form[name='password_edit_form']");

    if (form !== null) {

        let submitBtn = form.querySelector("button[type='submit']");
        let parentId = "staticBackdropEditPassword";
        let parentDom = document.getElementById(parentId);

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
                            parentDom.innerHTML = DOMResponse.getElementById(parentId).innerHTML;
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


/**
 * @description Handles all the edit-email process on the account page.
 * 
 * @return void
 */
function ajaxProfileEditEmail() {

    let form = document.querySelector("form[name='email_edit_form']");

    if (form !== null) {

        let submitBtn = form.querySelector("button[type='submit']");
        let parentId = "staticBackdropEditEmail";
        let parentDom = document.getElementById(parentId);

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
                            parentDom.innerHTML = DOMResponse.getElementById(parentId).innerHTML;
                            ajaxProfileEditEmail();
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