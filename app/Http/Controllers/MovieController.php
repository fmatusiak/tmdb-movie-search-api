<?php

namespace App\Http\Controllers;

use App\Http\Requests\MovieIndexRequest;
use App\Http\Requests\MovieShowRequest;
use App\Repositories\MovieRepository;
use App\StringParser;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class MovieController extends Controller
{
    private MovieRepository $movieRepository;

    public function __construct(MovieRepository $movieRepository)
    {
        $this->movieRepository = $movieRepository;
    }

    public function index(MovieIndexRequest $request): JsonResponse
    {
        try {
            $perPage = $request->input('perPage', 15);
            $columns = StringParser::parseStringToArray($request->input('column', ['*']));
            $filters = $request->input('filters', []);
            $languages = StringParser::parseStringToArray($request->input('language', [app()->getLocale()]));

            $paginate = $this->movieRepository->paginate($perPage, $filters, $columns, $languages);

            return response()->json($paginate);
        } catch (Exception $e) {
            Log::error('An unexpected error occurred while get movies', ['error' => $e->getMessage(), 'exception' => $e]);

            return response()->json(['error' => 'An unexpected error occurred while get movies'], 500);
        }
    }

    public function show(int $movieId, MovieShowRequest $request): JsonResponse
    {
        try {
            $movie = $this->movieRepository->findOrFail($movieId);

            $languages = StringParser::parseStringToArray($request->input('language', [app()->getLocale()]));

            $translatedMovie = $movie->translate($languages);

            return response()->json(['data' => $translatedMovie]);
        } catch (ModelNotFoundException) {
            return response()->json(['error' => 'Movie not found'], 404);
        } catch (Exception $e) {
            Log::error('An unexpected error occurred while get movie', ['error' => $e->getMessage(), 'exception' => $e]);

            return response()->json(['error' => 'An unexpected error occurred while get movie'], 500);
        }
    }
}
