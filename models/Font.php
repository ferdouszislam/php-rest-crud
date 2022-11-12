<?php

include_once 'BaseModel.php';

class Font extends BaseModel
{

    public $table = 'font';

    const FILE_SIZE_UNIT = 'KB';

    public $id;
    public $font_name;
    public $file_path;
    public $file_size;

    public function create()
    {
        $query = 'INSERT INTO ' . $this->table . ' SET font_name = :font_name, file_path = :file_path, file_size = :file_size';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':font_name', $this->font_name);
        $stmt->bindParam(':file_path', $this->file_path);
        $stmt->bindParam(':file_size', $this->file_size);
        if ($stmt->execute()) return true;
        printf("error occurred in Font.create(): %s.\n", $stmt->error);
        return false;
    }

    public function readAll()
    {
        $fonts = array();
        $query = 'SELECT t.id, t.font_name, t.file_path, t.file_size FROM ' . $this->table . ' t';
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

    public function read($id)
    {
        $query = 'SELECT t.id, t.font_name, t.file_path, t.file_size FROM ' . $this->table . ' t WHERE t.id = ? LIMIT 0,1';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (gettype($result) == 'boolean') return null;
        $this->id = $result['id'];
        $this->font_name = $result['font_name'];
        $this->file_path = $result['file_path'];
        $this->file_size = $result['file_size'];
        return $this;
    }

    public function delete($id)
    {
        $query = 'DELETE FROM ' . $this->table . ' WHERE id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        if ($stmt->execute()) return true;
        printf("Error: %s.\n", $stmt->error);
        return false;
    }
}
