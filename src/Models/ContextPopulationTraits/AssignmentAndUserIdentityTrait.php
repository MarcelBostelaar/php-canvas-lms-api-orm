<?php

namespace CanvasApiLibrary\Models\ContextPopulationTraits;
use CanvasApiLibrary\Exceptions\MixingDomainsException;
use CanvasApiLibrary\Exceptions\NotPopulatedException;
use CanvasApiLibrary\Exceptions\ChangingIdException;
use CanvasApiLibrary\Models\Utility\ModelInterface;
use CanvasApiLibrary\Models\Domain;
use CanvasApiLibrary\Models\Course;
use CanvasApiLibrary\Models\Assignment;
use CanvasApiLibrary\Models\User;

trait AssignmentAndUserIdentityTrait{
    /**
     * Note to future devs. If the need arises for an identity of just the user, without an assignment,
     * consider reworking the structure of these traits to a subscription type, where instead of inheriting, you 
     */

    use AssignmentIdentityTrait {
        AssignmentIdentityTrait::populateWithContext as private ait_populateWithContext;
        AssignmentIdentityTrait::getContext as private ait_getContext;
        AssignmentIdentityTrait::getMinimumDataRepresentation as private ait_getMinimumDataRepresentation;
        AssignmentIdentityTrait::newFromMinimumDataRepresentation as private ait_newFromMinimumDataRepresentation;
        AssignmentIdentityTrait::validateIdentityIntegrity as private ait_validateIdentityIntegrity;
        AssignmentIdentityTrait::getUniqueId as private ait_getUniqueId;
    }
    protected mixed $user_identity;
    public User $user{
        get { 
            return User::newFromMinimumDataRepresentation($this->user_identity);
        }
        set (User $value) {
            if(!isset($this->user_identity)){
                if($this->domain != $value->domain){
                    $selfDomain = $this->domain->domain;
                    $otherDomain = $value->domain->domain;
                    throw new MixingDomainsException("Tried to save a User from domain '$otherDomain' to an item from domain '$selfDomain'.");
                }
                //same course, allowed to save
                $this->user_identity = $value->getMinimumDataRepresentation();
            }
            else{
                if($this->user_identity != $value->getMinimumDataRepresentation()){
                    throw new ChangingIdException("Tried to change the user of this item");
                }
                //Same user, pass.
            }
        }
    }
    
    /**
     * Populates the model using the provided other models, filling in missing data.
     * @param ModelInterface[] $context A list of context items from which to pull the needed data to populate.
     * @return void
     * @throws ChangingIdException When already set context data is provided again
     * @throws NotPopulatedException When not all required fields are set
     */
    public function populateWithContext(array $context){
        $this->ait_populateWithContext($context);
        foreach($context as $item){
            if($item instanceof User){
                $this->user = $item;
                continue;
            }
        }
    }

    public function getContext(): array{
        return [$this, $this->domain, $this->course, $this->assignment];
    }

    public function getMinimumDataRepresentation(): mixed{
        return [
            self::class => $this->id,
            Domain::class => $this->domain->domain,
            Course::class => $this->course->id,
            Assignment::class => $this->assignment->id,
            User::class => $this->user->id
        ];
    }

    public static function newFromMinimumDataRepresentation($data): static{
        $item = new (self::class)();
        $item->id = $data[self::class];
        $item->domain = new Domain($data[Domain::class]);
        $item->course = Course::newFromMinimumDataRepresentation($data);
        $item->assignment = Assignment::newFromMinimumDataRepresentation($data);
        $item->assignment = User::newFromMinimumDataRepresentation($data);
        return $item;
    }
    
    public function validateIdentityIntegrity() : bool{
        return isset($this->id) && isset($this->domain) && isset($this->course) && isset($this->assignment) && isset($this->user);
    }

    public function getUniqueId(): string{
        return static::class . "-" . $this->domain->domain . "-Course:" . $this->course->id . "-"  . "-Assignment:" . $this->assignment->id . "-"  . "-User:" . $this->user->id . "-" . $this->id;
    }
}
