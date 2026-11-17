<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Package extends Model
{
    protected $guarded = [];
    protected $casts = [
        'received_date' => 'date',
        'is_cod' => 'boolean',
        'is_zone_manual' => 'boolean',
        'needs_verification' => 'boolean',
        'cod_settled_at' => 'datetime',
        'weight_kg' => 'decimal:2',
    ];
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }
    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }
    public function courier()
    {
        return $this->belongsTo(Courier::class);
    }
    public function histories()
    {
        return $this->hasMany(PackageHistory::class);
    }
}
