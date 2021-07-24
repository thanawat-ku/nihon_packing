function syncProduct(){
    $('#syncTable').text("product start");
    $.ajax ({
        type: "GET",
        url: "http://mis.nihonseikithai.co.th/flask_detect_number/mis_sync_product",
        success: function (data) { 
            var obj=JSON.parse(data);
            var code = '';            
            for(var i=0; i<obj.length; i++) { // Loop through the data & construct the options
                code = obj[i].PartCode+' ';
            }
            // Append to the html
            $('#syncTable').text("product last code: "+code);
            syncSparePart();
        }
   });
}
function syncSparePart(){
    $('#syncTable').text("spare_part start");
    $.ajax ({
        type: "GET",
        url: "http://mis.nihonseikithai.co.th/flask_detect_number/mis_sync_spare_part",
        success: function (data) { 
            var obj=JSON.parse(data);
            var code = '';            
            for(var i=0; i<obj.length; i++) { // Loop through the data & construct the options
                code = obj[i].SparePartCode+' ';
            }
            // Append to the html
            $('#syncTable').text("spare_part last code: "+code);
            syncCustomer();
        }
   });
}
function syncCustomer(){
    $('#syncTable').text("customer start");
    $.ajax ({
        type: "GET",
        url: "http://mis.nihonseikithai.co.th/flask_detect_number/mis_sync_customer",
        success: function (data) { 
            var obj=JSON.parse(data);
            var code = '';            
            for(var i=0; i<obj.length; i++) { // Loop through the data & construct the options
                code = obj[i].CustomerCode+' ';
            }
            // Append to the html
            $('#syncTable').text("customer last code: "+code);
            syncMachine();
        }
   });
}
function syncMachine(){
    $('#syncTable').text("machine start");
    $.ajax ({
        type: "GET",
        url: "http://mis.nihonseikithai.co.th/flask_detect_number/mis_sync_machine",
        success: function (data) { 
            var obj=JSON.parse(data);
            var machine_no = '';            
            for(var i=0; i<obj.length; i++) { // Loop through the data & construct the options
                machine_no = obj[i].MachineNo+' ';
            }
            // Append to the html
            $('#syncTable').text("machine last machine_no: "+machine_no);
            syncVendor();
        }
   });
}
function syncVendor(){
    $('#syncTable').text("vendor start");
    $.ajax ({
        type: "GET",
        url: "http://mis.nihonseikithai.co.th/flask_detect_number/mis_sync_vendor",
        success: function (data) { 
            var obj=JSON.parse(data);
            var code = '';            
            for(var i=0; i<obj.length; i++) { // Loop through the data & construct the options
                code = obj[i].VendorCode+' ';
            }
            // Append to the html
            $('#syncTable').text("vendor last code: "+code);
            syncSpareReceive();
        }
   });
}
function syncSpareReceive(){
    $('#syncTable').text("spare_receive start");
    $.ajax ({
        type: "GET",
        url: "http://mis.nihonseikithai.co.th/flask_detect_number/mis_sync_spare_receive",
        success: function (data) { 
            var obj=JSON.parse(data);
            var code = '';            
            for(var i=0; i<obj.length; i++) { // Loop through the data & construct the options
                code = obj[i].InvNo+' ';
            }
            // Append to the html
            $('#syncTable').text("spare_receive last code: "+code);
            syncSpareReceiveDetail();
        }
   });
}
function syncSpareReceiveDetail(){
    $('#syncTable').text("spare_receive_detail start");
    $.ajax ({
        type: "GET",
        url: "http://mis.nihonseikithai.co.th/flask_detect_number/mis_sync_spare_receive_detail",
        success: function (data) { 
            var obj=JSON.parse(data);
            var code = '';            
            for(var i=0; i<obj.length; i++) { // Loop through the data & construct the options
                code = obj[i].SpareReceiveDetailID+' ';
            }
            // Append to the html
            $('#syncTable').text("spare_receive_detail last code: "+code);
        }
   });
}

function operationSparePartIssue(event,id){
    let user = event.currentTarget.name;
    console.log(user);
    var obj = JSON.parse(user);
    $("#operationIssueNo").html(obj.spare_part_issue_no);
    $("#issueId").val(obj.id);
    if(id == "confirmBt"){
        $("#operation").val("CONFIRMED");
        $("#operationName1").html("Confirm");
        $("#operationName2").html("confirm");
    }else if(id == "deleteBt"){
        $("#operation").val("DELETED");
        $("#operationName1").html("Delete");
        $("#operationName2").html("delete");
    }else if(id == "approveBt"){
        $("#operation").val("APPROVED");
        $("#operationName1").html("Approve");
        $("#operationName2").html("approve");
    }else if(id == "rejectBt"){
        $("#operation").val("REJECTED");
        $("#operationName1").html("Reject");
        $("#operationName2").html("reject");
    }else if(id == "preparingBt"){
        $("#operation").val("PREPARING");
        $("#operationName1").html("Prepare");
        $("#operationName2").html("prepare");
    }else if(id == "preparedBt"){
        $("#operation").val("PREPARED");
        $("#operationName1").html("Finish");
        $("#operationName2").html("finish");
    }else if(id == "completedBt"){
        $("#operation").val("COMPLETED");
        $("#operationName1").html("Complete");
        $("#operationName2").html("complete");
    }else if(id == "cancelBt"){
        $("#operation").val("CANCELED");
        $("#operationName1").html("Cancel");
        $("#operationName2").html("cancel");
    }
}
$(function() {
    $('#my-data-table').DataTable({"scrollX": true,"order": [[ 0, "desc" ]]});
    $('#searchIssueStartDate, #searchIssueEndDate').datepicker({
        format: 'yyyy-mm-dd'
    });
});

$(document).on(
    "click",
    "#syncBt",
    (event) => {
        syncProduct();
    }
);
$(document).on(
    "click",
    "#confirmBt,#deleteBt,#approveBt,#rejectBt,#preparingBt, #preparedBt, #cancelBt, #completedBt",
    (event) => {
        let id = event.currentTarget.id;
        operationSparePartIssue(event,id);
    }
);