<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, shrink-to-fit=no">
    <title>E-MDP</title>
    <link rel="icon" type="image/x-icon" href="../src/assets/img/BIRUNI.svg"/>
    <link href="../layouts/collapsible-menu/css/light/loader.css" rel="stylesheet" type="text/css" />
    <link href="../layouts/collapsible-menu/css/dark/loader.css" rel="stylesheet" type="text/css" />
    <script src="../layouts/collapsible-menu/loader.js"></script>
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <link href="https://fonts.googleapis.com/css?family=Nunito:400,600,700" rel="stylesheet">
    <link href="../src/bootstrap/css/bootstrap.min.css" rel="stylesheet" type="text/css" />

    <link href="../layouts/collapsible-menu/css/light/plugins.css" rel="stylesheet" type="text/css" />
    <link href="../src/assets/css/light/authentication/auth-cover.css" rel="stylesheet" type="text/css" />
    
    <link href="../layouts/collapsible-menu/css/dark/plugins.css" rel="stylesheet" type="text/css" />
    <link href="../src/assets/css/dark/authentication/auth-cover.css" rel="stylesheet" type="text/css" />
    <!-- END GLOBAL MANDATORY STYLES -->
    <!--  BEGIN CUSTOM STYLE FILE  -->
    <link rel="stylesheet" type="text/css" href="../src/assets/css/light/elements/alert.css">
    <link rel="stylesheet" type="text/css" href="../src/assets/css/dark/elements/alert.css">
    <!--  END CUSTOM STYLE FILE  -->
    
</head>
<body class="form">

    <!-- BEGIN LOADER -->
    <div id="load_screen"> <div class="loader"> <div class="loader-content">
        <div class="spinner-grow align-self-center"></div>
    </div></div></div>
    <!--  END LOADER -->

    <div class="auth-container d-flex">

        <div class="container mx-auto align-self-center">
    
            <div class="row">
    
                <div class="col-6 d-lg-flex d-none h-100 my-auto top-0 start-0 text-center justify-content-center flex-column">
                    <div class="auth-cover-bg-image"></div>
                    <div class="auth-overlay"></div>
                    <div class="auth-cover">
                        <div class="position-relative">
                            <img src="../src/assets/img/login2.png" alt="auth-img">
                        </div>
                    </div>
                </div>

                <div class="col-xxl-4 col-xl-5 col-lg-5 col-md-8 col-12 d-flex flex-column align-self-center ms-lg-auto me-lg-0 mx-auto">
                    <div class="card">
                        <div class="card-body">
    
                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    
                                    <h2>Log In</h2>
                                    <p>Enter your username and password to use E-MDP.</p>
                                    
                                </div>
                                    <form action="includes/authenticate.php" method="post">
                                        <div class="col-md-12">
                                            <div class="mb-3">
                                                <label for="username" class="form-label">Username</label>
                                                <input type="text" name="username" class="form-control" id="username" required>
                                            </div>
                                        </div>
                                        <div class="col-12">
                                            <div class="mb-4">
                                                <label for="password" class="form-label">Password</label>
                                                <input type="password" name="password" class="form-control" id="password" required>
                                            </div>
                                        </div>
                                    
                               
                                
                                <?php if (isset($_SESSION['login_failure'])): ?>
                                 <div class="alert alert-light-danger alert-dismissible fade show border-0 mb-4" role="alert">
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-bs-dismiss="alert"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button> 
                                <?php
                                echo $_SESSION['login_failure'];
                                unset($_SESSION['login_failure']);
                                ?>
                                </div>
                                <?php endif; ?>

                                <div class="col-12">
                                    <div class="mb-4">
                                        <button type="submit" value="Login" class="btn btn-secondary w-100">Log In</button>
                                    </div>
                                </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- BEGIN GLOBAL MANDATORY SCRIPTS -->
    <script src="../src/bootstrap/js/bootstrap.bundle.min.js"></script>
    <!-- END GLOBAL MANDATORY SCRIPTS -->


</body>
</html>