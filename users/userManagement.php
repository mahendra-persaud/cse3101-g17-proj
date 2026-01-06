<?php
$headerPath = __DIR__ . '/includes/header.php';
if (!file_exists($headerPath)) $headerPath = __DIR__ . '/../includes/header.php';
$parts = explode('/', trim($_SERVER['SCRIPT_NAME'], '/'));
$projectRoot = (isset($parts[1]) ? '/' . $parts[0] : '');
$extra_head = '<link rel="stylesheet" href="' . $projectRoot . '/assets/css/darkManagement.css">';
require_once $headerPath;
?>
<div class="Main-Container">
    <?php require_once dirname($headerPath) . '/sidebar.php'; ?>

        <main class="main-dashboard">

            <div class = "header">
                <h2>User Management</h2>

                <div class="user-info">
                    <p>Logged in as: &nbsp<span><strong><?php echo e($_SESSION['username'] ?? 'Admin'); ?></strong></span></p>
                    <a href="<?php echo $projectRoot; ?>/auth/logout.php" class="logout-btn">Logout</a>
                </div>
            </div>

            <h1>Add New User</h1>

            <section class="form-card">
                
                <form>
                    <input type="text" placeholder="Full Name" required>
                    <input type="email" placeholder="Email Address" required>
                    <input type="password" placeholder="Password" required>
                    <select required>
                    <option value="">Select Role</option>
                    <option value="Admin">Admin</option>
                    <option value="Teacher">Teacher</option>
                    <option value="Student">Student</option>
                    </select>
                    <button type="submit">Add User</button>
                </form>
            </section>

             <h2 class = 'heading users'>Users</h2>

            <section class="table-card">
               
                <table>
                    <thead>
                    <tr>
                        <th>Select</th>                        
                        <th>User ID</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td><input type="checkbox" name="user[]" value="1"></td>
                        <td>001</td>
                        <td>John Smith</td>
                        <td>john@example.com</td>
                        <td>Admin</td>
                        <td>
                            <a href="userPage.php?id=1" class="action-link">View</a> |
                            <a href="#" class="action-link">Edit</a> |
                            <a href="#" class="action-link delete">Delete</a>
                        </td>
                    </tr>
                    <tr>
                        <td><input type="checkbox" name="user[]" value="2"></td>
                        <td>002</td>
                        <td>Olivia Johnson</td>
                        <td>olivia@example.com</td>
                        <td>Teacher</td>
                        <td>
                            <a href="userPage.php?id=2" class="action-link">View</a> |
                            <a href="#" class="action-link">Edit</a> |
                            <a href="#" class="action-link delete">Delete</a>
                        </td>
                    </tr>
                     <tr>
                        <td><input type="checkbox" name="user[]" value="3"></td>
                        <td>003</td>
                        <td>Michael Brown</td>
                        <td>michael@example.com</td>
                        <td>Teacher</td>
                        <td>
                            <a href="userPage.php?id=3" class="action-link">View</a> |
                            <a href="#" class="action-link">Edit</a> |
                            <a href="#" class="action-link delete">Delete</a>
                        </td>
                    </tr>
                     <tr>
                        <td><input type="checkbox" name="user[]" value="4"></td>
                        <td>004</td>
                        <td>Emily Davis</td>
                        <td>emily@example.com</td>
                        <td>Teacher</td>
                        <td>
                            <a href="userPage.php?id=4" class="action-link">View</a> |
                            <a href="#" class="action-link">Edit</a> |
                            <a href="#" class="action-link delete">Delete</a>
                        </td>
                    </tr>
                     <tr>
                        <td><input type="checkbox" name="user[]" value="5"></td>
                        <td>005</td>
                        <td>David Wilson</td>
                        <td>david@example.com</td>
                        <td>Admin</td>
                        <td>
                            <a href="userPage.php?id=5" class="action-link">View</a> |
                            <a href="#" class="action-link">Edit</a> |
                            <a href="#" class="action-link delete">Delete</a>
                        </td>
                    </tr>
                    <!-- Add more rows as needed -->
                    </tbody>
                </table>
            </section>

        </main>
    </div>
</body>
</html>


