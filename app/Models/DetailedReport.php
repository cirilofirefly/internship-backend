<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailedReport extends Model
{
    protected $table = 'detailed_reports';

    protected $fillable = [
        'activities',
        'learning',
        'daily_time_record_id',
        'status'
    ];

    public function dateTimeRecord()
    {
        return $this->hasOne('App\Models\DateTimeRecord', 'daily_time_record_id', 'id');
    }
}
