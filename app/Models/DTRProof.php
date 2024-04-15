<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DTRProof extends Model
{
    use HasFactory;

    protected $table = "dtr_proofs";

    protected $fillable = [
        'daily_time_record_id',
        'key',
        'image_proof'
    ];

    public function dailyTimeRecords()
    {
        return $this->belongsTo('App\Models\DailyTimeRecord', 'id', 'daily_time_record_id');
    }
}
