<?php

namespace CanvasApiLibrary\Providers\Utility;

use CanvasApiLibrary\Models\Utility\ModelInterface;
/**
 * @template Key extends \CanvasApiLibrary\Models\Utility\ModelInterface
 * @template Value
 */
class Lookup {
    /**
     * Summary of map
     * @var Value[][]
     */
    private $map = [];

    public function add(ModelInterface $key, $value): void {
        if(!isset($this->map[$key->getUniqueId()])) {
            $this->map[$key->getUniqueId()] = [];
        }
        $this->map[$key->getUniqueId()][] = $value;
    }

    public function get(ModelInterface $key): array {
        return $this->map[$key->getUniqueId()] ?? [];
    }
}