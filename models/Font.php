<?php

include_once 'BaseModel.php';

class Font extends BaseModel
{

    protected $table = 'font';

    const FILE_SIZE_UNIT = 'KB';

    public $id;
    public $fontName;
    public $filePath;
    public $fileSize;

    public function create()
    {
        $query = 'INSERT INTO ' . $this->table . ' SET font_name = :fontName, file_path = :filePath, file_size = :fileSize';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':fontName', $this->fontName);
        $stmt->bindParam(':filePath', $this->filePath);
        $stmt->bindParam(':fileSize', $this->fileSize);
        if ($stmt->execute()) {
            $this->id = $this->getLastInsertedId();
            return true;
        } 
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
            $font->fontName = $row['font_name'];
            $font->filePath = $row['file_path'];
            $font->fileSize = $row['file_size'];
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
        $this->fontName = $result['font_name'];
        $this->filePath = $result['file_path'];
        $this->fileSize = $result['file_size'];
        return $this;
    }
}
