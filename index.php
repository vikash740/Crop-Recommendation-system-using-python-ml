<?php require_once "config.php"; ?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Crop Recommender</title>

  <!-- Tailwind CSS -->
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gradient-to-br from-green-50 to-emerald-100 min-h-screen">

<div class="max-w-5xl mx-auto py-10 px-4">

  <!-- Header -->
  <div class="text-center mb-8">
    <h1 class="text-4xl font-bold text-green-700">🌱 Crop Recommendation System</h1>
    <p class="text-gray-600 mt-2">AI Powered Smart Farming Decision System</p>
  </div>

  <!-- Form Card -->
  <div class="bg-white rounded-2xl shadow-xl p-8">

    <form method="post" action="save_and_predict.php" id="cropForm">
      <div class="grid md:grid-cols-3 gap-6">

        <div>
          <label class="block font-semibold mb-1">Nitrogen (N)</label>
          <input name="N" step="0.01" required type="number"
            class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-green-400"
            placeholder="Enter Nitrogen value">
        </div>

        <div>
          <label class="block font-semibold mb-1">Phosphorus (P)</label>
          <input name="P" step="0.01" required type="number"
            class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-green-400"
            placeholder="Enter Phosphorus value">
        </div>

        <div>
          <label class="block font-semibold mb-1">Potassium (K)</label>
          <input name="K" step="0.01" required type="number"
            class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-green-400"
            placeholder="Enter Potassium value">
        </div>

        <div>
          <label class="block font-semibold mb-1">Temperature (°C)</label>
          <input name="temperature" step="0.1" required type="number"
            class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-green-400"
            placeholder="e.g. 25">
        </div>

        <div>
          <label class="block font-semibold mb-1">Humidity (%)</label>
          <input name="humidity" step="0.1" required type="number"
            class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-green-400"
            placeholder="e.g. 60">
        </div>

        <div>
          <label class="block font-semibold mb-1">Soil pH</label>
          <input name="ph" step="0.01" required type="number"
            class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-green-400"
            placeholder="e.g. 6.5">
        </div>

        <div class="md:col-span-3">
          <label class="block font-semibold mb-1">Rainfall (mm)</label>
          <input name="rainfall" step="0.1" required type="number"
            class="w-full border rounded-lg p-3 focus:ring-2 focus:ring-green-400"
            placeholder="e.g. 200">
        </div>

      </div>

      <!-- Buttons -->
      <div class="mt-8 flex flex-wrap gap-4">

        <!-- Predict Button with Spinner -->
        <button id="predictBtn" type="submit"
          class="bg-green-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-700 transition flex items-center gap-2">

          <span id="btnText">🌾 Recommend Crop</span>

          <!-- Spinner -->
          <svg id="spinner" class="hidden animate-spin h-5 w-5 text-white"
               xmlns="http://www.w3.org/2000/svg" fill="none"
               viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10"
              stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor"
              d="M4 12a8 8 0 018-8v8H4z"></path>
          </svg>
        </button>

        <a href="admin.php"
          class="border border-gray-400 px-6 py-3 rounded-lg hover:bg-gray-100 transition">
          ⚙️ Admin Panel
        </a>

      </div>

    </form>
  </div>

</div>

<!-- Loading Script -->
<script>
document.getElementById("cropForm").addEventListener("submit", function() {
  const btn = document.getElementById("predictBtn");
  const text = document.getElementById("btnText");
  const spinner = document.getElementById("spinner");

  btn.disabled = true;
  text.innerText = "Processing...";
  spinner.classList.remove("hidden");
});
</script>

</body>
</html>
