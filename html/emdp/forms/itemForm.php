<div class="row layout-top-spacing">
	<div id="basic" class="col-lg-12 col-sm-12 col-12 layout-spacing">
		<div class="statbox widget box box-shadow">
			<!-- Start Part A. -->
			<div class="widget-header">
				<div class="row">
					<div class="col-xl-12 col-md-12 col-sm-12 col-12">
						<h4>Item</h4>
					</div>
				</div>
			</div>
			<!-- Start Part A Widget -->
			<div class="widget-content widget-content-area">

				<input type="hidden" name="itemID" value="<?php echo $id; ?>">

				<!-- Item Supplier -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">Supplier:</label>
					<div class="col-sm-9">
						<?php
						$sql = "SELECT * FROM suppliers";
						$result = mysqli_query($connection, $sql);
						if (!$result) {
							die("Invalid query: " . $connection->error);
						}?>
						<select class="form-select" name="supplier" id="selectItemSupplier" required>
							<option disabled selected value="">Please Choose</option>
							<?php
							while ($row = mysqli_fetch_assoc($result)) {
								$sel = ($update && $row['supplierID'] == $n['supplierID']) ? "selected" : "";
								echo '<option value="' . $row['supplierID'] . '" ' . $sel . '>' . htmlspecialchars($row['name']) . '</option>';
							}
							?>
						</select>
						
					</div>
				</div>
				
				<!-- Text Box Item Name -->
				<div class="row mb-3">
					<label for="" class="col-sm-3 col-form-label">Item Name:</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" placeholder="Item Name" aria-label="itemName"
							name="name" required
							value="<?php echo htmlspecialchars($update ? $n['name'] : '', ENT_QUOTES, 'UTF-8'); ?>">
							
					</div>
				</div>

				<!-- Item Type (Select Box?) -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">Inventory Type:</label>
					<div class="col-sm-9">
						<?php $opt_arr = array("Medicine", "Equipment"); ?>
						<select class="form-select" required name="type" id="selectItemType">
							<option disabled selected value="">Please Choose</option>
							<?php
							foreach ($opt_arr as $opt) {
								if ($update && $opt == $n['type']) {
									$sel = "selected";
								} else {
									$sel = "";
								}

								echo '<option value="' . $opt . '"' . $sel . '>' . $opt . '</option>';
							}
							?>
						</select>
						
					</div>
				</div>
				

				<!-- Category Type (Select Box?) -->
				<div class="row mb-3">
					<label class="col-sm-3 col-form-label">Category:</label>
					<div class="col-sm-9">
						<?php $opt_arr = array("Painkiller", "Antibiotic", "Anti-inflammatory", "Antihistamine", "Diabetes", "Diagnostic"); ?>
						<select  class="form-select" required name="category" id="selectItemCat" >
							<option disabled selected value="">Please Choose</option>
							<?php
							foreach ($opt_arr as $opt) {
								if ($update && $opt == $n['category']) {
									$sel = "selected";
								} else {
									$sel = "";
								}

								echo '<option value="' . $opt . '"' . $sel . '>' . $opt . '</option>';
							}
							?>
						</select>
						
					</div>
				</div>

				<!-- Manufacture Date 
				<div class="row mb-3" id="manufactureDateRow">
					<label class="col-sm-3 col-form-label">Manufacture Date:</label>
					<div class="col-sm-9">
						<input required class="form-control flatpickr flatpickr-input active" name="manufactured_date" id="manufactureDate"
							value="<?php #echo htmlspecialchars($update ? $n['manufactured_date'] : '', ENT_QUOTES, 'UTF-8'); ?>"
							 type="text" placeholder="Select Date">
							
					</div>
					
				</div>

				<!-- Expiry Date 
				<div class="row mb-4" id="expiryDateRow">
					<label class="col-sm-3 col-form-label">Expiry Date:</label>
					<div class="col-sm-9">
						<input  required name="expired_date" id="expiryDate"
							value="<?php #echo htmlspecialchars($update ? $n['expired_date'] : '', ENT_QUOTES, 'UTF-8'); ?>" class="form-control flatpickr flatpickr-input active" type="text" placeholder="Select Date">
							
					</div>
					
				</div>
				-->

				<div class="row mb-3" id="manufactureDateRow">
					<label class="col-sm-3 col-form-label">Manufacture Date:</label>
					<div class="col-sm-9">
						<input id="manufactureDate" required type="text" class="form-control" placeholder="YYYY/MM/DD" aria-label="yyyy/mm/dd"
							name="manufactured_date"
							value="<?php echo htmlspecialchars($update ? $n['manufactured_date'] : '', ENT_QUOTES, 'UTF-8'); ?>">
					</div>
				</div>

				<div class="row mb-4" id="expiryDateRow">
					<label class="col-sm-3 col-form-label">Expiry Date:</label>
					<div class="col-sm-9">
						<input id="expiryDate" required type="text" class="form-control" placeholder="YYYY/MM/DD" aria-label="yyyy/mm/dd"
							name="expired_date"
							value="<?php echo htmlspecialchars($update ? $n['expired_date'] : '', ENT_QUOTES, 'UTF-8'); ?>">
					</div>
				</div>

				<!-- Quantity -->
				<div class="row mb-3">
    <label class="col-sm-3 col-form-label">Quantity:</label>
    <div class="col-sm-9">
        <input required type="number" class="form-control no-spinner" placeholder="Quantity" aria-label="Quantity"
               name="quantity" min="0" step="1"
               oninput="validateQuantity(this)"
               value="<?php echo htmlspecialchars($update ? $n['quantity'] : '', ENT_QUOTES, 'UTF-8'); ?>">
    </div>
</div>


				<!-- Price  -->
				<!-- Price Input -->
<div class="row mb-4">
    <label class="col-sm-3 col-form-label">Price (RM):</label>
    <div class="col-sm-9">
        <input type="text" id="price" name="price" class="form-control" required placeholder="Price (RM)"
               oninput="formatPrice(this)"
               value="<?php echo htmlspecialchars($update ? $n['price'] : '', ENT_QUOTES, 'UTF-8'); ?>">
    </div>
</div>




				<!-- Description  -->
				<div class="row mb-4">
					<label class="col-sm-3 col-form-label">Description:</label>
					<div class="col-sm-9">
						<textarea required name="description" id="textarea" class="form-control textarea" maxlength="225"
							rows="2"
							placeholder="Description"><?php echo htmlspecialchars($update ? $n['description'] : '', ENT_QUOTES, 'UTF-8'); ?></textarea>
					</div>

				</div>

				<!-- Select Box Status maybe this should be automatic-->
				<div hidden class="row mb-4">
					<label class="col-sm-3 col-form-label">Status:</label>
					<div class="col-sm-9">
						<?php $opt_arr = array("In Stock", "Low Stock", "Out Of Stock"); ?>
						<select name="status" id="selectStatus">
							<option value="" selected disabled>Please Choose</option>
							<?php
							foreach ($opt_arr as $opt) {
								if ($update && $opt == $n['status']) {
									$sel = "selected";
								} else {
									$sel = "";
								}

								echo '<option value="' . $opt . '"' . $sel . '>' . $opt . '</option>';
							}
							?>
						</select>
					</div>
				</div>

				<!-- End Part A  Widget -->

				<?php if ($update == true): ?>
					<input type="submit" class="btn btn-primary" name="update" value="Update">
				<?php else: ?>
					<input type="submit" class="btn btn-primary" name="submit" value="Submit">
				<?php endif ?>
			</div>

		</div>
	</div>
</div>


