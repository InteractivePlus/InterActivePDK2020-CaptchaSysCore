<?php
namespace InteractivePlus\PDK2020CaptchaCore\Implementions\Storage;

use InteractivePlus\PDK2020CaptchaCore\Interfaces\Storages\CaptchaInfoStorage;
use InteractivePlus\PDK2020Core\Exceptions\PDKException;
use MysqliDb;

class CaptchaInfoStorageMySQLImpl implements CaptchaInfoStorage{
    private $_Database;
    public function __construct(MysqliDb $mysqliDb)
    {
        $this->_Database = $mysqliDb;
    }
    public function getDatabase() : MysqliDb{
        return $this->_Database;
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
        $insertData = array(
            'phrase' => strtolower($phrase),
            'gen_time' => $generationTime,
            'expire_time' => $expireTime,
            'clientAddr' => $clientAddr,
            'actionID' => $actionID
        );
        $insertState = $this->_Database->insert('captchas',$insertData);
        if(!$insertState){
            throw new PDKException(
                50007,
                __CLASS__ . ' insert error',
                array(
                    'errNo'=>$this->_Database->getLastErrno(),
                    'errMsg'=>$this->_Database->getLastError()
                )
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function checkCaptchaPhrase(
        string $phrase,
        int $actionID = 0,
        ?string $clientAddr = NULL
    ) : bool{
        $ctime = time();
        $this->_Database->where('phrase',strtolower($phrase));
        $this->_Database->where('actionID',$actionID);
        if(!empty($clientAddr)){
            $this->_Database->where('clientAddr',$clientAddr);
        }
        $this->_Database->where('expire_time',$ctime,'>');

        $count = $this->_Database->getValue('captchas','count(*)');
        if($count >= 1){
            return true;
        }
        return false;
    }

    /**
     * @inheritdoc
     */
    public function deleteCaptchaPhrase(
        string $phrase,
        int $actionID = 0,
        ?string $clientAddr = NULL
    ) : void{
        $this->_Database->where('phrase',strtolower($phrase));
        $this->_Database->where('actionID',$actionID);
        if(!empty($clientAddr)){
            $this->_Database->where('clientAddr',$clientAddr);
        }
        $deleteStatus = $this->_Database->delete('captchas');
        if(!$deleteStatus){
            throw new PDKException(
                50007,
                __CLASS__ . ' update error',
                array(
                    'errNo'=>$this->_Database->getLastErrno(),
                    'errMsg'=>$this->_Database->getLastError()
                )
            );
        }
    }

    /**
     * @inheritdoc
     */
    public function clearInvalidPhrases() : void{
        $this->_Database->where('expire_time',time(),'<=');
        $deleteStatus = $this->_Database->delete('captchas');
        if(!$deleteStatus){
            throw new PDKException(
                50007,
                __CLASS__ . ' update error',
                array(
                    'errNo'=>$this->_Database->getLastErrno(),
                    'errMsg'=>$this->_Database->getLastError()
                )
            );
        }
    }
}