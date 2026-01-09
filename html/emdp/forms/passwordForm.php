<input type="hidden" name="userID" value="<?php echo $id; ?>">

<div class="row mb-3">
    <label class="col-sm-3 col-form-label">Old Password:</label>
    <div class="col-sm-9">
    <input type="text" class="form-control"  placeholder="Old Password" aria-label="Old Password" name="oldPassword"  required value="">
    </div>
</div>

<div class="row mb-3">
    <label class="col-sm-3 col-form-label">New Password:</label>
    <div class="col-sm-9">
    <input type="text" class="form-control"  placeholder="New Password" aria-label="New Password" name="newPassword"  required value="">
    </div>
</div>

<div class="row mb-3">
    <label class="col-sm-3 col-form-label">Verify New Password:</label>
    <div class="col-sm-9">
    <input type="text" class="form-control"  placeholder="Verify New Password" aria-label="Verify New Password" name="newPassword2"  required value="">
    </div>
</div>

<div class="row mb-3 justify-content-center">
<div class="col-sm-1">
<input type="submit" class="btn btn-primary" name="submit" value="Submit">
</div>
</div>
