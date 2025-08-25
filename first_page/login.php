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
			<div class="hero-content">
				<div class="hero-quote">A WISE QUOTE</div>
				<h1 class="hero-title">Get Everything<br>You Want</h1>
				<p class="hero-subtitle">You can get everything you want if you work hard, trust the process, and stick to the plan.</p>
			</div>
		</section>
		<aside class="right">
			<div class="card">
				<div class="brand">Cogie</div>
				<h1 class="title">Welcome Back</h1>
				<p>Enter your email and password to access your account</p>
				<?php if ($locked): ?><p class="error">Too many attempts. Try again later.</p><?php endif; ?>
				<?php foreach ($errors as $e): ?><p class="error"><?php echo htmlspecialchars($e); ?></p><?php endforeach; ?>
				<form method="post" novalidate>
					<div class="field">
						<label for="username">Name</label>
						<input class="input" id="username" name="username" type="text" autocomplete="username" placeholder="Enter your full name" required value="<?php echo htmlspecialchars($name); ?>">
					</div>
					<div class="field">
						<label for="password">Password</label>
						<div class="pw">
							<input class="input" id="password" name="password" type="password" autocomplete="current-password" placeholder="Enter your password" required>
							<button type="button" id="togglePw" class="show" aria-pressed="false">👁</button>
						</div>
					</div>
					<div class="field" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
						<label style="display: flex; align-items: center; margin: 0; cursor: pointer;">
							<input type="checkbox" style="margin-right: 0.5rem;"> Remember me
						</label>
						<a href="#" style="color: var(--accent-color); text-decoration: none; font-size: 0.875rem;">Forgot Password?</a>
					</div>
					<button class="btn btn-primary" type="submit">Sign In</button>
				</form>
			</div>
		</aside>
	</div>
	<script>
	(function(){
		const pw=document.getElementById('password');
		const btn=document.getElementById('togglePw');
		if(btn&&pw){
			btn.addEventListener('click',()=>{
				const t=pw.type==='text';
				pw.type=t?'password':'text';
				btn.setAttribute('aria-pressed',String(!t));
				btn.textContent=t?'👁':'👁‍🗨';
				pw.focus();
			});
		}
	})();
	</script>
</body>
</html>
