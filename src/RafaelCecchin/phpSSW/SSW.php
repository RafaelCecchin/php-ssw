<?php

namespace RafaelCecchin\phpSSW;

class SSW
{
    public static function httpRequest($url, $data)
    {
        $options = \stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => 'Content-Type: application/x-www-form-urlencoded',
                'content' => \http_build_query($data)
            ]
        ]);

        return \file_get_contents($url, false, $options);
    }

    public static function getDocumentID($html)
    {
        if (!preg_match('/<input[^>]*id=[\'"]?DOCUMENTID[\'"]?[^>]*value=[\'"]([^\'"]+)[\'"]/', $html, $matches)) return null;
        
        return urldecode($matches[1]);
    }

    public static function danfeUrl($codigo)
    {
        $response = SSW::httpRequest('https://ssw.inf.br/2/rastreamento_danfe', ['danfe' => $codigo]);
        $documentID = SSW::getDocumentID($response);
        
        if (!$documentID) return false;

        return 'https://ssw.inf.br/2/SSWDetalhado?' . $documentID;
    }
}