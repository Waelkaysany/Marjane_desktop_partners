<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/session.php';

$errors = [];
$name = '';
$_SESSION['login_failures'] = $_SESSION['login_failures'] ?? 0;
$locked_until = $_SESSION['locked_until'] ?? 0;
$locked = $locked_until && $locked_until > time();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$locked) {
    $name = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($name === '' || $password === '') {
        $errors[] = 'Please enter your username and password.';
    }
    if (!$errors) {
        $stmt = $pdo->prepare('SELECT id, username, password_hash, full_name, role, status, last_login FROM partners WHERE username = :u LIMIT 1');
        $stmt->execute([':u' => $name]);
        $user = $stmt->fetch();
        $valid = $user && password_verify($password, $user['password_hash']);
        if (!$valid) {
            $_SESSION['login_failures']++;
            if ($_SESSION['login_failures'] >= 5) {
                $_SESSION['locked_until'] = time() + 5 * 60;
            }
            $errors[] = 'Invalid name or password.';
        } elseif ($user['status'] !== 'active') {
            $errors[] = 'Account inactive. Please contact support.';
        } else {
            $_SESSION['login_failures'] = 0; unset($_SESSION['locked_until']);
            $upd = $pdo->prepare('UPDATE partners SET last_login = NOW() WHERE id = :id');
            $upd->execute([':id' => $user['id']]);
            session_login($user);
            header('Location: home.php');
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" href="assets/css/login.css">
	<title>Sign In — Partner</title>
</head>
<body>
	<div class="login-wrap">
		<section class="left">
			<div class="brand">MARJANE Partner</div>
			<div class="card">
				<h1 class="title">Sign In</h1>
				<?php if ($locked): ?><p class="error">Too many attempts. Try again later.</p><?php endif; ?>
				<?php foreach ($errors as $e): ?><p class="error"><?php echo htmlspecialchars($e); ?></p><?php endforeach; ?>
				<form method="post" novalidate>
					<div class="field">
						<label for="username">User Name</label>
						<input class="input" id="username" name="username" type="text" autocomplete="username" required value="<?php echo htmlspecialchars($name); ?>">
					</div>
					<div class="field">
						<label for="password">Password</label>
						<div class="pw">
							<input class="input" id="password" name="password" type="password" autocomplete="current-password" required>
							<button type="button" id="togglePw" class="show" aria-pressed="false">Show</button>
						</div>
						<small class="helper">Use your partner credentials</small>
					</div>
					<button class="btn btn-primary" type="submit">Sign in</button>
				</form>
			</div>
		</section>
		<aside class="right" aria-hidden="true"></aside>
	</div>
	<script>
	(function(){
		const pw=document.getElementById('password');
		const btn=document.getElementById('togglePw');
		if(btn&&pw){btn.addEventListener('click',()=>{const t=pw.type==='text';pw.type=t?'password':'text';btn.setAttribute('aria-pressed',String(!t));btn.textContent=t?'Show':'Hide';pw.focus();});}
	})();
	</script>
</body>
</html>
