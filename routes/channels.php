<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});

Broadcast::channel('task.{taskId}', function ($user, $taskId) {
    if ($user->isTaskCreator()) {
        return true;
    }
    return $user->tasks->contains($taskId);
});

Broadcast::channel('project.{projectId}', function ($user, $projectId) {
    if ($user->isProjectCreator()) {
        return true;
    }
    return $user->projects->contains($projectId);
});

Broadcast::channel('notification.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});

Broadcast::channel('project.{projectId}.user.{userId}.tasks', function ($user, $projectId, $userId) {
    return $user->id == $userId && $user->isCollaboratorOfProject($projectId);
});


