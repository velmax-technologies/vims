<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'group',
        'locked',
        'type',
        'value',
    ];

     public function getValueAttribute($value)
    {
        if($this->type === 'json') {
            return json_decode($value, true); 
        }
        if($this->type === 'boolean') {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }
        if($this->type === 'integer') {
            return (int) $value;
        }
        if($this->type === 'float') {
            return (float) $value;
        }
        if($this->type === 'string') {
            return (string) $value;
        }
       
        return $value; // Return original value if no specific cast is needed
    }
}
