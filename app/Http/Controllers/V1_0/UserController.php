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

use function FastRoute\TestFixtures\empty_options_cached;
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
            return Response::common(200, $users);
        } else {
            return Response::common(404);
        }
    }

    public function getUser(int $userIdx)
    {
        $foundUser = Account::where('accounts.idx', $userIdx)
            ->leftJoin('accounts_users', 'accounts.idx', '=', 'accounts_users.account_idx')
            ->first();


        if (empty($foundUser)) {
            return Response::common(404);
        }

        unset($foundUser['password']);

        return Response::common(200, $foundUser);
    }

}