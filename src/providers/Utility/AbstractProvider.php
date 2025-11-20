<?php
namespace CanvasApiLibrary\Providers\Utility;
use CanvasApiLibrary\Models\Utility\ModelInterface;
use CanvasApiLibrary\Services\CanvasCommunicator;
use CanvasApiLibrary\Services\StatusHandlerInterface;
use CanvasApiLibrary\Models\Domain;
use CanvasApiLibrary\Providers\Utility\ModelPopulator\ModelPopulationConfigBuilder;
use Exception;

abstract class AbstractProvider implements HandleEmittedInterface{
    public function __construct(
        public readonly StatusHandlerInterface $statusHandler,
        public readonly CanvasCommunicator $canvasCommunicator
        ){}

    /**
     * Some services can prefetch additional data with requests, such as an assignment which can prefetch users.
     * If additional data is fetched, the fetched info is passed to their corresponding providers "handlEmitted" method.
     * This can be overridden to do things such as caching.
     * @param mixed $data A single raw data item to be processed and handled.
     * @param ModelInterface[] $context A single raw data item to be processed and handled.
     * @return void 
     */
    public function HandleEmitted(mixed $data, array $context){
        //Do nothing by default.
    }

    abstract protected static ModelPopulationConfigBuilder $modelPopulator;

    private static function GetDomainFromContext($context){
        foreach($context as $item){
            if($item instanceof Domain){
                return $item;
            }
        }
        throw new Exception("Domain not found in context");
    }

    private function GetInternal(string $route, array $context, ?ModelPopulationConfigBuilder $customBuilder = null){
        $domain = self::GetDomainFromContext($context);
        [$data, $status] = $this->canvasCommunicator->Get($route, $domain);
        $data = $this->statusHandler->HandleStatus($data, $status);
        if($customBuilder === null){
            $customBuilder = static::$modelPopulator;
        }
        return [$data, $customBuilder];
    }

    protected function GetMany(string $route, array $context, ?ModelPopulationConfigBuilder $customBuilder = null, ?callable $postProcessor = null): array{
        [$data, $builder] = $this->GetInternal($route, $context, $customBuilder);
        if($postProcessor){
            $data = $postProcessor($data);
        }
        return $builder->buildMany($data, ...$context);
    }
    protected function Get(string $route, array $context, ?ModelPopulationConfigBuilder $customBuilder = null, ?callable $postProcessor = null): ModelInterface{
        [$data, $builder] = $this->GetInternal($route, $context, $customBuilder);
        if($postProcessor){
            $data = array_map($postProcessor, $data);
        }
        return $builder->build($data, ...$context);
    }
}