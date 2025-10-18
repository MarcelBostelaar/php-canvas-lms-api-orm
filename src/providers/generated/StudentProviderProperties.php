<?php
/* Automatically generated to provide array mapped versions of methods in a provider, 
as well as missing alias methods for models with multiple plural names.
Using provider and plurals defined in the models. */

namespace CanvasApiLibrary\Providers;

use CanvasApiLibrary\Providers\Utility\Lookup;
use CanvasApiLibrary\Models\Student;
use CanvasApiLibrary\Models\Domain;
use CanvasApiLibrary\Models\Group;
use CanvasApiLibrary\Models\Section;

trait StudentProviderProperties{
    abstract public function populateStudent(Student $student);
    
    /**
     * Array variant of populateStudent
     * @param Student[] $students
     * @return Student[]
     */
    public function populateStudents(array $students): array{
        return array_map(fn($x) => $this->populateStudent($x), $students);
    }

    abstract public function getStudentsInGroup(Domain $domain, Group $group) : array;
    
    /**
     * Summary of getStudentsInGroups
     * @param Group[] $groups
     * @return Lookup<Group, Student>
     */
    public function getStudentsInGroups(Domain $domain, array $groups): Lookup{
        $lookup = new Lookup();
        foreach($groups as $group){
            $lookup->add($group, $this->getStudentsInGroup($domain, $group));
        }
        return $lookup;
    }

    abstract public function getStudentsInSection(Domain $domain, Section $section) : array;
    
    /**
     * Summary of getStudentsInSections
     * @param Section[] $sections
     * @return Lookup<Section, Student>
     */
    public function getStudentsInSections(Domain $domain, array $sections): Lookup{
        $lookup = new Lookup();
        foreach($sections as $section){
            $lookup->add($section, $this->getStudentsInSection($domain, $section));
        }
        return $lookup;
    }
}
