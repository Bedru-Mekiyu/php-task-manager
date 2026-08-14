<?php

function validateTask(string $title, string $priority): array
{
    $errors = [];

    // Validate title
    $title = trim($title);

    if ($title === "") {
        $errors[] = "Task title is required.";
    }

    if (strlen($title) > 255) {
        $errors[] = "Task title cannot exceed 255 characters.";
    }

    // Validate priority
    $allowedPriorities = ["low", "medium", "high"];

    if (!in_array($priority, $allowedPriorities, true)) {
        $errors[] = "Invalid priority.";
    }

    return $errors;
}