<?php

namespace App\Exceptions;

use App\Notifications\ErrorLog;
use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Support\Facades\Route;
use Log;
use Mail;
use Illuminate\Support\Facades\Auth;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param \Exception $exception
     * @return void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
//        if ($this->shouldReport($exception)) {
//            ErrorLog::sendTelegram($exception);
//        }

        //send error to email
        $this->sendErrorLog($exception);
        
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception $exception
     * @return \Illuminate\Http\Response|\Symfony\Component\HttpFoundation\Response
     * @throws Exception
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof \Illuminate\Session\TokenMismatchException) {
            return redirect('/login');
        }
        return parent::render($request, $exception);
    }

    private function sendErrorLog($e){

        $receivers = [
            // ['name'=>'Mr. Pov', 'email'=> 'oeurnpov007@gmail.com'],
            ['name'=>'Mr. Pov-STSK', 'email'=> 'pov@sahakrinpheap.com.kh'],
            // ['name'=>'Mr. Pov-Error-Log', 'email'=> 'eapprov.log@stskgroup.com'],
        ];

        if (!Auth::check()) {
            // The user is logged in...
            return false;
        }

        if (!env('MAIL_USERNAME')) {
            return false;
        }

        //it not error just user expired session
        if (@$e->getMessage() == 'Unauthenticated.' || @$e->getMessage() == 'CSRF token mismatch.' || @$e->getMessage() == null || @$e->getMessage() == '') {
            return false;
        }

        foreach($receivers as $obj){

            $to_name = @$obj['name'];
            $to_email = @$obj['email'];

            $error_from = env('APP_ENV');

            $data = [
                'name' => @$to_name,
                'error_from' => @$error_from,
                'msg' => @$e->getMessage(),
                'line' => @$e->getLine(),
                'action' => @Route::currentRouteAction(),
                'fullUrl' => @$_SERVER['REQUEST_URI'],
                'user' => @Auth::user()->id.': '.@Auth::user()->username. ': '.@Auth::user()->name,
                'time' => date('d-M-Y H:i:s A', strtotime(now())),
            ];

            try {
                Mail::send('emails.error_log', $data, function($message) use ($to_name, $to_email) {
                    $message->to($to_email, $to_name)->subject('E-Approval Error Logs');
                    $message->from(env('MAIL_USERNAME'), 'E-Approval System Log');
                });
            } catch(\Swift_TransportException $e) {
                // dd($e, app('mailer'));
            }

        }

    }
}
