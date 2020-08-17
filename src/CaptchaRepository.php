<?php
namespace InteractivePlus\PDK2020CaptchaCore;

use Gregwar\Captcha\CaptchaBuilder;
use Gregwar\Captcha\PhraseBuilder;
use InteractivePlus\PDK2020CaptchaCore\Interfaces\Storages\CaptchaInfoStorage;

class CaptchaRepository implements CaptchaInfoStorage{
    private $_Storage;
    public function __construct(CaptchaInfoStorage $storage){
        $this->_Storage = $storage;
    }
    public function getStorage() : CaptchaInfoStorage{
        return $this->_Storage;
    }
    
    public function generateCaptcha(
        int $numDigits = 5,
        ?string $phraseRewrite = NULL,
        int $width = 150,
        int $height = 40
    ) : CaptchaInfo{
        $captchaBuilder = NULL;
        if(empty($phraseRewrite)){
            $phraseBuilder = new PhraseBuilder($numDigits);
            $captchaBuilder = new CaptchaBuilder(NULL,$phraseBuilder);
        }else{
            $captchaBuilder = new CaptchaBuilder($phraseRewrite);
        }
        $captchaBuilder->buildAgainstOCR($width,$height);
        return new CaptchaInfo(
            $captchaBuilder->getPhrase(),
            $captchaBuilder->get(),
            $width,
            $height
        );
    }

    public function generateAndSaveCaptcha(
        int $availableDurationInSec,
        int $actionID,
        ?string $clientAddr = NULL,
        int $numDigits = 5,
        ?string $phraseRewrite = NULL,
        int $width = 150,
        int $height = 40
    ) : CaptchaInfo{
        $captchaInfo = $this->generateCaptcha(
            $numDigits,
            $phraseRewrite,
            $width,
            $height
        );
        $ctime = time();
        $this->addCaptchaInfo(
            $captchaInfo->phrase,
            $ctime,
            $ctime + $availableDurationInSec,
            $actionID,
            $clientAddr
        );
        return $captchaInfo;
    }

    /**
     * @inheritdoc
     */
    public function addCaptchaInfo(
        string $phrase,
        int $generationTime,
        int $expireTime,
        int $actionID = 0,
        ?string $clientAddr = NULL
    ) : void{
        $this->_Storage->addCaptchaInfo(
            $phrase,
            $generationTime,
            $expireTime,
            $actionID,
            $clientAddr
        );
    }
    
    /**
     * @inheritdoc
     */
    public function checkCaptchaPhrase(
        string $phrase,
        int $actionID = 0,
        ?string $clientAddr = NULL
    ) : bool{
        return $this->checkCaptchaPhrase(
            $phrase,
            $actionID,
            $clientAddr
        );
    }
    
    /**
     * @inheritdoc
     */
    public function deleteCaptchaPhrase(
        string $phrase,
        int $actionID = 0,
        ?string $clientAddr = NULL
    ) : void{
        $this->_Storage->deleteCaptchaPhrase(
            $phrase,
            $actionID,
            $clientAddr
        );
    }
    
    /**
     * @inheritdoc
     */
    public function clearInvalidPhrases() : void{
        $this->_Storage->clearInvalidPhrases();
    }
}