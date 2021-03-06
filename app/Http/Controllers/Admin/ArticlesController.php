<?php

namespace App\Http\Controllers\Admin;
use Validator;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Article;
use App\Media;
use App\Taxonomy;
use DB;
use Carbon\Carbon;
use Lang;
use App\Http\Requests\Admin\ArticleRequest;
use DataTables;
use Illuminate\Support\Facades\Input;


class ArticlesController extends AdminController
{

  public function __construct(){
    $this->table_type = 'articles';
    $parent_articles = Article::where('parent_id', 0)->get();
    Lang::setLocale(config('app.locale'));
    $this->middleware(['auth', 'permissions'])->except('index');
    // Define an array of all related taxonomie's parents
    $this->taxonomies = [1,2];
    parent::__construct();
  }

  /**
   * List all articles by parent
   *
   * @param  string  $parent_id
   * @return \Illuminate\Http\Response
   */

  public function index($parent_id = 0){
    $articles = Article::where('id', $parent_id)
                  ->orderBy('order', 'asc')
                  ->orderBy('created_at', 'desc')
                  ->get();
    $data = array(
      'page_class' => 'index',
      'page_title' => 'Articles',
      'page_id'    => 'index-articles',
      'table_type' => $this->table_type,
    );
    return view('admin/templates/articles-index', compact('articles', 'data'));
  }


  /**
   * Get articles for datatables (ajax)
   *
   * @return \Illuminate\Http\Response
   */

  public function getDataTable(){
    return \DataTables::of(Article::withTranslation()
                        ->get())
                        ->addColumn('action', function ($article) {
                          return '<a href="' . route('admin.articles.edit', $article->id) . '" class="link">' . __('admin.edit') . '</a>';
                        })
                        ->make(true);
  }


  /**
   * Show the form for editing the specified resource.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */

  public function edit($id){
    $article = Article::findOrFail($id);
    $data = array(
      'page_class' => 'article',
      'page_title' => 'Article edit',
      'page_id'    => 'index-',
      'table_type' => $this->table_type,
    );
    // dd($article->categories);
  	return view('admin/templates/article-edit',  compact('article', 'data'));
  }


  /**
   * Show the form for creating a new resource.
   *
   * @param  string  $parent_slug
   * @return \Illuminate\Http\Response
   */

  public function create($parent_id = 0){
    $data = array(
      'page_class' => 'article create',
      'page_title' => 'Article create',
      'page_id'    => 'index-'.$parent_id,
      'table_type' => $this->table_type,
    );
    $article = new Article;
    $article->parent = new Article;
    return view('admin.templates.article-edit', compact('article', 'data'));
  }


  /**
   * Update the specified resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   */

  public function update(Article $article, ArticleRequest $request){
    // Save article
    return $this->saveObject($article, $request, $this->taxonomies);
  }


  /**
   * Store a newly created resource in storage.
   *
   * @param  \Illuminate\Http\Request  $request
   * @return \Illuminate\Http\Response
   */

  public function store(ArticleRequest $request){
    // Create article
    return $this->createObject(Article::class, $request, 'redirect', $this->taxonomies);
  }


  /**
   * Remove the specified resource from storage.
   *
   * @param  int  $id
   * @return \Illuminate\Http\Response
   */

  public function destroy(Article $article){
    return $this->destroyObject($article);
  }


  /**
   * Reorder the articles relative to parent
   *
   * @param  \Illuminate\Http\Request  $request
   * @param  int  $id
   * @return Json response
   */

   public function reorder(Request $request){
     return $this->orderObject(Article::class, $request);
   }
}
