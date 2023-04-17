<?php
namespace app\model;
use core\Model;

class Tag extends Model{
    protected static string $tableName = "tag";
    protected static string $className = "Tag";
    protected static $primaryKey = array("tagID");
    protected static bool $isAutoGenerated = true;

    public int $tagID;
    public string $tagName;
    public int $minAge;
    
}