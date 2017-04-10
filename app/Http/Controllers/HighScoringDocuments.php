<?php

namespace App\Http\Controllers;

use App\Services\Search;
use App\Services\StopWords;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HighScoringDocuments extends Controller
{
    public function getDocs(Request $req)
    {
        $queries = array_unique($req->get('query', []));
        if (count($queries) < 1) return JsonResponse::create(['error' => 'no tokens present'], 400);
        $trie = StopWords::getStopWordsTrie();
        $keywords = array_values(array_filter($queries, function($q) use($trie) {
            return $trie->search($q) === null;
        }));
        return JsonResponse::create([
            'keywords' => $keywords,
            'result' => (Search::findTopDocs($keywords, $req->get('k', 10)))
        ]
        );
    }

}
