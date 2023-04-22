import DataTable from 'datatables.net-bs4';
$(function() {
    let table=new DataTable('#my-data-table');
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