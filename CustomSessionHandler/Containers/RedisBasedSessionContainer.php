<?php
require_once __DIR__ . '/SessionContainerInterface.php';
require_once __DIR__ . '/SessionContainerHelper.php';

/**
 * Class for using Redis based Session Container
 *
 * @category   Session
 * @package    Session Handlers
 * @author     Ramesh Narayan Jangid
 * @copyright  Ramesh Narayan Jangid
 * @version    Release: @1.0.0@
 * @since      Class available since Release 1.0.0
 */
class RedisBasedSessionContainer extends SessionContainerHelper implements SessionContainerInterface
{
    public $REDIS_HOSTNAME = null;
    public $REDIS_PORT = null;
    public $REDIS_USERNAME = null;
    public $REDIS_PASSWORD = null;
    public $REDIS_DATABASE = null;

    private $redis = null;

    public function init($sessionSavePath, $sessionName)
    {
        $this->connect();
        $this->currentTimestamp = time();
    }

    public function get($sessionId)
    {
        if ($this->redis->exists($sessionId)) {
            return $this->decryptData($this->getKey($sessionId));
        }
        return false;
    }

    public function set($sessionId, $sessionData)
    {
        return $this->setKey($sessionId, $this->encryptData($sessionData));
    }

    public function touch($sessionId, $sessionData)
    {
        return $this->resetExpire($sessionId);
    }

    public function gc($sessionMaxlifetime)
    {
        return true;
    }

    public function delete($sessionId)
    {
        return $this->deleteKey($sessionId);
    }

    public function close()
    {
        $this->redis = null;
    }

    private function connect()
    {
        try {
            $this->redis = new \Redis(
                [
                    'host' => $this->REDIS_HOSTNAME,
                    'port' => (int)$this->REDIS_PORT,
                    'connectTimeout' => 2.5,
                    'auth' => [$this->REDIS_USERNAME, $this->REDIS_PASSWORD],
                ]
            );
            $this->redis->select($this->REDIS_DATABASE);
        } catch (\Exception $e) {
            $this->manageException($e);
        }
    }

    private function getKey($key)
    {
        $row = [];
        try {
            $return = false;
            if ($data = $this->redis->get($key)) {
                $return = &$data;
            }
            return $return;
        } catch (\Exception $e) {
            $this->manageException($e);
        }
    }

    private function setKey($key, $value)
    {
        try {
            $return = false;
            if ($this->redis->set($key, $value, $this->sessionMaxlifetime)) {
                $return = true;
            }
            return $return;
        } catch (\Exception $e) {
            $this->manageException($e);
        }
    }

    private function resetExpire($key)
    {
        try {
            $return = false;
            if ($this->redis->expire($key, $this->sessionMaxlifetime)) {
                $return = true;
            }
            return $return;
        } catch (\Exception $e) {
            $this->manageException($e);
        }
    }

    private function deleteKey($key)
    {
        try {
            $return = false;
            if ($this->redis->del($key)) {
                $return = true;
            }
            return $return;
        } catch (\Exception $e) {
            $this->manageException($e);
        }
    }

    private function manageException(\Exception $e)
    {
        die($e->getMessage());
    }
}
