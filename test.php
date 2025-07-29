<?php

require 'vendor/autoload.php'; // Se você instalou via Composer

use RafaelCecchin\phpSSW\SSW;

$danfeCode = '00000000000000000000000000000000000000000000';
$url = SSW::danfeUrl($danfeCode);
$scrapeDanfe = SSW::scrapeDanfe($danfeCode);

if ($url) echo "URL de rastreamento DANFE: " . $url . PHP_EOL;
if ($scrapeDanfe) echo "Dados de rastreamento: " . JSON_encode($scrapeDanfe) . PHP_EOL;

$cnpj = '11111111111111'; 
$codigoNfe = '1234';
$url = SSW::cnpjNFeUrl($cnpj, $codigoNfe);
$scrapeCnpjNFe = SSW::scrapeCnpjNFe($cnpj, $codigoNfe);

if ($url) echo "URL de rastreamento CNPJ + NFe: " . $url . PHP_EOL;
if ($scrapeCnpjNFe) echo "Dados de rastreamento: " . JSON_encode($scrapeCnpjNFe) . PHP_EOL;

/*
    A estrutura de um item de atualização retornado por scrapeDanfe ou scrapeCnpjNFe é a seguinte:

    [
        'dateTime' => 'DD/MM/AAAA HH:MM',
        'localization' => [
            'city' => 'Nome da Cidade',
            'unit' => 'Nome da Unidade'
        ],
        'status' => [
            'titulo' => 'Título do Status',
            'descricao' => 'Descrição detalhada do Status'
        ]
    ]
*/