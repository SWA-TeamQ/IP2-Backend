# File Uploads & Services

The application handles file uploads (primarily product images) through a centralized `FileService` located in `app/services/FileService.php`.

## Storage Location
All uploaded files are stored in `Ecomerece_Web_Backend/public/uploads/`. 
The `FileService` automatically creates this directory if it doesn't exist.

## Workflow

1. **Request**: The client sends a `multipart/form-data` request.
2. **Controller**: `ProductController` receives the request and extracts files using `$request->getFiles()`.
3. **Service**: The controller passes the files to `FileService->uploadMultiple()`.
4. **Validation & Renaming**:
   - `FileService` generates a unique filename using `uniqid('img_', true)`.
   - Files are moved from the temporary PHP directory to the `public/uploads/` folder.
5. **Persistence**: The service returns an array of relative URLs (e.g., `/uploads/img_645a...png`), which are then saved into the database's `images` column (PostgreSQL `TEXT[]` array).

## Usage in Controllers

```php
$files = $request->getFiles();
if (!empty($files['images'])) {
    $imageUrls = $this->fileService->uploadMultiple($files['images']);
}
```

## Frontend Requirements
To upload files to the API, ensure your request uses `multipart/form-data`:

```javascript
const formData = new FormData();
formData.append('name', 'Product Name');
// Append files
images.forEach(image => {
    formData.append('images[]', image); 
});

axios.post('/api/products', formData, {
    headers: { 'Content-Type': 'multipart/form-data' }
});
```

## Security Note
Currently, files are renamed with a unique ID to prevent overwriting. In the future, we should add MIME-type validation to ensure only images (JPG, PNG, WEBP) are accepted.
