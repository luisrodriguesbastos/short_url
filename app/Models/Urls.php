<?php


namespace App\Models;


use App\Enums\StatusUrl;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class Urls
 * @package App\Models
 * @property $url
 * @property $short_url
 * @property $status
 * @property $created_at
 * @property $updated_at
 */
class Urls extends Model
{
    /**
     * @param $url
     */
    public function setUrl($url)
    {
        $this->url = $url;
    }

    /**
     * @return mixed
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return mixed
     */
    public function getShortUrl()
    {
        return $this->short_url;
    }

    /**
     * @param mixed $shortUrl
     */
    public function setShortUrl($shortUrl): void
    {
        $this->short_url = $shortUrl;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * @param Urls $register
     * @param $status
     */
    public function updateStatus(Urls $register, $status)
    {
        $now = new Carbon();
        $register->setUpdatedAt($now);
        $register->setStatus($status);
        $register->save();
    }

    public function expiredUrl(Urls $register)
    {
        $now = new Carbon();
        $register->setUpdatedAt($now->subMinutes(40));
        $register->save();
    }

    /**
     * @return mixed
     */
    public function getUpdatedAt()
    {
        return $this->updated_at;
    }

    /**
     * @param mixed $updated_at
     */
    public function setUpdatedAt($updated_at): void
    {
        $this->updated_at = $updated_at;
    }
}
