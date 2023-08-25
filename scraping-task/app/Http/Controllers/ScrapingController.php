<?php

namespace App\Http\Controllers;
use App\Models\ScrapedData;
use Illuminate\Http\Request;
use Goutte\Client;

class ScrapingController extends Controller
{
    public function getData()
    {
        $data = ScrapedData::all();

        return view('scraped-data', ['data' => $data]);
    }

    public function scrapeData()
    {
        // $ret = [
        //     'link' => 'links',
        //     'title' => 'titles',
        //     'author' => 'authors',
        //     'section' => 'sections',
        //     'info' => [],
        // ];
        // return response()->json(['data' => [$ret]]);
        $client = new Client();
        $crawler = $client->request('GET', 'https://www.kotobati.com/section/%D8%B1%D9%88%D8%A7%D9%8A%D8%A7%D8%AA'); // Replace with the actual URL

        $data = $crawler->filter('.book-box')->each(function ($node) use ($client) {
            
            $link = $node->filter('.title a')->attr('href');
            $link = 'https://www.kotobati.com' .$link; 

            $existingRecord = ScrapedData::where('link', $link)->first();
           
            if ($existingRecord) {
                return null;
            }


            $title = $node->filter('.title a')->text();
            $author = $node->filter('.author-label a')->text();
            $section = $node->filter('.section-label a')->text();

            // Visit the individual book link
            $bookCrawler = $client->request('GET', $link);

            // Extract data from the book's page
            $bookInfo = $bookCrawler->filter('.book-table-info li')->each(function ($liNode) {
                try
                {
                    $label = $liNode->filter('p:first-child')->text();
                    $value = $liNode->filter('p:last-child')->text();

                    if ($label == $value)
                    {
                        $labelAndValue = explode(' ', $label, 2);
                        $label = $labelAndValue[0];
                        $value = $labelAndValue[1];
                    }                    
                    return [
                        'label' => $label,
                        'value' => $value,
                    ];
                }
                catch (\Exception $e)
                {
                    return null;
                }
            });

            $bookInfo = array_filter($bookInfo);

            $scrapedData = new ScrapedData(); // Assuming you have a ScrapedData model
            $scrapedData->link = $link;
            $scrapedData->title = $title;
            $scrapedData->author = $author;
            $scrapedData->section = $section;
            $scrapedData->info = json_encode($bookInfo); // Convert info array to JSON and save
            $scrapedData->save();

            return [
                'link' => $link,
                'title' => $title,
                'author' => $author,
                'section' => $section,
                'info' => $bookInfo,
            ];
        });
        
        // $view = view('scraped-data', ['data' => $data])->render();
        // return response()->json(['html' => $view]);

        $data = array_filter($data);
        return response()->json(['data' => $data]);
    }
}
