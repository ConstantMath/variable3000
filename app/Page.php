<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Cviebrock\EloquentSluggable\Sluggable;

class Page extends Model{
  protected $table = 'pages';
  use \Dimsav\Translatable\Translatable;
  public $translatedAttributes = ['title', 'intro', 'text', 'slug'];
  protected $fillable = ['created_at', 'order', 'published', 'parent_id'];


  /**
   * Get children articles
   *
   */

  public function children(){
    return $this->hasMany('App\Page', 'parent_id')
                ->orderBy('order', 'asc');
  }


  /**
   * GET: Formate le champs 'created_at'
   * @param date  $date
   *
   */

  public function getCreatedAtAttribute($date){
    if(empty($date)){
      $date = Carbon::now('Europe/Paris');
    }
    $date = Carbon::createFromFormat('Y-m-d H:i:s', $date)->format('d.m.Y');
    return $date;
  }


  /**
   * Set default parent_id
   *
   */

  public function setParentIdAttribute($parent_id){
    if (empty($parent_id)){
      $this->attributes['parent_id'] = 0;
    }
  }
}
