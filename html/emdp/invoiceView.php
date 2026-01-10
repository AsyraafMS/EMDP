<?php
include_once('includes/auth.php');
include_once('includes/config.php');

#Delete
if (isset($_GET['del'])) {
    $id = $_GET['del'];

    // Validate that ID is numeric
    if (!is_numeric($id)) {
        $_SESSION['message'] = "Invalid invoice ID.";
        header('Location: invoiceView.php');
        exit;
    }

    // Use prepared statement to prevent SQL injection
    $stmt = $connection->prepare("DELETE FROM invoices WHERE invoiceID = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Data successfully deleted!";
    } else {
        $_SESSION['message'] = "Failed to delete data: " . $stmt->error;
    }

    $stmt->close();
    header('Location: invoiceView.php');
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
                        <li class="breadcrumb-item"><a href="#">Payments</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Invoice</li>
                    </ol>
                </nav>
            </div>
            <!-- /BREADCRUMB --><br>
            <a href="invoiceAdd.php" class="btn btn-success">Add New Invoice</a>
            <!-- do here --><br>
            <br>
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
                            <table id="invoice-list" class="table style-3 dt-table-hover">
                                <thead>
                                    <tr>
                                        <th class="checkbox-column"> No. </th>
                                        <th>Invoice Id</th>
                                        <th>Created by</th>
                                        <th>Supplier</th>
                                        <th>Status</th>
                                        <th>Amount</th>
                                        <th>Date Created</th>
                                        <th>Due Date</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    //Read all row from database table
                                    $sql = "SELECT 
  invoices.*, 
  users.name AS name,
  suppliers.name AS supplier_name
FROM invoices
JOIN users ON invoices.userID = users.userID
LEFT JOIN suppliers ON invoices.supplierID = suppliers.supplierID;";
                                    $result = $connection->query($sql);

                                    if (!$result) {
                                        die("Invalid query: " . $connection->error);
                                    }
                                    //Num. of rows
                                    $i = 1;
                                    //Read data of each row
                                    while ($row = $result->fetch_assoc()) {

                                        if ($row['status'] == 'Paid') {
                                            $badge = 'light-success';
                                        } else  if ($row['status'] == 'Pending') {
                                            $badge = 'light-warning';
                                        } else{
                                            $badge = 'light-danger';
                                        }
                                        echo '<tr>
                                        <td class="text-center checkbox-column">' . $i . '</td>
                                        <td><a href="./invoicePreview.php?view=' . $row['invoiceID'] . '"><span class="inv-number"> #' . str_pad($row['invoiceID'], 5, '0', STR_PAD_LEFT) . '</span></a></td>
                                        <td>
                                            <div class="d-flex">
                                                <p class="align-self-center mb-0 ">' . $row['name'] . '</p>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex">
                                                <p class="align-self-center mb-0 ">' . $row['supplier_name'] . '</p>
                                            </div>
                                        </td>
                                        <td><span class="text-center  badge badge-' . $badge . ' inv-status">' . $row['status'] . '</span></td>
                                        <td><span class="text-center  inv-amount">RM' . $row['total_amount'] . '</span></td>
                                        <td><span class="inv-date">' . $row['invoice_date'] . '</span></td>
                                        <td><span class="inv-date">' . $row['invoice_dueDate'] . '</span></td>
                                        <td>
                                            <a href="./invoicePreview.php?view=' . $row['invoiceID'] . '" class="badge badge-light-primary text-start me-2 action-edit" href="javascript:void(0);">
<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-eye"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg></a>
                                            <a href="#" class="warning confirm badge badge-light-danger text-start action-delete" data-id="'.$row['invoiceID'].'"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-trash"><polyline points="3 6 5 6 21 6"></polyline><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"></path></svg></a>
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
    <?php include_once('includes/footer.php'); ?>

    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script src="../src/plugins/src/table/datatable/datatables.js"></script>
    <script src="../src/plugins/src/table/datatable/button-ext/dataTables.buttons.min.js"></script>
    <script src="../src/plugins/src/table/datatable/button-ext/jszip.min.js"></script>
    <script src="../src/plugins/src/table/datatable/button-ext/buttons.html5.min.js"></script>
    <script src="../src/plugins/src/table/datatable/button-ext/buttons.print.min.js"></script>
        <script src="../src/plugins/src/sweetalerts2/sweetalerts2.min.js"></script>
    <script src="../src/plugins/src/sweetalerts2/invoiceSweetAlert.js"></script>
    <script>
        c3 = $('#invoice-list').DataTable({
            "dom": "<'dt--top-section'<'row'<'col-sm-12 col-md-6 d-flex justify-content-md-start justify-content-center'B><'col-sm-12 col-md-6 d-flex justify-content-md-end justify-content-center mt-md-0 mt-3'f>>>" +
                "<'dt--top-section'<'col-4 col-sm-6 d-flex justify-content-sm-start justify-content-center'l>>" +
                "<'table-responsive'tr>" +
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
        });
        multiCheck(c3);
    </script>
    <!-- END PAGE LEVEL SCRIPTS -->