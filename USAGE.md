# Usage & Deployment

## Local Development
- Use XAMPP or Docker to run PHP and MySQL locally.
- Place `front_zephyr` and `admin_zephyr` in your webroot.
- Import `zephyr.sql` via phpMyAdmin and update `linc.php` credentials.

## Production Deployment
- Configure a proper webserver (Nginx or Apache) with PHP-FPM.
- Use environment variables for DB credentials and never commit secrets.
- Consider Dockerizing the app for consistent deployments.

## Recommendations
- Serve static assets (CSS/JS/images) from a CDN in production.
- Configure caching headers and a small service worker for offline support.

## Troubleshooting
- If pages render blank, check PHP error logs and database connectivity.
- For permission issues, verify file ownership for the webserver user.
