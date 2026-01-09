<?php
include_once('includes/auth.php');
include_once('includes/config.php');

   if (isset($_POST['submit'])) {
    $oldPassword = $_POST['oldPassword'];
    $newPassword = $_POST['newPassword'];
    $newPassword2 = $_POST['newPassword2'];

    $id = $_SESSION['id'];

    $sql = "SELECT password FROM users WHERE userID = '$id'";
    $result = $connection->query($sql);

    if ($result && $result->num_rows === 1) {
        $row = $result->fetch_assoc();
        $currentPass = $row['password'];

        if (password_verify($oldPassword, $currentPass)) {
           
            if ($newPassword === $newPassword2) {
                $newPasswordHash = password_hash($newPassword, PASSWORD_DEFAULT);

                $updateSql = "UPDATE users SET password='$newPasswordHash' WHERE userID = '$id'";
                if (mysqli_query($connection, $updateSql)) {
                    $_SESSION['message'] = "Password successfully changed!";
                } else {
                    $_SESSION['message2'] = "Failed to update password.";
                }
            } else {
                $_SESSION['message2'] = "New passwords do not match.";
            }
        } else {
            $_SESSION['message2'] = "Old password is incorrect.";
        }
    } else {
        $_SESSION['message2'] = "User not found.";
    }

    header('Location: changePassword.php');
    exit();
}

include_once('includes/header.php'); 
?>

<head>
	<!--  BEGIN CUSTOM STYLE FILE  -->
    <link rel="stylesheet" type="text/css" href="../src/plugins/src/vanillaSelectBox/vanillaSelectBox.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/css/light/vanillaSelectBox/custom-vanillaSelectBox.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/css/dark/vanillaSelectBox/custom-vanillaSelectBox.css">
    <link rel="stylesheet" type="text/css" href="../src/assets/css/light/elements/alert.css">
    <link rel="stylesheet" type="text/css" href="../src/assets/css/dark/elements/alert.css">
    <link href="../src/assets/css/light/scrollspyNav.css" rel="stylesheet" type="text/css" />
    <link href="../src/assets/css/dark/scrollspyNav.css" rel="stylesheet" type="text/css" />
    <!--  END CUSTOM STYLE FILE  -->	
</head>

<!--  BEGIN CONTENT AREA  -->
<div id="content" class="main-content">
            <div class="container">

                <div class="container">

                    <!-- BREADCRUMB -->
                    <div class="page-meta">
                        <nav class="breadcrumb-style-one" aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Profile</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Settings</li>
                            </ol>
                        </nav>
                    </div>
                    <!-- /BREADCRUMB --> 
                     <br>

                    <?php if (isset($_SESSION['message'])): ?>

                    <div class="alert alert-light-success alert-dismissible fade show border-0 mb-4" role="alert">
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"> <svg
                                xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                class="feather feather-x close" data-bs-dismiss="alert">
                                <line x1="18" y1="6" x2="6" y2="18"></line>
                                <line x1="6" y1="6" x2="18" y2="18"></line>
                            </svg></button>
                        <?php
                        echo $_SESSION['message'];
                        unset($_SESSION['message']);
                        ?>
                    </div>
                    <?php endif; ?>
                    <?php if (isset($_SESSION['message2'])): ?>

<div class="alert alert-light-danger alert-dismissible fade show border-0 mb-4" role="alert">
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"> <svg
            xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
            stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
            class="feather feather-x close" data-bs-dismiss="alert">
            <line x1="18" y1="6" x2="6" y2="18"></line>
            <line x1="6" y1="6" x2="18" y2="18"></line>
        </svg></button>
    <?php
    echo $_SESSION['message2'];
    unset($_SESSION['message2']);
    ?>
</div>
<?php endif; ?>



					<!-- here-->
                    <div class="row layout-top-spacing">
                        <div class="col-lg-12 col-12  layout-spacing">
                            <div class="statbox widget box box-shadow">
                                <div class="widget-header">   
                                    <div class="row">
                                        <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                            <h4>Change Password</h4>
                                        </div>
                                    </div>
                                </div>

                                <div class="widget-content widget-content-area">
                                <form class="" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                                    <?php  include_once('forms/passwordForm.php'); ?>
                                </form>
                                

                                </div>
                            </div>
                        </div>
                    </div> 
                    
                </div>
            </div>

			
			<?php include_once('includes/footer.php');?>

			<!-- BEGIN PAGE LEVEL SCRIPTS -->
			<script src="../src/assets/js/scrollspyNav.js"></script>
			<script src="../src/plugins/src/vanillaSelectBox/vanillaSelectBox.js"></script>
			<script src="../src/plugins/src/input-mask/jquery.inputmask.bundle.min.js"></script>
			<!-- END PAGE LEVEL SCRIPTS -->