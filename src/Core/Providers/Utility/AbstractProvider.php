<?php
namespace CanvasApiLibrary\Core\Providers\Utility;
use CanvasApiLibrary\Core\Models\Utility\ModelInterface;
use CanvasApiLibrary\Core\Services\CanvasCommunicator;
use CanvasApiLibrary\Core\Services\CanvasReturnStatus;
use CanvasApiLibrary\Core\Models\Domain;
use CanvasApiLibrary\Core\Providers\Utility\ModelPopulator\ModelPopulationConfigBuilder;
use CanvasApiLibrary\Core\Providers\Utility\Results\ErrorResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\NotFoundResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\SuccessResult;
use CanvasApiLibrary\Core\Providers\Utility\Results\UnauthorizedResult;
use Exception;

abstract class AbstractProvider implements HandleEmittedInterface{
    public function __construct(
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

    /**
     * @param array<int, ModelInterface> $context
     * @return array{0:mixed,1:CanvasReturnStatus,2:ModelPopulationConfigBuilder}
     */
    private function GetInternal(string $route, array $context, ?ModelPopulationConfigBuilder $customBuilder = null): array{
        $builder = $customBuilder ?? $this->modelPopulator;

        $domain = self::GetDomainFromContext($context);
        [$data, $status] = $this->canvasCommunicator->Get($route, $domain);
        if($data === null){
            throw new Exception("No data found in route");
        }
        return [$data, $status, $builder];
    }

    /**
     * @param array<int, ModelInterface> $context
    * @return SuccessResult<ModelInterface[]>|UnauthorizedResult|NotFoundResult|ErrorResult
    */
    protected function GetMany(string $route, array $context, ?ModelPopulationConfigBuilder $customBuilder = null, ?callable $postProcessor = null): SuccessResult|UnauthorizedResult|NotFoundResult|ErrorResult{
        [$data, $status, $builder] = $this->GetInternal($route, $context, $customBuilder);
        if($postProcessor){
            $data = $postProcessor($data);
        }

        return match($status){
            CanvasReturnStatus::SUCCESS => new SuccessResult($builder->buildMany($data, ...$context)),
            CanvasReturnStatus::UNAUTHORIZED => new UnauthorizedResult(),
            CanvasReturnStatus::NOT_FOUND => new NotFoundResult(),
            default => new ErrorResult(),
        };
    }

    /**
     * @param array<int, ModelInterface> $context
     * @return SuccessResult<ModelInterface>|UnauthorizedResult|NotFoundResult|ErrorResult
     */
    protected function Get(string $route, array $context, ?ModelPopulationConfigBuilder $customBuilder = null, ?callable $postProcessor = null): SuccessResult|UnauthorizedResult|NotFoundResult|ErrorResult{
        [$data, $status, $builder] = $this->GetInternal($route, $context, $customBuilder);
        if($postProcessor){
            $data = array_map($postProcessor, $data);
        }

        return match($status){
            CanvasReturnStatus::SUCCESS => new SuccessResult($builder->build($data, ...$context)),
            CanvasReturnStatus::UNAUTHORIZED => new UnauthorizedResult(),
            CanvasReturnStatus::NOT_FOUND => new NotFoundResult(),
            default => new ErrorResult(),
        };
    }
}