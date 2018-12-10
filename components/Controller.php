<?php
/**
 * Created by PhpStorm.
 * User: sanzhikee
 * Date: 2018-12-09
 * Time: 21:01
 */

namespace App\components;

abstract class Controller
{
    /**
     * @var string
     */
    public $viewPath = __DIR__ . '/../views/';
    /**
     * @var string
     */
    public $layoutFilePath = __DIR__ . '/../views/layouts/main';

    /**
     * Controller constructor.
     */
    public function __construct()
    {
    }

    /**
     * @param $viewFile
     * @param $params
     * @return false|string
     */
    public function render($viewFile, $params, $layout = null)
    {
        $content = $this->template($params, $this->viewPath.$viewFile);

        return $this->template(['content' => $content], $this->layoutFilePath);
    }

    public function template($params, $viewFile)
    {
        ob_start();
        foreach ( $params as $key => $value) {
            ${$key} = $value;
        }
        include $viewFile.".php";
        return ob_get_clean();
    }

    /**
     * @param $path
     */
    public function redirect($path)
    {
        header('Location: '.$path);
        exit;
    }
}