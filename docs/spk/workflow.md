# 🔄 SPK Workflow Guide

## 📋 Complete Workflow Overview

### Process Flow Diagram
```
┌─────────────┐    ┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│   CREATE    │───▶│   SUBMIT    │───▶│   APPROVE   │───▶│   EXECUTE   │
│   (DRAFT)   │    │ (SUBMITTED) │    │(IN_PROGRESS)│    │ (COMPLETED) │
└─────────────┘    └─────────────┘    └─────────────┘    └─────────────┘
       │                   │                   │                   │
       ▼                   ▼                   ▼                   ▼
┌─────────────┐    ┌─────────────┐    ┌─────────────┐    ┌─────────────┐
│   DELETE    │    │   REJECT    │    │   ON_HOLD   │    │   ARCHIVE   │
│             │    │             │    │             │    │             │
└─────────────┘    └─────────────┘    └─────────────┘    └─────────────┘
```

## 🎯 Phase 1: SPK Creation (DRAFT)

### User Actions
1. **Access SPK Module**
   ```
   Dashboard → SPK Management → Create New SPK
   ```

2. **Fill Basic Information**
   ```
   ┌─────────────────────────────────────┐
   │ SPK Information                     │
   ├─────────────────────────────────────┤
   │ Description: [Project description]  │
   │ Due Date: [Target completion]       │
   │ Priority: [Low/Medium/High/Urgent]  │
   │ Location: [Work location]           │
   │ Department: [Responsible dept]      │
   │ Project Type: [Service/Product]     │
   └─────────────────────────────────────┘
   ```

3. **Add SPK Items/Tasks**
   ```
   For each item:
   ┌─────────────────────────────────────┐
   │ Task/Item Details                   │
   ├─────────────────────────────────────┤
   │ Name: [Task/service name]           │
   │ Description: [Detailed specs]       │
   │ Quantity: [Amount needed]           │
   │ Unit: [Measurement unit]            │
   │ Estimated Cost: [Budget estimate]   │
   │ Estimated Hours: [Time needed]      │
   │ Dependencies: [Prerequisites]       │
   │ Skills Required: [Technical needs]  │
   └─────────────────────────────────────┘
   ```

4. **Review & Save Draft**
   - Validate all mandatory fields
   - Check cost estimates
   - Save for later editing or submit

### Business Rules - Creation Phase
- ✅ Description minimum 20 characters
- ✅ Due date must be at least 3 days from now
- ✅ At least 1 task/item required
- ✅ Total estimated cost validation per user level
- ✅ Duplicate SPK number prevention

### Auto-Generated Fields
```php
SPK Number Format: SPK-{YYYY}{MM}{DD}-{###}
Example: SPK-20250115-001

Created By: [Current user ID]
Created At: [Current timestamp]
Status: DRAFT
Initial Approval Level: dept_head
```

## 📤 Phase 2: Submission Process (SUBMITTED)

### Pre-Submission Validation
```
┌─────────────────────────────────────┐
│ Validation Checklist               │
├─────────────────────────────────────┤
│ ✓ All mandatory fields completed   │
│ ✓ At least 1 task/item added       │
│ ✓ Cost estimates provided           │
│ ✓ Due date is reasonable            │
│ ✓ Resource availability checked     │
│ ✓ Budget allocation confirmed       │
└─────────────────────────────────────┘
```

### Submission Triggers
1. **User clicks "Submit SPK"**
2. **System validations run**
3. **Status changes to SUBMITTED**
4. **Approval notifications sent**
5. **Edit permissions revoked**

### Automatic Notifications
```
Notification Recipients:
├── Direct Manager/Team Lead
├── Department Head (if different)
├── Project Manager (if assigned)
└── Budget Controller (for high-value SPKs)

Notification Content:
├── SPK Number & Description
├── Requester Information
├── Estimated Cost & Timeline
├── Approval Action Required
└── Link to SPK Details
```

## ✅ Phase 3: Approval Workflow

### Multi-Level Approval Flow
```
Level 1: Team Lead Approval
├── Scope: Technical feasibility
├── Focus: Resource availability
├── Authority: Up to 10M IDR
└── Timeline: 2 business days

        ↓ (if approved)

Level 2: Project Manager Review  
├── Scope: Project alignment
├── Focus: Priority & planning
├── Authority: Up to 50M IDR
└── Timeline: 3 business days

        ↓ (if approved)

Level 3: Department Head Approval
├── Scope: Budget & strategy
├── Focus: Department priorities
├── Authority: Unlimited
└── Timeline: 5 business days
```

### Approval Decision Matrix
| Estimated Cost | Team Lead | PM Required | Dept Head | Additional |
|---------------|-----------|-------------|-----------|------------|
| < 5M IDR | ✅ Final | ❌ | ❌ | - |
| 5M - 10M IDR | ✅ Required | ✅ Final | ❌ | - |
| 10M - 50M IDR | ✅ Required | ✅ Required | ✅ Final | - |
| > 50M IDR | ✅ Required | ✅ Required | ✅ Required | CEO Review |

### Approval Actions

#### Approve Action
```
Approver fills:
┌─────────────────────────────────────┐
│ Approval Form                       │
├─────────────────────────────────────┤
│ Decision: [APPROVE]                 │
│ Notes: [Optional feedback]          │
│ Conditions: [Any special terms]     │
│ Budget Adjustment: [If needed]      │
│ Timeline Adjustment: [If needed]    │
└─────────────────────────────────────┘

Result:
- Status progresses to next level
- Notifications sent to next approver
- If final approval: Status → IN_PROGRESS
```

#### Reject Action
```
Approver fills:
┌─────────────────────────────────────┐
│ Rejection Form                      │
├─────────────────────────────────────┤
│ Decision: [REJECT]                  │
│ Reason: [Mandatory explanation]     │
│ Suggestions: [Improvement ideas]    │
│ Resubmit Allowed: [Yes/No]         │
└─────────────────────────────────────┘

Result:
- Status changes to REJECTED
- Notification sent to requester
- SPK returns to DRAFT (if resubmit allowed)
```

#### Request Info Action
```
Approver can request additional information:
┌─────────────────────────────────────┐
│ Information Request                 │
├─────────────────────────────────────┤
│ Questions: [Specific queries]       │
│ Required Docs: [Additional files]   │
│ Deadline: [Response timeline]       │
└─────────────────────────────────────┘

Result:
- Status: PENDING_INFO
- Notification to requester
- Timer starts for response
```

## 🚀 Phase 4: Execution (IN_PROGRESS)

### Project Initiation
1. **Team Assignment**
   ```
   Project Manager:
   ├── Reviews approved SPK
   ├── Assigns team members
   ├── Creates detailed work plan
   └── Sets up tracking mechanisms
   ```

2. **Resource Allocation**
   ```
   Resources Assigned:
   ├── Human Resources (team members)
   ├── Equipment & Tools
   ├── Budget allocation
   ├── Workspace/location
   └── External vendors (if needed)
   ```

### Task Management

#### Task Breakdown
```
SPK Tasks are broken down into:
┌─────────────────────────────────────┐
│ Task Structure                      │
├─────────────────────────────────────┤
│ Task ID: [Unique identifier]        │
│ Task Name: [Descriptive name]       │
│ Description: [Detailed specs]       │
│ Assigned To: [Team member]          │
│ Estimated Hours: [Time allocation]  │
│ Dependencies: [Prerequisites]       │
│ Due Date: [Task deadline]           │
│ Status: [Not Started/In Progress/   │
│         Completed/Blocked]          │
└─────────────────────────────────────┘
```

#### Progress Tracking
```
Team members update progress:
├── Daily standup updates
├── Task completion percentage
├── Time spent vs. estimated
├── Issues/blockers encountered
└── Next steps planned

Project Manager monitors:
├── Overall SPK progress
├── Resource utilization
├── Budget consumption
├── Timeline adherence
└── Quality checkpoints
```

### Status Updates During Execution

#### Regular Updates
- **Daily**: Task progress updates by team members
- **Weekly**: Overall SPK status review
- **Milestone**: Major deliverable completions
- **Issue**: Immediate notification of blockers

#### Progress Calculation
```php
Overall Progress = (Σ(Task Progress × Task Weight)) / Total Tasks

Example:
Task A (50% complete, 40% weight) = 20%
Task B (100% complete, 30% weight) = 30% 
Task C (25% complete, 30% weight) = 7.5%
--------------------------------
Total SPK Progress = 57.5%
```

## ⏸️ Phase 5: Exception Handling

### ON_HOLD Status
**Triggers:**
- Dependencies not met
- Resource unavailability
- Budget constraints
- External factors

**Process:**
1. **Hold Request**
   ```
   ┌─────────────────────────────────────┐
   │ Hold Request Form                   │
   ├─────────────────────────────────────┤
   │ Reason: [Detailed explanation]      │
   │ Expected Duration: [Time estimate]  │
   │ Impact Assessment: [Effect on other]│
   │ Mitigation Plan: [Alternative plan] │
   └─────────────────────────────────────┘
   ```

2. **Approval Required**
   - Team Lead approval for short holds (<1 week)
   - PM approval for medium holds (1-4 weeks)
   - Dept Head approval for long holds (>4 weeks)

3. **Resource Reallocation**
   - Team members assigned to other projects
   - Equipment/tools returned to pool
   - Budget held but not consumed

### CANCELLED Status
**Triggers:**
- Business priorities changed
- Budget cuts
- Technical impossibility discovered
- Stakeholder decision

**Process:**
1. **Cancellation Request** (requires Dept Head approval)
2. **Impact Assessment** (effect on other projects)
3. **Resource Recovery** (reallocation planning)
4. **Documentation** (lessons learned)

## ✅ Phase 6: Completion (COMPLETED)

### Completion Criteria
```
All criteria must be met:
├── ✓ All tasks marked as completed
├── ✓ Deliverables delivered & accepted
├── ✓ Quality review passed
├── ✓ Budget within approved limits
├── ✓ Timeline met (or justified variance)
├── ✓ Documentation completed
└── ✓ Stakeholder sign-off received
```

### Completion Process
1. **Pre-Completion Review**
   ```
   Team Lead Reviews:
   ├── Task completion verification
   ├── Quality standards met
   ├── Documentation complete
   └── Deliverables ready
   ```

2. **Stakeholder Acceptance**
   ```
   SPK Requester:
   ├── Reviews deliverables
   ├── Tests functionality/quality
   ├── Provides feedback
   └── Signs off on completion
   ```

3. **Final Approval**
   ```
   Project Manager:
   ├── Validates all criteria met
   ├── Reviews stakeholder acceptance
   ├── Updates SPK status to COMPLETED
   └── Triggers completion notifications
   ```

### Post-Completion Activities

#### Resource Release
- Team members released to other projects
- Equipment returned to inventory
- Final budget reconciliation
- Vendor payments processed

#### Documentation & Archival
```
Completion Package:
├── Final deliverables
├── Technical documentation
├── User manuals/guides
├── Lessons learned document
├── Performance metrics
├── Budget variance report
└── Stakeholder feedback
```

#### Performance Metrics Calculation
```
Metrics Captured:
├── Actual vs. Estimated Timeline
├── Actual vs. Budgeted Cost
├── Quality Score (defects/rework)
├── Stakeholder Satisfaction Rating
├── Team Productivity Metrics
└── Resource Utilization Efficiency
```

## 📊 Workflow Analytics

### Key Performance Indicators
- **Cycle Time**: Average time from submission to completion
- **Approval Time**: Average time spent in approval process
- **Execution Efficiency**: Actual vs. estimated execution time
- **Quality Index**: Based on rework and client satisfaction
- **Resource Utilization**: Team efficiency metrics

### Workflow Bottleneck Analysis
```
Common Bottlenecks:
├── Approval Delays (solution: streamline approval process)
├── Resource Constraints (solution: better capacity planning)
├── Scope Creep (solution: change control process)
├── Dependencies (solution: better project planning)
└── Quality Issues (solution: better standards/training)
```

### Continuous Improvement
- Monthly workflow review meetings
- Quarterly process optimization
- Annual workflow redesign assessment
- Stakeholder feedback integration

---

**Prev:** [SPK Overview](overview.md) | **Next:** [SPK User Guide](user-guide.md)
