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
                <h2>Class Management</h2></div>

            <h1>Add New Class</h1>

            <section class="form-card">
                
                <form>
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
                        <option value="">Select Teacher</option>
                        <option value="Mrs. Smith">Mrs. Smith</option>
                        <option value="Mr. Johnson">Mr. Johnson</option>
                        <option value="Miss Brown">Miss Brown</option>
                    </select>
                    <input type="number" placeholder="Capacity" min="1" required>
                    <button type="submit">Add Class</button>
                </form>
            </section>

             <h2 class = 'heading users'>Classes</h2>

            <section class="table-card">
                <table>
                    <thead>
                        <tr>
                        <th>Select</th>
                        <th>Class Name</th>
                        <th>Grade</th>
                        <th>Teacher</th>
                        <th>No. of Students</th>
                        <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><input type="checkbox" name="class[]" value="1"></td>
                            <td>Grade 1A</td>
                            <td>Grade 1</td>
                            <td>Mrs. Smith</td>
                            <td>25</td>
                            <td><a href="#">Edit</a> | <a href="#">Delete</a></td>
                        </tr>
                        <tr>
                            <td><input type="checkbox" name="class[]" value="1"></td>
                            <td>Grade 1A</td>
                            <td>Grade 1</td>
                            <td>Mrs. Smith</td>
                            <td>25</td>
                            <td><a href="#">Edit</a> | <a href="#">Delete</a></td>
                        </tr>
                        <tr>
                            <td><input type="checkbox" name="class[]" value="1"></td>
                            <td>Grade 1A</td>
                            <td>Grade 1</td>
                            <td>Mrs. Smith</td>
                            <td>25</td>
                            <td><a href="#">Edit</a> | <a href="#">Delete</a></td>
                        </tr>
                        <tr>
                            <td><input type="checkbox" name="class[]" value="1"></td>
                            <td>Grade 1A</td>
                            <td>Grade 1</td>
                            <td>Mrs. Smith</td>
                            <td>25</td>
                            <td><a href="#">Edit</a> | <a href="#">Delete</a></td>
                        </tr>
                        <tr>
                            <td><input type="checkbox" name="class[]" value="1"></td>
                            <td>Grade 1A</td>
                            <td>Grade 1</td>
                            <td>Mrs. Smith</td>
                            <td>25</td>
                            <td><a href="#">Edit</a> | <a href="#">Delete</a></td>
                        </tr>
                        <tr>
                            <td><input type="checkbox" name="class[]" value="1"></td>
                            <td>Grade 1A</td>
                            <td>Grade 1</td>
                            <td>Mrs. Smith</td>
                            <td>25</td>
                            <td><a href="#">Edit</a> | <a href="#">Delete</a></td>
                        </tr>
                        <!-- More rows -->
                    </tbody>
                </table>
                <!-- <button type="submit" class="delete-btn">Delete Selected</button> -->
            </section>

        </main>
    </div>
</body>
</html>