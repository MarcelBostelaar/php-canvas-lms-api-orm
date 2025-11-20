<?php
require_once __DIR__ . '/vendor/autoload.php';

use CanvasApiLibrary\Models\Assignment;
use CanvasApiLibrary\Models\Course;
use CanvasApiLibrary\Models\Domain;
use CanvasApiLibrary\Providers\AssignmentProvider;
use CanvasApiLibrary\Providers\GroupProvider;
use CanvasApiLibrary\Providers\SectionProvider;
use CanvasApiLibrary\Providers\SubmissionProvider;
use CanvasApiLibrary\Providers\UserProvider;
use CanvasApiLibrary\Services\CanvasCommunicator;
use CanvasApiLibrary\Services\CanvasReturnStatus;
use CanvasApiLibrary\Services\StatusHandlerInterface;

function formatted_vardump($data) {
    echo '<pre>' . htmlspecialchars(var_dump($data, true)) . '</pre>';
}
class DummyHandler implements StatusHandlerInterface{
    public function HandleStatus(mixed $data, $status): mixed {
        if($status === CanvasReturnStatus::NOT_FOUND){
            throw new Exception("Resource not found");
        }
        if($status === CanvasReturnStatus::ERROR){
            throw new Exception("An error occurred while processing the request: " . json_encode($data));
        }
        // Simply return the data for testing purposes
        return $data;
    }
}

$canvasCommunicator = new CanvasCommunicator($_ENV['CANVAS_API_TOKEN'] ?? getenv('CANVAS_API_TOKEN'));
$handler = new DummyHandler();

$domain = new Domain("https://flexedu.instructure.com/api/v1");

$course = new Course();
$course->id = 68750;
$course->domain = $domain;
if(!$course->validateIdentityIntegrity()){
    throw new Exception("Course identity integrity validation failed.");
}

$assignment = new Assignment();
$assignment->id = 146427;
$assignment->domain = $domain;
$assignment->course = $course;
formatted_vardump($assignment);
if(!$assignment->validateIdentityIntegrity()){
    throw new Exception("Assignment identity integrity validation failed.");
}

$assignmentProvider = new AssignmentProvider($handler, $canvasCommunicator);
$groupProvider = new GroupProvider($handler, $canvasCommunicator);
$sectionProvider = new SectionProvider($handler, $canvasCommunicator);
$submissionProvider = new SubmissionProvider($handler, $canvasCommunicator);
$userProvider = new UserProvider($handler, $canvasCommunicator);

$assignment = $assignmentProvider->populateAssignment($assignment);
echo "<h2>Populated Assignment</h2>";
formatted_vardump($assignment);
$submissions = $submissionProvider->getSubmissionsForAssignment($assignment);
echo "<h2>Submissions for Assignment</h2>";
formatted_vardump($submissions);
echo "<h2>Raw Users</h2>";
formatted_vardump(array_map(fn($s) => $s->user, $submissions));
$populatedUsers = $userProvider->populateUsers(array_map(fn($s) => $s->user, $submissions));
formatted_vardump($populatedUsers);
$groups = $groupProvider->getAllGroupsInGroupCategory($assignment->group_category);
echo "<h2>Groups in Group Category</h2>";
formatted_vardump($groups);