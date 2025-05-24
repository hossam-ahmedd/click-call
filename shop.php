<?php
include 'config.php';
session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
    exit;
}

if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $product_quantity = $_POST['product_quantity'];

    // جلب البيانات من قاعدة البيانات للتحقق من الكمية المتوفرة
    $check_product_stock = mysqli_query($conn, "SELECT quantity, name, price, image FROM `products` WHERE id = '$product_id'") or die('Query failed');
    $fetch_product_stock = mysqli_fetch_assoc($check_product_stock);
    $available_quantity = $fetch_product_stock['quantity'];
    $product_name = $fetch_product_stock['name'];
    $product_price = $fetch_product_stock['price'];
    $product_image = $fetch_product_stock['image'];

    if ($product_quantity > $available_quantity) {
        $message[] = 'You cannot add more than the available quantity!';
    } else {
        $check_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE product_id = '$product_id' AND user_id = '$user_id'") or die('Query failed');

        if (mysqli_num_rows($check_cart) > 0) {
            $existing_cart = mysqli_fetch_assoc($check_cart);
            $new_quantity = $existing_cart['quantity'] + $product_quantity;

            if ($new_quantity > $available_quantity) {
                $message[] = 'You cannot add more than the available quantity!';
            } else {
                mysqli_query($conn, "UPDATE `cart` SET quantity = quantity + '$product_quantity' WHERE id = '{$existing_cart['id']}'") or die('Query failed');
                mysqli_query($conn, "UPDATE `products` SET quantity = quantity - '$product_quantity' WHERE id = '$product_id'") or die('Query failed');
                $message[] = 'Product quantity updated in cart!';
            }
        } else {
            mysqli_query($conn, "INSERT INTO `cart`(user_id, product_id, name, price, quantity, image) VALUES('$user_id', '$product_id', '$product_name', '$product_price', '$product_quantity', '$product_image')") or die('Query failed');
            mysqli_query($conn, "UPDATE `products` SET quantity = quantity - '$product_quantity' WHERE id = '$product_id'") or die('Query failed');
            $message[] = 'Product added to cart successfully!';
        }
    }
}

// معالجة عملية حذف المنتج من السلة
if (isset($_GET['remove_from_cart'])) {
    $cart_id = $_GET['remove_from_cart'];
    $check_cart_product = mysqli_query($conn, "SELECT * FROM `cart` WHERE id = '$cart_id'") or die('Query failed');
    $fetch_cart_product = mysqli_fetch_assoc($check_cart_product);
    $product_id = $fetch_cart_product['product_id'];
    $product_quantity = $fetch_cart_product['quantity'];

    // استرجاع الكمية المحذوفة إلى المخزون
    mysqli_query($conn, "UPDATE `products` SET quantity = quantity + '$product_quantity' WHERE id = '$product_id'") or die('Query failed');
    mysqli_query($conn, "DELETE FROM `cart` WHERE id = '$cart_id'") or die('Query failed');
    $message[] = 'Product removed from cart successfully!';
}

// معالجة الرسائل
if (isset($message)) {
    foreach ($message as $msg) {
        echo '<div class="message">' . $msg . '</div>';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Shop</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; ?>

<div class="heading">
   <h3>Our Shop</h3>
   <p> <a href="home.php">Home</a> / Shop </p>
</div>

<section class="products">
   <h1 class="title">Latest Products</h1>
   <div class="box-container">
      <?php  
         $select_products = mysqli_query($conn, "SELECT * FROM `products`") or die('Query failed');
         if (mysqli_num_rows($select_products) > 0) {
            while ($product = mysqli_fetch_assoc($select_products)) {
                $is_out_of_stock = $product['quantity'] == 0;
      ?>
      <form action="" method="post" class="box <?php if ($is_out_of_stock) echo 'out-of-stock'; ?>">
         <img 
    src="<?php echo htmlspecialchars($product['image']); ?>" 
    alt="Image" 
    style="
        max-width: 100px; 
        height: auto; 
        border-radius: 8px; 
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
        transition: transform 0.3s ease, box-shadow 0.3s ease;" 
    onmouseover="this.style.transform='scale(1.2)'; this.style.boxShadow='0 8px 16px rgba(0, 0, 0, 0.2)';" 
    onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 4px 8px rgba(0, 0, 0, 0.1)';">


         <div class="name"><?php echo $product['name']; ?></div>
         <div class="price">$<?php echo $product['price']; ?></div>
         <div class="stock">Available: <?php echo $product['quantity']; ?></div>
         <?php if (!$is_out_of_stock): ?>
         <input type="number" min="1" max="<?php echo $product['quantity']; ?>" name="product_quantity" value="1" class="qty">
         <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
         <input type="submit" value="Add to Cart" name="add_to_cart" class="btn">
         <?php else: ?>
         <div class="out-of-stock-message">Out of Stock</div>
         <?php endif; ?>
      </form>
      <?php
            }
         } else {
            echo '<p class="empty">No products available!</p>';
         }
      ?>
   </div>
</section>

<?php include 'footer.php'; ?>
<script src="js/script.js"></script>
</body>
</html>
