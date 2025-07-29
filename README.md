# php-ssw 🚚
Biblioteca PHP simples e robusta para extrair informações de rastreamento a partir do site ssw.inf.br.

# Funcionalidades
- Realiza requisições HTTP POST para o serviço de rastreamento DANFE do ssw.inf.br.
- Extrai o DOCUMENTID a partir do HTML retornado.
- Gera a URL detalhada para visualização do documento com base no código DANFE.
- Gera a URL detalhada para visualização do documento com base no CNPJ e Código NFe.
- Verifica a presença de mensagens de erro no HTML retornado.
- Limpa e formata strings, removendo tags HTML e decodificando entidades.
- Extrai e organiza informações detalhadas de rastreamento (data, localização, status) a partir de URLs de rastreamento.
- Scrapeia informações de rastreamento detalhadas usando o código DANFE (44 dígitos).
- Scrapeia informações de rastreamento detalhadas usando CNPJ e Código NFe.

# Requisitos
PHP 7.0 ou superior
symfony/dom-crawler: Utilizado para facilitar a manipulação do HTML.

# Instalação
Você pode instalar a biblioteca e suas dependências via Composer:
```bash
composer require rafaelcecchin/php-ssw symfony/dom-crawler
```

Ou, se preferir, pode simplesmente copiar o arquivo `src/RafaelCecchin/phpSSW/SSW.php` para o seu projeto e gerenciar a dependência do symfony/dom-crawler manualmente.

# Uso
Para usar a biblioteca, utilize o código abaixo como base.
```php
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
```
