<?php

namespace App\Models\Access_PR\Purchase_Request;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class PurchaseRequestStatusUpdate extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_request_id',
        'update_type',
        'description',
        'data',
        'updated_by'
    ];

    protected $casts = [
        'data' => 'array'
    ];

    // Relationship dengan Purchase Request
    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    // Relationship dengan User yang melakukan update
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Get update type labels
    public static function getUpdateTypeLabels()
    {
        return [
            'ITEMS_PROCESSED' => 'Proses Items Purchasing',
            'VENDOR_SEARCH' => 'Pencarian Vendor',
            'PRICE_COMPARISON' => 'Perbandingan Harga',
            'PO_CREATED' => 'PO ke Vendor',
            'GOODS_RECEIVED' => 'Barang Diterima',
            'GOODS_RETURNED' => 'Barang Dikembalikan',
            'COMPLAIN' => 'Complain',
            'TERSEDIA_DI_GA' => 'Tersedia di GA',
            'CFO_QUANTITY_ADJUSTMENT' => 'Penyesuaian Quantity oleh CFO',
            'CLOSED' => 'Selesai'
        ];
    }

    public function getUpdateTypeLabelAttribute()
    {
        $labels = self::getUpdateTypeLabels();
        return $labels[$this->update_type] ?? $this->update_type;
    }
}
