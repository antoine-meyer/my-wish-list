<?php

/**
 * modèle qui représente la table LISTE de la base de données
 */

namespace mywishlist\models;

use Illuminate\Database\Eloquent\Model;
//use \mywishlist\models\Item as Item;

class Liste extends Model{
    protected $table = 'liste';
    protected $primaryKey = 'no';
    public $timestamps = false;
    //fonction qui fait l'association
    //avec les items
    public function items(){
        return $this->hasMany('\mywishlist\models\Item', 'liste_id');
    }
    //avec les commentaires
    public function commentaires(){
        return $this->hasMany('\mywishlist\models\CommentairesListes', 'liste_id');
    }
}