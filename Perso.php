<?php

class Perso
{
    private $_id;
    private $_name;
    private $_damage;

    const ITS_ME = 1;
    const IM_DOWN = 2;
    const IM_HIT = 3;

    public function hit(Perso $perso)
    {
        if ($perso->getId() == $this->getId()) {
            return self::ITS_ME;
        }
        return $perso->takeDamage();
    }

    public function takeDamage()
    {
        $this->_damage += 5;
        return ($this->_damage >= 100) ? self::IM_DOWN : self::IM_HIT;
    }

    public function validName()
    {
        return !empty($this->_name);
    }

    /**
     * GETTERS
     */
    public function getId()
    {
        return $this->_id;
    }

    public function getName()
    {
        return $this->_name;
    }

    public function getDamage()
    {
        return $this->_damage;
    }

    /**
     * SETTERS
     */
    public function setId($newId)
    {
        $newId = (int) $newId;

        if ($newId > 0) {
            $this->_id = $newId;
        }
    }

    public function setName($newName)
    {
        if (is_string($newName)) {
            $this->_name = $newName;
        }
    }

    public function setDamage($newDamage)
    {
        $newDamage = (int) $newDamage;

        if ($newDamage >= 0 && $newDamage <= 100) {
            $this->_damage = $newDamage;
        }
    }

    /**
     * Hydrate Perso object with values in data array
     * @param  array  $data Associative array containing object values.
     */
    public function hydrate(array $data)
    {
        foreach ($data as $key => $value) {
            $method = 'set'.ucfirst($key);

            if (method_exists($this, $method)) {
                $this->$method($value);
            }
        }
    }

    /**
     * Constructor of Perso
     * @param array $data Array containing all Perso's values.
     */
    public function __construct(array $data)
    {
        $this->hydrate($data);
    }
}
