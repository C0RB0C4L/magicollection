ajaxFormSubmission("form[name='password_edit_form']", "#staticBackdropEditPassword .modal-body");
ajaxFormSubmission("form[name='email_edit_form']", "#staticBackdropEditEmail .modal-body");
ajaxFormSubmission("form[name='album_form']", "#staticBackdropCreateAlbum .modal-body");

ajaxFormFetchAndSubmission("button[data-bs-target='#staticBackdropAlbumRenameAjax']", "#staticBackdropAlbumRenameAjax .modal-body");

/**
 * @description Handles all the edit-password process on the account page.
 * 
 * @return void
 */
function ajaxFormSubmission(formSelector, containerSelector) {

    let form = document.querySelector(formSelector);

    if (form !== null) {

        let submitBtn = form.querySelector("button[type='submit']");
        let container = document.querySelector(containerSelector);

        if (submitBtn !== null && container !== null) {

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

                        if (response.status !== undefined && response.status === 0) {
                            let DOMResponse = new DOMParser().parseFromString(response.body, "text/html");
                            container.innerHTML = "";
                            container.append(DOMResponse.querySelector(formSelector));
                            ajaxFormSubmission(formSelector, containerSelector);
                            formSubmissionSpinner();
                            enableHighlightIfFieldsAreDifferent();
                        }

                        if (response.status !== undefined && response.status === 1) {
                            window.location.href = response.url;
                        }
                    })
            })
        }
    }
}

function ajaxFormFetchAndSubmission(fetcherSelector, containerSelector) {

    let fetchers = document.querySelectorAll(fetcherSelector);
    let container = document.querySelector(containerSelector);

    if (fetchers.length !== 0 && container !== null) {

        for (let fetcher of fetchers) {
            fetcher.addEventListener("click", function () {

                let url = fetcher.getAttribute("data-form-fetch");

                ajaxFetchSpinner(containerSelector, true);

                fetch(url, {
                    method: 'GET',
                    headers: new Headers({ "X-Requested-With": "XMLHttpRequest" }),
                })
                    .then(response => response.json())
                    .then(response => {

                        if (response.status !== undefined && response.status === 0) {
                            let DOMResponse = new DOMParser().parseFromString(response.body, "text/html");
                            let form = DOMResponse.querySelector("form");
                            ajaxFetchSpinner(containerSelector, false);
                            container.innerHTML = "";
                            container.append(form);
                            formSubmissionSpinner();
                            enableHighlightIfFieldsAreDifferent();

                            ajaxFormSubmission("form#" + form.getAttribute('id'), containerSelector);
                        }
                    })

            })

        }
    }
}
