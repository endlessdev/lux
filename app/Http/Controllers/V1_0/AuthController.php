<?php

namespace App\Http\Controllers\V1_0;

use App\Helpers\Token;
use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\AccountUser;
use App\Models\AccountUserFB;
use App\Models\AccountUserKakao;
use DateTime;
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

        if ($foundAccount->isDeletedUser()) {
            return Response::common(410);
        }

        if (!$foundAccount) {
            return Response::common(404);
        }

        if (app('hash')->check($this->request->input('password'), $foundAccount->password)) {
            $this->checkVerifyToken($foundAccount->expire_at, $foundAccount->idx);
            return Response::common(200, $foundAccount);
        } else {
            return Response::common(404);
        }
    }

    public function signUp()
    {

        $this->validate($this->request, $this->getCommonSignUpValidation());

        $account = new Account();
        $accountUser = new AccountUser();

        $this->baseSignUp($account, $accountUser, "general");
        $this->generateToken($account->idx);

        return Response::common(201, $account->getAccountByIdx($account->idx));
    }

    public function signInWithApp($snsType)
    {

        switch ($snsType) {
            case "fb":

                $this->validate($this->request, [
                    'appId' => 'required|exists:accounts_users_fb,fb_id',
                    'appToken' => 'required|exists:accounts_users_fb,fb_token'
                ]);

                $userFB = new AccountUserFB();
                $userFB->fb_id = $this->request->appId;

                $foundUser = $userFB->findUserByAppId();
                $this->checkDeletedUser($foundUser->account_idx);
                $this->checkVerifyToken($foundUser->expire_at, $foundUser->idx);
                return Response::common(200, $foundUser);
            case "kakao":

                $this->validate($this->request, [
                    'appId' => 'required|exists:accounts_users_kakao,kakao_id',
                    'appToken' => 'required|exists:accounts_users_kakao,kakao_token'
                ]);

                $userKakao = new AccountUserKakao();
                $userKakao->kakao_id = $this->request->appId;

                $foundUser = $userKakao->findUserByAppId();
                $this->checkDeletedUser($foundUser->account_idx);
                $this->checkVerifyToken($foundUser->expire_at, $foundUser->idx);
                return Response::common(200, $foundUser);
        }
        return Response::common(401);
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

                $this->baseSignUp($account, $accountUser, "facebook");

                $accountUserFB = new AccountUserFB();

                $this->generateToken($account->idx);

                $accountUserFB->fb_id = $this->request->appId;
                $accountUserFB->fb_token = $this->request->appToken;
                $accountUserFB->account_idx = $account->idx;

                $accountUserFB->save();


                return Response::common(201, $account->getAccountByIdx());
                break;
            case 'kakao':
                $fbValidationCollection = collect([
                    'appId' => 'required|unique:accounts_users_kakao,kakao_id',
                    'appToken' => 'required|unique:accounts_users_kakao,kakao_token'
                ]);

                $this->validate($this->request, $fbValidationCollection
                    ->merge($this->getCommonSignUpValidation())->toArray());

                $account = new Account();
                $accountUser = new AccountUser();

                $this->baseSignUp($account, $accountUser, "kakao");

                $accountUserKakao = new AccountUserKakao();

                $this->generateToken($account->idx);

                $accountUserKakao->kakao_id = $this->request->appId;
                $accountUserKakao->kakao_token = $this->request->appToken;
                $accountUserKakao->account_idx = $account->idx;

                $accountUserKakao->save();


                return Response::common(201, $account->getAccountByIdx());
                break;
        }

    }

    public function refreshToken()
    {
        $userToken = $this->request->header('Authorization');


        $tokenModel = new TokenModel();
        $foundToken = $tokenModel->findByToken($userToken);

        if (!isset($foundToken)) {
            return Response::common(401, $foundToken);
        }

        $this->getNewToken($foundToken);

        $foundToken->expire_at = TokenModel::getTokenVerifyTime();
        $foundToken->save();

        return Response::common(200, $foundToken);
    }

    public function getAuthInfo()
    {
        $userToken = $this->request->header('Authorization');

        $foundUserInfo = TokenModel::findByToken($userToken);

        if (!isset($foundUserInfo)) {
            return Response::common(401);
        }

        $this->checkDeletedUser($foundUserInfo->account_idx);
        return Response::common(200, $foundUserInfo);
    }

    public function deleteAccount(int $accountIdx)
    {
        $userToken = $this->request->header('Authorization');
        $foundToken = TokenModel::where('account_idx', $accountIdx)->where('token', $userToken);

        $foundAccount = Account::where('idx', $accountIdx);
        $this->checkDeletedUser($accountIdx);

        if (!isset($foundAccount) || !isset($foundToken)) {
            return Response::common(401);
        }

        $foundAccount->update(['deleted_at' => date('Y-m-d H:i:s')]);
        return Response::common(200, $foundAccount);
    }

    public function updateUserInfo()
    {
        $userToken = $this->request->header('Authorization');
        $foundToken = TokenModel::findByToken($userToken);

        if (!empty($foundToken)) {

            $this->validate($this->request, [
                'birth' => 'date',
                'gender' => 'in:male,female,androgyne,trigender,agender,genderfluid',
                'username' => 'unique:accounts_users,username'
            ]);

            $accountUser = AccountUser::where('account_idx', $foundToken->account_idx)->first();

            if ($this->request->input('birth')) {
                $accountUser->birth = $this->request->input('birth');

            }
            if ($this->request->input('gender')) {
                $accountUser->gender = $this->request->input('gender');

            }
            if ($this->request->input('username')) {
                $accountUser->username = $this->request->input('username');
            }

            $accountUser->save();
            return Response::common(200, $accountUser);
        } else {
            return Response::common(401);
        }

    }

    private function getCommonSignUpValidation()
    {
        return ['email' => 'required|email|unique:accounts',
            'password' => 'required',
            'username' => 'required|unique:accounts_users|max:255',
            'gender' => 'required',
            'birth' => 'required|date'];
    }

    private function checkVerifyToken(string $expireAt, int $accountIdx)
    {
        if (new DateTime($expireAt) < new DateTime()) {
            $tokenModel = new TokenModel();
            $foundToken = $tokenModel->findByAccountIdx($accountIdx);
            $this->getNewToken($foundToken);
        }
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

        $tokenModel->expire_at = $tokenModel->getTokenVerifyTime();

        return $tokenModel;
    }

    private function baseSignUp(Account &$account, AccountUser &$accountUser, string $joinType)
    {

        $account->email = $this->request->email;
        $account->password = app('hash')->make($this->request->password);

        $account->save();

        $accountUser->username = $this->request->username;
        $accountUser->birth = $this->request->birth;
        $accountUser->gender = $this->request->gender;
        $accountUser->account_idx = $account->idx;
        $accountUser->join_type = $joinType;

        $accountUser->save();
    }

    private function isDeletedUser(int $accountIdx)
    {
        $account = Account::where('idx', $accountIdx)->first();
        if (!empty($account->deleted_at)) {
            return true;
        }
        return false;
    }

    private function checkDeletedUser(int $accountIdx)
    {
        if ($this->isDeletedUser($accountIdx)) {
            return Response::common(404);
        }
        return false;
    }

}