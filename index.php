<?php

require __DIR__ . '/vendor/autoload.php';

use Guzzle\Http\Client; 
use Symfony\Component\CssSelector;
use Symfony\Component\DomCrawler\Crawler;
use Guzzle\Http\Exception\ClientErrorResponseException;


function grabSindo($url, $category) {
    $data = array();
    $userAgent = 'Mozilla/5.0 (Windows NT 10.0)'
                . ' AppleWebKit/537.36 (KHTML, like Gecko)'
                . ' Chrome/48.0.2564.97'
                . ' Safari/537.36';
    $headers = array('User-Agent' => $userAgent);

    // Set Guzzle
    $client = new Client();
    $request = $client->get($url, $headers);

    try {
        $response = $request->send();
        $body = $response->getBody(true);
    } catch (ClientErrorResponseException $e) {
        $responseBody = $e->getResponse()->getBody(true);
        return $responseBody;
    }

    // Crawl
    $crawler = new Crawler($body);
    $filter = '.lst-mr > ul > li > .lnk-t a';
    $catsHTML = $crawler->filter($filter)
                        ->each(function (Crawler $node) {
                        return array(
                            'href' => $node->attr('href')
                        );
                    });
    unset($crawler);
    
    foreach ($catsHTML as $index => $catHTML) {
        // Set Guzzle
        $sub = $client->get($catHTML['href'], $headers);
        try {
            $response = $sub->send();
            $body = $response->getBody(true);

            // Crawl
            $crawler = new Crawler($body);
            $title = $crawler->filter('.article > h1')->text();
            $content = $crawler->filter('.article > #content')->html();
            $img = $crawler->filter('.article > figure img')->attr('src');
            $data[] = array(
                'title' => $title,
                'content' => $content,
                'image' => $img,
                'category' => $category
            );
            // unset($crawler);
        } catch (ClientErrorResponseException $e) {
            $responseBody = $e->getResponse()
                            ->getBody(true);
            return $responseBody;
        }

        if ($index == 9) {
            break;
        }
    }

    return $data;
}

// print_r(grabSindo('https://nasional.sindonews.com/politik', 'politik'));


function grapHipwee($url)
{
    $data = array();
    $userAgent = 'Mozilla/5.0 (Windows NT 10.0)'
                . ' AppleWebKit/537.36 (KHTML, like Gecko)'
                . ' Chrome/48.0.2564.97'
                . ' Safari/537.36';
    $headers = array('User-Agent' => $userAgent);

    // Set Guzzle
    $client = new Client();
    $request = $client->get($url, $headers);

    try {
        $response = $request->send();
        $body = $response->getBody(true);
    } catch (ClientErrorResponseException $e) {
        $responseBody = $e->getResponse()->getBody(true);
        return $responseBody;
    }

    // Crawl
    $crawler = new Crawler($body);
    $title = $crawler->filter('.article-header h1')->text();
    $content = $crawler->filter('.article-content article')->html();
    $img = $crawler->filter('.featured-img img')->attr('src');

    $data[] = array(
        'title' => $title,
        'content' => $content,
        'image' => $img
    );

    return json_encode($data);
}

echo grapHipwee('https://www.hipwee.com/hiburan/pasangan-bucin');


function laravelNews($url)
{
    $data = array();
    $userAgent = 'Mozilla/5.0 (Windows NT 10.0)'
                . ' AppleWebKit/537.36 (KHTML, like Gecko)'
                . ' Chrome/48.0.2564.97'
                . ' Safari/537.36';
    $headers = array('User-Agent' => $userAgent);

    // Set Guzzle
    $client = new Client();
    $request = $client->get($url, $headers);

    try {
        $response = $request->send();
        $body = $response->getBody(true);
    } catch (ClientErrorResponseException $e) {
        $responseBody = $e->getResponse()->getBody(true);
        return $responseBody;
    }

    // Crawl
    $crawler = new Crawler($body);
    $title = $crawler->filter('.post__header h1')->text();
    $img = $crawler->filter('.post__image > img')->attr('src');
    // $content = $crawler->filter('.article-content > article')->html();
    $content = $crawler->filter('.post__content p')
                        ->each(function (Crawler $node) {
                        return array(
                            'p' => $node->text()
                        );
                    });
    $data[] = array(
        'title' => $title,
        'content' => $content,
        'image' => $img
    );

    return $data;
}

// print_r(laravelNews('https://laravel-news.com/form-spam-prevention'));