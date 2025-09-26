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
        'notes'
    ];

    protected $casts = [
        'estimated_price' => 'decimal:2'
    ];

    // Relationship dengan Purchase Request
    public function purchaseRequest()
    {
        return $this->belongsTo(PurchaseRequest::class);
    }
}
