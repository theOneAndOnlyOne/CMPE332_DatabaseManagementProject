<?php
// Include the database connection
include 'db_connection.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Conference Management System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <header>
        <img src="companylogo.png">
        <p>Welcome to the conference organizer's dashboard</p>
    </header>
    
    <nav>
        <a href="add_attendee.php">Add New Attendee</a>
        <a href="add_sponsor.php">Add New Sponsor</a>
        <a href="delete_sponsor.php">Delete Sponsor</a>
        <a href="switch_session.php">Switch Session</a>
        <a href="total_intake.php">See Total Intake</a>
    </nav>

    <h1>View Database Info</h1>
    <section>
        <h2>Sub-Committee Members</h2>
        <form method="POST">
            <label>Select Sub-Committee:</label>
            <select name="subcommittee_name">
                <option value="all">All Sub-Committees</option>
                <?php
                $query = "SELECT subcommittee_name FROM Subcommittee";
                $stmt = $pdo->query($query);
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='{$row['subcommittee_name']}'>{$row['subcommittee_name']}</option>";
                }
                ?>
            </select>
            <button type="submit" name="view_members">View Members</button>
        </form>
        <?php
        if (isset($_POST['view_members'])) {
            $subcommittee = $_POST['subcommittee_name'];
            if ($subcommittee == 'all') {
            $query = "SELECT Member.member_id, fname, lname, subcommittee_name FROM Member 
                  INNER JOIN HasSubCommitteeMembers ON Member.member_id = HasSubCommitteeMembers.member_id";
            $stmt = $pdo->query($query);
            echo "<h3>All Sub-Committee Members:</h3>";
            } else {
            $query = "SELECT Member.member_id, fname, lname, subcommittee_name FROM Member 
                  INNER JOIN HasSubCommitteeMembers ON Member.member_id = HasSubCommitteeMembers.member_id
                  WHERE HasSubCommitteeMembers.subcommittee_name = ?";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$subcommittee]);
            echo "<h3>Members of $subcommittee Sub-Committee:</h3>";
            }
            echo "<table><tr><th>Member ID</th><th>First Name</th><th>Last Name</th><th>Sub-Committee</th></tr>";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>
                <td>{$row['member_id']}</td>
                <td>{$row['fname']}</td>
                <td>{$row['lname']}</td>
                <td>{$row['subcommittee_name']}</td>
                  </tr>";
            }
            echo "</table>";
        }
        ?>
    </section>

    <section>
        <h2>Students in Hotel Room</h2>
        <form method="POST">
            <label>Select Room Number:</label>
            <select name="room_number">
                <option value="all">All Rooms</option>
                <?php
                $query = "SELECT room_number FROM Room";
                $stmt = $pdo->query($query);
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='{$row['room_number']}'>{$row['room_number']}</option>";
                }
                ?>
            </select>
            <button type="submit" name="view_room_students">View Students</button>
        </form>
        <?php
        if (isset($_POST['view_room_students'])) {
            $room_number = $_POST['room_number'];
            if ($room_number == 'all') {
                $query = "SELECT Student.student_id, fname, lname, room_number FROM Attendee
                          INNER JOIN Student ON Attendee.attendee_id = Student.student_id";
                $stmt = $pdo->query($query);
                echo "<h3>All Students in Hotel Rooms:</h3>";
            } else {
                $query = "SELECT Student.student_id, fname, lname, room_number FROM Attendee
                          INNER JOIN Student ON Attendee.attendee_id = Student.student_id
                          WHERE Student.room_number = ?";
                $stmt = $pdo->prepare($query);
                $stmt->execute([$room_number]);
                echo "<h3>Students in Room $room_number:</h3>";
            }
            echo "<table><tr><th>Student ID</th><th>First Name</th><th>Last Name</th><th>Room Number</th></tr>";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>
                        <td>{$row['student_id']}</td>
                        <td>{$row['fname']}</td>
                        <td>{$row['lname']}</td>
                        <td>{$row['room_number']}</td>
                      </tr>";
            }
            echo "</table>";
        }
        ?>
    </section>

    <section>
        <h2>Conference Schedule</h2>
        <p>View the conference schedule for a specific date or all dates</p>
        <p>Enter the date in the format YYYY-MM-DD between 2026-07-01 to 2026-07-03</p>
        <form method="POST">
            <label>Select Date:</label>
            <input type="date" name="schedule_date">
            <button type="submit" name="view_schedule">View Schedule</button>
            <button type="submit" name="view_all_schedules">View All Schedules</button>
        </form>
        <?php
        if (isset($_POST['view_schedule']) || isset($_POST['view_all_schedules'])) {
            if (isset($_POST['view_all_schedules'])) {
                $query = "SELECT session_date, session_name, room_location, start_time, end_time FROM Sessions";
                $stmt = $pdo->query($query);
                echo "<h3>All Schedules:</h3>";
            } else {
                $schedule_date = $_POST['schedule_date'];
                $query = "SELECT session_name, room_location, start_time, end_time FROM Sessions WHERE session_date = ?";
                $stmt = $pdo->prepare($query);
                $stmt->execute([$schedule_date]);
                echo "<h3>Schedule for $schedule_date:</h3>";
            }
            echo "<table><tr><th>Date</th><th>Session Name</th><th>Location</th><th>Start Time</th><th>End Time</th></tr>";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>
                        <td>{$row['session_date']}</td>
                        <td>{$row['session_name']}</td>
                        <td>{$row['room_location']}</td>
                        <td>{$row['start_time']}</td>
                        <td>{$row['end_time']}</td>
                      </tr>";
            }
            echo "</table>";
        }
        ?>
    </section>

    <section>
        <h2>Sponsors and Sponsorship Levels</h2>
        <?php
        $query = "SELECT company_name, sponsorship_level FROM Company";
        $stmt = $pdo->query($query);
        echo "<table><tr><th>Company Name</th><th>Sponsorship Level</th></tr>";
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>
                    <td>{$row['company_name']}</td>
                    <td>{$row['sponsorship_level']}</td>
                  </tr>";
        }
        echo "</table>";
        ?>
    </section>

    <section>
        <h2>Jobs Available by Company</h2>
        <form method="POST">
            <label>Select Company:</label>
            <select name="company_name">
                <option value="all">All Companies</option>
                <?php
                $query = "SELECT company_name FROM Company";
                $stmt = $pdo->query($query);
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='{$row['company_name']}'>{$row['company_name']}</option>";
                }
                ?>
            </select>
            <button type="submit" name="view_jobs">View Jobs</button>
        </form>
        <?php
        if (isset($_POST['view_jobs'])) {
            $company_name = $_POST['company_name'];
            if ($company_name == 'all') {
                $query = "SELECT company_name, job_title, pay_rate, duration, city FROM JobAd";
                $stmt = $pdo->query($query);
            } else {
                $query = "SELECT company_name, job_title, pay_rate, duration, city FROM JobAd WHERE company_name = ?";
                $stmt = $pdo->prepare($query);
                $stmt->execute([$company_name]);
            }
            echo "<h3>Jobs Available:</h3>";
            echo "<table><tr><th>Company Name</th><th>Job Title</th><th>Pay Rate</th><th>Duration</th><th>City</th></tr>";
            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                echo "<tr>
                        <td>{$row['company_name']}</td>
                        <td>{$row['job_title']}</td>
                        <td>{$row['pay_rate']}</td>
                        <td>{$row['duration']}</td>
                        <td>{$row['city']}</td>
                      </tr>";
            }
            echo "</table>";
        }
        ?>
    </section>
</body>
<footer>
    <img src="footericon.png">
    <p>&copy; 2025 Joshua Gonzales. All rights reserved.</p>
</footer>
</html>
