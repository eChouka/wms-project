<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    use HasFactory;
    protected $table='suppliers';
    protected $fillable = ['name', 'location'];

    public function getRelationMethodName($foreignKey)
    {
        $relationMap = [
        ];

        return $relationMap[$foreignKey] ?? null;
    }

    public function getHasManyRelations()
    {
        return [
        ];
    }

     public static function readableColumnNames() {
        return [
            'name' => 'Name',
            'location' => 'Location',
        ];
    }

    public static function requiredFields() {
        return [
            'name',
            'location',
        ];
    }


public static $search_fields = [];

}