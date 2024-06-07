<?php

namespace App\Repositories;

use App\DTO\TaskDTO;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\TaskRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class TaskRepository implements TaskRepositoryInterface
{
    public function getAllTasks(array $filters): Collection
    {
        $query = Task::query()
            ->where('user_id', auth()->id())
            ->whereNull('parent_id')
            ->filter($filters);

        if (isset($filters['sort'])) {
            $query->getQuery()->orders = [];

            if (isset($filters['sort']['field1'])) {
                $order1 = $filters['sort']['order1'] ?? 'asc';
                $query->orderBy($filters['sort']['field1'], $order1);
            }

            if (isset($filters['sort']['field2'])) {
                $order2 = $filters['sort']['order2'] ?? 'asc';
                $query->orderBy($filters['sort']['field2'], $order2);
            }
        }

        $tasks = $query->get();

        return $this->buildHierarchy($tasks, $filters);
    }

    private function buildHierarchy(Collection $tasks, array $filters): Collection
    {
        $tasks->each(function ($task) use ($filters) {
            $task->children = $this->getChildren($task, $filters);
        });

        return $tasks;
    }

    private function getChildren(Task $task, array $filters)
    {
        $children = $task->children()
            ->filter($filters)
            ->get();

        $children->each(function ($child) use ($filters) {
            $child->children = $this->getChildren($child, $filters);
        });

        return $children;
    }

    public function createTask(TaskDTO $taskDTO): Task
    {
        return auth()->user()->tasks()->create($taskDTO->toArray());
    }

    public function updateTask(Task $task, TaskDTO $taskDTO): bool
    {
        return $task->update($taskDTO->toArray());
    }

    public function markTaskAsDone(Task $task): bool
    {
        if (!$this->checkChildrenStatus($task)) {
            return false;
        }

        return $task->update(['status' => TaskStatus::DONE, 'completed_at' => now()]);
    }

    private function checkChildrenStatus(Task $task): bool
    {

        foreach ($task->children as $child) {
            if ($child->children()->exists() && !$this->checkChildrenStatus($child)) {
                return false;
            }
            if ($child->status == TaskStatus::TODO) {
                return false;
            }
        }
        return true;
    }

    public function deleteTask(Task $task): bool
    {
        return $task->delete();
    }


}
