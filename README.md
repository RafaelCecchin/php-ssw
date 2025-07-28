# php-ssw 🚚

Biblioteca PHP simples para extrair informações a partir do site [ssw.inf.br](https://ssw.inf.br).

## Funcionalidades

- Realiza requisições HTTP POST para o serviço de rastreamento DANFE do ssw.inf.br.
- Extrai o `DOCUMENTID` a partir do HTML retornado.
- Gera a URL detalhada para visualização do documento com base no código DANFE.

## Requisitos

- PHP 7.0 ou superior
- Extensão `file_get_contents` habilitada (padrão em PHP)

## Instalação

Você pode instalar via Composer (se desejar criar um pacote) ou simplesmente copiar o arquivo `src/RafaelCecchin/phpSSW/SSW.php` para o seu projeto.

Exemplo com Composer:

```bash
composer require rafaelcecchin/php-ssw
```

## Uso

Para usar a biblioteca, utilize o código abaixo como base.

```php
use RafaelCecchin\phpSSW\SSW;

$url = SSW::danfeUrl('CODIGO_44_DIGITOS_DANFE');
```