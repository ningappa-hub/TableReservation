<?php
session_start();
if (!isset($_SESSION['id'])) {
    header("Location: login.php");
    exit();
}

include 'dbCon.php';
$con = connect();

// Get booking number from URL
$booking_number = isset($_GET['booking-number']) ? $_GET['booking-number'] : '';

// Verify this booking belongs to logged in user
$sql = "SELECT * FROM booking_details WHERE booking_id = ? AND c_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("si", $booking_number, $_SESSION['id']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    header("Location: index.php");
    exit();
}

include 'template/header.php';
?>

<body>
    <?php include 'template/nav-bar.php'; ?>

    <section class="ftco-section">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <div class="invoice">
                        <header>
                            <div class="row">
                                <div class="col-sm-6">
                                    <h2 class="h2 mt-0 mb-1">INVOICE</h2>
                                    <h4 class="h4 m-0 text-dark font-weight-bold">#<?php echo $booking_number; ?></h4>
                                </div>
                                <div class="col-sm-6 text-right mt-3 mt-sm-0">
                                    <address class="ib mr-5">
                                        <?php 
                                        $booking = $result->fetch_assoc();
                                        echo htmlspecialchars($booking['name']) . "<br>";
                                        echo htmlspecialchars($booking['phone']) . "<br>";
                                        echo "Booking Date: " . htmlspecialchars($booking['booking_date']) . "<br>";
                                        echo "Booking Time: " . htmlspecialchars($booking['booking_time']);
                                        ?>
                                    </address>
                                </div>
                            </div>
                        </header>
                        
                        <div class="bill-info">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="bill-to">
                                        <p class="h5 mb-1 text-dark font-weight-semibold">Ordered Items:</p>
                                        <table class="table table-bordered table-striped mb-0">
                                            <thead>
                                                <tr>
                                                    <th>Item</th>
                                                    <th>Quantity</th>
                                                    <th>Price</th>
                                                    <th>Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $sql = "SELECT m.item_name, m.price, bm.qty 
                                                       FROM booking_menus bm 
                                                       JOIN menu_item m ON bm.item_id = m.id 
                                                       WHERE bm.booking_id = ?";
                                                $stmt = $con->prepare($sql);
                                                $stmt->bind_param("s", $booking_number);
                                                $stmt->execute();
                                                $items = $stmt->get_result();
                                                
                                                $total = 0;
                                                while($item = $items->fetch_assoc()):
                                                    $itemTotal = $item['price'] * $item['qty'];
                                                    $total += $itemTotal;
                                                ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($item['item_name']); ?></td>
                                                    <td><?php echo $item['qty']; ?></td>
                                                    <td>₹<?php echo $item['price']; ?></td>
                                                    <td>₹<?php echo $itemTotal; ?></td>
                                                </tr>
                                                <?php endwhile; ?>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="3" class="text-right">
                                                        <strong>Total:</strong>
                                                    </td>
                                                    <td>
                                                        <strong>₹<?php echo $total; ?></strong>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="bill-data text-right">
                                        <p class="mb-0">
                                            <span class="text-dark">Transaction ID:</span>
                                            <span class="value"><?php echo htmlspecialchars($booking['transactionid']); ?></span>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="text-right mr-4">
                            <button onclick="window.print();" class="btn btn-primary">
                                <i class="fa fa-print"></i> Print Invoice
                            </button>
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fa fa-arrow-left"></i> Back
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <?php include 'template/footer.php'; ?>
    <?php include 'template/script.php'; ?>
</body>
</html>