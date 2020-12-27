<?php

/**
 * modèle qui représente la table COMMENTAIRESLISTES de la base de données
 */

namespace mywishlist\models;

use Illuminate\Database\Eloquent\Model;

class CommentairesListes extends Model{
    protected $table = 'commentairesListes';
    protected $primaryKey = 'id';
    public $timestamps = false;
/*
    //fonction qui fait l'association
    public function liste(){
        return $this->belongsTo('\src\models\Liste', 'item_id');
    }
    */
    
}