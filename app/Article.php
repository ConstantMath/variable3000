<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;
use Spatie\MediaLibrary\HasMedia\HasMediaTrait;
use Spatie\MediaLibrary\HasMedia\HasMedia;

class Article extends Model implements HasMedia{

  use HasMediaTrait;
  use \Dimsav\Translatable\Translatable;
  protected $table = 'articles';
  public $translatedAttributes = ['title', 'intro', 'text', 'slug'];
  protected $fillable = ['created_at', 'order', 'parent_id', 'published'];

  // Medialibrary collections define
  public function registerMediaCollections(){
    $this->addMediaCollection('une')->singleFile();
    $this->addMediaCollection('gallery');
  }

  /**
   * Construct : default Locale
   *
   */

  public function __construct(array $attributes = []){
    parent::__construct($attributes);
    // $this->defaultLocale = 'en';
  }


  /**
  * Get all of the tags for the post.
  */
  public function taxonomies(){
    return $this->morphToMany('App\Taxonomy', 'taxonomyable');
  }

  // /**
  //  * Returns the tags (n)
  //  *
  //  */
  //
  // public function taxonomies(){
  //   return $this->belongsToMany('App\Taxonomy')->withTimestamps();
  // }
  //
  //
  // /**
  // * Returns the category (unique)
  // */
  //
  // public function category(){
  //   return $this->belongsToMany('App\Taxonomy')->where('parent_id', 1)->withTimestamps();
  // }
  //
  //
  //
  // /**
  //  * Returns all the tags
  //  *
  //  */
  //
  // public function tags(){
  //   return $this->belongsToMany('App\Taxonomy')->where('parent_id', 2)->withTimestamps();
  // }


  /**
   * Returns the categories for a select
   *
   */

  public function taxonomiesDropdown($parent_id, $appendEmpty=0){
    if($appendEmpty){
      return Taxonomy::where('parent_id', $parent_id)->get()->pluck('name', 'id')->prepend('', '');
    }else{
      return Taxonomy::where('parent_id', $parent_id)->get()->pluck('name', 'id');
    }
  }


  /**
  * Retourne l'id de l'article sur base du slug
  * @param string  $title
  *
  */

  public static function getSlugFromId($id){
    $article = Article::find($id);
    if($article){
      return $article->slug;
    }
  }


  /**
   * Réécrit la date d'update
   * @param string  $value (date)
	 *
   */
   // TODO: Réécrire la date pour toutes les ressources
  public function getUpdatedAtAttribute($value){
    if(!empty($value)){
      Carbon::setLocale(config('app.locale'));
      $date = Carbon::parse($value)->diffForHumans();
    }else{
      $date = "";
    }
    return $date;
  }


  /**
   * Retourne une liste de catégories associées à l'article
   * Nécessaire pour le dropdown select
   *
   */

   public function getCategoriesAttribute() {
    return $this->taxonomies->pluck('id')->all();
 	}

  /**
   * Retourne une liste des tags associées à l'article
   * Nécessaire pour le dropdown select
   *
   */

    public function getTagsAttribute() {
     return $this->taxonomies->pluck('id')->all();
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
    * Create formated date-time
    */

    public function dateTime($date){
      if(!empty($date)){
        return Carbon::createFromFormat('d.m.Y', $date)->format('Y-m-d');
      }else{
        return null;
      }
    }


    /**
     * Concact mdoel + title for related dropdown
     * @param date  $date
     *
     */

     public function getModelTitleAttribute(){
       $data = get_class().', '.$this->id;
       return $data;
     }


     /**
      * Loop through models that has medias related model
      * Define in config/admin.php
      *
      * @return Articles collection
      */

     static function listAll(){
       $media_models = config('admin.media_models');
       $articles = [];
       if($media_models){
         foreach($media_models as $model){
           $model_name = str_plural(str_replace('App\\','', $model));
           $articles[$model_name] = $model::all()->pluck('title', 'model_title')->toArray();
         }
       }
       return $articles;
     }
}
