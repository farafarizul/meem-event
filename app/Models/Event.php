<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Event extends Model
{
    use HasFactory, SoftDeletes;

    protected $primaryKey = 'event_id';

    protected $fillable = [
        'branch_id',
        'category_event',
        'event_name',
        'location',
        'start_date',
        'end_date',
        'unique_identifier',
        'status',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id', 'branch_id');
    }

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
