<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class PushNotification extends Model
{
    use HasFactory;

    protected $table = 'push_notifications';
    protected $primaryKey = 'push_notification_id';

    protected $fillable = [
        'title',
        'message',
        'image_url',
        'recipient_mode',
        'selected_meem_codes',
        'additional_data_1',
        'additional_data_2',
        'additional_data_3',
        'onesignal_app_id',
        'onesignal_request_payload',
        'onesignal_response',
        'onesignal_notification_id',
        'total_recipient',
        'send_status',
        'error_message',
        'created_by',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
