<?php
/**
 * SkinCheck - Application Router
 * Parses URLs and loads controllers, actions, and parameters.
 * Format: /controller/method/params
 */

class App {
    protected $currentController = 'HomeController';
    protected $currentMethod = 'index';
    protected $params = [];

    public function __construct() {
        $url = $this->getUrl();

        // Check if controller exists for the first url segment
        if (isset($url[0])) {
            $controllerName = ucwords($url[0]) . 'Controller';
            if (file_exists(__DIR__ . '/../controllers/' . $controllerName . '.php')) {
                $this->currentController = $controllerName;
                unset($url[0]);
            }
        }

        // Require the controller file (autoloader will trigger if not already loaded, but safe to verify)
        require_once __DIR__ . '/../controllers/' . $this->currentController . '.php';

        // Instantiate the controller
        $this->currentController = new $this->currentController();

        // Check if action method exists for the second url segment
        if (isset($url[1])) {
            if (method_exists($this->currentController, $url[1])) {
                $this->currentMethod = $url[1];
                unset($url[1]);
            }
        }

        // Capture remaining segments as parameters
        $this->params = $url ? array_values($url) : [];

        // Call controller action and pass parameter list
        call_user_func_array([$this->currentController, $this->currentMethod], $this->params);
    }

    /**
     * Sanitizes and parses URL parameters.
     * @return array
     */
    private function getUrl() {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }
        return [];
    }
}
