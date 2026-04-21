<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // 1. Envelopa tudo no Cache::remember
        // 'dashboard_data' é a chave, 300 são os 5 minutos em segundos
        return Cache::remember('dashboard_data', 300, function () 
        {
            $startOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d H:i:s');
            $startOfLastMonth = Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d H:i:s');
            $endOfLastMonth = Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d H:i:s');

            $result = DB::select("
                SELECT 
                    (SELECT SUM(partial_paid) FROM billings WHERE created_at >= '{$startOfMonth}') as total_mes_atual,
                    (SELECT SUM(partial_paid) FROM billings WHERE created_at BETWEEN '{$startOfLastMonth}' AND '{$endOfLastMonth}') as total_mes_anterior,
                    (SELECT SUM(items_sum.total - b.partial_paid) 
                    FROM billings b
                    JOIN (SELECT contract_id, SUM(quantity * unit_price) as total FROM contract_items GROUP BY contract_id) as items_sum 
                    ON b.contract_id = items_sum.contract_id
                    WHERE b.status IN ('pendente', 'pago_parcial')) as total_em_aberto,
                    (SELECT COUNT(*) FROM billings WHERE status = 'inadimplente') as total_inadimplentes,
                    (SELECT JSON_ARRAYAGG(JSON_OBJECT('status', status, 'total', cnt))
                    FROM (SELECT status, COUNT(*) as cnt FROM service_orders GROUP BY status) as os_stat) as os_distribution,
                    (SELECT JSON_ARRAYAGG(JSON_OBJECT('name', name, 'total_ativo', valor_contratos))
                    FROM (
                        SELECT c.name, SUM(ci.quantity * ci.unit_price) as valor_contratos
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

            $atual = (float) $result->total_mes_atual;
            $anterior = (float) $result->total_mes_anterior;
            $variacao = $anterior > 0 ? (($atual - $anterior) / $anterior) * 100 : ($atual > 0 ? 100 : 0);

            // Importante: O cache precisa retornar o ARRAY de dados
            return [
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
            ];
        });
    }
}
