<?php

include_once 'BaseModel.php';

class FontGroup extends BaseModel {

    protected $table = "font_group";

    public $id;
    public $fontGroupName;
    public $selectedFontList = array(); // array of FontForFontGroup.php model objects

    public function create()
    {
        $query = 'INSERT INTO ' . $this->table . ' SET font_group_name = :fontGroupName';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':fontGroupName', $this->fontGroupName);
        if (!$stmt->execute()) {
            printf("error occurred in FontGroup.create(): %s.\n", $stmt->error);
            return false;
        }
        $this->id = $this->getLastInsertedId();
        foreach($this->selectedFontList as $fontForFontGroup) {
            $fontForFontGroup->fontGroupId = $this->id;
            if (!$fontForFontGroup->create()) {
                printf("failed to create fontForFontGroup: %s in FontGroup.create()\n", strval($fontForFontGroup));
            }
        }
        return true;
    }

    public function readAll() 
    {
        $fontGroups = array();
        $query = 'SELECT fg.id as fg_id, fg.font_group_name,'
        .' ffg.id as ffg_id, ffg.font_name, ffg.font_id, ffg.specific_size, ffg.price_change' 
        . ' FROM ' . $this->table . ' fg' 
        . ' LEFT JOIN font_for_font_group ffg on fg.id = ffg.font_group_id';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $result = $stmt;
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $found = false;
            for($i = 0; $i < count($fontGroups); $i++) {
                if ($fontGroups[$i]->id == $row['fg_id']) {
                    $found = true;
                    $fontGroups[$i] = $this->add_font_for_group($fontGroups[$i], $row);
                } 
            }
            if (!$found) {
                $fontGroups[$i] = new FontGroup();
                $fontGroups[$i]->id = $row['fg_id'];
                $fontGroups[$i]->fontGroupName = $row['font_group_name'];
                $fontGroups[$i] = $this->add_font_for_group($fontGroups[$i], $row);
            }
        }
        return  $fontGroups;
    }

    public function read($id) 
    {
        $query = 'SELECT fg.id as fg_id, fg.font_group_name,'
        .' ffg.id as ffg_id, ffg.font_name, ffg.font_id, ffg.specific_size, ffg.price_change' 
        . ' FROM ' . $this->table . ' fg' 
        . ' LEFT JOIN font_for_font_group ffg on fg.id = ffg.font_group_id'
        . ' WHERE fg.id = ?';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        $result = $stmt;
        $found = false;
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $found = true;
            $this->id = $row['fg_id'];
            $this->fontGroupName = $row['font_group_name'];
            $this->add_font_for_group($this, $row);
        }
        return $found ? $this : null;
    }

    public function update() 
    {
        $query = 'UPDATE ' . $this->table . ' SET font_group_name = :fontGroupName'
        . ' WHERE id = :fontGroupId';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':fontGroupName', $this->fontGroupName);
        $stmt->bindParam(':fontGroupId', $this->id);
        if (!$stmt->execute()) {
            printf("error occurred in FontGroup.update(): %s.\n", $stmt->error);
            return false;
        }
        $fontForFontGroup = new FontForFontGroup($this->conn);
        if (!$fontForFontGroup->deleteByFontGroupId($this->id)) {
            printf("failed to delete fontForFontGroups in FontGroup.update()\n");
            return false;
        }
        foreach($this->selectedFontList as $fontForFontGroup) {
            $fontForFontGroup->fontGroupId = $this->id;
            if (!$fontForFontGroup->create()) {
                printf("failed to create fontForFontGroup: %s in FontGroup.update()\n", strval($fontForFontGroup));
                return false;
            }
            $fontForFontGroup->id = $fontForFontGroup->getLastInsertedId();
        }
        return true;
    }

    private function add_font_for_group($fontGroup, $row) {
        $fontForFontGroup = new FontForFontGroup();
        $fontForFontGroup->id = $row['ffg_id'];
        $fontForFontGroup->fontGroupId = $row['fg_id'];
        $fontForFontGroup->fontName = $row['font_name'];
        $fontForFontGroup->selectedFontId = $row['font_id'];
        $fontForFontGroup->specificSize = $row['specific_size'];
        $fontForFontGroup->priceChange = $row['price_change'];
        array_push($fontGroup->selectedFontList, $fontForFontGroup);
        return $fontGroup;
    }

}

?>