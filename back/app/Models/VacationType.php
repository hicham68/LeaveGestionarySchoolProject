<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasTimestamps;
use Illuminate\Database\Eloquent\Relations\HasMany;

class VacationType extends Model
{
    use HasFactory;
    use HasTimestamps;

    protected $fillable = [
        'label',
    ];

    public function vacationRequests(): HasMany
    {
        return $this->hasMany(vacationRequest::class);
    }
}
