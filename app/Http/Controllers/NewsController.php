<?php

namespace App\Http\Controllers;

use App\Http\Requests\NewsRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class NewsController extends Controller
{
    // public function __construct()
    // {
    //     $this->middleware('jwt');
    // }
    public function getFromNewsAPI(Request $request)
    {
        try {
            $news_url = env('NEWS_API_EVERYTHING');
            $API_KEY = env('NEWS_API_KEY');
            $keyword = $request->keyword ? $request->keyword : "tesla";
            $source = $request->source ? $request->input('source') : "techcrunch";
            $language = $request->language ? $request->language : "en";
            $sort_by = $request->sort_by ? $request->sort_by : "publishedAt";

            $response = Http::get($news_url, [
                'apiKey' => $API_KEY,
                'q' => $keyword,
                'language' => $language,
                'source' => $source,
                'sortBy' => $sort_by
            ]);

            $newsData = $response->json();
            return $newsData;
        } catch (\Exception $e) {
            return sendError('Error.', $e->getMessage(), 500);
        }
    }

    public function getFromTheGuardian(Request $request)
    {
        try {
            $news_url = env('THE_GUARDIAN');
            $API_KEY = env('GUARDIAN_API_KEY');

            $tag = $request->tag ? $request->tag : "politics/politics";
            $keyword = $request->keyword ? $request->keyword : "debate";

            $response = Http::get($news_url, [
                'api-key' => $API_KEY,
                'q' => $keyword,
                'tag' => $tag
            ]);

            $newsData = $response->json();
            return $newsData;
        } catch (\Exception $e) {
            return sendError('Error.', $e->getMessage(), 500);
        }
    }

    public function getFromNewYorkTimes(Request $request)
    {
        try {
            $news_url = env('NEW_YORK_TIMES');
            $API_KEY = env('NY_API_KEY');

            $tag = $request->tag ? $request->tag : "politics/politics";
            $keyword = $request->keyword ? $request->keyword : "election";
            $fq = $request->fq ? $request->fq : "The New York Times";

            $response = Http::get($news_url, [
                'q' => $keyword,
                'tag' => $tag,
                'fq' => $fq,
                'api-key' => $API_KEY

            ]);

            $newsData = $response->json();
            return $newsData;
        } catch (\Exception $e) {
            return sendError('Error.', $e->getMessage(), 500);
        }
    }

    public function getFeeds()
    {
        try {
            $ny_url = env('NEW_YORK_TIMES');
            $NY_API_KEY = env('NY_API_KEY');
            $news_url = env('NEWS_API_EVERYTHING');
            $NEWS_API_KEY = env('NEWS_API_KEY');
            $guardian_url = env('THE_GUARDIAN');
            $GUARDIAN_API_KEY = env('GUARDIAN_API_KEY');
            $newsApiResult = [];
            $nyTimesResult = [];
            $guardianResult = [];
            $uniqueIdCounter = 1;

            $keyword = "debate";
            $tag = "politics/politics";
            $sort_by = "publishedAt";
            $language = "en";

            $newsApiResponse = Http::get($news_url, [
                'apiKey' => $NEWS_API_KEY,
                'q' => $keyword,
                'language' => $language,
                'sortBy' => $sort_by
            ]);
            

            $guardianResponse = Http::get($guardian_url, [
                'api-key' => $GUARDIAN_API_KEY,
                'q' => $keyword,
                'tag' => $tag
            ]);

            $nyTimesResponse = Http::get($ny_url, [
                'q' => $keyword,
                'tag' => $tag,
                'api-key' => $NY_API_KEY

            ]);
            $newsApiData = $newsApiResponse->json();
            $nyTimesData = $nyTimesResponse->json();
            $guardianData = $guardianResponse->json();
            foreach($newsApiData['articles'] as $article){
                $result = [
                    'id' => $article['id'] ?? $uniqueIdCounter++,
                    'title' => $article['title'] ?? null,
                    'category' => null,
                    'source' => 'News API',
                    'author' => $article['author'] ?? null,
                    'date' => $article['publishedAt'] ?? null,
                ];
            
                $newsApiResult[] = $result;
            }
            foreach ($nyTimesData['response']['docs'] as $doc) {
                $result = [
                    'id' => $doc['id'] ?? $uniqueIdCounter++,
                    'title' => $doc['abstract'] ?? $doc['lead_paragraph'] ?? null,
                    'category' => null,
                    'source' => 'NY Times',
                    'author' => $doc['source'] ?? null, 
                    'date' => null, 
                ];
            
                $nyTimesResult[] = $result;
            }
            foreach ($guardianData['response']['results'] as $result) {
                $guardianResult[] = [
                    'id' => $uniqueIdCounter++,
                    'title' => $result['webTitle'] ?? null,
                    'category' => $result['sectionName'] ?? null,
                    'source' => 'The Guardian',
                    'author' => null, 
                    'date' => $result['webPublicationDate'] ?? null,
                ];
            }
            $mergedResult = array_merge($newsApiResult,$nyTimesResult,$guardianResult);
        
            $news =  response()->json($mergedResult);
            return sendResponse('Fetch news feeds successfully.', $news, 400);

        } catch (\Exception $e) {
            return sendError('Error.', $e->getMessage(), 500);
        }
    }
}
