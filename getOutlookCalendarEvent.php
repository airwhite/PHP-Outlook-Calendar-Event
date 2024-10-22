<?php
require 'vendor/autoload.php';

use Microsoft\Kiota\Authentication\Oauth\ClientCredentialContext;
use Microsoft\Graph\GraphServiceClient;
use Microsoft\Graph\Generated\Users\Item\Events\EventsRequestBuilderGetRequestConfiguration;

define('TenantID',     'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx');
define('ClientId',     'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx');
define('ClientSecret', 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx');
define('ScopeUrl',     'https://graph.microsoft.com/.default');
define('EventMax',     100);
define('UPN',          'xxxxxxxx@xxxxxxxx.co.jp');

function getOutlookCalendarEvent() {
  $cal = array();

  // クライアントシークレットによる認証
  $tokenRequestContext = new ClientCredentialContext(TenantID, ClientId, ClientSecret);

  // スコープの設定
  $graphServiceClient = new GraphServiceClient($tokenRequestContext, [ ScopeUrl ]);

  // 取得するイベントのタイムゾーンを東京にする
  $requestConfiguration = new EventsRequestBuilderGetRequestConfiguration();
  $headers = [
    'Prefer' => 'outlook.timezone="Tokyo Standard Time"'
  ];
  $requestConfiguration->headers = $headers;

  // イベント取得時のクエリを設定する
  $queryParameters = EventsRequestBuilderGetRequestConfiguration::createQueryParameters();
  // 取得するイベントの開始時刻を本日(UTC)以降に設定する
  $queryParameters->filter = "start/dateTime ge '".date('Y-m-d\TH:i:s\Z',strtotime('today -9 Hours'))."'";
  // 取得件数を指定する(初期値:10)
  $queryParameters->top = EventMax;
  $requestConfiguration->queryParameters = $queryParameters;

  // イベントを取得する
  $events = $graphServiceClient->users()->byUserId(UPN)->events()->get($requestConfiguration)->wait();
  foreach ($events->getValue() as $event) {
    // イベントID
    $evtId    = $event->getId();
    // 開始日時
    $evtStart = date("Y-m-d H:i:s", strtotime($event->getStart()->getDateTime()));
    // 終了日時
    $evtEnd   = date("Y-m-d H:i:s", strtotime($event->getEnd()->getDateTime()));
    // 件名
    $evtSubj  = $event->getSubject();
    // 内容（簡易表示）
    $evtBody  = $event->getBodyPreview();
    // 場所
    $evtLoc   = $event->getLocation()->getDisplayName();
    // 更新日時(UTC)
    $evTMod   = $event->getLastModifiedDateTime()->format("Y-m-d H:i:s");

    $cal[] = array(
      "calId"=>$evtId,
      "start"=>$evtStart,
      "end"=>$evtEnd,
      "subject"=>$evtSubj,
      "body"=>$evtBody,
      "location"=>$evtLoc,
      "modified"=>$evtMod,
    );
  }

  return $cal;
}

var_dump(getOutlookCalendarEvent());

?>
