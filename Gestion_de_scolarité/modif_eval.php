<?php
session_start();
include('cadre.php');
echo '<div class="corp"><img src="" class="position_titre"><pre>';
if(isset($_GET['modif_eval'])){//modif_el qu'on a recup�rer de l'affichage (modifier)
$id=$_GET['modif_eval'];
$ligne=mysql_fetch_array(mysql_query("select * from evaluation,eleve,classe where eleve.numel=evaluation.numel and eleve.codecl=classe.codecl and numeval='$id'"));//Pour afficher le nom de l'eleve et la note par deflault et le devoir,
$codecl=$ligne['codecl'];
$eleve=mysql_query("select numel,nomel,prenomel from eleve where codecl='$codecl'");
$numdev=stripslashes($ligne['numdev']);
$mat_dev=mysql_fetch_array(mysql_query("select * from matiere,devoir where devoir.codemat=matiere.codemat and numdev='$numdev'"));//pour selection la classe par defualt et afficher la promotion
?>
<form action="modif_eval.php" method="POST" class="formulaire">
Mati�re            :      <?php echo $mat_dev['nommat']; ?><br/>
Classe                :     <?php echo stripslashes($ligne['nom']); ?><br/>
Promotion        :       <?php echo stripslashes($ligne['promotion']); ?><br/>
Date du devoir :      <?php echo stripslashes($mat_dev['date_dev']); ?><br/>
Coefficient         :      <?php echo stripslashes($mat_dev['coeficient']); ?><br/>
Semestre            :    S<?php echo $mat_dev['numsem']; ?><br/>
Devoir N�         :     <?php echo $mat_dev['n_devoir']; ?><br/>
Etudiant            :     <select name="numel"> 
<?php while($a=mysql_fetch_array($eleve)){
echo '<option value="'.$a['numel'].'" '.choixpardefault2($a['numel'],$ligne['numel']).'>'.$a['nomel'].' '.$a['prenomel'].'</option>';
}?></select><br/>
Note                 :      <input type="text" name="note" value="<?php echo $ligne['note']; ?>">
<input type="hidden" name="id" value="<?php echo $id; ?>"><!-- pour revenir en arriere et pour avoir l'id dont lequel on va modifier-->
<center><input type="image" src="modifier.png" style="margin-top:13px;"></center>
</form>
<?php
echo '<br/><br/><a href="afficher_evaluation.php">Revenir � la page pr�c�dente !</a>';
}
if(isset($_POST['numel'])){//s'il a cliquer sur le bouton modifier
	if($_POST['note']!=""){
		$id=$_POST['id'];
		$numel=$_POST['numel'];
		$note=str_replace(",",".",$_POST['note']);//remplacer la , par .
		mysql_query("update evaluation set numel='$numel', note='$note' where numeval='$id'");
		?> <SCRIPT LANGUAGE="Javascript">	alert("Modifi� avec succ�s!"); </SCRIPT> <?php
	}
	else{
		?> <SCRIPT LANGUAGE="Javascript">	alert("erreur! Vous devez remplire tous les champss"); </SCRIPT> <?php
		}
	echo '<br/><br/><a href="modif_eval.php?modif_eval='.$id.'">Revenir � la page precedente !</a>';
}
if(isset($_GET['supp_eval'])){
$id=$_GET['supp_eval'];
mysql_query("delete from evaluation where numeval='$id'");
?> <SCRIPT LANGUAGE="Javascript">	alert("Supprim� avec succ�s!"); </SCRIPT> <?php
echo '<br/><br/><a href="afficher_evaluation.php">Revenir � la page � l\'affichage</a>'; //on revient � la page princippale car on n'a plus l'id dont on affiche la matiere dans la modification
}
?>
</pre>
</div>