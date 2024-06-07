<?php

namespace App;

use App\DTO\TaskDTO;
use App\Models\Task;
use Illuminate\Database\Eloquent\Collection;

interface TaskRepositoryInterface
{
    public function getAllTasks(array $filters): Collection;
    public function createTask(TaskDTO $taskDTO): Task;
    public function updateTask(Task $task, TaskDTO $data): bool;
    public function markTaskAsDone(Task $task): bool;
    public function deleteTask(Task $task): bool;
}
