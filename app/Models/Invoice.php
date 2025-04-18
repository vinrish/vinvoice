<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'ref',
        'date',
        'provider_id',
        'client_id',
        'net_tax',
        'grand_total',
        'warranty_period',
        'warranty_type',
        'warranty_expiry_date',
        'created_by',
    ];

    public function details(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(InvoiceDetail::class);
    }

    public function client(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    public function provider(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Provider::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
