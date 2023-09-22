<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reason extends Model
{
    use HasFactory;

    protected $fillable = [
        'label',
    ];

    public function vacationRequests(): HasMany
    {
        return $this->hasMany(vacationRequest::class);
    }
}
