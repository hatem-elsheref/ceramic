# MinIO S3 Storage Setup Guide

This guide will help you configure your Laravel application to use MinIO S3 bucket instead of the public directory.

## Method 1: Via Admin Panel (Recommended)

1. **Login to Admin Panel**
   - Go to `/admin`
   - Navigate to **Settings** → **Third Party** → **Storage Connection Settings**

2. **Configure S3 Credentials**
   - Click on **Add S3 Credential** or **Update S3 Credential**
   - Fill in the following details:
     - **S3 Key**: Your MinIO access key
     - **S3 Secret**: Your MinIO secret key
     - **S3 Region**: Any region (e.g., `us-east-1`) - MinIO doesn't require specific regions
     - **S3 Bucket**: Your MinIO bucket name
     - **S3 URL**: Your MinIO public URL (e.g., `http://minio.example.com:9000/bucket-name`)
     - **S3 Endpoint**: Your MinIO endpoint (e.g., `http://minio.example.com:9000`)

3. **Enable S3 Storage**
   - Toggle the storage connection type to **S3**
   - The system will test the connection automatically

## Method 2: Via Environment Variables

Add these variables to your `.env` file:

```env
# MinIO Configuration
AWS_ACCESS_KEY_ID=your-minio-access-key
AWS_SECRET_ACCESS_KEY=your-minio-secret-key
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=your-bucket-name
AWS_URL=http://minio.example.com:9000/your-bucket-name
AWS_ENDPOINT=http://minio.example.com:9000
AWS_USE_PATH_STYLE_ENDPOINT=true

# Filesystem Driver
FILESYSTEM_DRIVER=s3
FILESYSTEM_CLOUD=s3
```

## Method 3: Via Database Seeder

Create a seeder to configure MinIO settings:

```php
php artisan make:seeder MinIOConfigSeeder
```

Then update the seeder:

```php
<?php

namespace Database\Seeders;

use App\Models\BusinessSetting;
use Illuminate\Database\Seeder;

class MinIOConfigSeeder extends Seeder
{
    public function run()
    {
        // Set storage connection type to S3
        BusinessSetting::updateOrInsert(
            ['type' => 'storage_connection_type'],
            ['value' => 's3', 'updated_at' => now(), 'created_at' => now()]
        );

        // Set S3 credentials
        $s3Credentials = [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID', 'your-minio-access-key'),
            'secret' => env('AWS_SECRET_ACCESS_KEY', 'your-minio-secret-key'),
            'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
            'bucket' => env('AWS_BUCKET', 'your-bucket-name'),
            'url' => env('AWS_URL', 'http://minio.example.com:9000/your-bucket-name'),
            'visibility' => 'public',
            'endpoint' => env('AWS_ENDPOINT', 'http://minio.example.com:9000'),
        ];

        BusinessSetting::updateOrInsert(
            ['type' => 'storage_connection_s3_credential'],
            ['value' => json_encode($s3Credentials), 'updated_at' => now(), 'created_at' => now()]
        );

        echo "✓ MinIO S3 configuration set successfully!\n";
    }
}
```

## MinIO Server Setup (If not already set up)

### Using Docker:

```bash
docker run -d \
  -p 9000:9000 \
  -p 9001:9001 \
  --name minio \
  -e "MINIO_ROOT_USER=your-access-key" \
  -e "MINIO_ROOT_PASSWORD=your-secret-key" \
  -v /path/to/data:/data \
  minio/minio server /data --console-address ":9001"
```

### Access MinIO Console:
- URL: `http://localhost:9001`
- Login with your access key and secret key
- Create a bucket for your application

## Important Notes:

1. **Path Style Endpoint**: MinIO requires `use_path_style_endpoint` to be `true`. This is already configured in `config/filesystems.php`.

2. **Bucket Policy**: Make sure your MinIO bucket has the correct policy for public access if needed:
   ```json
   {
     "Version": "2012-10-17",
     "Statement": [
       {
         "Effect": "Allow",
         "Principal": {"AWS": ["*"]},
         "Action": ["s3:GetObject"],
         "Resource": ["arn:aws:s3:::your-bucket-name/*"]
       }
     ]
   }
   ```

3. **CORS Configuration**: If you need to access files from a web browser, configure CORS in MinIO:
   - Go to MinIO Console → Buckets → Your Bucket → Settings → CORS
   - Add CORS rule for your domain

4. **Testing**: After configuration, test the connection:
   ```php
   php artisan tinker
   Storage::disk('s3')->put('test.txt', 'Hello MinIO!');
   Storage::disk('s3')->exists('test.txt'); // Should return true
   ```

## Troubleshooting:

- **Connection Error**: Check if MinIO endpoint is accessible
- **Access Denied**: Verify credentials and bucket permissions
- **Files not showing**: Check bucket policy and CORS settings
- **Path issues**: Ensure `AWS_USE_PATH_STYLE_ENDPOINT=true` for MinIO

## Migration from Public to S3:

To migrate existing files from public storage to S3:

```bash
php artisan storage:link  # If not already done
# Then use the admin panel to switch storage type
# Or manually copy files using Storage facade
```
