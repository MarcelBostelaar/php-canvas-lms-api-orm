<?php
require_once __DIR__ . '/vendor/autoload.php';

use CanvasApiLibrary\Core\Models\Assignment;
use CanvasApiLibrary\Core\Models\Course;
use CanvasApiLibrary\Core\Models\Domain;
use CanvasApiLibrary\Core\Providers\AssignmentProvider;
use CanvasApiLibrary\Core\Providers\GroupProvider;
use CanvasApiLibrary\Core\Providers\SectionProvider;
use CanvasApiLibrary\Core\Providers\SubmissionProvider;
use CanvasApiLibrary\Core\Providers\UserProvider;
use CanvasApiLibrary\Core\Services\CanvasCommunicator;
use CanvasApiLibrary\Core\Services\CanvasReturnStatus;
use CanvasApiLibrary\Core\Services\StatusHandlerInterface;


set_error_handler(function ($severity, $message, $file, $line) {
    if (str_starts_with($message, 'Undefined array key')) {
        throw new ErrorException($message, 0, $severity, $file, $line);
    }
    if (str_contains($message, 'Passing null to parameter #1 ($datetime) of type string is deprecated')) {
        throw new ErrorException($message, 0, $severity, $file, $line);
    }
    return false; // let other errors behave normally
});


function formatted_vardump($data) {
    echo '<pre>' . htmlspecialchars(var_dump($data)) . '</pre>';
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

$_ENV = parse_ini_file('.env');

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
$assignment->id = 152202;
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
$userProvider = new UserProvider($handler, $canvasCommunicator)->withinCourse($course);

$assignment = $assignmentProvider->populateAssignment($assignment);
echo "<h2>Populated Assignment</h2>";
formatted_vardump($assignment);
$submissions = $submissionProvider->getSubmissionsInAssignment($assignment);
echo "<h2>Submissions for Assignment</h2>";
formatted_vardump($submissions);
echo "<h2>Raw Users</h2>";
formatted_vardump(array_map(fn($s) => $s->user, $submissions));
$populatedUsers = $userProvider->populateUsers(array_map(fn($s) => $s->user, $submissions));
formatted_vardump($populatedUsers);
$groups = $groupProvider->getAllGroupsInGroupCategory($assignment->group_category);
echo "<h2>Groups in Group Category</h2>";
formatted_vardump($groups);