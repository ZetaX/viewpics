<!DOCTYPE html>
<html lang="fr" > 
<head> 
	<title>ViewPics v0.1</title> 
  	<meta http-equiv="content-type" content="text/html; charset=UTF-8" /> 
<!--Les styles avec le bootstrap Twitter-->
	<link rel="stylesheet" href="jqueryFileTree.css" type="text/css" />
	<link href="css/bootstrap.css" rel="stylesheet">
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
	
<!--fin des styles-->
	<script src="http://code.jquery.com/jquery-1.7.2.min.js" type="text/javascript"></script>
	<script src="http://gsgd.co.uk/sandbox/jquery/easing/jquery.easing.1.3.js" type="text/javascript"></script>
	<script src="jqueryFileTree.js" type="text/javascript"></script>
</head> 


<body>
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
              <li class="active"><a href="#">Link</a></li>
              <?php echo get_tree('Photos/'); ?>
             </ul>
          </div><!--/.well -->
        </div><!--/span-->
        <div class="span9">
          <div class="hero-unit">
            <h1>Bienvenue sur Viewpics!</h1>
            <p>La galerie officielle des G33K'OS.</p>
          </div>
          <div class="row-fluid">
          <div class="span6">
            <h2>Création Évènement</h2>
            <form class="well" method="post" action="index.php">
              <label>Nom de l'évènement</label>
              <input type="text" class="span6" placeholder="Type something…" name="foldername">
              <span class="help-block">Veuillez écrire le nom de l'évènement.</span>
              <button type="submit" class="btn">Créer</button>
              <?php 
              	if(isset($_POST['foldername'])){
              		if(@mkdir('Photos/'.$_POST['foldername'], 0777, true)){
              			echo '<div class="alert alert-success">
              			  <button class="close" data-dismiss="alert">×</button>
              			  <strong>Warning!</strong> Évènement créé.
              			</div>';
              		}
              	}
              ?>
            </form>
           </div><!--/span-->
          <div class="span6">
          <h2>Uploader</h2>
          <form class="well" method="post" action="index.php" enctype="multipart/form-data">
            <label>Sélectionner vos photos</label>
            <input type="file" multiple="multiple" name="photos[]"/>
            <span class="help-block">Seul le PNG et le JPG sont supportés.</span>
            <select id="select01" name="select01">
            	<?php get_tree_form('Photos/'); ?>
             </select>
             <span class="help-block">Sélectionner l'évènements.</span>
            <button type="submit" class="btn">Envoyer</button>
          <?php 
          $nb = count($_FILES['photos']['name']);
//          echo $nb;
          if(isset($_FILES['photos'])){
          	$dossier = $_POST['select01'];
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
          		if($taille>$taille_maxi)
          		{
          	    	 $erreur = 'Le fichier est trop gros...';
          		}
          		if(!isset($erreur)) //S'il n'y a pas d'erreur, on upload
          		{
          	    	 //On formate le nom du fichier ici...
          	     	$fichier = strtr($fichier, 
          	          'ÀÁÂÃÄÅÇÈÉÊËÌÍÎÏÒÓÔÕÖÙÚÛÜÝàáâãäåçèéêëìíîïðòóôõöùúûüýÿ', 
          	          'AAAAAACEEEEIIIIOOOOOUUUUYaaaaaaceeeeiiiioooooouuuuyy');
          	     	$fichier = preg_replace('/([^.a-z0-9]+)/i', '-', $fichier);
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
            
            <?php 
            function glob_free($dir,$patern='*'){
            	$tab='';
            	if (is_dir($dir)) {
            	    if ($dh = opendir($dir)) {
            	        while (($file = readdir($dh)) !== false) {
            				$ext=explode('.',$file);
            				$ext=$ext[count($ext)-1];
            	            if($ext==$patern || $patern=="*" && $file!='.' && $file!='..'){
            					$tab[]=$dir.$file;
            				}
            	        }
            	        closedir($dh);
            	    }
            	}
            	return $tab;
            }   
            function get_tree($path = './'){
                    if (substr($path,-1) !== '/')
                        $path .= '/';
                    $tree = '';
                    $dirs = glob_free($path,'*');
//            		<li><a href="#">Évènement 1</a></li>
            		if(is_array($dirs))
            		foreach ($dirs as $value){
                        if(is_dir($value))
//                        	on envoie l'url du dossier par $GET 
                            $tree .= "<li><a href='?rep=".urlencode($value.'/'.get_tree($value.'/'))."'>".$value.'/'.get_tree($value.'/')."</a></li>"; 
                    }
                    return $tree;
                }
             function get_tree_form($path = './'){
                                 if (substr($path,-1) !== '/')
                                     $path .= '/';
                                 $tree = '';
                                 $dirs = glob_free($path,'*');
                         		
                         		if(is_array($dirs))
                         		foreach ($dirs as $value){
                                     if(is_dir($value))
                                         $tree .= '<option value="'.$value.'/'.get_tree($value.'/').'">'.$value.'/'.get_tree($value.'/').'</option>'; 
                                 }
                                 echo $tree;
//                                 return $tree;
                             }
             
//                echo get_tree('Photos/');
             ?>
            
            <div class="span8">
              
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
                          echo '
                              <div style="float:left; width:'.$image_largeur.'px; margin-right:10px">
                                  <img src="'.$test_Fichier.'" style="'.$image_largeur.'px" />
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
	    <script src="js/jquery.js"></script>
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
	</body>
</html>

