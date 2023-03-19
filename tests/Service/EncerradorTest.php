<?php

namespace Tests\Service;

use App\Dao\Leilao as LeilaoDao;
use App\Infra\ConnectionCreator;
use App\Model\Leilao;
use App\Service\Encerrador;
use App\Service\EnviadorEmail;
use PHPUnit\Framework\TestCase;

class EncerradorTest extends TestCase
{
    /**
     * @var Encerrador
     */
    private $encerrador;

    /**
     * @var LeilaoDao
     */
    private $leilaoDao;

    /**
     * @var EnviadorEmail
     */
    private $enviadorEmail;

    protected function setUp(): void
    {
        $paganiZonda = new Leilao(
            'Pagani Zonda', new \DateTimeImmutable('8 days ago')
        );
        $bmwM8Competition = new Leilao(
            'BMW M8 Competition', new \DateTimeImmutable('10 days ago')
        );

        //testando sem construtor
        //$leilaoDao = $this->createMock(LeilaoDao::class);

        //testando com construtor
        $this->leilaoDao = $this->getMockBuilder(LeilaoDao::class)
            ->setConstructorArgs([
                $this->createMock(\PDO::class)
            ])
            ->getMock();

        $this->leilaoDao->method('recuperarNaoFinalizados')
            ->willReturn([
                $paganiZonda,
                $bmwM8Competition
            ]);
        $this->leilaoDao->expects($this->exactly(2))
            ->method('atualiza')
            ->withConsecutive(
                [$paganiZonda],
                [$bmwM8Competition],
            );
        $this->leilaoDao->method('recuperarFinalizados')
            ->willReturn([
                $paganiZonda,
                $bmwM8Competition
            ]);

        //$this->enviadorEmail  = new EnviadorEmail();
        $this->enviadorEmail = $this->createMock(EnviadorEmail::class);
        $this->enviadorEmail->expects($this->exactly(2))
            ->method('notificarTerminoLeilao')
            ->willReturnCallback(function (Leilao $leilao) {
                static::assertTrue($leilao->estaFinalizado());
                throw new \DomainException('Erro ao enviar email');
            })
//            ->willThrowException(
//                new \DomainException('Erro ao enviar email')
//            )
        ;

        $this->encerrador = new Encerrador(
            $this->leilaoDao,
            $this->enviadorEmail
        );
    }

    public function testLeiloesComMaisDeUmaSemanaDevenSerEncerradosComMocks()
    {
        $this->encerrador->encerra();
        $leiloes = $this->leilaoDao->recuperarFinalizados();
        $this->assertCount(2, $leiloes);
        $this->assertEquals('Pagani Zonda', $leiloes[0]->recuperarDescricao());
        $this->assertEquals('BMW M8 Competition', $leiloes[1]->recuperarDescricao());
    }

//    public function testLeiloesComMaisDeUmaSemanaDevenSerEncerradosSemMock()
//    {
//        $paganiZonda = new Leilao(
//            'Pagani Zonda', new \DateTimeImmutable('8 days ago')
//        );
//
//        $bmwM8Competition = new Leilao(
//            'BMW M8 Competition', new \DateTimeImmutable('10 days ago')
//        );
//
//        $pdo = ConnectionCreator::getConnection();
//        $leilaoDao = new \App\Dao\Leilao($pdo);
//        $leilaoDao->salva($paganiZonda);
//        $leilaoDao->salva($bmwM8Competition);
//
//        $encerrador = new Encerrador(new LeilaoDao($pdo), new EnviadorEmail());
//        $encerrador->encerra();
//
//        $leiloes = $leilaoDao->recuperarFinalizados();
//        //$this->assertCount(2, $leiloes);
//        $this->assertEquals('Pagani Zonda', $leiloes[0]->recuperarDescricao());
//        $this->assertEquals('BMW M8 Competition', $leiloes[1]->recuperarDescricao());
//    }

}