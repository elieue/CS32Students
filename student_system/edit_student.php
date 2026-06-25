<?php
require_once 'db_connect.php';

$errors = [];
$success = false;
$student = null;

// Determine targeted context record initialization scope
if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // Fetch individual dataset vector matrix row sequence elements
    $stmt = $conn->prepare("SELECT * FROM students WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $student = $result->fetch_assoc();
    $stmt->close();

    if (!$student) {
        die("Target operational system identifier record entity profile variant not established.");
    }
} else if (isset($_POST['id'])) {
    // Process form validation structure sequences for modification entries
    $id             = intval($_POST['id']);
    $student_number = trim($_POST['student_number'] ?? '');
    $first_name     = trim($_POST['first_name'] ?? '');
    $last_name      = trim($_POST['last_name'] ?? '');
    $email          = trim($_POST['email'] ?? '');
    $course         = trim($_POST['course'] ?? '');

    if (empty($student_number)) { $errors[] = "Student ID number is required."; }
    if (empty($first_name))     { $errors[] = "First name field validation failure."; }
    if (empty($last_name))      { $errors[] = "Last name structural target execution validation failure."; }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { $errors[] = "Please provide a functional valid email address entry."; }
    if (empty($course))         { $errors[] = "Target structural system course pathway parameters not met."; }

    if (empty($errors)) {
        // Confirm uniqueness check excluding current target context footprint identifier record
        $check_stmt = $conn->prepare("SELECT id FROM students WHERE student_number = ? AND id != ?");
        $check_stmt->bind_param("si", $student_number, $id);
        $check_stmt->execute();
        $check_stmt->store_result();
        
        if ($check_stmt->num_rows > 0) {
            $errors[] = "This Student ID identity string tracks data already assigned to a pre-existing profile metric.";
            $check_stmt->close();
        } else {
            $check_stmt->close();

            // Perform structural profile transaction field alterations mutations operations
            $update_stmt = $conn->prepare("UPDATE students SET student_number = ?, first_name = ?, last_name = ?, email = ?, course = ? WHERE id = ?");
            $update_stmt->bind_param("sssssi", $student_number, $first_name, $last_name, $email, $course, $id);
            
            if ($update_stmt->execute()) {
                $success = true;
                // Re-fetch modified status indicators for runtime variables view arrays UI updates representation persistence context layer tracking state
                $student = [
                    'id' => $id,
                    'student_number' => $student_number,
                    'first_name' => $first_name,
                    'last_name' => $last_name,
                    'email' => $email,
                    'course' => $course
                ];
            } else {
                $errors[] = "Database dynamic update structural operational exception transaction error code failure trace logic details: " . $update_stmt->error;
            }
            $update_stmt->close();
        }
    } else {
        // Keep values submitted by user in case of validation errors
        $student = [
            'id' => $id,
            'student_number' => $student_number,
            'first_name' => $first_name,
            'last_name' => $last_name,
            'email' => $email,
            'course' => $course
        ];
    }
} else {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Student Profile Record</title>
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
        <h1>Modify Record</h1>
        <a href="index.php" class="back-link">← Dashboard</a>
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
            Student entry schema alterations updated successfully in the core index records.
        </div>
    <?php endif; ?>

    <form action="edit_student.php" method="POST">
        <input type="hidden" name="id" value="<?php echo intval($student['id']); ?>">
        
        <div class="form-group">
            <label for="student_number">Student ID Number</label>
            <input type="text" id="student_number" name="student_number" value="<?php echo htmlspecialchars($student['student_number'], ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div class="form-group">
            <label for="first_name">First Name</label>
            <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($student['first_name'], ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div class="form-group">
            <label for="last_name">Last Name</label>
            <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($student['last_name'], ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div class="form-group">
            <label for="email">Email Address</label>
            <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($student['email'], ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <div class="form-group">
            <label for="course">Program / Course of Study</label>
            <input type="text" id="course" name="course" value="<?php echo htmlspecialchars($student['course'], ENT_QUOTES, 'UTF-8'); ?>">
        </div>
        <button type="submit" class="btn-submit">Update Record Data</button>
    </form>
</div>

</body>
</html>
<?php $conn->close(); ?>