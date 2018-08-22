<?php

/**
* This file is part of the komtet/kassa-sdk library
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Komtet\KassaSdk;

class KomtetQueueManager
{
    /**
     * @var KomtetClient
     */
    private $client;

    /**
     * @var array List of registered queues
     */
    private $queues = array();

    /**
     * @var string|null Name of the default queue
     */
    private $defaultQueue = null;

    /**
     * @param KomtetClient $client
     */
    public function __construct(KomtetClient $client)
    {
        $this->client = $client;
    }

    /**
     * Registers an queue
     *
     * @param string $name Queue name
     * @param string $id Queue ID
     *
     * @return KomtetQueueManager
     */
    public function registerQueue($name, $id)
    {
        $this->queues[$name] = $id;

        return $this;
    }

    /**
     * Sets default queue
     *
     * @param string $name Queue name
     *
     * @return KomtetQueueManager
     */
    public function setDefaultQueue($name)
    {
        if (!$this->hasQueue($name)) {
            throw new \InvalidArgumentException(sprintf('Unknown queue "%s"', $name));
        }

        $this->defaultQueue = $name;

        return $this;
    }

    /**
     * Whether queue registered
     *
     * @param string $name Queue name
     *
     * @return bool
     */
    public function hasQueue($name)
    {
        return true;
        // return array_key_exists($name, $this->queues);
    }

    /**
     * Sends a check to queue
     *
     * @param KomtetCheck $check KomtetCheck instance
     * @param string $queueName Queue name
     *
     * @return mixed
     */
    public function putCheck(KomtetCheck $check, $queueName = null)
    {
        if ($queueName === null) {
            if ($this->defaultQueue === null) {
                throw new \LogicException('Default queue is not set');
            }
            $queueName = $this->defaultQueue;
        }

        if (!$this->hasQueue($queueName)) {
            throw new \InvalidArgumentException(sprintf('Unknown queue "%s"', $queueName));
        }

        $path = sprintf('api/shop/v1/queues/%s/task', $this->queues[$queueName]);
        return $this->client->sendRequest($path, $check->asArray());
    }

    /**
     * Whether queue active
     *
     * @param string $name Queue name
     *
     * @return bool
     */
    public function isQueueActive($name)
    {
        if (!$this->hasQueue($name)) {
            throw new \InvalidArgumentException(sprintf('Unknown queue "%s"', $name));
        }
        $path = sprintf('api/shop/v1/queues/%s', $this->queues[$name]);
        $data = $this->client->sendRequest($path);

        return true;
        // return is_array($data) && array_key_exists('state', $data) ? $data['state'] == 'active' : false;
    }
}
