<!-- Modal Label lot -->
<div class="modal fade" id="labelLot">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="form-labelLot" action="label_lot" method="post">
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Label Lot</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <!-- Modal body -->
        <div class="modal-body">          
          <div class="form-product">
            <input type="hidden" id="labelLotID" name="id">
            <input type="hidden" id="labelLotNoLabel" name="lot_no">
            <input type="hidden" id="labelLotNoLabelStatus" name="status">
            <label for="lotNo">Do you want to label lot: <span id="labelLotNo"></span></label>
          </div>                 
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-danger">Label</button>
          <button type="button" class="btn btn-info" data-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>