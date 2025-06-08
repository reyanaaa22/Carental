<?php
ob_start(); // Start output buffering

session_start();
if (!isset($_SESSION['login']) || empty($_SESSION['login'])) {
    header("Location: login.php");
    exit;
}

include('db.php');
include('header.php');

// Get the logged-in user's ID
$userId = $_SESSION['user_id'];

// Fetch user info
$stmt = $dbh->prepare("SELECT * FROM tblusers WHERE UserID = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    header("Location: login.php");
    exit;
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = $_POST['FullName'];
    $email = $_POST['EmailId'];
    $contact = $_POST['ContactNumber'];
    $password = !empty($_POST['Password']) ? password_hash($_POST['Password'], PASSWORD_DEFAULT) : $user['Password'];
    $profileImage = $user['profile_image'] ?? '';

    // Handle image upload
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
        if (!is_dir('images')) {
            mkdir('images', 0777, true);
        }
        $imgName = uniqid('profile_', true) . '.' . pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION);
        move_uploaded_file($_FILES['profile_image']['tmp_name'], "images/$imgName");
        $profileImage = "images/$imgName";
    }

    // Update user info in the database
    $stmt = $dbh->prepare("UPDATE tblusers SET FullName=?, EmailId=?, ContactNumber=?, Password=?, profile_image=? WHERE UserID=?");
    $stmt->execute([$fullName, $email, $contact, $password, $profileImage, $userId]);

    // Log the profile update with changed fields
    $updatedFields = [];
    if (isset($_POST['FullName']) && !empty($_POST['FullName'])) $updatedFields[] = 'name';
    if (isset($_POST['EmailId']) && !empty($_POST['EmailId'])) $updatedFields[] = 'email';
    if (isset($_POST['ContactNumber']) && !empty($_POST['ContactNumber'])) $updatedFields[] = 'phone';
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['size'] > 0) $updatedFields[] = 'profile image';
    
    logUserActivity($dbh, $_SESSION['user_id'], 'profile_update', ['fields' => $updatedFields]);
    
    $_SESSION['success'] = "Profile updated successfully";
    header('Location: profile.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Profile</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .profile-container { max-width: 600px; margin: 10px auto; background: #fff; border-radius: 8px; box-shadow: 0 2px 8px #0001; padding: 32px; }
        .profile-img { width: 120px; height: 120px; object-fit: cover; border-radius: 50%; border: 2px solid #eee; }
        .profile-img-wrapper {
            position: relative;
            display: inline-block;
        }
        .camera-icon-label {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: #fff;
            border-radius: 50%;
            padding: 8px;
            cursor: pointer;
            box-shadow: 0 2px 8px #0002;
            border: 1px solid #eee;
        }
        .camera-icon-label i {
            font-size: 1.2rem;
            color: #333;
        }
        input[type="file"].d-none {
            display: none;
        }

    </style>
<div class="profile-container">
    <h3>My Profile</h3>
    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3 text-center">
            <div class="profile-img-wrapper">
                <img src="<?= !empty($user['profile_image']) ? $user['profile_image'] : 'default-profile.png' ?>" class="profile-img mb-2" alt="Profile Image">
                <label for="profile_image" class="camera-icon-label">
                    <i class="fa fa-camera"></i>
                </label>
                <input type="file" id="profile_image" name="profile_image" class="form-control d-none">
            </div>
        </div>
        <div class="mb-3">
            <label class="form-label">Full Name</label>
            <input type="text" name="FullName" class="form-control" value="<?= htmlspecialchars($user['FullName']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="EmailId" class="form-control" value="<?= htmlspecialchars($user['EmailId']) ?>" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Contact Number</label>
            <input type="text" name="ContactNumber" class="form-control" value="<?= htmlspecialchars($user['ContactNumber']) ?>">
        </div>
        <div class="mb-3">
            <label class="form-label">New Password (leave blank to keep current)</label>
            <input type="password" name="Password" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Update Profile</button>
    </form>
</div>
<script>
document.getElementById('profile_image').addEventListener('change', function(event) {
    const [file] = event.target.files;
    if (file) {
        const img = document.querySelector('.profile-img');
        img.src = URL.createObjectURL(file);
    }
});
</script>
</body>
</html>