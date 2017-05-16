<?php
/**
 * Created by PhpStorm.
 * User: jade
 * Date: 16/05/2017
 * Time: 12:33 AM
 */

namespace App\Http\Controllers\V1_0;


use App\Helpers\Response;
use App\Http\Controllers\Controller;
use App\Models\Account;

use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $request;

    function __construct(Account $account, Request $request)
    {
        $this->account = $account;
    }

    public function getUsers()
    {

        $users = Account::leftJoin('accounts_users', 'accounts.idx', '=', 'accounts_users.account_idx')
            ->leftJoin('tokens', 'accounts.idx', '=', 'tokens.account_idx')
//            ->distinct()->get(['accounts.password'])
//            ->select('accounts.idx, accounts.deleted_at, accounts.type accounts.email, accounts.created_at, accounts.updated_at')
            ->paginate();

        if (!empty($users)) {
            return Response::commonResponse("Success", $users, 200);
        } else {
            return Response::commonResponse("Not found", [], 404);
        }
    }

}