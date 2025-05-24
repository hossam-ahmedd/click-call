<?php
include 'config.php';
session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:login.php');
    exit;
}

// Add to Cart logic
if (isset($_POST['add_to_cart'])) {
    $product_id = $_POST['product_id'];
    $product_quantity = $_POST['product_quantity'];

    // Get available stock
    $check_product_stock = mysqli_query($conn, "SELECT * FROM `products` WHERE id = '$product_id'") or die('Query failed');
    $fetch_product_stock = mysqli_fetch_assoc($check_product_stock);
    $available_quantity = $fetch_product_stock['quantity'];
    $product_name = $fetch_product_stock['name'];
    $product_price = $fetch_product_stock['price'];
    $product_image = $fetch_product_stock['image'];

    if ($product_quantity > $available_quantity) {
        $message[] = '❌ لا يمكن إضافة كمية أكبر من المتاحة!';
    } else {
        $check_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE product_id = '$product_id' AND user_id = '$user_id'") or die('Query failed');
        if (mysqli_num_rows($check_cart) > 0) {
            $message[] = '⚠️ المنتج موجود بالفعل في السلة!';
        } else {
            mysqli_query($conn, "INSERT INTO `cart`(user_id, product_id, name, price, quantity, image) VALUES('$user_id', '$product_id', '$product_name', '$product_price', '$product_quantity', '$product_image')") or die('Query failed');
            mysqli_query($conn, "UPDATE `products` SET quantity = quantity - '$product_quantity' WHERE id = '$product_id'") or die('Query failed');
            $message[] = '✅ تم إضافة المنتج إلى السلة!';
        }
    }

    header('location:cart.php');
    exit;
}

if (isset($_POST['update_cart'])) {
    $cart_id = $_POST['cart_id'];
    $cart_quantity = $_POST['cart_quantity'];
    $product_name = $_POST['product_name'];

    // التحقق من الكمية المتاحة
    $check_product_stock = mysqli_query($conn, "SELECT quantity FROM `products` WHERE name = '$product_name'") or die('query failed');
    $fetch_product_stock = mysqli_fetch_assoc($check_product_stock);
    $available_quantity = $fetch_product_stock['quantity'];

    if ($cart_quantity > $available_quantity) {
        $message[] = 'You cannot add more than the available quantity of this product!';
    } else {
        mysqli_query($conn, "UPDATE `cart` SET quantity = '$cart_quantity' WHERE product_id = '$cart_id' AND user_id = '$user_id'") or die('query failed');
        $message[] = 'Cart quantity updated!';
    }
}

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    $product_name = $_GET['product_name'];
    $deleted_item_quantity = $_GET['quantity'];

    mysqli_query($conn, "DELETE FROM `cart` WHERE product_id = '$delete_id' AND user_id = '$user_id'") or die('query failed');
    mysqli_query($conn, "UPDATE `products` SET quantity = quantity + '$deleted_item_quantity' WHERE name = '$product_name'") or die('query failed');
    header('location:cart.php');
    exit;
}

if (isset($_GET['delete_all'])) {
    $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
    while ($fetch_cart = mysqli_fetch_assoc($select_cart)) {
        $product_name = $fetch_cart['name'];
        $quantity = $fetch_cart['quantity'];
        mysqli_query($conn, "UPDATE `products` SET quantity = quantity + '$quantity' WHERE name = '$product_name'") or die('query failed');
    }

    mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
    header('location:cart.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Cart</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'header.php'; ?>
<div class="heading">
   <h3>Shopping Cart</h3>
   <p> <a href="home.php">Home</a> / Cart </p>
</div>
<section class="shopping-cart">
   <h1 class="title">Products Added</h1>
   <div class="box-container">
      <?php
         $grand_total = 0;
         $select_cart = mysqli_query($conn, "SELECT cart.*, products.quantity AS available_stock 
                                    FROM `cart` 
                                    JOIN `products` ON cart.product_id = products.id 
                                    WHERE cart.user_id = '$user_id'") or die('query failed');

while ($fetch_cart = mysqli_fetch_assoc($select_cart)) {

      ?>
      <div class="box">
         <a href="cart.php?delete=<?php echo $fetch_cart['product_id']; ?>&product_name=<?php echo $fetch_cart['name']; ?>&quantity=<?php echo $fetch_cart['quantity']; ?>" class="fas fa-times" onclick="return confirm('Delete this from cart?');"></a>
         <img 
   src="<?php echo htmlspecialchars($fetch_cart['image']); ?>" 
   alt="Image" 
   style="
      max-width: 100px; 
      height: auto; 
      border-radius: 8px; 
      box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); 
      transition: transform 0.3s ease, box-shadow 0.3s ease;" 
   onmouseover="this.style.transform='scale(1.2)'; this.style.boxShadow='0 8px 16px rgba(0, 0, 0, 0.2)';" 
   onmouseout="this.style.transform='scale(1)'; this.style.boxShadow='0 4px 8px rgba(0, 0, 0, 0.1)';">


         <div class="name"><?php echo $fetch_cart['name']; ?></div>
         <div class="price">$<?php echo $fetch_cart['price']; ?></div>
         <form action="" method="post">
            <input type="hidden" name="cart_id" value="<?php echo $fetch_cart['product_id']; ?>">
            <input type="hidden" name="product_name" value="<?php echo $fetch_cart['name']; ?>">
            <input type="number" min="1" name="cart_quantity" 
            value="<?php echo $fetch_cart['quantity']; ?>" 
            max="<?php echo $fetch_cart['available_stock']; ?>">
            <input type="submit" name="update_cart" value="Update" class="option-btn">
         </form>
         <div class="sub-total"> Sub total : <span>$<?php echo $sub_total = ($fetch_cart['quantity'] * $fetch_cart['price']); ?></span> </div>
      </div>
      <?php
      $grand_total += $sub_total;
         
      }
      ?>
   </div>
   <div style="margin-top: 2rem; text-align:center;">
      <a href="cart.php?delete_all" class="delete-btn <?php echo ($grand_total > 1)?'':'disabled'; ?>" onclick="return confirm('Delete all from cart?');">Delete All</a>
   </div>
   <div class="cart-total">
      <p>Grand Total : <span>$<?php echo $grand_total; ?></span></p>
      <div class="flex">
         <a href="shop.php" class="option-btn">Continue Shopping</a>
         <a href="checkout.php" class="btn <?php echo ($grand_total > 1)?'':'disabled'; ?>">Proceed to Checkout</a>
      </div>
   </div>
</section>
<?php include 'footer.php'; ?>
<script src="js/script.js"></script>
</body>
</html>
