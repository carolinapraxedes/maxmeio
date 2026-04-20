<?php
namespace App\Enums;

enum BillingStatus: string
{
    case PENDING = 'pendente';
    case AWAITING_PAYMENT = 'aguardando_pagamento';
    case PAID = 'pago';
    case PARTIAL_PAID = 'pago_parcial';
    case OVERDUE = 'inadimplente';
    case NEGOTIATING = 'negociando';
    case CANCELLED = 'cancelado';

    public function canTransitionTo(BillingStatus $newStatus): bool
    {
        
        if ($this === $newStatus) return true;

        return match($this) {
            // pendente -> aguardando_pagamento
            self::PENDING => $newStatus === self::AWAITING_PAYMENT,

            // aguardando_pagamento -> pago OR pago_parcial OR inadimplente
            self::AWAITING_PAYMENT => in_array($newStatus, [
                self::PAID, 
                self::PARTIAL_PAID, 
                self::OVERDUE
            ]),

            // pago_parcial -> pago
            self::PARTIAL_PAID => $newStatus === self::PAID,

            // inadimplente -> negociando
            self::OVERDUE => $newStatus === self::NEGOTIATING,

            // negociando -> pago OR cancelado
            self::NEGOTIATING => in_array($newStatus, [
                self::PAID, 
                self::CANCELLED
            ]),

            // Estados finais (pago e cancelado não saem para lugar nenhum)
            self::PAID, self::CANCELLED => false,

            default => false,
        };
    }
}      