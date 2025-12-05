<?php

namespace CanvasApiLibrary\Core\Models\Utility;
use CanvasApiLibrary\Core\Exceptions\NotPopulatedException;
use CanvasApiLibrary\Core\Exceptions\ChangingIdException;

interface ModelInterface{
    public function getUniqueId() : string;
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
}