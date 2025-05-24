<?php
include 'config.php';
session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
}

if (isset($_POST['add_to_cart'])) {
    $product_name = $_POST['product_name'];
    $product_price = $_POST['product_price'];
    $product_image = $_POST['product_image'];
    $product_quantity = $_POST['product_quantity'];

    // التحقق من الكمية المتوفرة
    $check_product_stock = mysqli_query($conn, "SELECT max_quantity FROM products WHERE name = '$product_name'") or die('query failed');
    $fetch_product_stock = mysqli_fetch_assoc($check_product_stock);
    $available_quantity = $fetch_product_stock['max_quantity'];

    if ($product_quantity > $available_quantity) {
        $message[] = 'You cannot add more than the available quantity of this product!';
    } else {
        $check_cart_numbers = mysqli_query($conn, "SELECT * FROM cart WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed');
        if (mysqli_num_rows($check_cart_numbers) > 0) {
            $message[] = 'Already added to cart!';
        } else {
            // إضافة المنتج إلى العربة
            mysqli_query($conn, "INSERT INTO cart(user_id, name, price, quantity, image) VALUES('$user_id', '$product_name', '$product_price', '$product_quantity', '$product_image')") or die('query failed');
            // تقليل الكمية المتاحة في قاعدة البيانات
            mysqli_query($conn, "UPDATE products SET max_quantity = max_quantity - '$product_quantity' WHERE name = '$product_name'") or die('query failed');
            $message[] = 'Product added to cart!';
        }
    }
}

if (isset($_POST['remove_from_cart'])) {
    $product_name = $_POST['product_name'];
    $product_quantity = $_POST['product_quantity'];

    // حذف المنتج من العربة
    mysqli_query($conn, "DELETE FROM cart WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed');
    // إعادة الكمية إلى قاعدة البيانات
    mysqli_query($conn, "UPDATE products SET max_quantity = max_quantity + '$product_quantity' WHERE name = '$product_name'") or die('query failed');
    $message[] = 'Product removed from cart!';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Home</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; ?>
<section class="home">
   <div class="content">
      <h3>Click&Call<br> For You</h3>
      <p>Your Next Phone, Just a Click Away.</p>
      <a href="shop.php" class="white-btn">Discover More</a>
   </div>
</section>

<section class="about" id="about-section">
   <div class="flex">
      <div class="image">
<img src="images/tayawee-supan-60LrgcVi5zQ-unsplash.jpg" alt="" style="width: 400px; height: auto; margin-left: 50px;">
      </div>
      <div class="content">
         <h3>About Us</h3>
         <p>At Click&Call, we bring the latest in mobile technology right to your fingertips. Discover top brands, unbeatable deals, and seamless service — all with just a click or call!</p>
      </div>
   </div>
</section>

<?php include 'footer.php'; ?>
<script src="js/script.js"></script>
</body>
</html>