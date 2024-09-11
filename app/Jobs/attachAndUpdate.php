<?php

namespace App\Jobs;

use App\Facades\ArticleRepositoryFacade;
use App\Models\Article;
use App\Models\Dette;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class attachAndUpdate implements ShouldQueue
{
    use Queueable;

    protected $articles;
    protected $dette;
    /**
     * Create a new job instance.
     */
    public function __construct($article,Dette $dette)
    {
        $this->articles = $article;
        $this->dette = $dette;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->articles as $article) {
            $this->dette->articles()->attach($article['id'], [
                'quantite' => $article['quantite'],
                'prixVente' => $article['prixVente'],
            ]);

            // Update articles quantite after attach
            $articlefound = ArticleRepositoryFacade::find($article['id']);
            $articlefound->update(['quantite' => $articlefound['quantite'] - $article['quantite']]);
        }
    }
}
