<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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

    use SoftDeletes;

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

    public function getAccountByIdx()
    {
        return $this->where('accounts.idx', $this->idx)
            ->leftJoin('accounts_users', 'accounts.idx', '=', 'accounts_users.account_idx')
            ->leftJoin('tokens', 'accounts.idx', '=', 'tokens.account_idx')
            ->first();
    }

    public function getAccountInfoByEmail(string $userEmail)
    {

        return $this->withTrashed()->where('accounts.email', $userEmail)
            ->leftJoin('accounts_users', 'accounts.idx', '=', 'accounts_users.account_idx')
            ->leftJoin('tokens', 'accounts.idx', '=', 'tokens.account_idx')->first();
    }

    public function isDeletedUser()
    {
        return $this->where('idx', $this->idx)->trashed();
    }

}