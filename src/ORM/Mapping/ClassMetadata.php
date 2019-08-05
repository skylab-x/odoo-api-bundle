<?php

namespace Ang3\Bundle\OdooApiBundle\ORM\Mapping;

use Generator;
use ReflectionClass;
use Ang3\Bundle\OdooApiBundle\ORM\Exception\MappingException;

/**
 * @author Joanis ROUANET
 */
class ClassMetadata
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var ReflectionClass
     */
    private $reflection;

    /**
     * @var array
     */
    private $properties = [];

    /**
     * @param string $class
     */
    public function __construct(string $class)
    {
        $this->setClass($class);
    }

    /**
     * @param string $class
     *
     * @return self
     */
    public function setClass(string $class)
    {
        $this->class = $class;
        $this->reflection = new ReflectionClass($class);

        return $this;
    }

    /**
     * @return string
     */
    public function getClass()
    {
        return $this->class;
    }

    /**
     * @return ReflectionClass
     */
    public function getReflection()
    {
        return $this->reflection;
    }

    /**
     * @param PropertyInterface $property
     *
     * @return self
     */
    public function addProperty(PropertyInterface $property)
    {
        // Enregistrement du champ
        $this->properties[$property->getName()] = $property;

        // Retour des métadonnées
        return $this;
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public function removeProperty(string $name)
    {
        // Si le champ est déjà enregistré
        if ($this->hasProperty($name)) {
            // Suppression du champ
            unset($this->properties[$name]);
        }

        // Retour des métadonnées
        return $this;
    }

    /**
     * @param string $name
     *
     * @throws MappingException when the property was not found
     *
     * @return PropertyInterface
     */
    public function getProperty(string $name)
    {
        if (!$this->hasProperty($name)) {
            throw new MappingException(sprintf('The property "%s" does not exists in metadata of class "%s"', $name, $this->class));
        }

        // Retour de la propriété
        return $this->properties[$name];
    }

    /**
     * @return array
     */
    public function getProperties()
    {
        return $this->properties;
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasProperty(string $name)
    {
        return array_key_exists($name, $this->properties);
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasField(string $name)
    {
        return $this->hasProperty($name) && $this->properties[$name]->isField();
    }

    /**
     * @param string $name
     *
     * @return bool
     */
    public function hasAssociation(string $name)
    {
        return $this->hasProperty($name) && $this->properties[$name]->isAssociation();
    }

    /**
     * Get all serialized properties names.
     *
     * @return array
     */
    public function getSerializedNames()
    {
        // Initialisation de la liste des noms sérialisés
        $serializedNames = [];

        // Pour chaque propriété
        foreach ($this->properties as $name => $property) {
            // Enregistrement du nom sérialisé
            $serializedNames[$property->getName()] = $property->getSerializedName();
        }

        // Retour des noms sérialisés
        return $serializedNames;
    }

    /**
     * @return Generator
     */
    public function iterateProperties()
    {
        // Pour chaque propriété
        foreach ($this->properties as $name => $property) {
            // On rend le champ avec son nom en clé
            yield $name => $property;
        }
    }

    /**
     * @return Generator
     */
    public function iterateFields()
    {
        // Pour chaque propriété
        foreach ($this->properties as $name => $property) {
            // Si c'est un champ simple
            if ($property->isField()) {
                // On rend le champ avec son nom en clé
                yield $name => $property;
            }
        }
    }

    /**
     * @return Generator
     */
    public function iterateAssociations()
    {
        // Pour chaque propriété
        foreach ($this->properties as $name => $property) {
            // Si c'est une association
            if ($property->isAssociation()) {
                // On rend le champ avec son nom en clé
                yield $name => $property;
            }
        }
    }
}
