<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    protected $primaryKey = 'event_id';

    protected $fillable = [
        'category_event',
        'event_name',
        'location',
        'start_date',
        'end_date',
        'unique_identifier',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function checkins()
    {
        return $this->hasMany(EventCheckin::class);
    }

    public function attendees()
    {
        return $this->belongsToMany(User::class, 'event_checkins')
            ->withPivot('checked_in_at')
            ->withTimestamps();
    }
}
