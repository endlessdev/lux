<?php

namespace App\Http\Controllers\V1_0;

use App\Helpers\Token;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\AccountUser;
use App\Models\AccountUserFB;
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

        $this->validate($this->request, $this->getCommonSignUpValidation());

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

    public function signInWithApp($snsType)
    {

        $this->validate($this->request, [
            'appId' => 'required|exists:accounts_users_fb,fb_id',
            'appToken' => 'required|exists:accounts_users_fb,fb_token'
        ]);

        switch ($snsType) {
            case "fb":
                $userFB = new AccountUserFB();
                $foundUser = $userFB->findUserByAppId($this->request->appId);
                return Response::commonResponse("Success signInWithApp", $foundUser, 200);
                break;
        }

        return Response::commonResponse("Failed signInWithApp", [], 400);
    }

    public function signUpWithApp($snsType)
    {

        switch ($snsType) {
            case 'fb':
                $fbValidationCollection = collect([
                    'appId' => 'required|unique:accounts_users_fb,fb_id',
                    'appToken' => 'required|unique:accounts_users_fb,fb_token'
                ]);
                $this->validate($this->request, $fbValidationCollection
                    ->merge($this->getCommonSignUpValidation())->toArray());

                $account = new Account();
                $accountUser = new AccountUser();
                $accountUserFB = new AccountUserFB();
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

                $accountUserFB->fb_id = $this->request->appId;
                $accountUserFB->fb_token = $this->request->appToken;
                $accountUserFB->account_idx = $account->account_idx;

                $accountUser->save();
                $token->save();

                break;
        }

    }

    public function refreshToken(int $accountIdx)
    {

    }

    public function getAuthInfo(int $accountIdx)
    {

    }

    public function deleteAccount()
    {

    }

    private function getCommonSignUpValidation()
    {
        return ['email' => 'required|email|unique:accounts',
            'password' => 'required',
            'username' => 'required|unique:accounts_users|max:255',
            'gender' => 'required',
            'birth' => 'required|date'];
    }

}