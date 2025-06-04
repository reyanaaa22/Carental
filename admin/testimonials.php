<?php
session_start();
include_once('../db.php');

// Check if admin is logged in
if (!isset($_SESSION['admin_id']) || empty($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// Function to get initials from name
function getInitials($name) {
    $words = explode(' ', $name);
    $initials = '';
    foreach ($words as $word) {
        $initials .= strtoupper(substr($word, 0, 1));
    }
    return substr($initials, 0, 2); // Return maximum 2 initials
}

// Function to generate a consistent color based on name
function getColorFromName($name) {
    $colors = [
        '#1abc9c', '#2ecc71', '#3498db', '#9b59b6', '#34495e',
        '#16a085', '#27ae60', '#2980b9', '#8e44ad', '#2c3e50',
        '#f1c40f', '#e67e22', '#e74c3c', '#95a5a6', '#f39c12',
        '#d35400', '#c0392b', '#7f8c8d'
    ];
    return $colors[abs(crc32($name)) % count($colors)];
}

// Handle status updates
if (isset($_POST['action']) && isset($_POST['testimonial_id'])) {
    $testimonial_id = $_POST['testimonial_id'];
    $action = $_POST['action'];
    
    if ($action === 'approve' || $action === 'reject') {
        $status = ($action === 'approve') ? 'approved' : 'rejected';
        $update = $dbh->prepare("UPDATE testimonials SET status = ? WHERE id = ?");
        $update->execute([$status, $testimonial_id]);
        
        // Add success message
        $success_msg = "Testimonial has been " . $status;
    }
}

// Fetch all testimonials
$stmt = $dbh->prepare("
    SELECT t.*, u.FullName, u.EmailId 
    FROM testimonials t 
    LEFT JOIN tblusers u ON t.user_id = u.UserID 
    ORDER BY t.created_at DESC
");
$stmt->execute();
$testimonials = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Testimonials</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        .main-content {
            position: absolute;
            top: 70px;
            left: 250px;
            width: calc(100% - 250px);
            height: calc(100vh - 70px);
            overflow-y: auto;
            padding: 30px;
            background-color: #f8f9fa;
        }

        .testimonial-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 0 20px rgba(0,0,0,0.05);
            padding: 20px;
            margin-bottom: 20px;
        }

        .testimonial-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }

        .testimonial-content {
            font-size: 16px;
            color: #555;
            margin-bottom: 15px;
            line-height: 1.6;
        }

        .rating {
            color: #ffc107;
            margin-bottom: 10px;
        }

        .status-badge {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-pending {
            background-color: #ffeeba;
            color: #856404;
        }

        .status-approved {
            background-color: #d4edda;
            color: #155724;
        }

        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
        }

        .user-initials {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: white;
            font-size: 16px;
        }

        .testimonial-date {
            color: #6c757d;
            font-size: 0.9em;
        }

        .user-details {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: 600;
            color: #333;
            margin: 0;
        }

        .user-email {
            font-size: 0.85em;
            color: #666;
            margin: 0;
        }
    </style>
</head>
<?php include('includes/header.php'); ?>
<?php include('includes/sidebar.php'); ?>
<body>

<div class="main-content">
    <div class="container">
        <div class="row mb-4">
            <div class="col">
                <h2><i class="fas fa-comments me-2"></i>Manage Testimonials</h2>
                <?php if (isset($success_msg)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <?= htmlentities($success_msg) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <?php if (empty($testimonials)): ?>
            <div class="text-center py-5">
                <i class="fas fa-comment-slash fa-3x text-muted mb-3"></i>
                <p class="lead text-muted">No testimonials available yet.</p>
            </div>
        <?php else: ?>
            <?php foreach ($testimonials as $testimonial): ?>
                <div class="testimonial-card">
                    <div class="testimonial-header">
                        <div class="user-info">
                            <?php 
                            $displayName = $testimonial['FullName'] ?? $testimonial['user_name'];
                            $initials = getInitials($displayName);
                            $bgColor = getColorFromName($displayName);
                            ?>
                            <div class="user-initials" style="background-color: <?= $bgColor ?>">
                                <?= htmlentities($initials) ?>
                            </div>
                            <div class="user-details">
                                <p class="user-name"><?= htmlentities($displayName) ?></p>
                                <p class="user-email"><?= htmlentities($testimonial['EmailId'] ?? 'N/A') ?></p>
                            </div>
                        </div>
                        <span class="status-badge status-<?= $testimonial['status'] ?>">
                            <?= ucfirst($testimonial['status']) ?>
                        </span>
                    </div>

                    <div class="rating">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <i class="fas fa-star<?= $i <= $testimonial['rating'] ? '' : '-o' ?>"></i>
                        <?php endfor; ?>
                    </div>

                    <div class="testimonial-content">
                        <?= nl2br(htmlentities($testimonial['testimonial'])) ?>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <small class="testimonial-date">
                            <i class="far fa-clock me-1"></i>
                            <?= date('F j, Y g:i A', strtotime($testimonial['created_at'])) ?>
                        </small>

                        <?php if ($testimonial['status'] === 'pending'): ?>
                            <div class="action-buttons">
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="testimonial_id" value="<?= $testimonial['id'] ?>">
                                    <input type="hidden" name="action" value="approve">
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="fas fa-check me-1"></i>Approve
                                    </button>
                                </form>
                                <form method="POST" class="d-inline">
                                    <input type="hidden" name="testimonial_id" value="<?= $testimonial['id'] ?>">
                                    <input type="hidden" name="action" value="reject">
                                    <button type="submit" class="btn btn-danger btn-sm">
                                        <i class="fas fa-times me-1"></i>Reject
                                    </button>
                                </form>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 