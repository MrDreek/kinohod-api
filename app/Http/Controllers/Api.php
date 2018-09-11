<?php

namespace App\Http\Controllers;

use App\City;
use App\Movie;
use Illuminate\Routing\Controller;
use Ixudra\Curl\Facades\Curl;
use Illuminate\Http\Request;

class Api extends Controller
{
    /**
     * Метод подтягивает список городов и перезаписывает их в базу
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function getCityList()
    {
        $key = config('app.key_kinohod');
        $url = "https://api.kinohod.ru/api/data/2/$key/cities.json";

        $response = Curl::to($url);

        // если нужен прокси
        if (config('app.proxy')) {
            $response = $response->withProxy(config('app.proxy_url'), config('app.proxy_port'), config('app.proxy_type'), config('app.proxy_username'), config('app.proxy_password'));
        }

        $response = $response->get();

        $cities = json_decode(gzdecode($response));

        City::truncate();

        foreach ($cities as $cityObj) {
            $city = new City;
            $city->utcOffset = $cityObj->utcOffset;
            $city->title = $cityObj->title;
            $city->code = $cityObj->id;
            $city->alias = $cityObj->alias;
            $city->location = $cityObj->location;
            $city->save();
        }

        return response('OK', 200);
    }

    /**
     * Метод возвращает код города из нашей базы который соответствует коду из сервиса kinohod
     * @param Request $request
     * @return array
     */
    public function getCode(Request $request): array
    {
        $city = City::where('title', 'like', '%' . $request->input('name') . '%')->firstOrFail();
        return ['code' => $city->code];
    }

    /**
     * Метод возвращает список фильмов с краткой ифнормацие по коду города
     * @param $code
     * @return array
     */
    public function getMovieListByCode($code): array
    {
        $key = config('app.key_kinohod');
        $url = "https://api.kinohod.ru/api/data/2/$key/city/$code/running/week.json";

        $response = Curl::to($url);

        // если нужен прокси
        if (config('app.proxy')) {
            $response = $response->withProxy(config('app.proxy_url'), config('app.proxy_port'), config('app.proxy_type'), config('app.proxy_username'), config('app.proxy_password'));
        }

        $response = $response->get();

        $moviesJson = json_decode(gzdecode($response));

        $movies = [];

        foreach ($moviesJson as $movieJson) {
            $movies[] = [
                'id' => $movieJson->id,
                'originalTitle' => $movieJson->originalTitle,
                'annotationFull' => $movieJson->annotationFull,
                'genres' => $movieJson->genres,
                'countries' => $movieJson->countries,
                'productionYear' => $movieJson->productionYear,
                'title' => $movieJson->title,
                'ageRestriction' => $movieJson->ageRestriction,
                'annotationShort' => $movieJson->annotationShort,
                'poster' => $movieJson->poster,
            ];
        }

        return $movies;
    }

    /**
     * Метод подтягивает список городов и перезаписывает их в базу
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function getMovieList()
    {
        $key = config('app.key_kinohod');
        $url = "https://api.kinohod.ru/api/data/2/$key/running.json";

        $response = Curl::to($url);

        // если нужен прокси
        if (config('app.proxy')) {
            $response = $response->withProxy(config('app.proxy_url'), config('app.proxy_port'), config('app.proxy_type'), config('app.proxy_username'), config('app.proxy_password'));
        }

        $response = $response->get();

        $moviesJson = json_decode(gzdecode($response));

        Movie::truncate();

        foreach ($moviesJson as $moviesObj) {
            $movie = new Movie;
            $movie->originalTitle = $moviesObj->originalTitle;
            $movie->annotationFull = $moviesObj->annotationFull;
            $movie->genres = $moviesObj->genres;
            $movie->id = $moviesObj->id;
            $movie->countries = $moviesObj->countries;
            $movie->productionYear = $moviesObj->productionYear;
            $movie->title = $moviesObj->title;
            $movie->ageRestriction = $moviesObj->ageRestriction;
            $movie->annotationShort = $moviesObj->annotationShort;
            $movie->poster = $moviesObj->poster;
            $movie->trailers = $moviesObj->trailers;
            $movie->premiereDateWorld = $moviesObj->premiereDateWorld;
            $movie->imdbId = $moviesObj->imdbId;
            $movie->directors = $moviesObj->directors;
            $movie->duration = $moviesObj->duration;
            $movie->save();
        }

        return response('OK', 200);
    }

    /**
     * Метод возвращает детальную информацию по id фильма
     * @param $id
     * @return mixed
     */
    public function getMovieDetail($id)
    {
        return Movie::where('id', '=', (integer)$id)->firstOrFail();
    }

    /**
     * Метод возвращает список сеансов по id фильма
     * @param $code
     * @param $movieId
     * @return array
     */
    public function getSeances($code, $movieId)
    {
        $key = config('app.key_kinohod');
        $url = "https://api.kinohod.ru/api/data/2/$key/city/$code/seances.json?movieId=$movieId";

        $response = Curl::to($url);

        // если нужен прокси
        if (config('app.proxy')) {
            $response = $response->withProxy(config('app.proxy_url'), config('app.proxy_port'), config('app.proxy_type'), config('app.proxy_username'), config('app.proxy_password'));
        }

        $response = $response->get();

        $seancesJson = json_decode($response);

        $seances = [];

        foreach ($seancesJson as $seanceJson) {
            $seances[] = [
                'id' => $seanceJson->id,
                'hallId' => $seanceJson->hallId,
                'startTime' => $seanceJson->startTime,
                'languageId' => $seanceJson->languageId,
                'subtitleId' => $seanceJson->subtitleId,
                'groupName' => $seanceJson->groupName,
                'time' => $seanceJson->time,
                'formats' => $seanceJson->formats,
                'minPrice' => $seanceJson->minPrice,
                'maxPrice' => $seanceJson->maxPrice,
                'date' => $seanceJson->date,
                'cinemaId' => $seanceJson->cinemaId,
            ];
        }

        return $seances;
    }
}
