<?php

namespace application\controllers;

use application\core\Controller;
use application\lib\db;

class MainController extends Controller{

    public function indexAction(){
        $result = $this->model->getPosts();
        $vars = [
            'news' =>$result,
        ];
        $this->view->render('Главная страница', $vars);
    }
    
    
}