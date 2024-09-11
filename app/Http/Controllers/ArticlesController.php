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

/**
 * @OA\Info(
 *     title="Article API",
 *     version="1.0.0",
 *     description="API for managing articles."
 * )
 *
 * @OA\Tag(
 *     name="Articles",
 *     description="Operations related to articles"
 * )
 *
 * @OA\PathItem(
 *     path="/api/articles"
 * )
 */
class ArticlesController extends Controller
{
    use RestResponseTrait;

    private $articleService;
    public function __construct(ArticleServiceInt $articleService)
    {
        $this->articleService = $articleService;
        // $this->authorizeResource(Article::class, 'article');
    }

    /**
     * Display a listing of the resource.
     */

    /**
     * @OA\Get(
     *     path="/api/articles",
     *     tags={"Articles"},
     *     summary="Get list of articles",
     *     @OA\Parameter(
     *         name="surname",
     *         in="query",
     *         description="Filter by surname",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="username",
     *         in="query",
     *         description="Filter by username",
     *         required=false,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="disponible",
     *         in="query",
     *         description="Filter by availability",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of articles",
     *         @OA\JsonContent(ref="#/components/schemas/ArticleCollection")
     *     )
     * )
     */
    public function index(Request $request)
    {

        $filters = $request->only(['surname', 'username', 'disponible']);

        $articles = $this->articleService->getArticles($filters);

        $message = $articles->count() . ' article(s) trouvé(s)';
        $data = $articles->count() > 0 ? new ArticleCollection($articles) : [];

        return compact('data', 'message');
    }

    /**
     * Store a newly created resource in storage.
     */
    /**
     * @OA\Post(
     *     path="/api/articles",
     *     tags={"Articles"},
     *     summary="Create a new article",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/StoreArticleRequest")
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Article created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Article")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error"
     *     )
     * )
     */
    public function store(StoreArticleRequest $request, User $user)
    {

        $data = $request->validated();
        $article = $this->articleService->create($data);
        return compact('article');
    }

    /**
     * Display the specified resource.
     */
    /**
     * @OA\Get(
     *     path="/api/articles/{id}",
     *     tags={"Articles"},
     *     summary="Get details of a specific article",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the article to retrieve",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Article details",
     *         @OA\JsonContent(ref="#/components/schemas/Article")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Article not found"
     *     )
     * )
     */
    public function show($id)
    {
        $article = $this->articleService->find($id);

        return compact('article');
    }

    public function get(Request $request)
    {
        $article = $this->articleService->findByLibelle($request->input('libelle'));
        return compact('article');
    }

    /**
     * Update the specified resource in storage.
     */
    /**
     * @OA\Post(
     *     path="/api/articles/{id}",
     *     tags={"Articles"},
     *     summary="Update an article",
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="ID of the article to update",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(ref="#/components/schemas/UpdateArticleRequest")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Article updated successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Article")
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Validation error"
     *     )
     * )
     */
    public function update(UpdateArticleRequest $request, $id, User $user)
    {

        if (empty($request->all())) {
            return $this->sendResponse([], StateEnum::ECHEC, 'pas de donnee fournit', 400);
        }

        $article = Article::find($id);

        if (!$article) {
            return $this->sendResponse([], StateEnum::ECHEC, 404);
        }


        $data = $request->validated();
        if ($request->has('quantite')) {
            $request["quantite"] = $article->quantite + $request->quantite;
        }
        $article->update($data);
        return $this->sendResponse(new ArticleCollection($article), StateEnum::SUCCESS, 'article updated successfully', 200);
    }

    /**
 * @OA\Post(
 *     path="/api/articles/mass-update",
 *     tags={"Articles"},
 *     summary="Mass update articles",
 *     @OA\RequestBody(
 *         required=true,
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 type="object",
 *                 required={"id", "quantite"},
 *                 @OA\Property(
 *                     property="id",
 *                     type="integer",
 *                     description="ID of the article to update",
 *                     example=1
 *                 ),
 *                 @OA\Property(
 *                     property="quantite",
 *                     type="integer",
 *                     description="New quantity of the article",
 *                     example=10
 *                 )
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=200,
 *         description="Articles updated successfully",
 *         @OA\JsonContent(
 *             type="array",
 *             @OA\Items(
 *                 ref="#/components/schemas/Article"
 *             )
 *         )
 *     ),
 *     @OA\Response(
 *         response=400,
 *         description="Validation error",
 *         @OA\JsonContent(
 *             @OA\Property(
 *                 property="error",
 *                 type="string",
 *                 description="Error message"
 *             )
 *         )
 *     )
 * )
 */
    public function massUpdate(Request $request)
    {

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
