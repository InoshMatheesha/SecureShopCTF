# SecureShop - Static Preview Site

This folder contains a static HTML version of the SecureShop homepage, designed for GitHub Pages hosting.

## 🌐 Purpose

Since GitHub Pages cannot run PHP applications, this static version provides a **preview** of the SecureShop design and layout for visitors to your repository.

## 📁 Contents

- `index.html` - Static homepage with hardcoded products
- `images/` - Product images and hero image
- `assets/` - Logo files

## 🚀 Viewing the Site

Once you enable GitHub Pages (Settings → Pages → Deploy from branch → main → /docs), the site will be available at:

```
https://inoshmatheesha.github.io/SecureShopCTF/
```

## 🔧 Full Application

The complete PHP application with all dynamic features (login, admin, upload, search, etc.) is in the `/SecureShop` directory. To run the full application:

1. Use Docker:
   ```bash
   docker build -t secureshop .
   docker run -p 8080:80 secureshop
   ```

2. Or deploy to a PHP-capable host (Render, Fly.io, Heroku, etc.)

## ⚠️ Note

This is a **static snapshot** for demonstration purposes only. Interactive features (login, search, admin panel, etc.) require the PHP backend and are not available in this static version.
