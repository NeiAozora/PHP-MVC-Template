<?php

class Router {
    
    private static $instance = null;
    private $routes = [];
    private $globalMiddlewares = [];
    private $notFoundHandler;
    private $exceptionHandler;
    private $currentMethod;
    private $currentPath;

    private function __construct() {}

    public static function getInstance(): self {
        if (self::$instance === null) {
            self::$instance = new Router();
        }
        return self::$instance;
    }

    public static function get(string $path, callable $callback, array $middlewares = []): self {
        return self::getInstance()->addRoute('GET', $path, $callback, $middlewares);
    }

    public static function post(string $path, callable $callback, array $middlewares = []): self {
        return self::getInstance()->addRoute('POST', $path, $callback, $middlewares);
    }

    public static function notFound(callable $callback): self {
        self::getInstance()->setNotFoundHandler($callback);
        return self::getInstance();
    }

    public static function setExceptionHandler(callable $callback): self {
        self::getInstance()->exceptionHandler = $callback;
        return self::getInstance();
    }

    public function addGlobalMiddleware(callable $middleware): self {
        $this->globalMiddlewares[] = $middleware;
        return $this;
    }

    public function addMiddleware(callable $middleware): self {
        $this->routes[$this->currentMethod][$this->currentPath]['middlewares'][] = $middleware;
        return $this;
    }

    private function addRoute(string $method, string $path, callable $callback, array $middlewares): self {
        $this->routes[$method][$path] = [
            'callback' => $callback,
            'middlewares' => $middlewares
        ];
        return $this;
    }

    private function setNotFoundHandler(callable $callback): void {
        $this->notFoundHandler = $callback;
    }

    public function dispatch(): void {

        $this->currentMethod = $_SERVER['REQUEST_METHOD'];
        $this->currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    
        // Handle empty path by redirecting to homepage ('/')
        if ($this->currentPath === '') {
            $this->currentPath = '/';
        }

        
        // Diperuntukan jika memakai XAMPP di htdocs dan masuk ke sub-directory untuk masuk ke aplikasi
        if (!WEB_DOMAIN_MODE) {
            $this->currentPath = str_replace("/" . ROOT_DIRECTORY_NAME . "/public", "", $this->currentPath);
            $this->currentPath = str_replace("/" . ROOT_DIRECTORY_NAME . "", "", $this->currentPath);
        }
    
        $publicDir = ROOT . '/public';
        $filePath = $publicDir . $this->currentPath;
    
        if (file_exists($filePath) && is_file($filePath)) {
            $this->serveStaticFile($filePath);
            return;
        } elseif (strpos($this->currentPath, '/public/') === 0) {
            throw new Exception("Target file '$filePath' tidak ada: " . $this->currentPath);
        }
    
        // Apply global middlewares
        foreach ($this->globalMiddlewares as $middleware) {
            call_user_func($middleware);
        }
    
        try {
            if (isset($this->routes[$this->currentMethod])) {
                $routeFound = false;
                foreach ($this->routes[$this->currentMethod] as $path => $route) {
                    $pathRegex = preg_replace('/\{\w+\}/', '(\w+)', $path);
                    if (preg_match('#^' . $pathRegex . '$#', $this->currentPath, $matches)) {
                        $routeFound = true;
                        array_shift($matches); // Remove the full match
                        $params = $matches;
    
                        foreach ($route['middlewares'] as $middleware) {
                            call_user_func($middleware);
                        }
                        call_user_func_array($route['callback'], $params);
                        break;
                    }
                }
                if (!$routeFound) {
                    $this->handleNotFound();
                }
            } else {
                $this->handleNotFound();
            }
        } catch (Exception $e) {
            $this->handleException($e);
        }
    }
    
    private function serveStaticFile(string $filePath): void {
        $fileInfo = pathinfo($filePath);
        $extension = isset($fileInfo['extension']) ? $fileInfo['extension'] : '';
        $mimeType = $this->getMimeType($extension);
        if ($mimeType) {
            header('Content-Type: ' . $mimeType);
        }
        readfile($filePath);
    }

    private function getMimeType(string $extension): ?string {
        $mimeTypes = [
            'html' => 'text/html',
            'css' => 'text/css',
            'js' => 'application/javascript',
            'json' => 'application/json',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'svg' => 'image/svg+xml',
            'ico' => 'image/x-icon',
            'txt' => 'text/plain',
            'pdf' => 'application/pdf',
            'zip' => 'application/zip',
            'rar' => 'application/x-rar-compressed',
        ];
        return $mimeTypes[$extension] ?? null;
    }

    private function handleNotFound(): void {
        if ($this->notFoundHandler) {
            call_user_func($this->notFoundHandler);
        } else {
            http_response_code(404);
            echo '<h1>404 Tidak Ditemukan</h1><p>Halaman yang Anda cari tidak ada.</p>';
        }
    }

    private function handleException(Exception $e): void {
        if ($this->exceptionHandler) {
            call_user_func($this->exceptionHandler, $e);
        } else {
            http_response_code(500);
            echo '<h1>500 Kesalahan Internal Server</h1><p>Terjadi kesalahan: ' . htmlspecialchars($e->getMessage()) . '</p>';
        }
    }
}
?>
