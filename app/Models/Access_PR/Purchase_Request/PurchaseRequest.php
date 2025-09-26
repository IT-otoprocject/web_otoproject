<?php

namespace App\Models\Access_PR\Purchase_Request;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use Carbon\Carbon;

class PurchaseRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'pr_number',
        'user_id',
        'request_date',
        'due_date',
        'description',
        'location',
        'status',
        'approval_flow',
        'approvals',
        'notes'
    ];

    protected $casts = [
        'request_date' => 'date',
        'due_date' => 'date',
        'approval_flow' => 'array',
        'approvals' => 'array'
    ];

    // Get default approval flow based on user divisi
    public static function getApprovalFlowByDivisi($divisi)
    {
        // Semua divisi menggunakan alur yang sama:
        // User buat -> Dept Head -> GA -> Finance Dept -> CEO -> CFO (optional)
        return ['dept_head', 'ga', 'finance_dept', 'ceo', 'cfo'];
    }

    // Get detailed approval flow configuration
    public static function getApprovalFlowConfig()
    {
        return [
            'dept_head' => [
                'name' => 'Department Head',
                'description' => 'Manager di divisi yang sama dengan pembuat PR',
                'get_approvers' => function($pr) {
                    return User::where('divisi', $pr->user->divisi)
                              ->where('level', 'manager')
                              ->get();
                }
            ],
            'ga' => [
                'name' => 'GA Approval',
                'description' => 'HCGA Department (Manager, SPV, atau Staff)',
                'get_approvers' => function($pr) {
                    return User::where('divisi', 'HCGA')
                              ->whereIn('level', ['manager', 'spv', 'staff'])
                              ->get();
                }
            ],
            'finance_dept' => [
                'name' => 'Finance Department',
                'description' => 'FAT Manager atau SPV',
                'get_approvers' => function($pr) {
                    return User::where('divisi', 'FAT')
                              ->whereIn('level', ['manager', 'spv'])
                              ->get();
                }
            ],
            'ceo' => [
                'name' => 'CEO Approval',
                'description' => 'Chief Executive Officer',
                'optional' => true,
                'get_approvers' => function($pr) {
                    return User::where('level', 'admin')
                              ->where('name', 'LIKE', '%CEO%')
                              ->get();
                }
            ],
            'cfo' => [
                'name' => 'CFO Approval', 
                'description' => 'Chief Financial Officer',
                'optional' => true,
                'get_approvers' => function($pr) {
                    return User::where('level', 'admin')
                              ->where('name', 'LIKE', '%CFO%')
                              ->get();
                }
            ]
        ];
    }

    // Get available approval levels for dropdown
    public static function getAvailableApprovalLevels()
    {
        return [
            'dept_head' => 'Department Head',
            'ga' => 'GA',
            'finance_dept' => 'Finance Department',
            'ceo' => 'CEO',
            'cfo' => 'CFO',
        ];
    }

    // Relationship dengan User
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship dengan Purchase Request Items
    public function items()
    {
        return $this->hasMany(PurchaseRequestItem::class);
    }

    // Relationship dengan Status Updates
    public function statusUpdates()
    {
        return $this->hasMany(PurchaseRequestStatusUpdate::class);
    }

    // Generate PR Number dengan format PO/DDMMYY1
    public static function generatePRNumber($location = 'HQ')
    {
        $day = date('d');    // 2 digit day
        $month = date('m');  // 2 digit month  
        $year = date('y');   // 2 digit year
        
        $prefix = 'PO';
        $dateFormat = $day . $month . $year;
        
        // Get last PR number for today (based on request date)
        $today = date('Y-m-d');
        $lastPR = self::whereDate('request_date', $today)
                     ->where('pr_number', 'LIKE', $prefix . '/' . $dateFormat . '%')
                     ->orderBy('pr_number', 'desc')
                     ->first();
        
        if ($lastPR) {
            // Extract sequence number dari format PO/DDMMYY123
            // Find the last occurrence of the date pattern and get everything after it
            $datePattern = $prefix . '/' . $dateFormat;
            $sequencePart = str_replace($datePattern, '', $lastPR->pr_number);
            $lastSequence = (int) $sequencePart;
            $newSequence = $lastSequence + 1;
        } else {
            $newSequence = 1; // Mulai dari 1 untuk hari baru
        }
        
        return $prefix . '/' . $dateFormat . $newSequence;
    }

    // Get approval status dengan format tanggal yang lebih baik
    public function getApprovalStatus()
    {
        $approvals = $this->approvals ?? [];
        $flow = $this->approval_flow;
        $currentLevel = $this->getCurrentApprovalLevel();
        
        $status = [];
        $rejectionFound = false;
        
        foreach ($flow as $level) {
            $approved = isset($approvals[$level]['approved']) ? $approvals[$level]['approved'] : null;
            $approvedAt = isset($approvals[$level]['approved_at']) ? $approvals[$level]['approved_at'] : null;
            
            // Format tanggal untuk semua status (approved atau rejected)
            $formattedDate = null;
            $statusText = 'Menunggu persetujuan';
            
            // Jika level ini sudah diproses
            if ($approved === true && $approvedAt) {
                $formattedDate = Carbon::parse($approvedAt)->setTimezone('Asia/Jakarta')->format('d/m/Y | H:i');
                $statusText = 'Approved pada ' . $formattedDate;
            } elseif ($approved === false && $approvedAt) {
                $formattedDate = Carbon::parse($approvedAt)->setTimezone('Asia/Jakarta')->format('d/m/Y | H:i');
                $statusText = 'Rejected pada ' . $formattedDate;
                $rejectionFound = true;
            } elseif ($rejectionFound) {
                // Jika ada rejection sebelumnya, level selanjutnya tidak perlu diproses
                $statusText = 'Tidak perlu approval (PR ditolak)';
                $approved = null;
            } elseif ($level === $currentLevel) {
                // Level yang sedang pending approval
                $statusText = 'Menunggu persetujuan';
                $approved = null;
            } else {
                // Level yang belum sampai waktunya (masih waiting)
                $statusText = 'Menunggu persetujuan';
                $approved = null;
            }
            
            $status[$level] = [
                'approved' => $approved,
                'approved_at' => $approvedAt,
                'approved_by' => isset($approvals[$level]['approved_by']) ? $approvals[$level]['approved_by'] : null,
                'approved_by_name' => isset($approvals[$level]['approved_by_name']) ? $approvals[$level]['approved_by_name'] : null,
                'notes' => isset($approvals[$level]['notes']) ? $approvals[$level]['notes'] : null,
                'formatted_date' => $formattedDate,
                'status_text' => $statusText
            ];
        }
        
        return $status;
    }

    // Check if can be approved by user
    public function canBeApprovedByUser($user)
    {
        $currentApprovalLevel = $this->getCurrentApprovalLevel();
        
        // Admin bisa approve semua level
        if ($user->level === 'admin') {
            return $currentApprovalLevel !== null;
        }
        
        // Jika tidak ada level yang perlu approval
        if (!$currentApprovalLevel) {
            return false;
        }
        
        // Check jika level ini sudah di-approve
        $approvals = $this->approvals ?? [];
        if (isset($approvals[$currentApprovalLevel]['approved']) && $approvals[$currentApprovalLevel]['approved']) {
            return false; // Sudah di-approve
        }
        
        // Check berdasarkan approval flow baru
        switch ($currentApprovalLevel) {
            case 'dept_head':
                // Manager di divisi yang sama dengan pembuat PR
                return ($user->level === 'manager' && $user->divisi === $this->user->divisi);
                
            case 'ga':
                // HCGA Department (Manager, SPV, atau Staff)
                return ($user->divisi === 'HCGA' && in_array($user->level, ['manager', 'spv', 'staff']));
                
            case 'finance_dept':
                // FAT Manager atau SPV
                return ($user->divisi === 'FAT' && in_array($user->level, ['manager', 'spv']));
                
            case 'ceo':
                // CEO - bisa level ceo atau admin level dengan indikator CEO
                return ($user->level === 'ceo' || 
                       ($user->level === 'admin' && (stripos($user->name, 'CEO') !== false || stripos($user->name, 'Chief Executive') !== false)));
                
            case 'cfo':
                // CFO - bisa level cfo atau admin level dengan indikator CFO
                return ($user->level === 'cfo' || 
                       ($user->level === 'admin' && (stripos($user->name, 'CFO') !== false || stripos($user->name, 'Chief Financial') !== false)));
        }
        
        return false;
    }

    // Check if can be approved by level (deprecated - use canBeApprovedByUser instead)
    public function canBeApprovedByLevel($level)
    {
        $flow = $this->approval_flow;
        $approvals = $this->approvals ?? [];
        
        $levelIndex = array_search($level, $flow);
        if ($levelIndex === false) {
            return false;
        }
        
        // Check if current level is already approved
        if (isset($approvals[$level]['approved']) && $approvals[$level]['approved']) {
            return false;
        }
        
        // Check if previous levels are approved
        for ($i = 0; $i < $levelIndex; $i++) {
            $prevLevel = $flow[$i];
            if (!isset($approvals[$prevLevel]['approved']) || !$approvals[$prevLevel]['approved']) {
                return false;
            }
        }
        
        return true;
    }

    // Get current approval level
    public function getCurrentApprovalLevel()
    {
        $flow = $this->approval_flow;
        $approvals = $this->approvals ?? [];
        
        // Check if any level has been rejected
        foreach ($flow as $level) {
            if (isset($approvals[$level]['approved']) && $approvals[$level]['approved'] === false) {
                return null; // If rejected, no current approval level
            }
        }
        
        // Find first level that hasn't been approved yet
        foreach ($flow as $level) {
            if (!isset($approvals[$level]['approved']) || !$approvals[$level]['approved']) {
                return $level;
            }
        }
        
        return null; // All approved
    }

    // Check if fully approved
    public function isFullyApproved()
    {
        $flow = $this->approval_flow;
        $approvals = $this->approvals ?? [];
        
        foreach ($flow as $level) {
            if (!isset($approvals[$level]['approved']) || !$approvals[$level]['approved']) {
                return false;
            }
        }
        
        return true;
    }

    // Check if user has ever approved this PR
    public function hasBeenApprovedByUser($user)
    {
        $approvals = $this->approvals ?? [];
        
        foreach ($approvals as $level => $approval) {
            if (isset($approval['approved_by']) && $approval['approved_by'] == $user->id) {
                return true;
            }
        }
        
        return false;
    }

    // Check if user should have access to view this PR
    public function canBeViewedByUser($user)
    {
        // User bisa lihat PR mereka sendiri
        if ($this->user_id === $user->id) {
            return true;
        }
        
        // Admin dan purchasing bisa lihat semua
        if (in_array($user->level, ['admin']) || 
            ($user->divisi === 'PURCHASING' && in_array($user->level, ['manager', 'spv', 'staff']))) {
            return true;
        }
        
        // User bisa lihat jika mereka bisa approve PR ini
        if ($this->canBeApprovedByUser($user)) {
            return true;
        }
        
        // User bisa lihat jika mereka PERNAH melakukan approval pada PR ini
        if ($this->hasBeenApprovedByUser($user)) {
            return true;
        }
        
        // HCGA bisa lihat semua PR yang dalam flow approval GA
        if ($user->divisi === 'HCGA' && in_array($user->level, ['manager', 'spv', 'staff'])) {
            $approvalFlow = $this->approval_flow ?? [];
            if (in_array('ga', $approvalFlow)) {
                return true;
            }
        }
        
        // Department Head bisa lihat PR dari divisi yang sama yang dalam approval flow
        if ($user->level === 'manager') {
            $approvalFlow = $this->approval_flow ?? [];
            if (in_array('dept_head', $approvalFlow) && 
                $user->divisi === $this->user->divisi) {
                return true;
            }
        }
        
        // Finance Department (FAT) bisa lihat PR yang dalam flow approval finance
        if ($user->divisi === 'FAT' && in_array($user->level, ['manager', 'spv'])) {
            $approvalFlow = $this->approval_flow ?? [];
            if (in_array('finance_dept', $approvalFlow)) {
                return true;
            }
        }
        
        return false;
    }
}
