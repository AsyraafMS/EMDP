<?php
include_once('includes/auth.php');
include_once('includes/config.php');

$productID = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$product = null;
$mediaItems = [];
$pageMessage = '';
$feedback = null;
$feedbackType = 'success';
$canEdit = isset($_SESSION['acctype']) && in_array($_SESSION['acctype'], ['Superuser', 'Admin'], true);

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_product'])) {
    $deleteID = isset($_POST['productID']) ? (int) $_POST['productID'] : 0;
    if (!$canEdit) {
        $pageMessage = 'You do not have permission to delete products.';
    } elseif ($deleteID <= 0) {
        $pageMessage = 'Invalid product selected.';
    } else {
        $mediaPaths = [];
        $mediaResult = mysqli_query($connection, "SELECT file_path FROM product_media WHERE productID = {$deleteID}");
        if ($mediaResult) {
            while ($row = mysqli_fetch_assoc($mediaResult)) {
                $mediaPaths[] = $row['file_path'];
            }
        }

        $deleteResult = mysqli_query($connection, "DELETE FROM products WHERE productID = {$deleteID}");
        if ($deleteResult) {
            foreach ($mediaPaths as $path) {
                if (is_string($path) && strpos($path, 'media/') === 0) {
                    $fullPath = __DIR__ . DIRECTORY_SEPARATOR . $path;
                    if (is_file($fullPath)) {
                        unlink($fullPath);
                    }
                }
            }
            header('Location: products.php');
            exit;
        }

        $feedback = 'Failed to delete product.';
        $feedbackType = 'danger';
    }
}

if ($productID <= 0) {
    $pageMessage = 'No product selected.';
} else {
    $productQuery = "SELECT p.productID,
            p.description,
            i.name AS item_name,
            i.category,
            i.quantity,
            s.name AS supplier_name
        FROM products p
        INNER JOIN items i ON p.itemID = i.itemID
        LEFT JOIN suppliers s ON i.supplierID = s.supplierID
        WHERE p.productID = {$productID}
        LIMIT 1";
    $productResult = mysqli_query($connection, $productQuery);
    if ($productResult && mysqli_num_rows($productResult) > 0) {
        $product = mysqli_fetch_assoc($productResult);
        $mediaQuery = "SELECT media_type, file_path
            FROM product_media
            WHERE productID = {$productID}
            ORDER BY is_primary DESC, sort_order ASC, mediaID ASC";
        $mediaResult = mysqli_query($connection, $mediaQuery);
        if ($mediaResult) {
            while ($row = mysqli_fetch_assoc($mediaResult)) {
                $mediaItems[] = $row;
            }
        }
    } else {
        $pageMessage = 'Product not found.';
    }
}

include_once('includes/header.php');
?>
<head>
    <!--  BEGIN CUSTOM STYLE FILE  -->
    <link rel="stylesheet" type="text/css" href="../src/plugins/src/bootstrap-touchspin/jquery.bootstrap-touchspin.min.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/src/glightbox/glightbox.min.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/src/splide/splide.min.css">

    <link rel="stylesheet" type="text/css" href="../src/assets/css/light/elements/alert.css">
    <link rel="stylesheet" type="text/css" href="../src/assets/css/dark/elements/alert.css">
    <link rel="stylesheet" type="text/css" href="../src/assets/css/light/components/tabs.css">
    <link rel="stylesheet" type="text/css" href="../src/assets/css/light/apps/ecommerce-details.css">
    
    <link rel="stylesheet" type="text/css" href="../src/assets/css/dark/components/tabs.css">
    <link rel="stylesheet" type="text/css" href="../src/assets/css/dark/apps/ecommerce-details.css">
    <!--  END CUSTOM STYLE FILE  -->
</head>
<body class="">

        <!--  BEGIN CONTENT AREA  -->
        <div id="content" class="main-content">

            <div class="layout-px-spacing">

                <div class="middle-content container-xxl p-0">
    
                    <!-- BREADCRUMB -->
                    <div class="page-meta">
                        <nav class="breadcrumb-style-one" aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="#">Products</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Details</li>
                            </ol>
                        </nav>
                    </div>
                    <!-- /BREADCRUMB -->
    
                    <div class="row layout-top-spacing">

                        <?php if ($pageMessage !== ''): ?>
                            <div class="col-12">
                                <div class="alert alert-light-danger border-0 mb-4" role="alert">
                                    <?php echo htmlspecialchars($pageMessage); ?>
                                </div>
                            </div>
                        <?php elseif ($feedback !== null): ?>
                            <div class="col-12">
                                <div class="alert alert-light-<?php echo htmlspecialchars($feedbackType); ?> border-0 mb-4" role="alert">
                                    <?php echo htmlspecialchars($feedback); ?>
                                </div>
                            </div>
                        <?php else: ?>

                        <div class="col-xxl-12 col-xl-12 col-lg-12 col-md-12 col-sm-12 mb-4">

                            <div class="widget-content widget-content-area br-8">

                                <div class="row justify-content-center">
                                    <div class="col-xxl-5 col-xl-6 col-lg-7 col-md-7 col-sm-9 col-12 pe-3">
                                        <!-- Swiper -->
                                        <div id="main-slider" class="splide">
                                            <div class="splide__track">
                                                    <ul class="splide__list">
                                                        <?php if (empty($mediaItems)): ?>
                                                            <li class="splide__slide">
                                                                <a href="../src/assets/img/fallback.png" class="glightbox">
                                                                    <img alt="<?php echo htmlspecialchars($product['item_name']); ?>" src="../src/assets/img/fallback.png">
                                                                </a>
                                                            </li>
                                                        <?php else: ?>
                                                            <?php foreach ($mediaItems as $media): ?>
                                                                <?php
                                                                    $mediaPath = htmlspecialchars($media['file_path']);
                                                                    $mediaType = $media['media_type'];
                                                                ?>
                                                                <?php if ($mediaType === 'image'): ?>
                                                                    <li class="splide__slide">
                                                                        <a href="<?php echo $mediaPath; ?>" class="glightbox">
                                                                            <img alt="<?php echo htmlspecialchars($product['item_name']); ?>" src="<?php echo $mediaPath; ?>">
                                                                        </a>
                                                                    </li>
                                                                <?php else: ?>
                                                                    <li class="splide__slide">
                                                                        <video controls preload="metadata" style="width: 100%; height: 100%; object-fit: cover; border-radius: 12px;">
                                                                            <source src="<?php echo $mediaPath; ?>">
                                                                        </video>
                                                                    </li>
                                                                <?php endif; ?>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </ul>
                                            </div>
                                            </div>

                                            <div id="thumbnail-slider" class="splide">
                                            <div class="splide__track">
                                                    <ul class="splide__list">
                                                        <?php if (empty($mediaItems)): ?>
                                                            <li class="splide__slide"><img alt="<?php echo htmlspecialchars($product['item_name']); ?>" src="../src/assets/img/fallback.png"></li>
                                                        <?php else: ?>
                                                            <?php foreach ($mediaItems as $media): ?>
                                                                <?php
                                                                    $mediaPath = htmlspecialchars($media['file_path']);
                                                                    $mediaType = $media['media_type'];
                                                                ?>
                                                                <?php if ($mediaType === 'image'): ?>
                                                                    <li class="splide__slide"><img alt="<?php echo htmlspecialchars($product['item_name']); ?>" src="<?php echo $mediaPath; ?>"></li>
                                                                <?php else: ?>
                                                                    <li class="splide__slide">
                                                                        <video muted playsinline preload="metadata" style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;">
                                                                            <source src="<?php echo $mediaPath; ?>">
                                                                        </video>
                                                                    </li>
                                                                <?php endif; ?>
                                                            <?php endforeach; ?>
                                                        <?php endif; ?>
                                                    </ul>
                                            </div>
                                            </div>

                                    </div>

                                    <div class="col-xxl-4 col-xl-5 col-lg-12 col-md-12 col-12 mt-xl-0 mt-5 align-self-center">

                                        <div class="product-details-content">
                                            
                                            <h3 class="product-title mb-3"><?php echo htmlspecialchars($product['item_name']); ?></h3>

                                            

                                            <div class="mb-3">
                                                <div class="text-muted">Supplier</div>
                                                <div><?php echo htmlspecialchars($product['supplier_name'] ?? 'Not set'); ?></div>
                                            </div>

                                            <div class="mb-3">
                                                <div class="text-muted">Category</div>
                                                <div><?php echo htmlspecialchars($product['category'] ?? 'Not set'); ?></div>
                                            </div>

                                            <div class="mb-3">
                                                <div class="text-muted">Current Stock</div>
                                                <div><?php echo htmlspecialchars((string) ($product['quantity'] ?? '0')); ?></div>
                                            </div>

                                            <hr class="mb-4">

                                            <div>
                                                <div class="text-muted mb-2">Description</div>
                                                <div class="product-description">
                                                    <?php
                                                    $descriptionHtml = trim($product['description'] ?? '');
                                                    if ($descriptionHtml !== '') {
                                                        echo $descriptionHtml;
                                                    } else {
                                                        echo '<span class="text-muted">No description.</span>';
                                                    }
                                                    ?>
                                                </div>
                                            </div>
<hr class="mb-4">
                                            <?php if ($canEdit): ?>
                                                <div class="d-flex gap-2 mb-3">
                                                    <a class="btn btn-warning" href="productsEdit.php?id=<?php echo (int) $product['productID']; ?>">Edit Product</a>
                                                    <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>?id=<?php echo (int) $product['productID']; ?>" onsubmit="return confirm('Delete this product listing?');">
                                                        <input type="hidden" name="productID" value="<?php echo (int) $product['productID']; ?>">
                                                        <button type="submit" name="delete_product" class="btn btn-danger">Delete Product</button>
                                                    </form>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                        
                                    </div>
                                </div>

                            </div>

                        </div>

                        <?php endif; ?>

                    </div>

                </div>

            </div>

            <?php include_once('includes/footer.php');?>
    
    <!-- BEGIN GLOBAL MANDATORY STYLES -->
    <script src="../src/plugins/src/global/vendors.min.js"></script>
    <script src="../src/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../src/plugins/src/perfect-scrollbar/perfect-scrollbar.min.js"></script>
    <script src="../src/plugins/src/mousetrap/mousetrap.min.js"></script>
    <script src="../layouts/collapsible-menu/app.js"></script>
    <!-- END GLOBAL MANDATORY STYLES -->

    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script src="../src/plugins/src/bootstrap-touchspin/jquery.bootstrap-touchspin.min.js"></script>
    <script src="../src/plugins/src/glightbox/glightbox.min.js"></script>
    <script src="../src/plugins/src/splide/splide.min.js"></script>
    <script src="../src/assets/js/apps/ecommerce-details.js"></script>    
    <!-- END PAGE LEVEL SCRIPTS -->    
</body>
</html>
