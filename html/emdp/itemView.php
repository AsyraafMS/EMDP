<?php
include_once('includes/auth.php');
include_once('includes/config.php');

#Delete
if (isset($_GET['del'])) {
    $id = $_GET['del'];

    // Validate that ID is numeric
    if (!is_numeric($id)) {
        $_SESSION['message'] = "Invalid item ID.";
        header('Location: itemView.php');
        exit;
    }

    // Use prepared statement to prevent SQL injection
    $stmt = $connection->prepare("DELETE FROM items WHERE itemID = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Data successfully deleted!";
    } else {
        $_SESSION['message'] = "Failed to delete data: " . $stmt->error;
    }

    $stmt->close();
    header('Location: itemView.php');
    exit;
}
include_once('includes/header.php');
?>

<!-- BEGIN PAGE LEVEL CUSTOM STYLES -->
<link rel="stylesheet" type="text/css" href="../src/plugins/src/table/datatable/datatables.css">
<link rel="stylesheet" type="text/css" href="../src/plugins/css/light/table/datatable/dt-global_style.css">
<link rel="stylesheet" type="text/css" href="../src/plugins/css/light/table/datatable/custom_dt_custom.css">
<link rel="stylesheet" type="text/css" href="../src/plugins/css/dark/table/datatable/dt-global_style.css">
<link rel="stylesheet" type="text/css" href="../src/plugins/css/dark/table/datatable/custom_dt_custom.css">

<link rel="stylesheet" type="text/css" href="../src/plugins/css/light/table/datatable/custom_dt_miscellaneous.css">
<link rel="stylesheet" type="text/css" href="../src/plugins/css/dark/table/datatable/custom_dt_miscellaneous.css">

<link rel="stylesheet" type="text/css" href="../src/assets/css/light/elements/alert.css">
<link rel="stylesheet" type="text/css" href="../src/assets/css/dark/elements/alert.css">

<link href="../src/assets/css/light/scrollspyNav.css" rel="stylesheet" type="text/css" />
<link href="../src/assets/css/dark/scrollspyNav.css" rel="stylesheet" type="text/css" />

<link rel="stylesheet" href="../src/plugins/src/sweetalerts2/sweetalerts2.css">
<link href="../src/assets/css/dark/scrollspyNav.css" rel="stylesheet" type="text/css" />
<link href="../src/plugins/css/dark/sweetalerts2/custom-sweetalert.css" rel="stylesheet" type="text/css" />


<!-- END PAGE LEVEL CUSTOM STYLES -->
        <!--  BEGIN CONTENT AREA  -->
        <div id="content" class="main-content">
            <div class="layout-px-spacing">

                <div class="middle-content container-xxl p-0">

                    <!-- BREADCRUMB -->
                    <div class="page-meta">
                        <nav class="breadcrumb-style-one" aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Inventory</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Item</li>
                            </ol>
                        </nav>
                    </div>

                    <!-- /BREADCRUMB --><br>
                    <a href="itemAdd.php" class="btn btn-success">Add New Item</a>

                    <br> <br>
                    
                    <?php if (isset($_SESSION['message'])): ?>
                                <div class="alert alert-light-success alert-dismissible fade show border-0 mb-4" role="alert">
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"> <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-bs-dismiss="alert"><line x1="18" y1="6" x2="6" y2="18"></line><line x1="6" y1="6" x2="18" y2="18"></line></svg></button> 
                                <?php
                                echo $_SESSION['message'];
                                unset($_SESSION['message']);
                                ?>
                                </div>
                    <?php endif; ?>
                   

                    <div class="row layout-spacing">
                        <div class="col-lg-12">
                            <div class="statbox widget box box-shadow">
                                <div class="widget-content widget-content-area">
                                    <table id="tableahli" class="table style-3 dt-table-hover">
                                        <thead>
                                            
                                            <tr>
                                                <th class="checkbox-column text-center"> No. </th>
                                                <th>Supplier Name</th>
                                                <th>Item Name</th>
                                                <th >Item Type</th>
                                                <!--<th>Category</th>-->
                                                <th>Price (RM)</th>
                                                <th>Quantity</th>
                                                <th>Manufactured Date</th>
                                                <th>Expiry Date</th>
                                                
                                                
                                                <!--<th>Description</th>-->
                                                <th class="text-center">Status</th>
                                                <th class="text-center">Tracker</th>
                                                <th class="text-center dt-no-sorting">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        <?php
                                            //Read all row from database table
                                            $sql = "SELECT items.*, suppliers.name AS supplier_name 
FROM items
JOIN suppliers ON items.supplierID = suppliers.supplierID";
                                            $result = $connection->query($sql);

                                            if(!$result){
                                                die("Invalid query: " . $connection->error);
                                            }
                                            //Num. of rows
                                            $i = 1;
                                           
                                            //Read data of each row
                                            while($row = $result->fetch_assoc()){
                                                if(strcasecmp('In Stock', $row['status']) == 0){
                                                    $badge = 'light-success';
                                                } elseif(strcasecmp('Low Stock', $row['status']) == 0){
                                                    $badge = 'light-warning';
                                                } else{
                                                    $badge = 'light-danger';
                                                }
                                                if (empty($row['expired_date'])) {
                                                    $badge2 = '';
                                                    $tracker = null;
                                                } elseif (strtotime($row['expired_date']) < strtotime(date('Y-m-d'))) {
                                                    $badge2 = 'light-danger';
                                                    $tracker = "Expired";
                                                } elseif (strtotime($row['expired_date']) < strtotime('+10 days')) {
                                                    $badge2 = 'light-warning';
                                                    $tracker = "Expiring Soon";
                                                } else {
                                                    $badge2 = 'light-success';
                                                    $tracker = "Expires Later";
                                                }

                                                echo '<tr>
                                                <td class="checkbox-column text-center"> '. $i .' </td>
                                                <td>' . $row['supplier_name'] . '</td>
                                                <td>' . $row['name'] . '</td>
                                                <td >' . $row['type'] . '</td>
                                                <!--<td>' . $row['category'] . '</td>-->
                                                <td>' . $row['price'] . '</td>
                                                <td>' . $row['quantity'] . '</td>
                                                <td>' . $row['manufactured_date'] . '</td>
                                                <td>' . $row['expired_date'] . '</td>
                                                <td class="text-center"><span class="shadow-none badge badge-'.$badge.'">' . $row['status'] . '</span></td>
                                                <td class="text-center"><span class="shadow-none badge badge-' . $badge2 . '">' . $tracker . '</span></td>
                                                <td class="text-center">
                                                    <ul class="table-controls">
                                                        <li><a href="itemEdit.php?edit='.$row['itemID'].'" class="bs-tooltip" data-bs-toggle="tooltip" data-bs-placement="top" title="Edit" data-original-title="Edit">
                                                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                                        stroke-linejoin="round" class="feather feather-edit-2 p-1 br-8 mb-1"><path d="M17 3a2.828 2.828 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5L17 3z"></path></svg></a></li>
                                                                                                            
                                                        <li>
                                                        <a href="#" class="warning confirm bs-tooltip" data-id="'.$row['itemID'].'" data-bs-toggle="tooltip" data-bs-placement="top" title="Delete" data-original-title="Delete">
                                                                
                                                        <svg  xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash p-1 br-8 mb-1">
                                                                    <polyline 
                                                                        points="3 6 5 6 21 6"> 
                                                                    </polyline>
                                                                    <path 
                                                                        d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2">
                                                                    </path>
                                                                </svg>
                                                            </a>
                                                            
                                                        </li>
                                                        </ul>
                                                </td>
                                            </tr>'; 
                                            $i++;
                                            }
                                            ?>
                                        </tbody>
                                        
                                    </table>
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php include_once('includes/footer.php');?>

    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script src="../src/plugins/src/table/datatable/datatables.js"></script>
    <script src="../src/plugins/src/table/datatable/button-ext/dataTables.buttons.min.js"></script>
    <script src="../src/plugins/src/table/datatable/button-ext/jszip.min.js"></script>    
    <script src="../src/plugins/src/table/datatable/button-ext/buttons.html5.min.js"></script>
    <script src="../src/plugins/src/table/datatable/button-ext/buttons.print.min.js"></script>

    <script src="../src/plugins/src/sweetalerts2/sweetalerts2.min.js"></script>
    <script src="../src/plugins/src/sweetalerts2/itemSweetAlert.js"></script>
    
    <script>

        c3 = $('#tableahli').DataTable({
            "dom": "<'dt--top-section'<'row'<'col-sm-12 col-md-6 d-flex justify-content-md-start justify-content-center'B><'col-sm-12 col-md-6 d-flex justify-content-md-end justify-content-center mt-md-0 mt-3'f>>>" +
     "<'dt--top-section'<'col-4 col-sm-6 d-flex justify-content-sm-start justify-content-center'l>>" +
        "<'table-responsive'tr>"+
    "<'dt--bottom-section d-sm-flex justify-content-sm-between text-center'<'dt--pages-count  mb-sm-0 mb-3'i><'dt--pagination'p>>",
        buttons: {
            buttons: [
                { extend: 'copy', className: 'btn' },
                { extend: 'csv', className: 'btn' },
                { extend: 'excel', className: 'btn' },
                { extend: 'print', className: 'btn' }
            ]
        },
        "oLanguage": {
            "oPaginate": { "sPrevious": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-left"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>', "sNext": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-arrow-right"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>' },
            "sInfo": "Showing page _PAGE_ of _PAGES_",
            "sSearch": '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-search"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>',
            "sSearchPlaceholder": "Search...",
           "sLengthMenu": "Results :  _MENU_",
        },
        "stripeClasses": [],
        "lengthMenu": [5, 10, 20, 50, 100],
        "pageLength": 10 
    } );
        multiCheck(c3);
    </script>
    <!-- END PAGE LEVEL SCRIPTS -->  
