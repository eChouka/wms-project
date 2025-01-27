<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestModel2 extends Model
{
    use HasFactory;

    protected $table = 'testmodel2s';

    protected $fillable = ['field_1', 'field_2', 'field_3'];

    public static function requiredFields()
    {
        return ['field_1', 'field_2', 'field_3'];
    }

    public static function readableColumnNames()
    {
        return ['field_1' => 'Field 1', 'field_2' => 'Field 2', 'field_3' => 'Field 3'];
    }


    
    
    
    
    public function getRelationMethodName($foreignKey)
    {
        $relationMap = [            'field_1' => 'Action',
        ];

        return $relationMap[$foreignKey] ?? null;
    }

    public function getRelationMappings()
    {
       return $relationMap = [            'field_1' => 'Action',
        ];
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







    



    



    



    



    public function users()
    {
        return $this->hasOne(User::class, 'field_1', 'id');
    }



    public function getHasManyRelations()
    {
        return [        ];
    }



public static $search_fields = ['field_1', 'field_2'];

}