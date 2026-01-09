<!-- Text Box ID -->
<input type="hidden" class="form-control" placeholder="UserID" aria-label="userID" name="userID" value="<?php  echo htmlspecialchars($update ? $n['userID'] : '', ENT_QUOTES, 'UTF-8'); ?>">


<!-- Text Box Nama -->
<div class="row mb-3">
    <label for="" class="col-sm-3 col-form-label">Name:</label>
    <div class="col-sm-9">
        <input type="text" class="form-control" placeholder="Full Name" aria-label="Full Name" name="name"  required value="<?php  echo htmlspecialchars($update ? $n['name'] : '', ENT_QUOTES, 'UTF-8'); ?>">
    </div>
</div>

<!-- Text Box Username -->
<div class="row mb-3">
    <label for="" class="col-sm-3 col-form-label">Username:</label>
    <div class="col-sm-9">
        <input type="text" class="form-control" placeholder="Username" aria-label="Username" name="username"  required value="<?php  echo htmlspecialchars($update ? $n['username'] : '', ENT_QUOTES, 'UTF-8'); ?>">
    </div>
</div>

<!-- Account Type -->
<div class="row mb-3">
    <label class="col-sm-3 col-form-label">Account Type:</label>
    <div class="col-sm-9">
        <?php $opt_arr = array("Superuser","Admin","Pharmacist","Supplier");?>
        <select name="type" required id="type">
            <?php
                foreach ($opt_arr as $opt) {
                    if($update && $opt == $n['type'] ){
                        $sel ="selected";
                    } else{
                        $sel ="";
                    }
                    echo '<option value="'.$opt.'"' . $sel . '>' . $opt . '</option>';
                }
            ?>
        </select>
    </div>
</div>

<!-- Text Box Email -->
<div class="row mb-3">
    <label class="col-sm-3 col-form-label">E-Mail:</label>
    <div class="col-sm-9">
        <input type="text" class="form-control" placeholder="E-Mail Address" aria-label="E-Mail Address" id="email" name="email" required value="<?php  echo htmlspecialchars($update ? $n['email'] : '', ENT_QUOTES, 'UTF-8'); ?>">
    </div>
</div>

<!-- Text Box Password -->
<?php if (!$update): ?>
    <div class="row mb-3">
        <label for="" class="col-sm-3 col-form-label">Password:</label>
        <div class="col-sm-9">
            <input type="text" class="form-control" placeholder="Password" aria-label="Password" name="password"  required value="">
        </div>
    </div>
<?php endif; ?>

<!-- Button -->

<div class="row mb-3">
    <label class="col-sm-5 col-form-label"></label>
    <div class="col-sm-3">
    <?php if ($update == true): ?>
    <input type="submit" class="btn btn-primary" name="update" value="Update">
<?php else: ?>
    <input type="submit" class="btn btn-primary" name="submit" value="Submit">
<?php endif ?>   
    </div>
</div>

   