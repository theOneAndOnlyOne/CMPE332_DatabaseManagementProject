<?php
// add_sponsor.php
include 'db_connection.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add New Sponsor</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <img src="companylogo.png">
        <p>Welcome to the conference organizer's dashboard</p>
    </header>
    <a href="conference.php">Back to Homepage</a>
    <h2>Add New Sponsor</h2>
    <form method="POST">
        <!-- Since a Sponsor is also an Attendee, you may want to collect
             first/last name for the person representing the sponsor, or
             store them in a generic way. -->
        <label>Representative First Name:</label>
        <input type="text" name="fname" required><br>

        <label>Representative Last Name:</label>
        <input type="text" name="lname" required><br>

        <label>Company Name:</label>
        <input type="text" name="company_name" required><br>

        <label>Sponsorship Level:</label>
        <select name="sponsorship_level" required>
            <option value="Bronze">Bronze</option>
            <option value="Silver">Silver</option>
            <option value="Gold">Gold</option>
            <option value="Platinum">Platinum</option>
        </select><br>

        <button type="submit" name="add_sponsor">Add Sponsor</button>
    </form>

    <?php
    if (isset($_POST['add_sponsor'])) {
        $fname             = $_POST['fname'];
        $lname             = $_POST['lname'];
        $company_name      = $_POST['company_name'];
        $sponsorship_level = $_POST['sponsorship_level'];
    
        try {
            // Start a transaction
            $pdo->beginTransaction();
        
            // 1) Find the next attendee_id
            //    (since Attendee is the superclass of Sponsor)
            $stmt = $pdo->query("SELECT COALESCE(MAX(attendee_id), 0) + 1 AS next_id 
                                 FROM Attendee");
            $row  = $stmt->fetch(PDO::FETCH_ASSOC);
            $newSponsorId = $row['next_id'];  // This is now guaranteed to be defined
        
            // 2) Insert into Attendee (superclass)
            //    We'll assume sponsors attend for free (fee=0.00).
            $queryAttendee = "
                INSERT INTO Attendee (attendee_id, fname, lname, fee)
                VALUES (?, ?, ?, 0.00)
            ";
            $stmtAttendee = $pdo->prepare($queryAttendee);
            $stmtAttendee->execute([$newSponsorId, $fname, $lname]);
        
            // 3) Upsert/Insert the company row
            //    to ensure it exists before we reference it in Sponsor
            $queryCompany = "
                INSERT INTO Company (company_name, sponsorship_level, num_emails_set)
                VALUES (?, ?, 0)
                ON DUPLICATE KEY UPDATE sponsorship_level = VALUES(sponsorship_level)
            ";
            $stmtCompany = $pdo->prepare($queryCompany);
            $stmtCompany->execute([$company_name, $sponsorship_level]);
        
            // 4) Insert into Sponsor (subclass)
            //    This references the same ID as Attendee
            $querySponsor = "
                INSERT INTO Sponsor (sponsor_id, company_name)
                VALUES (?, ?)
            ";
            $stmtSponsor = $pdo->prepare($querySponsor);
            $stmtSponsor->execute([$newSponsorId, $company_name]);
        
            // Commit the transaction
            $pdo->commit();
        
            echo "<p style='color:green;'>New sponsor added successfully! (ID: $newSponsorId)</p>";
        } catch (Exception $e) {
            $pdo->rollBack();
            echo "<p style='color:red;'>Error adding sponsor: " . $e->getMessage() . "</p>";
        }
    }
    ?>

</body>
<footer>
    <img src="footericon.png">
    <p>&copy; 2025 Joshua Gonzales. All rights reserved.</p>
</footer>
</html>
