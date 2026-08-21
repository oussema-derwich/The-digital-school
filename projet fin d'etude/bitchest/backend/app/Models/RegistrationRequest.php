<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class RegistrationRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'email',
        'role',
        'temp_password',
        'is_approved',
        'is_rejected',
        'rejection_reason',
        'approval_email_sent',
        'approval_email_sent_at',
        'approval_email_error',
    ];

    protected $casts = [
        'is_approved' => 'boolean',
        'is_rejected' => 'boolean',
        'approval_email_sent' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'approval_email_sent_at' => 'datetime',
    ];

    /**
     * Relation: Une demande d'inscription appartient à un utilisateur
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
