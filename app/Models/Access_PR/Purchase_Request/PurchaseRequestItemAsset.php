<?php

namespace App\Models\Access_PR\Purchase_Request;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseRequestItemAsset extends Model
{
    use HasFactory;

    protected $table = 'pr_item_assets';

    protected $fillable = [
        'purchase_request_id',
        'purchase_request_item_id',
        'item_description',
        'base_code',
        'asset_code',
    'asset_pajak',
        'sequence_no',
        'created_by',
    ];

    protected $casts = [
        'asset_pajak' => 'boolean',
    ];

    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class);
    }

    public function item()
    {
        return $this->belongsTo(PurchaseRequestItem::class, 'purchase_request_item_id');
    }
}
