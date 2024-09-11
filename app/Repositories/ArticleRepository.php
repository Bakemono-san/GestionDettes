<?php

namespace App\Repositories;

use App\Contracts\ArticleRepositoryImpl;
use App\Enums\StateEnum;
use App\Http\Requests\UpdateMassArticleRequest;
use App\Models\Article;
use App\Traits\RestResponseTrait;
use Illuminate\Support\Facades\Validator;

class ArticleRepository implements ArticleRepositoryImpl
{

    use RestResponseTrait;
    protected $model;
    public function __construct(Article $article)
    {
        $this->model = $article;
    }

    public function all()
    {
        return $this->model->all();
    }

    public function find($id)
    {
        $article = $this->model->find($id);
        return $article;
    }

    public function create(array $data)
    {
        return $this->model->create($data);
    }

    public function update($id, array $data)
    {
        $article = $this->model->find($id);
        $article->update($data);
        return $article;
    }

    public function delete($id)
    {
        $article = $this->model->find($id);
        $article->delete();
        return true;
    }

    public function findByLibelle($libelle)
    {
        return $this->model->where('libelle', $libelle)->first();
    }

    public function findEtat($etat)
    {
        return $this->model->where('etat', $etat)->get();
    }

    public function getArticles(array $filters = [])
    {
        $query = $this->model->query();

        // Apply query scopes based on filters
        if (isset($filters['disponible'])) {
            $query->disponible($filters['disponible']);
        }

        if (isset($filters['surname'])) {
            $query->filterBySurname($filters['surname']);
        }

        if (isset($filters['surname'])) {
            $query->filterByUsername($filters['username']);
        }

        return $query->get();
    }

    public function updateMass($request)
    {
        if (!$request->has('articles') || empty($request->input('articles'))) {
            return $this->sendResponse([], StateEnum::ECHEC, 'les donnees sont requises', 400);
        }

        $errors = [];
        $reussies = [];

        $articles = $request->input('articles');

        foreach ($articles as $article) {
            $articleData = Article::find($article['id']);

            if (!$articleData) {
                $errors[] =  ['id' => $article['id'], 'message' => 'l\'article n\'existe pas'];
                continue;
            }

            $validationRequest = new UpdateMassArticleRequest();
            $validator = Validator::make($article, $validationRequest->rules());

            if ($validator->fails()) {
                array_push($errors, ['id' => $articleData['id'], 'message' => $validator->errors()->all()]);
                continue;
            } else {
                array_push($reussies, ['id' => $articleData['id'], 'message' => 'article mis à jour avec succès']);
            }

            $validatedData = $validator->validated();
            $validatedData['quantite'] = $articleData->quantite + $validatedData['quantite'];
            $articleData->update($validatedData);
        }

        return ["errors" => $errors, "successes" => $reussies];
    }
}
