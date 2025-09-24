<?php
session_start();
include('cadre.php');
mysql_connect("localhost", "root", "");
mysql_select_db("gestion");
echo '<div class="corp"><img src="titre_img/modif_prof.png" class="position_titre"><pre>';
if(isset($_GET['modif_prof'])){//modif_el qu'on a recupérer de l'affichage (modifier)
$id=$_GET['modif_prof'];
$ligne=mysql_fetch_array(mysql_query("select * from prof where numprof='$id'"));
$nom=stripslashes($ligne['nom']);
$prenom=stripslashes($ligne['prenom']);
$phone=stripslashes($ligne['telephone']);
$adresse=stripslashes($ligne['adresse']);
?>

<form action="modif_prof.php" method="POST" class="formulaire">
Nom étudiant       :       <?php echo $nom; ?><br/><br/>
Prénom                  :     <?php echo $prenom; ?><br/><br/>
Adresse                :       <textarea name="adresse" ><?php echo $adresse; ?></textarea><br/><br/>
Telephone             :         <input type="text" name="phone" value="<?php echo $phone; ?>"><br/><br/>
<input type="hidden" name="id" value="<?php echo $id; ?>">
<center><input type="image" src="button.png"></center>
</form>
<br/><br/><a href="afficher_prof.php?nomcl=<?php echo $ligne['nom']; ?>">Revenir à la page précédente !</a>
<?php
}
if(isset($_POST['nom'])){
	if($_POST['adresse']!="" and $_POST['phone']!=""){
		$id=$_POST['id'];
		$phone=addslashes(Htmlspecialchars($_POST['phone']));
		$adresse=addslashes(Nl2br(Htmlspecialchars($_POST['adresse'])));
		mysql_query("update prof set adresse='$adresse', telephone='$phone' where numprof='$id'");
		?> <SCRIPT LANGUAGE="Javascript">	alert("Modifié avec succés!"); </SCRIPT> <?php
		echo '<br/><br/><a href="modif_prof.php?modif_prof='.$id.'">Revenir à la page precedente !</a>';
	}
	else{
	?> <SCRIPT LANGUAGE="Javascript">	alert("erreur! Vous devez remplire tous les champs"); </SCRIPT> <?php
		echo '<br/><br/><a href="index.php?">Revenir à la page principale !</a>';
	}
}
if(isset($_GET['supp_prof'])){
$id=$_GET['supp_prof'];
mysql_query("delete from prof where numprof='$id'");
?> <SCRIPT LANGUAGE="Javascript">	alert("Supprimé avec succés!"); </SCRIPT> <?php
echo '<br/><br/><a href="index.php?">Revenir à la page principale !</a>';
}
?>
</pre>
</div>