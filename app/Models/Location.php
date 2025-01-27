<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Location extends Model
{
    use HasFactory;
    protected $table='locations';
    protected $fillable = ['name', 'area_id'];

    public static function readableColumnNames() {
        return [
            'name' => 'Location Name',
            'area_id' => 'Area',
        ];
    }

    public static function requiredFields() {
        return [
            'name',
            'area_id',
        ];
    }
    
    public function areas()
    {
        return $this->hasOne(Area::class, 'area_id', 'id');
    }

    public function getRelationMethodName($foreignKey)
    {
        $relationMap = [            'area_id' => 'Area',
        ];

        return $relationMap[$foreignKey] ?? null;
    }

    public function getHasManyRelations()
    {
        return [
        ];
    }

    public static function getRelationMappingConfig()
    {
        return [
        ];
    }


public static $search_fields = ['name', 'area_id'];

}