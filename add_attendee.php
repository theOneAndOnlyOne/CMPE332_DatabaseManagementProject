<?php
// add_attendee.php
include 'db_connection.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New Attendee</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <img src="companylogo.png">
        <p>Welcome to the conference organizer's dashboard</p>
    </header>

    <a href="conference.php">Back to Homepage</a>
    <h2>Add New Attendee</h2>

    <form method="POST">
        <label>First Name:</label>
        <input type="text" name="fname" required><br>

        <label>Last Name:</label>
        <input type="text" name="lname" required><br>

        <label>Fee:</label>
        <input type="number" step="0.01" name="fee" required><br>

        <label>Type:</label>
        <select name="type" required>
            <option value="Student">Student</option>
            <option value="Professional">Professional</option>
            <option value="Sponsor">Sponsor</option>
            <option value="Speaker">Speaker</option>
        </select><br>

        <button type="submit" name="add_attendee">Add Attendee</button>
    </form>

    <?php
    if (isset($_POST['add_attendee'])) {
        $fname = $_POST['fname'];
        $lname = $_POST['lname'];
        $fee   = $_POST['fee'];
        $type  = $_POST['type'];

        try {
            // --- Start a transaction to ensure a consistent ID ---
            $pdo->beginTransaction();

            // 1) Generate next available attendee ID
            $sqlGetMaxId = "SELECT COALESCE(MAX(attendee_id), 0) AS max_id FROM Attendee FOR UPDATE";
            // FOR UPDATE locks the row in a transaction, preventing concurrent inserts from grabbing the same max ID
            $stmtMax = $pdo->query($sqlGetMaxId);
            $rowMax = $stmtMax->fetch(PDO::FETCH_ASSOC);
            $new_attendee_id = $rowMax['max_id'] + 1; // fallback to 1 if null

            // 2) Insert the new Attendee
            $sqlInsertAttendee = "INSERT INTO Attendee (attendee_id, fname, lname, fee)
                                  VALUES (?, ?, ?, ?)";
            $stmtIns = $pdo->prepare($sqlInsertAttendee);
            $stmtIns->execute([$new_attendee_id, $fname, $lname, $fee]);

            // 3) Insert into the correct subclass table
            if ($type == "Student") {
                // We will ask the user to assign a room next, so just do nothing here
                // or create a "placeholder" row if desired. 
                // We'll show a form to pick the room below.
            } elseif ($type == "Professional") {
                $stmtPro = $pdo->prepare("INSERT INTO Professional (professional_id) VALUES (?)");
                $stmtPro->execute([$new_attendee_id]);
            } elseif ($type == "Sponsor") {
                $stmtSponsor = $pdo->prepare("INSERT INTO Sponsor (sponsor_id) VALUES (?)");
                $stmtSponsor->execute([$new_attendee_id]);
            } elseif ($type == "Speaker") {
                $stmtSpeaker = $pdo->prepare("INSERT INTO Speaker (speaker_id) VALUES (?)");
                $stmtSpeaker->execute([$new_attendee_id]);
            }

            $pdo->commit();

            // Show success & if it's a Student, let them select a room
            echo "<p style='color:green;'> Attendee added successfully! (ID: $new_attendee_id)</p>";

            if ($type == "Student") {
                // Provide a follow-up form to assign a room
                echo "<form method='POST'>";
                echo "<label>Select Room:</label>";
                echo "<select name='room_number'>";
                $room_query = "SELECT room_number FROM Room";
                $room_stmt = $pdo->query($room_query);
                while ($room_row = $room_stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='{$room_row['room_number']}'>{$room_row['room_number']}</option>";
                }
                echo "</select>";
                echo "<input type='hidden' name='student_id' value='$new_attendee_id'>";
                echo "<button type='submit' name='assign_room'>Assign Room</button>";
                echo "</form>";
            }

        } catch (Exception $e) {
            $pdo->rollBack();
            echo "<p style='color:red;'>Error adding attendee: ".$e->getMessage()."</p>";
        }
    }

    // If user assigns a room after creating a Student
    if (isset($_POST['assign_room'])) {
        $student_id  = $_POST['student_id'];
        $room_number = $_POST['room_number'];

        try {
            // Insert the new Student row referencing the same ID from Attendee
            // We do this outside the transaction or in a new one
            $pdo->beginTransaction();
            $check = $pdo->prepare("SELECT COUNT(*) as cnt FROM Student WHERE student_id = ?");
            $check->execute([$student_id]);
            $cnt = $check->fetch(PDO::FETCH_ASSOC)['cnt'];

            if ($cnt > 0) {
                echo "Error: That student_id already exists. Please try again.";
            } else {
                $stmtStudent = $pdo->prepare("INSERT INTO Student (student_id, room_number) VALUES (?, ?)");
                $stmtStudent->execute([$student_id, $room_number]);
                echo "<p style='color:green;'>Student assigned to room $room_number successfully!</p>";
            }
            $pdo->commit();
        } catch (Exception $ex) {
            $pdo->rollBack();
            echo "<p style='color:red;'>Error assigning room: ".$ex->getMessage()."</p>";
        }
    }
    ?>
</body>
<footer>
    <img src="footericon.png">
    <p>&copy; 2025 Joshua Gonzales. All rights reserved.</p>
</footer>
</html>
