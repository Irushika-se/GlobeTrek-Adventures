<?php
// Sanitize user input to prevent XSS
function clean($data) {
    return htmlspecialchars(strip_tags(trim($data ?? '')));
}

function formatPrice($price) {
    return "LKR " . number_format($price, 2);
}

function successMsg($msg) {
    echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
            $msg
            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
          </div>";
}

function errorMsg($msg) {
    echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
            $msg
            <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
          </div>";
}

function getPackageImage($image_name) {
    $path = "/globetrek/assets/images/packages/" . $image_name;
    return $path;
}

function redirect($url) {
    header("Location: " . $url);
    exit();
}
?>
