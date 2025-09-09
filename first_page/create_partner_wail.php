<?php
// One-time utility to create/update partner 'wail' with password 'wailelkaysany'
// After running successfully, delete this file for security.

require_once __DIR__ . '/includes/db.php';

header('Content-Type: text/html; charset=utf-8');
echo '<h2>Create/Update Partner: wail</h2>';

$username = 'wail';
$plainPassword = 'wailelkaysany';
$fullName = 'Wail';
$email = 'wail@example.com';
$phone = '';
$role = 'partner';
$status = 'active';

try {
    // Detect schema columns
    $hasPasswordHash = (bool)$pdo->query("SHOW COLUMNS FROM partners LIKE 'password_hash'")->fetch();
    $hasPasswordLegacy = (bool)$pdo->query("SHOW COLUMNS FROM partners LIKE 'password'")->fetch();
    $hasCompany = (bool)$pdo->query("SHOW COLUMNS FROM partners LIKE 'company'")->fetch();
    $hasNotes = (bool)$pdo->query("SHOW COLUMNS FROM partners LIKE 'notes'")->fetch();
    $hasCreatedAt = (bool)$pdo->query("SHOW COLUMNS FROM partners LIKE 'created_at'")->fetch();

    if (!$hasPasswordHash && !$hasPasswordLegacy) {
        throw new Exception("partners table has neither 'password_hash' nor 'password' column.");
    }

    // Check if partner exists
    $stmt = $pdo->prepare('SELECT id FROM partners WHERE username = ? LIMIT 1');
    $stmt->execute([$username]);
    $existing = $stmt->fetch();

    $hash = password_hash($plainPassword, PASSWORD_DEFAULT);

    if ($existing) {
        // Build UPDATE dynamically
        $sets = ['status = ?'];
        $vals = [$status];
        if ($hasPasswordHash) { $sets[] = 'password_hash = ?'; $vals[] = $hash; }
        if ($hasPasswordLegacy) { $sets[] = 'password = ?'; $vals[] = $hash; }
        $sets[] = 'full_name = ?'; $vals[] = $fullName;
        $sets[] = 'email = ?'; $vals[] = $email;
        $sets[] = 'phone = ?'; $vals[] = $phone;
        $sets[] = 'role = ?'; $vals[] = $role;
        $vals[] = $username;

        $sql = 'UPDATE partners SET ' . implode(', ', $sets) . ' WHERE username = ?';
        $upd = $pdo->prepare($sql);
        $upd->execute($vals);

        echo '<div style="color:green">✓ Updated existing partner.</div>';
    } else {
        // Build INSERT dynamically
        $cols = ['username','full_name','email','phone','role','status'];
        $vals = [$username,$fullName,$email,$phone,$role,$status];
        if ($hasPasswordHash) { $cols[] = 'password_hash'; $vals[] = $hash; }
        if ($hasPasswordLegacy) { $cols[] = 'password'; $vals[] = $hash; }
        if ($hasCompany) { $cols[] = 'company'; $vals[] = null; }
        if ($hasNotes) { $cols[] = 'notes'; $vals[] = null; }
        if ($hasCreatedAt) { $cols[] = 'created_at'; $vals[] = date('Y-m-d H:i:s'); }

        $placeholders = rtrim(str_repeat('?,', count($cols)), ',');
        $sql = 'INSERT INTO partners (' . implode(',', $cols) . ') VALUES (' . $placeholders . ')';
        $ins = $pdo->prepare($sql);
        $ins->execute($vals);
        echo '<div style="color:green">✓ Inserted new partner.</div>';
    }

    echo '<p><strong>Username:</strong> ' . htmlspecialchars($username) . '</p>';
    echo '<p><strong>Password:</strong> ' . htmlspecialchars($plainPassword) . '</p>';
    echo '<p>Login at <code>first_page/login.php</code>. If this worked, delete <code>first_page/create_partner_wail.php</code>.</p>';
} catch (Throwable $e) {
    http_response_code(500);
    echo '<div style="color:red">✗ Error: ' . htmlspecialchars($e->getMessage()) . '</div>';
}

?>


