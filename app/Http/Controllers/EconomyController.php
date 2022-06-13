<?php

namespace App\Http\Controllers;

use App\Actions\EconomyAction;
use App\Helper\Model\filter\FilterBuilder;
use App\Http\Requests\AzuxRequest;
use App\Models\DadosCadastrais;
use App\Models\Economy;
use App\Repositories\Economy\EconomyContractInterface;
use App\Repository\Economy\EconomyRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Gate;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class EconomyController extends Controller
{

    public function __construct(
        protected EconomyContractInterface $economyContract
    )
    {
    }

    public function __invoke(Request $request)
    {
        try {
            $this->economyContract->execute($request);
        } catch (\Exception $exception) {
            return \response()->json([], ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
        }

    }

//    public function index(): JsonResponse
//    {
//
//        Economy::query()->select();
//        abort_if(Gate::denies('teste-index'), ResponseAlias::HTTP_FORBIDDEN, '403 Forbidden');
//
//        $result = DadosCadastrais::query()->limit(10)->get();
//
//        return response()->json($result, 200);
//
//    }
//
//
//    public function teste(Request $request): JsonResponse
//    {
//        try {
//            $data = (new EconomyRepository())->execute($request->all());
//            return \response()->json($data, ResponseAlias::HTTP_OK);
//        } catch (\Exception $exception) {
//            return \response()->json([], ResponseAlias::HTTP_INTERNAL_SERVER_ERROR);
//        }
//
//    }

}
