<?php
require_once 'config.php';

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Basic validation
    if (empty($username) || empty($email) || empty($password)) {
        $error = 'All fields are required.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters long.';
    } else {
        try {
            // Check if username already exists
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ?");
            $stmt->execute([$username]);
            if ($stmt->fetch()) {
                $error = 'Username already exists.';
            } else {
                // Hash password and insert user
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
                $stmt->execute([$username, $email, $hashed_password]);
                
                $message = 'Registration successful! You can now login.';
            }
        } catch (PDOException $e) {
            $error = 'Registration failed: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - SecureShop</title>
    <style>
        :root {
            --primary: hsl(217, 91%, 60%);
            --primary-glow: hsl(217, 91%, 70%);
            --accent: hsl(193, 95%, 68%);
            --accent-glow: hsl(193, 95%, 78%);
            --gaming-primary: hsl(271, 91%, 65%);
            --bg-light: hsl(210, 40%, 98%);
            --text-light: hsl(210, 40%, 15%);
            --card-light: rgba(255, 255, 255, 0.8);
            --bg-dark: hsl(220, 26%, 6%);
            --text-dark: hsl(210, 40%, 95%);
            --card-dark: rgba(30, 30, 45, 0.8);
            --bg: var(--bg-light);
            --text: var(--text-light);
            --card-bg: var(--card-light);
            --border-radius: 12px;
            --shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            --glow-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        [data-theme="dark"] {
            --bg: var(--bg-dark);
            --text: var(--text-dark);
            --card-bg: var(--card-dark);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
            transition: var(--transition);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        .container { max-width: 1200px; margin: 0 auto; padding: 0 2rem; }
        .form-container {
            background: var(--card-bg); backdrop-filter: blur(12px); border-radius: var(--border-radius);
            border: 1px solid rgba(255, 255, 255, 0.2); padding: 2rem; box-shadow: var(--shadow);
            transition: var(--transition); max-width: 500px; margin: 2rem auto;
        }
        .form-group { margin-bottom: 1.5rem; }
        .form-group label {
            display: block; margin-bottom: 0.5rem; font-weight: 600; color: var(--text);
        }
        .form-control {
            width: 100%; padding: 0.8rem 1rem; border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 8px; font-size: 1rem; transition: var(--transition);
            background: var(--card-bg); color: var(--text); backdrop-filter: blur(8px);
        }
        .form-control:focus {
            outline: none; border-color: var(--primary); box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
            transform: translateY(-2px);
        }
        .btn, .btn-gaming {
            background: linear-gradient(135deg, var(--primary), var(--accent)); color: white;
            border: none; padding: 0.8rem 2rem; border-radius: var(--border-radius);
            font-size: 1rem; font-weight: 600; cursor: pointer; transition: var(--transition);
            width: 100%; text-align: center; text-decoration: none; display: inline-block;
        }
        .btn:hover, .btn-gaming:hover { transform: translateY(-2px); box-shadow: var(--glow-shadow); }
        .btn:active, .btn-gaming:active { transform: translateY(0); }
        .alert {
            padding: 1rem; border-radius: 8px; margin-bottom: 1rem; font-weight: 500;
        }
        .alert-error {
            background: rgba(239, 68, 68, 0.1); color: #ef4444; border: 1px solid rgba(239, 68, 68, 0.2);
        }
        .alert-success {
            background: rgba(34, 197, 94, 0.1); color: #22c55e; border: 1px solid rgba(34, 197, 94, 0.2);
        }
        @media (max-width: 768px) {
            .container { padding: 0 1rem; }
            .form-container { margin: 1rem auto; padding: 1.5rem; }
        }
        
        /* Improved Header Styles */
        header#main-header {
            background: linear-gradient(135deg, hsl(217, 91%, 60%), hsl(193, 95%, 68%));
            backdrop-filter: blur(20px); border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            position: sticky; top: 0; z-index: 1000; transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        [data-theme="dark"] header#main-header {
            background: linear-gradient(135deg, #0f1419, #1e293b);
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.4);
        }
        .nav-link {
            color: white; text-decoration: none; font-weight: 500; 
            padding: 0.5rem 1rem; border-radius: 8px; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
            position: relative; overflow: hidden;
        }
        .nav-link:hover {
            background: rgba(255, 255, 255, 0.1); 
            transform: translateY(-2px); 
            box-shadow: 0 0 20px rgba(59, 130, 246, 0.3);
        }
        [data-theme="dark"] .nav-link:hover {
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 0 20px rgba(139, 92, 246, 0.4);
        }
        .theme-toggle {
            background: rgba(255, 255, 255, 0.1); 
            border: 1px solid rgba(255, 255, 255, 0.2); 
            border-radius: 50px; padding: 0.5rem 1rem; 
            color: white; cursor: pointer; 
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
            font-size: 1rem;
        }
        .theme-toggle:hover {
            background: rgba(255, 255, 255, 0.2); 
            transform: scale(1.05);
        }
        [data-theme="dark"] .theme-toggle {
            background: rgba(139, 92, 246, 0.2);
            border-color: rgba(139, 92, 246, 0.3);
        }
        [data-theme="dark"] .theme-toggle:hover {
            background: rgba(139, 92, 246, 0.3);
            box-shadow: 0 0 15px rgba(139, 92, 246, 0.4);
        }
    </style>
</head>
<body>
    <header id="main-header">
        <nav style="max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; padding: 1rem 2rem;">
            <a href="index.php" style="font-size: 1.8rem; font-weight: bold; color: white; text-decoration: none; display: flex; align-items: center; gap: 0.5rem; text-shadow: 0 0 10px rgba(255, 255, 255, 0.3); margin-left: -1rem;">
                <img src="assets/logo.png?v=20251002" alt="SecureShop Logo" style="width: 40px; height: 40px; object-fit: contain;">
                <span class="logo-text">SecureShop</span>
            </a>
            
            <ul style="display: flex; list-style: none; gap: 2rem; align-items: center; margin: 0; padding: 0;">
                <li style="display: flex; align-items: center;"><a href="index.php" class="nav-link">Home</a></li>
                <li style="display: flex; align-items: center;"><a href="products.php" class="nav-link">Products</a></li>
                <li style="display: flex; align-items: center;"><a href="search.php" class="nav-link">Search</a></li>
                <li style="display: flex; align-items: center;"><a href="login.php" class="nav-link">Login</a></li>
                <li style="display: flex; align-items: center;"><button onclick="toggleTheme()" class="theme-toggle">🌙</button></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="container">
            <div class="form-container glass-container">
                <h2 style="text-align: center; margin-bottom: 2rem; color: var(--primary);">Create Account</h2>
                
                <?php if ($error): ?>
                    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <?php if ($message): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" class="form-control" 
                               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" 
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn-gaming" style="width: 100%;">Create Account</button>
                    </div>
                </form>
                
                <div style="text-align: center; margin-top: 1.5rem;">
                    <p>Already have an account? <a href="login.php" style="color: var(--primary);">Login here</a></p>
                </div>
            </div>
        </div>
    </main>

    <footer style="text-align: center; padding: 40px 20px; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; transition: all 0.3s ease;" id="site-footer">
  <div style="max-width: 800px; margin: 0 auto;">
    <h3 style="font-size: 1.8rem; margin-bottom: 15px; color: var(--primary); text-shadow: 0px 0px 8px rgba(59,130,246,0.4);">🛡️ SecureShop</h3>
    <p style="font-size: 1rem; line-height: 1.6; color: var(--text); opacity: 0.85;">
      Your premier destination for cutting-edge gaming gear and cybersecurity tools. We're committed to providing secure, high-quality products for tech enthusiasts worldwide.
    </p>
  </div>

  <div style="margin-top: 25px; font-size: 0.9rem; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 15px; color: var(--text); opacity: 0.6;">
    © <?= date("Y") ?> SecureShop. All rights reserved.
  </div>
</footer>

<script>
  function toggleTheme() {
    const html = document.documentElement;
    const current = html.getAttribute("data-theme");
    const next = current === "dark" ? "light" : "dark";
    html.setAttribute("data-theme", next);

    // Footer reacts to theme (no need for extra CSS file)
    const footer = document.getElementById("site-footer");
    if (next === "dark") {
      footer.style.background = "linear-gradient(135deg, #0d1117, #161b22)";
      footer.style.color = "#f5f5f5";
    } else {
      footer.style.background = "linear-gradient(135deg, #f9f9f9, #e5e7eb)";
      footer.style.color = "#111827";
    }
  }

  // Run once on load to match default theme
  document.addEventListener("DOMContentLoaded", () => {
    toggleTheme();
    toggleTheme(); // run twice so it syncs without flipping
  });
</script>



    <script>
        // Theme management - Inline JavaScript
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
            
            function updateThemeToggle() {
                const toggle = document.querySelector('.theme-toggle');
                if (toggle) {
                    const currentTheme = document.documentElement.getAttribute('data-theme');
                    toggle.textContent = currentTheme === 'light' ? '🌙' : '☀️';
                }
            }
            
            document.addEventListener('DOMContentLoaded', updateThemeToggle);
            
            window.toggleTheme = function() {
                const html = document.documentElement;
                const currentTheme = html.getAttribute('data-theme');
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                
                html.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                updateThemeToggle();
            };
        })();

        // CTF debugging hints
        console.log("📝 Registration page loaded");
        console.log("🎯 CTF Tip: Create accounts to explore user features");
    </script>
</body>
</html>
