<?php

namespace Alura\Leilao\Tests\Integration\Web;

use PHPUnit\Framework\TestCase;

class RestTest extends TestCase
{
    public function testApiRestDeveRetornarArrayDeLeiloes()
    {
        $reposta = file_get_contents('http://localhost:8080');

        self::assertStringContainsString('200 OK', $http_response_header[0]);
        self::assertIsArray(json_decode($reposta));
    }
}