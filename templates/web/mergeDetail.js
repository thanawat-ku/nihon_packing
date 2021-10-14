$(function() { //dont finish!!!!!
    $('#my-data-table').DataTable();
});


$(document).on(
    "click",
    "#addLabelBt",
    (event) => {
        let id = event.currentTarget.id;
        switch (id) {
            case "addLabelBt":
                addLabel(event);
                break;
            default:
                console.log("no any events click");
        }
    }
);