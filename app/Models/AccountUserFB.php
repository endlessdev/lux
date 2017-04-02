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

    public function findUserByAppId(){
        return $this->where('accounts_users_fb.fb_id', $this->fb_id)
            ->leftJoin('accounts_users', 'accounts_users_fb.account_idx', '=', 'accounts_users.account_idx')
            ->leftJoin('accounts', 'accounts_users_fb.account_idx', '=', 'accounts.idx')
            ->leftJoin('tokens', 'accounts_users_fb.account_idx', '=', 'tokens.account_idx')
            ->first();
    }

}