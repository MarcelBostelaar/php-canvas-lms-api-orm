<?php
namespace CanvasApiLibrary\Providers\Utility;
use CanvasApiLibrary\Models\Utility\AbstractCanvasPopulatedModel;
use CanvasApiLibrary\Models\Utility\ModelInterface;
use CanvasApiLibrary\Services\CanvasCommunicator;
use CanvasApiLibrary\Services\StatusHandlerInterface;
use CanvasApiLibrary\Models\Domain;
use CanvasApiLibrary\Providers\Utility\ModelPopulator\ModelPopulationConfigBuilder;

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
     * @return void 
     */
    public function HandleEmitted(mixed $data, Domain $domain){
        //Do nothing by default.
    }

    abstract protected static ModelPopulationConfigBuilder $modelPopulator;

    protected function Get(Domain $domain, string $route, ?ModelPopulationConfigBuilder $customBuilder = null): array{
        [$data, $status] = $this->canvasCommunicator->Get($route, $domain);
        $data = $this->statusHandler->HandleStatus($data, $status);
        if($customBuilder === null){
            $customBuilder = static::$modelPopulator;
        }
        return $customBuilder->build($domain, $data);
    }
}