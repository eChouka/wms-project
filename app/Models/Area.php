<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;
    protected $table='areas';
    protected $fillable = [
        'name',
    ];

    
    
    
    
    
    
    
    public function getRelationMethodName($foreignKey)
    {
        $relationMap = [            'id' => 'Location',
        ];

        return $relationMap[$foreignKey] ?? null;
    }

    public function getRelationMappings()
    {
       return $relationMap = [            'id' => 'Location',
        ];
    }


    public static function readableColumnNames()
    {
        return [
            'name' => 'Area Name',
        ];
    }

    public static function requiredFields()
    {
        return [
            'name',
        ];
    }

     
    
    public function location()
    {
        return $this->belongsTo(Location::class, 'id', 'area_id');
    }

    
    public static function getRelationMappingConfig()
    {
        return [
            'sourceModel' => [
                
            ],
            'targetModel' => [
                
            ],
            'fieldMapping' => [
                
            ],
        ];
    }



    public function getHasManyRelations()
    {
        return [        ];
    }

}