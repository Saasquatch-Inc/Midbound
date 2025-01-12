<?php

namespace Midbound;

use Carbon\Carbon;
use Laravel\Spark\CanJoinTeams;
use Laravel\Spark\User as SparkUser;

/**
 * Class User
 * @package Midbound
 */
class User extends SparkUser
{
    use CanJoinTeams;

    /**
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'confirmed_at',
    ];

    /**
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'authy_id',
        'country_code',
        'phone',
        'card_brand',
        'card_last_four',
        'card_country',
        'billing_address',
        'billing_address_line_2',
        'billing_city',
        'billing_zip',
        'billing_country',
        'extra_billing_information',
    ];

    /**
     * @var array
     */
    protected $casts = [
        'trial_ends_at' => 'date',
        'uses_two_factor_auth' => 'boolean',
    ];

    /**
     * @var array
     */
    protected $appends = ['firstname'];

    /**
     * @var array
     */
    public $timestamps = ['created_at', 'updated_at', 'confirmed_at'];

    /**
     * @return mixed
     */
    public function getFirstnameAttribute()
    {
        $firstname = explode(' ', $this->name);

        return $firstname[0];
    }

    /**
     * Confirm email address
     */
    public function confirm()
    {
        $this->confirmed_at = Carbon::now();
        $this->save();
    }

    /**
     * @return bool
     */
    public function isNotConfirmed()
    {
        return is_null($this->confirmed_at);
    }
}
