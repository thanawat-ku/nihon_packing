$(function () { //dont finish!!!!!
    $('#my-data-table').DataTable();
});


$(document).on(
    "click",
    "#deleteDetailBt",
    (event) => {
        let id = event.currentTarget.id;
        switch (id) {
            case "deleteDetailBt":
                deleteDetail(event);
                break;
            default:
                console.log("no any events click");
        }
    }
);