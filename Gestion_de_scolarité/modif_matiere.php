<?php
session_start();
include('cadre.php');

mysql_connect("localhost", "root", "");
mysql_select_db("gestion");
echo '<div class="corp">';
if(isset($_GET['modif_matiere'])){//modif_el qu'on a recupérer de l'affichage (modifier)
$id=$_GET['modif_matiere'];
$ligne=mysql_fetch_array(mysql_query("select * from matiere,classe where classe.codecl=matiere.codecl and codemat='$id'"));//classe pour afficher la promotion
$nom=stripslashes($ligne['nommat']);
$codecl=stripslashes($ligne['codecl']);
$promo=mysql_fetch_array(mysql_query("select promotion,nom from classe where codecl='$codecl'"));//pour selection la classe par defualt et afficher la promotion
?>
<center><h1>Modifier une matiére</h1></center>
<form action="modif_matiere.php" method="POST" class="formulaire">
Matiére :<input type="text" name="nommat" value="<?php echo $nom; ?>"><br/><br/>
Classe : <?php echo $promo['nom']; ?><br/><br/>
Promotion : <?php echo $promo['promotion']; ?><br/><br/>
<input type="hidden" name="id" value="<?php echo $id; ?>"><!-- pour revenir en arriere et pour avoir l'id dont lequel on va modifier-->
<center><input type="image" src="button.png"></center>
</form>
<?php
echo '<br/><br/><a href="affiche_matiere.php?nomcl='.$promo['nom'].'">Revenir à la page précédente !</a>';
}
if(isset($_POST['nommat'])){//s'il a cliquer sur le bouton modifier
	if($_POST['nommat']!=""){
		$id=$_POST['id'];
		$nom=addslashes(Htmlspecialchars($_POST['nommat']));
		mysql_query("update matiere set nommat='$nom' where codemat='$id'");
		?> <SCRIPT LANGUAGE="Javascript">	alert("Modifié avec succés!"); </SCRIPT> <?php
	}
	else{
		?> <SCRIPT LANGUAGE="Javascript">	alert("erreur! Vous devez remplire tous les champss"); </SCRIPT> <?php
		}
	echo '<br/><br/><a href="modif_matiere.php?modif_matiere='.$id.'">Revenir à la page precedente !</a>';
}
if(isset($_GET['supp_matiere'])){
$id=$_GET['supp_matiere'];
mysql_query("delete from matiere where codemat='$id'");
?> <SCRIPT LANGUAGE="Javascript">	alert("Supprimé avec succés!"); </SCRIPT> <?php
echo '<br/><br/><a href="index.php">Revenir à la page  principale!</a>'; //on revient à la page princippale car on n'a plus l'id dont on affiche la matiere dans la modification
}
?>
</div>