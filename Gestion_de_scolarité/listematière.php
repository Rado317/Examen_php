<?php
include("header.php");
include("connect.php"); // Ce fichier doit définir $conn = new mysqli(...)

$result = $conn->query("SELECT * FROM matiere");
?>

<h2>Liste des Matières</h2>
<table class="table table-bordered table-striped">
  <thead>
    <tr>
      <th>Nom</th>
      <th>Coefficient</th>
    </tr>
  </thead>
  <tbody>
    <?php while ($row = $result->fetch_assoc()) : ?>
      <tr>
        <td><?= htmlspecialchars($row['nommat']) ?></td>
        <td><?= htmlspecialchars($row['codecl']) ?></td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

<?php include("footer.php"); ?>
