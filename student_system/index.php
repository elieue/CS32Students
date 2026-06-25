<?php
require_once 'db_connect.php';

// Fetch all student records, ordered by newest addition first
$sql = "SELECT id, student_number, first_name, last_name, email, course FROM students ORDER BY created_at DESC";
$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Student Records</title>
    <style>
        :root {
            --bg-color: #121214;
            --card-bg: #1a1a1e;
            --text-color: #e2e8f0;
            --text-muted: #94a3b8;
            --accent-pink: #f472b6;
            --accent-pink-hover: #ec4899;
            --border-color: #2d2d34;
            --danger: #ef4444;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        body { background-color: var(--bg-color); color: var(--text-color); padding: 2rem 1rem; display: flex; justify-content: center; }
        .container { width: 100%; max-width: 1000px; }
        
        /* Header Dashboard Styling */
        .header-actions { display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem; }
        h1 { font-size: 1.75rem; font-weight: 600; color: #fff; }
        .btn { display: inline-block; background-color: var(--accent-pink); color: #121214; padding: 0.6rem 1.2rem; border-radius: 6px; text-decoration: none; font-weight: 600; font-size: 0.9rem; transition: background-color 0.2s ease; border: none; cursor: pointer; }
        .btn:hover { background-color: var(--accent-pink-hover); }
        
        /* Table View Wrapper */
        .table-responsive { background-color: var(--card-bg); border: 1px solid var(--border-color); border-radius: 8px; overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; text-align: left; }
        th, td { padding: 1rem; border-bottom: 1px solid var(--border-color); }
        th { background-color: #222227; color: var(--accent-pink); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600; }
        td { font-size: 0.95rem; }
        tr:last-child td { border-bottom: none; }
        tr:hover td { background-color: #222227; }

        /* Row Actions Layout */
        .actions { display: flex; gap: 0.5rem; }
        .btn-sm { padding: 0.4rem 0.8rem; font-size: 0.8rem; border-radius: 4px; }
        .btn-edit { background-color: transparent; color: var(--text-color); border: 1px solid var(--border-color); }
        .btn-edit:hover { background-color: var(--border-color); }
        .btn-delete { background-color: transparent; color: var(--danger); border: 1px solid rgba(239, 68, 68, 0.2); }
        .btn-delete:hover { background-color: rgba(239, 68, 68, 0.1); }
        
        .empty-state { text-align: center; padding: 3rem 1rem; color: var(--text-muted); }
    </style>
</head>
<body>

<div class="container">
    <div class="header-actions">
        <h1>Student Records</h1>
        <a href="add_student.php" class="btn">+ Add New Student</a>
    </div>

    <div class="table-responsive">
        <table>
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Email Address</th>
                    <th>Course/Program</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($result && $result->num_rows > 0): ?>
                    <?php while($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($row['student_number'], ENT_QUOTES, 'UTF-8'); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['last_name'] . ', ' . $row['first_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td><?php echo htmlspecialchars($row['course'], ENT_QUOTES, 'UTF-8'); ?></td>
                            <td>
                                <div class="actions">
                                    <a href="edit_student.php?id=<?php echo urlencode($row['id']); ?>" class="btn btn-sm btn-edit">Edit</a>
                                    <a href="delete_student.php?id=<?php echo urlencode($row['id']); ?>" class="btn btn-sm btn-delete" onclick="return confirm('Are you sure you want to permanently delete this record?');">Delete</a>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="empty-state">No student records found in the database.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
<?php $conn->close(); ?>