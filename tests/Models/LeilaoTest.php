<?php

namespace Tests\Models;

use App\Model\Lance;
use App\Model\Leilao;
use App\Model\Usuario;
use PHPUnit\Framework\TestCase;

class LeilaoTest extends TestCase
{
    public function geraLances()
    {
        $felipe = new Usuario('Felipe');
        $jaqueline = new Usuario('Jaqueline');
        $jaquelineGomes = new Usuario('Jaqueline Gomes');
        $thiago = new Usuario('Thiago');
        $camen = new Usuario('Carmen');
        $juliana = new Usuario('Juliana');
        $francisco = new Usuario('Francisco');

        $leilaoCom7Lances = new Leilao("Ferrari 2023");
        $leilaoCom7Lances->recebeLance(new Lance($felipe, 2000));
        $leilaoCom7Lances->recebeLance(new Lance($jaqueline, 3000));
        $leilaoCom7Lances->recebeLance(new Lance($thiago, 5000));
        $leilaoCom7Lances->recebeLance(new Lance($juliana, 5000));
        $leilaoCom7Lances->recebeLance(new Lance($camen, 7000));
        $leilaoCom7Lances->recebeLance(new Lance($francisco, 8000));
        $leilaoCom7Lances->recebeLance(new Lance($jaquelineGomes, 10000));

        $LeilaoCom1lance = new Leilao("BMW M8 2023");
        $LeilaoCom1lance->recebeLance(new Lance($felipe, 2000));

        return [
            '7 lances' => [7, $leilaoCom7Lances, [2000, 3000, 5000, 5000, 7000, 8000, 10000]],
            '1 lances' => [1, $LeilaoCom1lance, [2000]]
        ];

    }

    public function testLeilaoDeveNaoReceberLancesRepetidos()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Usuário não pode propor 2 lances consecutivos');

        $felipe = new Usuario('Felipe');

        $leilao = new Leilao("Ferrari 2023");
        $leilao->recebeLance(new Lance($felipe, 2000));
        $leilao->recebeLance(new Lance($felipe, 3000));
    }


    public function testLeilaoNaoDeveAceitarMaisDe5LancesPorUsuario()
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('Usuário não pode propor 5 lances por leilão');

        $felipe = new Usuario('Felipe');
        $jaquelineGomes = new Usuario('Jaqueline Gomes');

        $leilao = new Leilao("Ferrari 2023");

        $leilao->recebeLance(new Lance($felipe, 2000));
        $leilao->recebeLance(new Lance($jaquelineGomes, 3000)); // 1

        $leilao->recebeLance(new Lance($felipe, 4000));
        $leilao->recebeLance(new Lance($jaquelineGomes, 5000)); // 2

        $leilao->recebeLance(new Lance($felipe, 6000));
        $leilao->recebeLance(new Lance($jaquelineGomes, 7000)); // 3

        $leilao->recebeLance(new Lance($felipe, 8000));
        $leilao->recebeLance(new Lance($jaquelineGomes, 9000)); // 4

        $leilao->recebeLance(new Lance($felipe, 10000));
        $leilao->recebeLance(new Lance($jaquelineGomes, 11000)); // 5

        $leilao->recebeLance(new Lance($felipe, 12000));
        $leilao->recebeLance(new Lance($jaquelineGomes, 13000)); // 6
    }

    /**
     * @dataProvider geraLances
     */
    public function testLeilaoDeveReceberLances(
        int    $qdtLances,
        Leilao $leilao,
        array  $valores)
    {
        $this->assertCount($qdtLances, $leilao->getLances());
        foreach ($valores as $key => $valorEsperado) {
            $this->assertEquals($valorEsperado, $leilao->getLances()[$key]->getValor());
        }
    }

}