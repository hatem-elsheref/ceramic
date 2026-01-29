# Production Setup Guide

This guide helps you set up a clean, production-ready environment with public storage and no cache.

## Quick Setup (Recommended)

Run the production reset command:

```bash
php artisan production:reset --fresh
```

This command will:
- ✅ Clear all caches
- ✅ Run fresh migrations
- ✅ Seed production-ready data
- ✅ Set storage to public directory
- ✅ Create storage link
- ✅ Clear cache again after seeding

## Manual Setup

### Step 1: Clean Everything

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Clear database (optional - if you want fresh start)
php artisan migrate:fresh
```

### Step 2: Seed Production Data

```bash
php artisan db:seed
```

This will:
- Clear all existing data
- Set storage to public directory
- Clear all caches
- Seed production-ready data:
  - Admin users
  - Categories and brands
  - Products
  - Banners
  - Static pages
  - Business settings

### Step 3: Create Storage Link

```bash
php artisan storage:link
```

This creates a symbolic link from `public/storage` to `storage/app/public` so uploaded files are accessible via web.

### Step 4: Verify Storage Configuration

Ensure storage is set to public (not S3):

1. Go to Admin Panel → Settings → Third Party → Storage Connection Settings
2. Verify "Storage Connection Type" is set to **Public**
3. If it's set to S3, toggle it to Public

## Storage Configuration

### Public Storage (Default)

Files are stored in: `storage/app/public/`
- Products: `storage/app/public/product/`
- Categories: `storage/app/public/category/`
- Brands: `storage/app/public/brand/`
- Banners: `storage/app/public/banner/`
- Company logos: `storage/app/public/company/`

### Accessing Files

Files are accessible via: `http://your-domain.com/storage/product/image.jpg`

## Cache Management

### Disable Cache (Development)

Add to `.env`:
```env
CACHE_DRIVER=array
```

### Clear Cache Commands

```bash
# Clear all caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Or use the production reset command
php artisan production:reset
```

## Production Checklist

- [ ] Run `php artisan production:reset --fresh`
- [ ] Verify storage is set to public
- [ ] Verify storage link exists (`public/storage` → `storage/app/public`)
- [ ] Check that images are uploading to `storage/app/public/`
- [ ] Verify cache is cleared
- [ ] Test file uploads
- [ ] Verify images are accessible via web

## Troubleshooting

### Images Not Showing

1. Check storage link exists:
   ```bash
   ls -la public/storage
   ```

2. Recreate storage link:
   ```bash
   php artisan storage:link --force
   ```

3. Check file permissions:
   ```bash
   chmod -R 755 storage/app/public
   chown -R www-data:www-data storage/app/public
   ```

### Cache Issues

1. Clear all caches:
   ```bash
   php artisan cache:clear
   php artisan config:clear
   ```

2. Check `.env` for cache driver:
   ```env
   CACHE_DRIVER=file  # or 'array' for no cache
   ```

### Storage Not Public

1. Check database:
   ```sql
   SELECT * FROM business_settings WHERE type = 'storage_connection_type';
   ```
   Should return: `public`

2. Or update via seeder:
   ```bash
   php artisan db:seed --class=ProductionSetupSeeder
   ```

## Files Created/Modified

- `database/seeders/ProductionSetupSeeder.php` - Production setup seeder
- `database/seeders/DatabaseSeeder.php` - Updated to include production setup
- `app/Console/Commands/ProductionReset.php` - Production reset command

## Notes

- All images are stored in `storage/app/public/` directory
- Storage link must exist for files to be accessible via web
- Cache is automatically cleared during seeding
- Storage is automatically set to public during seeding
  #!/bin/bash
  set -e

ACTION="$1"
PROJECT="$2"
ARG="$3"

BASE="/var/www/projects/$PROJECT"
CONF="$BASE/project.conf"

declare -A SERVERS=( ["server1"]="user@ip1" ["server2"]="user@ip2" )

# Include common functions
notify() { /usr/local/bin/notify.sh "$PROJECT" "$1"; }

# -------------------
# CREATE PROJECT
# -------------------
create() {
read -p "Git repo (SSH) or leave empty for manual: " GIT_REPO
read -p "Branch [main]: " BRANCH
BRANCH=${BRANCH:-main}

    read -p "PHP version (e.g., 8.2): " PHP_VERSION
    PHP_VERSION=${PHP_VERSION:-8.2}

    read -p "DB name: " DB_NAME
    read -p "DB user: " DB_USER
    read -s -p "DB password: " DB_PASS; echo

    read -p "Enable Redis? [y/N]: " REDIS
    REDIS=${REDIS:-n}
    USE_REDIS=false
    [[ "$REDIS" =~ [yY] ]] && USE_REDIS=true

    WEBHOOK_SECRET=$(openssl rand -hex 20)
    mkdir -p "$BASE/releases" "$BASE/shared/storage"

    # Create database
    mysql -u root <<MYSQL
CREATE DATABASE IF NOT EXISTS $DB_NAME;
CREATE USER IF NOT EXISTS '$DB_USER'@'localhost' IDENTIFIED BY '$DB_PASS';
GRANT ALL PRIVILEGES ON $DB_NAME.* TO '$DB_USER'@'localhost';
FLUSH PRIVILEGES;
MYSQL

    # Save project.conf
    cat <<EOF > "$CONF"
DOMAIN=$PROJECT
PROJECT_DIR=$BASE
GIT_REPO=$GIT_REPO
BRANCH=$BRANCH
PHP_VERSION=$PHP_VERSION
DB_NAME=$DB_NAME
DB_USER=$DB_USER
DB_PASS=$DB_PASS
USE_REDIS=$USE_REDIS
WEBHOOK_SECRET=$WEBHOOK_SECRET
EOF

    # ENV file
    cat <<EOF > "$BASE/shared/.env"
APP_NAME=$PROJECT
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://$PROJECT

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=$DB_NAME
DB_USERNAME=$DB_USER
DB_PASSWORD=$DB_PASS

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
EOF

    chown -R www-data:www-data "$BASE"
    echo "✅ Project $PROJECT created. Use 'laravelctl deploy $PROJECT' to deploy."
}

# -------------------
# DEPLOY GIT
# -------------------
deploy() {
[[ ! -f "$CONF" ]] && { echo "Project not found"; exit 1; }
source "$CONF"

    TIMESTAMP=$(date +%Y%m%d%H%M%S)
    RELEASE="$BASE/releases/$TIMESTAMP"
    mkdir -p "$RELEASE"

    if [[ -n "$GIT_REPO" ]]; then
        git clone -b "$BRANCH" "$GIT_REPO" "$RELEASE"
    else
        echo "No Git repo defined. Use manual-deploy instead."
        exit 1
    fi

    ln -nfs "$BASE/shared/.env" "$RELEASE/.env"
    ln -nfs "$BASE/shared/storage" "$RELEASE/storage"

    if [ -f "$RELEASE/composer.json" ]; then
        sudo -u www-data composer install --no-dev --optimize-autoloader
    fi

    read -p "Run migrations? [y/N]: " MIGRATE
    [[ "$MIGRATE" =~ [yY] ]] && php "$RELEASE/artisan" migrate --force

    chown -R www-data:www-data "$RELEASE"
    chmod -R 775 "$RELEASE/storage" "$RELEASE/bootstrap/cache"
    find "$RELEASE" -type f -exec chmod 644 {} \;
    find "$RELEASE" -type d -exec chmod 755 {} \;

    ln -nfs "$RELEASE" "$BASE/current"
    systemctl reload php$PHP_VERSION-fpm

    notify "✅ Deploy completed for $PROJECT"
}

# -------------------
# ROLLBACK
# -------------------
rollback() {
PREV=$(ls -dt $BASE/releases/* | sed -n '2p')
ln -nfs "$PREV" "$BASE/current"
systemctl reload php$PHP_VERSION-fpm
notify "↩️ Rollback completed for $PROJECT"
}

# -------------------
# MANUAL DEPLOY
# -------------------
manual() {
[[ -z "$ARG" ]] && { echo "Usage: laravelctl manual PROJECT /path/to/archive.zip"; exit 1; }
/usr/local/bin/manual-deploy.sh "$PROJECT" "$ARG"
}

# -------------------
# BACKUP
# -------------------
backup() {
/usr/local/bin/backup-mysql.sh "$PROJECT"
/usr/local/bin/backup-storage.sh "$PROJECT"
}

# -------------------
# MONITOR
# -------------------
monitor() {
/usr/local/bin/monitor.sh
}

# -------------------
# MULTI-SERVER DEPLOY
# -------------------
deploy-multi() {
for SERVER in "${!SERVERS[@]}"; do
echo "Deploying $PROJECT to $SERVER..."
ssh "${SERVERS[$SERVER]}" "bash -s" < /usr/local/bin/deploy-project.sh "$CONF"
done
notify "🌐 Multi-server deploy completed for $PROJECT"
}

# -------------------
# ACTION SWITCH
# -------------------
case "$ACTION" in
create) create ;;
deploy) deploy ;;
rollback) rollback ;;
manual) manual ;;
backup) backup ;;
monitor) monitor ;;
deploy-multi) deploy-multi ;;
*)
echo "Usage:"
echo "  laravelctl create PROJECT"
echo "  laravelctl deploy PROJECT"
echo "  laravelctl rollback PROJECT"
echo "  laravelctl manual PROJECT /path/to/archive.zip"
echo "  laravelctl backup PROJECT"
echo "  laravelctl monitor"
echo "  laravelctl deploy-multi PROJECT"
;;
esac
