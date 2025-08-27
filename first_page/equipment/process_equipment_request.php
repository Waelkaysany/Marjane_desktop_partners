<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/session.php';

// Ensure user is authenticated
require_auth();

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

// Verify CSRF token
if (!csrf_verify($_POST['csrf'] ?? '')) {
    http_response_code(403);
    echo json_encode(['error' => 'Invalid CSRF token']);
    exit;
}

try {
    // Get equipment database connection
    $pdoEquipment = getEquipmentDbConnection();
    
    // Get partner ID from session
    $partnerId = $_SESSION['auth']['partner_id'];
    
    // Get equipment details from POST data
    $equipmentId = $_POST['equipment_id'] ?? null;
    $equipmentName = $_POST['equipment_name'] ?? '';
    $equipmentType = $_POST['equipment_type'] ?? '';
    $price = $_POST['price'] ?? '';
    $notes = $_POST['notes'] ?? '';
    
    if (!$equipmentId) {
        throw new Exception('Equipment ID is required');
    }
    
    // Insert the equipment request into the database
    $stmt = $pdoEquipment->prepare("
        INSERT INTO requests (partner_id, equipement_id, time, status) 
        VALUES (?, ?, NOW(), 'pending')
    ");
    
    $stmt->execute([$partnerId, $equipmentId]);
    
    // Get the inserted record ID
    $requestId = $pdoEquipment->lastInsertId();
    
    // Return success response
    echo json_encode([
        'success' => true,
        'message' => 'Equipment request submitted successfully',
        'request_id' => $requestId,
        'reference' => 'REQ-' . date('Y') . '-' . str_pad($requestId, 3, '0', STR_PAD_LEFT)
    ]);
    
} catch (Exception $e) {
    error_log("Equipment request error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode([
        'error' => 'Failed to submit equipment request',
        'message' => $e->getMessage()
    ]);
}
?>
