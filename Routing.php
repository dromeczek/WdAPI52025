<?php
// Routing.php — minimalny router z regexami (singleton)
final class Router
{
    private static ?Router $instance = null;
    private array $routes = []; // każdy wpis: ['method'=>..., 'regex'=>..., 'handler'=>callable]

    private function __construct() {}

    public static function getInstance(): self {
        return self::$instance ??= new self();
    }

    // $pattern to fragment REGEX, bez delimiterów — my opakujemy go w #^...$#u
    public function add(string $method, string $pattern, callable $handler): void {
        $this->routes[] = [
            'method'  => strtoupper($method),
            'regex'   => "#^{$pattern}$#u",
            'handler' => $handler
        ];
    }

    public function dispatch(string $method, string $uri): void {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';
        $method = strtoupper($method);

        foreach ($this->routes as $r) {
            if ($r['method'] !== $method) continue;
            if (preg_match($r['regex'], $path, $m)) {
                // tylko nazwane grupy (?P<nazwa>...) jako parametry
                $params = array_filter($m, 'is_string', ARRAY_FILTER_USE_KEY);
                ($r['handler'])($params);
                return;
            }
        }

        // 404
        http_response_code(404);
        require __DIR__ . '/public/views/404.html';
    }
}
