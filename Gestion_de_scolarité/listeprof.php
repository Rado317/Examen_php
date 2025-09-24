<?php
include("header.php");
include("connect.php"); // Doit contenir $conn = new mysqli(...);

$result = $conn->query("SELECT * FROM prof");
?>

<h2>Liste des Enseignants</h2>
<table class="table table-bordered table-striped">
  <thead>
    <tr>
      <th>Nom</th>
      <th>Prénom</th>
      <th>adresse</th>
      <th>telephone</th>
    </tr>
  </thead>
  <tbody>
    <?php while ($row = $result->fetch_assoc()) : ?>
      <tr>
        <td><?= htmlspecialchars($row['nom']) ?></td>
        <td><?= htmlspecialchars($row['prenom']) ?></td>
        <td><?= htmlspecialchars($row['adresse']) ?></td>
        <td><?= htmlspecialchars($row['telephone']) ?></td>
      </tr>
    <?php endwhile; ?>
  </tbody>
</table>

<?php include("footer.php"); ?>
