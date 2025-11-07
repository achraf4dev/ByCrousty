# QR Code System Documentation

## Overview
This system automatically generates a unique, immutable QR code for each user upon registration. The QR code contains encrypted user information and can be used for various purposes like user identification, event check-ins, or authentication.

## Features
- **Unique QR Code**: Each user gets a unique QR code that cannot be changed
- **Automatic Generation**: QR code is generated automatically during user registration
- **Image Display**: QR codes are accessible as PNG images via API endpoints
- **Secure Data**: QR code data is base64 encoded and contains user information
- **Public Access**: QR code images can be accessed without authentication (useful for scanning)

## API Endpoints

### 1. User Registration
**POST** `/api/v1/register`

Response now includes QR code URL:
```json
{
    "user": {
        "id": 1,
        "full_name": "John Doe",
        "username": "johndoe",
        "email": "john@example.com",
        "qr_code_url": "http://localhost:8000/api/v1/users/1/qr-code"
    },
    "token": "...",
    "verification_url": "...",
    "qr_code_url": "http://localhost:8000/api/v1/users/1/qr-code"
}
```

### 2. Get QR Code Image
**GET** `/api/v1/users/{id}/qr-code` (requires authentication)

Returns a PNG image of the user's QR code. Users can only access their own QR code unless they are an admin.

**Authorization:**
- Users can only access their own QR code
- Admin users can access any user's QR code

**Response Headers:**
- Content-Type: image/png
- Cache-Control: private, max-age=3600

### 2.1. Get My QR Code (Convenience Endpoint)
**GET** `/api/v1/my-qr-code` (requires authentication)

Returns a PNG image of the authenticated user's QR code.

**Response Headers:**
- Content-Type: image/png
- Cache-Control: private, max-age=3600

### 2.2. Get All QR Codes (Admin Only)
**GET** `/api/v1/admin/qr-codes` (requires admin authentication)

Returns a paginated list of all users with their QR code information. Only accessible by admin users.

**Authorization:** Admin role required

**Response:**
```json
{
    "message": "QR codes retrieved successfully",
    "users": {
        "data": [
            {
                "id": 1,
                "full_name": "John Doe",
                "username": "johndoe",
                "email": "john@example.com",
                "role": "user",
                "created_at": "2025-11-06T04:31:17.000000Z",
                "qr_code_url": "http://localhost:8000/api/v1/users/1/qr-code"
            }
        ],
        "links": { ... },
        "meta": { ... }
    },
    "total_users_with_qr": 25
}
```

### 3. User Profile
**GET** `/api/v1/profile` (requires authentication)

Returns user profile including QR code URL:
```json
{
    "user": {
        "id": 1,
        "full_name": "John Doe",
        "username": "johndoe",
        "email": "john@example.com",
        "qr_code_url": "http://localhost:8000/api/v1/users/1/qr-code"
    },
    "qr_code_url": "http://localhost:8000/api/v1/users/1/qr-code"
}
```

## QR Code Data Structure

The QR code contains JSON data with the following structure:
```json
{
    "user_id": 1,
    "email": "john@example.com",
    "username": "johndoe",
    "unique_id": "550e8400-e29b-41d4-a716-446655440000",
    "generated_at": "2025-11-06T04:31:17.000000Z",
    "app": "ByCrousty"
}
```

This data is base64 encoded and stored in the `qr_code_data` field of the users table.

## Database Changes

### Migration: `add_qr_code_to_users_table`
Adds a new column to the users table:
- `qr_code_data` (string, unique, nullable): Stores the base64 encoded QR code data

### Model Updates
- Added `qr_code_data` to fillable fields
- Added `qr_code_data` to hidden fields (for security)
- Added `qr_code_url` accessor to automatically generate QR code image URL
- Added `qr_code_url` to appends array for JSON serialization

## Security Considerations

1. **Access Control**: Users can only access their own QR codes
2. **Admin Override**: Admin users can access any user's QR code
3. **Authentication Required**: All QR code endpoints require valid authentication
4. **Data Privacy**: Raw QR code data is hidden from API responses
5. **Immutable**: Once generated, QR codes cannot be changed
6. **Unique**: Each QR code is unique and tied to a specific user
7. **Private Caching**: QR code images use private caching (not public) for security

## Usage Examples

### Frontend Implementation
```javascript
// After user login/registration, get user's QR code
const token = localStorage.getItem('auth_token');

// Method 1: Use the convenience endpoint
const response = await fetch('/api/v1/my-qr-code', {
    method: 'GET',
    headers: { 
        'Authorization': `Bearer ${token}`,
        'Accept': 'image/png'
    }
});

if (response.ok) {
    const qrCodeBlob = await response.blob();
    const qrCodeUrl = URL.createObjectURL(qrCodeBlob);
    
    // Display QR code
    document.getElementById('qr-code').innerHTML = 
        `<img src="${qrCodeUrl}" alt="My QR Code" width="300" height="300">`;
}

// Method 2: Use the user-specific endpoint (if you know the user ID)
const userId = getCurrentUserId();
const response2 = await fetch(`/api/v1/users/${userId}/qr-code`, {
    method: 'GET',
    headers: { 
        'Authorization': `Bearer ${token}`,
        'Accept': 'image/png'
    }
});
```

### QR Code Scanning
When scanning a QR code, you'll get the JSON data which can be used to identify the user:
```javascript
// Decoded QR code data
const qrData = JSON.parse(scannedData);
const userId = qrData.user_id;
const userEmail = qrData.email;
```

## Testing

Run the QR code tests:
```bash
php artisan test tests/Feature/QrCodeTest.php
```

## Dependencies

- `endroid/qr-code`: QR code generation library
- Laravel's built-in UUID generation for unique identifiers