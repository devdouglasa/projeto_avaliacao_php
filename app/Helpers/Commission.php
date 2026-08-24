<?php

namespace App\Helpers;

/**
 * Cálculo da comissão do funcionário ao finalizar um serviço.
 *
 * Regras do enunciado:
 * - Até R$ 250,00: 5%
 * - Acima de R$ 1.000,00: 10%
 * - Acima de R$ 10.000,00: 20%
 *
 * Valores entre R$ 250,01 e R$ 1.000,00 não possuem faixa definida (0%).
 */
class Commission
{
    public static function calculate($price)
    {
        $price = (float) $price;

        if ($price > 10000) {
            return round($price * 0.20, 3);
        }

        if ($price > 1000) {
            return round($price * 0.10, 3);
        }

        if ($price <= 250) {
            return round($price * 0.05, 3);
        }

        return 0.000;
    }
}
