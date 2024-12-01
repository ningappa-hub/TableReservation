
<?php
// ...existing code...

function calculateBookingRevenue($res_id) {
    global $con;
    $stmt = $con->prepare("SELECT CalculateBookingRevenue(?) AS Revenue");
    $stmt->bind_param("i", $res_id);
    $stmt->execute();
    $stmt->bind_result($revenue);
    $stmt->fetch();
    $stmt->close();
    return $revenue;
}

// ...existing code...
?>