<?php
declare(strict_types=1);
namespace PHPJava\Kernel\Provider;

use PHPJava\Exceptions\ProviderException;

class Provider implements ProviderInterface
{
    private $entries = [];

    /**
     * @return static
     */
    public function add($key, $value)
    {
        $this->entries[$key] = $value;
        return $this;
    }

    /**
     * @throws ProviderException
     */
    public function get($key, ...$arguments)
    {
        if (!isset($this->entries[$key])) {
            throw new ProviderException('Entry does not exist.');
        }
        if (is_callable($this->entries[$key])) {
            return ($this->entries[$key])(...$arguments);
        }
        return $this->entries[$key];
    }
}
