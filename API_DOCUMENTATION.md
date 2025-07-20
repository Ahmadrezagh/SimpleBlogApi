# SimpleBlogApi - Authentication & Blog API Documentation

This document describes the authentication and blog API endpoints for the SimpleBlogApi project.

## Base URL
```
http://localhost:8000/api
```

## Authentication Endpoints

### 1. Register User
**POST** `/api/register`

Register a new user account.

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
}
```

**Response (201):**
```json
{
    "status": true,
    "message": "User registered successfully",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "created_at": "2024-01-01T00:00:00.000000Z",
            "updated_at": "2024-01-01T00:00:00.000000Z"
        },
        "token": "1|abc123...",
        "token_type": "Bearer"
    }
}
```

### 2. Login User
**POST** `/api/login`

Authenticate a user and get access token.

**Request Body:**
```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response (200):**
```json
{
    "status": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "created_at": "2024-01-01T00:00:00.000000Z",
            "updated_at": "2024-01-01T00:00:00.000000Z"
        },
        "token": "1|abc123...",
        "token_type": "Bearer"
    }
}
```

### 3. Get User Profile
**GET** `/api/me`

Get the authenticated user's profile information.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "status": true,
    "message": "User profile retrieved successfully",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "created_at": "2024-01-01T00:00:00.000000Z",
            "updated_at": "2024-01-01T00:00:00.000000Z"
        }
    }
}
```

### 4. Update User Profile
**PUT** `/api/update-profile`

Update the authenticated user's profile information.

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body (all fields optional):**
```json
{
    "name": "John Smith",
    "email": "johnsmith@example.com",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
}
```

**Response (200):**
```json
{
    "status": true,
    "message": "Profile updated successfully",
    "data": {
        "user": {
            "id": 1,
            "name": "John Smith",
            "email": "johnsmith@example.com",
            "created_at": "2024-01-01T00:00:00.000000Z",
            "updated_at": "2024-01-01T00:00:00.000000Z"
        }
    }
}
```

### 5. Logout User
**POST** `/api/logout`

Logout the authenticated user (revoke token).

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "status": true,
    "message": "Successfully logged out"
}
```

## Blog Endpoints

### 1. List All Blogs (Public)
**GET** `/api/blogs`

Get a paginated list of all blogs with search and ordering options.

**Query Parameters:**
- `search` (optional): Search term for title, description, or author name
- `order` (optional): Ordering method - `latest`, `oldest`, `most_liked`, `alphabetical` (default: `latest`)
- `per_page` (optional): Number of items per page (1-100, default: 10)

**Examples:**
```
GET /api/blogs
GET /api/blogs?search=laravel
GET /api/blogs?order=most_liked&per_page=20
GET /api/blogs?search=php&order=alphabetical&per_page=5
```

**Response (200):**
```json
{
    "status": true,
    "message": "Blogs retrieved successfully",
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "title": "My First Blog Post",
                "slug": "my-first-blog-post",
                "description": "This is my first blog post content...",
                "cover_image": "http://localhost:8000/storage/blogs/covers/abc123.jpg",
                "user_id": 1,
                "created_at": "2024-01-01T00:00:00.000000Z",
                "updated_at": "2024-01-01T00:00:00.000000Z",
                "likes_count": 5,
                "is_liked_by_user": true,
                "user": {
                    "id": 1,
                    "name": "John Doe"
                }
            }
        ],
        "per_page": 10,
        "total": 1
    }
}
```

### 2. Show Blog (Public)
**GET** `/api/blogs/{slug}`

Get a specific blog by its slug.

**Response (200):**
```json
{
    "status": true,
    "message": "Blog retrieved successfully",
    "data": {
        "id": 1,
        "title": "My First Blog Post",
        "slug": "my-first-blog-post",
        "description": "This is my first blog post content...",
        "cover_image": "http://localhost:8000/storage/blogs/covers/abc123.jpg",
        "user_id": 1,
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z",
        "likes_count": 5,
        "is_liked_by_user": true,
        "user": {
            "id": 1,
            "name": "John Doe"
        },
        "comments": [
            {
                "id": 1,
                "content": "Great blog post!",
                "created_at": "2024-01-01T00:00:00.000000Z",
                "user": {
                    "id": 2,
                    "name": "Jane Smith"
                }
            }
        ]
    }
}
```

### 3. Create Blog (Authenticated Users Only)
**POST** `/api/blogs`

Create a new blog post.

**Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Request Body (multipart/form-data):**
```
title: "My New Blog Post"
description: "This is the content of my new blog post..."
cover_image: [file] (optional)
```

**File Requirements:**
- **cover_image**: Optional image file
- **Supported formats**: JPEG, PNG, JPG, GIF, WEBP
- **Maximum size**: 2MB

**Response (201):**
```json
{
    "status": true,
    "message": "Blog created successfully",
    "data": {
        "id": 2,
        "title": "My New Blog Post",
        "slug": "my-new-blog-post",
        "description": "This is the content of my new blog post...",
        "cover_image": "http://localhost:8000/storage/blogs/covers/def456.jpg",
        "user_id": 1,
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z",
        "likes_count": 0,
        "is_liked_by_user": false,
        "user": {
            "id": 1,
            "name": "John Doe"
        },
        "comments": []
    }
}
```

### 4. Update Blog (Authenticated Users - Own Blogs Only)
**PUT** `/api/blogs/{id}`

Update an existing blog post (only if you own it).

**Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Request Body (multipart/form-data, all fields optional):**
```
title: "Updated Blog Title"
description: "Updated blog content..."
cover_image: [file] (optional)
```

**File Requirements:**
- **cover_image**: Optional image file
- **Supported formats**: JPEG, PNG, JPG, GIF, WEBP
- **Maximum size**: 2MB

**Response (200):**
```json
{
    "status": true,
    "message": "Blog updated successfully",
    "data": {
        "id": 2,
        "title": "Updated Blog Title",
        "slug": "updated-blog-title",
        "description": "Updated blog content...",
        "cover_image": "http://localhost:8000/storage/blogs/covers/ghi789.jpg",
        "user_id": 1,
        "created_at": "2024-01-01T00:00:00.000000Z",
        "updated_at": "2024-01-01T00:00:00.000000Z",
        "likes_count": 3,
        "is_liked_by_user": true,
        "user": {
            "id": 1,
            "name": "John Doe"
        },
        "comments": []
    }
}
```

### 5. Get My Blogs (Authenticated Users Only)
**GET** `/api/my-blogs`

Get a paginated list of the authenticated user's blogs with search and ordering options.

**Headers:**
```
Authorization: Bearer {token}
```

**Query Parameters:**
- `search` (optional): Search term for title, description, or author name
- `order` (optional): Ordering method - `latest`, `oldest`, `most_liked`, `alphabetical` (default: `latest`)
- `per_page` (optional): Number of items per page (1-100, default: 10)

**Examples:**
```
GET /api/my-blogs
GET /api/my-blogs?search=laravel
GET /api/my-blogs?order=most_liked&per_page=20
GET /api/my-blogs?search=php&order=alphabetical&per_page=5
```

**Response (200):**
```json
{
    "status": true,
    "message": "Your blogs retrieved successfully",
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "title": "My First Blog Post",
                "slug": "my-first-blog-post",
                "description": "This is my first blog post content...",
                "cover_image": "http://localhost:8000/storage/blogs/covers/abc123.jpg",
                "user_id": 1,
                "created_at": "2024-01-01T00:00:00.000000Z",
                "updated_at": "2024-01-01T00:00:00.000000Z",
                "likes_count": 5,
                "is_liked_by_user": true
            }
        ],
        "per_page": 10,
        "total": 1
    }
}
```

## Blog Interaction Endpoints

### 1. Like/Unlike Blog (Authenticated Users Only)
**POST** `/api/blogs/{blogId}/like`

Like or unlike a blog post. Each user can like a blog only once.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200) - When liking:**
```json
{
    "status": true,
    "message": "Blog liked successfully",
    "data": {
        "liked": true,
        "likes_count": 6
    }
}
```

**Response (200) - When unliking:**
```json
{
    "status": true,
    "message": "Blog unliked successfully",
    "data": {
        "liked": false,
        "likes_count": 5
    }
}
```

### 2. Add Comment (Authenticated Users Only)
**POST** `/api/blogs/{blogId}/comment`

Add a comment to a blog post.

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "content": "This is a great blog post! Thanks for sharing."
}
```

**Response (201):**
```json
{
    "status": true,
    "message": "Comment added successfully",
    "data": {
        "comment": {
            "id": 1,
            "content": "This is a great blog post! Thanks for sharing.",
            "created_at": "2024-01-01T00:00:00.000000Z",
            "user": {
                "id": 2,
                "name": "Jane Smith"
            }
        }
    }
}
```

### 3. Get Comments (Authenticated Users Only)
**GET** `/api/blogs/{blogId}/comments`

Get paginated comments for a specific blog post.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "status": true,
    "message": "Comments retrieved successfully",
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "content": "This is a great blog post! Thanks for sharing.",
                "created_at": "2024-01-01T00:00:00.000000Z",
                "user": {
                    "id": 2,
                    "name": "Jane Smith"
                }
            },
            {
                "id": 2,
                "content": "I learned a lot from this post.",
                "created_at": "2024-01-01T00:00:00.000000Z",
                "user": {
                    "id": 3,
                    "name": "Bob Johnson"
                }
            }
        ],
        "per_page": 10,
        "total": 2
    }
}
```

## Error Responses

### Validation Error (422)
```json
{
    "status": false,
    "message": "Validation error",
    "errors": {
        "title": ["The title field is required."],
        "description": ["The description field is required."],
        "content": ["The content field is required."],
        "order": ["Order must be one of: latest, oldest, most_liked, alphabetical."],
        "per_page": ["Per page must be at least 1."],
        "cover_image": ["Cover image must be an image file."],
        "cover_image.mimes": ["Cover image must be a JPEG, PNG, JPG, GIF, or WEBP file."],
        "cover_image.max": ["Cover image cannot exceed 2MB."]
    }
}
```

### Authentication Error (401)
```json
{
    "status": false,
    "message": "Invalid credentials"
}
```

### Unauthorized Error (401)
```json
{
    "message": "Unauthenticated."
}
```

### Forbidden Error (403)
```json
{
    "status": false,
    "message": "Unauthorized. You can only update your own blogs."
}
```

### Not Found Error (404)
```json
{
    "status": false,
    "message": "Blog not found"
}
```

## Testing the API

### Using cURL

#### Authentication
1. **Register a new user:**
```bash
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123"
  }'
```

2. **Login:**
```bash
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

#### Blog Operations
3. **Create a blog with cover image (replace {token} with actual token):**
```bash
curl -X POST http://localhost:8000/api/blogs \
  -H "Authorization: Bearer {token}" \
  -F "title=My First Blog Post" \
  -F "description=This is my first blog post content..." \
  -F "cover_image=@/path/to/your/image.jpg"
```

4. **Get all blogs (public):**
```bash
curl -X GET http://localhost:8000/api/blogs
```

5. **Get blogs with search:**
```bash
curl -X GET "http://localhost:8000/api/blogs?search=laravel&order=latest&per_page=5"
```

6. **Get blogs ordered by most liked:**
```bash
curl -X GET "http://localhost:8000/api/blogs?order=most_liked&per_page=10"
```

7. **Get specific blog (public):**
```bash
curl -X GET http://localhost:8000/api/blogs/my-first-blog-post
```

8. **Update blog with new cover image (replace {token} and {id}):**
```bash
curl -X PUT http://localhost:8000/api/blogs/1 \
  -H "Authorization: Bearer {token}" \
  -F "title=Updated Blog Title" \
  -F "description=Updated content..." \
  -F "cover_image=@/path/to/new/image.jpg"
```

9. **Get my blogs with search:**
```bash
curl -X GET "http://localhost:8000/api/my-blogs?search=php&order=alphabetical" \
  -H "Authorization: Bearer {token}"
```

#### Blog Interactions
10. **Like a blog (replace {token} and {blogId}):**
```bash
curl -X POST http://localhost:8000/api/blogs/1/like \
  -H "Authorization: Bearer {token}"
```

11. **Add a comment (replace {token} and {blogId}):**
```bash
curl -X POST http://localhost:8000/api/blogs/1/comment \
  -H "Authorization: Bearer {token}" \
  -H "Content-Type: application/json" \
  -d '{
    "content": "This is a great blog post! Thanks for sharing."
  }'
```

12. **Get comments for a blog (replace {token} and {blogId}):**
```bash
curl -X GET http://localhost:8000/api/blogs/1/comments \
  -H "Authorization: Bearer {token}"
```

## Search & Ordering Options

### Search Parameters
- **search**: Search in blog title, description, and author name
- **order**: Sort blogs by different criteria
  - `latest` (default): Newest blogs first
  - `oldest`: Oldest blogs first
  - `most_liked`: Blogs with most likes first
  - `alphabetical`: Blogs sorted by title A-Z
- **per_page**: Number of blogs per page (1-100, default: 10)

### Examples
```
GET /api/blogs?search=laravel&order=latest
GET /api/blogs?order=most_liked&per_page=20
GET /api/blogs?search=php&order=alphabetical&per_page=5
GET /api/my-blogs?search=tutorial&order=oldest
```

## File Upload Features

### Cover Image Upload
- **Supported formats**: JPEG, PNG, JPG, GIF, WEBP
- **Maximum file size**: 2MB
- **Storage location**: `storage/app/public/blogs/covers/`
- **URL format**: `http://localhost:8000/storage/blogs/covers/{filename}`
- **Automatic cleanup**: Old files are deleted when updating
- **Unique filenames**: Generated using random strings to prevent conflicts

### File Upload Process
1. **Validation**: File type, size, and format validation
2. **Storage**: Files stored in public disk with organized directory structure
3. **Cleanup**: Old files automatically deleted when updating
4. **URL Generation**: Full URLs returned in API responses
5. **Security**: File type validation prevents malicious uploads

## Features

### Authentication
- ✅ User registration with validation
- ✅ User login with token generation
- ✅ Protected routes using Laravel Sanctum
- ✅ User profile retrieval
- ✅ User profile update
- ✅ Token-based logout
- ✅ Comprehensive error handling
- ✅ Input validation
- ✅ Password hashing
- ✅ Email uniqueness validation

### Blog Management
- ✅ Public blog listing and viewing
- ✅ Authenticated users can create blogs
- ✅ Users can only update their own blogs
- ✅ Automatic slug generation from title
- ✅ Unique slug handling
- ✅ Blog ownership validation
- ✅ Pagination for blog lists
- ✅ User relationship with blogs
- ✅ Cover image file upload support
- ✅ Comprehensive validation

### Blog Search & Filtering
- ✅ Search blogs by title, description, and author name
- ✅ Multiple ordering options (latest, oldest, most liked, alphabetical)
- ✅ Configurable pagination (1-100 items per page)
- ✅ Clean URL query parameters
- ✅ Validation for search parameters
- ✅ Default values for optional parameters
- ✅ Search functionality for both public and user's blogs

### Blog Interactions
- ✅ Like/Unlike blogs (one like per user per blog)
- ✅ Add comments to blogs (multiple comments allowed)
- ✅ Get comments for specific blogs
- ✅ Like count display in blog responses
- ✅ User like status in blog responses
- ✅ Comments display in individual blog view
- ✅ Comments pagination
- ✅ User information in comments
- ✅ Proper authorization for interactions

### File Upload System
- ✅ Secure file upload handling
- ✅ Image format validation (JPEG, PNG, JPG, GIF, WEBP)
- ✅ File size limits (2MB maximum)
- ✅ Automatic file cleanup on updates
- ✅ Unique filename generation
- ✅ Organized storage structure
- ✅ Full URL generation for API responses
- ✅ Storage link for public access

## Security Features

- Passwords are automatically hashed using Laravel's built-in hashing
- API tokens are managed by Laravel Sanctum
- Protected routes require valid authentication
- Input validation prevents malicious data
- Email uniqueness ensures no duplicate accounts
- Blog ownership validation prevents unauthorized updates
- Automatic slug generation ensures SEO-friendly URLs
- Like uniqueness prevents duplicate likes
- Comment validation ensures proper content
- Search parameter validation prevents injection attacks
- File upload validation prevents malicious file uploads
- File type validation ensures only images are uploaded
- File size limits prevent storage abuse 