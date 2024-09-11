<?php
// app/Contracts/AuthenticationServiceInterface.php
namespace App\Contracts;

interface ArticleServiceInt
{
    public function __construct(ArticleRepositoryImpl $articleRepository);

    public function all();
    public function find($id);
    public function create(array $data);
    public function update($id, array $data);
    public function delete($id);
    public function findByLibelle($libelle);
    public function findEtat($etat);
    public function getArticles(array $filters = []);
    public function updateMass($request);
}
