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
            ->select([
                'users.client_id as client_id',
                'dados_cadastrais.cliente as name',
                'users.email as email',
                DB::raw('COALESCE(personal_access_tokens.last_used_at, personal_access_tokens.created_at) as last_used_at'),
            ])
            ->orderByDesc(DB::raw('COALESCE(personal_access_tokens.last_used_at, personal_access_tokens.created_at)'))
            ->get()
            ->filter(static fn ($client) => $client->email !== '' && !str_starts_with($client->email, 'cli_'))
            ->groupBy('email')
            ->map(static function ($group, $email) {
                $latestClient = $group
                    ->sortByDesc('last_used_at')
                    ->first();

                $clientIds = $group->pluck('client_id')->map(static fn ($clientId) => (int) $clientId)->values()->all();

                return [
                    'client_id' => (int) $latestClient->client_id,
                    'client_ids' => $clientIds,
                    'name' => (string) $latestClient->name,
                    'email' => (string) $email,
                    'last_used_at_raw' => $latestClient->last_used_at,
                ];
            })
            ->sortByDesc('last_used_at_raw')
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