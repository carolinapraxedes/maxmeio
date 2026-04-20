<?php
namespace App\Enums;

enum ServiceOrderStatus: string
{
    case PENDING = 'pendente';
    case IN_PROGRESS = 'em_andamento';
    case COMPLETED = 'concluida';
    case CANCELED = 'cancelada';

    /**
     * Returns a human-readable label for the dashboard.
     */
    public function label(): string
    {
        return match($this) {
            self::PENDING => 'Pendente',
            self::IN_PROGRESS => 'Em Andamento',
            self::COMPLETED => 'Concluída',
            self::CANCELED => 'Cancelada',
        };
    }
}