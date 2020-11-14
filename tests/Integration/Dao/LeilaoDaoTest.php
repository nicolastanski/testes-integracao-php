<?php

namespace Alura\Leilao\Tests\Integration\Dao;

use Alura\Leilao\Dao\Leilao as LeilaoDao;
use Alura\Leilao\Model\Leilao;
use PHPUnit\Framework\TestCase;

class LeilaoDaoTest extends TestCase
{
    /** @var \PDO */
    private static $pdo;

    public static function setUpBeforeClass(): void
    {
        self::$pdo = new \PDO('sqlite::memory:');
        self::$pdo->exec('create table leiloes (
            id INTEGER primary key,
            descricao TEXT,
            finalizado BOOL,
            dataInicio TEXT
        );');
    }

    public function setUp(): void
    {
        self::$pdo->beginTransaction();
    }

    /** @dataProvider leiloes */
    public function testBuscaLeiloesNaoFinalizados(array $leiloes)
    {
        // arrange
        $leilaoDao = new LeilaoDao(self::$pdo);

        foreach($leiloes as $leilao) {
            $leilaoDao->salva($leilao);
            // testes intermediários
        }

        // act
        $leiloes = $leilaoDao->recuperarNaoFinalizados();

        // asserts
        self::assertCount(1, $leiloes);
        self::assertContainsOnlyInstancesOf(Leilao::class, $leiloes);
        self::assertSame('Variante 0KM', $leiloes[0]->recuperarDescricao());
        self::assertFalse($leiloes[0]->estaFinalizado());
    }

    /** @dataProvider leiloes */
    public function testBuscaLeiloesFinalizados(array $leiloes)
    {
        // arrange
        $leilaoDao = new LeilaoDao(self::$pdo);

        foreach($leiloes as $leilao) {
            $leilaoDao->salva($leilao);
        }
        
        // act
        $leiloes = $leilaoDao->recuperarFinalizados();

        // asserts
        self::assertCount(1, $leiloes);
        self::assertContainsOnlyInstancesOf(Leilao::class, $leiloes);
        self::assertSame('Fiat 147 0KM', $leiloes[0]->recuperarDescricao());
        self::assertTrue($leiloes[0]->estaFinalizado());
    }

    public function leiloes()
    {
        $leilaoNaoFinalizado = new Leilao('Variante 0KM');
        $leilaoFinalizado = new Leilao('Fiat 147 0KM');
        $leilaoFinalizado->finaliza();

        return [
            [
                [$leilaoNaoFinalizado, $leilaoFinalizado]
            ]
        ];
    }

    public function tearDown(): void
    {
        self::$pdo->rollback();
    }
}
