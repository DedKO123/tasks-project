<?php

namespace Database\Seeders;

use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\User;
use App\Repositories\TaskRepository;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $users = User::all();

        foreach ($users as $user) {
            $rootTask1 = Task::create([
                'user_id' => $user->id,
                'title' => 'Root Task 1 for ' . $user->name,
                'description' => 'Description for root task 1',
                'status' => TaskStatus::TODO,
                'priority' => TaskPriority::MEDIUM,
            ]);

            $rootTask2 = Task::create([
                'user_id' => $user->id,
                'title' => 'Root Task 2 for ' . $user->name,
                'description' => 'Description for root task 2',
                'status' => TaskStatus::TODO,
                'priority' => TaskPriority::HIGH,
            ]);

            $subTask1 = Task::create([
                'user_id' => $user->id,
                'parent_id' => $rootTask1->id,
                'title' => 'Sub Task 1 for Root Task 1',
                'description' => 'Description for sub task 1',
                'status' => TaskStatus::TODO,
                'priority' => TaskPriority::LOW,
            ]);

            $subTask2 = Task::create([
                'user_id' => $user->id,
                'parent_id' => $rootTask1->id,
                'title' => 'Sub Task 2 for Root Task 1',
                'description' => 'Description for sub task 2',
                'status' => TaskStatus::DONE,
                'priority' => TaskPriority::MEDIUM,
            ]);

            $subSubTask = Task::create([
                'user_id' => $user->id,
                'parent_id' => $subTask1->id,
                'title' => 'Sub-Sub Task for Sub Task 1',
                'description' => 'Description for sub-sub task',
                'status' => TaskStatus::TODO,
                'priority' => TaskPriority::HIGH,
            ]);
        }
    }
}
