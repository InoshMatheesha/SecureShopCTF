FROM php:8.1-apache

# Enable Apache mod_rewrite
RUN a2enmod rewrite

# Install MySQL PDO and MySQLi extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Set working directory
WORKDIR /var/www/html

# Copy application files
COPY . /var/www/html/

# Set proper permissions for uploads directory
RUN chmod 755 /var/www/html/uploads && \
    chown -R www-data:www-data /var/www/html/uploads

# Create .htaccess file to allow file uploads
RUN echo "DirectoryIndex index.php" > /var/www/html/.htaccess

EXPOSE 80