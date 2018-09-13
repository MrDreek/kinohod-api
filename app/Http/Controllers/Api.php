<?php

namespace App\Http\Controllers;

use App\Cinema;
use App\City;
use App\Movie;
use App\Http\Requests\CodeRequest;
use App\Http\Requests\MovieIdReques;
use App\Http\Requests\NameRequest;
use App\Http\Requests\SeanceRequest;
use Illuminate\Routing\Controller;
use Ixudra\Curl\Facades\Curl;

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

        City::truncate();

        foreach (json_decode(gzdecode($response->get())) as $city) {
            $city = new City;
            foreach ($city as $key => $item) {
                $city->{$key} = $item;
            }
            $city->save();
        }

        return response('OK', 200);
    }

    /**
     * Метод возвращает код города из нашей базы который соответствует коду из сервиса kinohod
     * @param NameRequest $request
     * @return array
     */
    public function getCode(NameRequest $request)
    {
        $city = City::where('title', 'like', '%' . $request->name . '%')->first();

        if ($city === null) {
            return response()->json(['error' => 404, 'message' => 'Not found'], 404);
        }

        return $city->getCode();
    }

    /**
     * Метод подтягивает список фильмов и перезаписывает их в базу
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

        Movie::truncate();

        foreach (json_decode(gzdecode($response->get())) as $movies) {
            $movie = new Movie;
            foreach ($movies as $key => $item) {
                $movie->{$key} = $item;
            }
            $movie->save();
        }

        return response('OK', 200);
    }

    /**
     * Метод возвращает список фильмов с краткой ифнормацие по коду города
     * @param CodeRequest $request
     * @return array
     */
    public function getMovieListByCode(CodeRequest $request)
    {
        $key = config('app.key_kinohod');

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

        $movies = [];

        foreach (json_decode(gzdecode($response->content)) as $movie) {
            $movies[] = [
                'id' => $movie->id,
                'originalTitle' => $movie->originalTitle ?? null,
                'annotationFull' => $movie->annotationFull ?? null,
                'genres' => $movie->genres ?? null,
                'countries' => $movie->countries ?? null,
                'productionYear' => $movie->productionYear ?? null,
                'title' => $movie->title ?? null,
                'ageRestriction' => $movie->ageRestriction ?? null,
                'annotationShort' => $movie->annotationShort ?? null,
                'poster' => $movie->poster ?? null,
                'imdbId' => $movie->imdbId ?? null,
            ];
        }

        return $movies;
    }

    /**
     * Метод возвращает детальную информацию по id фильма | не используется
     * @param MovieIdReques $request
     * @return mixed
     */
    public function getMovieDetail(MovieIdReques $request)
    {
        $movie = Movie::where('id', (integer)$request->movieId)->first();

        if ($movie === null) {
            return response()->json(['error' => 404, 'message' => 'Not found'], 404);
        }

        return $movie;
    }

    /**
     * Метод возвращает список сеансов по id фильма
     * @param SeanceRequest $request
     * @return array
     */
    public function getSeances(SeanceRequest $request)
    {
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

    /**
     * Метод подтягивает кинотеатров городов и перезаписывает их в базу
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function getCinemaList()
    {
        $key = config('app.key_kinohod');
        $url = "https://api.kinohod.ru/api/data/2/$key/cinemas.json";

        $response = Curl::to($url);

        // если нужен прокси
        if (config('app.proxy')) {
            $response = $response->withProxy(config('app.proxy_url'), config('app.proxy_port'), config('app.proxy_type'), config('app.proxy_username'), config('app.proxy_password'));
        }

        $response = $response->returnResponseObject()->get();

        if ($response->status !== 200) {
            return response()->json(['error' => $response->status], $response->status);
        }

        Cinema::truncate();

        foreach (json_decode(gzdecode($response->content)) as $cinemaObj) {
            $cinema = new Cinema;
            foreach ($cinemaObj as $key => $item) {
                $cinema->{$key} = $item;
            }
            $cinema->save();
        }

        return response('OK', 200);
    }
}
