<?php
// total_intake.php
include 'db_connection.php';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Total Intake</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <img src="companylogo.png">
        <p>Welcome to the conference organizer's dashboard</p>
    </header>
    <a href="conference.php">Back to Homepage</a>
    <h2>Total Intake of Conference</h2>

    <?php

    // 1. Fetch data for Students
    $query = "
        SELECT A.fname, A.lname, A.fee
        FROM Student S
        JOIN Attendee A ON S.student_id = A.attendee_id
    ";
    $stmt = $pdo->query($query);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $student_total = array_sum(array_column($students, 'fee'));

    // 2. Fetch data for Speakers
    $query = "
        SELECT A.fname, A.lname, A.fee
        FROM Speaker Sp
        JOIN Attendee A ON Sp.speaker_id = A.attendee_id
    ";
    $stmt = $pdo->query($query);
    $speakers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $speaker_total = array_sum(array_column($speakers, 'fee'));

    // 3. Fetch data for Professionals
    $query = "
        SELECT A.fname, A.lname, A.fee
        FROM Professional P
        JOIN Attendee A ON P.professional_id = A.attendee_id
    ";
    $stmt = $pdo->query($query);
    $professionals = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $professional_total = array_sum(array_column($professionals, 'fee'));

    // --------------------------------------
    // 4. Fetch data for Sponsors
    // --------------------------------------
    $query = "
        SELECT A.fname, A.lname, A.fee
        FROM Sponsor S
        JOIN Attendee A ON S.sponsor_id = A.attendee_id
    ";
    $stmt = $pdo->query($query);
    $sponsors = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $sponsor_total = array_sum(array_column($sponsors, 'fee'));

    // 5. Fetch data for Sponsorships from Company Table
    $query = "
        SELECT sponsorship_level, COUNT(*) as count
        FROM Company
        GROUP BY sponsorship_level
    ";
    $stmt = $pdo->query($query);
    $sponsorships = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Calculate sponsorship totals based on level
    $sponsorship_total = 0;
    $sponsorship_values = [
        'Platinum' => 10000,
        'Gold' => 5000,
        'Silver' => 3000,
        'Bronze' => 1000
    ];
    foreach ($sponsorships as $sponsor) {
        $level = $sponsor['sponsorship_level'];
        $count = $sponsor['count'];
        if (isset($sponsorship_values[$level])) {
            $sponsorship_total += $sponsorship_values[$level] * $count;
        }
    }

    // Calculate the grand total
    $grand_total = $student_total + $speaker_total + $professional_total + $sponsor_total + $sponsorship_total;
    ?>

    <!-- Student Section -->
    <section>
        <h3>Student Registrations</h3>
        <table>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Fee Paid</th>
            </tr>
            <?php foreach ($students as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['fname']); ?></td>
                    <td><?php echo htmlspecialchars($row['lname']); ?></td>
                    <td>$<?php echo htmlspecialchars($row['fee']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <p><strong>Total (Students):</strong> $<?php echo $student_total; ?></p>
    </section>

    <!-- Speaker Section -->
    <section>
        <h3>Speaker Registrations</h3>
        <table>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Fee Paid</th>
            </tr>
            <?php foreach ($speakers as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['fname']); ?></td>
                    <td><?php echo htmlspecialchars($row['lname']); ?></td>
                    <td>$<?php echo htmlspecialchars($row['fee']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <p><strong>Total (Speakers):</strong> $<?php echo $speaker_total; ?></p>
    </section>

    <!-- Professional Section -->
    <section>
        <h3>Professional Registrations</h3>
        <table>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Fee Paid</th>
            </tr>
            <?php foreach ($professionals as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['fname']); ?></td>
                    <td><?php echo htmlspecialchars($row['lname']); ?></td>
                    <td>$<?php echo htmlspecialchars($row['fee']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <p><strong>Total (Professionals):</strong> $<?php echo $professional_total; ?></p>
    </section>

    <!-- Sponsor Section -->
    <section>
        <h3>Sponsor Registrations</h3>
        <table>
            <tr>
                <th>First Name</th>
                <th>Last Name</th>
                <th>Fee Paid</th>
            </tr>
            <?php foreach ($sponsors as $row): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['fname']); ?></td>
                    <td><?php echo htmlspecialchars($row['lname']); ?></td>
                    <td>$<?php echo htmlspecialchars($row['fee']); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <p><strong>Total (Sponsors):</strong> $<?php echo $sponsor_total; ?></p>
    </section>

    <!-- Sponsorship Section -->
    <section>
        <h3>Sponsorships</h3>
        <table>
            <tr>
                <th>Sponsorship Level</th>
                <th>Count</th>
                <th>Total Contribution</th>
            </tr>
            <?php foreach ($sponsorships as $sponsor): ?>
                <tr>
                    <td><?php echo htmlspecialchars($sponsor['sponsorship_level']); ?></td>
                    <td><?php echo htmlspecialchars($sponsor['count']); ?></td>
                    <td>$<?php echo number_format($sponsorship_values[$sponsor['sponsorship_level']] * $sponsor['count'], 2); ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
        <p><strong>Total (Sponsorships):</strong> $<?php echo number_format($sponsorship_total, 2); ?></p>
    </section>

    <!-- Grand Total Section -->
    <section>
        <h3>Total Intake</h3>
        <p><strong>Grand Total (All Fees + Sponsorships):</strong> $<?php echo number_format($grand_total, 2); ?></p>
    </section>
</body>
<footer>
    <img src="footericon.png">
    <p>&copy; 2025 Joshua Gonzales. All rights reserved.</p>
</footer>
</html>
