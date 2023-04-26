import DataTable from 'datatables.net-bs4';
$(function () { //dont finish!!!!!
    let table=new DataTable('#my-data-table');
});


function deleteDetail(event) {
    let lot = event.currentTarget.name;
    console.log(lot);
    var obj = JSON.parse(lot);
    $("#deleteLabelID").val(obj.id);
    $("#deleteLabelNo").text(obj.label_no);
}

function confirmMerge(event) {
    let lot = event.currentTarget.name;
    console.log(lot);
    var obj = JSON.parse(lot);
    $("#confirmMergeId").val(obj.id);
    $("#stdPack").val(obj.std_pack);
    $("#confirmMergeNo").text(obj.merge_no);
}

$(document).on(
    "click",
    "#deleteDetailBt, #confirmBt ",
    (event) => {
        let id = event.currentTarget.id;
        switch (id) {
            case "deleteDetailBt":
                deleteDetail(event);
                break;
            case "confirmBt":
                confirmMerge(event);
                break;
            default:
                console.log("no any events click");
        }
    }
);