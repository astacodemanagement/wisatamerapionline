<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'image',
        'name',
        'user',
        'email',
        'phone_number',
        'password',
        'birth_place',
        'birth_date',
        'address_by_card',
        'rt_rw',
        'subdistrict',
        'district',
        'city',
        'province',
        'postal_code',
        'use_address',
        'occupation_type',
        'company_name',
        'position',
        'company_address',
        'correspondence_address',
        'last_education',
        'study_program',
        'university_name',
        'graduation_year',
        'member_number',
        'join_date',
        'member_type',
        'member_status',
        'branch_id',
        'legal_authorization_permission',
        'legal_authorization_file',
        'retired_tax_officer',
        'position_tax',
        'retirement_year',
        'retirement_decision_letter',
        'nik',
        'npwp',
        'practice_license_number',
        'certification_level',
        'practice_license_issue_date',
        'status',
        'uuid'
    ];

    // User.php


    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->uuid = Str::uuid()->toString();
        });
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }


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
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    

    
}
