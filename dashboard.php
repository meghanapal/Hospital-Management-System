<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard | SwiftHealth</title>
  <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Chicle&family=Sour+Gummy:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  <link rel="icon" type="image/png" href="./assets/logo.png">
</head>

<body>

  <?php
  session_start();
  // check if the user is not logged in
  require __DIR__ . "/../backend/includes/is_loggedin.php";

  // connect to DB and fetch user details.
  $userEmail = $_SESSION['user']['email'];
  require __DIR__ . "/../backend/database/connectDB.php";
  $stmt = $conn->prepare("SELECT * FROM users WHERE email = ?");
  if (!$stmt) {
    echo json_encode([
      "success" => false,
      "message" => "DB Error: " . $conn->error
    ]);
    exit();
  }
  $stmt->bind_param("s", $userEmail);
  if (!$stmt->execute()) {
    echo json_encode([
      "success" => false,
      "message" => "Error fetching details: " . $stmt->error
    ]);
    exit();
  }
  $result = $stmt->get_result()->fetch_assoc();
  $stmt->close();

  if (!isset($result['name']) || !isset($result['role'])) {
    echo "<p>Unable to fetch user details.</p>";
    die();
  }
  $user_id = $result['id'];
  $user_name = $result['name'];
  $user_role = $result['role'];

  // saving the id in session for future use.
  $_SESSION['user']['id'] = $user_id;
  $_SESSION['user']['role'] = $user_role;
  $_SESSION['user']['name'] = $user_name;
  ?>

  <!-- Top Bar -->
  <header class="text-black">
    <div class="container mx-auto flex justify-between items-center py-8 px-6">
      <div class="flex items-center">
        <span class="lg:text-4xl text-2xl font-bold font-[Chicle]">SwiftHealth</span>
      </div>
      <!-- <nav>
        <ul class="flex space-x-4 hidden lg:flex">
          <li><a href="dashboard.php" class="hover:underline">Dashboard</a></li>
          <li><a href="bookappointment.php" class="hover:underline">Appointments</a></li>
          <li><a href="profile.html" class="hover:underline">Profile</a></li>
        </ul>
      </nav> -->
      <div class="flex items-center text-sm lg:text-[16px]">
        <i class="fa-solid fa-user"></i><a href="userProfile.php" id="user_name" class="cursor-pointer mr-4 ml-2"><?= $user_name ?></a>
        <button id="logout-btn" class="bg-transparent text-white-400 px-3 py-1 rounded cursor-pointer border-2 border-red-700 text-red-700">Logout</button>
      </div>
    </div>
  </header>

  <!-- Main Content Area -->
  <main class="container mx-auto px-6 py-8">
    <!-- Upcoming Appointments Section -->
    <?php
    if ($user_role == 'patient') {
      require __DIR__ . "/../backend/views/patient_dashboard.php";
    } else {
      require __DIR__ . "/../backend/views/doctor_dashboard.php";
    }
    ?>
  </main>



  <script src="./js/dashboard.js"></script>
  <script src="./js/appointmentActions.js"></script>

</body>

</html>