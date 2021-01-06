<?php

/**
 * modèle qui repré
 */

namespace mywishlist\models;

use Illuminate\Database\Eloquent\Model;

class Compte extends Model{
    protected $table = 'compte';
    protected $primaryKey = 'id';
    public $timestamps = false;
    //fonction qui fait l'association
    //avec les listes
    public function listes(){
        return $this->hasMany('\mywishlist\models\Liste', 'user_id');
    }
}