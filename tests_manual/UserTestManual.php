<?php

use Teguh02\Rijanphp\Master\Models\User;

class UserTestManual
{
    public function setUp()
    {
        setupManualDatabase();
    }

    public function testFindAll()
    {
        echo "Running testFindAll... ";
        $userModel = new User();
        $users = $userModel->findAll();

        if (count($users) !== 3) {
            throw new Exception("Expected 3 users, got " . count($users));
        }
        echo "PASS\n";
    }

    public function testFindById()
    {
        echo "Running testFindById... ";
        $userModel = new User();
        $user = $userModel->find(1);

        if ($user->name !== 'John Doe') {
            throw new Exception("Expected John Doe, got " . $user->name);
        }
        echo "PASS\n";
    }

    public function testWhere()
    {
        echo "Running testWhere... ";
        $userModel = new User();
        $users = $userModel->where('email', 'jane@example.com')->findAll();

        if (count($users) !== 1) {
            throw new Exception("Expected 1 user, got " . count($users));
        }
        if ($users[0]->name !== 'Jane Smith') {
            throw new Exception("Expected Jane Smith, got " . $users[0]->name);
        }
        echo "PASS\n";
    }

    public function testChainedWhere()
    {
        echo "Running testChainedWhere... ";
        $userModel = new User();
        $users = $userModel->where('name', 'John Doe')->where('age', 30)->findAll();

        if (count($users) !== 1) {
            throw new Exception("Expected 1 user, got " . count($users));
        }
        echo "PASS\n";
    }

    public function testChainedUpdate()
    {
        echo "Running testChainedUpdate... ";
        $userModel = new User();
        $userModel->where('email', 'john@example.com')->update(['age' => 31]);

        $user = $userModel->find(1);
        if ($user->age != 31) {
            throw new Exception("Expected age 31, got " . $user->age);
        }
        echo "PASS\n";
    }

    public function testChainedDelete()
    {
        echo "Running testChainedDelete... ";
        $userModel = new User();
        $userModel->where('email', 'bob@example.com')->delete();

        $user = $userModel->where('email', 'bob@example.com')->first();
        if ($user !== null) {
            throw new Exception("Expected user to be deleted");
        }
        echo "PASS\n";
    }

    public function testJoin()
    {
        echo "Running testJoin... ";

        $userModel = new User();

        // Select users name and post title
        $results = $userModel->select(['users.name', 'posts.title'])
            ->join('posts', 'users.id', '=', 'posts.user_id')
            ->where('users.id', 1)
            ->findAll();

        // Manual Assertions
        if (!is_array($results)) {
            throw new Exception("Results should be an array");
        }

        if (count($results) !== 2) {
            throw new Exception("Expected 2 results, got " . count($results));
        }

        $firstPost = $results[0];
        if ($firstPost->name !== 'John Doe') {
            throw new Exception("Expected name 'John Doe', got '{$firstPost->name}'");
        }

        if (empty($firstPost->title)) {
            throw new Exception("Expected title to be set");
        }

        echo "PASS\n";
    }

    public function testRawQuery()
    {
        echo "Running testRawQuery... ";

        $userModel = new User();

        // Execute raw query
        $stmt = $userModel->query("SELECT count(*) as count FROM users");
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        $count = $result['count'];

        if ($count != 3) {
            throw new Exception("Expected 3 users, got $count");
        }

        echo "PASS\n";
    }
}
