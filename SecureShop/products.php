<?php
require_once 'config.php';

// Get all products
$stmt = $pdo->query("SELECT * FROM products ORDER BY category, name");
$products = $stmt->fetchAll();

// Group products by category
$categories = [];
foreach ($products as $product) {
    $categories[$product['category']][] = $product;
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products - SecureShop</title>
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

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.6;
            min-height: 100vh;
            transition: var(--transition);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            /* Performance optimizations */
            transform: translateZ(0);
            backface-visibility: hidden;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .glass-container {
            background: var(--card-bg);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            transition: var(--transition);
            animation: fadeInUp 0.6s ease-out;
        }

        .glass-container:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
        }

        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 2rem;
            margin: 2rem 0;
        }

        .product-card {
            background: var(--card-bg);
            backdrop-filter: blur(12px);
            border-radius: var(--border-radius);
            border: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1.5rem;
            transition: var(--transition);
            display: flex;
            flex-direction: column;
            height: 100%;
            position: relative;
            overflow: hidden;
            animation: fadeInUp 0.6s ease-out backwards;
        }

        .product-card:nth-child(1) { animation-delay: 0.1s; }
        .product-card:nth-child(2) { animation-delay: 0.2s; }
        .product-card:nth-child(3) { animation-delay: 0.3s; }
        .product-card:nth-child(4) { animation-delay: 0.4s; }
        .product-card:nth-child(5) { animation-delay: 0.5s; }
        .product-card:nth-child(6) { animation-delay: 0.6s; }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(40px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .product-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, transparent 0%, rgba(59, 130, 246, 0.05) 100%);
            opacity: 0;
            transition: var(--transition);
            z-index: 0;
        }

        .product-card:hover::before {
            opacity: 1;
        }

        .product-card:hover {
            transform: translateY(-12px) scale(1.03);
            box-shadow: 0 20px 60px rgba(59, 130, 246, 0.4);
            border-color: var(--primary);
        }

        .product-card > * {
            position: relative;
            z-index: 1;
        }

        .product-image {
            width: 100%;
            height: 280px;
            background: linear-gradient(135deg, #ffffff, #f8f9fa);
            border-radius: 12px;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
            position: relative;
            padding: 1.5rem;
            box-shadow: inset 0 0 0 1px rgba(0,0,0,0.05);
        }

        [data-theme="dark"] .product-image {
            background: linear-gradient(135deg, #1a1a2e, #16213e);
            box-shadow: inset 0 0 0 1px rgba(255,255,255,0.05);
        }

        .product-image img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: center;
            transition: transform 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
            filter: drop-shadow(0 10px 20px rgba(0,0,0,0.1));
        }

        [data-theme="dark"] .product-image img {
            filter: drop-shadow(0 10px 20px rgba(0,0,0,0.4)) brightness(0.95);
        }

        .product-card:hover .product-image img {
            transform: scale(1.08) translateY(-5px);
        }

        .product-image::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(circle at 50% 120%, rgba(59, 130, 246, 0.08), transparent 70%);
            opacity: 0;
            transition: opacity 0.4s ease;
        }

        .product-card:hover .product-image::before {
            opacity: 1;
        }

        .product-image::after {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, transparent 0%, rgba(59, 130, 246, 0.05) 100%);
            opacity: 0;
            transition: var(--transition);
            pointer-events: none;
        }

        .product-card:hover .product-image::after {
            opacity: 1;
        }

        .product-card h3 {
            color: var(--text);
            margin-bottom: 0.5rem;
            font-size: 1.25rem;
            font-weight: 600;
        }

        .product-card p {
            color: var(--text);
            opacity: 0.8;
            margin-bottom: 1rem;
            flex-grow: 1;
        }

        .btn-gaming {
            background: linear-gradient(135deg, var(--primary), var(--accent));
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 0.875rem;
        }

        [data-theme="dark"] .btn-gaming {
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            box-shadow: 0 4px 15px rgba(59, 130, 246, 0.5);
        }

        .btn-gaming:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(59, 130, 246, 0.3);
        }

        .btn-gaming:active {
            transform: translateY(0);
        }

        header#main-header {
            background: linear-gradient(135deg, hsl(217, 91%, 60%), hsl(193, 95%, 68%));
            backdrop-filter: blur(20px);
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            position: sticky;
            top: 0;
            z-index: 1000;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
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

        /* Responsive Design */
        @media (max-width: 768px) {
            #mobile-menu-toggle {
                display: block !important;
            }

            #nav-menu {
                position: fixed;
                top: 70px;
                left: 0;
                right: 0;
                background: linear-gradient(135deg, hsl(217, 91%, 55%), hsl(193, 95%, 63%));
                flex-direction: column;
                gap: 0;
                padding: 1rem 0;
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3);
                transform: translateY(-100%);
                opacity: 0;
                transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                pointer-events: none;
            }

            [data-theme="dark"] #nav-menu {
                background: linear-gradient(135deg, #0f1419, #1e293b);
                box-shadow: 0 10px 30px rgba(0, 0, 0, 0.6);
            }

            #nav-menu.active {
                transform: translateY(0);
                opacity: 1;
                pointer-events: all;
            }

            #nav-menu li {
                width: 100%;
            }

            #nav-menu li a,
            #nav-menu li button {
                display: block;
                width: 100%;
                text-align: center;
                padding: 1rem !important;
                border-radius: 0 !important;
            }

            .logo-text {
                display: none;
            }

            .container {
                padding: 1rem;
            }

            .product-grid {
                grid-template-columns: 1fr;
                gap: 1.5rem;
            }

            .product-card {
                padding: 1rem;
            }
        }
        
        /* Creative Footer Styles */
        .footer {
            background: linear-gradient(135deg, var(--card-bg), rgba(15, 20, 25, 0.95));
            backdrop-filter: blur(20px);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 4rem;
            position: relative;
            overflow: hidden;
        }
        [data-theme="dark"] .footer {
            background: linear-gradient(135deg, rgba(15, 20, 25, 0.95), rgba(30, 41, 59, 0.9));
            border-top: 1px solid rgba(139, 92, 246, 0.2);
        }
        .footer::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 2px;
            background: linear-gradient(90deg, var(--primary), var(--accent), var(--gaming-primary), var(--primary));
            animation: footerGlow 3s ease-in-out infinite alternate;
        }
        @keyframes footerGlow {
            0% { opacity: 0.6; }
            100% { opacity: 1; }
        }
        .footer-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 3rem 2rem 2rem;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 2rem;
        }
        .footer-section h3 {
            color: var(--text);
            font-size: 1.2rem;
            font-weight: 600;
            margin-bottom: 1rem;
            position: relative;
            padding-bottom: 0.5rem;
        }
        .footer-section h3::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 30px;
            height: 2px;
            background: linear-gradient(90deg, var(--primary), var(--accent));
            border-radius: 1px;
        }
        .footer-section p, .footer-section li {
            color: rgba(var(--text-light), 0.8);
            line-height: 1.6;
            margin-bottom: 0.5rem;
            font-size: 0.9rem;
        }
        [data-theme="dark"] .footer-section p,
        [data-theme="dark"] .footer-section li {
            color: rgba(210, 214, 220, 0.8);
        }
        .footer-section ul {
            list-style: none;
            padding: 0;
        }
        .footer-section a {
            color: inherit;
            text-decoration: none;
            transition: var(--transition);
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }
        .footer-section a:hover {
            color: var(--primary);
            transform: translateX(5px);
        }
        .social-links {
            display: flex;
            gap: 1rem;
            margin-top: 1rem;
        }
        .social-link {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transition: var(--transition);
            font-size: 1.2rem;
        }
        .social-link:hover {
            background: var(--primary);
            transform: translateY(-3px) scale(1.1);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
        }
        [data-theme="dark"] .social-link:hover {
            background: var(--gaming-primary);
            box-shadow: 0 8px 25px rgba(139, 92, 246, 0.4);
        }
        .newsletter {
            background: rgba(255, 255, 255, 0.05);
            padding: 1.5rem;
            border-radius: var(--border-radius);
            border: 1px solid rgba(255, 255, 255, 0.1);
            margin-top: 1rem;
        }
        [data-theme="dark"] .newsletter {
            background: rgba(139, 92, 246, 0.1);
            border-color: rgba(139, 92, 246, 0.2);
        }
        .newsletter-form {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }
        .newsletter-input {
            flex: 1;
            padding: 0.75rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.1);
            color: var(--text);
            font-size: 0.9rem;
        }
        .newsletter-input::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }
        [data-theme="dark"] .newsletter-input::placeholder {
            color: rgba(210, 214, 220, 0.6);
        }
        .newsletter-btn {
            padding: 0.75rem 1.5rem;
            background: linear-gradient(135deg, var(--primary), var(--accent));
            border: none;
            border-radius: 8px;
            color: white;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            font-size: 0.9rem;
        }
        .newsletter-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(59, 130, 246, 0.4);
        }
        .footer-bottom {
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding: 1.5rem 2rem;
            text-align: center;
            background: rgba(0, 0, 0, 0.2);
        }
        [data-theme="dark"] .footer-bottom {
            border-top-color: rgba(139, 92, 246, 0.2);
            background: rgba(0, 0, 0, 0.4);
        }
        .footer-bottom p {
            margin: 0;
            color: rgba(var(--text-light), 0.7);
            font-size: 0.9rem;
        }
        [data-theme="dark"] .footer-bottom p {
            color: rgba(210, 214, 220, 0.7);
        }
        .tech-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            background: rgba(255, 255, 255, 0.1);
            padding: 0.3rem 0.8rem;
            border-radius: 15px;
            font-size: 0.8rem;
            margin: 0.2rem;
            transition: var(--transition);
        }
        .tech-badge:hover {
            background: var(--primary);
            transform: scale(1.05);
        }
        [data-theme="dark"] .tech-badge:hover {
            background: var(--gaming-primary);
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
            <h1 style="text-align: center; margin: 2rem 0; color: var(--primary);">Our Products</h1>
            
            <?php foreach ($categories as $category => $category_products): ?>
                <section style="margin: 3rem 0;">
                    <h2 style="color: var(--accent); margin-bottom: 1.5rem; text-transform: capitalize;">
                        <?= htmlspecialchars($category) ?>
                    </h2>
                    
                    <div class="product-grid">
                        <?php 
                        // Map product names to images
                        $productImages = [
                            'Gaming Laptop Pro' => 'Gaming Laptop Pro.png',
                            'Mechanical Gaming Keyboard' => 'Mechanical Gaming Keyboard.png',
                            'Gaming Mouse Ultra' => 'Gaming Mouse Ultra.png',
                            'Gaming Headset Pro' => 'Gaming Headset Pro.png',
                            'Graphics Card RTX' => 'Graphics Card RTX..png',
                            '4K Gaming Monitor' => '4K Gaming Monitor.png'
                        ];
                        
                        foreach ($category_products as $product): 
                            $imagePath = 'images/' . ($productImages[$product['name']] ?? 'Welcome.jpg');
                        ?>
                            <div class="product-card">
                                <div class="product-image">
                                    <img src="<?= htmlspecialchars($imagePath) ?>" alt="<?= htmlspecialchars($product['name']) ?>" loading="lazy">
                                </div>
                                <h3><?= htmlspecialchars($product['name']) ?></h3>
                                <p><?= htmlspecialchars($product['description']) ?></p>
                                <div style="margin-top: auto; display: flex; justify-content: space-between; align-items: center; gap: 1rem;">
                                    <span style="font-size: 1.5rem; font-weight: bold; background: linear-gradient(135deg, var(--primary), var(--accent)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text;">
                                        $<?= number_format($product['price'], 2) ?>
                                    </span>
                                    <button class="btn-gaming" style="font-size: 0.9rem; padding: 0.5rem 1rem;">Add to Cart</button>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </section>
            <?php endforeach; ?>
            
            <!-- Customer Testimonials Section -->
            <section style="margin: 4rem 0; padding: 2rem; background: rgba(59, 130, 246, 0.05); border-radius: 12px;">
                <h2 style="color: var(--accent); margin-bottom: 2rem; text-align: center;">What Our Customers Say</h2>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 2rem;">
                    <div class="glass-container" style="padding: 1.5rem;">
                        <div style="margin-bottom: 1rem;">
                            <strong>Mahinda Rajapaksha</strong> ⭐⭐⭐⭐⭐
                            <div style="font-size: 0.8rem; opacity: 0.6;">Order #001 - Gaming Laptop Pro</div>
                        </div>
                        <p style="font-style: italic; opacity: 0.9;">"Amazing gaming laptop! Fast delivery and great customer service. The invoice was clear and professional."</p>
                    </div>
                    
                    <div class="glass-container" style="padding: 1.5rem;">
                        <div style="margin-bottom: 1rem;">
                            <strong>Anura Kumara</strong> ⭐⭐⭐⭐⭐
                            <div style="font-size: 0.8rem; opacity: 0.6;">Order #002 - Gaming Accessories</div>
                        </div>
                        <p style="font-style: italic; opacity: 0.9;">"Love my new keyboard and mouse! SecureShop makes ordering easy and I can always download my invoices when needed for my records."</p>
                    </div>
                    
                    <div class="glass-container" style="padding: 1.5rem;">
                        <div style="margin-bottom: 1rem;">
                            <strong>Sajith Premadhasa</strong> ⭐⭐⭐⭐⭐
                            <div style="font-size: 0.8rem; opacity: 0.6;">Order #003 - Gaming Headset & Accessories</div>
                        </div>
                        <p style="font-style: italic; opacity: 0.9;">"Excellent service! The headset quality is top-notch. Their invoice system is convenient for expense tracking."</p>
                    </div>
                </div>
                
                <div style="text-align: center; margin-top: 2rem; padding-top: 2rem; border-top: 1px solid rgba(255,255,255,0.1);">
                    <p style="opacity: 0.7; font-size: 0.9rem;">
                        📧 Need your purchase receipt? All registered customers can access their invoices through our secure portal.
                        <br><a href="download_invoice.php" style="color: var(--accent); text-decoration: none;">Visit Invoice Center →</a>
                    </p>
                </div>
            </section>
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
                const toggles = document.querySelectorAll('[onclick="toggleTheme()"]');
                toggles.forEach(toggle => {
                    const currentTheme = document.documentElement.getAttribute('data-theme');
                    toggle.textContent = currentTheme === 'light' ? '🌙' : '☀️';
                });
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
            
            // Mobile menu toggle
            window.toggleMobileMenu = function() {
                const menu = document.getElementById('nav-menu');
                menu.classList.toggle('active');
            };
            
            // Close mobile menu when clicking outside
            document.addEventListener('click', function(event) {
                const menu = document.getElementById('nav-menu');
                const toggle = document.getElementById('mobile-menu-toggle');
                
                if (menu && toggle && !menu.contains(event.target) && !toggle.contains(event.target)) {
                    menu.classList.remove('active');
                }
            });
        })();
    </script>
</body>
</html>
