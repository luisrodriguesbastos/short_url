<?php


namespace App\Services;


use App\Enums\StatusUrl;
use App\Models\Urls;
use Carbon\Carbon;

class UrlServices
{
    /**
     * @var Urls
     */
    private $urlModel;

    /**
     * UrlServices constructor.
     */
    public function __construct()
    {
        $this->urlModel = new Urls();
    }

    /**
     * @param $url
     * @return string
     * @throws \Exception
     */
    public function buildShortenUrl($url)
    {
        $this->checkValidUrl($url);
        $hash = $this->shortenUrl($url);
        /**
         * @var $register Urls
         */
        $register = $this->checkExists($hash);
        if (empty($register)) {
            $this->newUrl($hash, $url);
        } else {
            $this->urlModel->updateStatus($register, StatusUrl::ACTIVE);
        }
        return env('APP_URL') . $hash;
    }

    /**
     * @param $hash
     * @param $url
     */
    private function newUrl($hash, $url)
    {
        $this->urlModel->setShortUrl($hash);
        $this->urlModel->setStatus(StatusUrl::ACTIVE);
        $this->urlModel->setUrl($url);
        $this->urlModel->save();
    }

    /**
     * @param $hash
     * @return mixed
     */
    public function checkExists($hash)
    {
        return Urls::where('short_url', $hash)->first();
    }

    /**
     * Poderia ter sido feito uma validação no request mas começaria deixar muito extenso
     * @param $url
     * @throws \Exception
     */
    public function checkValidUrl($url)
    {
        if (! preg_match("/\b(?:(?:https?|ftp) :\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i", $url)) {
            throw new \Exception('URL invalida.');
        }
    }

    /**
     * @param $url
     * @return false|string
     */
    public function shortenUrl($url)
    {
        return substr(sha1($url), 0, 6);
    }

    /**
     * @param $hash
     * @return string
     * @throws \Exception
     */
    public function getLongUrl($hash)
    {
        /**
         * @var $register Urls
         */
        $register = $this->checkExists($hash);

        if (empty($register) || $register->getStatus() !== StatusUrl::ACTIVE) {
            throw new \Exception('Infelizmente não conseguimos realizar sua solicitação');
        }

        $this->checkValidity($register);

        return 'https://' . $register->getUrl();
    }

    /**
     * @param Urls $register
     * @throws \Exception
     */
    public function checkValidity(Urls $register)
    {
        $now = new Carbon();
        $diffMinutes = $now->diffInMinutes($register->getUpdatedAt());
        if ($diffMinutes >= config('url.duration_minutes')) {
            $this->urlModel->updateStatus($register, StatusUrl::INACTIVE);
            throw new \Exception('Sua URL expirou.');
        }
    }

}
