enableDynamicTable();
/**
 * @description Setups the dynamic tables\
 * Add the class .js-table to the \<table> element.
 * 
 * @return void
 */
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