# 📚 System Documentation

Welcome to the comprehensive documentation for our enterprise management system. This documentation covers all major modules and provides detailed guides for users, administrators, and developers.

## 🚀 Quick Start

### For New Users
1. **Start Here**: [Purchase Request User Guide](purchase-request/user-guide.md)
2. **Understanding Workflows**: [Purchase Request Workflow](purchase-request/workflow.md)
3. **System Overview**: [Purchase Request Overview](purchase-request/overview.md)

### For Administrators
1. **Admin Setup**: [Purchase Request Admin Guide](purchase-request/admin-guide.md)
2. **User Management**: [User Management Overview](user-management/overview.md)
3. **System Configuration**: [Database Schema](database/schema.md)

### For Developers
1. **API Documentation**: [Purchase Request API](purchase-request/api-reference.md)
2. **Database Design**: [Database Documentation](database/overview.md)
3. **Integration Guides**: [API Integration](api/integration.md)

## 📋 System Modules

### 🛒 Purchase Request System
Complete purchase requisition and approval workflow system with multi-level authorization (Department Head → GA → Finance → CFO → CEO) and purchasing department integration.

- **[Overview](purchase-request/overview.md)** - System architecture, database schema, and business rules
- **[Workflow Guide](purchase-request/workflow.md)** - Detailed approval process flows and status transitions
- **[User Guide](purchase-request/user-guide.md)** - End-user documentation with step-by-step guides
- **[Admin Guide](purchase-request/admin-guide.md)** - System administration, user management, and troubleshooting
- **[API Reference](purchase-request/api-reference.md)** - Complete REST API documentation with examples

**Key Features:**
- Multi-level approval workflow with role-based authorization
- Purchasing department notification and processing system
- CEO/CFO user levels for executive oversight
- Comprehensive audit trails and reporting
- Real-time notifications with session-based dismissal

### � SPK (Service Package) System
Service package and work order management system for project tracking and resource allocation.

- **[Overview](spk/overview.md)** - System architecture, workflow definitions, and business rules
- **[Workflow Guide](spk/workflow.md)** - Complete process flows from creation to completion
- **[User Guide](spk/user-guide.md)** - User operation manual with task management
- **[Admin Guide](spk/admin-guide.md)** - Administrative procedures and system maintenance
- **[API Reference](spk/api-reference.md)** - Technical integration documentation

**Key Features:**
- Project lifecycle management (Draft → Submit → Approve → Execute → Complete)
- Task assignment and progress tracking
- Resource allocation and budget monitoring
- Quality checkpoints and stakeholder approval
- Performance analytics and KPI tracking

### 👥 User Management System
Centralized user, role, and permission management with 7-level hierarchy (USER → PURCHASING → DEPT_HEAD → GA → FINANCE_DEPT → CFO → CEO).

- **[Overview](user-management/overview.md)** - User roles, permissions matrix, and security features
- **[Admin Guide](user-management/admin-guide.md)** - User administration and lifecycle management
- **[Security Guide](user-management/security.md)** - Security policies and audit procedures

**Key Features:**
- Hierarchical user level system with granular permissions
- Department-based access control with optional divisi for executives
- Session management with security monitoring
- Bulk user operations and lifecycle management
- Comprehensive audit logging and activity tracking

### 🗄️ Database Documentation
Database schema and data management guides with migration procedures.

- **[Schema Overview](database/schema.md)** - Complete database structure and relationships
- **[Migration Guide](database/migrations.md)** - Database update procedures and version control
- **[Backup Procedures](database/backup.md)** - Data protection and recovery strategies

### 🔌 API Documentation
RESTful API guides and integration documentation for system interoperability.

- **[API Overview](api/overview.md)** - API architecture and design principles
- **[Integration Guide](api/integration.md)** - Implementation guide with code examples
- **[Authentication](api/authentication.md)** - Security protocols and token management

## 🎯 User Role Guides

### For Regular Users (Level 1)
- [Creating Purchase Requests](purchase-request/user-guide.md#membuat-purchase-request-baru) - Step-by-step PR creation process
- [Managing SPK Projects](spk/user-guide.md#project-management) - Personal project tracking
- [Understanding Approval Workflows](purchase-request/workflow.md#approval-process) - How approvals work

### For Department Heads (Level 3)
- [Approval Responsibilities](purchase-request/workflow.md#approval-process) - First-level approval duties
- [Team Management](user-management/overview.md#dept_head-level-3) - Managing department users
- [Budget Oversight](purchase-request/admin-guide.md#reporting--analytics) - Department budget monitoring

### For Purchasing Team (Level 2)
- [Processing Approved PRs](purchase-request/user-guide.md#purchasing-process) - Purchasing workflow guide
- [Vendor Management](purchase-request/admin-guide.md#notification-settings) - Supplier relationship management
- [Purchase Analytics](purchase-request/api-reference.md#status--analytics-endpoints) - Performance reporting

### For Finance Department (Level 5)
- [Financial Approvals](purchase-request/workflow.md#multi-level-approval-flow) - Finance approval process
- [Budget Control](purchase-request/admin-guide.md#reporting--analytics) - Financial oversight tools
- [Cost Analysis](purchase-request/api-reference.md#get-pr-statistics) - Financial reporting and analytics

### For Executives (CFO Level 6, CEO Level 7)
- [Executive Approvals](purchase-request/workflow.md#approval-decision-matrix) - High-value approval authority
- [Strategic Oversight](user-management/overview.md#cfo-level-6) - Executive dashboard access
- [System Governance](user-management/overview.md#ceo-level-7) - Ultimate system authority

### For System Administrators
- [User Management](user-management/admin-guide.md) - Complete user administration guide
- [System Monitoring](purchase-request/admin-guide.md#application-monitoring) - Performance and health monitoring
- [Backup & Recovery](database/backup.md) - Data protection procedures

## 🔧 Technical Architecture

### System Components
```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│ Purchase Request│    │ SPK Management  │    │ User Management │
│ Module          │    │ Module          │    │ Module          │
│                 │    │                 │    │                 │
│ - Multi-level   │    │ - Project       │    │ - 7-level       │
│   approval      │    │   tracking      │    │   hierarchy     │
│ - Purchasing    │    │ - Task mgmt     │    │ - Role-based    │
│   integration   │    │ - Progress      │    │   permissions   │
│ - Notifications │    │   monitoring    │    │ - Audit logs    │
└─────────────────┘    └─────────────────┘    └─────────────────┘
         │                       │                       │
         └───────────────────────┼───────────────────────┘
                                 │
         ┌─────────────────────────────────────────────────┐
         │            Database Layer (MySQL 8.0+)         │
         │  ┌─────────┐  ┌─────────┐  ┌─────────────────┐  │
         │  │  users  │  │  spks   │  │ spks (PRs)      │  │
         │  │ - levels│  │ - items │  │ - multi-approval│  │
         │  │ - divisi│  │ - tasks │  │ - status flow   │  │
         │  │ - perms │  │ - flow  │  │ - purchasing    │  │
         │  └─────────┘  └─────────┘  └─────────────────┘  │
         └─────────────────────────────────────────────────┘
```

### Technology Stack
- **Backend**: Laravel 11+ (PHP 8.1+) with advanced middleware and authorization
- **Frontend**: Blade Templates + Tailwind CSS + JavaScript (session-based notifications)
- **Database**: MySQL 8.0+ with optimized indexes and relationships
- **Authentication**: Laravel Sanctum with role-based access control
- **Notifications**: Laravel Mail + Queue System with real-time updates

### Key Technical Features
- **Authorization**: Complex multi-level permission system with role-based access
- **Notifications**: Dual notification system (approval + purchasing) with session storage
- **Database**: Optimized schema with proper foreign keys and indexes
- **API**: RESTful endpoints with comprehensive error handling
- **Security**: Session management, audit logging, and access control

## 📊 System Statistics & Capabilities

### Current Implementation Status
✅ **Completed Features:**
- Multi-level Purchase Request approval workflow (5 levels: Dept Head → GA → Finance → CFO → CEO)
- SPK management with project lifecycle tracking
- User management with 7-level hierarchy
- Purchasing department integration with status updates
- CEO/CFO user levels with optional divisi configuration
- Comprehensive notification system with role-based targeting
- Form validation with dynamic JavaScript behavior
- Session-based notification dismissal system
- Comprehensive API with authentication and error handling

### Performance Metrics
- **Approval Flow**: 5-level approval process with proper authorization
- **User Levels**: 7 distinct levels with granular permissions
- **Notification System**: Dual system (approval + purchasing) with session persistence
- **Database Optimization**: Proper indexing and relationship management
- **API Coverage**: Complete CRUD operations with advanced filtering

## 📞 Support & Maintenance

### Getting Help
- **Technical Issues**: Check troubleshooting sections in admin guides
- **User Training**: Review user guides and workflow documentation
- **Feature Requests**: Contact system administrator
- **API Integration**: Reference comprehensive API documentation

### Maintenance Schedule
- **Daily**: Automated backups, log monitoring, notification system health
- **Weekly**: Performance review, security updates, user activity analysis
- **Monthly**: Full system health check, database optimization
- **Quarterly**: Documentation review, security audit, performance tuning

### Emergency Procedures
- **System Down**: Follow recovery procedures in admin guides
- **Data Issues**: Reference backup and recovery documentation
- **Security Breach**: Implement security protocols from user management guide
- **Performance Issues**: Use monitoring guides for diagnosis

## 🔄 Version History

### Current Version: 2.1 (January 2025)
- ✅ Enhanced Purchase Request system with complete 5-level approval workflow
- ✅ SPK management with comprehensive project tracking
- ✅ Advanced user management with CEO/CFO levels and optional divisi
- ✅ Purchasing department integration with status update system
- ✅ Dual notification system with session-based dismissal
- ✅ Comprehensive API documentation with examples
- ✅ Role-based access control with 7-level hierarchy
- ✅ Complete documentation structure with detailed guides

### Technical Improvements in v2.1
- Enhanced authorization logic in controllers for proper access control
- Improved UI with responsive design and better notification positioning
- Advanced form validation with JavaScript behavior for user levels
- Session-based notification system with proper dismissal tracking
- Comprehensive documentation with organized folder structure

### Planned Updates (v2.2+)
- 🔄 Mobile-responsive interface improvements
- 🔄 Advanced reporting dashboard with analytics
- 🔄 Integration with external ERP systems
- 🔄 Automated workflow optimization
- 🔄 Enhanced audit and compliance features

---

*Last Updated: January 2025*
*Documentation Version: 2.1*
*System Version: 2.1*
