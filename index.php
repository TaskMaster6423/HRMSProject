<?php
	session_start();
	error_reporting(0);
	include('includes/config.php');
	if(strlen($_SESSION['userlogin'])==0){
		header('location:login.php');
	}

	// Fetch total projects
	$projectCount = 0;
	try {
		$sql = "SELECT COUNT(*) as total FROM projects";
		$query = $dbh->prepare($sql);
		$query->execute();
		$projectCount = $query->fetch(PDO::FETCH_OBJ)->total;
	} catch (Exception $e) {
		$projectCount = 0;
	}

	// Fetch total clients
	$clientCount = 0;
	try {
		$sql = "SELECT COUNT(*) as total FROM clients";
		$query = $dbh->prepare($sql);
		$query->execute();
		$clientCount = $query->fetch(PDO::FETCH_OBJ)->total;
	} catch (Exception $e) {
		$clientCount = 0;
	}

	// Fetch total tasks
	$taskCount = 0;
	try {
		$sql = "SELECT COUNT(*) as total FROM tasks";
		$query = $dbh->prepare($sql);
		$query->execute();
		$taskCount = $query->fetch(PDO::FETCH_OBJ)->total;
	} catch (Exception $e) {
		$taskCount = 0;
	}

	// Fetch total employees
	$employeeCount = 0;
	try {
		$sql = "SELECT COUNT(*) as total FROM employees";
		$query = $dbh->prepare($sql);
		$query->execute();
		$employeeCount = $query->fetch(PDO::FETCH_OBJ)->total;
	} catch (Exception $e) {
		$employeeCount = 0;
	}

	// Fetch new employees (last 30 days)
	$newEmployeeCount = 0;
	try {
		$sql = "SELECT COUNT(*) as total FROM employees WHERE JoinDate >= DATE_SUB(NOW(), INTERVAL 30 DAY)";
		$query = $dbh->prepare($sql);
		$query->execute();
		$newEmployeeCount = $query->fetch(PDO::FETCH_OBJ)->total;
	} catch (Exception $e) {
		$newEmployeeCount = 0;
	}

	// Calculate employee growth percentage
	$employeeGrowth = 0;
	if($employeeCount > 0) {
		$employeeGrowth = round(($newEmployeeCount / $employeeCount) * 100, 1);
	}

	// Fetch earnings data
	$currentEarnings = 0;
	$previousEarnings = 0;
	try {
		$sql = "SELECT 
			SUM(CASE WHEN MONTH(date) = MONTH(CURRENT_DATE()) THEN amount ELSE 0 END) as current,
			SUM(CASE WHEN MONTH(date) = MONTH(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH)) THEN amount ELSE 0 END) as previous
			FROM earnings";
		$query = $dbh->prepare($sql);
		$query->execute();
		$result = $query->fetch(PDO::FETCH_OBJ);
		$currentEarnings = $result->current ?? 0;
		$previousEarnings = $result->previous ?? 0;
	} catch (Exception $e) {
		$currentEarnings = 0;
		$previousEarnings = 0;
	}

	// Calculate earnings growth
	$earningsGrowth = 0;
	if($previousEarnings > 0) {
		$earningsGrowth = round((($currentEarnings - $previousEarnings) / $previousEarnings) * 100, 1);
	}

	// Fetch expenses data
	$currentExpenses = 0;
	$previousExpenses = 0;
	try {
		$sql = "SELECT 
			SUM(CASE WHEN MONTH(date) = MONTH(CURRENT_DATE()) THEN amount ELSE 0 END) as current,
			SUM(CASE WHEN MONTH(date) = MONTH(DATE_SUB(CURRENT_DATE(), INTERVAL 1 MONTH)) THEN amount ELSE 0 END) as previous
			FROM expenses";
		$query = $dbh->prepare($sql);
		$query->execute();
		$result = $query->fetch(PDO::FETCH_OBJ);
		$currentExpenses = $result->current ?? 0;
		$previousExpenses = $result->previous ?? 0;
	} catch (Exception $e) {
		$currentExpenses = 0;
		$previousExpenses = 0;
	}

	// Calculate expenses growth
	$expensesGrowth = 0;
	if($previousExpenses > 0) {
		$expensesGrowth = round((($currentExpenses - $previousExpenses) / $previousExpenses) * 100, 1);
	}

	// Calculate profit
	$currentProfit = $currentEarnings - $currentExpenses;
	$previousProfit = $previousEarnings - $previousExpenses;

	// Calculate profit growth
	$profitGrowth = 0;
	if($previousProfit > 0) {
		$profitGrowth = round((($currentProfit - $previousProfit) / $previousProfit) * 100, 1);
	}

	// Fetch recent projects
	$recentProjects = [];
	try {
		$sql = "SELECT p.*, 
			(SELECT COUNT(*) FROM tasks WHERE project_id = p.id AND status = 'open') as open_tasks,
			(SELECT COUNT(*) FROM tasks WHERE project_id = p.id AND status = 'completed') as completed_tasks
			FROM projects p 
			ORDER BY p.created_at DESC 
			LIMIT 5";
		$query = $dbh->prepare($sql);
		$query->execute();
		$recentProjects = $query->fetchAll(PDO::FETCH_OBJ);
	} catch (Exception $e) {
		$recentProjects = [];
	}
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0">
        <meta name="description" content="Smarthr - Bootstrap Admin Template">
		<meta name="keywords" content="admin, estimates, bootstrap, business, corporate, creative, management, minimal, modern, accounts, invoice, html5, responsive, CRM, Projects">
        <meta name="author" content="Dreamguys - Bootstrap Admin Template">
        <meta name="robots" content="noindex, nofollow">
        <title>Dashboard - HRMS admin template</title>
		
		<!-- Favicon -->
        <link rel="shortcut icon" type="image/x-icon" href="assets/img/favicon.png">
		
		<!-- Bootstrap CSS -->
        <link rel="stylesheet" href="assets/css/bootstrap.min.css">
		
		<!-- Fontawesome CSS -->
        <link rel="stylesheet" href="assets/css/font-awesome.min.css">
		
		<!-- Lineawesome CSS -->
        <link rel="stylesheet" href="assets/css/line-awesome.min.css">
		
		<!-- Chart CSS -->
		<link rel="stylesheet" href="assets/plugins/morris/morris.css">
		
		<!-- Main CSS -->
        <link rel="stylesheet" href="assets/css/style.css">
		
		<!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
		<!--[if lt IE 9]>
			<script src="assets/js/html5shiv.min.js"></script>
			<script src="assets/js/respond.min.js"></script>
		<![endif]-->
    </head>
	
    <body>
		<!-- Main Wrapper -->
        <div class="main-wrapper">
		
			<!-- Header -->
            <?php include_once("includes/header.php"); ?>
			<!-- /Header -->
			
			<!-- Sidebar -->
            <?php include_once("includes/sidebar.php");?>
			<!-- /Sidebar -->
			
			<!-- Page Wrapper -->
            <div class="page-wrapper">
			
				<!-- Page Content -->
                <div class="content container-fluid">
				
					<!-- Page Header -->
					<div class="page-header">
						<div class="row">
							<div class="col-sm-12">
								<h3 class="page-title">Welcome <?php echo htmlentities(ucfirst($_SESSION['userlogin']));?>!</h3>
								<ul class="breadcrumb">
									<li class="breadcrumb-item active">Dashboard</li>
								</ul>
							</div>
						</div>
					</div>
					<!-- /Page Header -->
				
					<div class="row">
						<div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
							<div class="card dash-widget">
								<div class="card-body">
									<span class="dash-widget-icon"><i class="fa fa-cubes"></i></span>
									<div class="dash-widget-info">
										<h3><?php echo $projectCount; ?></h3>
										<span>Projects</span>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
							<div class="card dash-widget">
								<div class="card-body">
									<span class="dash-widget-icon"><i class="fa fa-users"></i></span>
									<div class="dash-widget-info">
										<h3><?php echo $clientCount; ?></h3>
										<span>Clients</span>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
							<div class="card dash-widget">
								<div class="card-body">
									<span class="dash-widget-icon"><i class="fa fa-diamond"></i></span>
									<div class="dash-widget-info">
										<h3><?php echo $taskCount; ?></h3>
										<span>Tasks</span>
									</div>
								</div>
							</div>
						</div>
						<div class="col-md-6 col-sm-6 col-lg-6 col-xl-3">
							<div class="card dash-widget">
								<div class="card-body">
									<span class="dash-widget-icon"><i class="fa fa-user"></i></span>
									<div class="dash-widget-info">
										<h3><?php echo $employeeCount; ?></h3>
										<span>Employees</span>
									</div>
								</div>
							</div>
						</div>
					</div>
					
					<div class="row">
						<div class="col-md-12">
							<div class="card-group m-b-30">
								<div class="card">
									<div class="card-body">
										<div class="d-flex justify-content-between mb-3">
											<div>
												<span class="d-block">New Employees</span>
											</div>
											<div>
												<span class="text-success">+<?php echo $employeeGrowth; ?>%</span>
											</div>
										</div>
										<h3 class="mb-3"><?php echo $newEmployeeCount; ?></h3>
										<div class="progress mb-2" style="height: 5px;">
											<div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo $employeeGrowth; ?>%;" aria-valuenow="<?php echo $employeeGrowth; ?>" aria-valuemin="0" aria-valuemax="100"></div>
										</div>
										<p class="mb-0">Overall Employees <?php echo $employeeCount; ?></p>
									</div>
								</div>
							
								<div class="card">
									<div class="card-body">
										<div class="d-flex justify-content-between mb-3">
											<div>
												<span class="d-block">Earnings</span>
											</div>
											<div>
												<span class="text-<?php echo $earningsGrowth >= 0 ? 'success' : 'danger'; ?>"><?php echo $earningsGrowth >= 0 ? '+' : ''; ?><?php echo $earningsGrowth; ?>%</span>
											</div>
										</div>
										<h3 class="mb-3">$<?php echo number_format($currentEarnings, 2); ?></h3>
										<div class="progress mb-2" style="height: 5px;">
											<div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo abs($earningsGrowth); ?>%;" aria-valuenow="<?php echo abs($earningsGrowth); ?>" aria-valuemin="0" aria-valuemax="100"></div>
										</div>
										<p class="mb-0">Previous Month <span class="text-muted">$<?php echo number_format($previousEarnings, 2); ?></span></p>
									</div>
								</div>
							
								<div class="card">
									<div class="card-body">
										<div class="d-flex justify-content-between mb-3">
											<div>
												<span class="d-block">Expenses</span>
											</div>
											<div>
												<span class="text-<?php echo $expensesGrowth <= 0 ? 'success' : 'danger'; ?>"><?php echo $expensesGrowth >= 0 ? '+' : ''; ?><?php echo $expensesGrowth; ?>%</span>
											</div>
										</div>
										<h3 class="mb-3">$<?php echo number_format($currentExpenses, 2); ?></h3>
										<div class="progress mb-2" style="height: 5px;">
											<div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo abs($expensesGrowth); ?>%;" aria-valuenow="<?php echo abs($expensesGrowth); ?>" aria-valuemin="0" aria-valuemax="100"></div>
										</div>
										<p class="mb-0">Previous Month <span class="text-muted">$<?php echo number_format($previousExpenses, 2); ?></span></p>
									</div>
								</div>
							
								<div class="card">
									<div class="card-body">
										<div class="d-flex justify-content-between mb-3">
											<div>
												<span class="d-block">Profit</span>
											</div>
											<div>
												<span class="text-<?php echo $profitGrowth >= 0 ? 'success' : 'danger'; ?>"><?php echo $profitGrowth >= 0 ? '+' : ''; ?><?php echo $profitGrowth; ?>%</span>
											</div>
										</div>
										<h3 class="mb-3">$<?php echo number_format($currentProfit, 2); ?></h3>
										<div class="progress mb-2" style="height: 5px;">
											<div class="progress-bar bg-primary" role="progressbar" style="width: <?php echo abs($profitGrowth); ?>%;" aria-valuenow="<?php echo abs($profitGrowth); ?>" aria-valuemin="0" aria-valuemax="100"></div>
										</div>
										<p class="mb-0">Previous Month <span class="text-muted">$<?php echo number_format($previousProfit, 2); ?></span></p>
									</div>
								</div>
							</div>
						</div>	
					</div>
					
					<div class="row">
						<div class="col-md-12 d-flex">
							<div class="card card-table flex-fill">
								<div class="card-header">
									<h3 class="card-title mb-0">Recent Projects</h3>
								</div>
								<div class="card-body">
									<div class="table-responsive">
										<table class="table custom-table mb-0">
											<thead>
												<tr>
													<th>Project Name </th>
													<th>Progress</th>
													<th class="text-right">Action</th>
												</tr>
											</thead>
											<tbody>
												<?php if(count($recentProjects) > 0): ?>
													<?php foreach($recentProjects as $project): ?>
														<tr>
															<td>
																<h2><a href="project-view.php?id=<?php echo $project->id; ?>"><?php echo htmlentities($project->name); ?></a></h2>
																<small class="block text-ellipsis">
																	<span><?php echo $project->open_tasks; ?></span> <span class="text-muted">open tasks, </span>
																	<span><?php echo $project->completed_tasks; ?></span> <span class="text-muted">tasks completed</span>
																</small>
															</td>
															<td>
																<div class="progress progress-xs progress-striped">
																	<?php 
																		$progress = 0;
																		$total_tasks = $project->open_tasks + $project->completed_tasks;
																		if($total_tasks > 0) {
																			$progress = round(($project->completed_tasks / $total_tasks) * 100);
																		}
																	?>
																	<div class="progress-bar" role="progressbar" data-toggle="tooltip" title="<?php echo $progress; ?>%" style="width: <?php echo $progress; ?>%"></div>
																</div>
															</td>
															<td class="text-right">
																<div class="dropdown dropdown-action">
																	<a href="#" class="action-icon dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="material-icons">more_vert</i></a>
																	<div class="dropdown-menu dropdown-menu-right">
																		<a class="dropdown-item" href="edit-project.php?id=<?php echo $project->id; ?>"><i class="fa fa-pencil m-r-5"></i> Edit</a>
																		<a class="dropdown-item" href="javascript:void(0)" onclick="deleteProject(<?php echo $project->id; ?>)"><i class="fa fa-trash-o m-r-5"></i> Delete</a>
																	</div>
																</div>
															</td>
														</tr>
													<?php endforeach; ?>
												<?php else: ?>
													<tr>
														<td colspan="3" class="text-center">No projects found</td>
													</tr>
												<?php endif; ?>
											</tbody>
										</table>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- /Page Content -->

   </div>
			<!-- /Page Wrapper -->
			
        </div>
		<!-- /Main Wrapper -->
		
		<!-- javascript links starts here -->
		<!-- jQuery -->
        <script src="assets/js/jquery-3.2.1.min.js"></script>
		
		<!-- Bootstrap Core JS -->
        <script src="assets/js/popper.min.js"></script>
        <script src="assets/js/bootstrap.min.js"></script>
		
		<!-- Slimscroll JS -->
		<script src="assets/js/jquery.slimscroll.min.js"></script>
		
		<!-- Chart JS -->
		<script src="assets/plugins/morris/morris.min.js"></script>
		<script src="assets/plugins/raphael/raphael.min.js"></script>
		<script src="assets/js/chart.js"></script>
		
		<!-- Custom JS -->
		<script src="assets/js/app.js"></script>
		<!-- javascript links ends here  -->
		
		<script>
			function deleteProject(id) {
				if(confirm('Are you sure you want to delete this project?')) {
					window.location.href = 'delete-project.php?id=' + id;
				}
			}
		</script>
    </body>
</html>