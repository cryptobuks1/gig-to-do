<?php
@session_start();
require_once("../includes/db.php");
if(!isset($_SESSION['seller_user_name'])){
echo "<script>window.open('../login','_self')</script>";
}

// $rules = array(
// "message" => "required",
// "description" => "required",
// "delivery_time" => "required",
// "amount" => "required");

// $messages = array("cat_id" => "you must need to select a category","child_id" => "you must need to select a child category");
// $val = new Validator($_POST,$rules,$messages);

// if($val->run() == false){
	// Flash::add("form_errors",$val->get_all_errors());
	// Flash::add("form_data",$_POST);
// }else{

$login_seller_user_name = $_SESSION['seller_user_name'];
$select_login_seller = $db->select("sellers",array("seller_user_name" => $login_seller_user_name));
$row_login_seller = $select_login_seller->fetch();
$login_seller_id = $row_login_seller->seller_id;

$receiver_seller_id = $input->post('receiver_id');
$proposal_id = $input->post('proposal_id');
$message = $input->post('message');
$file = $input->post('file');
$description = $input->post('description');
$delivery_time = $input->post('delivery_time');
$amount = $input->post('amount');

$insert_offer = $db->insert("messages_offers",array("sender_id" => $login_seller_id,"proposal_id" => $proposal_id,"description" => $description,"delivery_time" => $delivery_time,"amount" => $amount,"status" => 'active'));
$last_offer_id = $db->lastInsertId();
if($insert_offer){
		
	$message_date = date("h:i: F d, Y");
	$dateAgo = date("Y-m-d H:i:s");
	$message_status = "unread";
		
	$get_inbox_sellers = $db->query("select * from inbox_sellers where sender_id='$login_seller_id' and receiver_id=:r_id or sender_id=:s_id and receiver_id='$login_seller_id'",array("r_id"=>$receiver_seller_id,"s_id"=>$receiver_seller_id));
	$row_inbox_sellers = $get_inbox_sellers->fetch();
	$message_group_id = $row_inbox_sellers->message_group_id;
		

	$insert_message = $db->insert("inbox_messages",array("message_sender" => $login_seller_id,"message_receiver" => $receiver_seller_id,"message_offer_id" => $last_offer_id,"message_group_id" => $message_group_id,"message_desc" => $message,"message_file" => $file,"message_date" => $message_date,"dateAgo" => $dateAgo,"bell" => 'active',"message_status" => $message_status));
	$last_message_id = $db->lastInsertId();

	$update_inbox_sellers = $db->update("inbox_sellers",array("sender_id" => $login_seller_id,"receiver_id" => $receiver_seller_id,"message_status" => $message_status,"message_id" => $last_message_id,'popup'=>'1'),array("message_group_id" => $message_group_id));
	if($update_inbox_sellers){

		$select_hide_seller_messages = $db->delete("hide_seller_messages",array("hider_id"=>$login_seller_id,"hide_seller_id"=>$receiver_seller_id));	
		$count_hide_seller_messages = $select_hide_seller_messages->rowCount();
		if($count_hide_seller_messages == 1){
		$delete_hide_seller_messages = $db->delete("hide_seller_messages",array("hider_id"=>$login_seller_id,"hide_seller_id"=>$receiver_seller_id));	
		}

		$site_email_address = $row_general_settings->site_email_address;
		$site_logo = $row_general_settings->site_logo;
		$get_seller = $db->select("sellers",array("seller_id" => $receiver_seller_id));
		$row_seller = $get_seller->fetch();
		$seller_user_name = $row_seller->seller_user_name;
		$seller_email = $row_seller->seller_email;
		require '../mailer/PHPMailerAutoload.php';
		$mail = new PHPMailer;
		if($enable_smtp == "yes"){
		$mail->isSMTP();
		$mail->Host = $s_host;
		$mail->Port = $s_port;
		$mail->SMTPAuth = true;
		$mail->SMTPSecure = $s_secure;
		$mail->Username = $s_username;
		$mail->Password = $s_password;
		}
		$mail->setFrom($site_email_address,$site_name);
		$mail->addAddress($seller_email);
		$mail->addReplyTo($site_email_address,$site_name);
		$mail->isHTML(true);
		$mail->Subject = "You've received a message from $login_seller_user_name";
		$mail->Body = "
		<html>
		<head>
		<style>
		.container {
		background: rgb(238, 238, 238);
		padding: 80px;
		}
		@media only screen and (max-device-width: 690px) {
		.container {
			background: rgb(238, 238, 238);
		width:100%;
		padding:1px;
		}
		}
		.box {
			background: #fff;
			margin: 0px 0px 30px;
			padding: 8px 20px 20px 20px;
		    border:1px solid #e6e6e6;
		    box-shadow:0px 1px 5px rgba(0, 0, 0, 0.1);			
		}
		.lead {
			font-size:16px;
		}
		.btn{
			background:green;
			margin-top:20px;
			color:white !important;
			text-decoration:none;
			padding:10px 16px;
			font-size:18px;
			border-radius:3px;
		}
		hr{
			margin-top:20px;
			margin-bottom:20px;
			border:1px solid #eee;
		}
		</style>
		</head>
		<body class='is-responsive'>
		<div class='container'>
		<div class='box'>
		<center>
		<img src='$site_url/images/$site_logo' width='100'>
		<h2> You've received a message from $login_seller_user_name </h2>
		</center>
		<hr>
		<p class='lead'> Dear $seller_user_name, </p>
		<p class='lead'> $login_seller_user_name left you a message in your inbox. </p>
		<p class='lead'> $message </p>
		<center>
		<a href='$site_url/conversations/inbox?single_message_id=$message_group_id' class='btn'>View & Reply</a>
		</center>
		</div>
		</div>
		</body>
		</html>";
		$mail->send();	
		echo "<script>window.open('inbox?single_message_id=$message_group_id','_self')</script>";
	}
		
}
	
// }

?>