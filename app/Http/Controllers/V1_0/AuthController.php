<?php

namespace App\Http\Controllers\V1_0;

//use Illuminate\Support\Facades\Validator as Validator;
use Illuminate\Support\Facades\Validator;

use App\Helpers\Token;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\AccountUser;
use App\Models\AccountUserFB;
use Illuminate\Http\Request;
use App\Helpers\Response;


use App\Models\Token as TokenModel;


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

        $foundAccount = $this->account->getAccountInfoByEmail($this->request->email);

        if (!$foundAccount) {
            return Response::commonResponse("not exists", [], 404);
        }

        if (app('hash')->check($this->request->input('password'), $foundAccount->password)) {
            return Response::commonResponse("succeed sign in", $foundAccount, 200);
        } else {
            return Response::commonResponse("incorrect password", [], 401);
        }
    }

    public function signUp()
    {

        $this->validate($this->request, $this->getCommonSignUpValidation());

        $account = new Account();
        $accountUser = new AccountUser();

        $this->baseSignUp($account, $accountUser);

        $this->generateToken($account->idx);

        return Response::commonResponse("successful signUp", $account->getAccountByIdx($account->idx), 201);
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
                $userFB->fb_id = $this->request->appId;

                $foundUser = $userFB->findUserByAppId();

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

                $this->baseSignUp($account, $accountUser);

                $accountUserFB = new AccountUserFB();

                $this->generateToken($account->idx);

                $accountUserFB->fb_id = $this->request->appId;
                $accountUserFB->fb_token = $this->request->appToken;
                $accountUserFB->account_idx = $account->idx;

                $accountUserFB->save();


                return Response::commonResponse("Successful SignUp", $account->getAccountByIdx(), 201);
                break;
        }

    }

    public function refreshToken()
    {
        $userToken = $this->request->header('Authorization');


        $tokenModel = new TokenModel();
        $foundToken = $tokenModel->findByToken($userToken);

        if(!isset($foundToken)){
            return Response::commonResponse("not valid token", $foundToken, 401);
        }

        $this->getNewToken($foundToken);

        $foundToken->expire_at = TokenModel::getTokenVerifyTime();
        $foundToken->save();

        return Response::commonResponse("successful refreshed token", $foundToken, 200);
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

    private function generateToken(int $accountIdx)
    {
        $tokenModel = new TokenModel();

        $this->getNewToken($tokenModel);

        $tokenModel->account_idx = $accountIdx;
        $tokenModel->expire_at = TokenModel::getTokenVerifyTime();

        $tokenModel->save();
    }

    private function getNewToken(TokenModel &$tokenModel)
    {
        $tokenModel->token = Token::getToken();

        while ($tokenModel->isExistsToken()) {
            $tokenModel->token = Token::getToken();
        }

        return $tokenModel;
    }

    private function baseSignUp(Account &$account, AccountUser &$accountUser)
    {

        $account->email = $this->request->email;
        $account->password = app('hash')->make($this->request->password);

        $account->save();

        $accountUser->username = $this->request->username;
        $accountUser->birth = $this->request->birth;
        $accountUser->gender = $this->request->gender;
        $accountUser->account_idx = $account->idx;

        $accountUser->save();
    }

}