<?php
namespace InteractivePlus\PDK2020CaptchaCore\Interfaces\Storages;
interface CaptchaInfoStorage{
    public function addCaptchaInfo(
        string $phrase,
        int $generationTime,
        int $expireTime,
        int $actionID = 0,
        ?string $clientAddr = NULL
    ) : void;
    public function checkCaptchaPhrase(
        string $phrase,
        int $actionID = 0,
        ?string $clientAddr = NULL
    ) : bool;
    public function deleteCaptchaPhrase(
        string $phrase,
        int $actionID = 0,
        ?string $clientAddr = NULL
    ) : void;
    public function clearInvalidPhrases() : void;
}