<?php
require_once "db.php";
require_once "config.php";

$fields = ['N','P','K','temperature','humidity','ph','rainfall'];
$vals = [];

foreach($fields as $f) {
  if (!isset($_POST[$f]) || $_POST[$f]==='') {
    die("Missing $f");
  }
  $vals[$f] = floatval($_POST[$f]);
}

$python = "python3";
if (stripos(PHP_OS, "WIN") === 0) {
  $python = "python";
}

$script = realpath(__DIR__ . "/../ml/predict.py");
$parts = array_merge([$python, $script], array_values($vals));
$escaped = array_map('escapeshellarg', $parts);
$cmd = implode(' ', $escaped) . " 2>&1";

$output = shell_exec($cmd);
if ($output === null) {
  die("Prediction failed.");
}

$out = json_decode($output, true);
if (!$out || isset($out['error'])) {
  die("Model error: " . htmlspecialchars($output));
}

/* SAFE VARIABLE SETTING */
$crop = isset($out['crop']) ? $out['crop'] : "Unknown";
$conf = isset($out['confidence']) ? floatval($out['confidence']) : 0;

try {
  $pdo = getPDO();
  $stmt = $pdo->prepare(
    "INSERT INTO predictions 
    (N,P,K,temperature,humidity,ph,rainfall,predicted_crop,confidence_score) 
    VALUES (?,?,?,?,?,?,?,?,?)"
  );
  $stmt->execute([
    $vals['N'],$vals['P'],$vals['K'],
    $vals['temperature'],$vals['humidity'],
    $vals['ph'],$vals['rainfall'],
    $crop,$conf
  ]);
  $id = $pdo->lastInsertId();
} catch (Throwable $e) {
  $id = null;
  $db_err = $e->getMessage();
}
?>
<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Result</title>
<script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-green-50 to-emerald-100 min-h-screen">

<div class="max-w-3xl mx-auto py-12 px-4">

  <div class="bg-white rounded-2xl shadow-xl p-8 text-center">

    <h2 class="text-3xl font-bold text-green-700 mb-4">
      🎉 Crop Successfully Recommended!
    </h2>

    <p class="text-4xl font-bold text-gray-800 mb-4">
      <?php echo htmlspecialchars($crop); ?>
    </p>

    <?php $percent = round($conf * 100, 2); ?>

    <p class="text-lg font-semibold text-gray-700 mb-2">
      Confidence: <?php echo $percent; ?>%
    </p>

    <div class="w-full bg-gray-200 rounded-full h-4 mb-6">
      <div class="bg-green-600 h-4 rounded-full"
           style="width: <?php echo $percent; ?>%">
      </div>
    </div>

    <div class="text-left bg-gray-50 rounded-xl p-4 mb-6">
      <h4 class="font-semibold mb-2">Input Values:</h4>
      <ul class="list-disc ml-6">
        <?php foreach($vals as $k=>$v): ?>
          <li><?php echo htmlspecialchars($k); ?>: <?php echo htmlspecialchars($v); ?></li>
        <?php endforeach; ?>
      </ul>
    </div>

    <?php if (isset($id) && $id): ?>
      <p class="text-green-600 font-semibold">
        Saved Successfully (ID: <?php echo (int)$id; ?>)
      </p>
    <?php endif; ?>

    <?php if (isset($db_err)): ?>
      <p class="text-red-600">
        DB error: <?php echo htmlspecialchars($db_err); ?>
      </p>
    <?php endif; ?>

    <div class="mt-6 flex justify-center gap-4">
      <a href="index.php"
         class="bg-gray-600 text-white px-6 py-2 rounded-lg hover:bg-gray-700">
        🔄 Try Again
      </a>

      <a href="admin.php"
         class="border border-gray-400 px-6 py-2 rounded-lg hover:bg-gray-100">
        📊 View Analytics
      </a>
    </div>

  </div>

</div>

</body>
</html>
