<?php

namespace Tests\Http\Controllers\Api;

use App\Enums\StatusUrl;
use App\Models\Urls;
use App\Services\UrlServices;
use Tests\TestCase;

class ShortenControllerTest extends TestCase
{

    /**
     * @var UrlServices
     */
    private $urlService;
    /**
     * @var string
     */
    private $url = 'www.studos.com.br';

    /**
     * ShortenController constructor.
     */
    public function __construct()
    {
        $this->urlService = new UrlServices();
        parent::__construct();
    }

    /**
     * @return false|string
     * @throws \Exception
     */
    public function testShortenUrl()
    {
        $hash = $this->urlService->shortenUrl($this->url);
        $this->validHash($hash);
        $this->assertIsString($hash);
        return $hash;
    }

    /**
     * @throws \Exception
     */
    public function testShorten()
    {
        $response = $this->urlService->buildShortenUrl($this->url);
        $this->assertIsString($response);
    }

    /**
     * @depends testShorten
     */
    public function testUrlExpired()
    {
        $this->expectException(\Exception::class);
        $hash = $this->urlService->shortenUrl($this->url);
        $urlModel = new Urls();
        /**
         * @var $register Urls
         */
        $register = $this->urlService->checkExists($hash);
        $this->assertIsObject($register);
        $urlModel->expiredUrl($register);
        $this->urlService->getLongUrl($hash);
    }

    /**
     * @param $response
     * @throws \Exception
     */
    public function validHash($response)
    {
        $len = strlen($response);
        if ($len > 10 || $len < 5) {
            throw new \Exception('teste');
        }
    }
}
