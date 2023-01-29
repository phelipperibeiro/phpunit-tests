<?php

namespace Tests\Service;

use App\Service\Avaliador;
use App\Model\Lance;
use App\Model\Leilao;
use App\Model\Usuario;
use PHPUnit\Framework\TestCase;

class AvaliadorTest extends TestCase
{
    private $leiloeiro;

    public function setUp(): void
    {
        $this->leiloeiro = new Avaliador();
    }

    public function dadosleilaoEmOrdemCrescente()
    {
        $leilao = new Leilao("Ferrari 2023");

        $felipe = new Usuario('Felipe');
        $jaqueline = new Usuario('Jaqueline');
        $jaquelineGomes = new Usuario('Jaqueline Gomes');
        $thiago = new Usuario('Thiago');
        $camen = new Usuario('Carmen');
        $juliana = new Usuario('Juliana');
        $francisco = new Usuario('Francisco');

        $leilao->recebeLance(new Lance($felipe, 2000));
        $leilao->recebeLance(new Lance($jaqueline, 3000));
        $leilao->recebeLance(new Lance($thiago, 5000));
        $leilao->recebeLance(new Lance($juliana, 5000));
        $leilao->recebeLance(new Lance($camen, 7000));
        $leilao->recebeLance(new Lance($francisco, 8000));
        $leilao->recebeLance(new Lance($jaquelineGomes, 10000));
        return $leilao;
    }

    public function dadosleilaoEmOrdemDecrescente()
    {
        $leilao = new Leilao("Ferrari 2023");

        $felipe = new Usuario('Felipe');
        $jaqueline = new Usuario('Jaqueline');
        $jaquelineGomes = new Usuario('Jaqueline Gomes');
        $thiago = new Usuario('Thiago');
        $camen = new Usuario('Carmen');
        $juliana = new Usuario('Juliana');
        $francisco = new Usuario('Francisco');

        $leilao->recebeLance(new Lance($jaquelineGomes, 10000));
        $leilao->recebeLance(new Lance($francisco, 8000));
        $leilao->recebeLance(new Lance($camen, 7000));
        $leilao->recebeLance(new Lance($thiago, 5000));
        $leilao->recebeLance(new Lance($juliana, 5000));
        $leilao->recebeLance(new Lance($jaqueline, 3000));
        $leilao->recebeLance(new Lance($felipe, 2000));
        return $leilao;
    }

    public function dadosleilaoEmOrdemAleatoria()
    {
        $leilao = new Leilao("Ferrari 2023");

        $felipe = new Usuario('Felipe');
        $jaqueline = new Usuario('Jaqueline');
        $jaquelineGomes = new Usuario('Jaqueline Gomes');
        $thiago = new Usuario('Thiago');
        $camen = new Usuario('Carmen');
        $juliana = new Usuario('Juliana');
        $francisco = new Usuario('Francisco');

        $leilao->recebeLance(new Lance($jaquelineGomes, 10000));
        $leilao->recebeLance(new Lance($jaqueline, 3000));
        $leilao->recebeLance(new Lance($felipe, 2000));
        $leilao->recebeLance(new Lance($thiago, 5000));
        $leilao->recebeLance(new Lance($francisco, 8000));
        $leilao->recebeLance(new Lance($juliana, 5000));
        $leilao->recebeLance(new Lance($camen, 7000));

        return $leilao;
    }

    public function entregaLeiloes()
    {
        return [
            'Em Ordem Crescente' => [$this->dadosleilaoEmOrdemCrescente()],
            'Em Ordem Decrescente' => [$this->dadosleilaoEmOrdemDecrescente()],
            'Em Ordem Aleatoria' => [$this->dadosleilaoEmOrdemAleatoria()]
        ];
    }

    /**
     * @dataProvider entregaLeiloes
     */
    public function testAvaliadorDeveEncontrarOMaiorValorDeLances(Leilao $leilao)
    {

        $this->leiloeiro->avalia($leilao);
        $maiorValor = $this->leiloeiro->getMaiorValor();

        $this->assertEquals(10000, $maiorValor);
    }


    /**
     * @dataProvider entregaLeiloes
     */
    public function testAvaliadorDeveEncontrarOMenorValorDeLances(Leilao $leilao)
    {
        $this->leiloeiro->avalia($leilao);
        $menorValor = $this->leiloeiro->getMenorValor();

        $this->assertEquals(2000, $menorValor);
    }

    /**
     * @dataProvider entregaLeiloes
     */
    public function testAvaliadorDeveBuscar3MaioresValores(Leilao $leilao)
    {
        $this->leiloeiro->avalia($leilao);
        $maioresLances = $this->leiloeiro->getMaioresLances();
        $this->assertEquals(count($maioresLances), 3);
        $this->assertEquals($maioresLances[0]->getValor(), 10000);
        $this->assertEquals($maioresLances[1]->getValor(), 8000);
        $this->assertEquals($maioresLances[2]->getValor(), 7000);
    }

    public function testLeilaoFinalizadoNaoPodeSerAvaliado()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Leilão já finalizado');

        $leilao = $this->dadosleilaoEmOrdemAleatoria();
        $leilao->finaliza();
        $this->leiloeiro->avalia($leilao);
    }

    public function testLeilaoVazioNaoPodeSerAvaliado()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('não é possivel avaliar leilão vazio');
        $leilao = new Leilao("Ferrari 2023");
        $this->leiloeiro->avalia($leilao);
    }
}