<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    public const SUPER_ADMIN = 'super-admin';
    public const ADMIN = 'admin';
    public const INTERN = 'intern';
    public const COORDINATOR = 'coordinator';
    public const SUPERVISOR = 'supervisor';

    public const APPROVED = 'approved';
    public const DECLINED = 'declined';
    public const PENDING = 'pending';

    public const USER_TYPES = [
        self::SUPER_ADMIN,
        self::ADMIN,
        self::INTERN,
        self::COORDINATOR,
        self::SUPERVISOR,
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'first_name',
        'last_name',
        'middle_name',
        'birthday',
        'nationality',
        'civil_status',
        'contact_number',
        'email',
        'gender',
        'password',
        'suffix',
        'user_type',
        'username',
        'profile_picture',
        'e_signature',
        'is_approved'
    ];

    protected $appends = ['full_name'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];


    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function setPasswordAttribute($password)
    {
        $this->attributes['password'] = Hash::make($password);
    }

    public function scopeWhereIntern($query)
    {
        return $query->where('user_type', User::INTERN);
    }

    public function scopeWhereUserId($query, $value)
    {
        return $query->where('id', $value);
    }

    public function intern()
    {
        return $this->belongsTo('App\Models\Intern', 'id', 'portal_id');
    }

    public function coordinator()
    {
        return $this->belongsTo('App\Models\Coordinator', 'id', 'portal_id');
    }
}
