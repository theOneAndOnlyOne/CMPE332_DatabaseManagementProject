<?php
// delete_sponsor.php
include 'db_connection.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Delete Sponsor</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <img src="companylogo.png">
        <p>Welcome to the conference organizer's dashboard</p>
    </header>
    <a href="conference.php">Back to Homepage</a>
    <section>
        <h2>Delete Sponsor and Associated Attendees</h2>
        <form method="POST">
            <label>Select Sponsor:</label>
            <select name="company_name" required>
                <?php
                $query = "SELECT company_name FROM Company";
                $stmt = $pdo->query($query);
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<option value='{$row['company_name']}'>{$row['company_name']}</option>";
                }
                ?>
            </select>
            <button type="submit" name="delete_sponsor">Delete Sponsor</button>
        </form>

        <?php
        if (isset($_POST['delete_sponsor'])) {
            $company_name = $_POST['company_name'];

            // Delete all Sponsors associated with the company
            $query = "DELETE FROM Sponsor WHERE company_name = ?";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$company_name]);

            // Delete the Company
            $query = "DELETE FROM Company WHERE company_name = ?";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$company_name]);

            echo "<p style='color:green;'>Sponsor and associated attendees deleted successfully!</p>";
        }
        ?>
        
    </section>
</body>
<footer>
    <img src="footericon.png">
    <p>&copy; 2025 Joshua Gonzales. All rights reserved.</p>
</footer>
</html>
