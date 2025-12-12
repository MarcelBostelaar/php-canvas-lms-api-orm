<?php
namespace CanvasApiLibrary\Core\Providers\Utility;
use CanvasApiLibrary\Core\Models\Utility\ModelInterface;
use CanvasApiLibrary\Core\Services\CanvasCommunicator;
use CanvasApiLibrary\Core\Services\StatusHandlerInterface;
use CanvasApiLibrary\Core\Models\Domain;
use CanvasApiLibrary\Core\Providers\Utility\ModelPopulator\ModelPopulationConfigBuilder;
use Exception;

abstract class AbstractProvider implements HandleEmittedInterface{
    public function __construct(
        public readonly StatusHandlerInterface $statusHandler,
        public readonly CanvasCommunicator $canvasCommunicator,
        protected readonly ModelPopulationConfigBuilder $modelPopulator
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
    
    /**Returns an id that identifies the current client uniquely */
    public function getClientID(): string{
        return hash("sha256", $this->canvasCommunicator->apiKey);
    }

    /**
     * @param array $context
     * @throws Exception
     * @return Domain
     */
    private static function GetDomainFromContext(array $context){
        foreach($context as $item){
            if($item instanceof Domain){
                return $item;
            }
        }
        throw new Exception("Domain not found in context");
    }

    private function GetInternal(string $route, array $context, ?ModelPopulationConfigBuilder $customBuilder = null, ?StatusHandlerInterface $customHandler = null){
        if($customBuilder === null){
            $customBuilder = $this->modelPopulator;
        }
        if($customHandler === null){
            $customHandler = $this->statusHandler;
        }
        
        $domain = self::GetDomainFromContext($context);
        [$data, $status] = $this->canvasCommunicator->Get($route, $domain);
        if($data === null){
            throw new Exception("Not data found in route");
        }
        $data = $customHandler->HandleStatus($data, $status);
        
        return [$data, $customBuilder];
    }

    protected function GetMany(string $route, array $context, ?ModelPopulationConfigBuilder $customBuilder = null, ?callable $postProcessor = null, ?StatusHandlerInterface $customHandler = null): array{
        [$data, $builder] = $this->GetInternal($route, $context, $customBuilder, $customHandler);
        if($postProcessor){
            $data = $postProcessor($data);
        }
        return $builder->buildMany($data, ...$context);
    }
    protected function Get(string $route, array $context, ?ModelPopulationConfigBuilder $customBuilder = null, ?callable $postProcessor = null, ?StatusHandlerInterface $customHandler = null): ModelInterface{
        [$data, $builder] = $this->GetInternal($route, $context, $customBuilder, $customHandler);
        if($postProcessor){
            $data = array_map($postProcessor, $data);
        }
        return $builder->build($data, ...$context);
    }
}