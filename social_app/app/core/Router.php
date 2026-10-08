<?php
/**
 * Router - maps  index.php?url=controller/action/param  to
 *          ControllerController::action($param)
 */
class Router
{
    public function dispatch(): void
    {
        $path = isset($_GET['url']) && is_string($_GET['url']) ? trim($_GET['url'], '/') : '';
        if ($path === '') {
            $path = Auth::check() ? 'feed/index' : 'auth/login';
        }

        $parts          = explode('/', $path);
        $controllerName = strtolower((string) array_shift($parts));
        $action         = strtolower((string) array_shift($parts));
        if ($action === '') {
            $action = 'index';
        }

        if (!preg_match('/^[a-z]+$/', $controllerName) || !preg_match('/^[a-z]+$/', $action)) {
            $this->notFound();
            return;
        }

        $class = ucfirst($controllerName) . 'Controller';
        $file  = APP_PATH . '/controllers/' . $class . '.php';
        if (!is_file($file)) {
            $this->notFound();
            return;
        }
        require_once $file;

        if (!class_exists($class) || !method_exists($class, $action)) {
            $this->notFound();
            return;
        }

        // Only public methods declared in the controller itself are routable.
        $ref = new ReflectionMethod($class, $action);
        if (!$ref->isPublic() || $ref->isStatic() || $ref->getDeclaringClass()->getName() !== $class) {
            $this->notFound();
            return;
        }
        if (count($parts) < $ref->getNumberOfRequiredParameters() || count($parts) > $ref->getNumberOfParameters()) {
            $this->notFound();
            return;
        }

        $controller = new $class();
        call_user_func_array([$controller, $action], $parts);
    }

    private function notFound(): void
    {
        http_response_code(404);
        View::render('errors/error', [
            'title'   => 'Page not found',
            'code'    => 404,
            'message' => 'The page you are looking for could not be found.',
        ]);
    }
}
