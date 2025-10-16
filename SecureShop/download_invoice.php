<?php
require_once 'config.php';

// Invoice management system - handles invoice downloads
// Note: This system processes customer invoice requests based on order history

// Mapping: invoice_id => [owner_id, filename, order_date, amount]
$invoices = [
    1 => [1, 'invoice_alice_1.txt', '2025-01-15', 1403.99],  // Alice's invoice
    2 => [2, 'invoice_bob_2.txt', '2025-01-20', 248.38],     // Bob's invoice  
    3 => [3, 'invoice_charlie_3.txt', '2025-01-22', 899.50], // Charlie's invoice
];

$error = '';
$success = '';

if (isset($_GET['invoice_id'])) {
    $invoice_id = (int)$_GET['invoice_id'];
    
    if (isset($invoices[$invoice_id])) {
        $invoice_data = $invoices[$invoice_id];
        $owner_id = $invoice_data[0];
        $filename = $invoice_data[1];
        $filepath = __DIR__ . '/files/' . $filename;
        
        // Basic file existence check
        if (file_exists($filepath)) {
            // Note: Missing authorization check - should verify ownership
            // The system assumes the frontend only shows user's own invoices
            // This creates an IDOR vulnerability when accessed directly
            
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="' . $filename . '"');
            header('Content-Length: ' . filesize($filepath));
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            
            readfile($filepath);
            exit;
        } else {
            $error = 'Invoice file not found.';
        }
    } else {
        $error = 'Invalid invoice ID.';
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Invoice Download - SecureShop</title>
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
            white-space: nowrap;
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

        .alert-error {
            background: rgba(239, 68, 68, 0.1);
            border: 1px solid rgba(239, 68, 68, 0.2);
            color: rgb(239, 68, 68);
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
                <h1 style="color: var(--primary); text-align: center; margin-bottom: 2rem;">📄 Invoice Management</h1>
                
                <?php if ($error): ?>
                    <div class="alert alert-error"><?= htmlspecialchars($error) ?></div>
                <?php endif; ?>
                
                <div style="margin-bottom: 2rem;">
                    <p style="font-size: 1.1rem; margin-bottom: 1rem;">
                        Welcome back, <strong><?= htmlspecialchars($_SESSION['username']) ?></strong>!
                    </p>
                    <p style="opacity: 0.8;">
                        Access your purchase history and download invoices for your records.
                    </p>
                </div>

                <!-- Recent Orders Notice -->
                <div class="glass-container" style="margin: 1.5rem 0; background: rgba(59, 130, 246, 0.08);">
                    <h3 style="color: var(--accent); margin-bottom: 1rem;">📊 Recent Activity</h3>
                    <p style="margin-bottom: 1rem; opacity: 0.9;">
                        System shows <?= count($invoices) ?> total invoices in the database. 
                        You have access to your personal purchase history.
                    </p>
                    <div style="font-size: 0.9rem; opacity: 0.7;">
                        <p><strong>Latest Order ID:</strong> #<?= max(array_keys($invoices)) ?> processed on <?= $invoices[max(array_keys($invoices))][2] ?></p>
                    </div>
                </div>

                <!-- User's Invoice List -->
                <div class="glass-container" style="margin: 1.5rem 0;">
                    <h3 style="color: var(--accent); margin-bottom: 1rem;">Your Invoices</h3>
                    <div style="display: grid; gap: 1rem;">
                        <?php 
                        $user_invoices = [];
                        foreach ($invoices as $id => $data) {
                            if ($data[0] == $_SESSION['user_id']) {
                                $user_invoices[$id] = $data;
                            }
                        }
                        
                        if (empty($user_invoices)): ?>
                            <div style="padding: 2rem; text-align: center; opacity: 0.6;">
                                <p>No invoices found for your account.</p>
                                <p style="font-size: 0.9rem; margin-top: 0.5rem;">Purchase some items to see invoices here!</p>
                            </div>
                        <?php else: ?>
                            <?php foreach ($user_invoices as $invoice_id => $invoice_data): ?>
                                <div style="padding: 1rem; background: rgba(59, 130, 246, 0.05); border-radius: 8px; display: flex; justify-content: space-between; align-items: center;">
                                    <div>
                                        <strong>Invoice #<?= str_pad($invoice_id, 3, '0', STR_PAD_LEFT) ?></strong>
                                        <br><small style="opacity: 0.7;">Date: <?= $invoice_data[2] ?> | Total: $<?= number_format($invoice_data[3], 2) ?></small>
                                    </div>
                                    <a href="?invoice_id=<?= $invoice_id ?>" class="btn-gaming" style="font-size: 0.9rem; padding: 0.5rem 1rem;">Download</a>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Direct Access Form -->
                <div class="glass-container" style="margin: 1.5rem 0;">
                    <h3 style="color: var(--gaming-primary); margin-bottom: 1rem;">Quick Invoice Access</h3>
                    <p style="margin-bottom: 1rem; font-size: 0.9rem; opacity: 0.8;">
                        If you know your invoice ID, you can access it directly below:
                    </p>
                    <form method="GET" style="display: flex; gap: 1rem; align-items: end;">
                        <div style="flex: 1;">
                            <label for="invoice_id" style="display: block; margin-bottom: 0.5rem;">Invoice ID:</label>
                            <input type="number" id="invoice_id" name="invoice_id" class="form-control" 
                                   placeholder="Enter your invoice ID" min="1" required>
                        </div>
                        <button type="submit" class="btn-gaming">Access Invoice</button>
                    </form>
                    <p style="font-size: 0.8rem; opacity: 0.6; margin-top: 0.5rem;">
                        * Invoice IDs are sequential numbers assigned to each order
                    </p>
                </div>

                <!-- Security Tips -->
                <div style="margin-top: 3rem; padding: 1.5rem; background: rgba(34, 197, 94, 0.08); border-radius: 8px;">
                    <h3 style="color: rgb(34, 197, 94); margin-bottom: 1rem;">🔒 Privacy & Security</h3>
                    <p style="margin-bottom: 1rem; opacity: 0.9;">
                        Your invoice data is protected and only accessible to you. Our system ensures:
                    </p>
                    <ul style="margin: 0; padding-left: 1.5rem; opacity: 0.8;">
                        <li>Secure file storage and access controls</li>
                        <li>Encrypted data transmission</li>
                        <li>User authentication and authorization</li>
                        <li>Audit logging for all access attempts</li>
                    </ul>
                    <p style="margin-top: 1rem; font-size: 0.9rem; opacity: 0.7;">
                        If you experience any issues accessing your invoices, please contact our support team.
                    </p>
                </div>
            </div>
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
        
        // IDOR Challenge Detection
        document.addEventListener('DOMContentLoaded', () => {
            new ThemeManager();
            
            // System information for debugging (visible in developer tools)
            console.log("Invoice Management System v2.1");
            console.log("Total invoices in database: " + <?= count($invoices) ?>);
            console.log("User session active for: <?= $_SESSION['username'] ?>");
            
            // IDOR Detection: Check if user accessed someone else's invoice
            const urlParams = new URLSearchParams(window.location.search);
            const requestedInvoiceId = parseInt(urlParams.get('invoice_id'));
            
            if (requestedInvoiceId) {
                const userInvoices = [
                    <?php 
                    $user_invoice_ids = [];
                    foreach ($invoices as $id => $data) {
                        if ($data[0] == $_SESSION['user_id']) {
                            $user_invoice_ids[] = $id;
                        }
                    }
                    echo implode(',', $user_invoice_ids);
                    ?>
                ];
                
                // Check if they successfully accessed someone else's invoice (IDOR exploit)
                if (!userInvoices.includes(requestedInvoiceId) && requestedInvoiceId > 0) {
                    console.log("%c🎯 IDOR VULNERABILITY EXPLOITED!", 
                        "color: #ff6b6b; font-size: 18px; font-weight: bold;");
                    console.log("%cYou've accessed invoice #" + requestedInvoiceId + " which doesn't belong to you!", 
                        "color: #ffd700; font-size: 14px;");
                    
                    // Map invoice IDs to flags
                    const idorFlags = {
                        1: "THM{idor_invoice_2025}",      // Alice's invoice
                        2: "THM{idor_bob_invoice_access_2025}",  // Bob's invoice
                        3: "THM{idor_charlie_invoice_2025}" // Charlie's invoice (if has flag)
                    };
                    
                    const capturedFlag = idorFlags[requestedInvoiceId];
                    
                    if (capturedFlag) {
                        // Show celebration for IDOR exploitation
                        setTimeout(() => {
                            showFlagCelebration(
                                capturedFlag,
                                'IDOR',
                                'You successfully exploited an Insecure Direct Object Reference vulnerability to access unauthorized invoice data!'
                            );
                        }, 1500); // Delay to let download start
                    }
                }
            }
            
            // Add form interactivity
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const invoiceId = document.getElementById('invoice_id').value;
                    if (invoiceId && invoiceId > 0) {
                        console.log("Requesting invoice ID: " + invoiceId);
                    }
                });
            }
        });
    </script>
</body>
</html>
