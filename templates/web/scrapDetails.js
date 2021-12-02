$(function () {
    $('#my-data-table').DataTable({ "order": [[0, "desc"]] });
    $('#searchIssueStartDate, #searchIssueEndDate').datepicker({
        format: 'yyyy-mm-dd'
    });
});

function editScrapDetail(event){
    let scrapDetail = event.currentTarget.name;
    console.log(scrapDetail);
    var obj = JSON.parse(scrapDetail);
    $("#editScrapDetailID").val(obj.id);
    $("#editSectionID").val(obj.section_id);
    $("#editProductID").val(obj.product_id);
    $("#editDefectID").val(obj.defact_id);
    $("#editDefectCode").val(obj.defect_code);
    $("#editDefectDescription").val(obj.defect_description);
    $("#editSectionName").val(obj.section_name);
    $("#editQuantity").val(obj.scrap_detail_qty);
    
}

function deleteScrapDetail(event){
    let scrapDetail = event.currentTarget.name;
    console.log(scrapDetail);
    var obj = JSON.parse(scrapDetail);
    $("#deleteScrapDetailID").val(obj.id);
    $("#deleteDefectCode").val(obj.defect_code);
    $("#deleteDefectDescription").val(obj.defect_description);
    $("#deleteSectionName").val(obj.section_name);
    $("#deleteQuantity").val(obj.scrap_detail_qty);
    
}

$(document).on(
    "click",
    "#editBt, #deleteBt",
    (event) => {
        let id = event.currentTarget.id;
        switch (id) {
            case "editBt":
                editScrapDetail(event);
                break;
            case "deleteBt":
                deleteScrapDetail(event);
                break;
            default:
                console.log("no any events click");
        }
    }
);