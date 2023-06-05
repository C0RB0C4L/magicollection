enableMultiSelectInput();
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