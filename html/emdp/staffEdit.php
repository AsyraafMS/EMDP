<?php
include_once('includes/auth.php');
include_once('includes/config.php');
if($_SESSION['acctype'] == "Pharmacist"){
    header('Location: accessdenied.php');
    exit();
}
  
//Initialize
$name = $username = $type = $email  = "";
$update = false;
#Edit
if (isset($_GET['edit'])) {
    $id = $_GET['edit'];
    $update = true;
    $record = mysqli_query($connection, "SELECT * FROM users WHERE userID=$id");

    $n = mysqli_fetch_array($record);
}

    #Update
	if (isset($_POST['update'])) {

        $id = $_POST['userID'];
        
        $name = $_POST['name'];
        $username = $_POST['username'];
        $type = $_POST['type'];
        $email = $_POST['email'];

        // Sanitize and validate input as needed here

        $query = "UPDATE users SET 
            name = '$name',
            username = '$username',
            type = '$type',
            email = '$email'
            WHERE userID = $id";

        $result = mysqli_query($connection, $query);

        if ($result && mysqli_affected_rows($connection) > 0) {
            $_SESSION['message'] = "Item row successfully updated!";
        } else {
            $_SESSION['message'] = "Update failed or no changes made. $query";
        }

        header('location: staffView.php');
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
                                <li class="breadcrumb-item"><a href="#">Users</a></li>
                                <li class="breadcrumb-item active" aria-current="page">User Account</li>
                            </ol>
                        </nav>
                    </div>
                    <!-- /BREADCRUMB --> 


					<!-- here-->
                    <div class="row layout-top-spacing">
                        <div class="col-lg-12 col-12  layout-spacing">
                            <div class="statbox widget box box-shadow">
                                <div class="widget-header">   
                                    <div class="row">
                                        <div class="col-xl-12 col-md-12 col-sm-12 col-12">
                                            <h4>Edit Account Information</h4>
                                        </div>
                                    </div>
                                </div>

                                <div class="widget-content widget-content-area">

                                <?php if (isset($_SESSION['message'])): ?>
                                <div class="alert alert-light-danger alert-dismissible fade show border-0 mb-4" role="alert">
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-bs-dismiss="alert"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button> 
                                <?php
                                echo $_SESSION['message'];
                                unset($_SESSION['message']);
                                ?>
                                </div>
                    <?php endif; ?>


                                <form action="staffEdit.php" method="post">
                                    <?php  include_once('forms/usersForm.php'); ?>
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
<script src="../src/plugins/src/vanillaSelectBox/selectboxUsers.js"></script>
            <script>
                selectBox = new vanillaSelectBox("#jenisakaun", {
                    "keepInlineStyles":true,
                    "maxHeight": 200,
                    "minWidth":325,
                    "search": true,
                    "placeHolder": "Sila Pilih" 
                });
                $(document).ready(function(){
                $("#email").inputmask(
                    {
                        mask:"*{1,20}[.*{1,20}][.*{1,20}][.*{1,20}]@*{1,20}[.*{2,6}][.*{1,2}]",
                        greedy:!1,onBeforePaste:function(m,a){return(m=m.toLowerCase()).replace("mailto:","")},
                        definitions:{"*":
                            {
                                validator:"[0-9A-Za-z!#$%&'*+/=?^_`{|}~-]",
                                cardinality:1,
                                casing:"lower"
                            }
                        }
                    }
                )
            });
            </script>
			<!-- END PAGE LEVEL SCRIPTS -->