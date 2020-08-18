<?php
namespace InteractivePlus\PDK2020CaptchaCore;
class CaptchaInfo{
    public $phrase = NULL;
    public $jpegData = NULL;
    public $width = 0;
    public $height = 0;
    public $expires = 0;
    public function __construct(
        string $phrase,
        string $jpeyData,
        int $width = 150,
        int $height = 60,
        int $expires = 0
    )
    {
        $this->phrase = $phrase;
        $this->jpegData = $jpeyData;
        $this->width = $width;
        $this->height = $height;
        $this->expires = $expires;
    }
    public function inlineSRC() : string{
        return 'data:image/jpeg;base64, ' . base64_encode($this->jpegData);
    }
}