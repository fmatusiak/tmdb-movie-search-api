<?php

namespace App\Console\Commands;

use App\Exceptions\TMDBApiException;
use App\Repositories\GenreRepository;
use App\Services\GenreService;
use App\Services\MovieService;
use App\Services\SerieService;
use App\TMDBApiLanguage;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Contracts\Console\Isolatable;
use Illuminate\Support\Facades\Log;

class TMDBFetchContent extends Command implements Isolatable
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tmdb:fetch-content {--movies=50 : Number of movies to fetch} {--series=10 : Number of series to fetch}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fetch content from TMDB api';

    private MovieService $movieService;

    private GenreService $genreService;

    private SerieService $serieService;

    private GenreRepository $genreRepository;

    private array $translateToLanguages = [
        TMDBApiLanguage::English,
        TMDBApiLanguage::Polish,
        TMDBApiLanguage::Deutsch,
    ];

    public function __construct(
        MovieService    $movieService,
        SerieService    $serieService,
        GenreService    $genreService,
        GenreRepository $genreRepository,
    )
    {
        parent::__construct();

        $this->movieService = $movieService;
        $this->serieService = $serieService;
        $this->genreService = $genreService;
        $this->genreRepository = $genreRepository;
    }

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->fetchAndSaveContent();
    }

    private function fetchAndSaveContent(): void
    {
        $this->fetchAndSaveGenres(TMDBApiLanguage::English);
        $this->fetchAndSaveGenres(TMDBApiLanguage::Polish);
        $this->fetchAndSaveGenres(TMDBApiLanguage::Deutsch);

        $moviesCount = $this->option('movies');
        $seriesCount = $this->option('series');

        $this->fetchAndSaveTopRatedMovies(TMDBApiLanguage::English, $moviesCount);
        $this->fetchAndSaveTopRatedSeries(TMDBApiLanguage::English, $seriesCount);
    }

    private function fetchAndSaveGenres(TMDBApiLanguage $apiLanguage): void
    {
        try {
            $genresData = $this->genreService->fetchGenresFromTMDBApi($apiLanguage);

            $this->saveGenres($genresData, $apiLanguage);
        } catch (TMDBApiException $e) {
            $this->logError("TMDB API error for language '{$apiLanguage->value}': " . $e->getMessage(), $e);
        } catch (Exception $e) {
            $this->logError("Error fetching and saving genres for language '$apiLanguage->value': " . $e->getMessage(), $e);
        }
    }

    /**
     * @throws Exception
     */
    private function saveGenres(array $genresData, TMDBApiLanguage $apiLanguage): void
    {
        $language = $apiLanguage->value;

        $this->info("Fetched " . count($genresData) . " genres for '{$language}' language");

        foreach ($genresData as $genreData) {
            $this->genreRepository->createOrUpdateFromTMDBData($genreData, $language);
        }
    }

    private function logError(string $errorMessage, Exception $exception): void
    {
        Log::error($errorMessage, ['exception' => $exception]);

        $this->error($errorMessage);
    }

    private function fetchAndSaveTopRatedMovies(TMDBApiLanguage $apiLanguage, int $totalMoviesToFetch = 50): void
    {
        try {
            $language = $apiLanguage->value;

            $popularMovies = $this->movieService->fetchTopRatedMoviesFromTMDBApi($apiLanguage, $totalMoviesToFetch);

            $this->info("Fetched " . count($popularMovies) . " movies for '{$language}' language");

            $this->saveTMDBContent($this->movieService, $popularMovies, $language);

            $this->fetchAndSaveTranslationsForTMDBContent($this->movieService, $popularMovies, $this->translateToLanguages);
        } catch (TMDBApiException $e) {
            $this->logError("TMDB API error for language '{$language}'" . $e->getMessage(), $e);
        } catch (Exception $e) {
            $this->logError("Error fetching and saving movies for language '$language': " . $e->getMessage(), $e);
        }
    }

    /**
     * @throws Exception
     */
    private function saveTMDBContent(object $service, array $data, string $language): void
    {
        $chunkSize = 100;
        $chunks = array_chunk($data, $chunkSize);

        foreach ($chunks as $chunk) {
            foreach ($chunk as $TMDBData) {
                $service->createOrUpdateTMDB($TMDBData, $language);
            }
        }
    }

    /**
     * @throws Exception
     */
    private function fetchAndSaveTranslationsForTMDBContent(object $service, array $data, array $translateToLanguages = []): void
    {
        $chunkSize = 100;
        $chunks = array_chunk($data, $chunkSize);

        $this->info("Fetching translations");

        foreach ($chunks as $chunk) {
            foreach ($chunk as $TMDBData) {
                $service->fetchAndSaveTranslations($TMDBData, $translateToLanguages);
            }
        }

        $this->info("Fetched translations");
    }

    private function fetchAndSaveTopRatedSeries(TMDBApiLanguage $apiLanguage, int $totalSeriesToFetch = 10): void
    {
        try {
            $language = $apiLanguage->value;

            $popularSeries = $this->serieService->fetchTopRatedFromTMDBApi($apiLanguage, $totalSeriesToFetch);

            $this->info("Fetched " . count($popularSeries) . " series for '{$language}' language");

            $this->saveTMDBContent($this->serieService, $popularSeries, $language);

            $this->fetchAndSaveTranslationsForTMDBContent($this->serieService, $popularSeries, $this->translateToLanguages);
        } catch (TMDBApiException $e) {
            $this->logError("TMDB API error for language '{$language}' : " . $e->getMessage(), $e);
        } catch (Exception $e) {
            $this->logError("Error fetching and saving series for language '$language': " . $e->getMessage(), $e);
        }
    }

}
