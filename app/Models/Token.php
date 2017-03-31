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
    protected $guarded = ['idx', 'type', 'created_at'];

    protected $dates = ['expire_at', 'created_at'];

    public function getAccountByEmail(string $userEmail)
    {
        return $this->where('email', $userEmail)->first();
    }

}