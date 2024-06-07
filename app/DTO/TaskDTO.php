<?php

namespace App\DTO;

final class TaskDTO
{
    private ?string $title = null;
    private ?string $description = null;
    private ?string $status = null;
    private ?string $priority = null;
    private ?int $user_id = null;
    private ?int $parent_id = null;

    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function setPriority(string $priority): void
    {
        $this->priority = $priority;
    }

    public function setUserId(int $user_id): void
    {
        $this->user_id = $user_id;
    }

    public function setParentId(?int $parent_id): void
    {
        $this->parent_id = $parent_id;
    }


    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function getPriority(): string
    {
        return $this->priority;
    }

    public function getUserId(): int
    {
        return $this->user_id;
    }

    public function getParentId(): ?int
    {
        return $this->parent_id;
    }

    public function toArray(): array
    {
        return [
            'title' => $this->title,
            'description' => $this->description,
            'status' => $this->status,
            'priority' => $this->priority,
            'user_id' => $this->user_id,
            'parent_id' => $this->parent_id,
        ];
    }
}
