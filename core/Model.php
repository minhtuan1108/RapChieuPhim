<?php
namespace core;

use app\utils\Database;
use PDO;

abstract class Model implements ICurdData {
    // Property must re-define for child class if it needs
    protected static string $tableName;
    protected static string $className;
    protected static string $namespace = "app\model\\";
    protected static string $primaryKey = "id";
    protected static bool $isAutoGenerated = true;

    // Return object $className
    public static function find($id)
    {
        $conn = Database::getConnection();
        $sql = "SELECT * FROM " .static::$tableName. " WHERE ". static::$primaryKey ." = :id";
        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, self::getClassName());
        $stmt->execute(["id"=>$id]);
        return $stmt->fetch();
    }

    // Return array if it success else return bool
    public static function findAll(): array|bool
    {
        $conn = Database::getConnection();
        $sql = "SELECT * FROM " .static::$tableName;
        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, self::getClassName());
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Return index > 0 if insert successfully and 0 for primary key's type is string
    public static function save($object): int
    {
        $conn = Database::getConnection();
        $arr = self::reWritePrimaryToNULL(get_object_vars($object));
        $additionSQL = "";
        foreach ($arr as $key => $val){
            $additionSQL .= ":$key,";
        }
        $additionSQL = rtrim($additionSQL, ",");
        $sql = "INSERT INTO " .static::$tableName. " VALUES ($additionSQL)";
        $stmt = $conn->prepare($sql);
        if ($stmt->execute($arr) == true){
            return $conn->lastInsertId();
        }
        return -1;
    }

    // Return true if success else false
    public static function update($id, $object): bool
    {
        $conn = Database::getConnection();
        $arr = self::removePrimaryKey(get_object_vars($object));
        $additionSQL = "";
        foreach ($arr as $key => $val){
            $additionSQL .= " $key = :$key,";
        }
        $additionSQL = rtrim($additionSQL, ",");
        $sql = "UPDATE " .static::$tableName. " SET $additionSQL WHERE ".static::$primaryKey." = :id";
        $stmt = $conn->prepare($sql);
        $arr["id"] = $id;
        return $stmt->execute($arr);
    }

    public static function delete($id): bool
    {
        $conn = Database::getConnection();
        $sql = "DELETE FROM " .static::$tableName. " WHERE ". static::$primaryKey ." = :id";
        $stmt = $conn->prepare($sql);
        return $stmt->execute(["id"=>$id]);
    }

    public static function where(string $whereClause, array $parameters = []): bool|array
    {
        $conn = Database::getConnection();
        if ($whereClause == "")
            return self::findAll();
        $sql = "SELECT * FROM " .static::$tableName . " WHERE " .$whereClause;
        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, self::getClassName());
        $stmt->execute($parameters);
        return $stmt->fetchAll();
    }

    protected static function reWritePrimaryToNULL($objectArr){
        foreach ($objectArr as $key => $value){
            if ($key == static::$primaryKey && static::$isAutoGenerated == true){
                $objectArr[$key] = NULL;
                return $objectArr;
            }
        }
        return $objectArr;
    }

    protected static function removePrimaryKey($objectArr){
        foreach ($objectArr as $key => $value){
            if ($key == static::$primaryKey){
                unset($objectArr[$key]);
                return $objectArr;
            }
        }
        return null;
    }

    protected static function getClassName(): string
    {
        return static::$namespace.static::$className;
    }

    public function hasList($class): bool|array
    {
        $conn = Database::getConnection();
        $primaryKey = static::$primaryKey;
        $whereClause = $primaryKey ." = :" .$primaryKey;
        $sql = "SELECT * FROM " .$class::$tableName . " WHERE " .$whereClause;
        $stmt = $conn->prepare($sql);
        $stmt->setFetchMode(PDO::FETCH_CLASS, $class::getClassName());
        $stmt->execute([$primaryKey => $this->$primaryKey]);
        return $stmt->fetchAll();
    }

    public function belongTo($class){
        $primaryKey = static::$primaryKey;
        return $class::find($this->$primaryKey);
    }
}