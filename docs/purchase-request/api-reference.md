# 🔌 Purchase Request - API Reference

## 📡 API Overview

### Base Information
- **Base URL**: `https://your-domain.com/api`
- **Authentication**: Laravel Sanctum Token
- **Content-Type**: `application/json`
- **API Version**: v1

### Authentication
```http
Authorization: Bearer {your-api-token}
```

## 🔐 Authentication Endpoints

### Login
```http
POST /api/login
```

**Request Body:**
```json
{
    "email": "user@company.com",
    "password": "password123"
}
```

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        "token": "1|abcdef123456...",
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "user@company.com",
            "level": "USER",
            "divisi": "IT"
        }
    },
    "message": "Login successful"
}
```

### Logout
```http
POST /api/logout
```

**Headers:**
```http
Authorization: Bearer {token}
```

**Response (200 OK):**
```json
{
    "success": true,
    "message": "Logged out successfully"
}
```

## 📋 Purchase Request Endpoints

### Get All PRs
```http
GET /api/purchase-requests
```

**Query Parameters:**
- `status` (optional): Filter by status
- `user_id` (optional): Filter by user ID
- `page` (optional): Pagination page number
- `per_page` (optional): Items per page (default: 15)

**Example Request:**
```http
GET /api/purchase-requests?status=APPROVED&page=1&per_page=10
```

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        "current_page": 1,
        "data": [
            {
                "id": 1,
                "pr_number": "PR-2025001",
                "description": "Office supplies for Q1",
                "status": "APPROVED",
                "request_date": "2025-01-09",
                "due_date": "2025-01-15",
                "location": "Head Office",
                "user": {
                    "id": 1,
                    "name": "John Doe",
                    "email": "john@company.com"
                },
                "items": [
                    {
                        "id": 1,
                        "nama_item": "Printer Paper A4",
                        "quantity": 20,
                        "unit": "Box",
                        "estimated_price": 50000,
                        "description": "White A4 80gsm"
                    }
                ],
                "total_estimated_value": 1000000,
                "created_at": "2025-01-09T08:30:00.000000Z",
                "updated_at": "2025-01-10T14:20:00.000000Z"
            }
        ],
        "per_page": 10,
        "total": 25,
        "last_page": 3
    }
}
```

### Get Single PR
```http
GET /api/purchase-requests/{id}
```

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "pr_number": "PR-2025001",
        "description": "Office supplies for Q1",
        "status": "APPROVED",
        "request_date": "2025-01-09",
        "due_date": "2025-01-15",
        "location": "Head Office",
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@company.com",
            "level": "USER",
            "divisi": "IT"
        },
        "items": [
            {
                "id": 1,
                "nama_item": "Printer Paper A4",
                "quantity": 20,
                "unit": "Box",
                "estimated_price": 50000,
                "description": "White A4 80gsm",
                "notes": null
            }
        ],
        "approvals": {
            "dept_head_approval": "2025-01-09T10:30:00.000000Z",
            "ga_approval": "2025-01-09T14:20:00.000000Z",
            "finance_dept_approval": "2025-01-10T09:15:00.000000Z",
            "cfo_approval": "2025-01-10T11:45:00.000000Z",
            "ceo_approval": "2025-01-10T14:20:00.000000Z"
        },
        "approval_notes": {
            "dept_head_notes": "Approved for essential items",
            "ga_notes": null,
            "finance_dept_notes": "Budget confirmed available",
            "cfo_notes": "Approved within budget limits",
            "ceo_notes": "Final approval granted"
        },
        "total_estimated_value": 1000000,
        "created_at": "2025-01-09T08:30:00.000000Z",
        "updated_at": "2025-01-10T14:20:00.000000Z"
    }
}
```

### Create New PR
```http
POST /api/purchase-requests
```

**Request Body:**
```json
{
    "description": "IT Equipment for new employees",
    "request_date": "2025-01-15",
    "due_date": "2025-01-22",
    "location": "Main Office",
    "items": [
        {
            "nama_item": "Laptop Dell Latitude 5520",
            "quantity": 2,
            "unit": "Unit",
            "estimated_price": 15000000,
            "description": "Business laptop with Windows 11 Pro",
            "notes": "Must have SSD storage"
        },
        {
            "nama_item": "Wireless Mouse",
            "quantity": 2,
            "unit": "Unit", 
            "estimated_price": 250000,
            "description": "Ergonomic wireless mouse",
            "notes": null
        }
    ]
}
```

**Response (201 Created):**
```json
{
    "success": true,
    "data": {
        "id": 26,
        "pr_number": "PR-2025026",
        "description": "IT Equipment for new employees",
        "status": "SUBMITTED",
        "request_date": "2025-01-15",
        "due_date": "2025-01-22",
        "location": "Main Office",
        "user_id": 1,
        "items": [
            {
                "id": 45,
                "spk_id": 26,
                "nama_item": "Laptop Dell Latitude 5520",
                "quantity": 2,
                "unit": "Unit",
                "estimated_price": 15000000,
                "description": "Business laptop with Windows 11 Pro",
                "notes": "Must have SSD storage"
            },
            {
                "id": 46,
                "spk_id": 26,
                "nama_item": "Wireless Mouse",
                "quantity": 2,
                "unit": "Unit",
                "estimated_price": 250000,
                "description": "Ergonomic wireless mouse",
                "notes": null
            }
        ],
        "total_estimated_value": 30500000,
        "created_at": "2025-01-15T10:30:00.000000Z",
        "updated_at": "2025-01-15T10:30:00.000000Z"
    },
    "message": "Purchase Request created successfully"
}
```

### Update PR (Only DRAFT status)
```http
PUT /api/purchase-requests/{id}
```

**Request Body:** (Same as Create PR)

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        // Updated PR data
    },
    "message": "Purchase Request updated successfully"
}
```

### Delete PR (Only DRAFT status)
```http
DELETE /api/purchase-requests/{id}
```

**Response (200 OK):**
```json
{
    "success": true,
    "message": "Purchase Request deleted successfully"
}
```

## ✅ Approval Endpoints

### Approve PR
```http
POST /api/purchase-requests/{id}/approve
```

**Request Body:**
```json
{
    "approval_level": "dept_head", // dept_head, ga, finance_dept, cfo, ceo
    "notes": "Approved for essential business needs"
}
```

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "pr_number": "PR-2025001",
        "status": "SUBMITTED", // or "APPROVED" if final approval
        "approval_level": "dept_head",
        "approved_at": "2025-01-15T14:30:00.000000Z",
        "notes": "Approved for essential business needs"
    },
    "message": "Purchase Request approved successfully"
}
```

### Reject PR
```http
POST /api/purchase-requests/{id}/reject
```

**Request Body:**
```json
{
    "approval_level": "dept_head",
    "reason": "Budget not available for this quarter"
}
```

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "pr_number": "PR-2025001",
        "status": "REJECTED",
        "approval_level": "dept_head",
        "rejected_at": "2025-01-15T14:30:00.000000Z",
        "reason": "Budget not available for this quarter"
    },
    "message": "Purchase Request rejected"
}
```

### Get Pending Approvals
```http
GET /api/purchase-requests/pending-approvals
```

**Response (200 OK):**
```json
{
    "success": true,
    "data": [
        {
            "id": 5,
            "pr_number": "PR-2025005",
            "description": "Marketing materials",
            "user": {
                "name": "Jane Smith",
                "divisi": "Marketing"
            },
            "request_date": "2025-01-14",
            "due_date": "2025-01-20",
            "total_estimated_value": 5000000,
            "approval_level_needed": "dept_head",
            "created_at": "2025-01-14T09:00:00.000000Z"
        }
    ],
    "count": 1
}
```

## 📊 Status & Analytics Endpoints

### Get PR Statistics
```http
GET /api/purchase-requests/statistics
```

**Query Parameters:**
- `start_date` (optional): Start date for filtering (YYYY-MM-DD)
- `end_date` (optional): End date for filtering (YYYY-MM-DD)
- `user_id` (optional): Filter by specific user

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        "total_prs": 156,
        "by_status": {
            "DRAFT": 3,
            "SUBMITTED": 12,
            "APPROVED": 25,
            "REJECTED": 8,
            "COMPLETED": 108
        },
        "total_value": {
            "submitted": 125000000,
            "approved": 89000000,
            "completed": 567000000
        },
        "avg_approval_time_days": 3.2,
        "top_requesters": [
            {
                "user_id": 5,
                "name": "Alice Johnson",
                "total_prs": 23,
                "total_value": 45000000
            }
        ],
        "by_month": [
            {
                "month": "2025-01",
                "count": 34,
                "total_value": 125000000
            }
        ]
    }
}
```

### Get Approval Flow Status
```http
GET /api/purchase-requests/{id}/approval-flow
```

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        "pr_id": 1,
        "pr_number": "PR-2025001",
        "current_status": "APPROVED",
        "flow": [
            {
                "level": "dept_head",
                "status": "approved",
                "approved_by": "John Manager",
                "approved_at": "2025-01-09T10:30:00.000000Z",
                "notes": "Approved for essential items"
            },
            {
                "level": "ga",
                "status": "approved", 
                "approved_by": "GA Staff",
                "approved_at": "2025-01-09T14:20:00.000000Z",
                "notes": null
            },
            {
                "level": "finance_dept",
                "status": "approved",
                "approved_by": "Finance Manager",
                "approved_at": "2025-01-10T09:15:00.000000Z",
                "notes": "Budget confirmed available"
            },
            {
                "level": "cfo",
                "status": "approved",
                "approved_by": "CFO Name", 
                "approved_at": "2025-01-10T11:45:00.000000Z",
                "notes": "Approved within budget limits"
            },
            {
                "level": "ceo",
                "status": "approved",
                "approved_by": "CEO Name",
                "approved_at": "2025-01-10T14:20:00.000000Z", 
                "notes": "Final approval granted"
            }
        ]
    }
}
```

## 👥 User Management Endpoints

### Get User Profile
```http
GET /api/user/profile
```

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        "id": 1,
        "name": "John Doe",
        "email": "john@company.com",
        "level": "USER",
        "divisi": "IT",
        "created_at": "2024-12-01T00:00:00.000000Z",
        "email_verified_at": "2024-12-01T08:30:00.000000Z"
    }
}
```

### Update User Profile
```http
PUT /api/user/profile
```

**Request Body:**
```json
{
    "name": "John Doe Updated",
    "email": "john.new@company.com"
}
```

**Response (200 OK):**
```json
{
    "success": true,
    "data": {
        // Updated user data
    },
    "message": "Profile updated successfully"
}
```

### Get Users (Admin only)
```http
GET /api/users
```

**Response (200 OK):**
```json
{
    "success": true,
    "data": [
        {
            "id": 1,
            "name": "John Doe",
            "email": "john@company.com",
            "level": "USER",
            "divisi": "IT",
            "created_at": "2024-12-01T00:00:00.000000Z"
        }
    ]
}
```

## 🔍 Search & Filter Endpoints

### Search PRs
```http
GET /api/purchase-requests/search
```

**Query Parameters:**
- `q`: Search query (searches in description, pr_number, items)
- `status`: Filter by status
- `user_id`: Filter by user
- `date_from`: Start date (YYYY-MM-DD)
- `date_to`: End date (YYYY-MM-DD)
- `min_value`: Minimum estimated value
- `max_value`: Maximum estimated value

**Example:**
```http
GET /api/purchase-requests/search?q=laptop&status=APPROVED&date_from=2025-01-01
```

**Response:** (Same format as Get All PRs)

## ❌ Error Responses

### Standard Error Format
```json
{
    "success": false,
    "message": "Error description",
    "errors": {
        "field_name": [
            "Validation error message"
        ]
    }
}
```

### Common Error Codes

#### 400 Bad Request
```json
{
    "success": false,
    "message": "Validation failed",
    "errors": {
        "description": ["The description field is required."],
        "items": ["At least one item is required."]
    }
}
```

#### 401 Unauthorized
```json
{
    "success": false,
    "message": "Unauthenticated",
    "errors": null
}
```

#### 403 Forbidden
```json
{
    "success": false,
    "message": "This action is unauthorized.",
    "errors": null
}
```

#### 404 Not Found
```json
{
    "success": false,
    "message": "Purchase Request not found",
    "errors": null
}
```

#### 422 Unprocessable Entity
```json
{
    "success": false,
    "message": "Cannot edit PR with status APPROVED",
    "errors": null
}
```

#### 500 Internal Server Error
```json
{
    "success": false,
    "message": "Internal server error",
    "errors": null
}
```

## 📝 Rate Limiting

- **Rate Limit**: 60 requests per minute per user
- **Header**: `X-RateLimit-Remaining` indicates remaining requests
- **Reset**: `X-RateLimit-Reset` indicates reset time

## 🔧 Testing Examples

### Postman Collection Example

#### Environment Variables
```json
{
    "base_url": "https://your-domain.com/api",
    "token": "1|abcdef123456..."
}
```

#### Pre-request Script (for authenticated requests)
```javascript
pm.request.headers.add({
    key: 'Authorization',
    value: 'Bearer ' + pm.environment.get('token')
});
```

### cURL Examples

#### Login
```bash
curl -X POST "https://your-domain.com/api/login" \
     -H "Content-Type: application/json" \
     -d '{
       "email": "user@company.com",
       "password": "password123"
     }'
```

#### Create PR
```bash
curl -X POST "https://your-domain.com/api/purchase-requests" \
     -H "Authorization: Bearer 1|abcdef123456..." \
     -H "Content-Type: application/json" \
     -d '{
       "description": "Test PR via API",
       "request_date": "2025-01-15",
       "due_date": "2025-01-22",
       "location": "API Test",
       "items": [
         {
           "nama_item": "Test Item",
           "quantity": 1,
           "unit": "Unit",
           "estimated_price": 100000,
           "description": "API test item"
         }
       ]
     }'
```

#### Get PR List
```bash
curl -X GET "https://your-domain.com/api/purchase-requests?status=APPROVED" \
     -H "Authorization: Bearer 1|abcdef123456..."
```

---

**Prev:** [Admin Guide](admin-guide.md) | **Next:** [SPK Documentation](../spk/overview.md)
