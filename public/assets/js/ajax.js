profileAjaxEditPassword();

/**
 * @description Handles all the edit password process on the account page.
 * @return void
 */
function profileAjaxEditPassword() {

    let form = document.querySelector("form[name='password_form']");

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
                            profileAjaxEditPassword();
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


profileAjaxEditEmail();

/**
 * @description Handles all the edit email process on the account page.
 * @return void
 */
function profileAjaxEditEmail() {

    let form = document.querySelector("form[name='email_form']");

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
                            profileAjaxEditEmail();
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

