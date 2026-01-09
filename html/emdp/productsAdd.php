<?php
include_once('includes/auth.php');
include_once('includes/config.php');

$feedback = null;
$feedbackType = 'success';
$selectedItemID = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selectedItemID = isset($_POST['itemID']) ? (int) $_POST['itemID'] : 0;
    $description = trim($_POST['description'] ?? '');
    $isPublic = (isset($_POST['is_public']) && $_POST['is_public'] === '1') ? 1 : 0;

    if ($selectedItemID <= 0) {
        $feedback = 'Please select an item.';
        $feedbackType = 'danger';
    } else {
        $checkItem = $connection->prepare('SELECT itemID FROM items WHERE itemID = ?');
        if ($checkItem) {
            $checkItem->bind_param('i', $selectedItemID);
            $checkItem->execute();
            $checkItem->store_result();
            $itemExists = $checkItem->num_rows > 0;
            $checkItem->close();
        } else {
            $itemExists = false;
        }

        if (!$itemExists) {
            $feedback = 'Selected item was not found.';
            $feedbackType = 'danger';
        } else {
            $checkProduct = $connection->prepare('SELECT productID FROM products WHERE itemID = ?');
            if ($checkProduct) {
                $checkProduct->bind_param('i', $selectedItemID);
                $checkProduct->execute();
                $checkProduct->store_result();
                $productExists = $checkProduct->num_rows > 0;
                $checkProduct->close();
            } else {
                $productExists = false;
            }

            if ($productExists) {
                $feedback = 'This item already has a product listing.';
                $feedbackType = 'danger';
            } else {
                $insertProduct = $connection->prepare('INSERT INTO products (itemID, description, is_public, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())');
                if ($insertProduct) {
                    $descriptionValue = $description !== '' ? $description : null;
                    $insertProduct->bind_param('isi', $selectedItemID, $descriptionValue, $isPublic);
                    $insertOk = $insertProduct->execute();
                    $productID = $insertOk ? $insertProduct->insert_id : 0;
                    $insertProduct->close();
                } else {
                    $insertOk = false;
                    $productID = 0;
                }

                if ($insertOk && $productID > 0) {
                    $uploadIssues = [];
                    $mediaCount = 0;

                    if (!empty($_FILES['media']) && is_array($_FILES['media']['name'])) {
                        $mediaDir = __DIR__ . DIRECTORY_SEPARATOR . 'media';
                        if (!is_dir($mediaDir)) {
                            mkdir($mediaDir, 0755, true);
                        }

                        $maxSizeBytes = 50 * 1024 * 1024;
                        $allowedImageExt = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                        $allowedVideoExt = ['mp4', 'webm', 'ogg', 'mov'];
                        $finfo = class_exists('finfo') ? new finfo(FILEINFO_MIME_TYPE) : null;

                        $mediaInsert = $connection->prepare('INSERT INTO product_media (productID, media_type, file_path, sort_order, is_primary, created_at) VALUES (?, ?, ?, ?, ?, NOW())');

                        $uploadErrorMessages = [
                            UPLOAD_ERR_INI_SIZE => 'exceeds the upload_max_filesize limit.',
                            UPLOAD_ERR_FORM_SIZE => 'exceeds the MAX_FILE_SIZE limit.',
                            UPLOAD_ERR_PARTIAL => 'was only partially uploaded.',
                            UPLOAD_ERR_NO_FILE => 'was not uploaded.',
                            UPLOAD_ERR_NO_TMP_DIR => 'is missing a temporary folder.',
                            UPLOAD_ERR_CANT_WRITE => 'failed to write to disk.',
                            UPLOAD_ERR_EXTENSION => 'was blocked by a PHP extension.'
                        ];

                        $fileCount = count($_FILES['media']['name']);
                        for ($i = 0; $i < $fileCount; $i++) {
                            if ($_FILES['media']['error'][$i] !== UPLOAD_ERR_OK) {
                                $errorCode = $_FILES['media']['error'][$i];
                                $errorText = $uploadErrorMessages[$errorCode] ?? 'failed to upload.';
                                $uploadIssues[] = $_FILES['media']['name'][$i] . ' ' . $errorText;
                                continue;
                            }

                            if ($_FILES['media']['size'][$i] > $maxSizeBytes) {
                                $uploadIssues[] = $_FILES['media']['name'][$i] . ' exceeds 50MB.';
                                continue;
                            }

                            $originalName = $_FILES['media']['name'][$i];
                            $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
                            $tmpName = $_FILES['media']['tmp_name'][$i];
                            $mimeType = $finfo ? $finfo->file($tmpName) : (function_exists('mime_content_type') ? mime_content_type($tmpName) : '');

                            $mediaType = null;
                            if (is_string($mimeType) && strpos($mimeType, 'image/') === 0) {
                                $mediaType = 'image';
                            } elseif (is_string($mimeType) && strpos($mimeType, 'video/') === 0) {
                                $mediaType = 'video';
                            } elseif (in_array($extension, $allowedImageExt, true)) {
                                $mediaType = 'image';
                            } elseif (in_array($extension, $allowedVideoExt, true)) {
                                $mediaType = 'video';
                            }

                            if ($mediaType === null) {
                                $uploadIssues[] = $originalName . ' has an unsupported file type.';
                                continue;
                            }

                            $safeName = uniqid('media_', true) . '.' . $extension;
                            $targetPath = $mediaDir . DIRECTORY_SEPARATOR . $safeName;
                            if (!move_uploaded_file($tmpName, $targetPath)) {
                                $uploadIssues[] = 'Failed to upload ' . $originalName . '.';
                                continue;
                            }

                            if ($mediaInsert) {
                                $relativePath = 'media/' . $safeName;
                                $sortOrder = $mediaCount + 1;
                                $isPrimary = $mediaCount === 0 ? 1 : 0;
                                $mediaInsert->bind_param('issii', $productID, $mediaType, $relativePath, $sortOrder, $isPrimary);
                                $mediaInsert->execute();
                            }

                            $mediaCount++;
                        }

                        if ($mediaInsert) {
                            $mediaInsert->close();
                        }
                    }

                    if (!empty($uploadIssues)) {
                        $feedback = 'Product created, but some uploads failed: ' . implode(' ', $uploadIssues);
                        $feedbackType = 'danger';
                    } elseif (empty($_FILES['media']) || !isset($_FILES['media']['name'])) {
                        $feedback = 'Product created, but no files were received by the server.';
                        $feedbackType = 'danger';
                    } elseif (!empty($_FILES['media']) && is_array($_FILES['media']['name']) && $mediaCount === 0) {
                        $feedback = 'Product created, but no files were uploaded.';
                        $feedbackType = 'danger';
                    } else {
                        $feedback = 'Product created successfully.';
                        $feedbackType = 'success';
                        $selectedItemID = 0;
                    }
                } else {
                    $feedback = 'Failed to create product.';
                    $feedbackType = 'danger';
                }
            }
        }
    }
}

$items = [];
$itemQuery = "SELECT i.itemID, i.name AS item_name, i.category, i.quantity, s.name AS supplier_name, s.phone_num, s.email
    FROM items i
    LEFT JOIN suppliers s ON i.supplierID = s.supplierID
    ORDER BY i.name ASC";
$itemResult = mysqli_query($connection, $itemQuery);
if ($itemResult) {
    while ($row = mysqli_fetch_assoc($itemResult)) {
        $items[] = $row;
    }
}

include_once('includes/header.php');
?>
<head>
    <!-- BEGIN PAGE LEVEL PLUGINS/CUSTOM STYLES -->
    <link rel="stylesheet" href="../src/plugins/src/filepond/filepond.min.css">
    <link rel="stylesheet" href="../src/plugins/src/filepond/FilePondPluginImagePreview.min.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/src/tagify/tagify.css">
    
    <link rel="stylesheet" type="text/css" href="../src/assets/css/light/forms/switches.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/css/light/editors/quill/quill.snow.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/css/light/tagify/custom-tagify.css">
    <link href="../src/plugins/css/light/filepond/custom-filepond.css" rel="stylesheet" type="text/css" />
    
    <link rel="stylesheet" type="text/css" href="../src/assets/css/dark/forms/switches.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/css/dark/editors/quill/quill.snow.css">
    <link rel="stylesheet" type="text/css" href="../src/plugins/css/dark/tagify/custom-tagify.css">
    <link href="../src/plugins/css/dark/filepond/custom-filepond.css" rel="stylesheet" type="text/css" />
    <!-- END PAGE LEVEL PLUGINS/CUSTOM STYLES -->
    
    <!--  BEGIN CUSTOM STYLE FILE  -->
    <link rel="stylesheet" type="text/css" href="../src/assets/css/light/elements/alert.css">
    <link rel="stylesheet" type="text/css" href="../src/assets/css/dark/elements/alert.css">
    <link rel="stylesheet" href="../src/assets/css/light/apps/ecommerce-create.css">
    <link rel="stylesheet" href="../src/assets/css/dark/apps/ecommerce-create.css">
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
                                <li class="breadcrumb-item active" aria-current="page">Create</li>
                            </ol>
                        </nav>
                    </div>
                    <!-- /BREADCRUMB -->

                    <?php if ($feedback !== null): ?>
                        <div class="alert alert-light-<?php echo htmlspecialchars($feedbackType); ?> alert-dismissible fade show border-0 mb-4" role="alert">
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-x close" data-bs-dismiss="alert">
                                    <line x1="18" y1="6" x2="6" y2="18"></line>
                                    <line x1="6" y1="6" x2="18" y2="18"></line>
                                </svg>
                            </button>
                            <?php echo htmlspecialchars($feedback); ?>
                        </div>
                    <?php endif; ?>

                    <form id="product-form" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
                    <div class="row mb-4 layout-spacing layout-top-spacing">

                        <div class="col-xxl-9 col-xl-12 col-lg-12 col-md-12 col-sm-12">

                            <div class="widget-content widget-content-area ecommerce-create-section">

                                <div class="row mb-4">
                                    <div class="col-sm-12">
                                        <label for="itemID">Product Name</label>
                                        <input type="text" class="form-control mb-2" id="item-search" placeholder="Search items...">
                                        <select class="form-select" id="itemID" name="itemID" required>
                                            <option value="">Choose...</option>
                                            <?php foreach ($items as $item): ?>
                                                <option value="<?php echo (int) $item['itemID']; ?>"
                                                    data-supplier-name="<?php echo htmlspecialchars($item['supplier_name'] ?? ''); ?>"
                                                    data-supplier-phone="<?php echo htmlspecialchars($item['phone_num'] ?? ''); ?>"
                                                    data-supplier-email="<?php echo htmlspecialchars($item['email'] ?? ''); ?>"
                                                    data-category="<?php echo htmlspecialchars($item['category'] ?? ''); ?>"
                                                    data-stock="<?php echo htmlspecialchars((string) ($item['quantity'] ?? '')); ?>"
                                                    <?php echo ($selectedItemID === (int) $item['itemID']) ? 'selected' : ''; ?>>
                                                    <?php echo htmlspecialchars($item['item_name']); ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>

                                <div class="row mb-4">
                                    <div class="col-sm-12">
                                        <label>Description</label>
                                        <div id="product-description"></div>
                                        <input type="hidden" name="description" id="description-input" value="">
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-8">
                                        <label for="product-images">Upload Images / Video</label>
                                        <div class="multiple-file-upload">
                                            <input type="file" 
                                                class="filepond file-upload-multiple"
                                                name="media[]"
                                                id="product-images" 
                                                multiple 
                                                accept="image/*,video/*"
                                                data-allow-reorder="true"
                                                data-instant-upload="false"
                                                data-store-as-file="true"
                                                data-max-file-size="50MB"
                                                data-max-files="10">
                                        </div>
                                    </div>

                                    <div class="col-md-4 text-center">
                                        <div class="switch form-switch-custom switch-inline form-switch-primary mt-4">
                                            <input type="hidden" name="is_public" value="0">
                                            <input class="switch-input" type="checkbox" role="switch" id="showPublicly" name="is_public" value="1" checked>
                                            
                                        </div>
                                    </div>
                                    
                                </div>

                            </div>
                            
                        </div>

                        <div class="col-xxl-3 col-xl-12 col-lg-12 col-md-12 col-sm-12">

                            <div class="row">
                                <div class="col-xxl-12 col-xl-8 col-lg-8 col-md-7 mt-xxl-0 mt-4">
                                    <div class="widget-content widget-content-area ecommerce-create-section">
                                        <div class="row">
                                            <div class="col-sm-12 mb-4">
                                                <label for="supplier-name">Supplier Name</label>
                                                <input readonly type="text" class="form-control" id="supplier-name" value="">
                                            </div>
                                            <div class="col-sm-12 mb-4">
                                                <label for="item-category">Category</label>
                                                <input readonly type="text" class="form-control" id="item-category" value="">
                                            </div>
                                            <div class="col-sm-12 mb-4">
                                                <label for="current-stock">Current Stock</label>
                                                <input readonly type="text" class="form-control" id="current-stock" value="">
                                            </div>
                                            <div class="col-sm-12">
                                                <button type="submit" class="btn btn-success w-100">Add Product</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    </form>
                </div>
            </div>
            <?php include_once('includes/footer.php');?>
    
    <!-- BEGIN PAGE LEVEL SCRIPTS -->
    <script src="../src/plugins/src/editors/quill/quill.js"></script>
    <script src="../src/plugins/src/filepond/filepond.min.js"></script>
    <script src="../src/plugins/src/filepond/FilePondPluginFileValidateType.min.js"></script>
    <script src="../src/plugins/src/filepond/FilePondPluginImageExifOrientation.min.js"></script>
    <script src="../src/plugins/src/filepond/FilePondPluginImagePreview.min.js"></script>
    <script src="../src/plugins/src/filepond/FilePondPluginImageCrop.min.js"></script>
    <script src="../src/plugins/src/filepond/FilePondPluginImageResize.min.js"></script>
    <script src="../src/plugins/src/filepond/FilePondPluginImageTransform.min.js"></script>
    <script src="../src/plugins/src/filepond/filepondPluginFileValidateSize.min.js"></script>

    <script src="../src/plugins/src/tagify/tagify.min.js"></script>

    <script src="../src/assets/js/apps/ecommerce-create.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var itemSelect = document.getElementById('itemID');
            var searchInput = document.getElementById('item-search');
            var supplierName = document.getElementById('supplier-name');
            var itemCategory = document.getElementById('item-category');
            var itemStock = document.getElementById('current-stock');
            var form = document.getElementById('product-form');
            var descriptionInput = document.getElementById('description-input');

            function updateItemDetails() {
                var selectedOption = itemSelect.options[itemSelect.selectedIndex];
                if (!selectedOption || !selectedOption.value) {
                    supplierName.value = '';
                    itemCategory.value = '';
                    itemStock.value = '';
                    return;
                }

                supplierName.value = selectedOption.getAttribute('data-supplier-name') || '';
                itemCategory.value = selectedOption.getAttribute('data-category') || '';
                itemStock.value = selectedOption.getAttribute('data-stock') || '';
            }

            function filterItems() {
                var term = searchInput.value.trim().toLowerCase();
                Array.prototype.forEach.call(itemSelect.options, function (option) {
                    if (!option.value) {
                        option.hidden = false;
                        return;
                    }
                    option.hidden = term !== '' && option.textContent.toLowerCase().indexOf(term) === -1;
                });

                if (itemSelect.selectedOptions.length && itemSelect.selectedOptions[0].hidden) {
                    itemSelect.selectedIndex = 0;
                    updateItemDetails();
                }
            }

            if (searchInput) {
                searchInput.addEventListener('input', filterItems);
            }
            if (itemSelect) {
                itemSelect.addEventListener('change', updateItemDetails);
                updateItemDetails();
            }

            if (form) {
                form.addEventListener('submit', function () {
                    if (typeof quill !== 'undefined') {
                        descriptionInput.value = quill.root.innerHTML.trim();
                    }
                    if (typeof ecommerce !== 'undefined') {
                        var pondFiles = ecommerce.getFiles();
                        var dataTransfer = new DataTransfer();
                        pondFiles.forEach(function (fileItem) {
                            var fileObject = null;
                            if (fileItem && typeof fileItem.getFile === 'function') {
                                fileObject = fileItem.getFile();
                            } else if (fileItem && fileItem.file) {
                                fileObject = fileItem.file;
                            }
                            if (fileObject instanceof Blob) {
                                if (!(fileObject instanceof File)) {
                                    var fallbackName = (fileItem && (fileItem.filename || (fileItem.file && fileItem.file.name))) || 'upload';
                                    try {
                                        fileObject = new File([fileObject], fallbackName, { type: fileObject.type || 'application/octet-stream' });
                                    } catch (err) {
                                    }
                                }
                                dataTransfer.items.add(fileObject);
                            }
                        });
                        var fileInput = document.querySelector('input.file-upload-multiple');
                        if (fileInput) {
                            fileInput.files = dataTransfer.files;
                        }
                    }
                });
            }
        });
    </script>
    <!-- END PAGE LEVEL SCRIPTS -->
</body>
</html>
