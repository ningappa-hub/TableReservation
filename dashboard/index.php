<!-- booking-list.php -->
<?php include 'template/header.php'; 
if (!isset($_SESSION['isLoggedIn'])) {
	echo '<script>window.location="login.php"</script>';
}

// Include PHPMailerAutoload.php using the updated autoload method
require 'PHPMailer-master/PHPMailerAutoload.php';

// Remove the duplicate include and correct the path
include_once '../database/dbCon.php';
?>
	<body>
		<section class="body">

			<!-- start: header -->
			<?php include 'template/top-bar.php'; ?>
			<!-- end: header -->

			<div class="inner-wrapper">
				<!-- start: sidebar -->
				<?php include 'template/left-bar.php'; ?>
				<!-- end: sidebar -->

				<section role="main" class="content-body">
					<header class="page-header">
						<h2>Table</h2>
					
						<div class="right-wrapper pull-right">
							<ol class="breadcrumbs">
								<li>
									<a href="index.php">
										<i class="fa fa-home"></i>
									</a>
								</li>
								<li><span>Tables</span></li>
								<li><span>Booking List</span></li>
							</ol>
					
							<a class="sidebar-right-toggle" data-open="sidebar-right"><i class="fa fa-chevron-left"></i></a>
						</div>
					</header>

					<!-- start: page -->
						
						
						<section class="panel">
							<header class="panel-heading">
								<div class="panel-actions">
									<a href="#" class="fa fa-caret-down"></a>
									<a href="#" class="fa fa-times"></a>
								</div>
						
								<h2 class="panel-title">All Bookings</h2>
							</header>
							<div class="panel-body">
								<table class="table table-bordered table-striped mb-none" id="datatable-tabletools" data-swf-path="assets/vendor/jquery-datatables/extras/TableTools/swf/copy_csv_xls_pdf.swf">
									<thead>
										<tr>
											<th>No</th>
											<th>Transaction Id</th>
											<th>Name</th>
											<th>Phone</th>
											<th>Date</th>
											<th>Time</th>
											<th>Bill</th>
											<th class="hidden-phone">Status</th>
											<th class="hidden-phone">Action</th>
											<th class="hidden-phone">View</th>
										</tr>
									</thead>
									<tbody>
										<?php
										$count = 1;
										include_once 'dbCon.php';
										$con = connect();
										$res_id = $_SESSION['id'];
										$sql = "SELECT * FROM `booking_details` WHERE res_id = '$res_id'  ORDER BY make_date DESC;";
										$result = $con->query($sql);
										foreach ($result as $r) {
										?>
										<tr class="gradeX">
											<td class="center hidden-phone"><?php echo $count; ?></td>
											<td class="center hidden-phone"><?php echo $r['transactionid']; ?></td>
											<td><?php echo $r['name']; ?></td>
											<td><?php echo $r['phone']; ?></td>
											<td><?php echo $r['booking_date']; ?></td>
											<td><?php echo $r['booking_time']; ?></td>
											<td><?php echo $r['bill']; ?> ₹</td>
											<td class="center hidden-phone">
												<?php 
													$status = $r['status'];
													if ($status == 0) {
												?>
												<p class="text-danger">Rejected</p>
												<?php }else{ ?>
													<p class="text-success">Confirmed</p>
												<?php } ?>
											</td>
											<td class="center hidden-phone">
												<?php 
													if ($status == 1) {
												?>
												<a href="approve-reject.php?breject_id=<?php echo $r['id']; ?>&booking-number=<?php echo $r['booking_id']; ?>" class="btn btn-danger"  onclick="if (!Done()) return false; ">Reject</a>
												<?php }else{ ?>
												<a href="approve-reject.php?bapprove_id=<?php echo $r['id']; ?>&booking-number=<?php echo $r['booking_id']; ?>" class="btn btn-success"  onclick="if (!Done()) return false; ">Confirm</a>	
												<?php } ?>
											</td>
											<td class="center hidden-phone">
												<a href="invoice.php?booking-number=<?php echo $r['booking_id']; ?>" class="btn btn-primary">View</a>
											</td>
										</tr>
										<?php $count++; } ?>
									</tbody>
								</table>
							</div>
						</section>
						
						
					<!-- end: page -->
				</section>
			</div>

			<?php include 'template/right-bar.php'; ?>
		</section>
		<script type="text/javascript">
	       function Done(){
	         return confirm("Are you sure?");
	       }
   		</script>

		<?php include 'template/script-res.php'; ?>

		<?php
		// Initialize PHPMailer
		$mail = new PHPMailer(true);
		?>

		<?php
		// Include database connection
		include_once 'dbCon.php';
		$con = connect();

		// Query the RestaurantBookingSummary view
		$summary_sql = "SELECT * FROM RestaurantBookingSummary;";
		$summary_result = $con->query($summary_sql);
		?>

		<!-- Restaurant Booking Summary Section -->
		<section id="role="main" class="content-body" style="display: flex; flex-direction: column; align-items: center;">
			<h2>Restaurant Booking Summary</h2>
			<table class="table table-bordered table-striped" style="width: 50%">
				<thead>
					<tr>
						<th>Restaurant ID</th>
						<th>Total Bookings</th>
						<th>Total Revenue (₹)</th>
					</tr>
				</thead>
				<tbody>
					<?php while ($row = $summary_result->fetch_assoc()) { ?>
					<tr>
						<td><?php echo $row['RestaurantID']; ?></td>
						<td><?php echo $row['TotalBookings']; ?></td>
						<td><?php echo $row['TotalRevenue']; ?></td>
					</tr>
					<?php } ?>
				</tbody>
			</table>
			</table>
		</section>
		<section id="role="main" class="content-body"" style="display: flex; flex-direction: column; align-items: center;">
		<?php include('nestedquery.php'); ?>
		<div class="container" style="text-align: center; margin-top: 20px; background-color: black; color: white;">
			<?php
		$res_id = $_SESSION['id']; // Dynamically get the restaurant ID
		$total_revenue = calculateBookingRevenue($res_id);
		echo "<p>Total Revenue: ₹" . $total_revenue . "</p>";
		?>
		</div>
		</section>
		
	</body>
</html>