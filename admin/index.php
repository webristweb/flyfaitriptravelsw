<?php
// Admin Panel - Contact Inquiries Management
session_start();

// Simple authentication (you can enhance this)
$admin_username = "admin";
$admin_password = "admin123"; // Change this password

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in'])) {
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
        if ($_POST['username'] == $admin_username && $_POST['password'] == $admin_password) {
            $_SESSION['admin_logged_in'] = true;
        } else {
            $login_error = "Invalid username or password!";
        }
    }
    
    if (!isset($_SESSION['admin_logged_in'])) {
        // Show login form
        include 'login.php';
        exit;
    }
}

// Logout functionality
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: index.php');
    exit;
}

// Read all CSV files from inquiries folder
$inquiries = [];
$inquiries_dir = '../inquiries/';

if (is_dir($inquiries_dir)) {
    $files = glob($inquiries_dir . 'contacts_*.csv');
    
    foreach ($files as $file) {
        if (($handle = fopen($file, "r")) !== FALSE) {
            $headers = fgetcsv($handle); // Skip header row
            
            while (($data = fgetcsv($handle)) !== FALSE) {
                if (count($data) >= 6) {
                    $inquiries[] = [
                        'date' => $data[0],
                        'name' => $data[1],
                        'email' => $data[2],
                        'phone' => $data[3],
                        'service' => $data[4],
                        'message' => $data[5]
                    ];
                }
            }
            fclose($handle);
        }
    }
}

// Sort by date (newest first)
usort($inquiries, function($a, $b) {
    return strtotime($b['date']) - strtotime($a['date']);
});

$total_inquiries = count($inquiries);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Contact Inquiries</title>
    
    <!-- Bootstrap 4 CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css">
    
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.5.0/css/responsive.bootstrap4.min.css">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        body {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
        }
        
        .admin-container {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0,0,0,0.2);
            padding: 30px;
            margin: 20px auto;
            max-width: 95%;
        }
        
        .admin-header {
            border-bottom: 3px solid #667eea;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        
        .admin-header h1 {
            color: #667eea;
            font-weight: 700;
            margin: 0;
        }
        
        .stats-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            margin-bottom: 30px;
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.3);
        }
        
        .stats-card h3 {
            font-size: 2.5rem;
            font-weight: 700;
            margin: 0;
        }
        
        .stats-card p {
            margin: 0;
            opacity: 0.9;
        }
        
        .btn-logout {
            background: #dc3545;
            color: white;
            border: none;
            padding: 10px 25px;
            border-radius: 5px;
            font-weight: 600;
        }
        
        .btn-logout:hover {
            background: #c82333;
            color: white;
        }
        
        table.dataTable thead th {
            background: #667eea;
            color: white;
            font-weight: 600;
            border: none;
        }
        
        table.dataTable tbody tr:hover {
            background-color: #f8f9ff;
        }
        
        .badge-service {
            padding: 8px 12px;
            border-radius: 20px;
            font-size: 0.85rem;
            font-weight: 600;
        }
        
        .message-cell {
            max-width: 300px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        
        .message-cell:hover {
            white-space: normal;
            overflow: visible;
        }
        
        .dataTables_wrapper .dataTables_filter input {
            border: 2px solid #667eea;
            border-radius: 5px;
            padding: 5px 10px;
        }
        
        .dataTables_wrapper .dataTables_length select {
            border: 2px solid #667eea;
            border-radius: 5px;
            padding: 5px;
        }
        
        .page-item.active .page-link {
            background-color: #667eea;
            border-color: #667eea;
        }
        
        .page-link {
            color: #667eea;
        }
        
        .page-link:hover {
            color: #764ba2;
        }
        
        @media (max-width: 768px) {
            .admin-container {
                padding: 15px;
            }
            
            .admin-header h1 {
                font-size: 1.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="admin-container">
            <!-- Header -->
            <div class="admin-header d-flex justify-content-between align-items-center flex-wrap">
                <div>
                    <h1><i class="fas fa-user-shield"></i> Admin Panel</h1>
                    <p class="text-muted mb-0">Contact Inquiries Management</p>
                </div>
                <a href="?logout=1" class="btn btn-logout">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
            
            <!-- Stats Card -->
            <div class="row">
                <div class="col-md-4">
                    <div class="stats-card">
                        <h3><?php echo $total_inquiries; ?></h3>
                        <p><i class="fas fa-envelope"></i> Total Inquiries</p>
                    </div>
                </div>
            </div>
            
            <!-- Data Table -->
            <div class="table-responsive">
                <table id="inquiriesTable" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>Date & Time</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Service</th>
                            <th>Message</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($inquiries as $inquiry): ?>
                        <tr>
                            <td><?php echo date('d M Y, h:i A', strtotime($inquiry['date'])); ?></td>
                            <td><?php echo htmlspecialchars($inquiry['name']); ?></td>
                            <td>
                                <a href="mailto:<?php echo htmlspecialchars($inquiry['email']); ?>">
                                    <?php echo htmlspecialchars($inquiry['email']); ?>
                                </a>
                            </td>
                            <td>
                                <a href="tel:<?php echo htmlspecialchars($inquiry['phone']); ?>">
                                    <?php echo htmlspecialchars($inquiry['phone']); ?>
                                </a>
                            </td>
                            <td>
                                <span class="badge badge-primary badge-service">
                                    <?php echo htmlspecialchars($inquiry['service']); ?>
                                </span>
                            </td>
                            <td class="message-cell" title="<?php echo htmlspecialchars($inquiry['message']); ?>">
                                <?php echo htmlspecialchars($inquiry['message']); ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Bootstrap 4 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap4.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.5.0/js/responsive.bootstrap4.min.js"></script>
    
    <script>
        $(document).ready(function() {
            $('#inquiriesTable').DataTable({
                responsive: true,
                pageLength: 10,
                lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]],
                order: [[0, 'desc']], // Sort by date descending
                language: {
                    search: "Search Inquiries:",
                    lengthMenu: "Show _MENU_ entries",
                    info: "Showing _START_ to _END_ of _TOTAL_ inquiries",
                    infoEmpty: "No inquiries found",
                    infoFiltered: "(filtered from _MAX_ total inquiries)",
                    zeroRecords: "No matching inquiries found",
                    paginate: {
                        first: "First",
                        last: "Last",
                        next: "Next",
                        previous: "Previous"
                    }
                },
                columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 2, targets: 1 },
                    { responsivePriority: 3, targets: 4 }
                ]
            });
        });
    </script>
</body>
</html>
