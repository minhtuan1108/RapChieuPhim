<?php
namespace app\model;
use core\Model;

class Booking extends Model{
    
    protected static string $tableName = "booking";
    protected static string $className = "Booking";
    protected static $primaryKey = array("bookingID");
    protected static bool $isAutoGenerated = true;

    public int $bookingID;
    public string $bookingName;
    public string $bookingEmail;
    public string $bookingTime;
    public ?int $userID;
}