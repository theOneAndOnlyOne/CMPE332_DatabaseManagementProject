<?php
// switch_session.php
include 'db_connection.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Switch Session</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <img src="companylogo.png">
        <p>Welcome to the conference organizer's dashboard</p>
    </header>
    <a href="conference.php">Back to Homepage</a>
    <section>
        <h2>Switch Session Details</h2>
        <form method="POST">
            <label>Select Session:</label>
            <select name="session_name" required>
                <?php
                $query = "SELECT session_name FROM Sessions";
                $stmt = $pdo->query($query);
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='{$row['session_name']}'>{$row['session_name']}</option>";
                }
                ?>
            </select><br>

            <label>New Date:</label>
            <input type="date" name="new_date" required><br>

            <label>New Start Time:</label>
            <input type="time" name="new_start_time" required><br>

            <label>New End Time:</label>
            <input type="time" name="new_end_time" required><br>

            <label>New Location:</label>
            <input type="text" name="new_location" required><br>

            <button type="submit" name="switch_session">Switch Session</button>
        </form>

        <?php
        if (isset($_POST['switch_session'])) {
            $session_name = $_POST['session_name'];
            $new_date = $_POST['new_date'];
            $new_start_time = $_POST['new_start_time'];
            $new_end_time = $_POST['new_end_time'];
            $new_location = $_POST['new_location'];

            $query = "UPDATE Sessions 
                      SET session_date = ?, start_time = ?, end_time = ?, room_location = ?
                      WHERE session_name = ?";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$new_date, $new_start_time, $new_end_time, $new_location, $session_name]);

            echo "<p style='color:green;'>Session updated successfully!</p>";
        }
        ?>
    </section>
</body>
<footer>
    <img src="footericon.png">
    <p>&copy; 2025 Joshua Gonzales. All rights reserved.</p>
</footer>
</html>
