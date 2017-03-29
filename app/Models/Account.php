<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Created by PhpStorm.
 * User: jade
 * Date: 29/03/2017
 * Time: 11:44 AM
 */
class Account extends Model
{
    protected $table = "accounts";
    protected $primaryKey = 'idx';
    protected $dates = ['created_at', 'deleted_at'];
    protected $guarded = ['idx', 'type', 'created_at'];



}