$(function() {
    $("#searchIssueStartDate, #searchIssueEndDate").datepicker({
        format: "yyyy-mm-dd",
    });
    $("#CustomerName-report").val(0);
});

function datePick() {
    $("#datePicker").toggleClass("d-none");
    if ($("#datePicker").hasClass("d-none")) {
        $("#datePicker input.check-date").val("N");
    } else {
        $("#datePicker input.check-date").val("Y");
    }
}

$(document).on("change", "#dateCheck", (event) => {
    let id = event.currentTarget.id;
    switch (id) {
        case "dateCheck":
            datePick(event);
            break;
    }
});