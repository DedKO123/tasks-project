<?php

namespace App\Services;

use App\DTO\TaskDTO;
use App\Models\Task;
use App\TaskRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TaskService
{
    public function __construct(protected TaskRepositoryInterface $taskRepository)
    {
    }

    public function getAllTasks(array $filters): Collection
    {
        return $this->taskRepository->getAllTasks($filters);
    }

    public function createTask(TaskDTO $taskDTO): Task
    {
        return $this->taskRepository->createTask($taskDTO);
    }

    public function updateTask(Task $task, TaskDTO $taskDTO): bool
    {
        return $this->taskRepository->updateTask($task, $taskDTO);
    }

    public function markTaskAsDone(Task $task): bool
    {
        return $this->taskRepository->markTaskAsDone($task);
    }

    public function deleteTask(Task $task): bool
    {
        return $this->taskRepository->deleteTask($task);
    }
}
