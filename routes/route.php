<?php

class Route
{
    /**
     * @param string $method
     * @param string $url
     * @param string $callback
     */
    public static function run(string $method, string $url, string $callback)
    {
        // explode the method
        $method = explode('|', strtoupper($method));

        // check valid request method
        if (in_array($_SERVER['REQUEST_METHOD'], $method)) {

            // url patterns
            $patterns = [
                '{url}' => '([0-9a-zA-Z]+)',
                '{id}' => '([0-9]+)'
            ];

            // replace the url
            $url = str_replace(array_keys($patterns), array_values($patterns), $url);

            // get parsed request url
            $requestUri = self::parse_url();

            if (preg_match('@^' . $url . '$@', $requestUri, $parameters)) {

                unset($parameters[0]);

                // check is callable
                if (is_callable($callback)) {

                    call_user_func_array($callback, $parameters);

                } else {

                    // parse the class name
                    $controller = explode('@', $callback);
                    $className = explode('/', $controller[0]);
                    $className = end($className);
                    $function = $controller[1];
                    $controllerFile = './App/Controller/' . $controller[0] . '.php';

                    // check controller file
                    if (file_exists($controllerFile)) {

                        require "$controllerFile";
                        // call class function
                        call_user_func_array([new $className, $function], $parameters);

                    }else{
                        echo "Class not found.";
                        exit();
                    }
                }
            }
        }
    }

    /**
     * @return array|string
     */
    public static function parse_url()
    {
        $dirName = dirname($_SERVER['SCRIPT_NAME']);
        $dirName = $dirName != '/' ? $dirName : null;
        $baseName = basename($_SERVER['SCRIPT_NAME']);

        return str_replace([$dirName, $baseName], null, $_SERVER['REQUEST_URI']);
    }
}
