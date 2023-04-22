<?php
namespace app\model;
use core\Model;

class Food extends Model{
    protected static string $tableName = "food";
    protected static string $className = "Food";
    protected static $primaryKey = array("foodID");
    protected static bool $isAutoGenerated = true;

    public int $foodID;
    public string $foodImage;
    public string $foodName;
    public long $foodPrice;
    public string $foodDescription;
    public ?int $discountID;
    public bool $isDeleted;
}