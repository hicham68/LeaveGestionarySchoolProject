<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'vacation_balance',
        'email',
    ];


    public function vacationRequests(): HasMany
    {
        return $this->hasMany(vacationRequest::class);
    }

    public function vacationBackups(): HasMany
    {
        return $this->hasMany(vacationBackup::class);
    }

}
