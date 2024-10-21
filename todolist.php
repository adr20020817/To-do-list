<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>To-Do List</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f0f0f0;
        }
        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        h1 {
            text-align: center;
            color: #4285f4;
        }
        .task-form {
            display: flex;
            margin-bottom: 20px;
        }
        .task-input {
            flex-grow: 1;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 4px 0 0 4px;
        }
        .add-btn {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #4285f4;
            color: white;
            border: none;
            border-radius: 0 4px 4px 0;
            cursor: pointer;
        }
        .task-list {
            list-style-type: none;
            padding: 0;
        }
        .task-item {
            background-color: #f8f9fa;
            margin-bottom: 10px;
            padding: 15px;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .task-item.completed {
            text-decoration: line-through;
            opacity: 0.6;
        }
        .delete-btn {
            background-color: #ea4335;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 4px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>To-Do List</h1>
        
        <form class="task-form" method="POST" action="">
            <input type="text" name="task" class="task-input" placeholder="Add a new task" required>
            <button type="submit" class="add-btn">Add Task</button>
        </form>

        <?php
        error_reporting(E_ALL);
        ini_set('display_errors', 1);

        // Database connection details
        $host = 'localhost';
        $dbname = 'todo_list';
        $username = 'root';
        $password = '';

        // Connecting with database
        $con = mysqli_connect($host, $username, $password, $dbname);

        // Ensuring connection is made
        if (!$con) {
            die("Connection failed: " . mysqli_connect_error());
        }

        // Create task
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['task']) && !empty($_POST['task'])) {
            $task = mysqli_real_escape_string($con, $_POST['task']);
            $query = "INSERT INTO tasks (task_text, completed) VALUES ('$task', 0)";
            if (mysqli_query($con, $query)) {
                echo "<p>Task added successfully!</p>";
            } else {
                echo "<p>Error adding task: " . mysqli_error($con) . "</p>";
            }
        }

        // Delete task
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
            $id = mysqli_real_escape_string($con, $_POST['delete']);
            $query = "DELETE FROM tasks WHERE id = '$id'";
            mysqli_query($con, $query);
        }

        // Toggle task
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['toggle'])) {
            $id = mysqli_real_escape_string($con, $_POST['toggle']);
            $query = "UPDATE tasks SET completed = NOT completed WHERE id = '$id'";
            mysqli_query($con, $query);
        }

        // Fetch all tasks
        $query = "SELECT * FROM tasks ORDER BY id DESC";
        $result = mysqli_query($con, $query);
        $tasks = mysqli_fetch_all($result, MYSQLI_ASSOC);

        if (!empty($tasks)) {
            echo '<ul class="task-list">';
            foreach ($tasks as $task) {
                $completed = $task['completed'] ? 'completed' : '';
                echo '<li class="task-item ' . $completed . '">';
                echo '<span>' . htmlspecialchars($task['task_text']) . '</span>';
                echo '<div>';
                echo '<form method="POST" style="display:inline;">';
                echo '<input type="hidden" name="toggle" value="' . $task['id'] . '">';
                echo '<button type="submit" class="delete-btn">Toggle</button>';
                echo '</form>';
                echo '<form method="POST" style="display:inline;">';
                echo '<input type="hidden" name="delete" value="' . $task['id'] . '">';
                echo '<button type="submit" class="delete-btn">Delete</button>';
                echo '</form>';
                echo '</div>';
                echo '</li>';
            }
            echo '</ul>';
        } else {
            echo '<p>No tasks yet. Add a task to get started!</p>';
        }

        // Close the database connection
        mysqli_close($con);

        ?>
    </div>

   
</body>
</html>
