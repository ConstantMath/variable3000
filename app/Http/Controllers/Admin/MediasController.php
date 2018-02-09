<?php

namespace App\Http\Controllers\Admin;
use Validator;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Media;

class MediasController extends AdminController {

  public function __construct(){
    $this->middleware('auth');
  }


  /**
   * Return article's medias
   *
   * @param  string  $media_type
   * @param  string  $mediatable_type
   * @param  int  $article_id
   * @return \Json\Response
   */

  public function index($media_type, $mediatable_type, $article_id){
    $class = $this->getClass($mediatable_type);
    $article = $class::findOrFail($article_id);
    $medias = $article->medias->where('type', $media_type);
    return response()->json([
      'success' => true,
      'medias' => $medias,
    ]);
  }



  /**
   * Store media related to an article
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  string  $mediatable_type
   * @param  int  $article_id
   * @return JSON\Response
   */

  public function store(Request $request, $mediatable_type, $article_id){
    $class = $this->getClass($mediatable_type);
    if(!empty($article_id) && $article_id != 'null'){
      $article = $class::findOrFail($article_id);
    }
    // Champs requests
    $file        = $request->file('image');
    if($file){
      list($width, $height) = getimagesize($file);
      // Upload
      $media_file = Media::uploadMediaFile($file);
      // Media store
      $media = New media;
      $media->name       = $media_file['name'];
      $media->alt        = $media_file['orig_name'];
      $media->ext        = $file->getClientOriginalExtension();
      $media->type       = $request->input('type');
      $media->width      = $width;
      $media->height     = $height;
      /// If Media unique (not gallery) > Delete current before saving,
      if(($media->type == 'une' or $media->type == 'home_media') && isset($article)){
        $current_media = $article->medias->where('type', $media->type)->first();
        if(!empty($current_media)):
          Media::deleteMediaFile($current_media->id);
        endif;
      }
      $media->save();
      // Link media to article
      if(isset($article)){
        // retourne le dernier media
        $next_media_order = $article->lastMediaId($media->type);
        $next_media_order += 1;
        // Article -> media
        $media->order = $next_media_order;
        $article->medias()->save($media);
      }
      return response()->json([
        'success'     => true,
        'alt'         => $media->alt,
        'name'        => $media->name,
        'ext'         => $media->ext,
        'type'        => $media->type,
        'mediatable_type'  => 'articles',
        'article_id'  => $article_id,
        'id'          => $media->id,
        'description' => $media->description,
      ]);
    }
  }


    /**
     * Supprime un media de l'article courant
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    public function deleteMedia(Request $request, $id){
      // $article = Article::findOrFail($id);
      $media_type = $request->media_type;
      $media_id   = $request->media_id;
      Media::deleteMediaFile($media_id);
      return response()->json([
        'success'    => true,
        'media_type' => $media_type,
        'media_id'   => $media_id,
      ]);
    }


    /**
     * Réordonne les médias liés à l'article
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

     public function reorderMedia(Request $request, $id){
       $article = Article::findOrFail($id);
       $media_id  = $request->mediaId;
       $media_type  = $request->mediaType;
       $new_order = $request->newOrder;
       $v = 1;
       $medias = $article->medias->where('type', $media_type);
       // loop dans les médias liés
       foreach ($medias as $media) {
         if($v == $new_order){$v++;}
         $media = Media::findOrFail($media->id);
         if($media->id == $media_id){
           $media->order = $new_order;
         }else{
           $media->order = $v;
           $v++;
         }
         $media->update();
       }
       return response()->json([
         'status'        => 'success',
       ]);
     }

  /**
   * Upload de fichier simple (pour les champs texte)
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */

   public function fileUpload(Request $request){
     // Validator conditions
     $validator = Validator::make($request->all(), [
       'file' => 'required|mimes:jpeg,jpg,png,gif,pdf,mp4',
     ]);
     // Validator test
     if ($validator->fails()) {
       return response()->json([
         'status' => 'error',
         'error'    =>  'Error while uploading file, please check file format and size.'
       ]);
     }else{
       $file = $request->file;
       $file_name = $file->getClientOriginalName();
       $orig_name = pathinfo($file_name, PATHINFO_FILENAME);
       $extension = $file->getClientOriginalExtension();
       $name = time() .'-'. str_slug($orig_name).'.'.$extension;
       $mediapath = public_path().'/medias/';
       // Store the file
       //$path = $file->storeAs('public', $name);
       if($extension == 'pdf'){
         $file_url = '/medias/'.$name;
       }else{
         $file_url = '/imagecache/large/'.$name;
       }
       $file->move('medias', $name);

       return response()->json([
         'status'     => 'success',
         'filename'   => $file_url,
         'name'       => $file_name,
         'extension' =>  $extension
       ]);
     }
   }

  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */

  public function update(Request $request){
    $id = $request->media_id;
    $media = Media::findOrFail($id);
    $file = $request->file('background_image_file');
    if($file){
      // Upload
      $background_image = Media::uploadMediaFile($file);
      $media->update(['background_image' => $background_image['name']]);
    }
    $media->update($request->all());
    return response()->json([
      'status'                  => 'success',
      'media_id'                => $media->id,
      'media_alt'               => $media->alt,
      'media_description'       => $media->description,
      'media_type'              => $media->type,
    ]);
  }

  /**
   * GEt all medias from id array
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */

  public function getFromArray(Request $request){
    $medias_array = explode( ',', $request->medias[0]);
    $medias = Media::whereIn('id', $medias_array)->get();
    return response()->json([
      'success' => true,
      'medias' => $medias,
      'medias_array' => $medias_array,
    ]);
  }

}
