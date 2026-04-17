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
        return match($this) {
            self::PENDING => $newStatus === self::AWAITING_PAYMENT,
            self::AWAITING_PAYMENT => in_array($newStatus, [self::PAID, self::PARTIAL_PAID, self::OVERDUE]),
            self::OVERDUE => $newStatus === self::NEGOTIATING,
            self::NEGOTIATING => in_array($newStatus, [self::PAID, self::CANCELLED]),
            self::PAID, self::CANCELLED => false, // Estados finais
            default => false,
        };
    }
}      