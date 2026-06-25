<?php
require_once 'db_connect.php';

$errors = [];
$success = false;

// Process POST mutation data upon form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize data entries to strip unnecessary whitespace
    $student_number = trim($_POST['student_number'] ?? '');
    $first_name     = trim($_POST['first_name'] ?? '');
    $last_name      = trim($_POST['last_name'] ?? '');
    $email          = trim($_POST['email'] ?? '');
    $course         = trim($_POST['course'] ?? '');

    // Form Server-side Assertions
    if (empty($student_number)) { $errors[] = "Student ID number is required."; }
    if (empty($first_name))     { $errors[] = "First name field is required."; }
    if (empty($last_name))      { $errors[] = "Last name field is required."; }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = "Please provide a valid email address."; }
    if (empty($course))         { $errors[] = "Course declaration field is required."; }

    // If validations pass, check uniqueness and execute database insert operation
    if (empty($errors)) {
        // Enforce uniqueness validation of Student ID
        $check_stmt = $conn->prepare("SELECT id FROM students WHERE student_number = ?");
        $check_stmt->bind_param("s", $student_number);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if ($check_stmt->num_rows > 0) {
            $errors[] = "This Student ID number is already assigned to a registered profile.";
            $check_stmt->close();
        } else {
            $check_stmt->close();

            // Prepare secure transaction statement execution block
            $stmt = $conn->prepare("INSERT INTO students (student_number, first_name, last_name, email, course) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $student_number, $first_name, $last_name, $email, $course);
            
            if ($stmt->execute()) {
                $success = true;
                // Optional Redirect configuration: header('Location: index.php'); exit;
            } else {
                $errors[] = "An infrastructure level write error occurred: " . $stmt->error;
            }
            $stmt->close();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student Record</title>
    <style>
        :root {
            --bg-color: #121214;
            --card-bg: #1a1a1e;
            --text-color: #e2e8f0;
            --text-muted: #94a3b8;
            --accent-pink: #f472b6;
            --accent-pink-hover: #ec4899;
            --border-color: #2d2d34;
            --error: #ef4444;
            --success: #10b981;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: var(--bg-color); color: var(--text-color); padding: 2rem 1rem; display: flex; justify-content: center; }
        .form-card { width: 100%; max-width: 500px; background-color: var(--card-bg); border: 1px solid var(--border-color); border-radius: 8px; padding: 2rem; }
        
        .header { margin-bottom: 1.5rem; display: flex; justify-content: space-between; align-items: center; }
        h1 { font-size: 1.5rem; color: #fff; }
        .back-link { color: var(--accent-pink); text-decoration: none; font-size: 0.9rem; }
        .back-link:hover { text-decoration: underline; }

        .form-group { margin-bottom: 1.2rem; }
        label { display: block; margin-bottom: 0.4rem; font-size: 0.85rem; color: var(--text-muted); font-weight: 500; }
        input[type="text"], input[type="email"] { width: 100%; padding: 0.75rem; background-color: #121214; border: 1px solid var(--border-color); border-radius: 6px; color: #fff; font-size: 0.95rem; transition: border-color 0.2s; }
        input:focus { outline: none; border-color: var(--accent-pink); }
        
        .btn-submit { width: 100%; background-color: var(--accent-pink); color: #121214; padding: 0.75rem; border-radius: 6px; border: none; font-weight: 600; font-size: 1rem; cursor: pointer; transition: background-color 0.2s; margin-top: 0.5rem; }
        .btn-submit:hover { background-color: var(--accent-pink-hover); }

        .alert { padding: 0.75rem 1rem; border-radius: 6px; font-size: 0.9rem; margin-bottom: 1.2rem; line-height: 1.4; }
        .alert-danger { background-color: rgba(239, 68, 68, 0.1); border: 1px solid var(--error); color: var(--error); }
        .alert-success { background-color: rgba(16, 185, 129, 0.1); border: 1px solid var(--success); color: var(--success); }
    </style>
</head>
<body>

<div class="form-card">
    <div class="header">
        <h1>Add Student</h1>
        <a href="index.php" class="back-link">← Cancel & Return</a>
    </div>

    <?php if (!empty($errors)): ?>
        <div class="alert alert-danger">
            <?php foreach($errors as $error): ?>
                <div><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <?php if ($success): ?>
        <div class="alert alert-success">
            Student record cataloged successfully into the active directory database system.
        </div>
    <?php endif; ?>

    <form action="add_student.php" method="POST">
        <div class="form-group">
            <label for="student_number">Student ID Number</label>
            <input type="text" id="student_number" name="student_number" value="<?php echo htmlspecialchars($student_number ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="e.g., 2024-00123-MN-0">
        </div>
        <div class="form-group">
            <label for="first_name">First Name</label>
            <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($first_name ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div class="form-group">
            <label for="last_name">Last Name</label>
            <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($last_name ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div class="form-group">
            <label for="email">Institutional/Personal Email Address</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($email ?? '', ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div class="form-group">
            <label for="course">Program / Course of Study</label>
            <input type="text" id="course" name="course" value="<?php echo htmlspecialchars($course ?? '', ENT_QUOTES, 'UTF-8'); ?>" placeholder="e.g., BS Computer Science">
        </div>
        <button type="submit" class="btn-submit">Save Student Profile</button>
    </form>
</div>

</body>
</html>
<?php $conn->close(); ?>