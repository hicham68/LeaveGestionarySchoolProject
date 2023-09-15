<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeaveBackup extends Model
{
    use HasFactory;
    use HasTimestamps;


    protected $fillable = [
        'employee_id',
        'leave_type_id',
        'reason_id',
        'start_date',
        'end_date',

    ];

    public function employees(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function leaveTypes(): BelongsTo
    {
        return $this->belongsTo(LeaveType::class);
    }

    public function reasons(): BelongsTo
    {
        return $this->belongsTo(Reason::class);
    }
}

