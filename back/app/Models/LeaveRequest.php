<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class vacationRequest extends Model
{
    use HasFactory;
    use HasTimestamps;


    protected $fillable = [
        'employee_id',
        'vacation_type_id',
        'reason_id',
        'start_date',
        'end_date',
    ];

    public function employees(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function vacationTypes(): BelongsTo
    {
        return $this->belongsTo(vacationType::class);
    }

    public function reasons(): BelongsTo
    {
        return $this->belongsTo(Reason::class);
    }

}
