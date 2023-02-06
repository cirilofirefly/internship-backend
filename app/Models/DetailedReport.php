<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DetailedReport extends Model
{
    protected $table = 'detailed_reports';

    protected $fillable = [
        'activities',
        'learning',
        'date_time_record_id'
    ];

    public function dateTimeRecord()
    {
        return $this->hasOne('App\Models\DateTimeRecord', 'date_time_record_id', 'id');
    }
}
