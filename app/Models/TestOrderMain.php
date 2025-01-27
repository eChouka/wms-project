<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TestOrderMain extends Model
{
    use HasFactory;

    protected $table = 'testordermains';

    protected $fillable = ['field_1', 'field_2', 'field_3'];

    public static function requiredFields() {
        return [
            'field_1',
            'field_2',
            'field_3',
        ];
    }

    public static function readableColumnNames() {
        return [
            'field_1' => 'Field 1',
            'field_2' => 'Field 2',
            'field_3' => 'Field 3',
        ];
    }


    
    
    
    
    
    
    
    
    public function Pages()
    {
        return $this->hasOne(Page::class, 'field_1', 'id');
    }











    
    
    
    
    
    
    public function Orders()
    {
        return $this->hasOne(SalesOrder::class, 'field_2', 'id');
    }







}