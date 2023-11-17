<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Transaction extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'amount',
        'payer_id',
        'due_on',
        'vat',
        'is_vat_inclusive',
        'status',
        'total_paid_amount',
        'created_by'
    ];

    protected $casts = [
        'amount' => 'float',
        'payer_id' => 'integer',
        'due_on' => 'date',
        'vat' => 'float',
        'is_vat_inclusive' => 'boolean',
        'status' => 'string',
        'total_paid_amount' => 'float',
    ];

    public const UNPAID = 'unpaid';
    public const  PAID = 'paid';
    public const  OUTSTANDING = 'outstanding';
    public const  OVERDUE = 'overdue';

    public function payer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'payer_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'transaction_id');
    }
}
