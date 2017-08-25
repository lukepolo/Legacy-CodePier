<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [

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
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param \Exception $exception
     *
     * @return void
     */
    public function report(Exception $exception)
    {
        if ($this->shouldReport($exception) && config('app.env') == 'production') {
            app('sentry')->captureException($exception);
        }
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Exception               $exception
     *
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        return parent::render($request, $exception);
    }

    /**
     * Create a Symfony response for the given exception.
     *
     * @param \Exception $exception
     *
     * @return mixed
     */
    protected function convertExceptionToResponse(Exception $exception)
    {
        if (config('app.debug')) {
            $this->unsetSensitiveData();

            $whoops = new \Whoops\Run();
            $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler());

            return new \Illuminate\Http\Response(
                $whoops->handleException($exception),
                $exception->getStatusCode(),
                $exception->getHeaders()
            );
        }

        if (\Request::expectsJson()) {
            return response()->json('We have an error', 500);
        }

        return response()->view('errors.500', ['exception' => $exception], 500);
    }

    /**
     * Don't ever display sensitive data in Whoops pages.
     */
    protected function unsetSensitiveData()
    {
        foreach ($_ENV as $key => $value) {
            unset($_SERVER[$key]);
        }
        $_ENV = [];
    }
}
