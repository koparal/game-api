<?php

class APIController extends Controller
{
    /**
     * @var string
     */
    protected $contentType;

    /**
     * @var Predis\Client
     */
    protected $redis;

    public function __construct()
    {
        // Set content type
        $this->contentType = CONTENT_TYPE_JSON;
        // set response headers
        $this->setResponseHeaders();
        // check request headers
        $this->checkRequestHeaders();
        // init redis
        $this->initRedis();
    }

    public function initRedis()
    {
        // init register redis client
        Predis\Autoloader::register();
        // create redis client
        $this->redis = new Predis\Client([
            'host' => Config::REDIS_HOST,
            'port' => Config::REDIS_PORT,
            'persistent' => Config::REDIS_PERSISTENT
        ]);
    }

    /**
     * Set Response Headers
     */
    public function setResponseHeaders(): void
    {
        header("Content-Type: ". $this->contentType);
    }

    /**
     * Check the content type on header
     */
    public function checkRequestHeaders()
    {
        if ( ! $this->checkContentType($this->contentType)){
            $this->errorResponse('Content type invalid. Supported type : '. $this->contentType);
        }
    }

    /**
     * @param string $contentType
     * @return bool
     */
    public function checkContentType(string $contentType) : bool
    {
        // Check content type
        if (isset($_SERVER["CONTENT_TYPE"]) && $_SERVER["CONTENT_TYPE"] == $contentType){
            return true;
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function getJsonBody()
    {
        // Get body
        $jsonBody = file_get_contents("php://input");

        return json_decode($jsonBody, true);
    }

    /**
     * @param array $result
     * @param string|null $message
     */
    public function successResponse(array $result, string $message = null)
    {
        $response = [
            'status' => 'success',
            'timestamp' => date('Y-m-d H:i:s'),
            'result' => $result
        ];

        if($message){
            $response['message'] = $message;
        }

        http_response_code(200);

        echo json_encode($response);
        exit();
    }

    /**
     * @param string $error
     * @param int $code
     */
    public function errorResponse(string $error, int $code = 404)
    {
        $response = [
            'error' => $error,
        ];

        http_response_code($code);

        echo json_encode($response);
        exit();
    }

}