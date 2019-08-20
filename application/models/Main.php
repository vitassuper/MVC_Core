<?php

namespace application\models;

use application\core\Model;

class Main extends Model{
   
    public function getPosts(){
       $result = $this->db->row('SELECT title, `text` FROM posts');
       return $result;
    }

    public function createPost($request){
        $this->db->query("INSERT INTO `posts` (`title`, `text`) VALUES ('$request', '78')");
    }
}
