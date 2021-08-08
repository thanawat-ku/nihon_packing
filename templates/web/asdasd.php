<!-- Modal add lot -->
<div class="modal fade" id="addLot">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <form id="form-addLot" action="add_lot" method="post">
        <!-- Modal Header -->
        <div class="modal-header">
          <h4 class="modal-title">Add Lot</h4>
          <button type="button" class="close" data-dismiss="modal">&times;</button>
        </div>
        <!-- Modal body -->
        <div class="modal-body">          
          <div class="form-product">
            <input type="hidden" id="addLotID" name="id">
            <label for="lotNo">Lot No</label>
            <input type="text" autocomplete="off" class="form-control" id="addLotNo" name="lot_no" required>
          </div>                 
          <div class="form-product">
            <label for="email">Lot</label>
            <select class="form-control"  id="addLotID" name="product_id">
            {% for p in products %}
              <option value={{ p.id }}>({{p.product_code}}) {{ p.product_name}} </option>
            {% endfor %}
            </select>
          </div>           
          <div class="form-product">
            <label for="lotNo">Quantity</label>
            <input type="text" autocomplete="off" class="form-control" id="addQuantity" name="quantity" required>
          </div> 
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Save</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>
        </div>
      </form>
    </div>
  </div>
</div>