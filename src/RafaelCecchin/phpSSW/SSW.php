<?php

namespace RafaelCecchin\phpSSW;
use Symfony\Component\DomCrawler\Crawler;

class SSW
{
    
    /**
     * Create a new Crawler instance from the given HTML.
     *
     * @param string $html
     * @return Crawler
     */
    public static function crawler(string $html): Crawler
    {
        return new Crawler($html);
    }

    /**
     * Checks if the given HTML has an error message.
     *
     * @param string $html
     * @return bool
     */
    public static function hasError(string $html): bool
    {
        return SSW::crawler($html)->filter('.erro')->count() > 0;
    }

    /**
     * Gets the DOCUMENTID from the given HTML.
     *
     * @param string $html HTML to search for the DOCUMENTID input
     * @return ?string DOCUMENTID value or null if not found
     */
    public static function getDocumentID(string $html): ?string
    {
        $input = SSW::crawler($html)->filter('input#DOCUMENTID');

        if ($input->count() === 0) return null;
        return urldecode($input->attr('value'));
    }
    
    /**
     * Sanitizes a string by replacing all occurrences of <br> (including variations)
     * with a single space, stripping all HTML tags, decoding all HTML entities,
     * and trimming the resulting string.
     *
     * @param string $string
     * @return string
     */
    public static function sanitize(string $string): string
    {
        $string = str_replace(['<br>', '<br/>', '<br />'], ' ', $string);
        $string = strip_tags($string);
        $string = html_entity_decode($string);
        $string = trim($string);
        
        return $string;
    }

    /**
     * Retrieves updates from the specified URL and parses the HTML to extract
     * information about date, localization, and status of each entry.
     *
     * @param string $url The URL to fetch and parse updates from.
     * @return ?array An array of updates or null if the HTML cannot be parsed.
     */
    public static function getUpdates(string $url): ?array
    {
        $html = SSW::httpRequest($url);
        $crawler = SSW::crawler($html);
        $updates = [];

        $crawler->filter('table tr:nth-child(even)')->each(function ($tr) use (&$updates) {
            $tds = $tr->filter('td');

            if ($tds->count() !== 3) return; 
            
            $td1 = $tds->eq(0);
            $td2 = $tds->eq(1);
            $td3 = $tds->eq(2);

            if (!$td1->count() || !$td2->count() || !$td3->count()) return;
            
            $dateTime = SSW::sanitize($td1->html());

            $localization = [];
            $td2 = preg_split('/<br\s*\/?>/i', $td2->html(), -1, PREG_SPLIT_NO_EMPTY);
            if (isset($td2[0])) $localization['city'] = SSW::sanitize($td2[0]);
            if (isset($td2[1])) $localization['unit'] = SSW::sanitize($td2[1]);
            
            $status = [];
            $titulo = $td3->filter('b');
            $descricao = $td3->filter('.tdb');

            if ($titulo->count()) $status['titulo'] = SSW::sanitize($titulo->html());
            if ($descricao->count()) $status['descricao'] = SSW::sanitize($descricao->html());

            if (empty($dateTime) || empty($localization) || empty($status)) return;
            
            $updates[] = [
                'dateTime' => $dateTime,
                'localization' => $localization,
                'status' => $status
            ];
        });

        if (empty($updates)) return null;

        return $updates;
    }
    
    /**
     * Perform a HTTP request and return the response content.
     * 
     * @param string $url The URL to request
     * @param string $method The HTTP method to use (defaults to 'GET')
     * @param array $data The data to pass in the request body (if applicable)
     * @return ?string The response content or null if the request fails
     */
    public static function httpRequest(string $url, string $method = 'GET', array $data = null): ?string
    {
        $options = [
            'method'  => $method,
            'header'  => 'Content-Type: application/x-www-form-urlencoded'
        ];
    
        if ($data) $options['content'] = \http_build_query($data);
    
        $context = \stream_context_create(['http' => $options]);
        $response = @\file_get_contents($url, false, $context);

        if ($response === false) return null;

        return $response;
    }
    
    /**
     * Returns the detailed URL for the given DANFE code.
     *
     * @param string $codigo44 The 44-digit DANFE code
     * @return ?string The detailed URL or null if the request fails
     */
    public static function danfeUrl(string $codigo44): ?string
    {
        $response = SSW::httpRequest('https://ssw.inf.br/2/rastreamento_danfe', 'POST', ['danfe' => $codigo44]);
        $documentID = SSW::getDocumentID($response);
        
        if (!$documentID) return null;
        $link = 'https://ssw.inf.br/2/SSWDetalhado?' . $documentID;

        return $link;
    }
    
    /**
     * Retrieves the URL for the detailed page of the specified CNPJ and NFe
     * code. If the request fails, returns null.
     *
     * @param string $cnpj The 14-digit CNPJ code
     * @param string $codigo The NFe code
     * @return ?string The detailed URL or null if the request fails
     */
    public static function cnpjNFeUrl(string $cnpj, string $codigo): ?string
    {
        $link = "https://ssw.inf.br/app/tracking/$cnpj/$codigo/";
        $response = SSW::httpRequest($link);
                
        if (SSW::hasError($response)) return null;

        return $link;
    }
    
    /**
     * Scrapes the detailed updates for a given CNPJ and NFe code.
     * 
     * This function constructs the URL for the tracking page of the provided
     * CNPJ and NFe code, retrieves the page content, and extracts the updates
     * from it. If the URL cannot be constructed or the updates cannot be 
     * retrieved, it returns null.
     *
     * @param string $cnpj The 14-digit CNPJ code.
     * @param string $codigo The NFe code.
     * @return ?array An array of updates or null if the request fails.
     */
    public static function scrapeCnpjNFe(string $cnpj, string $codigo): ?array
    {
        $url = SSW::cnpjNFeUrl($cnpj, $codigo);        
        if (!$url) return null;

        $updates = SSW::getUpdates($url);
        
        return $updates;
    }

    /**
     * Scrapes the detailed updates for a given 44-digit DANFE code.
     * 
     * This function constructs the URL for the tracking page of the provided
     * DANFE code, retrieves the page content, and extracts the updates
     * from it. If the URL cannot be constructed or the updates cannot be 
     * retrieved, it returns null.
     *
     * @param string $codigo44 The 44-digit DANFE code.
     * @return ?array An array of updates or null if the request fails.
     */
    public static function scrapeDanfe(string $codigo44): ?array
    {
        $url = SSW::danfeUrl($codigo44);
        if (!$url) return null;
        
        $updates = SSW::getUpdates($url);
        
        return $updates;
    }
}