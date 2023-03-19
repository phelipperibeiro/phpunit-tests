<?php

namespace App\Service;

use App\Model\Leilao;

class EnviadorEmail
{
    public function notificarTerminoLeilao(Leilao $leilao): void
    {
        $response = mail(
            'usr@email.com',
            'Leilão Finalizado',
            'O Leilão para: ' . $leilao->recuperarDescricao() . ' foi finalizado'
        );

        if (!$response) {
            throw new \DomainException('Erro ao enviar email');
        }
    }
}