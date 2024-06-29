<?php

namespace App\Http\Controllers;

use App\Http\Requests\SerieIndexRequest;
use App\Http\Requests\SerieShowRequest;
use App\Repositories\SerieRepository;
use App\StringParser;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Throwable;

class SerieController extends Controller
{
    private SerieRepository $serieRepository;

    public function __construct(SerieRepository $serieRepository)
    {
        $this->serieRepository = $serieRepository;
    }

    public function index(SerieIndexRequest $request): JsonResponse
    {
        try {
            $perPage = $request->input('perPage', 15);
            $columns = StringParser::parseStringToArray($request->input('column', ['*']));
            $filters = $request->input('filters', []);
            $languages = StringParser::parseStringToArray($request->input('language', [app()->getLocale()]));
            $sortBy = $request->input('sortBy', 'title');
            $sortDirection = $request->input('sortDirection', 'asc');

            $paginate = $this->serieRepository->paginate($perPage, $filters, $columns, $languages, $sortBy, $sortDirection);

            return response()->json($paginate);
        } catch (Throwable $e) {
            Log::error('An unexpected error occurred while fetching series', ['error' => $e->getMessage(), 'exception' => $e]);

            return response()->json(['error' => Lang::get('messages.unexpected_error_get_series')], 500);
        }
    }

    public function show(int $serieId, SerieShowRequest $request): JsonResponse
    {
        try {
            $serie = $this->serieRepository->findOrFail($serieId);

            $languages = StringParser::parseStringToArray($request->input('language', [app()->getLocale()]));

            $translatedSerie = $serie->translate($languages);

            return response()->json(['data' => $translatedSerie]);
        } catch (ModelNotFoundException) {
            return response()->json(['error' => Lang::get('messages.serie_not_found')], 404);
        } catch (Throwable $e) {
            Log::error('An unexpected error occurred while getting serie', ['error' => $e->getMessage(), 'exception' => $e]);

            return response()->json(['error' => Lang::get('messages.unexpected_error_get_serie')], 500);
        }
    }

}
