<?php
require_once 'config.php';

// Check if user is logged in and has admin role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    $access_denied = true;
} else {
    $access_denied = false;
    
    // Get the admin flag from database
    $stmt = $pdo->prepare("SELECT value FROM flags WHERE name = 'admin_access'");
    $stmt->execute();
    $flag = $stmt->fetchColumn();
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - SecureShop</title>
    <style>
        :root {
            /* CSS Custom Properties for theming */
            --primary: #3b82f6;
            --primary-dark: #1e40af;
            --secondary: #8b5cf6;
            --accent: #06b6d4;
            --gaming-primary: #f59e0b;
            --success: #10b981;
            --warning: #f59e0b;
            --error: #ef4444;
            --bg: #0f172a;
            --surface: #1e293b;
            --card-bg: rgba(30, 41, 59, 0.8);
            --text: #f8fafc;
            --text-secondary: #cbd5e1;
            --border: rgba(255, 255, 255, 0.1);
            --shadow: rgba(0, 0, 0, 0.2);
            --glow: rgba(59, 130, 246, 0.2);
            --glass-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        [data-theme="light"] {
            --bg: #f8fafc;
            --surface: #ffffff;
            --card-bg: rgba(255, 255, 255, 0.9);
            --text: #1e293b;
            --text-secondary: #64748b;
            --border: rgba(0, 0, 0, 0.1);
            --shadow: rgba(0, 0, 0, 0.1);
            --glow: rgba(59, 130, 246, 0.1);
            --glass-bg: rgba(255, 255, 255, 0.7);
            --glass-border: rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, var(--bg) 0%, var(--surface) 100%);
            color: var(--text);
            line-height: 1.6;
            min-height: 100vh;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .glass-container {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 12px;
            padding: 2rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .glass-container:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 40px var(--shadow);
        }

        .btn-gaming {
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.875rem;
        }

        .btn-gaming:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(59, 130, 246, 0.3);
            background: linear-gradient(135deg, var(--primary-dark), var(--secondary));
        }

        .btn-gaming:active {
            transform: translateY(0);
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }
            
            .glass-container {
                padding: 1rem;
            }
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
            
            <button id="mobile-menu-toggle" style="display: none; background: rgba(255, 255, 255, 0.1); border: none; color: white; font-size: 1.5rem; padding: 0.5rem; border-radius: 8px; cursor: pointer;" onclick="toggleMobileMenu()">☰</button>
            
            <ul id="nav-menu" style="display: flex; list-style: none; gap: 2rem; align-items: center; margin: 0; padding: 0;">
                <li style="display: flex; align-items: center;"><a href="index.php" class="nav-link">Home</a></li>
                <li style="display: flex; align-items: center;"><a href="products.php" class="nav-link">Products</a></li>
                <li style="display: flex; align-items: center;"><a href="search.php" class="nav-link">Search</a></li>
                <?php if (isset($_SESSION['username']) && $_SESSION['username'] !== 'guest'): ?>
                    <li style="display: flex; align-items: center;"><a href="upload.php" class="nav-link">Upload</a></li>
                    <li style="display: flex; align-items: center;"><a href="download_invoice.php" title="Access your purchase invoices" class="nav-link">Invoices</a></li>
                    <?php if ($_SESSION['role'] === 'admin'): ?>
                        <li style="display: flex; align-items: center;"><a href="admin.php" class="nav-link">Admin</a></li>
                    <?php endif; ?>
                    <li style="display: flex; align-items: center;"><a href="logout.php" class="nav-link">Logout (<?= htmlspecialchars($_SESSION['username']) ?>)</a></li>
                <?php else: ?>
                    <li style="display: flex; align-items: center;"><a href="login.php" class="nav-link">Login</a></li>
                    <li style="display: flex; align-items: center;"><a href="register.php" class="nav-link">Register</a></li>
                <?php endif; ?>
                <li style="display: flex; align-items: center;"><button onclick="toggleTheme()" class="theme-toggle">🌙</button></li>
            </ul>
        </nav>
    </header>

    <main>
        <div class="container">
            <div class="glass-container" style="max-width: 800px; margin: 2rem auto;">
                <?php if ($access_denied): ?>
                    <div style="text-align: center; padding: 3rem;">
                        <h1 style="color: var(--primary); margin-bottom: 1rem;">🔒 Access Denied</h1>
                        <p style="font-size: 1.2rem; margin-bottom: 2rem; opacity: 0.8;">
                            You need administrator privileges to access this panel.
                        </p>
                        <a href="login.php" class="btn-gaming">Login as Administrator</a>
                        
                        <div style="margin-top: 3rem; padding: 1.5rem; background: rgba(239, 68, 68, 0.1); border-radius: 8px;">
                            <h3 style="color: rgb(239, 68, 68); margin-bottom: 1rem;">🎯 CTF Challenge: Admin Access</h3>
                            <p style="margin-bottom: 1rem;">You need to find a way to authenticate as an administrator to access this panel.</p>
                            <p><strong>Objective:</strong> Gain admin access and retrieve the admin panel flag.</p>
                            <p><strong>Points:</strong> 25 pts</p>
                        </div>
                    </div>
                <?php else: ?>
                    <div style="text-align: center; padding: 2rem;">
                        <h1 style="color: var(--primary); margin-bottom: 1rem;">
                            🏆 Admin Panel Access Successful!
                        </h1>
                        
                        <div style="background: linear-gradient(135deg, var(--primary), var(--accent)); padding: 2rem; border-radius: 12px; margin: 2rem 0;">
                            <h2 style="color: white; margin-bottom: 1rem;">🎯 Challenge Completed!</h2>
                            <div style="background: rgba(0,0,0,0.2); padding: 1rem; border-radius: 8px; font-family: monospace; font-size: 1.2rem; color: white; font-weight: bold;">
                                <?= htmlspecialchars($flag) ?>
                            </div>
                            <p style="color: white; margin-top: 1rem; opacity: 0.9;">
                                Congratulations! You successfully exploited the SQL injection vulnerability.
                            </p>
                        </div>

                        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin-top: 2rem;">
                            <div class="glass-container">
                                <h3 style="color: var(--primary);">👥 User Management</h3>
                                <p>Manage user accounts and permissions</p>
                                <button class="btn-gaming" style="margin-top: 1rem; font-size: 0.9rem;">Manage Users</button>
                            </div>
                            
                            <div class="glass-container">
                                <h3 style="color: var(--accent);">📊 System Logs</h3>
                                <p>View system activities and security events</p>
                                <button class="btn-gaming" style="margin-top: 1rem; font-size: 0.9rem;">View Logs</button>
                            </div>
                            
                            
                        </div>

                        

                        <div style="margin-top: 2rem;">
                            <h3 style="color: var(--primary);">🎯 Next Challenge</h3>
                            <p>Now that you have admin access, explore other parts of the application:</p>
                            <div style="display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; margin-top: 1rem;">
                                <a href="search.php" class="btn-gaming" style="font-size: 0.9rem;">Try XSS Challenge</a>
                                <a href="upload.php" class="btn-gaming" style="font-size: 0.9rem;">Upload Challenge</a>
                                <a href="download_invoice.php" class="btn-gaming" style="font-size: 0.9rem;">Try IDOR Challenge</a>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </main>

    <footer id="site-footer" 
  style="text-align:center; padding:40px 20px; font-family:'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
         transition:all 0.3s ease; background:linear-gradient(135deg,var(--bg),var(--surface)); color:var(--text);">
  <div style="max-width:800px; margin:0 auto;">
    <h3 style="font-size:1.8rem; margin-bottom:15px; color:var(--primary); text-shadow:0px 0px 8px rgba(59,130,246,0.4);">
      🛡️ SecureShop
    </h3>
    <p style="font-size:1rem; line-height:1.6; color:var(--text); opacity:0.85;">
      Your premier destination for cutting-edge gaming gear and cybersecurity tools. We're committed to providing secure, high-quality products for tech enthusiasts worldwide.
    </p>
  </div>

  <div style="margin-top:25px; font-size:0.9rem; border-top:1px solid var(--border); padding-top:15px; color:var(--text); opacity:0.6;">
    © <?= date("Y") ?> SecureShop. All rights reserved.
  </div>
</footer>




    <!-- Flag Celebration System -->
    <script src="js/flag-celebration.js"></script>
    
    <script>
        // Theme Management System
        class ThemeManager {
            constructor() {
                this.init();
            }

            init() {
                // Load saved theme or default to dark
                const savedTheme = localStorage.getItem('theme') || 'dark';
                this.setTheme(savedTheme);
                
                // Set up theme toggle listeners
                this.setupEventListeners();
            }

            setTheme(theme) {
                document.documentElement.setAttribute('data-theme', theme);
                localStorage.setItem('theme', theme);
                
                // Update toggle button text
                const toggleBtn = document.querySelector('button[onclick="toggleTheme()"]');
                if (toggleBtn) {
                    toggleBtn.textContent = theme === 'dark' ? '☀️' : '🌙';
                }
            }

            toggleTheme() {
                const currentTheme = document.documentElement.getAttribute('data-theme');
                const newTheme = currentTheme === 'dark' ? 'light' : 'dark';
                this.setTheme(newTheme);
            }

            setupEventListeners() {
                // Override the global toggleTheme function
                window.toggleTheme = () => this.toggleTheme();
            }
        }

        // Admin Flag Celebration
        <?php if (!$access_denied && isset($flag)): ?>
        document.addEventListener('DOMContentLoaded', () => {
            // Show celebration when admin flag is visible
            const flagElement = document.querySelector('div[style*="font-family: monospace"]');
            if (flagElement) {
                const flagText = flagElement.textContent.trim();
                if (flagText.includes('THM{')) {
                    // Trigger celebration after a short delay
                    setTimeout(() => {
                        showFlagCelebration(
                            '<?= addslashes($flag) ?>',
                            'SQL Injection',
                            'You successfully exploited SQL injection to bypass authentication and gain admin access!'
                        );
                    }, 800);
                }
            }
        });
        <?php endif; ?>

        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            new ThemeManager();
        });
    </script>
</body>
</html>
