<?php
//define( "FILE_DIR", "images/test/");
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
        $this->spreadsheetId = getenv('SPREADSHEET_ID_CONTACT');
        $client = new Google_Client();
        $client->useApplicationDefaultCredentials();
        $client->addScope(Google_Service_Sheets::SPREADSHEETS);
        $client->setApplicationName('test');
        $this->service = new Google_Service_Sheets($client);
    }
    /**
     * @param string $date
     * @param string $name
     * @param string $email
     * @param string $gender
     * @param string $age
     * @param string $content
     */
    public function append(string $date, string $name, string $email, string $gender, string $age, string $content)
    {
        $value = new Google_Service_Sheets_ValueRange();
        $value->setValues([ 'values' => [ $date, $name, $email, $gender, $age, $content ] ]);
        $response = $this->service->spreadsheets_values->append($this->spreadsheetId, 'シート1!A1', $value, [ 'valueInputOption' => 'USER_ENTERED' ] );
//        var_dump($response);
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
//	if( !empty($_FILES['attachment_file']['tmp_name']) ) {
//		$upload_res = move_uploaded_file( $_FILES['attachment_file']['tmp_name'], FILE_DIR.$_FILES['attachment_file']['name']);
//		if( $upload_res !== true ) {
//			$error[] = 'ファイルのアップロードに失敗しました。';
//		} else {
//			$clean['attachment_file'] = $_FILES['attachment_file']['name'];
//		}
//	}
	if( empty($error) ) {
		$page_flag = 1;
		// セッションの書き込み
		session_start();
		$_SESSION['page'] = true;		
	}
} elseif( !empty($clean['btn_submit']) ) {
	session_start();
	if( !empty($_SESSION['page']) && $_SESSION['page'] === true ) {
		// セッションの削除
		unset($_SESSION['page']);
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
//		$header = "Content-Type: multipart/mixed;boundary=\"__BOUNDARY__\"\n";
		$header .= "From: NO-X <${address}>\n";
		$header .= "Reply-To: NO-X <${address}>\n";
	
		// 件名を設定
		$auto_reply_subject = 'お問い合わせありがとうございます。';
	    $date = date("Y-m-d H:i");
		// 本文を設定
		$auto_reply_text = "この度は、お問い合わせ頂き誠にありがとうございます。
	下記の内容でお問い合わせを受け付けました。\n\n";
		$auto_reply_text .= "お問い合わせ日時：" . $date . "\n\n";
		$auto_reply_text .= "氏名：" . $clean['your_name'] . "\n\n";
		$auto_reply_text .= "メールアドレス：" . $clean['email'] . "\n\n";
	
		if( $clean['gender'] === "male" ) {
			$auto_reply_text .= "性別：男性\n\n";
		} else {
			$auto_reply_text .= "性別：女性\n\n";
		}
		
		if( $clean['age'] === "1" ){
			$auto_reply_text .= "年齢：〜19歳\n\n";
		} elseif ( $clean['age'] === "2" ){
			$auto_reply_text .= "年齢：20歳〜29歳\n\n";
		} elseif ( $clean['age'] === "3" ){
			$auto_reply_text .= "年齢：30歳〜39歳\n\n";
		} elseif ( $clean['age'] === "4" ){
			$auto_reply_text .= "年齢：40歳〜49歳\n\n";
		} elseif( $clean['age'] === "5" ){
			$auto_reply_text .= "年齢：50歳〜59歳\n\n";
		} elseif( $clean['age'] === "6" ){
			$auto_reply_text .= "年齢：60歳〜\n\n";
		}
	
		$auto_reply_text .= "お問い合わせ内容：" . $clean['contact'] . "\n\n";
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
		$admin_reply_subject = "お問い合わせを受け付けました";
	
		// 本文を設定
		$admin_reply_text = "下記の内容でお問い合わせがありました。\n\n";
		$admin_reply_text .= "お問い合わせ日時：" . $date . "\n\n";
		$admin_reply_text .= "氏名：" . $clean['your_name'] . "\n\n";
		$admin_reply_text .= "メールアドレス：" . $clean['email'] . "\n\n";
	    
        $gender = null;
		if( $clean['gender'] === "male" ) {
			$admin_reply_text .= "性別：男性\n\n";
            $gender = "男性";
		} else {
			$admin_reply_text .= "性別：女性\n\n";
            $gender = "女性";
		}
	    
        $age = null;
		if( $clean['age'] === "1" ){
			$admin_reply_text .= "年齢：〜19歳\n\n";
            $age = "〜19歳";
		} elseif ( $clean['age'] === "2" ){
			$admin_reply_text .= "年齢：20歳〜29歳\n\n";
            $age = "20歳〜29歳";
		} elseif ( $clean['age'] === "3" ){
			$admin_reply_text .= "年齢：30歳〜39歳\n\n";
            $age = "30歳〜39歳";
		} elseif ( $clean['age'] === "4" ){
			$admin_reply_text .= "年齢：40歳〜49歳\n\n";
            $age = "40歳〜49歳";
		} elseif( $clean['age'] === "5" ){
			$admin_reply_text .= "年齢：50歳〜59歳\n\n";
            $age = "50歳〜59歳";
		} elseif( $clean['age'] === "6" ){
			$admin_reply_text .= "年齢：60歳〜\n\n";
            $age = "60歳〜";
		}
	
		$admin_reply_text .= "お問い合わせ内容：" . $clean['contact'] . "\n\n";
		
		// テキストメッセージをセット
//		$body = "--__BOUNDARY__\n";
//		$body .= "Content-Type: text/plain; charset=\"ISO-2022-JP\"\n\n";
		$body = $admin_reply_text . "\n";
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
        $customer_data->append( $date, $clean['your_name'], $clean['email'], $gender, $age, $clean['contact']);
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
	// メールアドレスのバリデーション
	if( empty($data['email']) ) {
		$error[] = "「メールアドレス」は必ず入力してください。";
	} elseif( !preg_match( '/^[0-9a-z_.\/?-]+@([0-9a-z-]+\.)+[0-9a-z-]+$/', $data['email']) ) {
		$error[] = "「メールアドレス」は正しい形式で入力してください。";
	}
	// 性別のバリデーション
	if( empty($data['gender']) ) {
		$error[] = "「性別」は必ず入力してください。";
	} elseif( $data['gender'] !== 'male' && $data['gender'] !== 'female' ) {
		$error[] = "「性別」は必ず入力してください。";
	}
	// 年齢のバリデーション
	if( empty($data['age']) ) {
		$error[] = "「年齢」は必ず入力してください。";
	} elseif( (int)$data['age'] < 1 || 6 < (int)$data['age'] ) {
		$error[] = "「年齢」は必ず入力してください。";
	}
	// お問い合わせ内容のバリデーション
	if( empty($data['contact']) ) {
		$error[] = "「お問い合わせ内容」は必ず入力してください。";
	}
	// プライバシーポリシー同意のバリデーション
	if( empty($data['agreement']) ) {
		$error[] = "プライバシーポリシーをご確認ください。";
	} elseif( (int)$data['agreement'] !== 1 ) {
		$error[] = "プライバシーポリシーをご確認ください。";
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
    <title>お問い合わせフォーム</title>
    <link rel="stylesheet" media="all" href="css/style.css">
    <style rel="stylesheet" type="text/css">
        .container {
            padding: 20px;
            text-align: center;
        }

        h1 {
            margin-bottom: 20px;
            padding: 20px 0;
            color: #209eff;
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

        textarea[name=contact] {
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
        <h1>お問い合わせフォーム</h1>
        <?php if( $page_flag === 1 ): ?>

        <form method="post" action="">
            <div class="element_wrap">
                <label>氏名</label>
                <p>
                    <?php echo $clean['your_name']; ?>
                </p>
            </div>
            <div class="element_wrap">
                <label>メールアドレス</label>
                <p>
                    <?php echo $clean['email']; ?>
                </p>
            </div>
            <div class="element_wrap">
                <label>性別</label>
                <p>
                    <?php if( $clean['gender'] === "male" ){ echo '男性'; }else{ echo '女性'; } ?>
                </p>
            </div>
            <div class="element_wrap">
                <label>年齢</label>
                <p>
                    <?php if( $clean['age'] === "1" ){ echo '〜19歳'; }
		elseif( $clean['age'] === "2" ){ echo '20歳〜29歳'; }
		elseif( $clean['age'] === "3" ){ echo '30歳〜39歳'; }
		elseif( $clean['age'] === "4" ){ echo '40歳〜49歳'; }
		elseif( $clean['age'] === "5" ){ echo '50歳〜59歳'; }
		elseif( $clean['age'] === "6" ){ echo '60歳〜'; } ?>
                </p>
            </div>
            <div class="element_wrap">
                <label>お問い合わせ内容</label>
                <p>
                    <?php echo nl2br($clean['contact']); ?>
                </p>
            </div>
            <!--
	<?php if( !empty($clean['attachment_file']) ): ?>
	<div class="element_wrap">
		<label>画像ファイルの添付</label>
		<p><img src="<?php echo FILE_DIR.$clean['attachment_file']; ?>"></p>
	</div>
	<?php endif; ?>
-->
            <div class="element_wrap">
                <label>プライバシーポリシーに同意する</label>
                <p>
                    <?php if( $clean['agreement'] === "1" ){ echo '同意する'; }else{ echo '同意しない'; } ?>
                </p>
            </div>
            <input type="submit" name="btn_back" value="戻る">
            <input type="submit" name="btn_submit" value="送信">
            <input type="hidden" name="your_name" value="<?php echo $clean['your_name']; ?>">
            <input type="hidden" name="email" value="<?php echo $clean['email']; ?>">
            <input type="hidden" name="gender" value="<?php echo $clean['gender']; ?>">
            <input type="hidden" name="age" value="<?php echo $clean['age']; ?>">
            <input type="hidden" name="contact" value="<?php echo $clean['contact']; ?>">
            <!--
	<?php if( !empty($clean['attachment_file']) ): ?>
		<input type="hidden" name="attachment_file" value="<?php echo $clean['attachment_file']; ?>">
	<?php endif; ?>
-->
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
            <div class="element_wrap">
                <label>氏名</label>
                <input type="text" name="your_name" value="<?php if( !empty($clean['your_name']) ){ echo $clean['your_name']; } ?>">
            </div>
            <div class="element_wrap">
                <label>メールアドレス</label>
                <input type="text" name="email" value="<?php if( !empty($clean['email']) ){ echo $clean['email']; } ?>">
            </div>
            <div class="element_wrap">
                <label>性別</label>
                <label for="gender_male"><input id="gender_male" type="radio" name="gender" value="male" <?php if( !empty($clean['gender']) && $clean['gender']==="male" ){ echo 'checked' ; } ?>>男性</label>
                <label for="gender_female"><input id="gender_female" type="radio" name="gender" value="female" <?php if( !empty($clean['gender']) && $clean['gender']==="female" ){ echo 'checked' ; } ?>>女性</label>
            </div>
            <div class="element_wrap">
                <label>年齢</label>
                <select name="age">
                    <option value="">選択してください</option>
                    <option value="1" <?php if( !empty($clean['age']) && $clean['age']==="1" ){ echo 'selected' ; } ?>>〜19歳</option>
                    <option value="2" <?php if( !empty($clean['age']) && $clean['age']==="2" ){ echo 'selected' ; } ?>>20歳〜29歳</option>
                    <option value="3" <?php if( !empty($clean['age']) && $clean['age']==="3" ){ echo 'selected' ; } ?>>30歳〜39歳</option>
                    <option value="4" <?php if( !empty($clean['age']) && $clean['age']==="4" ){ echo 'selected' ; } ?>>40歳〜49歳</option>
                    <option value="5" <?php if( !empty($clean['age']) && $clean['age']==="5" ){ echo 'selected' ; } ?>>50歳〜59歳</option>
                    <option value="6" <?php if( !empty($clean['age']) && $clean['age']==="6" ){ echo 'selected' ; } ?>>60歳〜</option>
                </select>
            </div>
            <div class="element_wrap">
                <label>お問い合わせ内容</label>
                <textarea name="contact"><?php if( !empty($clean['contact']) ){ echo $clean['contact']; } ?></textarea>
            </div>
            <!--
	<div class="element_wrap">
		<label>画像ファイルの添付</label>
		<input type="file" name="attachment_file">
	</div>
-->
            <div class="element_wrap">
                <label for="agreement"><input id="agreement" type="checkbox" name="agreement" value="1" <?php if( !empty($clean['agreement']) && $clean['agreement']==="1" ){ echo 'checked' ; } ?>><a href="#privacy">プライバシーポリシー</a>に同意する</label>
            </div>
            <input type="submit" name="btn_confirm" value="入力内容を確認する">

        </form>


        <?php endif; ?>

    </div>
</body>

</html>
