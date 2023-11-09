<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VacationRequest extends Model
{
    use HasFactory;
    use HasTimestamps;

    public mixed $employee_id;
    public mixed $start_date;
    public mixed $end_date;
    public mixed $vacation_type;
    public mixed $reason;

    protected $table = 'vacation_requests';

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
