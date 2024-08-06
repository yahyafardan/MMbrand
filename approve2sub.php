<?php

require 'db.php';
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Check if the user is an app2
if ($_SESSION['role_name'] !== 'app2') {
    echo "Access denied.";
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Decode the incoming JSON data
        $input = json_decode(file_get_contents("php://input"), true);

        $clientName = $input['client_name'] ?? null;
        $month = $input['month'] ?? null;
        $action = $input['action'] ?? null;
        $id = $input['id'] ?? null;
        $notes = $input['rejection_reason'] ?? null;

        if (!$clientName || !$month || !$action) {
            echo 'Invalid input';
            exit;
        }

        if ($action === 'approve' || $action === 'reject') {
            if (!$id) {
                echo 'Invalid input: ID is required for approval/rejection';
                exit;
            }

            if ($action === 'approve') {
                $status = 'pendingC';
                $notes = null; // Clear notes if approving
            } elseif ($action === 'reject') {
                $status = 'rejected';
            } else {
                echo 'Invalid action';
                exit;
            }

            // Update the content status and notes
            $sql = "UPDATE content SET status = :status, notes = :notes WHERE id = :id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                'status' => $status,
                'notes' => $notes,
                'id' => $id
            ]);

            echo 'Success: Status updated.';

            // Check if all records for the client and month are pendingC
            if ($action === 'approve') {
                $checkSql = "SELECT COUNT(*) as total, 
                                     SUM(CASE WHEN status != 'pendingC' THEN 1 ELSE 0 END) as not_pending
                            FROM content 
                            WHERE client_name = :client_name AND month = :month";
                $checkStmt = $pdo->prepare($checkSql);
                $checkStmt->execute(['client_name' => $clientName, 'month' => $month]);
                $checkResult = $checkStmt->fetch(PDO::FETCH_ASSOC);

                if ($checkResult['not_pending'] == 0) {
                    // If all records are pendingC, update them to clientapp1
                    $updateSql = "UPDATE content SET status = 'clientapp1' WHERE client_name = :client_name AND month = :month";
                    $updateStmt = $pdo->prepare($updateSql);
                    $updateStmt->execute(['client_name' => $clientName, 'month' => $month]);

                    echo 'All records updated to client approval 1.';
                }
            }

        } elseif ($action === 'set_pending') {
            // Set all records to pendingC
            $updateSql = "UPDATE content SET status = 'pendingC' WHERE client_name = :client_name AND month = :month";
            $updateStmt = $pdo->prepare($updateSql);
            $updateStmt->execute(['client_name' => $clientName, 'month' => $month]);
            echo 'All records set to pendingC.';

        } elseif ($action === 'check_and_set_design') {
            // Check if all records are pendingC
            $sql = "SELECT COUNT(*) as total, 
                           SUM(CASE WHEN status != 'pendingC' THEN 1 ELSE 0 END) as not_pending
                    FROM content 
                    WHERE client_name = :client_name AND month = :month";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['client_name' => $clientName, 'month' => $month]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($result['not_pending'] > 0) {
                echo 'Cannot set status to "clientapp1" as not all records are pendingC.';
                exit;
            }

            // If all records are pendingC, update to clientapp1
            $updateSql = "UPDATE content SET status = 'clientapp1' WHERE client_name = :client_name AND month = :month";
            $updateStmt = $pdo->prepare($updateSql);
            $updateStmt->execute(['client_name' => $clientName, 'month' => $month]);

            echo 'Status updated to clientapp1.';

        } else {
            echo 'Invalid action';
            exit;
        }

    } catch (PDOException $e) {
        echo 'Error: ' . htmlspecialchars($e->getMessage());
    }
}
?>
