<?php


namespace App\Http\Controllers\Api;


use App\Http\Controllers\Controller;
use App\Services\UrlServices;
use Illuminate\Support\Facades\Response;

class ShortenController extends Controller
{
    /**
     * @var UrlServices
     */
    private $urlService;
    /**
     * ShortenController constructor.
     */
    public function __construct()
    {
        $this->urlService = new UrlServices();
        parent::__construct();
    }

    /**
     * @param $url
     * @return \Illuminate\Http\JsonResponse
     */
    public function shorten($url)
    {
        try {
            $url = $this->urlService->buildShortenUrl($url);
            return Response::json($url, 200);
        } catch (\Exception $ex) {
            return Response::json([
                'type' => 'error',
                'message' => $ex->getMessage()
            ], 404);
        }
    }

    /**
     * @param $hash
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function getUrl($hash)
    {
        try {
            $link = $this->urlService->getLongUrl($hash);
            return redirect()->away($link);
        } catch (\Exception $ex) {
            return Response::json([
                'type' => 'error',
                'message' => $ex->getMessage()
            ], 404);
        }
    }
}
