<?php

namespace App\Model;

class Leilao
{
    /** @var Lance[] */
    private $lances;

    /** @var string */
    private $descricao;

    /** @var boolean */
    private $finalizado;

    /** @var \DateTimeInterface */
    private $dataInicio;

    /** @var int */
    private $id;

    public function __construct(string $descricao, \DateTimeImmutable $dataInicio = null, int $id = null)
    {
        $this->descricao = $descricao;
        $this->finalizado = false;
        $this->lances = [];
        $this->dataInicio = $dataInicio ?? new \DateTimeImmutable();
        $this->id = $id;
    }

    public function recebeLance(Lance $lance)
    {
        $lances = $this->getLances();
        $ultimoLance = end($lances);

        if (!empty($lances) && $this->ehDoUltimoUsuario($lance, $ultimoLance)) {
            throw new \DomainException('Usuário não pode propor 2 lances consecutivos');
        }

        $totalLancesUsuario = $this->quantidadeDeLancesPorUsuario($lance->getUsuario());
        if ($totalLancesUsuario >= 5) {
            throw new \DomainException('Usuário não pode propor 5 lances por leilão');
        }

        $this->lances[] = $lance;
    }

    /**
     * @return Lance[]
     */
    public function getLances(): array
    {
        return $this->lances;
    }

    public function recuperarDescricao(): string
    {
        return $this->descricao;
    }

    public function finaliza(): void
    {
        $this->finalizado = true;
    }

    public function estaFinalizado(): bool
    {
        return $this->finalizado;
    }

    /**
     * @param Lance $lance
     * @param Lance $ultimoLance
     * @return bool
     */
    private function ehDoUltimoUsuario(Lance $lance, Lance $ultimoLance): bool
    {
        return $lance->getUsuario() === $ultimoLance->getUsuario();
    }

    /**
     * @param Usuario $usuario
     * @return int
     */
    public function quantidadeDeLancesPorUsuario(Usuario $usuario): int
    {
        $totalLancesUsuario = array_reduce(
            $this->getLances(),
            function ($totalAcumulado, Lance $lanceAtual) use ($usuario) {
                if ($lanceAtual->getUsuario() == $usuario) {
                    return $totalAcumulado + 1;
                }
                return $totalAcumulado;
            },
            0
        );
        return $totalLancesUsuario;
    }

    public function recuperarDataInicio(): \DateTimeInterface
    {
        return $this->dataInicio;
    }

    public function temMaisDeUmaSemana(): bool
    {
        $hoje = new \DateTime();
        $intervalo = $this->dataInicio->diff($hoje);

        return $intervalo->days > 7;
    }

    public function recuperarId(): int
    {
        return $this->id;
    }
}
