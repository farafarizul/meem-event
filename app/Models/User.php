<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'fullname',
        'phone_number',
        'meem_code',
        'meem_id',
        'email',
        'password',
        'is_admin',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_admin' => 'boolean',
    ];

    public function checkins()
    {
        return $this->hasMany(EventCheckin::class);
    }

    public function events()
    {
        return $this->belongsToMany(Event::class, 'event_checkins')
            ->withPivot('checked_in_at')
            ->withTimestamps();
    }
}
