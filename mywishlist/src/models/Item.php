<?php

namespace mywishlist\models;

use Illuminate\Database\Eloquent\Model;

class Item extends Model{
    protected $table = 'item';
    protected $primaryKey = 'id';
    public $timestamps = false;
    /*
    //fonction qui fait l'association
    public function liste(){
        return $this->belongsTo('\src\models\Liste', 'item_id');
    }
    */
}