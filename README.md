# PHP-Outlook-Calendar-Event
PHP で Outlook Calendar のイベントを取得するのに情報が少なくて苦労したので、記録に残しておきます。
## 動作環境
- PHP 7.4 以降
- composer 2.8 以降
- microsoft/microsoft-graph 2.16 以降
## 困った点
一番困ったのがMicrosoftのサイトで、サンプルコードがGraphで接続して取得で終わっていたところ、どのように取得したオブジェクトを扱えばよいのか不明だった。そこで、取得したオブジェクトに対して、PHPの関数 get_class_methds() で使えるメソッドに当たりを付けながらオブジェクトを操作しました。
次にGraphに対して、Cakendar Eventを取得した際に10件しかEventを取得出来なかった件。これは仕様で、Queryで何も指定しないとデフォルトで10件しか取得しないので、topで取得したい件数を指定する必要があった。
## エラー対策
```
PHP Fatal error: Uncaught GuzzleHttp\Exception\RequestException: cURL error 60: SSL certificate problem: unable to get local issuer certificate
```
上記のようなエラーが出た場合は、vendor/guzzlehttp/guzzle/src/Client.php の関数 configureDefaults() の $defaults[‘verify’] の値を true から false に変更することで解決できます。このような力業ではなくてスマートに解決できれば良いのですが調べていて途中で面倒になりました。
