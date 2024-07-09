import DataTable from 'datatables.net-bs4';
$(function() {
    let table=new DataTable('#my-data-table');
});
function editProduct(event){
    let product = event.currentTarget.name;
    console.log(product);
    var obj = JSON.parse(product);
    $("#editProductID").val(obj.id);
    $("#editProduct_code").val(obj.part_code);
    $("#editProduct_no").val(obj.part_no);
    $("#editProduct_name").val(obj.part_name);
    $("#editStdPack").val(obj.std_pack);
    $("#editStdBox").val(obj.std_box);
}

  function deleteProduct(event){
    let product = event.currentTarget.name;
    console.log(product);
    var obj = JSON.parse(product);
    $("#deleteProductID").val(obj.id);
    $("#deleteProductCode").text(obj.part_code);
    $("#deleteProductName").text(obj.part_name);
}

function undeleteProduct(event){
  let product = event.currentTarget.name;
  console.log(product);
  var obj = JSON.parse(product);
  $("#undeleteProductID").val(obj.id);
  $("#undeleteProductCode").text(obj.part_code);
  $("#undeleteProductName").text(obj.part_name);
}

$(document).on(
    "click",
    "#editBt, #deleteBt, #undeleteBt",
    (event) => {
        let id = event.currentTarget.id;
        switch (id) {
            case "editBt":
                editProduct(event);
                break;
            case "deleteBt":
                deleteProduct(event);
            case "undeleteBt":
                undeleteProduct(event);
                break;
            default:
                console.log("no any events click");
        }
    }
);