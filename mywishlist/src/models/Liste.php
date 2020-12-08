<?php

namespace mywishlist\models;

use Illuminate\Database\Eloquent\Model;

class Liste extends Model{
    protected $table = 'liste';
    protected $primaryKey = 'no';
    public $timestamps = false;
    //fonction qui fait l'association
    public function items(){
        return $this->hasMany('\src\models\Item', 'liste_id');
    }
}