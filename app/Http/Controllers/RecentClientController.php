<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Helpers\ResponseJsonMessage;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RecentClientController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $limit = max(1, min((int) $request->integer('limit', 25), 100));

        $clients = DB::table('users')
            ->join('dados_cadastrais', 'dados_cadastrais.cod_smart_cliente', '=', 'users.client_id')
            ->join('personal_access_tokens', function ($join) {
                $join->on('personal_access_tokens.tokenable_id', '=', 'users.id')
                    ->where('personal_access_tokens.tokenable_type', User::class);
            })
            ->whereNotNull('users.client_id')
            ->selectRaw(
                "users.client_id as client_id, dados_cadastrais.cliente as name, SUBSTRING_INDEX(GROUP_CONCAT(users.email ORDER BY COALESCE(personal_access_tokens.last_used_at, personal_access_tokens.created_at) DESC SEPARATOR ',') ,',',1) as email, MAX(COALESCE(personal_access_tokens.last_used_at, personal_access_tokens.created_at)) as last_used_at"
            )
            ->groupBy('users.client_id', 'dados_cadastrais.cliente')
            ->orderByDesc(DB::raw('MAX(COALESCE(personal_access_tokens.last_used_at, personal_access_tokens.created_at))'))
            ->get()
            ->groupBy('name')
            ->map(static function ($group, $name) {
                $clientIds = $group->pluck('client_id')->map(static fn ($clientId) => (int) $clientId)->all();

                // choose representative email from first row (rows are ordered by last_used_at desc)
                $email = (string) ($group->first()->email ?? '');

                return [
                    'client_id' => $clientIds[0],
                    'client_ids' => $clientIds,
                    'name' => (string) $name,
                    'email' => $email,
                    'last_used_at_raw' => $group->max('last_used_at'),
                ];
            })
            ->sortByDesc('last_used_at_raw')
            ->filter(static fn ($c) => $c['email'] !== '' && !str_starts_with($c['email'], 'cli_'))
            ->take($limit)
            ->values()
            ->map(static fn ($client) => [
                'client_id' => (int) $client['client_id'],
                'client_ids' => $client['client_ids'],
                'name' => (string) $client['name'],
                'email' => (string) $client['email'],
                'last_used_at' => Carbon::parse($client['last_used_at_raw'])->format('d/m/Y H:i:s'),
            ]);

        return ResponseJsonMessage::withData($clients);
    }
}