# 📦 SPK (Service Package) System - Overview

## 🎯 System Purpose

SPK (Service Package/Surat Perintah Kerja) adalah sistem manajemen untuk mengelola paket layanan dan order kerja dalam organisasi. Sistem ini dirancang untuk:

- **Service Management** - Mengelola berbagai jenis layanan
- **Work Order Tracking** - Melacak progress pekerjaan
- **Resource Allocation** - Mengalokasikan resources untuk projects
- **Performance Monitoring** - Monitoring kinerja tim dan layanan

## 🏗️ System Architecture

### Database Structure

#### Core Tables
```sql
┌─────────────────┐    ┌─────────────────┐
│      spks       │    │   spk_items     │
├─────────────────┤    ├─────────────────┤
│ id (PK)         │────│ spk_id (FK)     │
│ spk_number      │    │ id (PK)         │
│ description     │    │ nama_item       │
│ status          │    │ quantity        │
│ request_date    │    │ unit            │
│ due_date        │    │ estimated_price │
│ location        │    │ description     │
│ user_id (FK)    │    │ notes           │
│ created_at      │    │ created_at      │
│ updated_at      │    │ updated_at      │
└─────────────────┘    └─────────────────┘
        │
        │ (User Relationship)
        │
┌─────────────────┐
│     users       │
├─────────────────┤
│ id (PK)         │
│ name            │
│ email           │
│ level           │
│ divisi          │
│ created_at      │
│ updated_at      │
└─────────────────┘
```

#### SPK Status Flow
```
DRAFT → SUBMITTED → IN_PROGRESS → COMPLETED
  ↓         ↓           ↓
CANCELLED  REJECTED   ON_HOLD
```

### Application Structure

#### Models
```
app/Models/
├── Spk.php              # Main SPK model
├── SpkItem.php          # SPK items/tasks
└── User.php             # User management
```

#### Controllers
```
app/Http/Controllers/
├── SpkController.php           # SPK CRUD operations
├── SpkItemController.php       # SPK items management
└── DashboardController.php     # SPK dashboard
```

#### Views
```
resources/views/spk/
├── index.blade.php       # SPK listing
├── create.blade.php      # Create new SPK
├── edit.blade.php        # Edit existing SPK
├── show.blade.php        # SPK details
└── dashboard.blade.php   # SPK dashboard
```

## 👥 User Roles & Permissions

### Role Matrix
| Role | Create SPK | View SPK | Edit SPK | Assign Tasks | Approve | Complete |
|------|-----------|----------|----------|--------------|---------|----------|
| **USER** | ✅ Own | ✅ Own | ✅ Own (Draft) | ❌ | ❌ | ❌ |
| **TEAM_LEAD** | ✅ | ✅ Team | ✅ Team | ✅ | ✅ Level 1 | ✅ |
| **PROJECT_MANAGER** | ✅ | ✅ Dept | ✅ Dept | ✅ | ✅ Level 2 | ✅ |
| **DEPT_HEAD** | ✅ | ✅ Dept | ✅ Dept | ✅ | ✅ Final | ✅ |
| **ADMIN** | ✅ All | ✅ All | ✅ All | ✅ | ✅ Override | ✅ |

### Permission Logic
```php
// Example permission check
public function canEdit(Spk $spk, User $user): bool
{
    // Own SPK in DRAFT status
    if ($spk->user_id === $user->id && $spk->status === 'DRAFT') {
        return true;
    }
    
    // Team lead can edit team SPKs
    if ($user->level === 'TEAM_LEAD' && $spk->user->divisi === $user->divisi) {
        return true;
    }
    
    // Project manager and above
    if (in_array($user->level, ['PROJECT_MANAGER', 'DEPT_HEAD', 'ADMIN'])) {
        return true;
    }
    
    return false;
}
```

## 🔄 SPK Workflow

### Standard Process Flow
```
1. Creation (DRAFT)
   ├── User creates SPK
   ├── Adds items/tasks
   └── Saves as draft

2. Submission (SUBMITTED)
   ├── User submits for approval
   ├── Cannot be edited anymore
   └── Enters approval queue

3. Approval Process
   ├── Team Lead approval (if applicable)
   ├── Project Manager review
   └── Department Head final approval

4. Execution (IN_PROGRESS)
   ├── Tasks assigned to team members
   ├── Progress tracking
   └── Resource allocation

5. Completion (COMPLETED)
   ├── All tasks completed
   ├── Quality check
   └── Final delivery
```

### Status Definitions

#### 📝 DRAFT
- SPK masih dalam tahap pembuatan
- Dapat diedit dan dihapus oleh pembuat
- Belum masuk approval flow
- Items dapat ditambah/dikurang

#### 📤 SUBMITTED
- SPK telah disubmit untuk approval
- Tidak dapat diedit oleh pembuat
- Masuk dalam approval queue
- Awaiting team lead/manager review

#### ⚠️ REJECTED
- SPK ditolak dalam approval process
- Berisi alasan penolakan
- Dapat di-resubmit setelah perbaikan
- Status kembali ke DRAFT untuk editing

#### 🚀 IN_PROGRESS
- SPK telah diapprove dan mulai dikerjakan
- Tasks dapat diassign ke team members
- Progress tracking aktif
- Resource allocation running

#### ⏸️ ON_HOLD
- SPK dihentikan sementara
- Karena dependency/blocking issues
- Dapat dilanjutkan kemudian
- Resource dialokasikan ke project lain

#### ✅ COMPLETED
- Semua tasks telah selesai
- Quality check passed
- Deliverable telah diserahkan
- SPK officially closed

#### ❌ CANCELLED
- SPK dibatalkan
- Resource dialokasikan kembali
- Tidak dapat dilanjutkan
- Archive untuk reference

## 🛠️ Technical Implementation

### Model Relationships
```php
// Spk Model
class Spk extends Model
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
    
    public function items(): HasMany
    {
        return $this->hasMany(SpkItem::class);
    }
    
    public function assignedTasks(): HasMany
    {
        return $this->hasMany(SpkTask::class);
    }
    
    // Computed properties
    public function getTotalEstimatedCostAttribute(): float
    {
        return $this->items->sum(function ($item) {
            return $item->quantity * $item->estimated_price;
        });
    }
    
    public function getProgressPercentageAttribute(): float
    {
        $totalTasks = $this->assignedTasks->count();
        $completedTasks = $this->assignedTasks->where('status', 'completed')->count();
        
        return $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;
    }
}
```

### Controller Logic
```php
// SpkController key methods
class SpkController extends Controller
{
    public function index(Request $request)
    {
        $query = Spk::with(['user', 'items']);
        
        // Apply filters based on user role
        if (!$request->user()->isAdmin()) {
            $query->where(function ($q) use ($request) {
                $q->where('user_id', $request->user()->id)
                  ->orWhere('assigned_to', $request->user()->id);
            });
        }
        
        // Status filter
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        
        return view('spk.index', [
            'spks' => $query->paginate(15),
            'statuses' => ['DRAFT', 'SUBMITTED', 'IN_PROGRESS', 'COMPLETED']
        ]);
    }
    
    public function updateStatus(Request $request, Spk $spk)
    {
        $this->authorize('updateStatus', $spk);
        
        $validated = $request->validate([
            'status' => 'required|in:SUBMITTED,IN_PROGRESS,ON_HOLD,COMPLETED,CANCELLED',
            'notes' => 'nullable|string|max:1000'
        ]);
        
        $spk->update([
            'status' => $validated['status'],
            'status_notes' => $validated['notes'],
            'status_updated_by' => $request->user()->id,
            'status_updated_at' => now()
        ]);
        
        // Trigger notifications
        $this->notifyStatusChange($spk, $validated['status']);
        
        return redirect()->back()->with('success', 'SPK status updated successfully');
    }
}
```

## 📊 Business Rules

### SPK Creation Rules
1. **Mandatory Fields**
   - Description (min 10 characters)
   - Due date (must be future date)
   - At least 1 item/task
   - Location/department

2. **Item Validation**
   - Item name required
   - Quantity > 0
   - Valid unit of measurement
   - Estimated cost (if applicable)

3. **Business Logic**
   - SPK number auto-generated: `SPK-{YYYY}{MM}{DD}-{###}`
   - Due date cannot be past date
   - Items can be services or physical items

### Approval Rules
1. **Approval Hierarchy**
   ```
   Creator → Team Lead → Project Manager → Dept Head
   ```

2. **Approval Authority**
   - Team Lead: Up to 10M IDR
   - Project Manager: Up to 50M IDR
   - Dept Head: Unlimited
   - Emergency override: Admin/CEO

3. **Approval Timeframes**
   - Team Lead: 2 business days
   - Project Manager: 3 business days
   - Dept Head: 5 business days
   - Auto-escalation if overdue

### Progress Tracking Rules
1. **Task Assignment**
   - Only approved SPKs can have tasks assigned
   - Tasks must have clear deliverables
   - Each task has estimated hours/effort

2. **Progress Updates**
   - Team members update task progress
   - Team lead reviews and validates
   - Overall SPK progress auto-calculated

3. **Completion Criteria**
   - All tasks marked completed
   - Quality review passed
   - Deliverables accepted by requester

## 🔗 Integration Points

### With Purchase Request System
- SPK can reference approved PRs
- Share resource and budget information
- Cross-reference for audit trails

### With User Management
- Role-based access control
- Department-based visibility
- Approval hierarchy integration

### With Notification System
- Status change notifications
- Deadline reminders
- Escalation alerts

## 📈 Reporting & Analytics

### Standard Reports
1. **SPK Summary Dashboard**
   - Total SPKs by status
   - Completion rates
   - Average cycle time
   - Resource utilization

2. **Performance Metrics**
   - On-time completion rate
   - Budget variance analysis
   - Team productivity metrics
   - Quality metrics

3. **Management Reports**
   - Department performance
   - Project portfolio overview
   - Resource allocation reports
   - Trend analysis

### Key Performance Indicators (KPIs)
- **Cycle Time**: Average time from submission to completion
- **Completion Rate**: Percentage of SPKs completed on time
- **Quality Score**: Based on rework and client satisfaction
- **Resource Efficiency**: Actual vs. estimated effort
- **Cost Variance**: Actual vs. budgeted costs

---

**Next:** [SPK Workflow Guide](workflow.md) | [SPK User Guide](user-guide.md)
