<?php

namespace CanvasApiLibrary\Core\Models\Utility;
use CanvasApiLibrary\Core\Exceptions\NotPopulatedException;
use CanvasApiLibrary\Core\Exceptions\ChangingIdException;

interface ModelInterface{
    /**
     * Returns a resource key that uniquely identifies this object in the remote API, in this form.
     * Meaning, the Stub and non-stub versions of the same resource do not share a resource key. 
     * Sibling forms of models also have different keys.
     * @return string
     */
    public function getResourceKey() : string;
    /**
     * Returns an ID, which is shared among different forms of the model (stub, non stub, sibling model forms all return the same id)
     * Can be used for lookups and indexing.
     * @return string
     */
    public function getId() : string;
    /**
     * Populates the model using the provided other models, filling in missing data.
     * @param ModelInterface[] $context A list of context items from which to pull the needed data to populate.
     * @return void
     * @throws ChangingIdException When already set context data is provided again
     * @throws NotPopulatedException When not all required fields are set
     */
    public function populateWithContext(array $context);
    /**
     * Returns the context of this model + the model itself to be used for populating context needed for other items.
     * @return ModelInterface[]
     */
    public function getContext() : array;

    /**
     * Checks if all required data is set for updating the item from the api
     * @return bool
     */
    public function validateIdentityIntegrity() : bool;

    public function withMetadataStripped(): ModelInterface;
}