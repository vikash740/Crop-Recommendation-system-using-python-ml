<?php
require_once "config.php";
require_once "db.php";
session_start();

$error = "";

/* LOGIN */
if ($_SERVER["REQUEST_METHOD"] == "POST" && !isset($_SESSION["admin"])) {
    if ($_POST["username"] === "admin" && $_POST["password"] === "admin123") {
        $_SESSION["admin"] = true;
    } else {
        $error = "Invalid Username or Password!";
    }
}

/* LOGOUT */
if (isset($_GET["logout"])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}

$pdo = getPDO();

/* DELETE */
if (isset($_GET["delete"]) && isset($_SESSION["admin"])) {
    $stmt = $pdo->prepare("DELETE FROM predictions WHERE id=?");
    $stmt->execute([intval($_GET["delete"])]);
    header("Location: admin.php");
    exit;
}

/* FILTER */
$filter = $_GET["crop"] ?? "";
$where = "";
$params = [];

if ($filter !== "") {
    $where = "WHERE predicted_crop = ?";
    $params[] = $filter;
}

/* FETCH DATA */
if (isset($_SESSION["admin"])) {

    $total = $pdo->query("SELECT COUNT(*) FROM predictions")->fetchColumn();
    $unique = $pdo->query("SELECT COUNT(DISTINCT predicted_crop) FROM predictions")->fetchColumn();

    // Average Confidence (converted to %)
    $avgConfidence = $pdo->query("SELECT ROUND(AVG(confidence_score)*100,2) FROM predictions")
                         ->fetchColumn();

    $stmt = $pdo->prepare("SELECT * FROM predictions $where ORDER BY id DESC");
    $stmt->execute($params);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    /* Chart Data */
    $chartData = $pdo->query("SELECT predicted_crop, COUNT(*) as count 
                              FROM predictions GROUP BY predicted_crop")
                     ->fetchAll(PDO::FETCH_ASSOC);
}
?>
<!doctype html>
<html>
<head>
<title>Admin Panel</title>
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-gradient-to-br from-green-100 to-emerald-200 min-h-screen">

<?php if (!isset($_SESSION["admin"])): ?>

<!-- LOGIN UI -->
<div class="flex items-center justify-center min-h-screen">
  <div class="bg-white p-8 rounded-xl shadow-xl w-96">
    <h2 class="text-2xl font-bold mb-4 text-center">Admin Login</h2>
    <?php if($error): ?>
      <div class="bg-red-100 text-red-600 p-2 rounded mb-3 text-center"><?php echo $error; ?></div>
    <?php endif; ?>
    <form method="POST">
      <input name="username" class="w-full border p-2 mb-3 rounded" placeholder="Username">
      <input name="password" type="password" class="w-full border p-2 mb-3 rounded" placeholder="Password">
      <button class="w-full bg-green-600 text-white p-2 rounded">Login</button>
    </form>
  </div>
</div>

<?php else: ?>

<div class="max-w-7xl mx-auto p-6">

<!-- PROFESSIONAL HEADER -->
<div class="flex justify-between items-center mb-10">

    <div>
        <h1 class="text-4xl font-extrabold tracking-tight">
            <span class="text-gray-800">Crop Recommendation</span>
            <span class="text-green-600">Analytics</span>
        </h1>
        <p class="text-gray-500 text-sm mt-1">
            AI Model Monitoring & Prediction Insights Dashboard
        </p>
    </div>

    <a href="?logout=true"
       class="bg-red-500 hover:bg-red-600 text-white px-5 py-2.5 
              rounded-lg shadow-md transition duration-200 font-medium">
        Logout
    </a>

</div>



<!-- STATS -->
<div class="grid md:grid-cols-3 gap-6 mb-6">

<div class="bg-white p-6 rounded-xl shadow text-center">
<h3>Total Predictions</h3>
<p class="text-3xl font-bold text-green-700"><?php echo $total; ?></p>
</div>

<div class="bg-white p-6 rounded-xl shadow text-center">
<h3>Unique Crops</h3>
<p class="text-3xl font-bold text-green-700"><?php echo $unique; ?></p>
</div>

<div class="bg-white p-6 rounded-xl shadow text-center">
<h3>Average Confidence</h3>
<p class="text-3xl font-bold text-green-700">
<?php echo $avgConfidence ?? 0; ?>%
</p>
</div>

</div>

<div class="grid md:grid-cols-2 gap-6">

<!-- TABLE -->
<div class="bg-white p-6 rounded-xl shadow">
<h3 class="font-bold mb-4">Recent Predictions</h3>

<form method="GET" class="mb-4">
<input name="crop" value="<?php echo htmlspecialchars($filter); ?>"
       placeholder="Filter by crop"
       class="border p-2 rounded w-2/3">
<button class="bg-blue-500 text-white px-4 py-2 rounded">Search</button>
</form>

<table class="w-full text-sm">
<thead>
<tr class="border-b">
<th>ID</th>
<th>Crop</th>
<th>Confidence</th>
<th>NPK</th>
<th>Action</th>
</tr>
</thead>
<tbody>
<?php foreach($rows as $r): ?>
<tr class="border-b">
<td><?php echo $r['id']; ?></td>
<td><?php echo $r['predicted_crop']; ?></td>
<td><?php echo round($r['confidence_score']*100,2); ?>%</td>
<td>
N:<?php echo $r['N']; ?> 
P:<?php echo $r['P']; ?> 
K:<?php echo $r['K']; ?>
</td>
<td>
<a href="?delete=<?php echo $r['id']; ?>"
   onclick="return confirm('Delete this record?')"
   class="bg-slate-700 text-white px-3 py-1 rounded text-xs hover:bg-slate-800 transition">
Delete
</a>
</td>
</tr>
<?php endforeach; ?>
</tbody>
</table>
</div>

<!-- CHART -->
<div class="bg-white p-6 rounded-xl shadow">
<h3 class="font-bold mb-4">Crop Distribution</h3>
<canvas id="cropChart"></canvas>
</div>

</div>

</div>

<script>
const ctx = document.getElementById('cropChart');

new Chart(ctx, {
    type: 'pie',
    data: {
        labels: <?php echo json_encode(array_column($chartData, 'predicted_crop')); ?>,
        datasets: [{
            data: <?php echo json_encode(array_column($chartData, 'count')); ?>,
        }]
    }
});
</script>

<?php endif; ?>

</body>
</html>
