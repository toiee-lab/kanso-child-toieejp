<?php
/**
 * Customer processing order email
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/customer-processing-order.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see 	    https://docs.woocommerce.com/document/template-structure/
 * @author 		WooThemes
 * @package 	WooCommerce/Templates/Emails
 * @version     2.5.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * @hooked WC_Emails::email_header() Output the email header
 */
do_action( 'woocommerce_email_header', $email_heading, $email ); ?>

<h2>お申し込みが、完了しました</h2><br />
今後、「といリブ」の教材を、ドンドン追加していきます。<br />
ご意見・ご要望がある場合は、お気軽にdesk@toiee.jpまでご連絡ください。<br />
<br />
それでは、といリブをお楽しみに下さい。<br />
<br />
toiee Labスタッフ一同<br />
<br />
※サービスの仕様上、クレジット支払いの確認が取れ次第、「注文が完了しました」というメールをお送りします<br />
※サービスお申し込み後から時間差がありますが、ご心配なく「といリブ」をご利用ください

<br />

<h2>アクセス方法</h2>
<ol>
 <li><a href="https://toiee.jp/my-account/" >ここをクリック</a></li>
 <li> お申し込み時に、「toieelabアカウント登録のお知らせ」というタイトルでお送りしている「ユーザー名」と「パスワード」でログイン</li>
 <li><a href="https://toiee.jp/project/toieelib/about-toiee-lib/" >「といリブとは？」をクリック</a>して、使い方を学ぶ</li>
 <li>好きな教材にアクセスし、さっそく学ぶ</li>
 </ol>
<br />
<h2>お申し込み内容</h2><br />
<br />
<?php

/**
 * @hooked WC_Emails::order_details() Shows the order details table.
 * @hooked WC_Structured_Data::generate_order_data() Generates structured data.
 * @hooked WC_Structured_Data::output_structured_data() Outputs structured data.
 * @since 2.5.0
 */
do_action( 'woocommerce_email_order_details', $order, $sent_to_admin, $plain_text, $email );

/**
 * @hooked WC_Emails::order_meta() Shows order meta data.
 */
do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text, $email );

/**
 * @hooked WC_Emails::customer_details() Shows customer details
 * @hooked WC_Emails::email_address() Shows email address
 */
do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text, $email );

/**
 * @hooked WC_Emails::email_footer() Output the email footer
 */
do_action( 'woocommerce_email_footer', $email );