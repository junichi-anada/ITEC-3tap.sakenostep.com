<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ImportTask extends Model
{
   // softdeleteを使うための記述
    use SoftDeletes;

    const STATUS_PENDING = 0;
    const STATUS_PROCESSING = 1;
    const STATUS_COMPLETED = 2;
    const STATUS_FAILED = 3;

    protected $fillable = [
        'task_code',
        'site_id',
        'data_type',
        'file_path',
        'status',
        'status_message',
        'imported_by',
        'uploaded_at',
        'imported_at',
    ];

    protected $dates = [
        'uploaded_at',
        'imported_at',
        'created_at',
        'updated_at',
        'deleted_at',
    ];

    public function site()
    {
        return $this->belongsTo(Site::class);
    }

}
