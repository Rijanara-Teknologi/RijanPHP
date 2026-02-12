<?php

use Teguh02\Rijanphp\Core\View\View;

class ViewTestManual
{
    public function setUp()
    {
        // Add path to manual views
        View::addPath(__DIR__ . '/views');

        // Clear previous state (sections, etc)
        View::clear();
    }

    public function testRenderSimple()
    {
        echo "Running testRenderSimple... ";

        $output = View::render('simple');

        if (strpos($output, '<h1>Hello World</h1>') === false) {
            throw new Exception("Expected '<h1>Hello World</h1>', got: " . $output);
        }

        echo "PASS\n";
    }

    public function testRenderWithData()
    {
        echo "Running testRenderWithData... ";

        $output = View::render('data', ['name' => 'RijanPHP']);

        if (strpos($output, '<h1>Hello RijanPHP</h1>') === false) {
            throw new Exception("Expected '<h1>Hello RijanPHP</h1>', got: " . $output);
        }

        echo "PASS\n";
    }

    public function testRenderLayout()
    {
        echo "Running testRenderLayout... ";

        // Render child view which extends layout
        // Note: The child view uses $this->extend('layout')
        // In this simple manual setup, we need to ensure ViewEngine can find 'layout'
        // Since we added the path, it should work.

        $output = View::render('child');

        // Check for layout parts
        if (strpos($output, '<header>Header</header>') === false) {
            throw new Exception("Layout header missing");
        }

        if (strpos($output, '<footer>Footer</footer>') === false) {
            throw new Exception("Layout footer missing");
        }

        // Check for child content
        if (strpos($output, '<h1>Child Content</h1>') === false) {
            throw new Exception("Child content missing");
        }

        echo "PASS\n";
    }
}
