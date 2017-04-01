<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Created by PhpStorm.
 * User: jade
 * Date: 29/03/2017
 * Time: 11:44 AM
 */
class Token extends Model
{
    protected $table = "tokens";
    protected $primaryKey = 'idx';

    protected $dates = ['expire_at', 'created_at'];

    public function account(){
        return $this->belongsTo('App\Models\Account', 'account_idx', 'idx');
    }

    public function isVerifyToken(){
        return $this->where('token', $this->token)
            ->where('expire_at', '>', date('Y-m-d H:i:s'))->exists();
    }

}