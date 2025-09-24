<?php
session_start();
include('cadre.php');
include('calendrier.html');
mysql_connect("localhost", "root", "");
mysql_select_db("gestion");
if(isset($_GET['modif_el'])){//modif_el qu'on a recup�rer de l'affichage (modifier)
$id=$_GET['modif_el'];
$ligne=mysql_fetch_array(mysql_query("select * from eleve,classe where eleve.codecl=classe.codecl and numel='$id'"));
$nom=stripslashes($ligne['nomel']);
$prenom=stripslashes($ligne['prenomel']);
$date=stripslashes($ligne['date_naissance']);
$phone=stripslashes($ligne['telephone']);
$adresse=str_replace("<br />",' ',$ligne['adresse']);
$codecl=stripslashes($ligne['codecl']);
?>
<div class="corp">
<img src="" class="position_titre">
<center><pre>
<form action="modif_eleve.php" method="POST" class="formulaire">
   <FIELDSET>
 <LEGEND align=top>Modifier un �tudiant<LEGEND>  <pre>
Nom �tudiant        :           <?php echo $nom; ?><br/>
Pr�nom                   :          <?php echo $prenom; ?><br/>
Date de naissanc     :               <input type="text" name="date" class="calendrier" value="<?php echo $date; ?>"><br/>
Adresse                   :        <textarea name="adresse" ><?php echo $adresse; ?></textarea><br/>
Telephone                :          <input type="text" name="phone" value="<?php echo $phone; ?>"><br/>
Classe                      :              <?php echo $ligne['nom']; ?><br/>
Promotion               :             <?php echo $ligne['promotion']; ?>
<input type="hidden" name="id" value="<?php echo $id; ?>"><br/>
<input type="image" src="button.png">
</pre></fieldset>
</form><a href="listeEtudiant.php?nomcl=<?php echo $ligne['nom']; ?>">Revenir � la page pr�c�dente !</a>
</div>
<?php
}
if(isset($_POST['adresse'])){
	if($_POST['date']!="" and $_POST['adresse']!="" and $_POST['phone']!=""){
		$id=$_POST['id'];
		$date=addslashes(Htmlspecialchars($_POST['date']));
		$phone=addslashes(Htmlspecialchars($_POST['phone']));
		$adresse=addslashes(Nl2br(Htmlspecialchars($_POST['adresse'])));
		mysql_query("update eleve set date_naissance='$date', adresse='$adresse', telephone='$phone' where numel='$id'");
		?> <SCRIPT LANGUAGE="Javascript">	alert("Modifi� avec succ�s!"); </SCRIPT> 
		<?php
		
	}
	else{
	?> <SCRIPT LANGUAGE="Javascript">	alert("erreur! Vous devez remplire tous les champss"); </SCRIPT> <?php
	}
	echo '<div class="corp"><br/><br/><a href="modif_eleve.php?modif_el='.$id.'">Revenir � la page precedente !</a></div>';
}
if(isset($_GET['supp_el'])){
$id=$_GET['supp_el'];
mysql_query("delete from eleve where numel='$id'");
mysql_query("delete from evaluation where numel='$id'");/*	Supprimier tous les entres en relation		*/
mysql_query("delete from stage where numel='$id'");
mysql_query("delete from bulletin where numel='$id'");
?> <SCRIPT LANGUAGE="Javascript">	alert("Supprim� avec succ�s!"); </SCRIPT> <?php
echo '<br/><br/><a href="index.php?">Revenir � la page principale !</a>';
}
?>
</center></pre>

</body>
</html>