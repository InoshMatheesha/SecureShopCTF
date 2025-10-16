# SecureShopCTF

🛡️ **SecureShop** - A modern gaming & tech e-commerce platform with built-in CTF challenges.

## 🌐 Live Preview

**GitHub Pages (Static Demo):** [https://inoshmatheesha.github.io/SecureShopCTF/](https://inoshmatheesha.github.io/SecureShopCTF/)

> ⚠️ The GitHub Pages site is a static HTML preview. For the full PHP application with dynamic features, see the deployment instructions below.

## 🚀 Features

- Modern gaming-themed UI with light/dark mode
- Product catalog with search functionality
- User authentication and role-based access control
- Admin panel for management
- File upload system
- Invoice management
- CTF challenges integrated throughout

## 📦 Quick Start

### Using Docker (Recommended)

```bash
# Clone the repository
git clone https://github.com/InoshMatheesha/SecureShopCTF.git
cd SecureShopCTF

# Build and run with Docker
docker build -t secureshop .
docker run -p 8080:80 secureshop
```

Then visit `http://localhost:8080`

### Manual Setup

Requires PHP 7.4+ and a web server (Apache/Nginx).

1. Clone the repository
2. Configure your web server to serve the `/SecureShop` directory
3. Ensure PHP is installed and configured
4. Access the site through your web server

## 🎯 CTF Challenges

This application contains various security challenges for educational purposes. Explore the site to discover vulnerabilities and hidden flags!

## 📂 Project Structure

```
SecureShopCTF/
├── SecureShop/          # Main PHP application
├── docs/                # Static GitHub Pages site
├── cleanup.ps1          # Cleanup script for sensitive files
├── .gitignore           # Git ignore rules
└── README.md            # This file
```

## 🔐 Security Notice

This is a **CTF (Capture The Flag)** application intentionally containing vulnerabilities for educational purposes. **Do not deploy this in a production environment.**

## 📝 License

This project is for educational purposes only.