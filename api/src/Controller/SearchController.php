<?php
// src/Controller/SearchController.php
namespace App\Controller;

use App\Service\GoogleSearchService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Attribute\AsController;

#[AsController]
class SearchController extends AbstractController
{
//    #[Route('/search', name: 'api_search', methods: ['GET'])]
    public function __invoke(Request $request, GoogleSearchService $searchService): JsonResponse
    {
        $query = $request->query->get('q');
        $limit = $request->query->getInt('limit', 10);

        if (!$query) {
            return $this->json(['error' => 'Query parameter "q" is required'], 400);
        }

        $results = $searchService->search($query, $limit);

        return $this->json([
            'query' => $query,
            'results' => $results,
            'count' => count($results)
        ]);
    }
}