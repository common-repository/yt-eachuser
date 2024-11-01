<?php
function gwwpp_yt_each_user_admin_page(){

	//送信確認の隠しキー
	$hidden_field_name="yteu_posted_key";

	//Youtubeのユーザ名（挿入処理）
		//セットするフィールド名
		$data_field_name='user_name';
		$data_field_width='size_w';
		$data_field_height='size_h';
		$data_field_type='output_type';
		$data_field_api_key = 'api_key';

		//挿入先オプション名
		$opt_name=gwwpp_yt_each_user::USER_NAME_KEY;
		$opt_width=gwwpp_yt_each_user::SIZE_W_KEY;
		$opt_height=gwwpp_yt_each_user::SIZE_H_KEY;
		$opt_type=gwwpp_yt_each_user::OUTPUT_TYPE_KEY;
		$opt_api_key=gwwpp_yt_each_user::API_KEY_KEY;

		//現在の値
		$opt_name_value=get_option($opt_name);
		$opt_width_value=get_option($opt_width);
		$opt_height_value=get_option($opt_height);
		$opt_type_value=get_option($opt_type);
		$opt_api_key_value=get_option($opt_api_key);


	//投稿のチェックと挿入処理
	if($_POST[$hidden_field_name]=='Yes')://投稿処理-------------------------
		//投稿値
		$opt_name_value=$_POST[$data_field_name];
		$opt_width_value=$_POST[$data_field_width];
		$opt_height_value=$_POST[$data_field_height];
		$opt_type_value=$_POST[$data_field_type];
		$opt_api_key_value = $_POST[$data_field_api_key];

		//database更新
		update_option($opt_name,$opt_name_value);
		update_option($opt_width,$opt_width_value);
		update_option($opt_height,$opt_height_value);
		update_option($opt_type,$opt_type_value);
		update_option($opt_api_key,$opt_api_key_value);

?>
<div class="updated"><p><strong><?php echo('保存完了。'); ?></strong></p></div>
<?php

	endif;//投稿処理--ここまで---------------------------------------------------

?>
<div class="wrap">
<h2><?php _e('yt_EachUser','yt_eachuser');?></h2><?php /*Youtube　ユーザ毎動画一覧　設定画面*/?>
<form name="form1" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI']); ?>">
<input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Yes">

<p><?php _e('Youtube user name','yt_eachuser');?>
<input type="text" name="<?php echo $data_field_name; ?>" value="<?php echo $opt_name_value; ?>" size="20">
<br />
*<?php _e('the videos list this user\'s uploaded','yt_eachuser');?><br />
*<?php _e('Template tag','yt_eachuser');?>: <?php echo(htmlspecialchars('<?php get_yt_each_user();?> <?php get_yt_each_user($youtubeID,$width,$height,$type,$num);?>'));?><br />
<ul>
<li>type: thumbnail | player</li>
</ul>
*<?php _e('Shortcode API','yt_eachuser');?>: <?php echo(htmlspecialchars('[print_yt_EachUser]'));?>
</p>
<hr />

<p><?php _e('output type','yt_eachuser');?>
<select name="<?php echo $data_field_type; ?>">
	<option value="thumbnail" <?php if($opt_type_value=='thumbnail'):?>selected="selected"<?php endif;?>><?php _e('thumbnail','yt_eachuser');?>:</option>
	<option value="player" <?php if($opt_type_value=='player'):?>selected="selected"<?php endif;?>><?php _e('player','yt_eachuser');?>:</option>
</select>
<br />
*<?php _e('output type','yt_eachuser');?>:
</p>
<hr />

<p><?php _e('width','yt_eachuser');?>
<input type="text" name="<?php echo $data_field_width; ?>" value="<?php echo $opt_width_value; ?>" size="5">　px
<br />
*<?php _e('width pixel integer','yt_eachuser');?>
</p>

<p><?php _e('height','yt_eachuser');?>
<input type="text" name="<?php echo $data_field_height; ?>" value="<?php echo $opt_height_value; ?>" size="5">　px
<br />
*<?php _e('height pixel integer','yt_eachuser');?>
</p>
<hr />

<p><?php _e('API key','yt_eachuser');?>
<input type="text" name="<?php echo $data_field_api_key; ?>" value="<?php echo $opt_api_key_value; ?>" size="50"><br>
<?php _e('Please specify the API Key that allowed \'Internet Protocol address\' or \'host name\'.','yt_eachuser');?><br>
(<?php _e('Internet Protocol address','yt_eachuser');?>:<strong><?php echo esc_html(gethostbyname(gethostname()));?></strong>)<br>
(<?php _e('Host name','yt_eachuser');?><strong>:<?php echo esc_html(gethostname());?></strong>)
<hr />


<p class="submit">
<input type="submit" name="Submit" value="<?php _e('update options','yt_eachuser');?>" />
</p>

</form>
</div>

<?php
}
?>
