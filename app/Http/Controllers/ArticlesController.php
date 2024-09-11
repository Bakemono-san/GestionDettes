<?php

namespace App\Http\Controllers;

use App\Contracts\ArticleServiceInt;
use App\Enums\StateEnum;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UpdateArticleRequest;
use App\Http\Requests\UpdateMassArticleRequest;
use App\Http\Resources\ArticleCollection;
use App\Models\Article;
use App\Models\Role;
use App\Models\User;
use App\Traits\RestResponseTrait;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class ArticlesController extends Controller
{
    use RestResponseTrait;

    private $articleService;
    public function __construct(ArticleServiceInt $articleService) {
        $this->articleService = $articleService;
        // $this->authorizeResource(Article::class, 'article');
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        
        $filters = $request->only(['surname','username','disponible']);
        
        $articles = $this->articleService->getArticles($filters);

        $message = $articles->count().' article(s) trouvé(s)';
        $data = $articles->count() > 0 ? new ArticleCollection($articles) : [];
        
        return compact('data','message');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreArticleRequest $request,User $user)
    {

        $data = $request->validated();
        $article = $this->articleService->create($data);
        return compact('article');
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $article = $this->articleService->find($id);

        return compact('article');
    }

    public function get(Request $request){
        $article = $this->articleService->findByLibelle($request->input('libelle'));
        return compact('article');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateArticleRequest $request,$id,User $user)
    {

        if(empty($request->all())){
            return $this->sendResponse([], StateEnum::ECHEC, 'pas de donnee fournit',400);
        }

        $article = Article::find($id);

        if (!$article) {
            return $this->sendResponse([], StateEnum::ECHEC, 404);
        }

        
        $data = $request->validated();
        if($request->has('quantite')){
            $request["quantite"] = $article->quantite + $request->quantite;
        }
        $article->update($data);
        return $this->sendResponse(new ArticleCollection($article), StateEnum::SUCCESS, 'article updated successfully', 200);
    }

    public function massUpdate(Request $request){

        $articles = $this->articleService->updateMass($request);
        return compact('articles');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id, User $user)
    {
        $this->authorize('delete', $user);
        $article = Article::find($id);

        if (!$article) {
            return $this->sendResponse([], StateEnum::ECHEC, 404);
        }

        $article->delete();
        return $this->sendResponse([], StateEnum::SUCCESS, 200);
    }

    // public function get(Request $request){
        
    // }
}
