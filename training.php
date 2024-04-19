<?php
// Database connection parameters
$servername = "localhost";
$username = "sgamura2";
$password = "Aswin2401";
$dbname = "sgamura2";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Initialize variables
$topics = array();
$days = array();
$times = array();
$booking_message = "";

// If form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $topic = $_POST['topic'];
    $day = $_POST['day'];
    $time = $_POST['time'];
    $name = $_POST['name'];
    $email = $_POST['email'];

    // Validate name if not empty
    if (!empty($name) && !preg_match("/^[a-zA-Z][a-zA-Z' -]*[a-zA-Z]$/", $name)) {
        $booking_message = "Error: Invalid name format.";
    } else {
        // Validate email if not empty
        if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $booking_message = "Error: Invalid email address.";
        } else {
            // Check if topic, day, and time are selected
            if (!empty($topic) && !empty($day) && !empty($time)) {
                // Check if session is available
                $sql_check_capacity = "SELECT capacity FROM sessions WHERE topic_id=(SELECT id FROM topics WHERE name='$topic') AND day_of_week='$day' AND start_time='$time'";
                $result = $conn->query($sql_check_capacity);
                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $capacity = $row['capacity'];
                    // Check if remaining capacity of the session is greater than zero
                    if ($capacity > 0) {
                        // Decrement capacity
                        $capacity--;
                        // Update capacity
                        $sql_update_capacity = "UPDATE sessions SET capacity=$capacity WHERE topic_id=(SELECT id FROM topics WHERE name='$topic') AND day_of_week='$day' AND start_time='$time'";
                        if ($conn->query($sql_update_capacity) === TRUE) {
                            // Insert booking
                            $sql_insert_booking = "INSERT INTO bookings (session_id, student_name, student_email) VALUES ((SELECT id FROM sessions WHERE topic_id=(SELECT id FROM topics WHERE name='$topic') AND day_of_week='$day' AND start_time='$time'), '$name', '$email')";
                            if ($conn->query($sql_insert_booking) === TRUE) {
                                $booking_message = "Booking successful!";
                            } else {
                                $booking_message = "Error: " . $sql_insert_booking . "<br>" . $conn->error;
                            }
                        } else {
                            $booking_message = "Error: " . $sql_update_capacity . "<br>" . $conn->error;
                        }
                    } else {
                        $booking_message = "Session is already full.";
                    }
                } else {
                    $booking_message = "Session not found.";
                }
            } else {
                $booking_message = "Please select a topic, day, and time.";
            }
        }
    }
}

// Retrieve topics from the database
$sql_topics = "SELECT name FROM topics";
$result_topics = $conn->query($sql_topics);
if ($result_topics->num_rows > 0) {
    while ($row = $result_topics->fetch_assoc()) {
        $topics[] = $row['name'];
    }
}

// If a topic is selected, retrieve corresponding days and times
if (isset($_POST['topic']) && !empty($_POST['topic'])) {
    $selected_topic = $_POST['topic'];
    // Retrieve days for the selected topic from the database
    $sql_days = "SELECT DISTINCT day_of_week FROM sessions WHERE topic_id=(SELECT id FROM topics WHERE name='$selected_topic')";
    $result_days = $conn->query($sql_days);
    if ($result_days->num_rows > 0) {
        while ($row = $result_days->fetch_assoc()) {
            $days[] = $row['day_of_week'];
        }
    }
    // Retrieve times for the selected topic from the database
    $sql_times = "SELECT DISTINCT start_time FROM sessions WHERE topic_id=(SELECT id FROM topics WHERE name='$selected_topic')";
    $result_times = $conn->query($sql_times);
    if ($result_times->num_rows > 0) {
        while ($row = $result_times->fetch_assoc()) {
            $times[] = $row['start_time'];
        }
    }
}

// Display table of bookings
$sql_bookings = "SELECT t.name AS topic, s.day_of_week, s.start_time, b.student_name, b.student_email FROM bookings b JOIN sessions s ON b.session_id = s.id JOIN topics t ON s.topic_id = t.id";
$result_bookings = $conn->query($sql_bookings);

?>

<!DOCTYPE html>
<html>
<head>
    <title>IT Training Booking</title>
</head>
<body>
    <h2>Book IT Training Session</h2>
    <?php if(!empty($booking_message)): ?>
        <p><?php echo $booking_message; ?></p>
    <?php endif; ?>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>">
        <label for="topic">Select Topic:</label>
        <select id="topic" name="topic" onchange="this.form.submit()">
            <option value="">Select Topic</option>
            <?php
            foreach ($topics as $topic) {
                $selected = ($topic == $_POST['topic']) ? 'selected' : '';
                echo "<option value='$topic' $selected>$topic</option>";
            }
            ?>
        </select><br><br>
        <label for="day">Select Day:</label>
        <select id="day" name="day">
            <option value="">Select Day</option>
            <?php
            foreach ($days as $day) {
                echo "<option value='$day'>$day</option>";
            }
            ?>
        </select><br><br>
        <label for="time">Select Time:</label>
        <select id="time" name="time">
            <option value="">Select Time</option>
            <?php
            foreach ($times as $time) {
                echo "<option value='$time'>$time</option>";
            }
            ?>
        </select><br><br>
        <label for="name">Your Name:</label>
        <input type="text" id="name" name="name" pattern="[a-zA-Z][a-zA-Z' -]*[a-zA-Z]" title="Invalid name format." required><br><br>
        <label for="email">Your Email:</label>
        <input type="text" id="email" name="email" pattern="[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$" title="Invalid email address." required><br><br>
        <input type="submit" value="Submit">
    </form>

    <?php if ($result_bookings->num_rows > 0): ?>
        <h2>All Bookings</h2>
        <table>
            <tr>
                <th>Topic</th>
                <th>Day</th>
                <th>Time</th>
                <th>Name</th>
                <th>Email</th>
            </tr>
            <?php while ($row = $result_bookings->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $row['topic']; ?></td>
                    <td><?php echo $row['day_of_week']; ?></td>
                    <td><?php echo $row['start_time']; ?></td>
                    <td><?php echo $row['student_name']; ?></td>
                    <td><?php echo $row['student_email']; ?></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php endif; ?>

</body>
</html>

<?php
$conn->close();
?>
