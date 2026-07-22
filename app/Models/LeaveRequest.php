<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'approved_by', // Ditambahkan ke fillable
        'date',
        'type',
        'description',
        'proof',
        'status',
    ];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    /**
     * Get the student that owns the leave request.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the admin user who approved/rejected the request.
     */
    public function approver(): BelongsTo
    {
        // Parameter kedua ('approved_by') perlu ditulis eksplisit 
        // karena nama method (approver) berbeda dengan nama kolom (approved_by)
        return $this->belongsTo(User::class, 'approved_by');
    }
}