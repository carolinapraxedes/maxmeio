<?php

namespace App\Services;

use App\Models\Billing;
use App\Enums\BillingStatus;
use App\Exceptions\TransitionException;
use Illuminate\Support\Facades\DB;

class BillingService
{
    /**
     * Atualiza o status e processa regras de negócio.
     */
    public function updateStatus(Billing $billing, array $data): Billing
    {
        return DB::transaction(function () use ($billing, $data) {
            
            if (isset($data['status'])) {
                $newStatus = BillingStatus::from($data['status']);
                
                // 1. Valida se a transição é permitida pelo Enum
                if (!$billing->status->canTransitionTo($newStatus)) {
                    throw new TransitionException("Não é permitido mudar de {$billing->status->value} para {$newStatus->value}.");
                }

                // 2. Regra de Inadimplência: Só após o vencimento
                if ($newStatus === BillingStatus::OVERDUE && now()->lessThanOrEqualTo($billing->due_date)) {
                    throw new TransitionException("Esta cobrança ainda não venceu (Vencimento: {$billing->due_date->format('d/m/Y')}).");
                }

                // 3. Regra de Cancelamento: Motivo obrigatório
                if ($newStatus === BillingStatus::CANCELLED) {
                    if (empty($data['cancellation_reason']) || strlen($data['cancellation_reason']) < 10) {
                        throw new TransitionException("Para cancelar, é necessário um motivo com pelo menos 10 caracteres.");
                    }
                    $billing->cancellation_reason = $data['cancellation_reason'];
                }

                // 4. Regra de Pagamento: Aplicar saldo de crédito do cliente
                if ($newStatus === BillingStatus::PAID) {
                    $this->applyCredits($billing);
                } else {
                    $billing->status = $newStatus;
                }
            }

            // 5. Atualização de valor pago manualmente (se houver)
            if (isset($data['paid_amount'])) {
                $billing->paid_amount = $data['paid_amount'];
                $this->syncStatusWithPayment($billing);
            }

            $billing->save();
            
            return $billing;
        });
    }

    /**
     * Aplica automaticamente o saldo do cliente na cobrança.
     */
    private function applyCredits(Billing $billing): void
    {
        $client = $billing->contract->client;
        $remaining = $billing->remaining_amount;

        if ($client->credit_balance > 0 && $remaining > 0) {
            $amountToUse = min($client->credit_balance, $remaining);
            
            // Subtrai do saldo do cliente e adiciona ao pago da cobrança
            $client->decrement('credit_balance', $amountToUse);
            $billing->paid_amount += $amountToUse;
        }

        // Se o saldo cobriu tudo, fica PAGO, senão PAGO_PARCIAL
        $billing->status = ($billing->remaining_amount <= 0) 
            ? BillingStatus::PAID 
            : BillingStatus::PARTIAL_PAID;
    }

    /**
     * Garante que o status condiz com o valor pago.
     */
    private function syncStatusWithPayment(Billing $billing): void
    {
        if ($billing->paid_amount >= $billing->total_amount) {
            $billing->status = BillingStatus::PAID;
        } elseif ($billing->paid_amount > 0) {
            $billing->status = BillingStatus::PARTIAL_PAID;
        }
    }
}