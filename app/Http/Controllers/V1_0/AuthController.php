<?php

namespace App\Http\Controllers\V1_0;

use App\Helpers\Token;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\AccountUser;
use Illuminate\Http\Request;
use App\Helpers\Response;


/**
 * @class AuthController
 * @package App\Http\Controllers\V1_0
 * @author Jade Yeom <ysw0094@gmail.com>
 */
class AuthController extends Controller
{
    protected $request;
    protected $account;

    function __construct(Account $account, Request $request)
    {
        $this->request = $request;
        $this->account = $account;
    }

    public function signIn()
    {
        $this->validate($this->request, [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        $foundAccount = $this->account->getAccountByEmail($this->request->input('email'));

        if (!$foundAccount) {
            return Response::commonResponse("Not exists", [], 404);
        }

        if (app('hash')->check($this->request->input('password'), $foundAccount->password)) {
            return Response::commonResponse("Succeed Sign In", $foundAccount, 200);
        } else {
            return Response::commonResponse("Incorrect password", [], 401);
        }

    }

    public function signUp()
    {

        $this->validate($this->request, [
            'email' => 'required|email|unique:accounts',
            'password' => 'required',
            'username' => 'required|unique:accounts_users|max:255',
            'gender' => 'required',
            'birth' => 'required|date'
        ]);

        $account = new Account();
        $accountUser = new AccountUser();
        $token = new \App\Models\Token();

        $account->email = $this->request->email;
        $account->password = app('hash')->make($this->request->password);

        $account->save();

        $accountUser->username = $this->request->username;
        $accountUser->birth = $this->request->birth;
        $accountUser->gender = $this->request->gender;
        $accountUser->account_idx = $account->idx;

        $token->token = Token::getToken();
        $token->account_idx = $account->idx;
        $token->expire_at = date('Y-m-d H:i:s', strtotime('+3 days'));

        $accountUser->save();
        $token->save();

        return response()->json($account);

    }
}