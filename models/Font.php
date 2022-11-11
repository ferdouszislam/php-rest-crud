<?php

include_once 'BaseModel.php';

class Font extends BaseModel
{

    public $table = 'font';

    public $id;
    public $font_name;
    public $file_path;
    public $file_size;
    public $created_at;

    public function create() {
        
    }

    public function readAll()
    {
        $fonts = array();
        $query = 'SELECT t.id, t.font_name, t.file_path, t.file_size, t.created_at FROM ' . $this->table . ' t';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt;
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $font = new Font();
            $font->id = $row['id'];
            $font->font_name = $row['font_name'];
            $font->file_path = $row['file_path'];
            $font->file_size = $row['file_size'];
            array_push($fonts, $font);
        }
        return  $fonts;
    }

    public function delete() {

    }
}
