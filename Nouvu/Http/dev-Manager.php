<?php

declare ( strict_types = 1 );

namespace Nouvu\Web\Http;

use Nouvu\Web\Foundation\Application AS App;

class Request
{
	public const 
		METHOD_HEAD 	= 'HEAD',
		METHOD_GET 		= 'GET',
		METHOD_POST 	= 'POST',
		METHOD_PUT 		= 'PUT',
		METHOD_PATCH 	= 'PATCH',
		METHOD_DELETE 	= 'DELETE',
		METHOD_PURGE 	= 'PURGE',
		METHOD_OPTIONS 	= 'OPTIONS',
		METHOD_TRACE 	= 'TRACE',
		METHOD_CONNECT 	= 'CONNECT';
	
	/*
		- Custom parameters
	*/
	public $attributes;
	
	/*
		- $_POST
	*/
	public $request;
	
	/*
		- $_GET
	*/
	public $query;
	
	/*
		- Server and execution environment parameters ($_SERVER).
		- all ????
	*/
	public $server;
	
	/*
		- $_FILES
	*/
	public $files;
	
	/*
		- $_COOKIE
	*/
	public $cookies;
	
	/*
		- Headers (taken from the $_SERVER)
		- ???
	*/
	public $headers;
	
	/*
		- string|resource|false|null
	*/
	protected string | resource | false | null $content;
	
	/*
		- массив поддерживаемых локализаций
	*/
	protected array $languages;
	
	/*
		- массив разрешенных расширений
	*/
	protected array $acceptableContentTypes;
	
	/*
		- ??? файл
		- https://www.php.net/manual/ru/reserved.variables.server.php
		
		- Содержит любой предоставленный пользователем путь, 
		- содержащийся после имени скрипта, но до строки запроса, если она есть. 
		- Например, если текущий скрипт запрошен по URL 
		- http://www.example.com/php/path_info.php/some/stuff?foo=bar, 
		- то переменная $_SERVER['PATH_INFO'] будет содержать /some/stuff. 
	*/
	protected string $pathInfo;
	
	/*
		- полный/сырой uri запрос
	*/
	protected string $requestUri;
	
	/*
		- https://site.com/index/test.html
		- or
		- http://localhost/myproject/
	*/
	protected string $baseUrl;
	
	/*
		- ?????????
	*/
	protected string $basePath;
	
	/*
		- REQUEST_METHOD
	*/
	protected string $method;
	
	/*
		- ?????????
	*/
	protected string $format;
	
	/*
		- ?????????
	*/
	protected static $formats;
	
	/*
		- SessionInterface|callable
		- ?????????
	*/
	protected $session;
	
	/*
		- локализация
		- но смысл ?? $app -> getLocale()
		- https://www.php.net/manual/ru/locale.setdefault.php
	*/
	protected string $locale;
	
	/*
		- 
	*/
	protected $defaultLocale = 'en';
	
	/**
     * @param array                $query      The GET parameters
     * @param array                $request    The POST parameters
     * @param array                $attributes The request attributes (parameters parsed from the PATH_INFO, ...)
     * @param array                $cookies    The COOKIE parameters
     * @param array                $files      The FILES parameters
     * @param array                $server     The SERVER parameters
     * @param string|resource|null $content    The raw body data
     */
    public function __construct ( 
		array $query = [], 
		array $request = [], 
		array $attributes = [], 
		array $cookies = [], 
		array $files = [], 
		array $server = [], 
		string | resource | null $content = null 
	)
    {
        $this->initialize($query, $request, $attributes, $cookies, $files, $server, $content);
    }
	
	public function initialize(array $query = [], array $request = [], array $attributes = [], array $cookies = [], array $files = [], array $server = [], $content = null)
    {
        $this->request = new InputBag($request);
        $this->query = new InputBag($query);
        $this->attributes = new ParameterBag($attributes);
        $this->cookies = new InputBag($cookies);
        $this->files = new FileBag($files);
        $this->server = new ServerBag($server);
        $this->headers = new HeaderBag($this->server->getHeaders());

        $this->content = $content;
        $this->languages = null;
        $this->charsets = null;
        $this->encodings = null;
        $this->acceptableContentTypes = null;
        $this->pathInfo = null;
        $this->requestUri = null;
        $this->baseUrl = null;
        $this->basePath = null;
        $this->method = null;
        $this->format = null;
    }
}