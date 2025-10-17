<?php
// index.php
?><!doctype html>
<html lang="fa">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Library Admin - Kabul University</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <header>
    <div class="brand">Kabul University Library - Admin</div>
    <nav id="topnav">
      <!-- menu will be populated by JS based on table list -->
    </nav>
  </header>

  <main>
    <section class="control">
      <div class="left">
        <select id="tableSelector"></select>
        <button id="btnRefresh">Refresh</button>
        <button id="btnAdd">Add New</button>
        <input type="text" id="globalSearch" placeholder="Search in table...">
      </div>
      <div class="right" id="feedback"></div>
    </section>

    <section id="tableArea">
      <table id="dataTable">
        <thead id="tableHead"></thead>
        <tbody id="tableBody"></tbody>
      </table>
    </section>

    <div id="modal" class="modal hidden">
      <div class="modal-content">
        <h3 id="modalTitle">Form</h3>
        <form id="entityForm"></form>
        <div class="modal-actions">
          <button id="saveBtn" type="button">Save</button>
          <button id="cancelBtn" type="button">Cancel</button>
        </div>
      </div>
    </div>
  </main>

  <footer>
    <p>&copy; 2025 Kabul University Library</p>
  </footer>

  <script src="script.js" defer></script>
</body>
</html>
