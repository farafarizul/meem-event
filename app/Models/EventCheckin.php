<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EventCheckin extends Model
{
    use HasFactory, softDeletes;

    protected $primaryKey = 'event_checkin_id';

    protected $fillable = [
        'event_id',
        'user_id',
        'checked_in_at',
        'status',
    ];

    protected $casts = [
        'checked_in_at' => 'datetime',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
