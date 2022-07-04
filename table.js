/**
 * Function to see the content of the next page
 * for jsDataTable method
 * @param tableId id of the table to paginate
 * @param totalPages number of total pages
 * @param rowsToShow number of rows showed by page
 */
 function js_nextPage(tableId, totalPages, rowsToShow){
    // Get the number of the current page
    const currentPage = parseInt($('.numeric-pagination-button.page-selected').text(), 10);
    // Get the next page number
    const page = currentPage + 1;
    updateNumericPagination(tableId, page, totalPages, rowsToShow);
}

/**
 * Function to see the content of the previous page
 * for jsDataTable method
 * @param tableId id of the table to paginate
 * @param totalPages number of total pages
 * @param rowsToShow number of rows showed by page
 */
function js_previousPage(tableId, totalPages, rowsToShow){
    // Get the number of the current page
    const currentPage = parseInt($('.numeric-pagination-button.page-selected').text(), 10);
    // Get the previous page number
    const page = currentPage - 1;
    updateNumericPagination(tableId, page, totalPages, rowsToShow);
}

/**
 * Function to see the content of the page selected
 * for jsDataTable method
 * @param e element clicked
 * @param tableId id of the table to paginate
 * @param totalPages number of total pages
 * @param rowsToShow number of rows showed by page
 */
function js_gotoPage(e, tableId, totalPages, rowsToShow){
    let page;
    // Validate if the element has a number of page in dataset (go to first and go to last page buttons)
    if ($(e)[0].dataset.page) {
        page = parseInt($(e)[0].dataset.page, 10);
    } else {
        // Get the number of the page clicked
        page = parseInt($(e).text(), 10);
    }
    updateNumericPagination(tableId, page, totalPages, rowsToShow);
}

/**
 * Function to sort the table by the header clicked
 * for jsDataTable method
 * @param e element clicked (column header)
 * @param tableId id of the table to sort
 */
function js_orderColumn(e, tableId) {
    const table = $('#'+tableId+' tbody');
    // Convert the rows into an array and sort the content
    let rows = table.find('tr').toArray().sort(function(a, b) {
        // Get the values to compare according to the element clicked.
        const valA = $(a).children('td').eq($(e).index()).text();
        const valB = $(b).children('td').eq($(e).index()).text();
        // Sort depending if the values are valid dates, numbers or string
        // Consider valid date if has the characters - or / and if the conversion with Date.parse is not NaN
        if (
            !isNaN(Date.parse(valA)) && !isNaN(Date.parse(valB)) &&
            (valA.indexOf('-') !== -1 || valB.indexOf('-') !== -1 || valA.indexOf('/') !== -1 || valB.indexOf('/') !== -1)
        ) {
            // Validate if the sort is ascending, if not, sort descending
            if (e.ariaSort === "asc") {
                return Date.parse(valB) - Date.parse(valA);
            }
            return Date.parse(valA) - Date.parse(valB);
        } else if (!isNaN(valA.replace(",", "")) && !isNaN(valB.replace(",", ""))) {
            // Remove the commas (thousand separator) and convert to number (float numbers are valid too).
            // Commas are removed because they affect the comparison (if it is not removed, it is calculate as a string value)
            // Validate if the sort is ascending, if not, sort descending
            if (e.ariaSort === "asc") {
                return Number(valB.replace(",", "")) - Number(valA.replace(",", ""));
            }
            return Number(valA.replace(",", "")) - Number(valB.replace(",", ""));
        } else {
            // Compare characters if they are not dates or numbers (Strings)
            // Validate if the sort is ascending, if not, sort descending
            if (e.ariaSort === "asc") {
                return valB.toString().localeCompare(valA);
            }
            return valA.toString().localeCompare(valB);
        }
    });

    // Update the attribute ariaSort to the next sort
    if (e.ariaSort === "asc") {
        e.ariaSort = "desc";
        $(e).removeClass("order_def order_asc").addClass("order_desc");
    } else {
        e.ariaSort = "asc";
        $(e).removeClass("order_def order_desc").addClass("order_asc");
    }

    // Set default the class of the other sortable headers
    $(e).siblings("[aria-sort]").each(function () {
        $(this).removeClass("order_desc order_asc").addClass("order_def");
    });

    // Put the rows into the table
    table.append(rows);

    // Validate if the table has pagination and more of one page if the first or the last button has the onlick attr
    // if first/last button have an active "onclick" listener, means that table has more than one page.
    // When table only has one page, we do not need to hide rows.
    if ($('#pagination-' + tableId + ' span[data-page][onclick]')[0]) {
        // Get cuurentPageButton, totalPages and rowsToShow, they are required in the "js_gotoPage" function.
        // Get the button of the current page selected
        const currentPageButton = $('#pagination-' + tableId + ' .numeric-pagination-button.page-selected');
        // Get the function of onclick attribute
        const goToPageFunction = $('#pagination-' + tableId + ' span[data-page][onclick]')[0].attributes.onclick.value;
        // Get only the params provided to the function: totalPages (third param), rowsToShow (fourth parama)
        const params = goToPageFunction.substring(goToPageFunction.indexOf('(') + 1, goToPageFunction.indexOf(')'));
        // Get the number of total pages and rows to show from the params of the function
        const totalPages = parseInt(params.split(",")[2], 10);
        const rowsToShow = parseInt(params.split(",")[3], 10);
        // Call the function to paginate, update the rows and stay in the current page
        js_gotoPage(currentPageButton, tableId, totalPages, rowsToShow);
    }
}

/**
 * Function to update the pagination depending of the current page
 * for jsDataTable method
 * @param tableId id of the table
 * @param currentPage number of the current page
 * @param totalPages number of total pages
 * @param rowsToShow number of rows showed by page
 */
function updateNumericPagination(tableId, currentPage, totalPages, rowsToShow){
    const pagination = $('#pagination-' + tableId);

    // Update HTML of the numeric pagination
    $(pagination).children('.pagination-nums').empty();
    let start, end;
    // Show max 6 numbers in the numeric pagination
    const pageLimit = 6;
    if (totalPages > pageLimit) {
        start = (currentPage < pageLimit) ?  1 : currentPage - pageLimit + 2;
        end = (currentPage + 1 <= pageLimit) ? pageLimit : ((currentPage + 1 >= totalPages) ? totalPages : (currentPage + 1));
    } else {
        start = 1;
        end = totalPages;
    }
    // Create the numeric buttons
    for (let i = start; i <= end; i++) {
        let numericButton;
        if (i === currentPage) {
            numericButton = $('<div/>',{
                text: i,
                class: 'numeric-pagination-button page-selected page-unavailable',
                id: 'page-'+i
            });
        } else {
            numericButton = $('<div/>',{
                text: i,
                class: 'numeric-pagination-button page-available',
                id: 'page-'+i,
                onclick: `js_gotoPage(this, '${tableId}', ${totalPages}, ${rowsToShow})`
            });
        }
        numericButton.appendTo('.pagination-nums');
    }
    if (totalPages > end) {
        $('<div/>',{
            text: ' ... '
        }).appendTo('.pagination-nums');
    }

    // Selectors of the arrow buttons
    const firstBtn = $(pagination).children('span:first');
    const prevBtn = $(firstBtn).next();
    const lastBtn = $(pagination).children('span:last');
    const nextBtn = $(lastBtn).prev();

    // Styles and functionality of the arrows buttons depends of the current page
    if (currentPage === 1) {
        // Disable the first and previous button and enable the nex and the last buttons
        firstBtn.addClass("pagination-unavailable");
        firstBtn.removeClass("pagination-available pagination-button");
        prevBtn.addClass("pagination-unavailable");
        prevBtn.removeClass("pagination-available pagination-button");
        prevBtn.removeAttr('onclick');
        nextBtn.removeClass( "pagination-unavailable");
        nextBtn.addClass( "pagination-available pagination-button");
        nextBtn.attr('onclick', `js_nextPage('${tableId}', ${totalPages}, ${rowsToShow})`);
        lastBtn.removeClass( "pagination-unavailable");
        lastBtn.addClass( "pagination-available pagination-button");
        lastBtn.attr('data-page', totalPages);
        lastBtn.attr('onclick', `js_gotoPage(this, '${tableId}', ${totalPages}, ${rowsToShow})`);
    } else if (currentPage > 1) {
        // Enable all the buttons
        firstBtn.removeClass( "pagination-unavailable");
        firstBtn.addClass( "pagination-available pagination-button");
        firstBtn.attr('onclick', `js_gotoPage(this, '${tableId}', ${totalPages}, ${rowsToShow})`);
        prevBtn.removeClass( "pagination-unavailable");
        prevBtn.addClass( "pagination-available pagination-button");
        prevBtn.attr('onclick', `js_previousPage('${tableId}', ${totalPages}, ${rowsToShow})`);
        nextBtn.removeClass( "pagination-unavailable");
        nextBtn.addClass( "pagination-available pagination-button");
        nextBtn.attr('onclick', `js_nextPage('${tableId}', ${totalPages}, ${rowsToShow})`);
        lastBtn.removeClass( "pagination-unavailable");
        lastBtn.addClass( "pagination-available pagination-button");
        lastBtn.attr('data-page', totalPages);
        lastBtn.attr('onclick', `js_gotoPage(this, '${tableId}', ${totalPages}, ${rowsToShow})`);
    }
    if (currentPage === totalPages) {
        // Disable the next and last button and enable the first and previous buttons
        firstBtn.removeClass( "pagination-unavailable");
        firstBtn.addClass( "pagination-available pagination-button");
        firstBtn.attr('onclick', `js_gotoPage(this, '${tableId}', ${totalPages}, ${rowsToShow})`);
        prevBtn.removeClass( "pagination-unavailable");
        prevBtn.addClass( "pagination-available pagination-button");
        prevBtn.attr('onclick', `js_previousPage('${tableId}', ${totalPages}, ${rowsToShow})`);
        nextBtn.addClass( "pagination-unavailable");
        nextBtn.removeClass( "pagination-available pagination-button");
        nextBtn.removeAttr('onclick');
        lastBtn.addClass( "pagination-unavailable");
        lastBtn.removeClass( "pagination-available pagination-button");
        lastBtn.attr('data-page', totalPages);
    }

    // Update table hits
    const totalRows = $('#' + tableId + ' tbody tr').length;
    const tableHits = $('#PageHits-'+tableId).children();
    tableHits.text("Page " + currentPage + " of " + totalPages + " (" + totalRows + " entries)");

    // Show and hide the rows to display the desired page
    updateTableRows(tableId, currentPage, rowsToShow);
}

/**
 * Function to show or hide the rows of the table depending of the number of rows showed
 * for jsDataTable method
 * @param tableId id of the table
 * @param currentPage number of the current page
 * @param rowsToShow number of rows showed by page
 */
function updateTableRows(tableId, currentPage, rowsToShow) {
    const rows = $('#'+ tableId + ' tbody tr');
    // Set the first row and the end row to show
    const startRow = (currentPage-1) * rowsToShow;
    const endRow = startRow === 0 ? rowsToShow : startRow + rowsToShow;

    // Hide all the rows and only show a portion
    rows.hide();
    rows.slice(startRow, endRow).show();
}