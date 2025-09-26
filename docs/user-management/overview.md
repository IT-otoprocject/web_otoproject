# 👥 User Management System - Overview

## 🎯 System Purpose

User Management System adalah sistem pusat untuk mengelola user, roles, permissions, dan access control dalam aplikasi. Sistem ini mengintegrasikan semua module dan mengatur authorization untuk Purchase Request, SPK, dan sistem lainnya.

## 🏗️ Architecture Overview

### Database Schema
```sql
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│     users       │    │  user_sessions  │    │ system_access   │
├─────────────────┤    ├─────────────────┤    ├─────────────────┤
│ id (PK)         │    │ id (PK)         │    │ id (PK)         │
│ name            │    │ user_id (FK)    │    │ user_id (FK)    │
│ email (unique)  │    │ ip_address      │    │ module_name     │
│ password        │    │ user_agent      │    │ permissions     │
│ level (enum)    │    │ last_activity   │    │ granted_at      │
│ divisi          │    │ created_at      │    │ expires_at      │
│ email_verified  │    │ updated_at      │    │ created_at      │
│ created_at      │    └─────────────────┘    │ updated_at      │
│ updated_at      │                           └─────────────────┘
└─────────────────┘
```

### User Level Hierarchy
```
┌─────────────────┐
│      CEO        │ ← Highest Authority (Level 7)
├─────────────────┤
│      CFO        │ ← Financial Authority (Level 6)  
├─────────────────┤
│   FINANCE_DEPT  │ ← Finance Department (Level 5)
├─────────────────┤
│       GA        │ ← General Affairs (Level 4)
├─────────────────┤
│   DEPT_HEAD     │ ← Department Heads (Level 3)
├─────────────────┤
│   PURCHASING    │ ← Purchasing Team (Level 2)
├─────────────────┤
│      USER       │ ← Regular Users (Level 1)
└─────────────────┘
```

## 🔐 User Levels & Permissions

### Level Definitions

#### 👤 USER (Level 1)
**Purpose:** Regular employees who use the system for daily operations
```
Capabilities:
├── Create own Purchase Requests
├── Create own SPKs
├── View own submissions
├── Edit own drafts
├── Update personal profile
└── Access basic reports

Restrictions:
├── Cannot approve others' requests
├── Cannot access admin functions
├── Cannot view system-wide data
├── Cannot manage other users
└── Limited to own department data
```

#### 🛒 PURCHASING (Level 2)  
**Purpose:** Purchasing department staff who process approved requests
```
Capabilities:
├── All USER permissions
├── View all approved Purchase Requests
├── Process purchasing workflow
├── Update PR status to COMPLETED
├── Access vendor management
├── Generate purchase reports
└── Manage purchase orders

Special Features:
├── Purchasing notification system
├── Vendor comparison tools
├── Purchase order generation
├── Delivery tracking
└── Budget variance reporting
```

#### 👔 DEPT_HEAD (Level 3)
**Purpose:** Department heads who approve requests from their teams
```
Capabilities:
├── All USER permissions
├── View department PRs and SPKs
├── Approve/reject department requests
├── Access department analytics
├── Manage department users (limited)
├── Override department decisions
└── Department budget oversight

Approval Authority:
├── Purchase Requests: First level approval
├── SPKs: Department level approval
├── Budget: Department allocation
├── Personnel: Department staff
└── Equipment: Department assets
```

#### 🏢 GA (Level 4)
**Purpose:** General Affairs staff who handle operational approvals
```
Capabilities:
├── All DEPT_HEAD permissions
├── Cross-department visibility
├── Facility management
├── Asset management approval
├── Vendor relationship management
├── Compliance oversight
└── Operational policy enforcement

Approval Authority:
├── Purchase Requests: Second level approval
├── Facility-related requests
├── Asset procurement
├── Vendor registrations
└── Operational procedures
```

#### 💰 FINANCE_DEPT (Level 5)
**Purpose:** Finance department staff who handle budget and financial approvals
```
Capabilities:
├── All GA permissions
├── Full financial oversight
├── Budget management
├── Financial reporting
├── Cost center management
├── Financial compliance
└── Investment decisions

Approval Authority:
├── Purchase Requests: Financial approval
├── Budget allocations
├── Capital expenditures
├── Investment decisions
└── Financial policy changes
```

#### 💼 CFO (Level 6)
**Purpose:** Chief Financial Officer with executive financial authority
```
Capabilities:
├── All FINANCE_DEPT permissions
├── Strategic financial planning
├── Executive financial oversight
├── High-value approvals
├── Financial policy creation
├── Board-level reporting
└── M&A financial decisions

Special Features:
├── Executive dashboard access
├── Board reporting tools
├── Strategic planning interface
├── Financial risk management
└── Corporate governance tools

Approval Authority:
├── High-value Purchase Requests (>50M)
├── Capital investments
├── Budget revisions
├── Financial policies
└── Executive decisions
```

#### 🎯 CEO (Level 7)
**Purpose:** Chief Executive Officer with ultimate system authority
```
Capabilities:
├── All system permissions
├── Strategic decision making
├── Policy creation and modification
├── System configuration
├── User management override
├── Emergency access controls
└── Audit and compliance oversight

Special Features:
├── System-wide access
├── Override all restrictions
├── Emergency procedures
├── Strategic planning tools
├── Executive reporting
└── Governance controls

Ultimate Authority:
├── Final approval authority
├── System policy changes
├── Emergency overrides
├── Strategic direction
└── Corporate governance
```

## 🔑 Permission Matrix

### Module Access Rights
| Module | USER | PURCHASING | DEPT_HEAD | GA | FINANCE_DEPT | CFO | CEO |
|--------|------|------------|-----------|----|--------------|----|-----|
| **Dashboard** | ✅ Personal | ✅ Purchasing | ✅ Department | ✅ Operational | ✅ Financial | ✅ Executive | ✅ Strategic |
| **Purchase Request** | ✅ Create/View Own | ✅ Process Approved | ✅ Approve Dept | ✅ Approve GA | ✅ Finance Approval | ✅ CFO Approval | ✅ CEO Approval |
| **SPK Management** | ✅ Create/View Own | ✅ Service Delivery | ✅ Approve Dept | ✅ Operational | ✅ Budget Control | ✅ Strategic | ✅ Override |
| **User Management** | ❌ | ❌ | ✅ Dept Users | ✅ GA Users | ✅ Finance Users | ✅ Executive | ✅ All Users |
| **Reports** | ✅ Personal | ✅ Purchasing | ✅ Department | ✅ Operational | ✅ Financial | ✅ Executive | ✅ Strategic |
| **System Admin** | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ Limited | ✅ Full |

### Action-Level Permissions
| Action | USER | PURCHASING | DEPT_HEAD | GA | FINANCE_DEPT | CFO | CEO |
|--------|------|------------|-----------|----|--------------|----|-----|
| Create PR | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ | ✅ |
| Approve PR Level 1 | ❌ | ❌ | ✅ | ❌ | ❌ | ❌ | ✅ |
| Approve PR Level 2 | ❌ | ❌ | ❌ | ✅ | ❌ | ❌ | ✅ |
| Approve PR Level 3 | ❌ | ❌ | ❌ | ❌ | ✅ | ❌ | ✅ |
| Approve PR Level 4 | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ | ✅ |
| Final PR Approval | ❌ | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ |
| Process Purchase | ❌ | ✅ | ❌ | ❌ | ❌ | ❌ | ✅ |
| Create User | ❌ | ❌ | ✅ Dept | ✅ GA | ✅ Finance | ✅ Executive | ✅ All |
| Delete User | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ | ✅ |
| System Config | ❌ | ❌ | ❌ | ❌ | ❌ | ✅ Limited | ✅ Full |

## 🏢 Divisi (Department) Management

### Department Structure
```
Company Organization:
├── EXECUTIVE (CEO, CFO, Executive Team)
├── FINANCE (Finance Department)
├── HCGA (Human Capital & General Affairs)
├── IT (Information Technology)
├── MARKETING (Marketing Department)
├── SALES (Sales Department)
├── OPERATIONS (Operations Department)
├── PROCUREMENT (Purchasing Department)
└── LEGAL (Legal Department)
```

### Divisi-Based Permissions
```php
// Example permission logic
public function canViewPR(PurchaseRequest $pr, User $user): bool
{
    // CEO and CFO can view all
    if (in_array($user->level, ['CEO', 'CFO'])) {
        return true;
    }
    
    // Finance department can view all after submission
    if ($user->level === 'FINANCE_DEPT' && $pr->status !== 'DRAFT') {
        return true;
    }
    
    // GA can view all submitted PRs
    if ($user->level === 'GA' && $pr->status !== 'DRAFT') {
        return true;
    }
    
    // Department heads can view their department's PRs
    if ($user->level === 'DEPT_HEAD' && $pr->user->divisi === $user->divisi) {
        return true;
    }
    
    // Purchasing can view approved PRs
    if ($user->level === 'PURCHASING' && $pr->status === 'APPROVED') {
        return true;
    }
    
    // Users can view their own PRs
    if ($pr->user_id === $user->id) {
        return true;
    }
    
    return false;
}
```

## 📊 User Management Features

### User Registration & Onboarding
```
Registration Process:
├── Admin creates user account
├── System generates temporary password
├── Welcome email sent to user
├── User logs in and changes password
├── Profile completion required
├── Department assignment
├── System access training
└── Account activation
```

### Profile Management
```
User Profile Fields:
┌─────────────────────────────────────┐
│ Personal Information                │
├─────────────────────────────────────┤
│ Full Name: [Required]               │
│ Email: [Required, unique]           │
│ Phone: [Optional]                   │
│ Employee ID: [Optional]             │
│ Job Title: [Optional]               │
│ Department: [Required for most]     │
│ Manager: [Auto-assigned]            │
│ Profile Photo: [Optional]           │
└─────────────────────────────────────┘

System Settings:
├── Language Preference
├── Timezone
├── Notification Preferences
├── Dashboard Layout
└── Report Format Preferences
```

### Password Management
```
Password Requirements:
├── Minimum 8 characters
├── Must include uppercase letter
├── Must include lowercase letter  
├── Must include number
├── Must include special character
├── Cannot reuse last 5 passwords
├── Must change every 90 days
└── Account lockout after 5 failed attempts

Recovery Process:
├── Self-service password reset
├── Email verification required
├── Security questions backup
├── Admin override capability
└── Audit trail logging
```

## 🔐 Security Features

### Authentication Methods
```
Primary Authentication:
├── Email + Password
├── Session-based authentication
├── Remember me functionality
├── Multi-device support
└── Automatic session timeout

Enhanced Security:
├── IP-based restrictions (configurable)
├── Failed login attempt monitoring
├── Suspicious activity detection
├── Admin notification alerts
└── Emergency account lockdown
```

### Session Management
```php
// Session configuration
'lifetime' => 120, // 2 hours
'expire_on_close' => false,
'encrypt' => true,
'http_only' => true,
'same_site' => 'lax',

// Session tracking
user_sessions table tracks:
├── User ID
├── IP Address
├── User Agent
├── Last Activity
├── Session Data
└── Logout Method
```

### Audit Logging
```
Activity Tracking:
├── Login/Logout events
├── Password changes
├── Profile updates
├── Permission changes
├── Data access logs
├── Failed access attempts
├── Admin actions
└── System configuration changes

Log Data Structure:
├── Timestamp
├── User ID
├── Action Type
├── Resource Affected
├── IP Address
├── User Agent
├── Result (Success/Failure)
└── Additional Context
```

## 👨‍💼 Administrative Functions

### User Creation Process
```
Admin Dashboard → User Management → Create New User

Step 1: Basic Information
┌─────────────────────────────────────┐
│ User Creation Form                  │
├─────────────────────────────────────┤
│ Full Name: [Required]               │
│ Email: [Required, unique]           │
│ Initial Password: [Generated/Manual]│
│ User Level: [Dropdown selection]    │
│ Department: [Required*]             │
│ Manager: [Auto-populate based on dept]│
│ Start Date: [Employee start date]   │
│ Notes: [Admin notes]                │
└─────────────────────────────────────┘

*Note: CEO and CFO levels make department optional

Step 2: Permission Assignment
├── Auto-assigned based on level
├── Custom permissions (if needed)
├── Module access configuration
├── Approval authority setup
└── Notification preferences

Step 3: Activation
├── Account activation
├── Welcome email dispatch
├── Temporary password provision
├── Training materials assignment
└── Manager notification
```

### Bulk User Operations
```
Batch Operations:
├── Bulk user import (CSV/Excel)
├── Bulk permission updates
├── Bulk department transfers
├── Bulk account deactivation
├── Bulk password reset
└── Bulk notification send

Import Format:
name,email,level,divisi,manager_email
John Doe,john@company.com,USER,IT,manager@company.com
Jane Smith,jane@company.com,DEPT_HEAD,MARKETING,ceo@company.com
```

### User Lifecycle Management
```
Employee Onboarding:
├── Account creation
├── System access provision
├── Training material assignment
├── Buddy/mentor assignment
└── Progress monitoring

Role Changes:
├── Promotion handling
├── Department transfer
├── Permission adjustment
├── Data migration
└── Notification updates

Employee Offboarding:
├── Account deactivation
├── Data retention/transfer
├── Access revocation
├── Equipment return tracking
└── Exit documentation
```

## 📈 User Analytics & Reporting

### User Activity Reports
```sql
-- Active users report
SELECT 
    u.name,
    u.level,
    u.divisi,
    us.last_activity,
    COUNT(us.id) as session_count
FROM users u
LEFT JOIN user_sessions us ON u.id = us.user_id
WHERE us.last_activity >= DATE_SUB(NOW(), INTERVAL 30 DAY)
GROUP BY u.id
ORDER BY us.last_activity DESC;
```

### Permission Usage Analytics
```sql
-- Permission usage by level
SELECT 
    level,
    COUNT(*) as user_count,
    AVG(DATEDIFF(NOW(), last_login)) as avg_days_since_login
FROM users 
WHERE created_at >= DATE_SUB(NOW(), INTERVAL 90 DAY)
GROUP BY level
ORDER BY user_count DESC;
```

### System Adoption Metrics
```
Key Metrics:
├── Daily Active Users (DAU)
├── Monthly Active Users (MAU)
├── Feature adoption rates
├── Session duration averages
├── Mobile vs. desktop usage
├── Geographic usage patterns
└── Peak usage times

Department Analytics:
├── Department-wise adoption
├── Feature usage by department
├── Approval efficiency metrics
├── Request volume trends
└── Performance comparisons
```

## 🔧 System Configuration

### Level Management
```php
// User levels enum configuration
enum UserLevel: string
{
    case USER = 'USER';
    case PURCHASING = 'PURCHASING';
    case DEPT_HEAD = 'DEPT_HEAD';
    case GA = 'GA';
    case FINANCE_DEPT = 'FINANCE_DEPT';
    case CFO = 'CFO';
    case CEO = 'CEO';
    
    public function getNumericLevel(): int
    {
        return match($this) {
            self::USER => 1,
            self::PURCHASING => 2,
            self::DEPT_HEAD => 3,
            self::GA => 4,
            self::FINANCE_DEPT => 5,
            self::CFO => 6,
            self::CEO => 7,
        };
    }
}
```

### Department Configuration
```php
// Divisi configuration
protected $divisiOptions = [
    'EXECUTIVE' => 'Executive Management',
    'FINANCE' => 'Finance Department',
    'HCGA' => 'Human Capital & General Affairs',
    'IT' => 'Information Technology',
    'MARKETING' => 'Marketing Department',
    'SALES' => 'Sales Department',
    'OPERATIONS' => 'Operations Department',
    'PROCUREMENT' => 'Purchasing Department',
    'LEGAL' => 'Legal Department',
];

// CEO and CFO can have null divisi
protected $optionalDivisiLevels = ['CEO', 'CFO'];
```

---

**Next:** [User Management Admin Guide](admin-guide.md) | [Database Documentation](../database/schema.md)
