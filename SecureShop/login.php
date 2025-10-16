<?php
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please fill in all fields.';
    } else {
        try {
            // VULNERABILITY: SQL Injection in authentication
            // Legacy code with direct string concatenation
            $sql = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
            
            // Execute the vulnerable query
            $result = $pdo->query($sql);
            
            if ($result) {
                $user = $result->fetch();
                
                if ($user) {
                    // SQL injection worked or plain text password matched
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['role'] = $user['role'];
                    
                    $success = 'Login successful! Redirecting...';
                    
                    // Redirect to admin panel if admin role
                    if ($user['role'] === 'admin') {
                        header('Location: admin.php');
                        exit;
                    } else {
                        header('Location: index.php');
                        exit;
                    }
                } else {
                    // If vulnerable query failed, try secure method for regular users
                    // This allows both SQL injection AND normal password verification
                    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
                    $stmt->execute([$username]);
                    $user_secure = $stmt->fetch();
                    
                    if ($user_secure && password_verify($password, $user_secure['password'])) {
                        $_SESSION['user_id'] = $user_secure['id'];
                        $_SESSION['username'] = $user_secure['username'];
                        $_SESSION['role'] = $user_secure['role'];
                        
                        $success = 'Login successful! Redirecting...';
                        
                        if ($user_secure['role'] === 'admin') {
                            header('Location: admin.php');
                            exit;
                        } else {
                            header('Location: index.php');
                            exit;
                        }
                    } else {
                        $error = 'Invalid username or password.';
                    }
                }
            } else {
                $error = 'Query execution failed.';
            }
        } catch (PDOException $e) {
            // In CTF, we show SQL errors for educational purposes
            $error = 'SQL Error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SecureShop</title>
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
                <li style="display: flex; align-items: center;"><a href="register.php" class="nav-link">Register</a></li>
                <li style="display: flex; align-items: center;"><button onclick="toggleTheme()" class="theme-toggle">🌙</button></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="container">
            <div class="form-container">
                <h2 style="text-align: center; margin-bottom: 2rem; color: var(--primary);">Login to SecureShop</h2>
                
                <?php if ($error): ?>
                    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input type="text" id="username" name="username" class="form-control" 
                               value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <button type="submit" class="btn-gaming" style="width: 100%;">Login</button>
                    </div>
                </form>
                
                <div style="text-align: center; margin-top: 1.5rem;">
                    <p>Don't have an account? <a href="register.php" style="color: var(--primary);">Register here</a></p>
                </div>
                
                <!-- Help Section -->
                <div style="margin-top: 2rem; padding: 1rem; background: rgba(59, 130, 246, 0.05); border-radius: 8px; font-size: 0.9rem;">
                    <h4 style="color: var(--primary); margin-bottom: 0.5rem;">🔐 Login Help</h4>
                    <p style="margin-bottom: 1rem; opacity: 0.8;">
                        <strong>Test accounts:</strong> alice/password, bob/password<br>
                        <strong>New accounts:</strong> Register and login normally with your credentials
                    </p>
                    <details style="margin-bottom: 0.5rem;">
                        <summary style="cursor: pointer; color: var(--accent);">💡 Hint (Advanced Users)</summary>
                        <p style="margin-top: 0.5rem; color: var(--text); opacity: 0.8;">The login system may have legacy security issues. Admin access might be possible through SQL injection techniques using comment operators like '--'</p>
                    </details>
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



    <!-- Flag Celebration System -->
    <script src="js/flag-celebration.js"></script>
    
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

        // SQL Injection Detection
        document.addEventListener('DOMContentLoaded', () => {
            const form = document.querySelector('form');
            const usernameInput = document.getElementById('username');
            const passwordInput = document.getElementById('password');
            
            if (form) {
                form.addEventListener('submit', (e) => {
                    const username = usernameInput.value;
                    const password = passwordInput.value;
                    
                    // Detect common SQL injection patterns
                    const sqlPatterns = [
                        /--/,                    // SQL comment
                        /;.*DROP/i,              // Drop statement
                        /'.*OR.*'/i,             // OR injection
                        /UNION.*SELECT/i,        // UNION injection
                        /'.*=.*'/i,              // Always true
                        /admin.*--/i,            // Admin bypass
                    ];
                    
                    let detectedPattern = false;
                    sqlPatterns.forEach(pattern => {
                        if (pattern.test(username) || pattern.test(password)) {
                            detectedPattern = true;
                        }
                    });
                    
                    if (detectedPattern) {
                        console.log("%c🎯 SQL INJECTION ATTEMPT DETECTED!", 
                            "color: #ff6b6b; font-size: 18px; font-weight: bold;");
                        console.log("%cPayload: " + username + " / " + password, 
                            "color: #ffd700; font-size: 14px;");
                        console.log("%cIf successful, you'll be redirected to admin panel with the flag!", 
                            "color: #10b981; font-size: 12px;");
                    }
                });
            }
            
            // Check if redirect is happening (success message visible)
            const successAlert = document.querySelector('.alert-success');
            if (successAlert) {
                console.log("%c✅ LOGIN SUCCESSFUL!", 
                    "color: #10b981; font-size: 20px; font-weight: bold;");
                console.log("%c🏆 Redirecting to admin panel...", 
                    "color: #ffd700; font-size: 14px;");
            }
        });

        // CTF debugging hints
        console.log("🔐 Login page loaded");
        console.log("💡 Try different SQL injection techniques");
        console.log("🎯 Goal: Bypass authentication and login as admin");
        console.log("📝 Example payload: admin' -- ");
    </script>
</body>
</html>
