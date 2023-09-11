<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DTRFile extends Model
{

    protected $table = 'dtr_files';

    protected $fillable = [
        'user_id',
        'start_date',
        'end_date',
        'file_name',
        'file',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}
