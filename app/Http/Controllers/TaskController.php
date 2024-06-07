<?php

namespace App\Http\Controllers;

use App\DTO\TaskDTO;
use App\Enums\TaskPriority;
use App\Enums\TaskStatus;
use App\Models\Task;
use App\Services\TaskService;
use BenSampo\Enum\Rules\EnumValue;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TaskController extends Controller
{
    /**
     * @OA\Info(
     *     title="Название вашего API",
     *     version="1.0.0",
     *      @OA\SecurityScheme(
     *        securityScheme="bearerAuth",
     *        type="http",
     *        scheme="bearer",
     *        bearerFormat="JWT",
     *       ),
     *     @OA\Contact(
     *         name="Ваше имя",
     *         email="ваш email"
     *     ),
     * )
     */

    public function __construct(public TaskService $taskService)
    {
    }

    /**
     * @OA\Get (
     *     path="/api/tasks",
     *     summary="Get all tasks",
     *     tags={"Tasks"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Task status",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"todo", "done"}
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="priority",
     *         in="query",
     *         description="Task priority",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"1", "2", "3", "4", "5"}
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search by title or description",
     *         required=false,
     *         @OA\Schema(
     *             type="string"
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort[field1]",
     *         in="query",
     *         description="Sort by first column",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"created_at", "completed_at", "priority"}
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort[field2]",
     *         in="query",
     *         description="Sort by second column",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"created_at", "completed_at", "priority"}
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort[order1]",
     *         in="query",
     *         description="Sort order for first column",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"asc", "desc"}
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="sort[order2]",
     *         in="query",
     *         description="Sort order for second column",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *             enum={"asc", "desc"}
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="List of tasks",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent()
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent()
     *     )
     * )
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $filters = $request->only(['status', 'priority', 'search', 'sort']);

        $tasks = $this->taskService->getAllTasks($filters);

        return response()->json($tasks);
    }

    /**
     * @OA\Post(
     *     path="/api/tasks",
     *     summary="Create a new task",
     *     tags={"Tasks"},
     *     security={{"bearerAuth": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         description="Task data",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 required={"status", "priority", "title", "description"},
     *                 @OA\Property(
     *                     property="title",
     *                     type="string",
     *                     example="Task Title"
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string",
     *                     example="Task Description"
     *                 ),
     *                 @OA\Property(
     *                     property="priority",
     *                     type="integer",
     *                     example=1
     *                 ),
     *                 @OA\Property(
     *                     property="status",
     *                     type="string",
     *                     example="todo"
     *                 ),
     *                 @OA\Property(
     *                     property="parent_id",
     *                     type="integer",
     *                     example=123,
     *                     nullable=true
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Task created successfully",
     *         @OA\JsonContent(),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *         @OA\JsonContent(),
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *         @OA\JsonContent(),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object"
     *             )
     *         )
     *     )
     * )
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        try {
            $requestData = $request->validate([
                'title' => ['required', 'string'],
                'description' => ['required', 'string'],
                'priority' => ['required', new EnumValue(TaskPriority::class)],
                'status' => ['required', new EnumValue(TaskStatus::class)],
                'parent_id' => ['nullable', 'exists:tasks,id'],
            ]);

            $taskDTO = new TaskDTO();
            $taskDTO->setTitle($requestData['title']);
            $taskDTO->setDescription($requestData['description']);
            $taskDTO->setPriority($requestData['priority']);
            $taskDTO->setStatus($requestData['status']);
            $taskDTO->setParentId($requestData['parent_id'] ?? null);
            $taskDTO->setUserId(auth()->id());

            $task = $this->taskService->createTask($taskDTO);

            return response()->json($task, 201);

        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Put(
     *     path="/api/tasks/{task}",
     *     summary="Update a task",
     *     tags={"Tasks"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="task",
     *         in="path",
     *         description="Task id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         description="Task data",
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(
     *                     property="title",
     *                     type="string",
     *                 ),
     *                 @OA\Property(
     *                     property="description",
     *                     type="string"
     *                 ),
     *                 @OA\Property(
     *                     property="priority",
     *                     type="integer",
     *                     enum={1, 2, 3, 4, 5}
     *                 ),
     *                 @OA\Property(
     *                     property="status",
     *                     type="string",
     *                     enum={"todo", "done"}
     *                 ),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Task updated successfully",
     *      @OA\JsonContent(),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *  @OA\JsonContent(),
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="error",
     *  @OA\JsonContent(),
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *  @OA\JsonContent(),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object"
     *             )
     *         )
     *     )
     * )
     */
    public function update(Task $task, Request $request): \Illuminate\Http\JsonResponse
    {
        if ($request->user()->cannot('update', $task)) {
            return response()->json([
                'message' => 'You do not have permission to update this task',
            ], 403);
        }
        try {
            $requestData = $request->validate([
                'title' => ['string'],
                'description' => ['string'],
                'priority' => [new EnumValue(TaskPriority::class)],
                'status' => [new EnumValue(TaskStatus::class)],
                'parent_id' => ['exists:tasks,id'],
                'user_id' => ['exists:users,id'],
            ]);

            $taskDTO = new TaskDTO();
            $taskDTO->setTitle($requestData['title'] ?? $task->title);
            $taskDTO->setDescription($requestData['description'] ?? $task->description);
            $taskDTO->setPriority($requestData['priority'] ?? $task->priority);
            $taskDTO->setStatus($requestData['status'] ?? $task->status);
            $taskDTO->setParentId($requestData['parent_id'] ?? $task->parent_id);
            $taskDTO->setUserId($requestData['user_id'] ?? $task->user_id);

            $this->taskService->updateTask($task, $taskDTO);

            return response()->json([
                'task' => $task,
                'message' => 'Task updated successfully'
            ],
                201);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation Error',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Patch(
     *     path="/api/tasks/{task}/done",
     *     summary="Mark a task as done",
     *     tags={"Tasks"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="task",
     *         in="path",
     *         description="Task id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Task marked as done successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *  @OA\JsonContent(),
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *  @OA\JsonContent(),
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string"
     *             ),
     *             @OA\Property(
     *                 property="errors",
     *                 type="object"
     *             )
     *         )
     *     )
     * )
     */
    public function markTaskAsDone(Task $task): \Illuminate\Http\JsonResponse
    {
        if (auth()->user()->cannot('update', $task)) {
            return response()->json([
                'message' => 'You do not have permission to update this task',
            ], 403);
        }
        try {
            $message = 'Task marked as done successfully';
            if (!$this->taskService->markTaskAsDone($task)) {
                $message = 'Some children tasks are not completed yet';
            }

            return response()->json([
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'errors' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/tasks/{task}",
     *     summary="Delete a task",
     *     tags={"Tasks"},
     *     security={{"bearerAuth": {}}},
     *     @OA\Parameter(
     *         name="task",
     *         in="path",
     *         description="Task id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Task deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(
     *                 property="message",
     *                 type="string"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated",
     *  @OA\JsonContent(),
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden",
     *  @OA\JsonContent(),
     *     )
     * )
     */
    public function destroy(Task $task): \Illuminate\Http\JsonResponse
    {
        if (auth()->user()->cannot('delete', $task)) {
            return response()->json([
                'message' => 'You do not have permission to delete this task',
            ], 403);
        }
        if ($task->status == TaskStatus::DONE) {
            return response()->json([
                'message' => 'You cannot delete a completed task',
            ], 403);
        }
        try {
            $this->taskService->deleteTask($task);

            return response()->json([
                'message' => 'Task deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An unexpected error occurred',
                'errors' => $e->getMessage()
            ], 500);
        }
    }
}
