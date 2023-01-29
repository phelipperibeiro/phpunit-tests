<?php

namespace App\Service;

use App\Model\Lance;
use App\Model\Leilao;

class Avaliador
{
    private $maiorValor;

    private $menorValor;

    private $maioresValores;

    public function __construct()
    {
        $this->maiorValor = -INF;
        $this->menorValor = INF;
    }


    public function avalia(Leilao $leilao): void
    {
        /**
         * @var Lance[] $lances
         */
        $lances = $leilao->getLances();

        if ($leilao->estaFinalizado()) {
            throw new \DomainException('Leilão já finalizado');
        }


        if (empty($lances)) {
            throw new \DomainException('não é possivel avaliar leilão vazio');
        }


        foreach ($lances as $lance) {
            if ($lance->getValor() > $this->maiorValor) {
                $this->maiorValor = $lance->getValor();
            }
            if ($lance->getValor() < $this->menorValor) {
                $this->menorValor = $lance->getValor();
            }
        }

        usort($lances, function (Lance $lance1, Lance $lance2) {
            if ($lance1->getValor() == $lance2->getValor()) {
                return 0;
            }
            return ($lance1->getValor() < $lance2->getValor()) ? 1 : -1;
        });
        $this->maioresValores = array_slice($lances, 0, 3);
    }

    public function getMaiorValor()
    {
        return $this->maiorValor;
    }

    public function getMenorValor()
    {
        return $this->menorValor;
    }

    /**
     * @return Lance[]
     */
    public function getMaioresLances(): array
    {
        return $this->maioresValores;
    }

}