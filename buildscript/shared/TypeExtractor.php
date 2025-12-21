<?php

namespace Buildscript;

use Buildscript\AtomicTypeDefinition;
use Buildscript\GenericTypeDefinition;
use Buildscript\UnionTypeDefinition;
use Buildscript\TypeDefinitionBase;
use Closure;

/**
 * Parse result representing either a success with consumed characters and remaining input,
 * or a failure.
 */
class ParseResult
{
    public function __construct(
        public readonly bool $success,
        public readonly mixed $value = null,
        public readonly int $position = 0
    ) {
    }

    public static function success(mixed $value, int $position): self
    {
        return new self(true, $value, $position);
    }

    public static function failure(): self
    {
        return new self(false);
    }
}

/**
 * Interface for parser combinators.
 *
 * A parser combinator takes a string and a position and attempts to parse from that position.
 * It returns a ParseResult indicating success or failure, and if successful, the parsed value
 * and new position.
 * 
 * @template ReturnType
 */
interface IParserCombinator
{
    /**
     * Mark this parser as optional. Optional parsers never fail; they return null instead.
     */
    public function optional(): self;

    /**
     * Set a post-processing function to transform the parsed value.
     *
     * @param Closure $processor Function that takes the parsed value and returns the processed value
     */
    public function map(Closure $processor): self;

    /**
     * Parse the input at the given position.
     *
     * @param string $input The input string
     * @param int $position The position to start parsing from
     * @return ParseResult The result of parsing
     */
    public function parse(string $input, int $position): ParseResult;

    /**
     * Chain this parser with another parser, executing them in sequence.
     */
    public function then(IParserCombinator $nextParser): IParserCombinator;

    /**
     * Chain this parser with a lazily-evaluated parser.
     */
    public function lazyThen(Closure $nextParserFactory): IParserCombinator;

    /**
     * Apply this parser multiple times.
     */
    public function many(int $min = 0): IParserCombinator;

    public function wrapWhitepace(): IParserCombinator;

    public function or(IParserCombinator $other): IParserCombinator;
}

/**
 * Common base for parser combinators providing composition helpers.
 *
 * Subclasses must implement: optional(), map(), parse().
 */
abstract class AbstractCombinator implements IParserCombinator
{
    protected array $postProcessors = [];

    public function map(Closure $processor): IParserCombinator
    {
        $new = clone $this;
        $new->postProcessors[] = $processor;
        return $new;
    }

    public function then(IParserCombinator $nextParser): IParserCombinator
    {
        return new CombinedParser([$this, $nextParser]);
    }

    public function lazyThen(Closure $nextParserFactory): IParserCombinator
    {
        return $this->then(new LazyParser($nextParserFactory));
    }

    public function many(int $min = 0): IParserCombinator
    {
        return new ManyParser($this, $min);
    }

    public function wrapWhitepace(): IParserCombinator
    {
        return
            (new WhitespaceParser())->optional()
            ->then($this)
            ->then((new WhitespaceParser())->optional())
            ->map(fn($x) => $x[1]);
    }

    public function or(IParserCombinator $otherParser): IParserCombinator
    {
        return new OrParser($this, $otherParser);
    }
}

/**
 * Base class for parser combinators.
 */
abstract class ParserCombinator extends AbstractCombinator
{
    protected bool $isOptional = false;

    /**
     * Mark this parser as optional. Optional parsers never fail; they return null instead.
     */
    public function optional(): self
    {
        $copy = clone $this;
        $copy->isOptional = true;
        return $copy;
    }

    /**
     * Parse the input at the given position.
     *
     * @param string $input The input string
     * @param int $position The position to start parsing from
     * @return ParseResult The result of parsing
     */
    abstract protected function doParse(string $input, int $position): ParseResult;

    /**
     * Public parse method that handles optional and post-processing.
     */
    public final function parse(string $input, int $position): ParseResult
    {
        $result = $this->doParse($input, $position);

        if (!$result->success) {
            if ($this->isOptional) {
                return ParseResult::success(null, $position);
            }
            return $result;
        }

        $value = $result->value;
        foreach ($this->postProcessors as $processor) {
            $value = $processor($value);
        }

        return ParseResult::success($value, $result->position);
    }

    // Composition helpers are inherited from AbstractCombinator
}

class LazyParser extends AbstractCombinator
{
    private Closure $parserFactory;
    private bool $optional = false;

    public function __construct(Closure $parserFactory)
    {
        $this->parserFactory = $parserFactory;
    }

    private function evaluateParser(): ParserCombinator
    {
        $evaled = ($this->parserFactory)();
        if($this->optional){
            $evaled = $evaled->optional();
        }
        $evaled->postProcessors = array_merge($this->postProcessors, $evaled->postProcessors);
        return $evaled;
    }

    public function optional(): IParserCombinator
    {
        $copy = clone $this;
        $copy->optional = true;
        return $copy;
    }

    public function map(Closure $processor): IParserCombinator
    {
        $new = clone $this;
        $new->postProcessors[] = $processor;
        return $new;
    }

    public function parse(string $input, int $position): ParseResult
    {
        $parser = $this->evaluateParser();
        return $parser->parse($input, $position);
    }

    public function then(IParserCombinator $nextParser): IParserCombinator
    {
        $parser = $this->evaluateParser();
        return $parser->then($nextParser);
    }

    public function lazyThen(Closure $nextParserFactory): IParserCombinator
    {
        $parser = $this->evaluateParser();
        return $parser->lazyThen($nextParserFactory);
    }
}

class CombinedParser extends ParserCombinator
{
    private array $parsers;

    public function __construct(array $parsers)
    {
        $this->parsers = $parsers;
    }

    protected function doParse(string $input, int $position): ParseResult
    {
        $values = [];
        $currentPosition = $position;

        foreach ($this->parsers as $parser) {
            $result = $parser->parse($input, $currentPosition);
            if (!$result->success) {
                return ParseResult::failure();
            }
            $values[] = $result->value;
            $currentPosition = $result->position;
        }

        return ParseResult::success($values, $currentPosition);
    }

    public function then(IParserCombinator $nextParser): IParserCombinator
    {
        if(count($this->postProcessors) > 0){
            //If it already has a post-processor, the end result is no longer an array, and it should not be just chained.
            return parent::then($nextParser);
        }
        $newParsers = array_merge($this->parsers, [$nextParser]);
        return new CombinedParser($newParsers);
    }
}

class ManyParser extends ParserCombinator
{
    private IParserCombinator $parser;
    private int $min;

    public function __construct(IParserCombinator $parser, int $min = 0)
    {
        $this->parser = $parser;
        $this->min = $min;
    }

    protected function doParse(string $input, int $position): ParseResult
    {
        $values = [];
        $currentPosition = $position;

        while (true) {
            $result = $this->parser->parse($input, $currentPosition);
            if (!$result->success) {
                break;
            }
            $values[] = $result->value;
            $currentPosition = $result->position;
        }

        if (count($values) < $this->min) {
            return ParseResult::failure();
        }

        return ParseResult::success($values, $currentPosition);
    }
}

class OrParser extends ParserCombinator
{
    private IParserCombinator $firstParser;
    private IParserCombinator $secondParser;

    public function __construct(IParserCombinator $firstParser, IParserCombinator $secondParser)
    {
        $this->firstParser = $firstParser;
        $this->secondParser = $secondParser;
    }

    protected function doParse(string $input, int $position): ParseResult
    {
        $firstResult = $this->firstParser->parse($input, $position);
        if ($firstResult->success) {
            return $firstResult;
        }

        return $this->secondParser->parse($input, $position);
    }
}
/**
 * Parses whitespace (one or more whitespace characters).
 */
class WhitespaceParser extends ParserCombinator
{
    protected function doParse(string $input, int $position): ParseResult
    {
        if ($position >= strlen($input) || !ctype_space($input[$position])) {
            return ParseResult::failure();
        }

        $start = $position;
        while ($position < strlen($input) && ctype_space($input[$position])) {
            $position++;
        }

        $whitespace = substr($input, $start, $position - $start);
        return ParseResult::success($whitespace, $position);
    }
}

/**
 * Parses an alphanumeric identifier starting with a letter or underscore.
 * Matches sequences like: int, string, List, Map_Type, etc.
 */
class AtomicTypeParser extends ParserCombinator
{
    protected function doParse(string $input, int $position): ParseResult
    {
        if ($position >= strlen($input)) {
            return ParseResult::failure();
        }

        $char = $input[$position];
        if (!ctype_alpha($char) && $char !== '\\') {
            return ParseResult::failure();
        }

        $start = $position;
        $position++;

        while ($position < strlen($input)) {
            $char = $input[$position];
            if (!ctype_alnum($char) && $char !== '_' && $char !== '\\') {
                break;
            }
            $position++;
        }

        $identifier = substr($input, $start, $position - $start);
        return ParseResult::success($identifier, $position);
    }
}

/**
 * Parses an exact, literal string at the current position.
 */
class StringLiteralParser extends ParserCombinator
{
    public function __construct(private readonly string $literal)
    {
    }

    protected function doParse(string $input, int $position): ParseResult
    {
        $len = strlen($this->literal);

        if ($len === 0) {
            return ParseResult::failure();
        }

        if (substr($input, $position, $len) !== $this->literal) {
            return ParseResult::failure();
        }

        return ParseResult::success($this->literal, $position + $len);
    }

    public function asSeperator(IParserCombinator $valueParser){
        return $valueParser
        ->then($this->wrapWhitepace()->then($valueParser)->many(0))
        ->map(fn($x) => array_merge([$x[0]], array_map(fn($y) => $y[1], $x[1])));
    }
}

class PipeParser extends StringLiteralParser{
    public function __construct()
    {
        parent::__construct('|');
    }
}

class OpenAngleBracketParser extends StringLiteralParser{
    public function __construct()
    {
        parent::__construct('<');
    }
}

class CloseAngleBracketParser extends StringLiteralParser{
    public function __construct()
    {
        parent::__construct('>');
    }
}
/**
 * Summary of PhpCanvasLmsApiOrm\Buildscript\Shared\GenericTypeString
 * @return IParserCombinator<GenericTypeDefinition>
 */
function GenericTypeString(){
    return new AtomicTypeParser()
    ->then(new OpenAngleBracketParser())
    ->then(
        new StringLiteralParser(",")->asSeperator(
            new LazyParser(fn() => FullTypeString())
                                ->wrapWhitepace()
    ))
    ->then(new CloseAngleBracketParser())
    ->map(function($value){
        $Basename = $value[0];
        $GenericParams = $value[2];
        return new GenericTypeDefinition($Basename, $GenericParams);
    });
}

/**
 * First tries generic type, then tries alphanumeric type.
 * @return IParserCombinator<AtomicTypeDefinition|GenericTypeDefinition>
 */
function GenericOrAtomic(){
    return new StringLiteralParser("?")->optional()
    ->then(
        GenericTypeString()
        ->or(
            new AtomicTypeParser()
            ->map(fn($x) => new AtomicTypeDefinition($x))
        )
    )
    ->then(new StringLiteralParser("[]")->optional())
    ->map(function($result) {
        $isArray = $result[2] !== null;
        $isNullable = $result[0] !== null;
        if(\is_string($result[1])){
            //Atomic type
            return new AtomicTypeDefinition($result[1], $isArray, $isNullable);
        }
        else{
            $result[1]->isArrayVariant = $isArray;
            $result[1]->isNullable = $isNullable;
            return $result[1];
        }
    });
}

/**
 * @return IParserCombinator<UnionTypeDefinition|TypeDefinitionBase>
 */
function FullTypeString(){
    //(generic ?? atomic) [ | self ]*
    //first try generic, then atomic.
    return new PipeParser()
    ->asSeperator(new LazyParser(fn() => GenericOrAtomic()))
    ->map(function($value){
        if(count($value) === 1){
            return $value[0];
        }
        return new UnionTypeDefinition($value);
    });
}

function parseParamType(string $paramname, string $string, TypeDefinitionBase $fullyQualifiedCodeType): TypeDefinitionBase {
    //Split string on @param paramname
    $parts = explode("$" . $paramname, $string);
    if (count($parts) < 2) {
        throw new \Exception("Param $paramname not found in docstring: $string");
    }
    $parts = explode("@param", $parts[0]);
    $lastPart = trim(array_pop($parts));
    
    //Extract type string
    $result = FullTypeString()->parse($lastPart, 0);
    if (!$result->success) {
        var_dump($result);
        throw new \Exception("Failed to parse type for param $paramname from '$lastPart'");
    }
    $value = $result->value;
    if($value instanceof AtomicTypeDefinition){
        $value = $value->mergeEnhance($fullyQualifiedCodeType);
        //if it is a model, convert to short name
        if(preg_match('/^CanvasApiLibrary\\\\Core\\\\Models\\\\[a-zA-Z]+$/', $value->type)){
            //split then return last part
            $parts = explode("\\", $value->type);
            $value->type = array_pop($parts);
        }
    }

    return $value;
}

function parseReturnType(string $string): TypeDefinitionBase {
    //Split string on @return
    $parts = explode("@return", $string);
    if (count($parts) < 2) {
        throw new \Exception("@return not found in docstring.");
    }
    $afterReturn = trim($parts[1]);
    //Extract type string
    $result = FullTypeString()->parse($afterReturn, 0);
    if (!$result->success) {
        throw new \Exception("Failed to parse return type.");
    }
    return $result->value;
}