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

    public function detail()
    {
        switch ($this->type) {
            case 'admin':
//                TODO Except admin type user
                break;
        }
        return $this->hasOne('App\Models\AccountUser', 'account_idx');
    }

    public function token()
    {
        return $this->hasOne('App\ModelToken', 'account_idx');
    }

    public function getAccountByEmail(string $userEmail)
    {
        return $this->where('email', $userEmail)
            ->leftJoin('accounts_users', 'accounts.idx', '=', 'accounts_users.account_idx')
            ->leftJoin('tokens', 'accounts.idx', '=', 'tokens.account_idx')
            ->first();
    }

}