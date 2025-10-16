<?php
namespace CanvasApiLibrary\Providers\Utility;
use CanvasApiLibrary\Models\Utility\AbstractCanvasPopulatedModel;
use CanvasApiLibrary\Models\Utility\ModelInterface;
use CanvasApiLibrary\Services\CanvasCommunicator;
use CanvasApiLibrary\Services\StatusHandlerInterface;
use CanvasApiLibrary\Models\Domain;

abstract class AbstractProvider{
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

    /**
     * Summary of MapData
     * @param mixed $data
     * @param \CanvasApiLibrary\Models\Domain $domain
     * @param array $suplementaryDataMapping Additional key value mappings to apply when mapping data.
     * Allowed formats: 
     *  string (one to one mapping)
     *  [string, callable] transforms key to value using callable
     *  [string, string, callable] transforms key to value using callable with second argument as the target name
     * @return \CanvasApiLibrary\Models\Utility\AbstractCanvasPopulatedModel[]
     */
    abstract protected function MapData(mixed $data, Domain $domain, array $suplementaryDataMapping): array;
    abstract protected function populateModel(Domain $domain, $model, mixed $data): AbstractCanvasPopulatedModel;

    protected function Get(Domain $domain, string $route, array $suplementaryDataMapping = [], ?callable $preprocess = null): array{
        [$data, $status] = $this->canvasCommunicator->Get($route, $domain);
        $data = $this->statusHandler->HandleStatus($data, $status);
        if($preprocess){
            $data = array_map($preprocess, $data);
        }
        return $this->MapData($data, $domain, $suplementaryDataMapping);
    }

    /**
     * Handles unknown method calls with magic methods for the following cases, using plurals provided by the model, and the method name, 
     * to detect which item to provide the magic method for.
     * 
     * Starting with populate, provides array mapped version.
     * Original: populateThing: Thing -> Thing. Thing subclasses AbstractCanvasPopulatedModel
     * Magic method: populate<Things>: Thing[] -> Thing[].
     * 
     * Ending with ForThing, provided array mapped version for that Thing.
     * Original: doSomethingForThing: YourArgs -> Thing -> InAny -> In -> Order -> Mixed. Thing can be in any position.
     * Magic method: doSomethingFor<Things>: YourArgs -> Thing[] -> InAny -> Order -> Mixed[]. Position maintained.
     * @param string $method
     * @param mixed $args
     * @throws \BadMethodCallException
     * @return AbstractCanvasPopulatedModel[]|Lookup
     */
    public function __call($method, $args){
        //Match any methods with For in the middle, preceded by a lowercase letter and followed by an uppercase, such as getCommentsForAssignments
        if(preg_match('/[a-z]For[A-Z]/', $method)){
            return $this->handle__callForPlural($method, $args);
        }
        //Match any methods starting with populate, followed by a capital letter, such as populateAssignments
        if(preg_match('/^populate[A-Z]/', $method)){
            return $this->handle__callPopulate($method, $args);
        }
        throw new \BadMethodCallException("No such method: $method.");
    }

    private static function stripNamespace(string $fullClassName): string{
        $parts = explode("\\", $fullClassName);
        return end($parts);
    }

    /**
     * Handles calls to "populate<Thing>s" methods. Performs an array_map over the original populate method, to allow passing an array of objects to populate.
     * Only works for models extending AbstractCanvasPopulatedModel, not just any ModelInterface, because populate only works for canvas populated models.
     * @param string $method
     * @param mixed $args
     * @throws \BadMethodCallException
     * @return AbstractCanvasPopulatedModel[]
     */
    private function handle__callPopulate($method, $args): array{
        //checks
        $exceptionInvalidArgs = new \BadMethodCallException("Attempting call to magic populate<Thing>s call (array version of populate<Thing>). Invalid call. First and only argument to $method must be an array of AbstractCanvasPopulatedModel.");
        if(count($args) !== 1){
            throw $exceptionInvalidArgs;
        }
        if(!is_array($args[0])){
            throw $exceptionInvalidArgs;
        }
        if(!is_a($args[0], AbstractCanvasPopulatedModel::class, true)){
            throw $exceptionInvalidArgs;
        }
        $validPlural = false;
        foreach($args[0]->getPluralNames() as $plural){
            if($method == "populate$plural"){
                $validPlural = true;
                break;
            }
        }
        if(!$validPlural){
            throw new \BadMethodCallException("No such method: $method. Invalid plural name for given array of models " . $args[0]::class . 
            ". Valid method names: " . implode(", ", array_map(fn($x) => "populate$x", $args[0]->getPluralNames())) . ".");
        }
        
        $foundItemClass = $args[0]::class;
        $strippedClass = self::stripNamespace($foundItemClass);
        $fixedMethodName = "populate$strippedClass";
        if(!method_exists($this, $fixedMethodName)){
            throw new \BadMethodCallException("Unknown method: $method. No corresponding method $fixedMethodName found.");
        }

        //actual calls
        $populatedItems = [];
        foreach($args[0] as $item){
            if(!is_a($item, $foundItemClass, true)){
                throw new \BadMethodCallException("Array passed to $method contains an item that is not of type $foundItemClass: " . serialize($item) . ".");
            }
            $populatedItems[] = $this->{"populate$strippedClass"}($item);
        }
        return $populatedItems;
    }

    /**
     * Provides array based methods for any provider calls. Used the class name and provided plural name(s) to find the corresponding methods for a singular object.
     * For example, getCommentsForAssignment(domain, course, assignment) exists, which returns an Assignment, which has a plural form Assignments. 
     * This then automagically creates getCommentForAssignments(domain, course, array assignment) : Lookup<Assignment, Comment>
     * Method signature is identical, except for replacing the singular argument with an array of the models.
     * If multiple plurals are provided, all are valid. For example, cacti, cactuses -> getSpikesForCacti and getSpikesForCactuses both work.
     * @param mixed $method
     * @param mixed $args
     * @throws \BadMethodCallException
     * @return Lookup
     */
    private function handle__callForPlural($method, $args){
        //checks
        $indexOfMultiItem = -1;
        $foundPluralName = null;
        for($i = 0; $i < count($args); $i++){
            //if the argument is an array, and the first item in the array is a subclass of ModelInterface
            if(is_array($args[$i]) && count($args[$i]) > 0 && is_subclass_of($args[$i][0], ModelInterface::class)){
                $plurals = $args[$i][0]::getPluralNames();
                foreach($plurals as $plural){
                    //check if method ends with this plural
                    if(str_ends_with(strtolower($method), strtolower($plural))){
                        if($indexOfMultiItem !== -1){
                            throw new \BadMethodCallException("Multiple array arguments detected in call to $method. Only one array of models is allowed.");
                        }
                        $indexOfMultiItem = $i;
                        $foundPluralName = $plural;
                    }
                }
            }
        }
        if($indexOfMultiItem === -1){
            throw new \BadMethodCallException("Unknown method: $method.");
        }
        $singularClassnameFull = $args[$indexOfMultiItem][0]::class;
        $singularName = self::stripNamespace($args[$indexOfMultiItem][0]::class);
        $fixedMethodName = substr($method, -strlen($foundPluralName), strlen($foundPluralName)) . $singularName;

        if(!method_exists($this, $fixedMethodName)){
            throw new \BadMethodCallException("Unknown method: $method. No corresponding method $fixedMethodName found.");
        }

        $headArgs = array_slice($args, 0, $indexOfMultiItem);
        $tailArgs = array_slice($args, $indexOfMultiItem + 1);
        $newLookup = new Lookup();
        try{
            foreach($args[$indexOfMultiItem] as $item){
                if(!is_a($item, $singularClassnameFull, true)){
                    throw new \BadMethodCallException("Array passed to $method contains an item that is not of type $singularClassnameFull: " . serialize($item) . ".");
                }
                $newLookup->add($item, 
                    call_user_func([$this, $fixedMethodName], ...array_merge($headArgs, [$item], $tailArgs))
                );
            }
        }catch(\ArgumentCountError $e){
            throw new \BadMethodCallException("Argument count mismatch when calling $fixedMethodName. Check that the method signature matches the arguments passed to $method, and has the correct array in the same position.", 0, $e);
        }
        catch(\TypeError $e){
            throw new \BadMethodCallException("Type error when calling $fixedMethodName. Check that the method signature matches the arguments passed to $method, and has the correct array in the same position.", 0, $e);
        }
        return $newLookup;
    }
}