<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Created by PhpStorm.
 * User: jade
 * Date: 29/03/2017
 * Time: 11:44 AM
 */
class AccountUserFB extends Model
{
    protected $table = "accounts_users_fb";
    protected $primaryKey = 'idx';
    protected $guarded = ['idx', 'account_idx'];

    public $timestamps = false;

    public function account()
    {
        return $this->belongsTo('App\Models\Account', 'account_idx');
    }

    public function findUserByAppId($appId){
        return $this->where('fb_id', $appId)
            ->leftJoin('accounts_users', 'account_idx', '=', 'accounts_users.account_idx')
            ->leftJoin('accounts', 'account_idx', '=', 'accounts.idx')
            ->leftJoin('tokens', 'account_idx', '=', 'tokens.account_idx')
            ->first();
    }

}