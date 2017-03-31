<?php

namespace App\Http\Controllers\V1_0;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\AccountUser;
use Illuminate\Support\Facades\Hash;
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

        if(!$foundAccount){
            return Response::commonResponse("Not exists", [], 404);
        }

        if (Hash::check($this->request->input('password'), $foundAccount->password)) {
            return Response::commonResponse("Succeed Sign In", $foundAccount, 200);
        } else {
            return Response::commonResponse("Failed to find Account", [], 200);
        }

    }

    public function signUp()
    {
        $this->validate($this->request, [
            'email' => 'required|email',
            'password' => 'required',
            'username' => 'required|unique:accounts_user|max:255',
            'gender' => 'required',
            'birth' => 'required | date'
        ]);

        $account = new Account();
        $accountUser = new AccountUser();

        $account->email = $this->request->input('email');
        $account->password = $this->request->input('password');

    }
}