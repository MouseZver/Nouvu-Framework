<?php

header () ????

session_start () ???

ob_start () ???

    /**
     * Create a new request instance from the given Laravel request.
     *
     * @param  \Illuminate\Http\Request  $from
     * @param  \Illuminate\Http\Request|null  $to
     * @return static
     */
    public static function createFrom(self $from, $to = null)
    {
        $request = $to ?: new static;

        $files = $from->files->all();

        $files = is_array($files) ? array_filter($files) : $files;

        $request->initialize(
            $from->query->all(),
            $from->request->all(),
            $from->attributes->all(),
            $from->cookies->all(),
            $files,
            $from->server->all(),
            $from->getContent()
        );

        $request->headers->replace($from->headers->all());

        $request->setJson($from->json());

        if ($session = $from->getSession()) {
            $request->setLaravelSession($session);
        }

        $request->setUserResolver($from->getUserResolver());

        $request->setRouteResolver($from->getRouteResolver());

        return $request;
    }
	
	/**
     * Create an Illuminate request from a Symfony instance.
     *
     * @param  \Symfony\Component\HttpFoundation\Request  $request
     * @return static
     */
    public static function createFromBase(SymfonyRequest $request)
    {
        $newRequest = (new static)->duplicate(
            $request->query->all(), $request->request->all(), $request->attributes->all(),
            $request->cookies->all(), $request->files->all(), $request->server->all()
        );

        $newRequest->headers->replace($request->headers->all());

        $newRequest->content = $request->content;

        $newRequest->request = $newRequest->getInputSource();

        return $newRequest;
    }
	
$kernel = $app->make(Kernel::class);

$response = tap($kernel->handle(
    $request = Request::capture()
))->send();

$kernel->terminate($request, $response);



public function handle($request)
{
	try {
		$request->enableHttpMethodParameterOverride();

		$response = $this->sendRequestThroughRouter($request);
	} catch (Exception $e) {
		$this->reportException($e);

		$response = $this->renderException($request, $e);
	} catch (Throwable $e) {
		$this->reportException($e = new FatalThrowableError($e));

		$response = $this->renderException($request, $e);
	}

	event(new Events\RequestHandled($request, $response));

	return $response;
}

public function terminate($request, $response)
{
	$this->terminateMiddleware($request, $response);

	$this->app->terminate();
}