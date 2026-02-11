<?php

if (!function_exists('model')) {
    /**
     * Instantiate a model class.
     */
    function model(string $name)
    {
        // For simplicity, we assume the name is already namespaced if it starts with \
        // or we try to find it in common namespaces.

        if (class_exists($name)) {
            return new $name();
        }

        // Try to handle short names if needed, but for now, full class name is better.
        throw new \Exception("Model [{$name}] not found.");
    }
}
