<?php

namespace App\Models\Access_PR\Purchase_Request;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequestItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_request_id',
        'description',
        'quantity',
        'unit',
        'estimated_price',
        'notes',
    'item_status',
    'purchasing_notes',
    'is_asset',
    'is_asset_hcga',
    'payment_method_id'
    ];

    protected $casts = [
        'estimated_price' => 'decimal:2',
        'is_asset' => 'boolean',
        'is_asset_hcga' => 'boolean',
    ];

    // Get item status labels
    public static function getItemStatusLabels()
    {
        return [
            'PENDING' => 'Pending',
            'VENDOR_SEARCH' => 'Pencarian Vendor',
            'PRICE_COMPARISON' => 'Perbandingan Harga',
            'PO_CREATED' => 'PO ke Vendor',
            'GOODS_RECEIVED' => 'Barang Diterima',
            'GOODS_RETURNED' => 'Barang Dikembalikan',
            'COMPLAIN' => 'Complain',
            'TERSEDIA_DI_GA' => 'Tersedia di GA',
            'REJECTED' => 'Ditolak',
            'CLOSED' => 'Selesai'
        ];
    }

    public function getItemStatusLabelAttribute()
    {
        $labels = self::getItemStatusLabels();
        return $labels[$this->item_status] ?? $this->item_status;
    }

    // Relationship dengan Purchase Request
    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function assets()
    {
        return $this->hasMany(PurchaseRequestItemAsset::class, 'purchase_request_item_id');
    }

    public function paymentMethod()
    {
        return $this->belongsTo(\App\Models\Access_PR\PaymentMethod::class, 'payment_method_id');
    }
}
