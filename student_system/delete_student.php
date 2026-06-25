<?php
require_once 'db_connect.php';

// Check if parameter entity query keys exist
if (isset($_GET['id'])) {
    // Cast variable assignment values explicitly to integers to drop unwanted data vector inject parameters 
    $id = intval($_GET['id']);

    if ($id > 0) {
        // Construct parametrized statement transaction to clean targets safely
        $stmt = $conn->prepare("DELETE FROM students WHERE id = ?");
        $stmt->bind_param("i", $id);
        
        if ($stmt->execute()) {
            // Execution verified successfully. Release data streams handles context.
            $stmt->close();
            $conn->close();
            // Redirect seamlessly back to view table dashboard
            header('Location: index.php');
            exit;
        } else {
            echo "An operational error occurred while processing the deletion request: " . htmlspecialchars($stmt->error, ENT_QUOTES, 'UTF-8');
            $stmt->close();
        }
    }
} else {
    // Direct routing backup fallback protection rule structure redirection defaults
    header('Location: index.php');
    exit;
}

$conn->close();
?>