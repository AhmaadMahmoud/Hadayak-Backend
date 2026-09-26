<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceRequest extends Model
{
    protected $fillable = [
        'service_id', 'user_id', 'name', 'phone', 'email', 'image', 'notes', 'status',
    ];

    public const STATUSES = [
        'new'         => 'جديد',
        'contacted'   => 'تم التواصل',
        'in_progress' => 'قيد التنفيذ',
        'done'        => 'اتسلم',
        'cancelled'   => 'ملغي',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
