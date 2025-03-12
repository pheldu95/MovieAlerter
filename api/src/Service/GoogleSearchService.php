<?php
// src/Service/GoogleSearchService.php
namespace App\Service;

use Symfony\Component\DomCrawler\Crawler;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class GoogleSearchService
{
    private HttpClientInterface $httpClient;
    private string $userAgent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/96.0.4664.110 Safari/537.36';

    public function __construct(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    public function search(string $query, int $limit = 10): array
    {
        // URL encode the query
        $encodedQuery = urlencode($query);
        $url = "https://www.google.com/search?q={$encodedQuery}&num={$limit}";

        // Send the request with a browser-like user agent
        $response = $this->httpClient->request('GET', $url, [
            'headers' => [
                'User-Agent' => $this->userAgent,
                'Accept-Language' => 'en-US,en;q=0.9',
            ],
        ]);

        // Get the content
        $html = $response->getContent();
dd($html);
        // Parse with DomCrawler
        $crawler = new Crawler($html);

        // Extract search results
        $results = [];

        // Google's HTML structure might change over time - adjust selectors as needed
        $crawler->filter('div.g')->each(function (Crawler $node) use (&$results) {
            // Extract title
            $titleNode = $node->filter('h3');
            if ($titleNode->count() === 0) {
                return;
            }
            $title = $titleNode->text();

            // Extract URL
            $urlNode = $node->filter('a');
            if ($urlNode->count() === 0) {
                return;
            }
            $url = $urlNode->attr('href');

            // Clean URL (remove Google tracking)
            if (strpos($url, '/url?q=') === 0) {
                $url = substr($url, 7);
                $url = explode('&', $url)[0];
            }

            // Extract snippet
            $snippetNode = $node->filter('div.VwiC3b');
            $snippet = $snippetNode->count() > 0 ? $snippetNode->text() : null;

            // Store as array
            $results[] = [
                'title' => $title,
                'url' => $url,
                'snippet' => $snippet,
            ];
        });

        return $results;
    }
}