<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $startOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
        $startOfLastMonth = Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d H:i:s');
        $endOfLastMonth = Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d H:i:s');

        // Esta query única busca os globais, os clientes e os status de OS usando JSON_ARRAYAGG ou subconsultas
        $result = DB::select("
            SELECT 
                -- 1. Totais de Faturamento (Baseado no partial_paid que é o valor real em conta)
                (SELECT SUM(partial_paid) FROM billings WHERE created_at >= '{$startOfMonth}') as total_mes_atual,
                (SELECT SUM(partial_paid) FROM billings WHERE created_at BETWEEN '{$startOfLastMonth}' AND '{$endOfLastMonth}') as total_mes_anterior,

                -- 2. Total em Aberto (O que falta pagar das faturas pendentes ou parciais)
                -- Valor Total do Contrato - Valor já Pago
                (SELECT SUM(items_sum.total - b.partial_paid) 
                 FROM billings b
                 JOIN (SELECT contract_id, SUM(quantity * unit_value) as total FROM contract_items GROUP BY contract_id) as items_sum 
                   ON b.contract_id = items_sum.contract_id
                 WHERE b.status IN ('pendente', 'pago_parcial')) as total_em_aberto,

                -- 3. Total Inadimplente (Contagem de faturas com status atrasado)
                (SELECT COUNT(*) FROM billings WHERE status = 'inadimplente') as total_inadimplentes,

                -- 4. Distribuição de OS (Agrupado em uma string JSON para manter a query única)
                (SELECT JSON_ARRAYAGG(JSON_OBJECT('status', status, 'total', cnt))
                 FROM (SELECT status, COUNT(*) as cnt FROM service_orders GROUP BY status) as os_stat) as os_distribution,

                -- 5. Top 5 Clientes (JSON com os dados dos clientes e soma dos contratos ativos)
                (SELECT JSON_ARRAYAGG(JSON_OBJECT('name', name, 'total_ativo', valor_contratos))
                 FROM (
                    SELECT c.name, SUM(ci.quantity * ci.unit_value) as valor_contratos
                    FROM clients c
                    JOIN contracts ct ON c.id = ct.client_id
                    JOIN contract_items ci ON ct.id = ci.contract_id
                    WHERE ct.deleted_at IS NULL 
                      AND (ct.date_end IS NULL OR ct.date_end >= CURDATE())
                    GROUP BY c.id, c.name
                    ORDER BY valor_contratos DESC
                    LIMIT 5
                 ) as top_clients) as top_5_clients
        ")[0];

        // Processamento da variação percentual no PHP (após a query única)
        $atual = (float) $result->total_mes_atual;
        $anterior = (float) $result->total_mes_anterior;
        $variacao = $anterior > 0 ? (($atual - $anterior) / $anterior) * 100 : ($atual > 0 ? 100 : 0);

        return response()->json([
            'faturamento' => [
                'mes_atual' => round($atual, 2),
                'mes_anterior' => round($anterior, 2),
                'variacao_percentual' => round($variacao, 2) . '%'
            ],
            'cobrancas' => [
                'total_em_aberto' => round((float) $result->total_em_aberto, 2),
                'total_inadimplentes' => (int) $result->total_inadimplentes,
            ],
            'top_clientes' => json_decode($result->top_5_clients),
            'distribuicao_os' => json_decode($result->os_distribution)
        ]);
    }
}
