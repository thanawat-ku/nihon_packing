$(function() {
    $('#my-data-table').DataTable();
});
function editProduct(event){
    let product = event.currentTarget.name;
    console.log(product);
    var obj = JSON.parse(product);
    $("#editProductID").val(obj.id);
    $("#editProduct_code").val(obj.product_code);
    $("#editProduct_name").val(obj.product_name);
    $("#editPrice").val(obj.price);
    $("#editStdPack").val(obj.std_pack);
    $("#editStdBox").val(obj.std_box);
}

  function deleteProduct(event){
    let product = event.currentTarget.name;
    console.log(product);
    var obj = JSON.parse(product);
    $("#deleteProductID").val(obj.id);
    $("#deleteProductCode").text(obj.product_code);
    $("#deleteProductName").text(obj.product_name);
}

$(document).on(
    "click",
    "#editBt, #deleteBt",
    (event) => {
        let id = event.currentTarget.id;
        switch (id) {
            case "editBt":
                editProduct(event);
                break;
            case "deleteBt":
                deleteProduct(event);
                break;
            default:
                console.log("no any events click");
        }
    }
);