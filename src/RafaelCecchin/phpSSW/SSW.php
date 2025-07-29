<?php

namespace RafaelCecchin\phpSSW;

class SSW
{
    public static function hasError($html)
    {
        return preg_match('/<[^>]*class=[\'"]?erro[\'"]?/', $html);
    }

    public static function getDocumentID($html)
    {
        if (!preg_match('/<input[^>]*id=[\'"]?DOCUMENTID[\'"]?[^>]*value=[\'"]([^\'"]+)[\'"]/', $html, $matches)) return null;
        
        return urldecode($matches[1]);
    }

    public static function httpRequest($url, $data = null)
    {
        $options = \stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-Type: application/x-www-form-urlencoded'
            ]
        ]);

        if ($data) $options['content'] = \http_build_query($data);

        return \file_get_contents($url, false, $options);
    }

    public static function danfeUrl($codigo44)
    {
        $response = SSW::httpRequest('https://ssw.inf.br/2/rastreamento_danfe', ['danfe' => $codigo44]);
        $documentID = SSW::getDocumentID($codigo44);
        
        if (!$documentID) return false;
        $link = 'https://ssw.inf.br/2/SSWDetalhado?' . $documentID;

        return $link;
    }

    public static function cnpjNFeUrl($cnpj, $codigo)
    {
        $link = "https://ssw.inf.br/app/tracking/$cnpj/$codigo/";
        $response = SSW::httpRequest($link);
                
        if (SSW::hasError($response)) return false;

        return $link;
    }
}