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
    public function getCode(Request $request)
    {
        if ($request->name === null) {
            return response()->json(['error' => 400, 'message' => 'name is required params'], 400);
        }

        $city = City::where('title', 'like', '%' . $request->name . '%')->first();

        if ($city === null) {
            return response()->json(['error' => 404, 'message' => 'Not found'], 404);
        }

        return $city->getCode();
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
     * Метод возвращает список фильмов с краткой ифнормацие по коду города
     * @param Request $request
     * @return array
     */
    public function getMovieListByCode(Request $request)
    {
        $key = config('app.key_kinohod');

        if ($request->code === null) {
            return response()->json(['error' => 400, 'message' => 'code is required params'], 400);
        }
        $url = "https://api.kinohod.ru/api/data/2/$key/city/" . $request->code . '/running/week.json';

        $response = Curl::to($url);

        // если нужен прокси
        if (config('app.proxy')) {
            $response = $response->withProxy(config('app.proxy_url'), config('app.proxy_port'), config('app.proxy_type'), config('app.proxy_username'), config('app.proxy_password'));
        }

        $response = $response->returnResponseObject()->get();

        if ($response->status !== 200) {
            return response()->json(['error' => $response->status], $response->status);
        }

        $moviesJson = json_decode(gzdecode($response->content));

        $movies = [];

        foreach ($moviesJson as $movieJson) {
            $movies[] = [
                'id' => $movieJson->id,
                'originalTitle' => $movieJson->originalTitle ?? null,
                'annotationFull' => $movieJson->annotationFull ?? null,
                'genres' => $movieJson->genres ?? null,
                'countries' => $movieJson->countries ?? null,
                'productionYear' => $movieJson->productionYear ?? null,
                'title' => $movieJson->title ?? null,
                'ageRestriction' => $movieJson->ageRestriction ?? null,
                'annotationShort' => $movieJson->annotationShort ?? null,
                'poster' => $movieJson->poster ?? null,
                'imdbId' => $movieJson->imdbId ?? null,
            ];
        }

        return $movies;
    }

    /**
     * Метод возвращает детальную информацию по id фильма | не используется
     * @param Request $request
     * @return mixed
     */
    public function getMovieDetail(Request $request)
    {
        if ($request->movieId === null) {
            return response()->json(['error' => 400, 'message' => 'movieId is required params'], 400);
        }

        $movie = Movie::where('id', (integer)$request->movieId)->first();

        if ($movie === null) {
            return response()->json(['error' => 404, 'message' => 'Not found'], 404);
        }

        return $movie;
    }

    /**
     * Метод возвращает список сеансов по id фильма
     * @param Request $request
     * @return array
     */
    public function getSeances(Request $request)
    {
        if ($request->code === null || $request->movieId === null) {
            return response()->json(['error' => 400, 'message' => 'code and movieId is required params'], 400);
        }

        $key = config('app.key_kinohod');
        $url = "https://api.kinohod.ru/api/data/2/$key/city/" . $request->code . '/seances.json?movieId=' . $request->movieId . '&_fields=id,hallId,startTime,languageId,subtitleId,groupName,time,formats,minPrice,maxPrice,date,cinemaId';

        $response = Curl::to($url);

        // если нужен прокси
        if (config('app.proxy')) {
            $response = $response->withProxy(config('app.proxy_url'), config('app.proxy_port'), config('app.proxy_type'), config('app.proxy_username'), config('app.proxy_password'));
        }

        $response = $response->returnResponseObject()->get();


        if ($response->status !== 200) {
            return response()->json(['error' => $response->status], $response->status);
        }

        return json_decode($response->content);
    }
}
