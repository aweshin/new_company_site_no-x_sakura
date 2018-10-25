<?php
define( "FILE_DIR", "images/test/");
require 'vendor/autoload.php';
use Dotenv\Dotenv;
/**
 * Class GoogleSheetsAPISample
 */
class GoogleSheetsAPISample {
    /**
     * @var Google_Service_Sheets
     */
    protected $service;
    /**
     * @var array|false|string
     */
    protected $spreadsheetId;
    /**
     * GoogleSheetsAPISample constructor.
     */
    public function __construct()
    {
        $dotenv = new Dotenv(__dir__);
        $dotenv->load();
        $credentialsPath = getenv('SERVICE_KEY_JSON');
        putenv('GOOGLE_APPLICATION_CREDENTIALS=' . dirname(__FILE__) . '/' . $credentialsPath);
        $this->spreadsheetId = getenv('SPREADSHEET_ID_RECRUIT');
        $client = new Google_Client();
        $client->useApplicationDefaultCredentials();
        $client->addScope(Google_Service_Sheets::SPREADSHEETS);
        $client->setApplicationName('test');
        $this->service = new Google_Service_Sheets($client);
    }
    /**
     * @param string $date
     * @param string $name
     * @param string $ruby
     * @param string $gender
     * @param string $birth_year
     * @param string $birth_month
     * @param string $birth_day
     * @param string $blood_type
     * @param string $phone_number
     * @param string $mail_address
     * @param string $current_job
     * @param string $job_objective_1
     * @param string $job_objective_2
     * @param string $job_objective_3
     * @param string $job_objective_4
     * @param string $job_objective_5
     * @param string $event_experience
     * @param string $height
     * @param string $selfie
     * @param string $remarks
     */
    public function append(string $date, string $name, string $ruby, string $gender, string $birth_year, string $birth_month, string $birth_day, string $blood_type, string $phone_number, string $mail_address, string $current_job, string $job_objective_1, string $job_objective_2, string $job_objective_3, string $job_objective_4, string $job_objective_5, string $event_experience, string $height, string $selfie, string $remarks)
    {
        $value = new Google_Service_Sheets_ValueRange();
        $value->setValues([ 'values' => [ $date, $name, $ruby, $gender, $birth_year, $birth_month, $birth_day, $blood_type, $phone_number, $mail_address, $current_job, $job_objective_1, $job_objective_1, $job_objective_2, $job_objective_3, $$job_objective_4, $job_objective_5, $event_experience, $height, $selfie, $remarks ] ]);
        $response = $this->service->spreadsheets_values->append($this->spreadsheetId, 'シート1!A1', $value, [ 'valueInputOption' => 'USER_ENTERED' ] );
        var_dump($response);
    }
}
// 変数の初期化
$page_flag = 0;
$clean = array();
$error = array();
// サニタイズ
if( !empty($_POST) ) {
	foreach( $_POST as $key => $value ) {
		$clean[$key] = htmlspecialchars( $value, ENT_QUOTES);
	}
}
if( !empty($clean['btn_confirm']) ) {
	$error = validation($clean);
	// ファイルのアップロード
	if( !empty($_FILES['attachment_file']['tmp_name']) ) {
		$upload_res = move_uploaded_file( $_FILES['attachment_file']['tmp_name'], FILE_DIR.$_FILES['attachment_file']['name']);
		if( $upload_res !== true ) {
			$error[] = 'ファイルのアップロードに失敗しました。';
		} else {
			$clean['attachment_file'] = $_FILES['attachment_file']['name'];
		}
	}
	if( empty($error) ) {
		$page_flag = 1;
		// セッションの書き込み
		session_start();
		$_SESSION['page2'] = true;		
	}
} elseif( !empty($clean['btn_submit']) ) {
	session_start();
	if( !empty($_SESSION['page2']) && $_SESSION['page2'] === true ) {
		// セッションの削除
		unset($_SESSION['page2']);
		$page_flag = 2;
		// 変数とタイムゾーンを初期化
//		$header = null;
		$body = null;
		$admin_body = null;
		$auto_reply_subject = null;
		$auto_reply_text = null;
		$admin_reply_subject = null;
		$admin_reply_text = null;
		date_default_timezone_set('Asia/Tokyo');
		
		//日本語の使用宣言
		mb_language("ja");
		mb_internal_encoding("UTF-8");
        
        $dotenv = new Dotenv(__dir__);
        $dotenv->load();
        $address = getenv('ADMIN_EMAIL');
		$header = "MIME-Version: 1.0\n";
		$header = "Content-Type: multipart/mixed;boundary=\"__BOUNDARY__\"\n";
		$header .= "From: NO-X <${address}>\n";
		$header .= "Reply-To: NO-X <${address}>\n";
	
		// 件名を設定
		$auto_reply_subject = 'ご応募ありがとうございます。';
	    $date = date("Y-m-d H:i");
		// 本文を設定
		$auto_reply_text = "この度は、ご応募頂き誠にありがとうございます。\n下記の内容でご応募を受け付けました。\n担当の者より後日ご連絡差し上げます。\n\n";

		$auto_reply_text .= "お問い合わせ日時：" . $date . "\n\n";
		$auto_reply_text .= "氏名：" . $clean['your_name'] . "\n\n";
        $auto_reply_text .= "ふりがな：" . $clean['your_ruby'] . "\n\n";
		if( $clean['gender'] === "male" ) {
			$auto_reply_text .= "性別：男性\n\n";
		} else {
			$auto_reply_text .= "性別：女性\n\n";
		}
        $auto_reply_text .= "生年月日：" . $clean['birth_year'] . "年" . $clean['birth_month'] . "月" . $clean['birth_day'] . "日\n\n";
        $auto_reply_text .= "血液型：" . $clean['blood_type'] . "\n\n";
        $auto_reply_text .= "連絡先電話番号：" . $clean['phone_number'] . "\n\n";
        $auto_reply_text .= "メールアドレス：" . $clean['email'] . "\n\n";		
		if( $clean['current_job'] === "1" ){
			$auto_reply_text .= "現在の職業：パート・アルバイト\n\n";
		} elseif ( $clean['current_job'] === "2" ){
			$auto_reply_text .= "現在の職業：大学生\n\n";
		} elseif ( $clean['current_job'] === "3" ){
			$auto_reply_text .= "現在の職業：短大生\n\n";
		} elseif ( $clean['current_job'] === "4" ){
			$auto_reply_text .= "現在の職業：専門学生\n\n";
		} elseif( $clean['current_job'] === "5" ){
			$auto_reply_text .= "現在の職業：高校生\n\n";
		} elseif( $clean['current_job'] === "6" ){
			$auto_reply_text .= "現在の職業：会社員\n\n";
		} elseif( $clean['current_job'] === "7" ){
			$auto_reply_text .= "現在の職業：自営業\n\n";
		} elseif( $clean['current_job'] === "8" ){
			$auto_reply_text .= "現在の職業：主婦\n\n";
		} elseif( $clean['current_job'] === "9" ){
			$auto_reply_text .= "現在の職業：就職活動中\n\n";
		} elseif( $clean['current_job'] === "10" ){
			$auto_reply_text .= "現在の職業：その他\n\n";
		}
        $auto_reply_text .= "希望職種：";
        if( $clean['job_objective_1'] === "companion"){ $auto_reply_text .= 'コンパニオン  '; }
        if( $clean['job_objective_2'] === "narrator"){ $auto_reply_text .= 'ナレーター  '; }
        if( $clean['job_objective_3'] === "mc"){ $auto_reply_text .= 'MC  '; }
        if( $clean['job_objective_4'] === "model"){ $auto_reply_text .= 'モデル  '; }
        if( $clean['job_objective_5'] === "ad"){ $auto_reply_text .= 'AD,スタッフ'; }
        $auto_reply_text .= "\n\n";
            
        if( $clean['event_experience'] === "no" ){
			$auto_reply_text .= "イベント経験：なし\n\n";
		} else {
			$auto_reply_text .= "イベント経験：あり\n\n";
		} 
        $auto_reply_text .= "身長：" . $clean['height'] . "\n\n";
        $auto_reply_text .= "備考：" . nl2br($clean['remarks']) . "\n\n";

		$auto_reply_text .= "株式会社ノックス";
		
		// テキストメッセージをセット
//		$body = "--__BOUNDARY__\n";
//		$body .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\n\n";
		$body = $auto_reply_text . "\n";
//		$body .= "--__BOUNDARY__\n";
	
		// ファイルを添付
//		if( !empty($clean['attachment_file']) ) {
//			$body .= "Content-Type: application/octet-stream; name=\"{$clean['attachment_file']}\"\n";
//			$body .= "Content-Disposition: attachment; filename=\"{$clean['attachment_file']}\"\n";
//			$body .= "Content-Transfer-Encoding: base64\n";
//			$body .= "\n";
//			$body .= chunk_split(base64_encode(file_get_contents(FILE_DIR.$clean['attachment_file'])));
//			$body .= "--__BOUNDARY__\n";
//		}
//	
		// 自動返信メール送信
        mb_send_mail( $clean['email'], $auto_reply_subject, $body, $header);
//        $from = new SendGrid\Email(null, $clean['email']);
//        $to = new SendGrid\Email(null, $address);
//        $content = new SendGrid\Content("text/plain", $body);
//		$mail = new SendGrid\Mail( $from, $auto_reply_subject, $to, $content);
//        $apiKey = getenv('SENDGRID_API_KEY');
//        $sg = new \SendGrid($apiKey);
//        $response = $sg->client->mail()->send()->post($mail);
//        echo $response->statusCode();
//        echo $response->headers();
//        echo $response->body();
        
        
        
		// 運営側へ送るメールの件名
		$admin_reply_subject = "【ノックスHP】より応募を受け付けました";
	
		// 本文を設定
		$admin_reply_text = "下記の内容で応募がありました。\n\n";
		$admin_reply_text .= "お問い合わせ日時：" . $date . "\n\n";
		$admin_reply_text .= "氏名：" . $clean['your_name'] . "\n\n";
        $admin_reply_text .= "ふりがな：" . $clean['your_ruby'] . "\n\n";
        $gender = null;
		if( $clean['gender'] === "male" ) {
			$admin_reply_text .= "性別：男性\n\n";
            $gender = "男性";
		} else {
			$admin_reply_text .= "性別：女性\n\n";
            $gender = "女性";
		}
        
        $admin_reply_text .= "生年月日：" . $clean['birth_year'] . "年" . $clean['birth_month'] . "月" . $clean['birth_day'] . "日\n\n";
        
        $admin_reply_text .= "血液型：" . $clean['blood_type'] . "\n\n";
        $admin_reply_text .= "連絡先電話番号：" . $clean['phone_number'] . "\n\n";
        
        $admin_reply_text .= "メールアドレス：" . $clean['email'] . "\n\n";		
        $current_job = null;
		if( $clean['current_job'] === "1" ){
			$admin_reply_text .= "現在の職業：パート・アルバイト\n\n";
            $curret_job = "パート・アルバイト";
		} elseif ( $clean['current_job'] === "2" ){
			$admin_reply_text .= "現在の職業：大学生\n\n";
            $curret_job = "大学生";
		} elseif ( $clean['current_job'] === "3" ){
			$admin_reply_text .= "現在の職業：短大生\n\n";
            $curret_job = "短大生";
		} elseif ( $clean['current_job'] === "4" ){
			$admin_reply_text .= "現在の職業：専門学生\n\n";
            $curret_job = "専門学生";
		} elseif( $clean['current_job'] === "5" ){
			$admin_reply_text .= "現在の職業：高校生\n\n";
            $curret_job = "高校生";
		} elseif( $clean['current_job'] === "6" ){
			$admin_reply_text .= "現在の職業：会社員\n\n";
            $curret_job = "会社員";
		} elseif( $clean['current_job'] === "7" ){
			$admin_reply_text .= "現在の職業：自営業\n\n";
            $curret_job = "自営業";
		} elseif( $clean['current_job'] === "8" ){
			$admin_reply_text .= "現在の職業：主婦\n\n";
            $curret_job = "主婦";
		} elseif( $clean['current_job'] === "9" ){
			$admin_reply_text .= "現在の職業：就職活動中\n\n";
            $curret_job = "就職活動中";
		} elseif( $clean['current_job'] === "10" ){
			$admin_reply_text .= "現在の職業：その他\n\n";
            $curret_job = "その他";
		}
        
        $objective1 = "希望なし";
        $objective2 = "希望なし";
        $objective3 = "希望なし";
        $objective4 = "希望なし";
        $objective5 = "希望なし";
        
        $admin_reply_text .= "希望職種：";
        if( $clean['job_objective_1'] === "companion"){ 
            $admin_reply_text .= 'コンパニオン  ';
            $objective1 = '希望';
        }
        if( $clean['job_objective_2'] === "narrator"){
            $admin_reply_text .= 'ナレーター  ';
            $objective2 = '希望';
        }
        if( $clean['job_objective_3'] === "mc"){
            $admin_reply_text .= 'MC  ';
            $objective3 = '希望';
        }
        if( $clean['job_objective_4'] === "model"){
            $admin_reply_text .= 'モデル  ';
            $objective4 = '希望';
        }
        if( $clean['job_objective_5'] === "ad"){
            $admin_reply_text .= 'AD,スタッフ';
            $objective5 = '希望';
        }
        $admin_reply_text .= "\n\n";
        
        $event = null;
        if( $clean['event_experience'] === "no" ){
			$admin_reply_text .= "イベント経験：なし\n\n";
            $event = 'なし';
		} else {
			$admin_reply_text .= "イベント経験：あり\n\n";
            $event = 'あり';
		} 
        $admin_reply_text .= "身長：" . $clean['height'] . "cm\n\n";
        $admin_reply_text .= "備考：" . nl2br($clean['remarks']) . "\n\n";
		
		// テキストメッセージをセット
		$body = "--__BOUNDARY__\n";
		$body .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\n\n";
		$body = $admin_reply_text . "\n";
		$body .= "--__BOUNDARY__\n";
	    $selfie = "なし";
		// ファイルを添付
		if( !empty($clean['attachment_file']) ) {
            $selfie = "あり";
			$body .= "Content-Type: application/octet-stream; name=\"{$clean['attachment_file']}\"\n";
			$body .= "Content-Disposition: attachment; filename=\"{$clean['attachment_file']}\"\n";
			$body .= "Content-Transfer-Encoding: base64\n";
			$body .= "\n";
			$body .= chunk_split(base64_encode(file_get_contents(FILE_DIR.$clean['attachment_file'])));
			$body .= "--__BOUNDARY__\n";
		}
	    $admin_reply_text .= "画像：" . $selfie;
		// 管理者へメール送信
        mb_send_mail( 'awaya.android@gmail.com', $admin_reply_subject, $body, $header);
//        $from_admin = new SendGrid\Email(null, $address);
//        $to_admin = new SendGrid\Email(null, $address);
//        $content = new SendGrid\Content("text/plain", $body);
//        $mail = new SendGrid\Mail( $from_admin, $admin_reply_subject, $to_admin, $content);
//        $response = $sg->client->mail()->send()->post($mail);
//        echo $response->statusCode();
//        echo $response->headers();
//        echo $response->body();
        
        // GoogleSpreadSheetに書き込み
		$customer_data = new GoogleSheetsAPISample;
        $customer_data->append( $date, $clean['your_name'], $clean['your_ruby'], $gender, $clean['birth_year'], $clean['birth_month'], $clean['birth_day'], $clean['blood_type'], $clean['phone_number'], $clean['email'], $current_job, $objective1, $objective2, $objective3, $objective4, $objective5, $event, $clean['height'], $selfie, $clean['remarks']);
	} else {
		$page_flag = 0;
	}	
}

function validation($data) {
	$error = array();
	// 氏名のバリデーション
	if( empty($data['your_name']) ) {
		$error[] = "「氏名」は必ず入力してください。";
	} elseif( 20 < mb_strlen($data['your_name']) ) {
		$error[] = "「氏名」は20文字以内で入力してください。";
	}
    // ルビのバリデーション
    if( empty( $data['your_ruby']) ) {
        $error[] = "「ふりがな」は必ず入力してください。";
    } 

	// 性別のバリデーション
	if( empty($data['gender']) ) {
		$error[] = "「性別」は必ず入力してください。";
	} elseif( $data['gender'] !== 'male' && $data['gender'] !== 'female' ) {
		$error[] = "「性別」は必ず入力してください。";
	}
    
    // 生年月日のバリデーション
    if( empty( $data['birth_year']) ) {
        $error[] = "生年月日（年）を選択してください。";
    }
    if( empty( $data['birth_month']) ) {
        $error[] = "生年月日（月）を選択してください。";
    }
    if( empty( $data['birth_day']) ) {
        $error[] = "生年月日（日）を選択してください。";
    }
            
	// メールアドレスのバリデーション
	if( empty($data['email']) ) {
		$error[] = "「メールアドレス」は必ず入力してください。";
	} elseif( !preg_match( '/^[0-9a-z_.\/?-]+@([0-9a-z-]+\.)+[0-9a-z-]+$/', $data['email']) ) {
		$error[] = "「メールアドレス」は正しい形式で入力してください。";
	}
    
    // 現在の職業
    if( empty($data['current_job']) ) {
		$error[] = "「現在の職業」は必ず選択してください。";
	} 

    // イベント経験
    if( empty($data['event_experience']) ) {
		$error[] = "「イベント経験」は必ず選択してください。";
	} 

	// プライバシーポリシー同意のバリデーション
	if( empty($data['agreement']) ) {
		$error[] = "プライバシーポリシーをご確認ください。";
	} elseif( (int)$data['agreement'] !== 1 ) {
		$error[] = "プライバシーポリシーをご確認ください。";
	}
    
    // 電話番号のバリデーション
    if( !empty($data['phone_number']) && !preg_match( '/^\d{10}$|^\d{11}$/', $data['phone_number'])) {
        $error[] = "「電話番号」をご確認ください。";
    }

	return $error;
}
?>

<!DOCTYPE>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="description" content="no-x">
    <meta name="viewport" content="width=device-width">
    <link rel="apple-touch-icon" sizes="180x180" href="apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="favicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="favicon-16x16.png">
    <link rel="manifest" href="site.webmanifest">
    <link rel="mask-icon" href="safari-pinned-tab.svg" color="#5bbad5">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">
    <title>応募フォーム</title>
    <link rel="stylesheet" media="all" href="css/style.css">
    <style rel="stylesheet" type="text/css">
        .container {
            padding: 20px;
            text-align: center;
        }

        h1 {
            margin-bottom: 20px;
            padding: 20px 0;
            color: #4EB374;
            font-size: 122%;
            border-top: 1px solid #999;
            border-bottom: 1px solid #999;
        }

        input[type=text] {
            padding: 5px 10px;
            font-size: 86%;
            border: none;
            border-radius: 3px;
            background: #ddf0ff;
        }

        input[name=btn_confirm],
        input[name=btn_submit],
        input[name=btn_back] {
            margin-top: 10px;
            padding: 5px 20px;
            font-size: 100%;
            color: #fff;
            cursor: pointer;
            border: none;
            border-radius: 3px;
            box-shadow: 0 3px 0 #2887d1;
            background: #4eaaf1;
        }

        input[name=btn_back] {
            margin-right: 20px;
            box-shadow: 0 3px 0 #777;
            background: #999;
        }

        .element_wrap {
            margin-bottom: 10px;
            padding: 10px 0;
            border-bottom: 1px solid #ccc;
            text-align: left;
        }

        label {
            display: inline-block;
            margin-bottom: 10px;
            font-weight: bold;
            width: 150px;
            vertical-align: top;
        }

        .element_wrap p {
            display: inline-block;
            margin: 0;
            text-align: left;
        }

        label[for=gender_male],
        label[for=gender_female],
        label[for=agreement] {
            margin-right: 10px;
            width: auto;
            font-weight: normal;
        }

        textarea[name=remarks] {
            padding: 5px 10px;
            width: 60%;
            height: 100px;
            font-size: 86%;
            border: none;
            border-radius: 3px;
            background: #ddf0ff;
        }

        .error_list {
            padding: 10px 30px;
            color: #ff2e5a;
            font-size: 86%;
            text-align: left;
            border: 1px solid #ff2e5a;
            border-radius: 5px;
        }

        @media only screen and (min-width: 800px) {
            #sidebar h2 {
                padding: 30px 0;
            }
        }

        #privacy {
            padding: 10px 30px;
            color: #000;
            font-size: 86%;
            text-align: left;
            border: 1px solid #000;
            border-radius: 5px;
            margin: 20px 0;
        }

        #privacy h3 {
            text-align: center;
            font-weight: none;
        }

        #birth {
            display:flex;
        }
        
        .ib {
            margin-left: 30px;
        }
    </style>
</head>

<body id="top">
    <div id="wrapper">

        <div id="sidebar">
            <div id="sidebarWrap">
                <h2><img src="images/logo.jpg" width="100%" height="100%" alt="logo"></h2>
                <nav id="mainnav">
                    <p id="menuWrap"><a id="menu"><span id="menuBtn"></span></a></p>
                    <div class="panel">
                        <ul>
                            <li><a href="index.html#top">TOP</a></li>
                            <li><a href="index.html#sec01">MESSAGE</a></li>
                            <li><a href="index.html#sec02">WORKS</a></li>
                            <li><a href="index.html#sec03">COMPANY</a></li>
                            <li><a href="contact.php">CONTACT</a></li>
                            <li><a href="recruit.php">女の子募集</a></li>

                        </ul>
                        <ul id="sns">
                            <!--
							<li><a href="#" target="_blank"><img src="images/iconFb.png" width="20" height="20" alt="FB"></a></li>
							<li><a href="#" target="_blank"><img src="images/iconTw.png" width="20" height="20" alt="twitter"></a></li>
							<li><a href="#" target="_blank"><img src="images/iconInsta.png" width="20" height="20" alt="Instagram"></a></li>
							<li><a href="#" target="_blank"><img src="images/iconYouTube.png" width="20" height="20" alt="You Tube"></a></li>
-->
                        </ul>
                    </div>
                </nav>
            </div>
        </div>
    </div>
    <div id="content" class="container">
        <h1>応募フォーム</h1>
        <?php if( $page_flag === 1 ): ?>

        <form method="post" action="">
            <div class="element_wrap">
                <label>氏名</label>
                <p>
                    <?php echo $clean['your_name']; ?>
                </p>
            </div>
            <div class="element_wrap">
                <label>ふりがな</label>
                <p>
                    <?php echo $clean['your_ruby']; ?>
                </p>
            </div>
            <div class="element_wrap">
                <label>性別</label>
                <p>
                    <?php if( $clean['gender'] === "male" ){ echo '男性'; }else{ echo '女性'; } ?>
                </p>
            </div>
            <div class="element_wrap">
                <label>生年月日</label>
                <p>
                    <?php  echo $clean['birth_year']; ?>月 <?php  echo $clean['birth_month']; ?>月 <?php  echo $clean['birth_day']; ?>日
                </p>
            </div>
            <div class="element_wrap">
                <label>血液型</label>
                <p>
                    <?php if( $clean['blood_type'] === "1" ){ echo 'A型'; }
                    elseif( $clean['blood_type'] === "2" ){ echo 'B型'; }
                    elseif( $clean['blood_type'] === "3" ){ echo 'O型'; }
                    elseif( $clean['blood_type'] === "4" ){ echo 'AB型'; }
                    elseif( $clean['blood_type'] === "5" ){ echo '不明'; } ?>
                </p>
            </div>
            <div class="element_wrap">
                <label>連絡先電話番号（ハイフンなし）</label>
                <p>
                    <?php echo $clean['phone_number']; ?>
                </p>
            </div>
            <div class="element_wrap">
                <label>メールアドレス</label>
                <p>
                    <?php echo $clean['email']; ?>
                </p>
            </div>
            <div class="element_wrap">
                <label>現在の職業</label>
                <p>
                    <?php if( $clean['current_job'] === "1" ){ echo 'パート・アルバイト'; }
                    elseif( $clean['current_job'] === "2" ){ echo '大学生'; }
                    elseif( $clean['current_job'] === "3" ){ echo '短大生'; }
                    elseif( $clean['current_job'] === "4" ){ echo '専門学生'; }
                    elseif( $clean['current_job'] === "5" ){ echo '高校生'; }
                    elseif( $clean['current_job'] === "6" ){ echo '会社員'; }
                    elseif( $clean['current_job'] === "7" ){ echo '自営業'; }
                    elseif( $clean['current_job'] === "8" ){ echo '主婦'; } 
                    elseif( $clean['current_job'] === "9" ){ echo '就職活動中'; } 
                    elseif( $clean['current_job'] === "10" ){ echo 'その他'; }  ?>
                </p>
            </div>
            <div class="element_wrap">
                <label>希望職種（複数回答可）</label>
                <p>
                    <?php if( $clean['job_objective_1'] === "companion"){ echo 'コンパニオン  '; }
                    if( $clean['job_objective_2'] === "narrator"){ echo 'ナレーター  '; }
                    if( $clean['job_objective_3'] === "mc"){ echo 'MC  '; }
                    if( $clean['job_objective_4'] === "model"){ echo 'モデル  '; }
                    if( $clean['job_objective_5'] === "ad"){ echo 'AD,スタッフ'; } ?>
                </p>
            </div>
            <div class="element_wrap">
                <label>イベント経験</label>
                <p>
                    <?php if( $clean['event_experience'] === "no" ){ echo 'なし'; }else{ echo 'あり'; } ?>
                </p>
            </div>
            <div class="element_wrap">
                <label>身長</label>
                <p>
                    <?php echo $clean['height']; ?>cm
                </p>
            </div>
            <div class="element_wrap">
                <label>備考</label>
                <p>
                    <?php echo nl2br($clean['remarks']); ?>
                </p>
            </div>

            <?php if( !empty($clean['attachment_file']) ): ?>
            <div class="element_wrap">
                <label>ご自身の画像ファイルの添付（任意）</label>
                <p><img src="<?php echo FILE_DIR.$clean['attachment_file']; ?>"></p>
            </div>
            <?php endif; ?>

            <div class="element_wrap">
                <label>プライバシーポリシーに同意する</label>
                <p>
                    <?php if( $clean['agreement'] === "1" ){ echo '同意する'; }else{ echo '同意しない'; } ?>
                </p>
            </div>
            <input type="submit" name="btn_back" value="戻る">
            <input type="submit" name="btn_submit" value="送信">
            <input type="hidden" name="your_name" value="<?php echo $clean['your_name']; ?>">
            <input type="hidden" name="your_ruby" value="<?php echo $clean['your_ruby']; ?>">
            <input type="hidden" name="gender" value="<?php echo $clean['gender']; ?>">
            <input type="hidden" name="birth_year" value="<?php echo $clean['birth_year']; ?>">
            <input type="hidden" name="birth_month" value="<?php echo $clean['birth_month']; ?>">
            <input type="hidden" name="birth_day" value="<?php echo $clean['birth_day']; ?>">
            <?php if( !empty($clean['blood_type']) ): ?>
            <input type="hidden" name="blood_type" value="<?php echo $clean['blood_type']; ?>">
            <?php endif; ?>
            <?php if( !empty($clean['phone_number']) ): ?>
            <input type="hidden" name="phone_number" value="<?php echo $clean['phone_number']; ?>">
            <?php endif; ?>
            <input type="hidden" name="email" value="<?php echo $clean['email']; ?>">
            <input type="hidden" name="current_job" value="<?php echo $clean['current_job']; ?>">
            <?php if( !empty($clean['job_objective_1']) ): ?>
            <input type="hidden" name="job_objective_1" value="<?php echo $clean['job_objective_1']; ?>">
            <?php endif; ?>
            <?php if( !empty($clean['job_objective_2']) ): ?>
            <input type="hidden" name="job_objective_2" value="<?php echo $clean['job_objective_2']; ?>">
            <?php endif; ?>
            <?php if( !empty($clean['job_objective_3']) ): ?>
            <input type="hidden" name="job_objective_3" value="<?php echo $clean['job_objective_3']; ?>">
            <?php endif; ?>
            <?php if( !empty($clean['job_objective_4']) ): ?>
            <input type="hidden" name="job_objective_4" value="<?php echo $clean['job_objective_4']; ?>">
            <?php endif; ?>
            <?php if( !empty($clean['job_objective_5']) ): ?>
            <input type="hidden" name="job_objective_5" value="<?php echo $clean['job_objective_5']; ?>">
            <?php endif; ?>
            <input type="hidden" name="event_experience" value="<?php echo $clean['event_experience']; ?>">
            <input type="hidden" name="height" value="<?php echo $clean['height']; ?>">
            <?php if( !empty($clean['attachment_file']) ): ?>
            <input type="hidden" name="attachment_file" value="<?php echo $clean['attachment_file']; ?>">
            <?php endif; ?>
            <input type="hidden" name="remarks" value="<?php echo $clean['remarks']; ?>">
            <input type="hidden" name="agreement" value="<?php echo $clean['agreement']; ?>">
        </form>

        <?php elseif( $page_flag === 2 ): ?>

        <p>送信が完了しました。</p>
        <p><a href="index.html">トップに戻る</a></p>

        <?php else: ?>
        <div id="privacy">
            <h3>プライバシーポリシー</h3>
            <p>ご登録いただく前に、必ず下記の「登録情報の取り扱いに関する確認事項」
                をご確認お願い致します。</p>
            <p>登録フォームを送信頂いた時は、以下の確認事項に同意頂いたものとさせて
                頂きます。 </p>
            <br>

            <h3 style="color: red; font-weight: none;">＜ 登録情報の取り扱いに関する確認事項 ＞</h3>
            <p>■個人情報の取得および利用等は、就業の確保を図るものと、
                適切な雇用管理を行います。 </p>
            <p>■法令に基づく場合や、当社ノックスの業務関係以外は、
                取得した個人情報を本人の同意無しに第三者に提供はしません。</p>

            <br>
            <p>上記内容に関するお問い合わせは、下記までお問い合わせ下さい。</p>

            株式会社　ノックス <br>
            TEL 03-6264-8190
        </div>
        <?php if( !empty($error) ): ?>
        <ul class="error_list">
            <?php foreach( $error as $value ): ?>
            <li>
                <?php echo $value; ?>
            </li>
            <?php endforeach; ?>
        </ul>
        <?php endif; ?>
        <form method="post" action="" enctype="multipart/form-data">
            *は必須項目です
            <div class="element_wrap">
                <label>*氏名</label>
                <input type="text" name="your_name" value="<?php if( !empty($clean['your_name']) ){ echo $clean['your_name']; } ?>">
            </div>
            <div class="element_wrap">
                <label>*ふりがな</label>
                <input type="text" name="your_ruby" value="<?php if( !empty($clean['your_ruby']) ){ echo $clean['your_ruby']; } ?>">
            </div>
            <div class="element_wrap">
                <label>*性別</label>
                <label for="gender_female"><input id="gender_female" type="radio" name="gender" value="female" <?php if( !empty($clean['gender']) && $clean['gender']==="female" ){ echo 'checked' ; } ?>>女性</label>
                <label for="gender_male"><input id="gender_male" type="radio" name="gender" value="male" <?php if( !empty($clean['gender']) && $clean['gender']==="male" ){ echo 'checked' ; } ?>>男性</label>
            </div>
            <div class="element_wrap" id="birth">
                <label>*生年月日</label>
                <select name="birth_year">
                    <option value="">-</option>
                    <option value="1970" <?php if( !empty($clean['birth_year']) && $clean['birth_year']==="1970" ){ echo 'selected' ; } ?>>1970</option>
                    <option value="1971" <?php if( !empty($clean['birth_year']) && $clean['birth_year']==="1971" ){ echo 'selected' ; } ?>>1971</option>
                    <option value="1972" <?php if( !empty($clean['birth_year']) && $clean['birth_year']==="1972" ){ echo 'selected' ; } ?>>1972</option>
                    <option value="1973" <?php if( !empty($clean['birth_year']) && $clean['birth_year']==="1973" ){ echo 'selected' ; } ?>>1973</option>
                    <option value="1974" <?php if( !empty($clean['birth_year']) && $clean['birth_year']==="1974" ){ echo 'selected' ; } ?>>1974</option>
                    <option value="1975" <?php if( !empty($clean['birth_year']) && $clean['birth_year']==="1975" ){ echo 'selected' ; } ?>>1975</option>
                    <option value="1976" <?php if( !empty($clean['birth_year']) && $clean['birth_year']==="1976" ){ echo 'selected' ; } ?>>1976</option>
                    <option value="1977" <?php if( !empty($clean['birth_year']) && $clean['birth_year']==="1977" ){ echo 'selected' ; } ?>>1977</option>
                    <option value="1978" <?php if( !empty($clean['birth_year']) && $clean['birth_year']==="1978" ){ echo 'selected' ; } ?>>1978</option>
                    <option value="1979" <?php if( !empty($clean['birth_year']) && $clean['birth_year']==="1979" ){ echo 'selected' ; } ?>>1979</option>
                    <option value="1980" <?php if( !empty($clean['birth_year']) && $clean['birth_year']==="1980" ){ echo 'selected' ; } ?>>1980</option>
                    <option value="1981" <?php if( !empty($clean['birth_year']) && $clean['birth_year']==="1981" ){ echo 'selected' ; } ?>>1981</option>
                    <option value="1982" <?php if( !empty($clean['birth_year']) && $clean['birth_year']==="1982" ){ echo 'selected' ; } ?>>1982</option>
                    <option value="1983" <?php if( !empty($clean['birth_year']) && $clean['birth_year']==="1983" ){ echo 'selected' ; } ?>>1983</option>
                    <option value="1984" <?php if( !empty($clean['birth_year']) && $clean['birth_year']==="1984" ){ echo 'selected' ; } ?>>1984</option>
                    <option value="1985" <?php if( !empty($clean['birth_year']) && $clean['birth_year']==="1985" ){ echo 'selected' ; } ?>>1985</option>
                    <option value="1986" <?php if( !empty($clean['birth_year']) && $clean['birth_year']==="1986" ){ echo 'selected' ; } ?>>1986</option>
                    <option value="1987" <?php if( !empty($clean['birth_year']) && $clean['birth_year']==="1987" ){ echo 'selected' ; } ?>>1987</option>
                    <option value="1988" <?php if( !empty($clean['birth_year']) && $clean['birth_year']==="1988" ){ echo 'selected' ; } ?>>1988</option>
                    <option value="1989" <?php if( !empty($clean['birth_year']) && $clean['birth_year']==="1989" ){ echo 'selected' ; } ?>>1989</option>
                    <option value="1990" <?php if( !empty($clean['birth_year']) && $clean['birth_year']==="1990" ){ echo 'selected' ; } ?>>1990</option>
                    <option value="1991" <?php if( !empty($clean['birth_year']) && $clean['birth_year']==="1991" ){ echo 'selected' ; } ?>>1991</option>
                    <option value="1992" <?php if( !empty($clean['birth_year']) && $clean['birth_year']==="1992" ){ echo 'selected' ; } ?>>1992</option>
                    <option value="1993" <?php if( !empty($clean['birth_year']) && $clean['birth_year']==="1993" ){ echo 'selected' ; } ?>>1993</option>
                    <option value="1994" <?php if( !empty($clean['birth_year']) && $clean['birth_year']==="1994" ){ echo 'selected' ; } ?>>1994</option>
                    <option value="1995" <?php if( !empty($clean['birth_year']) && $clean['birth_year']==="1995" ){ echo 'selected' ; } ?>>1995</option>
                    <option value="1996" <?php if( !empty($clean['birth_year']) && $clean['birth_year']==="1996" ){ echo 'selected' ; } ?>>1996</option>
                    <option value="1997" <?php if( !empty($clean['birth_year']) && $clean['birth_year']==="1997" ){ echo 'selected' ; } ?>>1997</option>
                    <option value="1998" <?php if( !empty($clean['birth_year']) && $clean['birth_year']==="1998" ){ echo 'selected' ; } ?>>1998</option>
                    <option value="1999" <?php if( !empty($clean['birth_year']) && $clean['birth_year']==="1999" ){ echo 'selected' ; } ?>>1999</option>
                    <option value="2000" <?php if( !empty($clean['birth_year']) && $clean['birth_year']==="2000" ){ echo 'selected' ; } ?>>2000</option>
                    <option value="2001" <?php if( !empty($clean['birth_year']) && $clean['birth_year']==="2001" ){ echo 'selected' ; } ?>>2001</option>
                    <option value="2002" <?php if( !empty($clean['birth_year']) && $clean['birth_year']==="2002" ){ echo 'selected' ; } ?>>2002</option>
                    <option value="2003" <?php if( !empty($clean['birth_year']) && $clean['birth_year']==="2003" ){ echo 'selected' ; } ?>>2003</option>
                    <option value="2004" <?php if( !empty($clean['birth_year']) && $clean['birth_year']==="2004" ){ echo 'selected' ; } ?>>2004</option>
                    <option value="2005" <?php if( !empty($clean['birth_year']) && $clean['birth_year']==="2005" ){ echo 'selected' ; } ?>>2005</option>
                </select>　年
                <br>
                <select name="birth_month">
                    <option value="">-</option>
                    <option value="1" <?php if( !empty($clean['birth_month']) && $clean['birth_month']==="1" ){ echo 'selected' ; } ?>>1</option>
                    <option value="2" <?php if( !empty($clean['birth_month']) && $clean['birth_month']==="2" ){ echo 'selected' ; } ?>>2</option>
                    <option value="3" <?php if( !empty($clean['birth_month']) && $clean['birth_month']==="3" ){ echo 'selected' ; } ?>>3</option>
                    <option value="4" <?php if( !empty($clean['birth_month']) && $clean['birth_month']==="4" ){ echo 'selected' ; } ?>>4</option>
                    <option value="5" <?php if( !empty($clean['birth_month']) && $clean['birth_month']==="5" ){ echo 'selected' ; } ?>>5</option>
                    <option value="6" <?php if( !empty($clean['birth_month']) && $clean['birth_month']==="6" ){ echo 'selected' ; } ?>>6</option>
                    <option value="7" <?php if( !empty($clean['birth_month']) && $clean['birth_month']==="7" ){ echo 'selected' ; } ?>>7</option>
                    <option value="8" <?php if( !empty($clean['birth_month']) && $clean['birth_month']==="8" ){ echo 'selected' ; } ?>>8</option>
                    <option value="9" <?php if( !empty($clean['birth_month']) && $clean['birth_month']==="9" ){ echo 'selected' ; } ?>>9</option>
                    <option value="10" <?php if( !empty($clean['birth_month']) && $clean['birth_month']==="10" ){ echo 'selected' ; } ?>>10</option>
                    <option value="11" <?php if( !empty($clean['birth_month']) && $clean['birth_month']==="11" ){ echo 'selected' ; } ?>>11</option>
                    <option value="12" <?php if( !empty($clean['birth_month']) && $clean['birth_month']==="12" ){ echo 'selected' ; } ?>>12</option>
                </select>　月
                <br>
                <select name="birth_day">
                    <option value="">-</option>
                    <option value="1" <?php if( !empty($clean['birth_day']) && $clean['birth_day']==="1" ){ echo 'selected' ; } ?>>1</option>
                    <option value="2" <?php if( !empty($clean['birth_day']) && $clean['birth_day']==="2" ){ echo 'selected' ; } ?>>2</option>
                    <option value="3" <?php if( !empty($clean['birth_day']) && $clean['birth_day']==="3" ){ echo 'selected' ; } ?>>3</option>
                    <option value="4" <?php if( !empty($clean['birth_day']) && $clean['birth_day']==="4" ){ echo 'selected' ; } ?>>4</option>
                    <option value="5" <?php if( !empty($clean['birth_day']) && $clean['birth_day']==="5" ){ echo 'selected' ; } ?>>5</option>
                    <option value="6" <?php if( !empty($clean['birth_day']) && $clean['birth_day']==="6" ){ echo 'selected' ; } ?>>6</option>
                    <option value="7" <?php if( !empty($clean['birth_day']) && $clean['birth_day']==="7" ){ echo 'selected' ; } ?>>7</option>
                    <option value="8" <?php if( !empty($clean['birth_day']) && $clean['birth_day']==="8" ){ echo 'selected' ; } ?>>8</option>
                    <option value="9" <?php if( !empty($clean['birth_day']) && $clean['birth_day']==="9" ){ echo 'selected' ; } ?>>9</option>
                    <option value="10" <?php if( !empty($clean['birth_day']) && $clean['birth_day']==="10" ){ echo 'selected' ; } ?>>10</option>
                    <option value="11" <?php if( !empty($clean['birth_day']) && $clean['birth_day']==="11" ){ echo 'selected' ; } ?>>11</option>
                    <option value="12" <?php if( !empty($clean['birth_day']) && $clean['birth_day']==="12" ){ echo 'selected' ; } ?>>12</option>
                    <option value="13" <?php if( !empty($clean['birth_day']) && $clean['birth_day']==="13" ){ echo 'selected' ; } ?>>13</option>
                    <option value="14" <?php if( !empty($clean['birth_day']) && $clean['birth_day']==="14" ){ echo 'selected' ; } ?>>14</option>
                    <option value="15" <?php if( !empty($clean['birth_day']) && $clean['birth_day']==="15" ){ echo 'selected' ; } ?>>15</option>
                    <option value="16" <?php if( !empty($clean['birth_day']) && $clean['birth_day']==="16" ){ echo 'selected' ; } ?>>16</option>
                    <option value="17" <?php if( !empty($clean['birth_day']) && $clean['birth_day']==="17" ){ echo 'selected' ; } ?>>17</option>
                    <option value="18" <?php if( !empty($clean['birth_day']) && $clean['birth_day']==="18" ){ echo 'selected' ; } ?>>18</option>
                    <option value="19" <?php if( !empty($clean['birth_day']) && $clean['birth_day']==="19" ){ echo 'selected' ; } ?>>19</option>
                    <option value="20" <?php if( !empty($clean['birth_day']) && $clean['birth_day']==="20" ){ echo 'selected' ; } ?>>20</option>
                    <option value="21" <?php if( !empty($clean['birth_day']) && $clean['birth_day']==="21" ){ echo 'selected' ; } ?>>21</option>
                    <option value="22" <?php if( !empty($clean['birth_day']) && $clean['birth_day']==="22" ){ echo 'selected' ; } ?>>22</option>
                    <option value="23" <?php if( !empty($clean['birth_day']) && $clean['birth_day']==="23" ){ echo 'selected' ; } ?>>23</option>
                    <option value="24" <?php if( !empty($clean['birth_day']) && $clean['birth_day']==="24" ){ echo 'selected' ; } ?>>24</option>
                    <option value="25" <?php if( !empty($clean['birth_day']) && $clean['birth_day']==="25" ){ echo 'selected' ; } ?>>25</option>
                    <option value="26" <?php if( !empty($clean['birth_day']) && $clean['birth_day']==="26" ){ echo 'selected' ; } ?>>26</option>
                    <option value="27" <?php if( !empty($clean['birth_day']) && $clean['birth_day']==="27" ){ echo 'selected' ; } ?>>27</option>
                    <option value="28" <?php if( !empty($clean['birth_day']) && $clean['birth_day']==="28" ){ echo 'selected' ; } ?>>28</option>
                    <option value="29" <?php if( !empty($clean['birth_day']) && $clean['birth_day']==="29" ){ echo 'selected' ; } ?>>29</option>
                    <option value="30" <?php if( !empty($clean['birth_day']) && $clean['birth_day']==="30" ){ echo 'selected' ; } ?>>30</option>
                    <option value="31" <?php if( !empty($clean['birth_day']) && $clean['birth_day']==="31" ){ echo 'selected' ; } ?>>31</option>
                </select>　日
            </div>
            <div class="element_wrap">
                <label>血液型</label>
                <select name="blood_type">
                    <option value="">-</option>
                    <option value="1" <?php if( !empty($clean['blood_type']) && $clean['blood_type']==="1" ){ echo 'selected' ; } ?>>A型</option>
                    <option value="2" <?php if( !empty($clean['blood_type']) && $clean['blood_type']==="2" ){ echo 'selected' ; } ?>>B型</option>
                    <option value="3" <?php if( !empty($clean['blood_type']) && $clean['blood_type']==="3" ){ echo 'selected' ; } ?>>O型</option>
                    <option value="4" <?php if( !empty($clean['blood_type']) && $clean['blood_type']==="4" ){ echo 'selected' ; } ?>>AB型</option>
                    <option value="5" <?php if( !empty($clean['blood_type']) && $clean['blood_type']==="5" ){ echo 'selected' ; } ?>>不明</option>
                </select>
            </div>
            <div class="element_wrap">
                <label>連絡先電話番号（ハイフンなし）</label>
                <input type="number" name="phone_number" value="<?php if( !empty($clean['phone_number']) ){ echo $clean['phone_number']; } ?>">
            </div>
            <div class="element_wrap">
                <label>*メールアドレス</label>
                <input type="text" name="email" value="<?php if( !empty($clean['email']) ){ echo $clean['email']; } ?>">
            </div>
            
            <div class="element_wrap">
                <label>*現在の職業</label>
                <select name="current_job">
                    <option value="">選択してください</option>
                    <option value="1" <?php if( !empty($clean['current_job']) && $clean['current_job']==="1" ){ echo 'selected' ; } ?>>パート・アルバイト</option>
                    <option value="2" <?php if( !empty($clean['current_job']) && $clean['current_job']==="2" ){ echo 'selected' ; } ?>>大学生</option>
                    <option value="3" <?php if( !empty($clean['current_job']) && $clean['current_job']==="3" ){ echo 'selected' ; } ?>>短大生</option>
                    <option value="4" <?php if( !empty($clean['current_job']) && $clean['current_job']==="4" ){ echo 'selected' ; } ?>>専門学生</option>
                    <option value="5" <?php if( !empty($clean['current_job']) && $clean['current_job']==="5" ){ echo 'selected' ; } ?>>高校生</option>
                    <option value="6" <?php if( !empty($clean['current_job']) && $clean['current_job']==="6" ){ echo 'selected' ; } ?>>会社員</option>
                    <option value="7" <?php if( !empty($clean['current_job']) && $clean['current_job']==="7" ){ echo 'selected' ; } ?>>自営業</option>
                    <option value="8" <?php if( !empty($clean['current_job']) && $clean['current_job']==="8" ){ echo 'selected' ; } ?>>主婦</option>
                    <option value="9" <?php if( !empty($clean['current_job']) && $clean['current_job']==="9" ){ echo 'selected' ; } ?>>就職活動中</option>
                    <option value="10" <?php if( !empty($clean['current_job']) && $clean['current_job']==="10" ){ echo 'selected' ; } ?>>その他</option>
                </select>
            </div>
            
            <div class="element_wrap">
                <label>希望職種（複数回答可）</label>
                <input type="checkbox" class="ib" name="job_objective_1" value="companion" <?php if( !empty ($clean['job_objective_1']) && $clean['job_objective_1'] === "companion"){ echo 'checked' ; } ?>>コンパニオン
                <input type="checkbox" class="ib" name="job_objective_2" value="narrator" <?php if( !empty ($clean['job_objective_2']) && $clean['job_objective_2'] === "narrator"){ echo 'checked' ; } ?>>ナレーター
                <input type="checkbox" class="ib" name="job_objective_3" value="mc" <?php if( !empty ($clean['job_objective_3']) && $clean['job_objective_3'] === "mc"){ echo 'checked' ; } ?>>MC
                <input type="checkbox" class="ib" name="job_objective_4" value="model" <?php if( !empty ($clean['job_objective_4']) && $clean['job_objective_4'] === "model"){ echo 'checked' ; } ?>>モデル
                <input type="checkbox" class="ib" name="job_objective_5" value="ad" <?php if( !empty ($clean['job_objective_5']) && $clean['job_objective_5'] === "ad"){ echo 'checked' ; } ?>>AD,スタッフ
            </div>
            
            <div class="element_wrap">
                <label>*イベント経験</label>
                <label for="event_experience"><input id="event_experience" type="radio" name="event_experience" value="experience" <?php if( !empty($clean['event_experience']) && $clean['event_experience']==="experience" ){ echo 'checked' ; } ?>>あり</label>
                <label for="no_event_experience"><input id="no_event_experience" type="radio" name="event_experience" value="no" <?php if( !empty($clean['event_experience']) && $clean['event_experience']==="no" ){ echo 'checked' ; } ?>>なし</label>
            </div>
            
            <div class="element_wrap">
                <label>身長</label>
                <input type="number" step="0.1" name="height" value="<?php if( !empty($clean['height']) ){ echo $clean['height']; } ?>">cm
            </div>
            <div class="element_wrap">
                <label>備考</label>
                <textarea name="remarks"><?php if( !empty($clean['remarks']) ){ echo $clean['remarks']; } ?></textarea>
            </div>
            
            <div class="element_wrap">
                <label>ご自身の画像ファイルの添付（任意）</label>
                <input type="file" name="attachment_file">
            </div>

            <div class="element_wrap">
                <label for="agreement"><input id="agreement" type="checkbox" name="agreement" value="1" <?php if( !empty($clean['agreement']) && $clean['agreement']==="1" ){ echo 'checked' ; } ?>><a href="#privacy">プライバシーポリシー</a>に同意する</label>
            </div>
            <input type="submit" name="btn_confirm" value="入力内容を確認する">

        </form>


        <?php endif; ?>

    </div>
</body>

</html>
