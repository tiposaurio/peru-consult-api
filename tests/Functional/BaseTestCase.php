<?php

namespace Tests\Functional;

use PHPUnit\Framework\TestCase;
use Slim\App;
use Slim\Http\{Request, Response, Environment};

/**
 * This is an example class that shows how you could set up a method that
 * runs the application. Note that it doesn't cover all use-cases and is
 * tuned to the specifics of this skeleton app, so if your needs are
 * different, you'll need to change it.
 */
class BaseTestCase extends TestCase
{
    /**
     * Use middleware when running application?
     *
     * @var bool
     */
    protected $withMiddleware = true;

    /**
     * Process the application given a request method and URI
     *
     * @param string $requestMethod the request method (e.g. GET, POST, etc.)
     * @param string $requestUri the request URI
     * @param array|object|null $requestData the request data
     * @return \Slim\Http\Response
     */
    public function runApp($requestMethod, $requestUri, $requestData = null)
    {
        $settings = require __DIR__ . '/../../src/settings.php';
        // Create a mock environment for testing with
        $environment = Environment::mock(
            [
                'REQUEST_METHOD' => $requestMethod,
                'REQUEST_URI' => $requestUri.'?token='.$settings['settings']['auth']['token'],
            ]
        );

        // Set up a request object based on the environment
        $request = Request::createFromEnvironment($environment);

        // Add request data, if it exists
        if (isset($requestData)) {
            if (is_string($requestData)) {
                $request->getBody()->write($requestData);
                $request->getBody()->rewind();
            } else {
                $request = $request->withParsedBody($requestData);
            }
        }

        // Set up a response object
        $response = new Response();

        // Use the application settings

        // Instantiate the application
        $app = new App($settings);

        // Set up dependencies
        require __DIR__ . '/../../src/dependencies.php';

        // Register middleware
        if ($this->withMiddleware) {
            require __DIR__ . '/../../src/middleware.php';
        }

        // Register routes
        require __DIR__ . '/../../src/routes.php';

        // Process the application
        $response = $app->process($request, $response);

        // Return the response
        return $response;
    }
}
