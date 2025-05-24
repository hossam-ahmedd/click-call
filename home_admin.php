<?php
// File: home_admin.php
session_start();
include 'config.php';

// Example session check (redirect if not logged in)
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}

// Fetch admin info
$admin_id = $_SESSION['admin_id'];
$admin_query = $conn->prepare("SELECT * FROM users WHERE id = ? AND user_type = 'admin'");
$admin_query->bind_param("i", $admin_id);
$admin_query->execute();
$admin_result = $admin_query->get_result();
$admin = $admin_result->fetch_assoc();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f4f4; color: #333; margin: 0; padding: 0; }
        header { background-color: navy; color: white; padding: 1rem; display: flex; justify-content: space-between; align-items: center; }
        header h1 { margin: 0; }
        .admin-info { font-size: 0.9rem; }
        .container { max-width: 1000px; margin: auto; padding: 1rem; background: white; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 1rem; }
        table, th, td { border: 1px solid #ccc; padding: 0.5rem; text-align: center; }
        a.button, button { padding: 0.5rem 1rem; background: navy; color: white; text-decoration: none; border-radius: 5px; margin: 0.25rem; display: inline-block; border: none; cursor: pointer; }
        a.button:hover, button:hover { background: darkblue; }
    </style>

    <!-- font awesome cdn link -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
<header>
    <h1>Admin Dashboard</h1>
    <div class="admin-info">
        Logged in as: <strong><?php echo htmlspecialchars($admin['name']); ?></strong> |
        Email: <strong><?php echo htmlspecialchars($admin['email']); ?></strong>
    </div>
</header>
<div class="container"  style="display: flex; justify-content: flex-start; gap: 10px; margin-right: 70px; margin-top: 20px; background: fixed;">
    <a href="?page=add_product" class="button">Add Product</a>
    <a href="?page=manage_products" class="button">Manage Products</a>
    <a href="?page=manage_orders" class="button">Manage Orders</a>
    <a href="?page=manage_users" class="button">Manage Users</a>
    <a href="logout.php" class="button">Logout</a>
</div>
<div class="container">
<?php
$page = $_GET['page'] ?? '';

if ($page == 'add_product') {
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $name = $_POST['name'];
        $price = $_POST['price'];
        $image = $_POST['image'];
        $quantity = $_POST['quantity'];

        $check = $conn->prepare("SELECT * FROM products WHERE name = ?");
        $check->bind_param("s", $name);
        $check->execute();
        $result = $check->get_result();

        if ($result->num_rows > 0) {
            echo "<p>Phone already exists.</p>";
        } else {
            $stmt = $conn->prepare("INSERT INTO products (name, price, image, quantity) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("sisi", $name, $price, $image, $quantity);
            $stmt->execute();
            echo "<p>Product added.</p>";
        }
    }
    echo '<form method="POST">
        <input name="name" placeholder="Phone Name" required><br>
        <input name="price" type="number" placeholder="Price" required min="0"><br>
        <input name="image" placeholder="Image Path" required><br>
        <input name="quantity" type="number" placeholder="Quantity" required min="0"><br>
        <button type="submit">Add Product</button>
    </form>';

} elseif ($page == 'manage_products') {
    $result = $conn->query("SELECT * FROM products");
    echo "<table><tr><th>ID</th><th>Name</th><th>Price</th><th>Quantity</th><th>Actions</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>{$row['id']}</td><td>{$row['name']}</td><td>{$row['price']}</td><td>{$row['quantity']}</td>
        <td><a href='?page=update_product&id={$row['id']}' class='button'>Update</a></td></tr>";
    }
    echo "</table>";

} elseif ($page == 'update_product') {
    $id = $_GET['id'];
    if ($_SERVER["REQUEST_METHOD"] === "POST") {
        $quantity = $_POST['quantity'];
        $stmt = $conn->prepare("UPDATE products SET quantity = ? WHERE id = ?");
        $stmt->bind_param("ii", $quantity, $id);
        $stmt->execute();
        echo "<p>Quantity updated.</p>";
    }
    $product = $conn->query("SELECT * FROM products WHERE id = $id")->fetch_assoc();
    echo '<form method="POST">
        <input name="quantity" type="number" value="' . $product['quantity'] . '" required min="0"><br>
        <button type="submit">Update</button>
    </form>';

} elseif ($page == 'manage_orders') {
    if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['update_order'])) {
        $id = $_POST['id'];
        $status = $_POST['status'];
        $conn->query("UPDATE orders SET payment_status = '$status' WHERE id = $id");
    }
    if (isset($_GET['delete_order'])) {
        $id = $_GET['delete_order'];
        $conn->query("DELETE FROM orders WHERE id = $id");
    }
    $result = $conn->query("SELECT * FROM orders");
    echo "<table><tr><th>ID</th><th>User</th><th>Total</th><th>Status</th><th>Actions</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>{$row['id']}</td><td>{$row['user_id']}</td><td>{$row['total_price']}</td><td>{$row['payment_status']}</td>
        <td>
            <form method='POST' style='display:inline;'>
                <input type='hidden' name='id' value='{$row['id']}'>
                <select name='status'>
                    <option value='pending'" . ($row['payment_status']=='pending'?' selected':'') . ">Pending</option>
                    <option value='completed'" . ($row['payment_status']=='completed'?' selected':'') . ">Completed</option>
                </select>
                <button name='update_order'>Update</button>
            </form>
            <a href='?page=manage_orders&delete_order={$row['id']}' class='button'>Delete</a>
        </td></tr>";
    }
    echo "</table>";

} elseif ($page == 'manage_users') {
    if (isset($_GET['delete_user'])) {
        $id = $_GET['delete_user'];

        // تحقق من بيانات المستخدم اللي عايز يتمسح
        $check_user = $conn->prepare("SELECT * FROM users WHERE id = ?");
        $check_user->bind_param("i", $id);
        $check_user->execute();
        $user_to_delete = $check_user->get_result()->fetch_assoc();

        if ($user_to_delete && $user_to_delete['user_type'] != 'admin' && $id != $_SESSION['admin_id']) {
    // احذف بيانات المستخدم المرتبطة قبل مسحه
    $delete_cart = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $delete_cart->bind_param("i", $id);
    $delete_cart->execute();

    $delete_cart = $conn->prepare("DELETE FROM orders WHERE user_id = ?");
    $delete_cart->bind_param("i", $id);
    $delete_cart->execute();

    // بعد كده احذف المستخدم
    $delete_stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $delete_stmt->bind_param("i", $id);
    $delete_stmt->execute();
            
    echo "<p style='color: green;'>تم حذف المستخدم بنجاح.</p>";
}
 else {
            echo "<p style='color: red;'>You cannot delete yourself or delete another admin.</p>";
        }
    }
    $result = $conn->query("SELECT * FROM users");
    echo "<table><tr><th>ID</th><th>Name</th><th>Email</th><th>Actions</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>{$row['id']}</td><td>{$row['name']}</td><td>{$row['email']}</td>
        <td><a href='?page=manage_users&delete_user={$row['id']}' class='button'>Delete</a></td></tr>";
    }
    echo "</table>";
}
?>
</div>
</body>
</html>
