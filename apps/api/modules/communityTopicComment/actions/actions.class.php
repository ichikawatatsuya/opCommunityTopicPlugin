<?php

/**
 * This file is part of the OpenPNE package.
 * (c) OpenPNE Project (http://www.openpne.jp/)
 *
 * For the full copyright and license information, please view the LICENSE
 * file and the NOTICE file that were distributed with this source code.
 */

/**
 * community topic api actions.
 *
 * @package    OpenPNE
 * @subpackage action
 * @author     Shunsuke Watanabe <watanabe@craftgear.net>
 */
class communityTopicCommentActions extends opJsonApiActions
{
  public function preExecute()
  {
    parent::preExecute();
    $this->member = $this->getUser()->getMember();
  }

  public function executeSearch(sfWebRequest $request)
  {
    $this->forward400If('' === (string)$request['community_topic_id'], 'community_topic_id parameter is not specified.');

    $topic = Doctrine::getTable('CommunityTopic')->findOneById($request['community_topic_id']);

    $topic->actAs('opIsCreatableCommunityTopicBehavior');
    $this->forward400If(false === $topic->isViewableCommunityTopic($topic->getCommunity(), $this->member->getId()), 'you are not allowed to view this topic and comments on this community');

    $this->memberId = $this->getUser()->getMemberId();
    $this->comments = Doctrine::getTable('CommunityTopicComment')->createQuery('q')
                        ->where('community_topic_id = ?', $request['community_topic_id'])
                        ->orderBy('created_at')
                        ->execute();
  }

  public function executePost(sfWebRequest $request)
  {
    $this->forward400If('' === (string)$request['community_topic_id'], 'community_topic_id parameter is not specified.');
    $this->forward400If('' === (string)$request['body'], 'body parameter is not specified.');

    $comment = new CommunityTopicComment();
    $comment->setMemberId($this->member->getId());
    $comment->setCommunityTopicId($request['community_topic_id']);

    $this->forward400If(false === $comment->getCommunityTopic()->isCreatableCommunityTopicComment($this->member->getId()), 'you are not allowed to create comments on this topic');

    $comment->setBody($request['body']);
    $comment->save();

    $this->comment = $comment;
  }

  public function executeDelete(sfWebRequest $request)
  {
    $id = $request['id'];
    $this->forward400If('' === (string)$id, 'id parameter is not specified.');

    $comment = Doctrine::getTable('CommunityTopicComment')->findOneById($id);

    $this->forward400If(false === $comment, 'the comment does not exist. id:'.$id);
    $this->forward400If(false === $comment->isDeletable($this->member->getId()), 'you can not delete this comment. id:'.$id);

    $isDeleted = $comment->delete();
    if ($isDeleted)
    {
      $this->id = $id;
    }
    else
    {
      $this->forward400('failed to delete the comment. errorStack:'.$comment->getErrorStackAsString());
    }
  }
}
