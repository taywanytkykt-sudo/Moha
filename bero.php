<?php

// Create By @is0mar FROM @SeroBots 
// ALL RIGHTS RESERVED


$config = [
    'DOMAIN'        => 'smmlox.com',
    'SITE_KEY'      => '21324e4c933e8d29667c3f2b4b595fcc',
    'ID_TELEGRAM'   => '10580',
    'ID_TIKTOK'     => '10581',
    'ID_INSTAGRAM'  => '10611'
];


$admin=8578774826;
define('API_KEY' , '8681800183:AAE8tvZvlsJ1BgYVNO8KDgU2777fTTSZOao');
function bot(string $method, array $datas = [], bool $async = false)
{
    $url = "https://api.telegram.org/bot" . API_KEY . "/" . $method;

    if ($async) {
        $postData = http_build_query($datas);
        $cmd = "curl -s -X POST -d \"$postData\" \"$url\" > /dev/null 2>&1 &";
        exec($cmd);
        return true;
    }
    $ch = curl_init();

    curl_setopt_array($ch, [
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $datas,
        CURLOPT_CONNECTTIMEOUT => 5,
        CURLOPT_TIMEOUT        => 10,
        CURLOPT_SSL_VERIFYPEER => false,
    ]);

    $res = curl_exec($ch);

    if (curl_error($ch)) {
        error_log("CURL ERROR: " . curl_error($ch));
        curl_close($ch);
        return null;
    }

    curl_close($ch);
    return json_decode($res, false);
}

bot('SetWebhook',[
    'url' =>$_SERVER['SERVER_NAME'] . $_SERVER['SCRIPT_NAME']."?a=BY_BERO",
    'drop_pending_updates' => true,
]);


$update = json_decode(file_get_contents("php://input"));

if (isset($update->callback_query)) {
    $callback = $update->callback_query;
    $data        = $callback->data ?? null;
    $from_id     = $callback->from->id ?? null;
    $name        = $callback->from->first_name ?? null;
    $user        = $callback->from->username ?? null;
    $chat_id     = $callback->message->chat->id ?? null;
    $title       = $callback->message->chat->title ?? null;
    $message_id  = $callback->message->message_id ?? null;
}

if (isset($update->message)) {
    $message = $update->message;
    $from_id    = $message->from->id ?? null;
    $name       = $message->from->first_name ?? null;
    $user       = $message->from->username ?? null;
    $chat_id    = $message->chat->id ?? null;
    $title      = $message->chat->title ?? null;
    $text       = strtolower( $message->text );
    $message_id = $message->message_id ?? null;
}
$bero= json_decode(file_get_contents('BerO.json'),1);


$orders= $bero['orders'] ?? 0;

$SITE_KEY= $config['SITE_KEY'];
$DOMAIN= $config['DOMAIN'];
$ID_TELEGRAM= $config['ID_TELEGRAM'];
$ID_INSTAGRAM= $config['ID_INSTAGRAM'];
$ID_TIKTOK= $config['ID_TIKTOK'];

if(!in_array($from_id,$bero['members'])){
    $bero['members'][]= $from_id;
    file_put_contents("BerO.json", json_encode($bero));
    $members = count($bero['members']);
    bot("SendMessage",[
        'chat_id' => $admin,
        'text'=>"*دخل شخص جديد لبوتك 👤*

الاسم : [$name](tg://user?id=$from_id)
الايدي : `$from_id`
المعرف : [@$user]

- عدد مستخدمين البوت : *$members*",
        'parse_mode'=> "Markdown",
    ]);
}

$aa = bot('getChatMember', [
    'chat_id' => $bero['chIJBARE'],
    'user_id' => $from_id,
]);

if ($aa->result->status == 'left' ||$aa->result->status == 'kicked') {
$name_ch = bot('getChat', ['chat_id' => $bero['chIJBARE']])->result->title;
bot('SendMessage', [
        'chat_id' => $chat_id,
        'reply_to_message_id' => $message_id,
        'parse_mode' => 'Markdown',
        'text' => "🚸| عذراً عزيزي..
🔰| عليك الاشتراك في قناة البوت لتتمكن من استخدامه

- [{$bero['chIJBARE']}]

‼️| اشترك ثم ارسل /start",
        'reply_markup' => json_encode([
            'inline_keyboard' => [
                [['text' => "$name_ch",'url'  => 'https://t.me/'. str_replace("@","",$bero['chIJBARE'])]]
            ]
        ])
    ]);
    return;
}

if(!$bero['thbot']){
    $bero['thbot']='✅';
    file_put_contents("BerO.json", json_encode($bero));
}

if ($chat_id == $admin) {

    $members      = count($bero['members']);
    $teleorder    = $bero['orders_tele'] ?? 0;
    $tiktokorder  = $bero['orders_tiktok'] ?? 0;
    $instaorder   = $bero['orders_instagram'] ?? 0;
    $orders       = $bero['orders'] ?? 0;

    $chIJBARE = $bero['chIJBARE'] ?? "لايوجد";
    $thbot    = $bero['thbot']    ?? '❌';

    if ($data == 'thbot') {
        $thbot = ($thbot == '✅') ? '❌' : '✅';
        $bero['thbot'] = $thbot;

        bot('answerCallbackQuery', [
            'callback_query_id' => $update->callback_query->id,
            'text' => "- تم تغيير الحالة الى $thbot",
            'show_alert' => false
        ]);

        file_put_contents("BerO.json", json_encode($bero));
        $data = "XBACK";
    }

    if ($text == '/start') {
        bot("SendMessage", [
            'chat_id' => $chat_id,
            'parse_mode' => "Markdown",
            'text' => "- مرحبا بك عزيزي الادمن 👤

*احصائيات البوت 📊*
عدد مستخدمين البوت : `$members`
عدد الطلبات : `$orders`
عدد طلبات تلكرام : `$teleorder`
عدد طلبات تيكتوك : `$tiktokorder`
عدد طلبات انستكرام : `$instaorder`

قناة الاشتراك الاجباري : [$chIJBARE]",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [["text" => "البوت : $thbot", "callback_data" => "thbot"]],
                    [["text" => "تعين قناة الاشتراك الاجباري", "callback_data" => "setCH"]],
                ]
            ])
        ]);
    }

    if ($data == 'XBACK') {
        bot("EditMessageText", [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'parse_mode' => "Markdown",
            'text' => "- مرحبا بك عزيزي الادمن 👤

*احصائيات البوت 📊*
عدد مستخدمين البوت : `$members`
عدد الطلبات : `$orders`
عدد طلبات تلكرام : `$teleorder`
عدد طلبات تيكتوك : `$tiktokorder`
عدد طلبات انستكرام : `$instaorder`

قناة الاشتراك الاجباري : [$chIJBARE]",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [["text" => "البوت : $thbot", "callback_data" => "thbot"]],
                    [["text" => "تعين قناة الاشتراك الاجباري", "callback_data" => "setCH"]],
                ]
            ])
        ]);
    }

    if ($data == 'setCH') {
        bot("EditMessageText", [
            'chat_id' => $chat_id,
            'message_id' => $message_id,
            'parse_mode' => "Markdown",
            'text' => "- ارسل معرف القناة *لوضعه في الاشتراك الاجباري 🔱* 
- وتأكد ان البوت مشرف بالقناة لايعمل الاشتراك الاجباري",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [["text" => "الرجوع", "callback_data" => "XBACK"]],
                ]
            ])
        ]);

        $bero['mode_' . $from_id] = 'setCH';
        file_put_contents("BerO.json", json_encode($bero));
        return;
    }

    if ($text && ($bero['mode_' . $from_id] ?? null) == 'setCH') {

        $text = str_replace("@", "", $text);

        bot("SendMessage", [
            'chat_id' => $chat_id,
            'parse_mode' => "Markdown",
            'text' => "*- تم وضع القناة @$text اشتراك اجباري داخل البوت 📣*

- يرجي التاكد من ان البوت *مشرف في القناة*",
            'reply_markup' => json_encode([
                'inline_keyboard' => [
                    [["text" => "الرجوع", "callback_data" => "XBACK"]],
                ]
            ])
        ]);

        $bero['chIJBARE'] = "@$text";
        unset($bero['mode_' . $from_id]);
        file_put_contents("BerO.json", json_encode($bero));
        return;
    }
}

if($chat_id != $admin){
    if($bero['thbot']=='❌'){
        if(!$data){
        bot("SendMessage",[
        'chat_id' => $chat_id,
        'text'=>"*- البوت لايعمل حاليا, حاول لاحقا !*",
        'parse_mode'=> "Markdown",
    ]);
        }else{
            bot('answerCallbackQuery',[
      'callback_query_id'=>$update->callback_query->id,
      'text'=>"- البوت لايعمل حاليا, حاول لاحقا !",
      'show_alert'=>true
      ]);
        }
    return;
    }
}
if($text == "/start"){
    bot('SendPhoto', [
    'chat_id' => $chat_id,'photo'=> "https://files.catbox.moe/lhfw8w.jpg",
        'caption'=>"*- مرحبا بك *([$name](tg://user?id=$from_id)) 👋🏻
- *في بوت *رشق الخدمات المجانية 🎁
- ابدا الان بأختيار *الخدمه ادنى* واتبع التعليمات 📘",
        'parse_mode'=> "Markdown",
        'reply_markup' => json_encode([
        'inline_keyboard' => [
            [["text" => "- $name 👤", "url" => "tg://user?id=$from_id"]],
            [["text" => "🎁 مشاهدات تلكرام 🎁", "callback_data" => "TELEVIEWS"]],
            [["text" => "🎁 حفظ فيديو تيكتوك 🎁", "callback_data" => "SAVETIKTOK"],["text" => "🎁 مشاهدات ريلز انستا 🎁", "callback_data" => "reelsveiws"]],
            [["text" => "الطلبات المنجزة : $orders ✅", "callback_data" => "ORDERS"]],
        ]
    ])
    ]);
    unset($bero['helper_'. $from_id]);
    unset($bero['mode_'. $from_id]);
    file_put_contents("BerO.json", json_encode($bero));
    return;
}

if($data=='CBACK'){
bot('DeleteMessage',[
        'chat_id'=> $chat_id,
        'message_id' => $message_id,
    ]);
    bot("SendMessage",[
        'chat_id' => $chat_id,
        'text'=>"*- مرحبا بك *([$name](tg://user?id=$from_id)) 👋🏻
- *في بوت *رشق الخدمات المجانية 🎁
- ابدا الان بأختيار *الخدمه ادنى* واتبع التعليمات 📘",
        'parse_mode'=> "Markdown",
        'reply_markup' => json_encode([
        'inline_keyboard' => [
            [["text" => "- $name 👤", "url" => "tg://user?id=$from_id"]],
            [["text" => "🎁 مشاهدات تلكرام 🎁", "callback_data" => "TELEVIEWS"]],
            [["text" => "🎁 حفظ فيديو تيكتوك 🎁", "callback_data" => "SAVETIKTOK"],["text" => "🎁 مشاهدات ريلز انستا 🎁", "callback_data" => "reelsveiws"]],
            [["text" => "الطلبات المنجزة : $orders ✅", "callback_data" => "ORDERS"]],
        ]
    ])
    ]);
    unset($bero['helper_'. $from_id]);
    unset($bero['mode_'. $from_id]);
    file_put_contents("BerO.json", json_encode($bero));
    return;
}

if($data=='reelsveiws'){
bot('DeleteMessage',[
        'chat_id'=> $chat_id,
        'message_id' => $message_id,
    ]);
    bot('SendPhoto', [
    'chat_id' => $chat_id,'photo'=> "https://files.catbox.moe/pt55x6.jpg",
    'parse_mode'=> "Markdown",
    'caption'=>"- خدمة مشاهدات ريلز انستا *مجانا 🎁* . .
    
- أرسل رابط الريلز الان:",
    'reply_markup' => json_encode([
        'inline_keyboard' => [
            [["text" => "الرجوع", "callback_data" => "CBACK"]],
        ]
    ])
]);
$bero['mode_'. $from_id]=$data;
file_put_contents("BerO.json", json_encode($bero));
return;
}


if($text and $bero['mode_'. $from_id] =='reelsveiws'){
    bot("SendMessage",[
        'chat_id' => $chat_id,
        'text'=>"*- أرسل عدد الرشق بين 10 الى 50 🏮*",
        'parse_mode'=> "Markdown",
        'reply_markup' => json_encode([
        'inline_keyboard' => [
            [["text" => "الرجوع", "callback_data" => "CBACK"]],
        ]
    ])
    ]);
    $bero['helper_'. $from_id]=$text;
    $bero['mode_'. $from_id]='needcounreelsveiws';
    file_put_contents("BerO.json", json_encode($bero));
    return;
}

if($text and $bero['mode_'. $from_id] =='needcounreelsveiws'){
if($text <= 50 and $text >= 10){
$link=$bero['helper_'. $from_id];
$id = json_decode(file_get_contents("https://$DOMAIN/api/v2?key=$SITE_KEY&action=add&service=$ID_INSTAGRAM&link=$link&quantity=$text"))->order;
bot("SendMessage",[
        'chat_id' => $chat_id,
        'text'=>"*- تم استلام طلبك $id ✅*
- الى الرابط : [$link]
- العدد : [$text]
- سيتم اضافه المشاهدات ....

*شكرا لاستخدامك البوت ❇️*",
        'parse_mode'=> "Markdown",
    ]);
    $orders_sec= $bero['orders_instagram']+1;
    bot("SendMessage",[
        'chat_id' => $admin,
        'text'=>"*طلب جديد من خدمة* '`🎁 مشاهدات ريلز انستا 🎁' `
من الشخص 👤
الايدي: `$from_id`
المعرف: [@$user]
الاسم: [$name]

- الى الرابط : [$link]
- العدد : [$text]

- عدد الطلبات بهذا القسم : *$orders_sec* 🎟
",
        'parse_mode'=> "Markdown",
        'reply_markup' => json_encode([
        'inline_keyboard' => [
            [["text" => "- $name 👤", "url" => "tg://user?id=$from_id"]],
        ]
    ])
    ]);
    $bero['orders_instagram'] +=1;
    $bero['orders'] +=1;
    unset($bero['helper_'. $from_id]);
    unset($bero['mode_'. $from_id]);
    file_put_contents("BerO.json", json_encode($bero));
}else{
    bot("SendMessage",[
        'chat_id' => $chat_id,
        'text'=>"*- أرسل عدد الرشق بين 10 الى 50 فقط ❗️*",
        'parse_mode'=> "Markdown",
        'reply_markup' => json_encode([
        'inline_keyboard' => [
            [["text" => "الرجوع", "callback_data" => "CBACK"]],
        ]
    ])
    ]);
}
}


if($data=='SAVETIKTOK'){
    bot('DeleteMessage',[
        'chat_id'=> $chat_id,
        'message_id' => $message_id,
    ]);
    bot('SendPhoto', [
    'chat_id' => $chat_id,'photo'=> "https://files.catbox.moe/7ac87c.jpg",
    'parse_mode'=> "Markdown",
    'caption'=>"- خدمة حفظ فيديو تيكتوك *مجانا 🎁* . .
    
- أرسل رابط الفيديو الان:",
    'reply_markup' => json_encode([
        'inline_keyboard' => [
            [["text" => "الرجوع", "callback_data" => "CBACK"]],
        ]
    ])
]);
$bero['mode_'. $from_id]=$data;
file_put_contents("BerO.json", json_encode($bero));
return;
}

if($text and $bero['mode_'. $from_id] =='SAVETIKTOK'){
    bot("SendMessage",[
        'chat_id' => $chat_id,
        'text'=>"*- أرسل عدد الرشق بين 10 الى 10 🏮*",
        'parse_mode'=> "Markdown",
        'reply_markup' => json_encode([
        'inline_keyboard' => [
            [["text" => "الرجوع", "callback_data" => "CBACK"]],
        ]
    ])
    ]);
    $bero['helper_'. $from_id]=$text;
    $bero['mode_'. $from_id]='needcounSAVETIKTOK';
    file_put_contents("BerO.json", json_encode($bero));
    return;
}

if($text and $bero['mode_'. $from_id] =='needcounSAVETIKTOK'){
if($text == 10){
$link=$bero['helper_'. $from_id];
$id = json_decode(file_get_contents("https://$DOMAIN/api/v2?key=$SITE_KEY&action=add&service=$ID_TIKTOK&link=$link&quantity=$text"))->order;
bot("SendMessage",[
        'chat_id' => $chat_id,
        'text'=>"*- تم استلام طلبك $id ✅*
- الى الرابط : [$link]
- العدد : [$text]
- سيتم بدأ حفظ الفيديو ....

*شكرا لاستخدامك البوت ❇️*",
        'parse_mode'=> "Markdown",
    ]);
    $orders_sec= $bero['orders_tiktok']+1;
    bot("SendMessage",[
        'chat_id' => $admin,
        'text'=>"*طلب جديد من خدمة* ' `🎁 حفظ فيديو تيكتوك 🎁` '
من الشخص 👤
الايدي: `$from_id`
المعرف: [@$user]
الاسم: [$name]

- الى الرابط : [$link]
- العدد : [$text]

- عدد الطلبات بهذا القسم : *$orders_sec* 🎟
",
        'parse_mode'=> "Markdown",
        'reply_markup' => json_encode([
        'inline_keyboard' => [
            [["text" => "- $name 👤", "url" => "tg://user?id=$from_id"]],
        ]
    ])
    ]);
    $bero['orders_tiktok'] +=1;
    $bero['orders'] +=1;
    unset($bero['helper_'. $from_id]);
    unset($bero['mode_'. $from_id]);
    file_put_contents("BerO.json", json_encode($bero));
}else{
    bot("SendMessage",[
        'chat_id' => $chat_id,
        'text'=>"*- أرسل عدد الرشق بين 10 الى 10 فقط ❗️*",
        'parse_mode'=> "Markdown",
        'reply_markup' => json_encode([
        'inline_keyboard' => [
            [["text" => "الرجوع", "callback_data" => "CBACK"]],
        ]
    ])
    ]);
}
}

if($data=='TELEVIEWS'){
    bot('DeleteMessage',[
        'chat_id'=> $chat_id,
        'message_id' => $message_id,
    ]);
    bot('SendPhoto', [
    'chat_id' => $chat_id,'photo'=> "https://files.catbox.moe/gsijvw.jpg",
    'parse_mode'=> "Markdown",
    'caption'=>"- خدمة مشاهدات تلكرام *مجانا 🎁* .
    
- أرسل رابط المنشور الان:",
    'reply_markup' => json_encode([
        'inline_keyboard' => [
            [["text" => "الرجوع", "callback_data" => "CBACK"]],
        ]
    ])
]);
$bero['mode_'. $from_id]=$data;
file_put_contents("BerO.json", json_encode($bero));
return;
}

if($text and $bero['mode_'. $from_id] =='TELEVIEWS'){
    bot("SendMessage",[
        'chat_id' => $chat_id,
        'text'=>"*- أرسل عدد الرشق بين 10 الى 10 🏮*",
        'parse_mode'=> "Markdown",
        'reply_markup' => json_encode([
        'inline_keyboard' => [
            [["text" => "الرجوع", "callback_data" => "CBACK"]],
        ]
    ])
    ]);
    $bero['helper_'. $from_id]=$text;
    $bero['mode_'. $from_id]='needcounttele';
    file_put_contents("BerO.json", json_encode($bero));
    return;
}

if($text and $bero['mode_'. $from_id] =='needcounttele'){
if($text == 10){
$link=$bero['helper_'. $from_id];
$id = json_decode(file_get_contents("https://$DOMAIN/api/v2?key=$SITE_KEY&action=add&service=$ID_TELEGRAM&link=$link&quantity=$text"))->order;
bot("SendMessage",[
        'chat_id' => $chat_id,
        'text'=>"*- تم استلام طلبك $id ✅*
- الى الرابط : [$link]
- العدد : [$text]
- سيتم اضافه المشاهدات ....

*شكرا لاستخدامك البوت ❇️*",
        'parse_mode'=> "Markdown",
    ]);
    $orders_sec= $bero['orders_tele']+1;
    bot("SendMessage",[
        'chat_id' => $admin,
        'text'=>"*طلب جديد من خدمة* ' `🎁 مشاهدات تلكرام 🎁` '
من الشخص 👤
الايدي: `$from_id`
المعرف: [@$user]
الاسم: [$name]

- الى الرابط : [$link]
- العدد : [$text]

- عدد الطلبات بهذا القسم : *$orders_sec* 🎟",
        'parse_mode'=> "Markdown",
        'reply_markup' => json_encode([
        'inline_keyboard' => [
            [["text" => "- $name 👤", "url" => "tg://user?id=$from_id"]],
        ]
    ])
    ]);
    $bero['orders_tele'] +=1;
    $bero['orders'] +=1;
    unset($bero['helper_'. $from_id]);
    unset($bero['mode_'. $from_id]);
    file_put_contents("BerO.json", json_encode($bero));
}else{
    bot("SendMessage",[
        'chat_id' => $chat_id,
        'text'=>"*- أرسل عدد الرشق بين 10 الى 10 فقط ❗️*",
        'parse_mode'=> "Markdown",
        'reply_markup' => json_encode([
        'inline_keyboard' => [
            [["text" => "الرجوع", "callback_data" => "CBACK"]],
        ]
    ])
    ]);
}
}