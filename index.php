<!DOCTYPE html>
<html lang="fr" > 
<head> 
	<title>ViewPics v0.1</title> 
  	<meta http-equiv="content-type" content="text/html; charset=UTF-8" /> 
<!--Les styles avec le bootstrap Twitter-->
	<script src="js/jquery-1.7.2.min.js"></script>
	<script src="js/lightbox.js"></script>
	<link href="css/lightbox.css" rel="stylesheet" />
	<link href="css/bootstrap.css" rel="stylesheet" />
	<style type="text/css">
	      body {
	        padding-top: 60px;
	        padding-bottom: 40px;
	      }
	      .sidebar-nav {
	        padding: 9px 0;
	      }
	    </style>
	    <link href="css/bootstrap-responsive.css" rel="stylesheet">
	
	    <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
	    <!--[if lt IE 9]>
	      <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
	    <![endif]-->
	

</head> 


<body>
<?php

 
function parcours_rep($rep, $ssrep, $racine, $i)	//	fonction récursive	("racine" est là pour retrancher cette racine au chemin complet afin de remplir le tableau)
{
	if (is_dir($rep))	// Ouvre le dossier "racine" ("/photos_web/galeries/" par exemple)  et lit tous les répertoires
	{
		if( $dir = opendir($rep) )	// Ouvre le dossier "racine"  et lit tous les répertoires
		{
			while( ($fichier = readdir($dir)) !== false )
			{
				if ($fichier != "." && $fichier != ".." && $fichier != "miniatures")
				{
					$chemin = $rep.$fichier;
					if (is_dir($chemin))	// Est-ce que $chemin est un répertoire ?
					{
						global $liste_rep;
						global $i;
						$liste_rep[$i] = substr($chemin, strlen($racine));	// PLace les fichiers dans un tableau	($liste_rep[] = à la fin)
						$i++;
						parcours_rep($chemin.'/', ($ssrep==''?$fichier:$ssrep.'/'.$fichier) , $racine , $i);	//	fonction récursive
					}
				}
			}
		}
	} else
	{
		echo "le r&eacute;pertoire \"$rep\" n'existe pas ...";
	}
    closeDir($dir);
	return $liste_rep;
} 

function imagethumb ($img_src, $img_dest, $max_size = 150 ){
	$source = imagecreatefromjpeg($img_src); // La photo est la source

	// Les fonctions imagesx et imagesy renvoient la largeur et la hauteur d'une image
	$largeur_source = imagesx($source);
	$hauteur_source = imagesy($source);
	
	if ($largeur_source > $hauteur_source) { 
   // on crée une ressource pour notre miniature
   	$largeur_destination = round(($max_size/$hauteur_source)*$largeur_source);
   	$hauteur_destination = $max_size;
   	$destination = imagecreatetruecolor($largeur_destination, $hauteur_destination); // On crée la miniature vide
	   imagecopyresampled($destination, $source, 0, 0, 0, 0, $largeur_destination, $hauteur_destination, $largeur_source, $hauteur_source);
	}  
	else { 
   	// si la largeur est inférieure ou égale à la hauteur, on entre dans ce cas
   	// on crée une ressource pour notre miniature
   	$hauteur_destination = round(($max_size/$largeur_source)*$hauteur_source);
   	$largeur_destination = $max_size;
   	$destination = imagecreatetruecolor($largeur_destination, $hauteur_destination); // On crée la miniature vide 
   	// on place dans la ressource que nous venons de créer une copie de l'image originelle, redimensionnée et r
	// On crée la miniature
	imagecopyresampled($destination, $source, 0, 0, 0, 0, $largeur_destination, $hauteur_destination, $largeur_source, $hauteur_source);
	}
	// On enregistre la miniature sous le nom "mini_couchersoleil.jpg"
	imagejpeg($destination, $img_dest);
	imagedestroy($destination);
	return TRUE;
}


?>

	<div class="navbar navbar-fixed-top">
	  <div class="navbar-inner">
	    <div class="container">
	    <a class="brand" href=".">
	      Viewpics v0.1
	    </a>
	      <ul class="nav">
	        <li class="active">
	          <a href=".">Home</a>
	        </li>
	        
	      </ul>
	    </div>
	 </div>
	</div>
    <div class="container-fluid">
      <div class="row-fluid">
        <div class="span3">
          <div class="well sidebar-nav">
            <ul class="nav nav-list">
              <li class="nav-header">Évènements</li>
<!--              <li class="active"><a href="#">Link</a></li>-->
              <?php  
              
              $liste_repertoires	= array();
              $i=0;	//	$i = tous les répertoires
              $nb_rep=0;
              $racine = 'Photos/';
              $liste_repertoires = parcours_rep($racine, '', $racine, $i=0);
              $nb_rep = (count($liste_repertoires));
              sort($liste_repertoires);	//	ou   rsort($liste_repertoires);
              for ($i=0; $i<$nb_rep; $i++)
              {
              	if ($liste_repertoires[$i] != '')
              	{
              //		echo 'liste_rep '.$i.' = '.$liste_repertoires[$i].'<br/>';
              		echo '<li><a href="?rep='.urlencode('Photos_miniatures/'.$liste_repertoires[$i].'/').'">'.$liste_repertoires[$i].'</a></li>';
              	}
              }
               
              ?>
             </ul>
          </div><!--/.well -->
        </div><!--/span-->
        <div class="span9">
          <div class="hero-unit">
            <h1>Bienvenue sur Viewpics!</h1>
            <p>La galerie officielle des G33K'OS.</p>
          </div>
          <div class="row-fluid">
<!--            ############################################################-->
<!--            AFFICHAGE DU FORMULAIRE POUR LA CREATION D'EVENEMENTS       -->
<!--            ############################################################-->         
          <div class="span6">
            <h2>Création Évènement</h2>
            <form class="well" method="post" action="index.php">
              <label>Veuillez écrire le nom de l'évènement.</label>
              <input type="text" class="span6" placeholder="Type something…" name="foldername">
              <span class="help-block">Ex : Catégorie/Évènement/Sous évènement</span>
              <button type="submit" class="btn">Créer</button>
              <?php 
              	if(isset($_POST['foldername'])){
              		if(@mkdir('Photos/'.$_POST['foldername'], 0777, true)){
              			if(@mkdir('Photos_miniatures/'.$_POST['foldername'], 0777, true))
              				echo '<div class="alert alert-success">
              			  <button class="close" data-dismiss="alert">×</button>
              			  <strong>Warning!</strong> Évènement & miniaturisation créés .
              			</div>';
              		}
              	}
              ?>
            </form>
           </div><!--/span-->
<!--            ############################################################-->
<!--            AFFICHAGE DU FORMULAIRE POUR L'ENVOIE DES PHOTOS            -->
<!--            ############################################################-->
          <div class="span6">
          <h2>Uploader</h2>
          <form class="well" method="post" action="index.php" enctype="multipart/form-data">
            <label>Sélectionner vos photos</label>
            <input type="file" multiple="multiple" name="photos[]"/>
            <span class="help-block">Seul le PNG et le JPG sont supportés. N'uploadez 10 photos par 10 photos pas plus.</span>
            <select id="select01" name="select01">
            	<?php 
//            	get_tree_form('Photos/'); 
//				$liste_repertoires	= array();
//				$i=0;	//	$i = tous les répertoires
//				$nb_rep=0;
//				$racine = 'Photos/';
//				$liste_repertoires = parcours_rep($racine, '', $racine, $i=0);
//				$nb_rep = (count($liste_repertoires));
//				sort($liste_repertoires);	//	ou   rsort($liste_repertoires);
				for ($i=0; $i<$nb_rep; $i++)
				{
					if ($liste_repertoires[$i] != '')
					{
				//		echo 'liste_rep '.$i.' = '.$liste_repertoires[$i].'<br/>';
						echo '<option>'.$liste_repertoires[$i].'</option>';
					}
				}
				
            	?>
             </select>
             <span class="help-block">Sélectionner l'évènements.</span>
            <button type="submit" class="btn">Envoyer</button>
          <?php 
          $nb = count($_FILES['photos']['name']);
//          echo $nb;
          if(isset($_FILES['photos'])){
          	$dossier = 'Photos/'.$_POST['select01'].'/';
//          	var_dump($_FILES); // pour verifier si le tableau est rempli DEBUG
          	for($i = 0; $i<$nb; $i++){
          		$fichier = basename($_FILES['photos']['name'][$i]);
          		$taille_maxi = 100000000; // taille maxi du fichier à uploader.
          		$taille = filesize($_FILES['photos']['tmp_name'][$i]);
          		$extensions = array('.png', '.gif', '.jpg', '.jpeg', '.JPG');
          		$extension = strrchr($_FILES['photos']['name'][$i], '.'); 
          		//Début des vérifications de sécurité...
          		if(!in_array($extension, $extensions)) //Si l'extension n'est pas dans le tableau
          		{
          	     	$erreur = 'Vous devez uploader un fichier de type png, gif, jpg, jpeg, txt ou doc...';
          		}
          		if($taille>$taille_maxi) //Si la taille du fichier n'est pas trop grande
          		{
          	    	 $erreur = 'Le fichier est trop gros...';
          		}
          		if(!isset($erreur)) //S'il n'y a pas d'erreur, on upload
          		{
          	    	 //On formate le nom du fichier original ici...
          	     	$fichier = strtr($fichier, 
          	          'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 
          	          'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
          	     	$fichier = preg_replace('/([^.a-z0-9]+)/i', '-', $fichier);
          	     	// On vérifie que le fichier n'existe pas déjà
          	     	if (file_exists($dossier.$fichier)) {
						// On incrémente le préfixe
						$i = 0;
						while (file_exists($dossier.$i.'_'.$fichier)) {
							$i++;
						}
						// on vient de trouver un préfixe disponible
						$fichier = $i.'_'.$fichier;
					}
          	     	
          	     	if(move_uploaded_file($_FILES['photos']['tmp_name'][$i], $dossier . $fichier)) //Si la fonction renvoie TRUE, c'est que ça a fonctionné...
          	     	{
          	         
          	     	}
          	     	else //Sinon (la fonction renvoie FALSE).
          	     	{
          	          	echo 'Echec de l\'upload !';
          	     	}
          		}
          		else
          		{
          	     	echo $erreur;
          		}
//          		création de la miniature
//				echo $dossier;
				$dossier_miniature = str_replace('Photos', 'Photos_miniatures', $dossier);
//				echo $dossier_miniature.$fichier;
				imagethumb($dossier.$fichier,$dossier_miniature.'mini_'.$fichier,600);
				           	}
           	echo '<div class="alert alert-success">
           	  <button class="close" data-dismiss="alert">×</button>
           	  <strong>Super!</strong> '.$nb.' Envoie(s) effectué(s).
           	</div>';
          }
           ?>
          </form>
          </div>
          </div>
          <div class="row-fluid">
            

<!--            ############################################################-->
<!--            AFFICHAGE DE LA LISTE DES MINIATURES POUR LE REP SELECTIONNE-->
<!--            ############################################################-->
            <div class="span12">
              
              <?php
              $dossier = $_GET['rep'];
              echo '<h2>'.$dossier.'</h2>'; 
              $image_largeur = 150;
              $valide_extensions = array('jpg', 'jpeg', 'gif', 'png', 'JPG');
              
              $Ressource = opendir($dossier);
              while($fichier = readdir($Ressource))
              {
                  $berk = array('.', '..');
              
                  $test_Fichier = $dossier.$fichier;
              
                  if(!in_array($fichier, $berk) && !is_dir($test_Fichier))
                  {
                      $ext = pathinfo($fichier,  PATHINFO_EXTENSION);
              
                      if(in_array($ext, $valide_extensions))
                      {
                          echo '<div style="float:left; width:'.$image_largeur.'px; margin-right:10px" class="thumbnail">
                                  <a href="'.$test_Fichier.'" rel="lightbox[serie]" ><img src="'.$test_Fichier.'" style="'.$image_largeur.'px" /></a>
                              </div>';
                      }
                  }
              }
              
               ?>
            </div><!--/span-->
          </div><!--/row-->
         </div><!--/span-->
      </div><!--/row-->

      <hr>

      <footer>
        <p>&copy; ZetaX 2012</p>
      </footer>
	</div><!--/.fluid-container-->

	<!-- Le javascript
	    ================================================== -->
	    <!-- Placed at the end of the document so the pages load faster -->
	    <script src="js/bootstrap-transition.js"></script>
	    <script src="js/bootstrap-alert.js"></script>
	    <script src="js/bootstrap-modal.js"></script>
	    <script src="js/bootstrap-dropdown.js"></script>
	    <script src="js/bootstrap-scrollspy.js"></script>
	    <script src="js/bootstrap-tab.js"></script>
	    <script src="js/bootstrap-tooltip.js"></script>
	    <script src="js/bootstrap-popover.js"></script>
	    <script src="js/bootstrap-button.js"></script>
	    <script src="js/bootstrap-collapse.js"></script>
	    <script src="js/bootstrap-carousel.js"></script>
	    <script src="js/bootstrap-typeahead.js"></script>
	    <script src="js/jquery-ui-1.8.18.custom.min.js"></script>
	    <script src="js/jquery.smooth-scroll.min.js"></script>
	    <script src="http://gsgd.co.uk/sandbox/jquery/easing/jquery.easing.1.3.js"></script>
	    
	</body>
</html>

