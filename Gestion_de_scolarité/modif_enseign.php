<?php
session_start();
include('cadre.php');
include('calendrier.html');
echo '<div class="corp"><img src="" class="position_titre"><pre>';
if(isset($_GET['modif_ensein'])){//modif_el qu'on a recup�rer de l'affichage (modifier)
$id=$_GET['modif_ensein'];
$ligne=mysql_fetch_array(mysql_query("select classe.codecl,prof.numprof,promotion,classe.nom as nomcl,prenom,prof.nom as nomp,matiere.codemat,nommat,numsem from classe,matiere,enseignement,prof where classe.codecl=enseignement.codecl and matiere.codemat=enseignement.codemat and prof.numprof=enseignement.numprof and id='$id'"));
$prof=mysql_query("select * from prof");
$mat=mysql_query("select * from matiere");
?>
<form action="modif_enseign.php" method="POST" class="formulaire">
Mati�re     :     <select name="codemat"> 
<?php while($a=mysql_fetch_array($mat)){
echo '<option value="'.$a['codemat'].'" '.choixpardefault2($a['codemat'],$ligne['codemat']).'>'.$a['nommat'].'</option>';
}?></select><br/><br/>
Professeur :    <select name="numprof"> 
<?php while($a=mysql_fetch_array($prof)){
echo '<option value="'.$a['numprof'].'" '.choixpardefault2($a['numprof'],$ligne['numprof']).'>'.$a['nom'].' '.$a['prenom'].'</option>';
}?></select><br/><br/>
Classe        : <?php echo stripslashes($ligne['nomcl']); ?><br/><br/>
Promotion    :  <?php echo $ligne['promotion']; ?><br/><br/>
Semestre      : <?php echo $ligne['numsem']; ?>
<input type="hidden" name="id" value="<?php echo $id; ?>">
<input type="hidden" name="codecl" value="<?php echo $ligne['codecl']; ?>">
<input type="hidden" name="numsem" value="<?php echo $ligne['numsem']; ?>">
<input type="image" src="modifier.png">
</form>
<?php
echo '<br/><br/><a href="afficher_devoir.php">Revenir � la page pr�c�dente !</a>';
}
if(isset($_POST['numprof'])){//s'il a cliquer sur le bouton modifier
	$id=$_POST['id'];
		$numprof=$_POST['numprof'];
		$codemat=$_POST['codemat'];
		$codecl=$_POST['codecl'];
		$numsem=$_POST['numsem'];
		$compte=mysql_fetch_array(mysql_query("select count(*) as nb from enseignement where numprof='$numprof' and codemat='$codemat' and codecl='$codecl'"));
		if($compte['nb']!=0){//deux devoir similaire()2 devoirs par matiere
		?> <SCRIPT LANGUAGE="Javascript">	alert("erreur de modification,cet enseignement existe d�ja"); </SCRIPT> <?php
		}
		else{
		mysql_query("update enseignement set numprof='$numprof',codemat='$codemat' where id='$id'");
		$suppression=mysql_query("select * from devoir where codemat='$codemat' and codecl='$codecl' and numsem='$numsem'");//tres important()supprimier les devoir correspondants
		/*			Supprimer le devoir et l'evaluation correspondnt			*/
		while($a=mysql_fetch_array($suppression)){
			$cle=$a['numdev'];
			mysql_query("delete from evaluation where numdev='$cle'");
			mysql_query("delete from devoir where numdev='$cle'");
		}
		?> <SCRIPT LANGUAGE="Javascript">	alert("Modifi� avec succ�s!\ntous les entr�s reli�es � cet enregistrement en �t� supprimer"); </SCRIPT> <?php
		}
		
	echo '<br/><br/><a href="modif_enseign.php?modif_ensein='.$id.'">Revenir � la page precedente !</a>';
}
if(isset($_GET['supp_ensein'])){
$id=$_GET['supp_ensein'];
/* 		requete pour utiliser son retour afin de recuperer le numdev qu'on va supprimer aussi 		*/
$ligne=mysql_fetch_array(mysql_query("select classe.codecl,matiere.codemat,numsem from classe,matiere,enseignement where classe.codecl=enseignement.codecl and matiere.codemat=enseignement.codemat and id='$id'"));
$codemat=$ligne['codemat'];
$codecl=$ligne['codecl'];
$numsem=$ligne['numsem'];
$suppression=mysql_query("select * from devoir where codemat='$codemat' and codecl='$codecl' and numsem='$numsem'");//tres important()supprimier les devoir correspondants
		/*			Supprimer le devoir et l'evaluation correspondnte			*/
		while($a=mysql_fetch_array($suppression)){
			$cle=$a['numdev'];
			mysql_query("delete from evaluation where numdev='$cle'");
			mysql_query("delete from devoir where numdev='$cle'");
		}
mysql_query("delete from enseignement where id='$id'");
?> <SCRIPT LANGUAGE="Javascript">	alert("Supprim� avec succ�s!\ntous les entr�s reli�es � cet enregistrement en �t� supprimer"); </SCRIPT> <?php
echo '<br/><br/><a href="index.php">Revenir � la page � principale</a>'; //on revient � la page princippale car on n'a plus l'id dont on affiche la matiere dans la modification
}
?>
</pre>
</div>