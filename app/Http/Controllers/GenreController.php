<?php

namespace App\Http\Controllers;

use App\Http\Requests\GenreIndexRequest;
use App\Http\Requests\GenreShowRequest;
use App\Repositories\GenreRepository;
use App\StringParser;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Log;
use Throwable;

class GenreController extends Controller
{
    private GenreRepository $genreRepository;

    public function __construct(GenreRepository $genreRepository)
    {
        $this->genreRepository = $genreRepository;
    }

    public function index(GenreIndexRequest $request): JsonResponse
    {
        try {
            $perPage = $request->input('perPage', 15);
            $columns = StringParser::parseStringToArray($request->input('column', ['*']));
            $filters = $request->input('filters', []);
            $languages = StringParser::parseStringToArray($request->input('language', [app()->getLocale()]));

            $paginate = $this->genreRepository->paginate($perPage, $filters, $columns, $languages);

            return response()->json($paginate);
        } catch (Throwable $e) {
            Log::error('An unexpected error occurred while fetching genres', ['error' => $e->getMessage(), 'exception' => $e]);

            return response()->json(['error' => Lang::get('messages.unexpected_error_get_genres')], 500);
        }
    }

    public function show(int $genreId, GenreShowRequest $request): JsonResponse
    {
        try {
            $genre = $this->genreRepository->findOrFail($genreId);

            $languages = StringParser::parseStringToArray($request->input('language', [app()->getLocale()]));

            $translatedGenre = $genre->translate($languages);

            return response()->json(['data' => $translatedGenre]);
        } catch (ModelNotFoundException) {
            return response()->json(['error' => Lang::get('messages.genre_not_found')], 404);
        } catch (Throwable $e) {
            Log::error('An unexpected error occurred while fetching genre', ['error' => $e->getMessage(), 'exception' => $e]);

            return response()->json(['error' => Lang::get('messages.unexpected_error_get_genre')], 500);
        }
    }
}
