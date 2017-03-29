<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Created by PhpStorm.
 * User: jade
 * Date: 29/03/2017
 * Time: 11:44 AM
 */
class AccountUser extends Model
{
    protected $table = "accounts_users";
    protected $primaryKey = 'idx';
    protected $guarded = ['idx', 'account_idx', 'join_type'];

    public $timestamps = false;

}