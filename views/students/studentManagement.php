<?php
$headerPath = __DIR__ . '/includes/header.php';
if (!file_exists($headerPath)) $headerPath = __DIR__ . '/../../includes/header.php';
$parts = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$projectRoot = (isset($parts[1]) ? '/' . $parts[0] : '');
$extra_head = '<link rel="stylesheet" href="' . $projectRoot . '/public/assets/css/darkManagement.css">';
require_once $headerPath;
?>
<div class="Main-Container">
    <?php require_once dirname($headerPath) . '/sidebar.php'; ?>

        <main class="main-dashboard">

            <div class = "header">
                <h2>Student Management</h2></div>

            <h1>Add New Student</h1>

            <section class="form-card">
                
                <form>
                    <input type="text" placeholder="Student ID" >
                    <input type="text" placeholder="Full Name" required>
                    <input type="email" placeholder="Email Address" required>
                    <input type="date" placeholder="D.O.B" required>
                    <select required>
                    <option value="">Select Grade</option>
                    <option value="Grade 1">Grade 1</option>
                        <option value="Grade 2">Grade 2</option>
                        <option value="Grade 3">Grade 3</option>
                        <option value="Grade 4">Grade 4</option>
                        <option value="Grade 5">Grade 5</option>
                        <option value="Grade 6">Grade 6</option>
                    </select>
                    <select required>
                    <option value="">Select Class</option>
                    <option value="A">A</option>
                    <option value="B">B</option>
                    <option value="C">C</option>
                    <option value="D">D</option>
                    </select>
                    <button type="submit">Add Student</button>
                </form>
            </section>

             <h2 class = 'heading users'>Students</h2>

            <section class="table-card">
               
                <table>
                    <thead>
                    <tr>
                        <th>Select</th>                        
                        <th>Student ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Date of Birth</th>
                        <th>Grade</th>
                        <th>Class</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><input type="checkbox" name="user[]" value="1"></td>
                        <td>001</td>
                        <td>John Smith</td>
                        <td>john@example.com</td>
                        <td>DOB</td>
                        <td>Grade 1</td>
                        <td>A</td>
                        <td><a href="studentPage.php?id=1" class="action-link">View</a> | <a href="#" class="action-link">Edit</a> | <a href="#" class="action-link delete">Delete</a></td>
                    </tr>
                    <tr>
                        <td><input type="checkbox" name="user[]" value="1"></td>
                        <td>001</td>
                        <td>John Smith</td>
                        <td>john@example.com</td>
                        <td>DOB</td>
                        <td>Grade 1</td>
                        <td>A</td>
                        <td><a href="studentPage.php?id=1" class="action-link">View</a> | <a href="#" class="action-link">Edit</a> | <a href="#" class="action-link delete">Delete</a></td>
                    </tr>
                    <tr>
                        <td><input type="checkbox" name="user[]" value="1"></td>
                        <td>001</td>
                        <td>John Smith</td>
                        <td>john@example.com</td>
                        <td>DOB</td>
                        <td>Grade 1</td>
                        <td>A</td>
                        <td><a href="studentPage.php?id=1" class="action-link">View</a> | <a href="#" class="action-link">Edit</a> | <a href="#" class="action-link delete">Delete</a></td>
                    </tr>
                    <tr>
                        <td><input type="checkbox" name="user[]" value="1"></td>
                        <td>001</td>
                        <td>John Smith</td>
                        <td>john@example.com</td>
                        <td>DOB</td>
                        <td>Grade 1</td>
                        <td>A</td>
                        <td><a href="studentPage.php?id=1" class="action-link">View</a> | <a href="#" class="action-link">Edit</a> | <a href="#" class="action-link delete">Delete</a></td>
                    </tr>
                    <tr>
                        <td><input type="checkbox" name="user[]" value="1"></td>
                        <td>001</td>
                        <td>John Smith</td>
                        <td>john@example.com</td>
                        <td>DOB</td>
                        <td>Grade 1</td>
                        <td>A</td>
                        <td><a href="studentPage.php?id=1" class="action-link">View</a> | <a href="#" class="action-link">Edit</a> | <a href="#" class="action-link delete">Delete</a></td>
                    </tr>
                    <!-- Add more rows as needed -->
                    </tbody>
                </table>
            </section>

        </main>
    </div>
</body>
</html>


