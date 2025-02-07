<?php

namespace App\Models;

use App\Observers\PatientObserver;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;

#[ObservedBy([PatientObserver::class])]
class Patient extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'first_name',
        'last_name',
        'name',
        'email',
        'phone'
    ];

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }
}
