<?php

class FontForFontGroup extends BaseModel {

    protected $table = "font_for_font_group";

    public $id;
    public $fontName;
    public $fontGroupId;
    public $selectedFontId;
    public $specificSize;
    public $priceChange;

    public function create() {
        $query = 'INSERT INTO ' . $this->table . ' SET font_name = :fontName, font_group_id = :fontGroupId, '
        . 'font_id = :selectedFontId, specific_size = :specificSize, price_change = :priceChange';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':fontName', $this->fontName);
        $stmt->bindParam(':fontGroupId', $this->fontGroupId);
        $stmt->bindParam(':selectedFontId', $this->selectedFontId);
        $stmt->bindParam(':specificSize', $this->specificSize);
        $stmt->bindParam(':priceChange', $this->priceChange);
        if ($stmt->execute()) {
            $this->id = $this->getLastInsertedId();
            return true;
        }
        printf("error occurred in FontForFontGroup.create(): %s.\n", $stmt->error);
        return false;
    }

    public function readByFontGroupId($fontGroupId) {
        $query = 'SELECT t.id, t.font_name, t.font_group_id, t.font_id, t.specific_size, t.price_change' 
        . ' FROM ' . $this->table . ' t WHERE t.font_group_id = ? LIMIT 0,1';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $fontGroupId);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        if (gettype($result) == 'boolean') return null;
        $this->id = $result['id'];
        $this->fontName = $result['font_name'];
        $this->fontGroupId = $result['font_group_id'];
        $this->selectedFontId = $result['font_id'];
        $this->specificSize = $result['specific_size'];
        $this->priceChange = $result['price_change'];
        return $this;
    }

    public function deleteByFontGroupId($fontGroupId) {
        $query = 'DELETE FROM ' . $this->table . ' WHERE font_group_id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $fontGroupId);
        if ($stmt->execute()) return true;
        printf("Error: %s.\n", $stmt->error);
        return false;
    }

    public function __toString()
    {
        return '[id: ' . $this->id . " fontName: " . $this->fontName 
        . " fontGroupId: " . $this->fontGroupId . " selectedFontId: " . $this->selectedFontId 
        . " specificSize: " . $this->specificSize . " priceChange: " . $this->priceChange . ']';
    }

}

?>