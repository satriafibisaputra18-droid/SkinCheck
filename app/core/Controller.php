<?php
/**
 * SkinCheck - Base Controller
 * All controllers inherit from this class.
 * Handles loading models and rendering views.
 */

class Controller {
    /**
     * Loads and instantiates a model.
     * @param string $model Name of the model class
     * @return object Instance of the model class
     */
    public function model($model) {
        $modelFile = __DIR__ . '/../models/' . $model . '.php';
        if (file_exists($modelFile)) {
            require_once $modelFile;
            return new $model();
        }
        die("Model file for '{$model}' not found.");
    }

    /**
     * Loads a view and passes data variables.
     * @param string $view View path inside app/views (e.g., 'home/index')
     * @param array $data Data array to expose to the view
     */
    public function view($view, $data = []) {
        $viewFile = __DIR__ . '/../views/' . $view . '.php';
        if (file_exists($viewFile)) {
            // Extract the data array to variables so they are accessible in view files
            extract($data);
            require_once $viewFile;
        } else {
            die("View file '{$view}' not found.");
        }
    }
}
