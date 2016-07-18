<?php

namespace YOOtheme\Framework\Event;

class Event implements EventInterface, \ArrayAccess
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var array
     */
    protected $parameters;

    /**
     * @var bool
     */
    protected $propagationStopped = false;

    /**
     * Constructor.
     *
     * @param string $name
     * @param array  $parameters
     */
    public function __construct($name, array $parameters = array())
    {
        $this->name = $name;
        $this->parameters = $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Sets the event name.
     *
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Gets all parameters.
     *
     * @return array|object|\ArrayAccess
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * Sets all parameters.
     *
     * @param  array
     * @return array
     */
    public function setParameters(array $parameters)
    {
        return $this->parameters = $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function isPropagationStopped()
    {
        return $this->propagationStopped;
    }

    /**
     * {@inheritdoc}
     */
    public function stopPropagation()
    {
        $this->propagationStopped = true;
    }

    /**
     * Checks if a parameter exists.
     *
     * @param  string $name
     * @return mixed
     */
    public function offsetExists($name)
    {
        return isset($this->parameters[$name]);
    }

    /**
     * Gets a parameter.
     *
     * @param  string $name
     * @return mixed
     */
    public function offsetGet($name)
    {
        return isset($this->parameters[$name]) ? $this->parameters[$name] : null;
    }

    /**
     * Sets a parameter.
     *
     * @param  string   $name
     * @param  callable $callback
     */
    public function offsetSet($name, $callback)
    {
        $this->parameters[$name] = $callback;
    }

    /**
     * Unsets a parameter.
     *
     * @param string $name
     */
    public function offsetUnset($name)
    {
        unset($this->parameters[$name]);
    }
}
