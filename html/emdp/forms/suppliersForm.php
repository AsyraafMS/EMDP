<div class="row layout-top-spacing">
	<div id="basic" class="col-lg-12 col-sm-12 col-12 layout-spacing">
		<div class="statbox widget box box-shadow">
			<!-- Start Part A. -->
			<div class="widget-header">
				<div class="row">
					<div class="col-xl-12 col-md-12 col-sm-12 col-12">
						<h4>Supplier</h4>
					</div>
				</div>
			</div>
			<!-- Start Part A Widget -->
			<div class="widget-content widget-content-area">

				<input type="hidden" name="supplierID" value="<?php echo $id; ?>">

				<!-- Text Box Supplier Name -->
				<div class="row mb-3">
					<label for="supplierName" class="col-sm-3 col-form-label">Supplier Name:</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" placeholder="Supplier Name" aria-label="supplierName" name="name" required
							value="<?php echo htmlspecialchars($update ? $n['name'] : '', ENT_QUOTES, 'UTF-8'); ?>">
					</div>
					 <div class="valid-feedback">
						Looks good!
					</div>
					<div class="invalid-feedback">
						Please fill the name
					</div>
				</div>

				<!-- Text Box Phone Num -->
				<div class="row mb-3">
					<label for="" class="col-sm-3 col-form-label">Phone Num:</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" placeholder="Phone Number" aria-label="phoneNum" name="phoneNum" required
							value="<?php echo htmlspecialchars($update ? $n['phone_num'] : '', ENT_QUOTES, 'UTF-8'); ?>">
					</div>
					 <div class="valid-feedback">
						Looks good!
					</div>
					<div class="invalid-feedback">
						Please fill the name
					</div>
				</div>

				<!-- Text Box Email -->
				<div class="row mb-3">
					<label for="" class="col-sm-3 col-form-label">Email Address:</label>
					<div class="col-sm-9">
						<input type="text" class="form-control" placeholder="Email Address" aria-label="emailAddress" name="emailAddress" required
							value="<?php echo htmlspecialchars($update ? $n['email'] : '', ENT_QUOTES, 'UTF-8'); ?>">
					</div>
					 <div class="valid-feedback">
						Looks good!
					</div>
					<div class="invalid-feedback">
						Please fill the name
					</div>
				</div>

				<!-- Description  -->
				<div class="row mb-4">
					<label class="col-sm-3 col-form-label">Supplier Address:</label>
					<div class="col-sm-9">
						<textarea required name="address" id="textarea" class="form-control textarea" maxlength="225"
							rows="2"
							placeholder="Address"><?php echo htmlspecialchars($update ? $n['address'] : '', ENT_QUOTES, 'UTF-8'); ?></textarea>
					</div>
					 <div class="valid-feedback">
						Looks good!
					</div>
					<div class="invalid-feedback">
						Please fill the name
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