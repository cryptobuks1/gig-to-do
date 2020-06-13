<?php

session_start();

require_once("../includes/db.php");

require_once("../functions/functions.php");

if(isset($_GET['cat_url'])){
	
	unset($_SESSION['cat_child_id']);
	
	$get_cat = $db->select("categories",array('cat_url' => $input->get('cat_url')));

	$cat_id = $get_cat->fetch()->cat_id;
	
	$_SESSION['cat_id']=$cat_id;
	
}

if(isset($_GET['cat_child_url'])){
	
	unset($_SESSION['cat_id']);

	$get_cat = $db->select("categories",array('cat_url' => $input->get('cat_url')));

	$cat_id = $get_cat->fetch()->cat_id;
	
		
	$get_child = $db->select("categories_children",array('child_parent_id'=>$cat_id,'child_url'=>$input->get('cat_child_url')));

	$_SESSION['cat_child_id']= $get_child->fetch()->child_id;
	
}
?>

<!DOCTYPE html>
<html lang="en" class="ui-toolkit">

<head>
    
   <?php

    if(isset($_SESSION['cat_id'])){
	
	$cat_id = $_SESSION['cat_id'];


    $get_meta = $db->select("cats_meta",array("cat_id" => $cat_id,"language_id" => $siteLanguage));

    $row_meta = $get_meta->fetch();

    $cat_title = $row_meta->cat_title;

    $cat_desc = $row_meta->cat_desc;

  	?>

	<title><?php echo $site_name; ?> - <?php echo $cat_title; ?>  </title>
    
    <meta name="description" content="<?php echo $cat_desc; ?>" >

	<?php } ?>
    
    <?php

	if(isset($_SESSION['cat_child_id'])){
	
	$cat_child_id = $_SESSION['cat_child_id'];
	

    $get_meta = $db->select("child_cats_meta",array("child_id" => $cat_child_id,"language_id" => $siteLanguage));

    $row_meta = $get_meta->fetch();

    $child_title = $row_meta->child_title;

    $child_desc = $row_meta->child_desc;

	?>
	
	<title><?php echo $site_name; ?> - <?php echo $child_title; ?></title>

	<meta name="description" content="<?php echo $child_desc; ?>" >
	
	<?php } ?>
    
	<meta charset="utf-8">
	
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	
	<meta name="author" content="<?php echo $site_author; ?>">

    <link href="https://fonts.googleapis.com/css?family=Roboto:400,500,700,300,100" rel="stylesheet">

	<link href="<?= $site_url; ?>/styles/bootstrap.css" rel="stylesheet">
	
    <link href="<?= $site_url; ?>/styles/custom.css" rel="stylesheet">
    
    <!-- Custom css code from modified in admin panel --->
	
	<link href="<?= $site_url; ?>/styles/styles.css" rel="stylesheet">
	
	<link href="<?= $site_url; ?>/styles/categories_nav_styles.css" rel="stylesheet">
	
	<link href="<?= $site_url; ?>/font_awesome/css/font-awesome.css" rel="stylesheet">
	
	<link href="<?= $site_url; ?>/styles/sweat_alert.css" rel="stylesheet">

	<!-- Optional: include a polyfill for ES6 Promises for IE11 and Android browser -->

    <script src="<?= $site_url; ?>/js/ie.js"></script>

    <script type="text/javascript" src="<?= $site_url; ?>/js/sweat_alert.js"></script>

	<script type="text/javascript" src="<?= $site_url; ?>/js/jquery.min.js"></script>

	<?php if(!empty($site_favicon)){ ?>
    <link rel="shortcut icon" href="<?= $site_url; ?>/images/<?php echo $site_favicon; ?>" type="image/x-icon">
    <?php } ?>

</head>

<body class="bg-white is-responsive">

<?php require_once("../includes/header.php"); ?>

<div class="container-fluid mt-5"> <!-- Container start -->

	<div class="row">

		<div class="col-md-12">

			<center>
				<?php

                    if(isset($_SESSION['cat_id'])){

                    $cat_id = $_SESSION['cat_id'];

				    $get_meta = $db->select("cats_meta",array("cat_id" => $cat_id,"language_id" => $siteLanguage));

				    $row_meta = $get_meta->fetch();

				    $cat_title = $row_meta->cat_title;

				    $cat_desc = $row_meta->cat_desc;

                    ?>

                    <h1> <?php echo $cat_title; ?> </h1>

                    <p class="lead"><?php echo $cat_desc; ?></p>

                    <?php } ?>

                    <?php

                    if(isset($_SESSION['cat_child_id'])){

                    $cat_child_id = $_SESSION['cat_child_id'];

                    $get_meta = $db->select("child_cats_meta",array("child_id" => $cat_child_id,"language_id" => $siteLanguage));

				    $row_meta = $get_meta->fetch();

				    $child_title = $row_meta->child_title;

				    $child_desc = $row_meta->child_desc;
                   
                    ?>

                    <h1> <?php echo $child_title; ?> </h1>

                    <p class="lead"><?php echo $child_desc; ?></p>

                    <?php } ?>

			</center>

			<hr class="mt-5 pt-2">

		</div>

	</div>

	<div class="row mt-3">

		<div class="col-lg-3 col-md-4 col-sm-12 <?=($lang_dir == "right" ? 'order-2 order-sm-1':'')?>">

			<?php require_once("../includes/category_sidebar.php"); ?>

		</div>

		<div class="col-lg-9 col-md-8 col-sm-12 <?=($lang_dir == "right" ? 'order-1 order-sm-2':'')?>">
            
	    	<div class="row flex-wrap <?=($lang_dir == "right" ? 'justify-content':'')?>" id="category_proposals">

	            <?php get_category_proposals(); ?>

	        </div>

	        <div id="wait"></div>

	        <br>

	        <div class="row justify-content-center mb-5 mt-0"><!-- row justify-content-center Starts -->

	            <nav><!-- nav Starts -->

	                <ul class="pagination" id="category_pagination">

	                <?php get_category_pagination(); ?>

	                </ul>

	            </nav><!-- nav Ends -->

	        </div>

		</div>

	</div>

</div><!-- Container ends -->


<div class="append-modal"></div>

<?php require_once("../includes/footer.php"); ?>
    
<script>

function get_category_proposals(){

var sPath = ''; 

var aInputs = $('li').find('.get_online_sellers');

var aKeys   = Array();

var aValues = Array();

iKey = 0;

$.each(aInputs,function(key,oInput){

if(oInput.checked){
	
aKeys[iKey] =  oInput.value

};

iKey++;

});

if(aKeys.length>0){
	
var sPath = '';
	
for(var i = 0; i < aKeys.length; i++){

sPath = sPath + 'online_sellers[]=' + aKeys[i]+'&';

}

}


var cat_url = "<?php echo $input->get('cat_url'); ?>";

sPath = sPath + 'cat_url=' + cat_url +'&';

<?php if(isset($_REQUEST['cat_child_url'])){ ?>

var cat_child_url = "<?php echo $input->get('cat_child_url'); ?>";

sPath = sPath+ 'cat_child_url='+ cat_child_url +'&';

var url_plus = "../";

<?php }else{ ?>

var url_plus = "";

<?php } ?>


var aInputs = Array();

var aInputs = $('li').find('.get_delivery_time');

var aKeys   = Array();

var aValues = Array();

iKey = 0;

$.each(aInputs,function(key,oInput){

if(oInput.checked){
	
aKeys[iKey] =  oInput.value

};

iKey++;

});

if(aKeys.length>0){

for(var i = 0; i < aKeys.length; i++){
	
sPath = sPath + 'delivery_time[]=' + aKeys[i]+'&';

}

}

var aInputs = Array();

var aInputs = $('li').find('.get_seller_level');

var aKeys   = Array();

var aValues = Array();

iKey = 0;

$.each(aInputs,function(key,oInput){

if(oInput.checked){
	
aKeys[iKey] =  oInput.value

};

iKey++;

});

if(aKeys.length>0){
	
for(var i = 0; i < aKeys.length; i++){
	
sPath = sPath + 'seller_level[]=' + aKeys[i]+'&';

}

}

var aInputs = Array();

var aInputs = $('li').find('.get_seller_language');

var aKeys   = Array();

var aValues = Array();

iKey = 0;

$.each(aInputs,function(key,oInput){

if(oInput.checked){
	
aKeys[iKey] =  oInput.value

};

iKey++;

});

if(aKeys.length>0){
	
for(var i = 0; i < aKeys.length; i++){

sPath = sPath + 'seller_language[]=' + aKeys[i]+'&';

}

}		

$('#wait').addClass("loader");		

$.ajax({  

url: url_plus + "../category_load",  
method:"POST",  
data: sPath+'zAction=get_category_proposals',  
success:function(data){

$('#category_proposals').html('');  

$('#category_proposals').html(data);

$('#wait').removeClass("loader");

}  

});							  

$.ajax({  

url: url_plus + "../category_load",  
method:"POST",  
data: sPath+'zAction=get_category_pagination',  
success:function(data){  

$('#category_pagination').html('');  

$('#category_pagination').html(data); 

}  

});

}

$('.get_online_sellers').click(function(){ 

get_category_proposals(); 

});

$('.get_delivery_time').click(function(){ 

get_category_proposals(); 

}); 

$('.get_seller_level').click(function(){ 

get_category_proposals(); 

}); 

$('.get_seller_language').click(function(){ 

get_category_proposals(); 

});

</script>



<script type="text/javascript">
	
	$(document).ready(function(){

		$(".get_cat_id").click(function(){

			if($(".get_cat_id:checked").length > 0 ) {

				$(".clear_cat_id").show();
			}

			else{

				$(".clear_cat_id").hide();
			}



		});


		$(".get_delivery_time").click(function(){

			if($(".get_delivery_time:checked").length > 0 ) {

				$(".clear_delivery_time").show();
			}

			else{

				$(".clear_delivery_time").hide();
			}

		});


		$(".get_seller_level").click(function(){

			if($(".get_seller_level:checked").length > 0 ) {

				$(".clear_seller_level").show();
			}

			else{

				$(".clear_seller_level").hide();
			}

		});

		
		$(".get_seller_language").click(function(){

			if($(".get_seller_language:checked").length > 0 ) {

				$(".clear_seller_language").show();
			}

			else{

				$(".clear_seller_language").hide();
			}

		});


		$(".clear_cat_id").click(function(){

			$(".clear_cat_id").hide();

		});


		$(".clear_delivery_time").click(function(){

			$(".clear_delivery_time").hide();

		});
		

		$(".clear_seller_level").click(function(){

			$(".clear_seller_level").hide();

		});


		$(".clear_seller_language").click(function(){

			$(".clear_seller_language").hide();

		});
		

	});


	function clearCat(){

		$('.get_cat_id').prop('checked',false);
	}

	function clearDelivery(){

		$('.get_delivery_time').prop('checked',false);
	}

	function clearLevel(){

		$('.get_seller_level').prop('checked',false);
	}

	function clearLanguage(){

		$('.get_seller_language').prop('checked',false);
	}

</script>


</body>

</html>