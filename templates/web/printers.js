import DataTable from 'datatables.net-bs4';
$(function() {
    let table=DataTable('#my-data-table');
});
function editPrinter(event){
    let printer = event.currentTarget.name;
    console.log(printer);
    var obj = JSON.parse(printer);
    $("#editPrinterID").val(obj.id);
    $("#editPrinterName").val(obj.printer_name);
    $("#editAddress").val(obj.printer_address);
    $("#editPrinterType").val(obj.printer_type);
}

  function deletePrinter(event){
    let printer = event.currentTarget.name;
    console.log(printer);
    var obj = JSON.parse(printer);
    $("#deletePrinterId").val(obj.id);
    $("#deletePrinterName").text(obj.printer_name);
}
$(document).on(
    "click",
    "#editBt, #deleteBt",
    (event) => {
        let id = event.currentTarget.id;
        switch (id) {
            case "editBt":
                editPrinter(event);
                break;
            case "deleteBt":
                deletePrinter(event);
                break;
            default:
                console.log("no any events click");
        }
    }
);