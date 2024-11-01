<?php
/*
Plugin Name: yt_EachUser
Plugin URI: http://www.genki-works.com/
Description: The list of videos is displayed specifying the user of Youtube.
Version: 1.2.5
Auther kt_shin1
Auther URI: http://www.genki-works.com/
*/

/*  Copyright 2011 kt_shin1 (email : nomeshi74@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

/*----------------------------------------------------------
言語ファイル読み込み
----------------------------------------------------------*/
$gwwpp_yt_eachuser_locale=get_locale();
$gwwpp_yt_eachuser_locale_file=dirname(__FILE__).'/yt_eachuser-'.$gwwpp_yt_eachuser_locale.'.mo';
load_textdomain('yt_eachuser',$gwwpp_yt_eachuser_locale_file);

/*----------------------------------------------------------
テンプレート関数
----------------------------------------------------------*/
function get_yt_each_user($name='',$width='',$height='',$type='',$num='',$api_key=false){
	$cls=new gwwpp_yt_each_user($name,$width,$height,$type,$num,$api_key);
	$res=$cls->execute();

	echo $res;
}
/*----------------------------------------------------------
ショートコード
----------------------------------------------------------*/
function get_yt_each_user_sht($atts){
	extract(shortcode_atts(array(
		'ytname'=>'',
		'width'=>'',
		'height'=>'',
		'type'=>'',
		'num'=>'',
		'api_key' => '',
	),$atts));
	$cls=new gwwpp_yt_each_user($ytname,$width,$height,$type,$num);
	$res=$cls->execute();

	return $res;
}
add_shortcode('print_yt_EachUser','get_yt_each_user_sht');

/*----------------------------------------------------------
管理画面テンプレート関数
----------------------------------------------------------*/
require('tplt/admin_page.php');



/*----------------------------------------------------------
実行処理
----------------------------------------------------------*/
class gwwpp_yt_each_user{

	//---------------------------------------------DBkey名--
	const INIT_KEY='_gwwpp_yteu_init_key';//インストール時On
	const ACTIVATE_YTEU_INI_KEY='_gwwpp_yteu_activate_key';
	//---------------------------------------------メニュー関連--
	const ADMIN_MENU_PARENT='options-general.php';
	const ADMIN_MENU_ACCESS_LV=8;
	const ADMIN_PAGE_TITLE='yt_EachUser';
	const ADMIN_MENU_NAME='yt_EachUser';
	const ADMIN_PAGE_TEMPLATE='gwwpp_yt_each_user_admin_page';

	const USER_NAME_KEY='_gwwpp_yteu_user_name_key';//youtube ユーザ名
		const USER_NAME_DEFAULT='YouTube';//Youtube公式がデフォルト
	const SIZE_W_KEY='_gwwpp_yteu_size_w';
		const SIZE_W_DEFAULT=425;
	const SIZE_H_KEY='_gwwpp_yteu_size_h';
		const SIZE_H_DEFAULT=355;
	const OUTPUT_TYPE_KEY='_gwwpp_output_type';
		const OUTPUT_TYPE_DEFAULT='thumbnail';
	const VIEW_NUM_KEY='_gwwpp_view_num';
		const VIEW_NUM_DEFAULT=6;
	const API_KEY_KEY='_gwwpp_api_key';
		const API_KEY_DEFAULT='';

	private $name='';
	private $type='';
	private $width='';
	private $height='';
	private $num='';
	private $api_key='';

	//------------------------------------- コンストラクタ--
	function __construct($name='',$width='',$height='',$type='',$num='',$api_key=false){
		$this->name=$name;
		$this->type=$type;
		$this->width=$width;
		$this->height=$height;
		$this->num=$num;
		$this->api_key=$api_key;
	}


	//--------------------------------------------- 有効時--
	static public function init_option(){
		if(!get_option(self::INIT_KEY)){//インストールしたとき
			update_option(self::USER_NAME_KEY,self::USER_NAME_DEFAULT);
			update_option(self::SIZE_W_KEY,self::SIZE_W_DEFAULT);
			update_option(self::SIZE_H_KEY,self::SIZE_H_DEFAULT);
			update_option(self::OUTPUT_TYPE_KEY,self::OUTPUT_TYPE_DEFAULT);
			update_option(self::VIEW_NUM_KEY,self::VIEW_NUM_DEFAULT);
			update_option(self::API_KEY_KEY,self::API_KEY_DEFAULT);

			update_option(self::INIT_KEY,'1');
		}
	}
	//--------------------------------------------- 削除--
	static public function execute_uninstall(){
		delete_option(self::USER_NAME_KEY);
		delete_option(self::SIZE_W_KEY);
		delete_option(self::SIZE_H_KEY);
		delete_option(self::OUTPUT_TYPE_KEY);
		delete_option(self::VIEW_NUM_KEY);
		delete_option(self::API_KEY_KEY);

		delete_option(self::INIT_KEY);

	}
	//--------------------------------------- 設定メニュー--
	static public function add_admin_menu(){
		add_submenu_page(self::ADMIN_MENU_PARENT,self::ADMIN_PAGE_TITLE,self::ADMIN_MENU_NAME,self::ADMIN_MENU_ACCESS_LV,__FILE__,self::ADMIN_PAGE_TEMPLATE);
	}

	//--------------------------------------- 実行時処理--
	public function execute(){
		//オプション取得
		if(!($width=$this->width))
			$width=get_option(self::SIZE_W_KEY);
		if(!($height=$this->height))
			$height=get_option(self::SIZE_H_KEY);
		if(!($type=$this->type))
			$type=get_option(self::OUTPUT_TYPE_KEY);
		if(!($view_num=$this->num))
			$view_num=get_option(self::VIEW_NUM_KEY);
		$width=intval($width);
		$heigh=intval($height);
		$view_num=intval($view_num);

		// $api_key = 'AIzaSyDKWEJCPQV_vCf_CB2dNpLjdp8yxUvkvs8';
		if(!($api_key=$this->api_key)){
			$api_key=get_option(self::API_KEY_KEY);
		}
		// APIキーが無いときは終了
		if(!$api_key){
			return '';
		}
		//ユーザ名
		if(!($user_name=$this->name))
			$user_name=get_option(self::USER_NAME_KEY);
			$url = 'https://www.googleapis.com/youtube/v3/channels?part=contentDetails&forUsername='.$user_name.'&key='.$api_key;

		// CURLでJSON取得
		$option = array(
        CURLOPT_RETURNTRANSFER => true, //文字列として返す
        CURLOPT_TIMEOUT        => 3, // タイムアウト時間
				CURLOPT_REFERER				=> gethostname(),
    );
    $ch = curl_init($url);
    curl_setopt_array($ch, $option);
		$json = curl_exec($ch);//json
		$info = curl_getinfo($ch);//情報
    $errorNo = curl_errno($ch);//エラー

		// 取得エラー
		if (($errorNo !== CURLE_OK) || ($info['http_code'] !== 200)) {
        return '';
    }
		// json取得
		$channel = json_decode($json, true);
		// 情報取得できないときは終了
		if(!$channel["items"][0]["contentDetails"]["relatedPlaylists"]["uploads"]) {
			exit();
		}
		// リストID
		$listId = $channel["items"][0]["contentDetails"]["relatedPlaylists"]["uploads"];

		//動画リスト取得　JSONで取得
		$url = 'https://www.googleapis.com/youtube/v3/playlistItems?part=snippet&playlistId='.$listId.'&maxResults='.$view_num.'&key='.$api_key;
		$ch = curl_init($url);
    curl_setopt_array($ch, $option);
		$json = curl_exec($ch);//json
		$info = curl_getinfo($ch);//情報
    $errorNo = curl_errno($ch);//エラー
		// 取得エラー
		if (($errorNo !== CURLE_OK) || ($info['http_code'] !== 200)) {
        return '';
    }

		// json取得
		$videos = json_decode($json, true);

		$entries = $videos['items'];

		$html='<ul>';
		$n=0;

		foreach($entries AS $k=>$e){
			$snippet = $e['snippet'];

			$title=$snippet['title'];
			$id=$snippet['resourceId']['videoId'];

			$turl=$snippet['thumbnails']['default']['url'];
				$thumbnail1=esc_url($turl);

			if($type=='player'){
				//プレイヤーで表示の場合
				$url='https://www.youtube.com/embed/'.$id;
				$html.=$this->get_list_player($url,$width,$height);
			}elseif($type=='thumbnail'){
				//サムネイル形式で保存
				$url='http://www.youtube.com/watch?v='.$id;
				$html.=$this->get_list_thumbnail($url,$thumbnail1,$title,$width,$height);
			}else{
				$html.='';
			}


			$html.='';
		}

		$html.='</ul>';

		return $html;
	}

	private function get_list_thumbnail($url,$thumbnail,$title,$width,$height){
		return '<li><a href="'.$url.'"><img src="'.$thumbnail.'" alt="'.$title.'" width="'.$width.'" height="'.$height.'" style="width:'.$width.'px;height:'.$height.'px;" /></a></li>';
	}
	private function get_list_player($url,$width,$height){
			return '<iframe width="'.$width.'" height="'.$height.'" src="'.$url.'" frameborder="0" allowfullscreen></iframe>';
	}

}




/*----------------------------------------------------------
有効化関数
----------------------------------------------------------*/
register_activation_hook(__FILE__,array('gwwpp_yt_each_user','init_option'));

/*----------------------------------------------------------
無効化関数
----------------------------------------------------------*/
//register_deactivation_hook(__FILE__,array('gwwpp_yt_eachuser','execute_deactivate'));

/*----------------------------------------------------------
設定メニュー追加
----------------------------------------------------------*/
add_action('admin_menu',array('gwwpp_yt_each_user','add_admin_menu'));

/*----------------------------------------------------------
ページ表示
----------------------------------------------------------*/

?>
