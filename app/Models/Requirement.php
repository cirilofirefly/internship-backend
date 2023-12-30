<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Requirement extends Model
{
    const APPLICATION_LETTER = 'application-letter';
    const RESUME = 'resume';
    const COMPANY_PROFILE = 'company-profile';
    const LETTER_OF_ENDORSEMENT = 'letter-of-endorsement';
    const MEMORANDUM_OF_AGREEMENT = 'memorandum-of-agreement';
    const OTHERS = 'others';

    protected $fillable = [
        'user_id',
        'type',
        'file_name',
        'file',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User', 'user_id');
    }
}
