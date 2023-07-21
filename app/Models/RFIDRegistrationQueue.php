<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RFIDRegistrationQueue extends Model
{
    protected $fillable = [
		"intern_user_id",
		"coordinator_user_id",
		"device_token"
    ];
}
