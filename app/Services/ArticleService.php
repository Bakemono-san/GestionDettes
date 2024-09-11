<?php

namespace App\Services;

use App\Contracts\ArticleRepositoryImpl;
use App\Contracts\ArticleServiceInt;

class ArticleService implements ArticleServiceInt{
    private $articleRepository;
    public function __construct(ArticleRepositoryImpl $articleRepository){
        $this->articleRepository = $articleRepository;
    }

    public function all(){
        return $this->articleRepository->all();
    }

    public function find($id){
        return $this->articleRepository->find($id);
    }

    public function create(array $data){
        return $this->articleRepository->create($data);
    }

    public function update($id, array $data){
        return $this->articleRepository->update($id, $data);
    }

    public function delete($id){
        return $this->articleRepository->delete($id);
    }

    public function findByLibelle($libelle){
        return $this->articleRepository->findByLibelle($libelle);
    }

    public function findEtat($etat){
        return $this->articleRepository->findEtat($etat);
    }

    public function getArticles(array $filters = []){
        return $this->articleRepository->getArticles($filters);
    }

    public function updateMass($request){
        return $this->articleRepository->updateMass($request);
    }
}