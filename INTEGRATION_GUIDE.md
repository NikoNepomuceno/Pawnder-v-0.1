# Profile Picture Integration Guide

## Views to Update

Replace existing profile picture displays with the new component:

### 1. Navigation (layouts/navigation.blade.php)
```blade
<!-- Replace this: -->
@if(Auth::user() && Auth::user()->profile_picture)
    <img src="{{ Auth::user()->profile_picture }}" alt="Profile">
@else
    <img src="{{ asset('images/default-profile.png') }}" alt="Default Profile Picture">
@endif

<!-- With this: -->
<x-profile-picture :user="Auth::user()" size="sm" />
```

### 2. Home Page Posts (home.blade.php)
```blade
<!-- Replace this: -->
<img src="{{ $post->user->profile_picture ?? asset('images/default-profile.png') }}" 
     alt="Profile" class="post-avatar">

<!-- With this: -->
<x-profile-picture :user="$post->user" size="sm" class="post-avatar" />
```

### 3. Profile View (auth/view-profile.blade.php)
```blade
<!-- Replace this: -->
@if ($user->profile_picture)
    <img src="{{ $user->profile_picture }}" alt="Profile Picture">
@else
    <img src="{{ asset('images/default-profile.png') }}" alt="Default Profile Picture">
@endif

<!-- With this: -->
<x-profile-picture :user="$user" size="xl" />
```

### 4. Admin Post View (livewire/admin/post-view.blade.php)
```blade
<!-- Replace this: -->
<img src="{{ $post->user->profile_picture ?? asset('images/default-profile.png') }}" 
     alt="Profile" class="post-avatar">

<!-- With this: -->
<x-profile-picture :user="$post->user" size="sm" class="post-avatar" />
```

## Configuration Updates

### 1. Add to User Model fillable array:
```php
protected $fillable = [
    // ... existing fields
    'google_id',
    'google_token', 
    'google_refresh_token',
];
```

### 2. Create migration for Google OAuth fields:
```bash
php artisan make:migration add_google_oauth_fields_to_users_table
```

```php
public function up()
{
    Schema::table('users', function (Blueprint $table) {
        $table->string('google_id')->nullable()->unique();
        $table->text('google_token')->nullable();
        $table->text('google_refresh_token')->nullable();
    });
}
```

### 3. Update routes (web.php):
```php
Route::get('/auth/google', [GoogleController::class, 'redirectToGoogle'])->name('google.redirect');
Route::get('/auth/google/callback', [GoogleController::class, 'handleGoogleCallback'])->name('google.callback');
```

## Performance Considerations

### 1. Image Optimization
- All Google profile pictures are automatically converted to WebP format
- Face detection ensures proper cropping
- Images are resized to 400x400px for consistency

### 2. Caching Strategy
- Profile pictures are cached by Cloudinary CDN
- User model attributes are cached for profile_picture_url
- Consider adding Redis caching for frequently accessed user data

### 3. Database Indexing
Add indexes for performance:
```sql
ALTER TABLE users ADD INDEX idx_google_id (google_id);
ALTER TABLE users ADD INDEX idx_email_verified (email, email_verified_at);
```

## Security Considerations

### 1. Rate Limiting
- OAuth redirects: 10 attempts per 5 minutes per IP
- OAuth callbacks: 20 attempts per 5 minutes per IP

### 2. Data Validation
- All Google user data is validated before processing
- Names are sanitized to prevent XSS
- Email validation ensures proper format

### 3. Privacy Settings (Future Enhancement)
Consider adding user preferences:
```php
// Migration
$table->boolean('sync_google_profile_picture')->default(true);
$table->boolean('public_profile')->default(true);

// Usage
if ($user->sync_google_profile_picture) {
    // Update profile picture from Google
}
```

## Error Handling

### 1. Graceful Degradation
- If Google profile picture fails to download, keeps existing picture
- If Cloudinary upload fails, falls back to Google URL
- If all fails, uses default profile picture

### 2. Logging
All errors are logged with context:
- User ID
- Google user data
- Error messages
- Stack traces

### 3. User Feedback
Clear error messages for users:
- OAuth session expired
- Too many attempts
- Service unavailable

## Testing Recommendations

### 1. Unit Tests
```php
// Test profile picture processing
public function test_processes_google_profile_picture()
{
    // Test implementation
}

// Test username generation
public function test_generates_unique_username()
{
    // Test implementation
}
```

### 2. Integration Tests
- Test complete OAuth flow
- Test profile picture upload and display
- Test rate limiting functionality

### 3. Manual Testing Scenarios
- New user registration via Google
- Existing user login via Google
- User with custom profile picture
- Network failures during image processing
- Rate limiting triggers
