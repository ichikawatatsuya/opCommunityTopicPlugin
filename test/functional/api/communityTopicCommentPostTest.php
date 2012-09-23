<?php

include dirname(__FILE__).'/../../bootstrap/functional.php';

$t = new opTestFunctional(new sfBrowser());

//include dirname(__FILE__).'/../../bootstrap/database.php';

$t->info('should be able to post a new comment');
$body = 'コメント本文';
$json = $t->post('/topic_comment/post.json',
    array(
      'apiKey'       => 'dummyApiKey',
      'community_topic_id' => 1,
      'body'         => $body,
    )
  )->getResponse()->getContent()
;
$data = json_decode($json, true);
$t->test()->is($data['status'], 'success', 'should return status code "success"');
$t->test()->ok($data['data']['id'], 'should have an id');
$t->test()->ok($data['data']['member'], 'should have a member info');
$t->test()->is($data['data']['body'], $body, 'should have the same body posted');
$t->test()->ok($data['data']['ago'], 'should have the ago posted');
$t->test()->ok($data['data']['created_at'], 'should have the date posted');
