<?php


namespace App\Service;

use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PropertyAccess\Exception\NoSuchIndexException;

class SessionManager
{

    private SessionInterface $__session;

    public function __construct(SessionInterface $session)
    {
        $this->__session = $session;
    }

    public function set(string $name,  $value): self
    {

        if(is_null($this->get($name)))
            $this->__session->set($name, $value);

        else
            $this->replace($name, $value);

        return $this;

    }

    public function has(string $name)
    {
        return $this->__session->has($name);
    }

    public function get(string $name)
    {
        return $this->__session->get($name);
    }

    public function getAll()
    {
        return $this->__session->all();
    }

    public function remove(string $name): self
    {
        if(is_null($this->get($name)))
            throw new NoSuchIndexException(sprintf("Error ! Cause : session variable '%s' not found !", $name));

        $this->__session->remove($name);

        return $this;

    }

    public function replace(string $name, $values): self
    {

        if(is_null($this->get($name)))
            throw new NoSuchIndexException(sprintf("Error ! Cause : session variable '%s' not found !", $name));

        $this->remove($name);
        $this->set($name, $values);

        //dd($this->get($name), $values);

        return $this;

    }

    public function removeAll(): self
    {
        $this->__session->clear();

        return $this;
    }

    private function addMissingKeysInArray(string $name, array $keys)
    {

        $initialKeys = array_keys($this->get($name));

        foreach ($initialKeys as $index => $initialKey)
        {
            if(!array_key_exists($initialKey, $keys))
                $keys[$initialKey] = $this->get($name)[$initialKey];
        }
        //dd($keys, $initialKeys);
        return $keys;

    }

}