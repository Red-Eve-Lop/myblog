<?php

namespace App\Domain\Category;

class Category
{
    public function __construct(
        public readonly ?int $id,
        private string $name,
        private bool $isPublic = true,
    ) {}

    public function name(): string
    {
        return $this->name;
    }

    public function isPublic(): bool
    {
        return $this->isPublic;
    }

    public function publish(): void
    {
        $this->isPublic = true;
    }

    public function unpublish(): void
    {
        $this->isPublic = false;
    }
}
