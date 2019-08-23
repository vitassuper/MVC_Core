<?php

namespace application\controllers;

use application\core\Controller;
use application\lib\Validator\Factory;

class MainController extends Controller
{

    public function indexAction()
    {
        $result = $this->model->getPosts();
        $vars = [
            'news' => $result,
        ];
        $this->view->render('Главная страница', $vars);
    }
    public function formAction(){

        $messages = [
            'name' => 'Имя не должно быть пустым',
            'name.length' => 'Длина имени может быть от 5 до 15 символов'
        ];
        $validator = Factory::getInstance()->make($this->request->requestData(), [
            'name' => 'length:5,15|not_empty'
        ], $messages);
        $valid = $validator->validate();
        var_dump($validator->getErrors());
        //$this->redirect("http://core/");
    }

    public function contactAction(){
        
    }
}
