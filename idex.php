<?php
// --- DATABASE & LOGIC ---
$servername = "localhost";
$username = "root";
$password = ""; 
$dbname = "hostel_system";

$conn = new mysqli($servername, $username, $password);
$conn->query("CREATE DATABASE IF NOT EXISTS $dbname");
$conn->select_db($dbname);

// Create tables for both Hostels and Student Registrations
$conn->query("CREATE TABLE IF NOT EXISTS hostels (id INT AUTO_INCREMENT PRIMARY KEY, name VARCHAR(100), rent VARCHAR(100), location TEXT, empty_rooms INT, contact VARCHAR(20))");
$conn->query("CREATE TABLE IF NOT EXISTS students (id INT AUTO_INCREMENT PRIMARY KEY, s_name VARCHAR(100), email VARCHAR(100), phone VARCHAR(20), gender VARCHAR(10))");

// Auto-populate hostel data from your notes if empty
$check = $conn->query("SELECT id FROM hostels LIMIT 1");
if ($check->num_rows == 0) {
    $conn->query("INSERT INTO hostels (name, rent, location, empty_rooms, contact) VALUES 
    ('Chakraborty Mess', '3000-3500', 'Nearby Canning Govt. Polytechnic', 5, '7063322414'),
    ('Lokenath Mess', '4000', 'Narayanpur', 10, '7602874117'),
    ('Tara Maa Mess', '3500', 'Nearby Gaordha Rail Station', 3, '9735683985'),
    ('Mondal Complex', '5000', 'Nearby Gaordha Rail Station', 8, '9907402359')");
}

// Handle Student Registration Form Submission
$message = "";
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['register'])) {
    $stmt = $conn->prepare("INSERT INTO students (s_name, email, phone, gender) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $_POST['name'], $_POST['email'], $_POST['phone'], $_POST['gender']);
    if ($stmt->execute()) { $message = "<p style='color:green; text-align:center;'>Registration Successful!</p>"; }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Hostel Portal</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; font-family: sans-serif; }
        body { background: #f4f4f9; }
        header { background: #002147; color: white; text-align: center; padding: 40px 20px; }
        .section { max-width: 800px; margin: 20px auto; background: white; padding: 25px; border-radius: 10px; box-shadow: 0 4px 10px rgba(0,0,0,0.1); }
        
        /* Form Styling */
        h2 { color: #002147; margin-bottom: 20px; border-bottom: 2px solid #eee; padding-bottom: 10px; }
        .input-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input, select { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 5px; }
        .btn { width: 100%; padding: 12px; background: #002147; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold; }
        
        /* Table Styling */
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th { background: #f8f9fa; text-align: left; padding: 12px; border-bottom: 2px solid #002147; }
        td { padding: 12px; border-bottom: 1px solid #eee; }
        .search-bar { width: 100%; padding: 10px; margin-bottom: 15px; border: 2px solid #002147; border-radius: 5px; }
    </style>
</head>
<body>

<header>
    <h1>Welcome to College Hostel</h1>
    <p>Safe, Comfortable, and Convenient Accommodation Near Campus</p>
</header>

<div class="section">
    <h2>Student Registration</h2>
    <?php echo $message; ?>
    <form method="POST">
        <div class="input-group">
            <label>Student Name:</label>
            <input type="text" name="name" placeholder="Enter full name" required>
        </div>
        <div class="input-group">
            <label>Student e-mail id:</label>
            <input type="email" name="email" placeholder="Enter your e-mail id" required>
        </div>
        <div class="input-group">
            <label>Phone number:</label>
            <input type="tel" name="phone" placeholder="1234567890" required>
        </div>
        <div class="input-group">
            <label>Gender:</label>
            <select name="gender" required>
                <option value="">--Select Gender--</option>
                <option value="Male">Male</option>
                <option value="Female">Female</option>
            </select>
        </div>
        <button type="submit" name="register" class="btn">Submit Registration</button>
    </form>
</div>

<div class="section">
    <h2>Available Accommodation</h2>
    <input type="text" id="hostelSearch" class="search-bar" placeholder="Search by name or location..." onkeyup="filterHostels()">
    <table id="hostelTable">
        <thead>
            <tr>
                <th>Mess Name</th>
                <th>Rent (₹)</th>
                <th>Location</th>
                <th>Rooms Left</th>
            </tr>
        </thead>
        <tbody>
            <?php 
            $res = $conn->query("SELECT * FROM hostels");
            while($row = $res->fetch_assoc()): 
            ?>
            <tr>
                <td><strong><?php echo $row['name']; ?></strong></td>
                <td><?php echo $row['rent']; ?></td>
                <td><?php echo $row['location']; ?></td>
                <td><?php echo $row['empty_rooms']; ?></td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

<script>
// JavaScript for live filtering of the table
function filterHostels() {
    let input = document.getElementById("hostelSearch").value.toLowerCase();
    let rows = document.querySelectorAll("#hostelTable tbody tr");
    rows.forEach(row => {
        let text = row.innerText.toLowerCase();
        row.style.display = text.includes(input) ? "" : "none";
    });
}
</script>

</body>
</html>
