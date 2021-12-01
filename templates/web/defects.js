$(function() {
    $('#my-data-table').DataTable();
});

$(document).on(
    "click",
    (event) => {
        let id = event.currentTarget.id;
        switch (id) {
            default:
                console.log("no any events click");
        }
    }
);