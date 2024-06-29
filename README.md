# Pobieranie i zapisywanie danych z TMDB API  

Do korzystania z API TMDB wymagane jest dodanie klucza `TMDB_API_KEY`do konfiguracji (.env lub config/tmdb.php) 

Komenda `tmdb:fetch-content` służy do pobierania i zapisywania danych z TMDB API (The Movie Database).

### Parametry
- `--movies=` (opcjonalny): Określa liczbę filmów do pobrania. Domyślnie ustawione na 50.
- `--series=` (opcjonalny): Określa liczbę seriali do pobrania. Domyślnie ustawione na 10.

### Przykład

```sh
php artisan tmdb:fetch-content --movies=50 --series=10
````

# Endpointy

#### Obsługiwane języki

API obsługuje następujące języki dla parametrów `language` i `api_language`:

- `pl` - Polski
- `en`- Angielski
- `de`- Niemiecki

## Gatunki

### `GET /api/genres` - zwraca gatunki

#### Parametry

- `perPage` (opcjonalny): Liczba wyników na stronę. Musi być liczbą i większą od 0.
- `page` (opcjonalny): Numer strony. Musi być liczbą i większą od 0.
- `column` (opcjonalny): Kolumny do zwrócenia. Musi być ciągiem znaków.
- `filters` (opcjonalny): Tablica filtrów do zastosowania. Może zawierać następujące pola:
    - `filters.name`: Filtr na podstawie nazwy gatunku. Musi być ciągiem znaków.
    - `filters.external_id`: Filtr na podstawie zewnętrznego identyfikatora TMDB. Musi być ciągiem znaków.
- `api_language` (opcjonalny): Język API. Musi być ciągiem znaków.
- `language` (opcjonalny): Języki do tłumaczenia wyników. Musi być ciągiem znaków, gdzie języki są oddzielone przecinkami.

#### Przykład
```sh
"https://example.com/api/genres?perPage=10&columns=name,external_id&filters[name]=comedy&api_language=pl&language=en,pl"
```
### `GET /api/genres/{genreId}` - zwraca gatunek

#### Parametry

- `api_language` (opcjonalny): Język API. Musi być ciągiem znaków.
- `language` (opcjonalny): Języki do tłumaczenia wyników. Musi być ciągiem znaków, gdzie języki są oddzielone przecinkami.

#### Przykład
```sh
"https://example.com/api/genres/1?api_language=pl&language=en,pl"
```

## Filmy
### `GET /api/movies` - zwraca filmy

#### Parametry

- `perPage` (opcjonalny): Liczba wyników na stronę. Musi być liczbą całkowitą i większą od 0.
- `page` (opcjonalny): Numer strony. Musi być liczbą i większą od 0.
- `column` (opcjonalny): Kolumny do zwrócenia. Musi być ciągiem znaków.
- `filters` (opcjonalny): Tablica filtrów do zastosowania. Może zawierać następujące pola:
    - `filters.title`: Filtr na podstawie tytułu. Musi być ciągiem znaków.
    - `filters.external_id`: Filtr na podstawie zewnętrznego identyfikatora. Musi być ciągiem znaków.
    - `filters.genre_id`: Filtr na podstawie identyfikatora gatunku. Musi być liczbą.
    - `filters.from_vote_average`: Filtr na podstawie minimalnej średniej głosów. Musi być liczbą.
    - `filters.to_vote_average`: Filtr na podstawie maksymalnej średniej głosów. Musi być liczbą.
    - `filters.from_vote_count`: Filtr na podstawie minimalnej liczby głosów. Musi być liczbą.
    - `filters.to_vote_count`: Filtr na podstawie maksymalnej liczby głosów. Musi być liczbą.
    - `filters.from_popularity`: Filtr na podstawie minimalnej popularności. Musi być liczbą.
    - `filters.to_popularity`: Filtr na podstawie maksymalnej popularności. Musi być liczbą.
    - `filters.from_release_date`: Filtr na podstawie minimalnej daty wydania. Musi być datą.
    - `filters.to_release_date`: Filtr na podstawie maksymalnej daty wydania. Musi być datą.
- `api_language` (opcjonalny): Język API. Musi być ciągiem znaków.
- `language` (opcjonalny): Języki do tłumaczenia wyników. Musi być ciągiem znaków, gdzie języki są oddzielone przecinkami.

#### Przykład

```sh
"https://example.com/api/movies?perPage=10&columns=title,external_id&filters[title]=Inception&filters[genre_id]=1,2&filters[from_release_date]=01-01-2023&filters[to_release_date]=31-12-2024&api_language=pl&language=en,pl"
```
### `GET /api/movies/{movieId}` - zwraca film

#### Parametry

- `api_language` (opcjonalny): Język API. Musi być ciągiem znaków.
- `language` (opcjonalny): Języki do tłumaczenia wyników. Musi być ciągiem znaków, gdzie języki są oddzielone przecinkami.

#### Przykład
```sh
"https://example.com/api/movies/1?api_language=pl&language=en,pl"
```
## Seriale

### `GET /api/series` - zwraca seriale

#### Parametry

- `perPage` (opcjonalny): Liczba wyników na stronę. Musi być liczbą całkowitą i większą od 0.
- `page` (opcjonalny): Numer strony. Musi być liczbą i większą od 0.
- `column` (opcjonalny): Kolumny do zwrócenia. Musi być ciągiem znaków.
- `filters` (opcjonalny): Tablica filtrów do zastosowania. Może zawierać następujące pola:
    - `filters.title`: Filtr na podstawie tytułu. Musi być ciągiem znaków.
    - `filters.external_id`: Filtr na podstawie zewnętrznego identyfikatora. Musi być ciągiem znaków.
    - `filters.genre_id`: Filtr na podstawie identyfikatora gatunku. Musi być liczbą.
    - `filters.from_vote_average`: Filtr na podstawie minimalnej średniej głosów. Musi być liczbą.
    - `filters.to_vote_average`: Filtr na podstawie maksymalnej średniej głosów. Musi być liczbą.
    - `filters.from_vote_count`: Filtr na podstawie minimalnej liczby głosów. Musi być liczbą.
    - `filters.to_vote_count`: Filtr na podstawie maksymalnej liczby głosów. Musi być liczbą.
    - `filters.from_popularity`: Filtr na podstawie minimalnej popularności. Musi być liczbą.
    - `filters.to_popularity`: Filtr na podstawie maksymalnej popularności. Musi być liczbą.
- `api_language` (opcjonalny): Język API. Musi być ciągiem znaków.
- `language` (opcjonalny): Języki do tłumaczenia wyników. Musi być ciągiem znaków, gdzie języki są oddzielone przecinkami.

#### Przykład użycia

```sh
"https://example.com/api/series?perPage=10&columns=title,external_id&filters[title]=Inception&filters[genre_id]=1,2&api_language=pl&language=en,pl"
```
### `GET /api/series/{serieId}` - zwraca serial

#### Parametry

- `api_language` (opcjonalny): Język API. Musi być ciągiem znaków.
- `language` (opcjonalny): Języki do tłumaczenia wyników. Musi być ciągiem znaków, gdzie języki są oddzielone przecinkami.

#### Przykład
```sh
"https://example.com/api/series/1?api_language=pl&language=en,pl"
```
