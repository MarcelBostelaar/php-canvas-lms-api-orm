<?php

namespace CanvasApiLibrary\Core\Providers\Utility;

use CanvasApiLibrary\Core\Models\Utility\ModelInterface;
use Closure;
/**
 * @template Key extends \CanvasApiLibrary\Core\Models\Utility\ModelInterface
 * @template Value
 */
class Lookup {
    /**
     * Summary of map
     * @var Value[][]
     */
    private $map = [];

    /**
     * @param Value $value
     */
    public function add(ModelInterface $key, $value): void {
        if(!isset($this->map[$key->getId()])) {
            $this->map[$key->getId()] = [];
        }
        $this->map[$key->getId()][] = $value;
    }

    /**
     * @param Key $key
     * @return Value[]
     */
    public function get(ModelInterface $key): array {
        return $this->map[$key->getId()] ?? [];
    }

    public function addMany(Closure $keyFuncFromValue, array $values){
        foreach($values as $value){
            $this->add($keyFuncFromValue($value), $value);
        }
    }
}