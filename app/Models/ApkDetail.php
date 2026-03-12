<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApkDetail extends Model
{
    use HasFactory;

    protected $table = 'apk_detail';

    protected $primaryKey = 'apk_detail_id';

    protected $fillable = [
        'original_filename',
        'new_filename',
        'uploaded_date',
        'description',
        'download_link',
    ];

    protected $casts = [
        'uploaded_date' => 'date',
    ];
}
