<?php
use_helper('opAsset');
op_smt_use_stylesheet('/opCommunityTopicPlugin/css/smt-topic.css', 'last');
?>
<script id="topicEntry" type="text/x-jquery-tmpl">
<div class="row entry">
  <span class="span3">
    ${ago}
  </span>
  <span class="span9"><a href="<?php echo public_path('communityTopic'); ?>/${id}">${name}</a></span>
  <div class="span12">
    <div>
      {{if latest_comment}}
        {{html $item.truncateComment()}}
        <a href="<?php echo public_path('communityTopic'); ?>/${id}" class="readmore">続き</a>
      {{else}}
        <span class="muted">（まだコメントはありません）</span>
      {{/if}}
    </div>
  </div>
</div>
</script>

<script type="text/javascript">
function getList(params)
{
  var id = <?php echo $id ?>;
  if (id != null)
  {
    params.id = id;
  }
  params.format = 'mini';
  $('#loading').show();
  $.getJSON( openpne.apiBase + 'topic/search.json',
    params,
    function(json)
    {
      if (json.data.length === 0)
      {
        $('#noEntry').show();
      }
      else
      {
        var entry = $('#topicEntry').tmpl(json.data, 
        {
          truncateComment: function(){
            return this.data.latest_comment.substr(0, 50);
          }
        });
        $('#list').append(entry);
      }
      if (json.next != false)
      {
        $('#loadmore').attr('x-page', json.next).show();
      }
      else
      {
        $('#loadmore').hide();
      }
      $('#loading').hide();
    }
  );
}

$(function(){
  getList({apiKey: openpne.apiKey});

  $('#loadmore').click(function()
  {
    var params = {
      apiKey: openpne.apiKey,
      page: $(this).attr('x-page')
    };
    getList(params);
  })
})
</script>
<div class="row">
  <a href="<?php echo public_path('communityTopic/new').'/'.$id ?>" class="btn span11"><?php echo __('Create a new topic');?></a>
</div>
<hr class="toumei"/>
<div class="row">
  <div class="gadget_header span12"><?php echo __('List of topics of this community'); ?></div>
</div>
<div id="list"></div>
<div class="row hide" id="noEntry">
  <div class="center span12">まだトピックはありません</div>
</div>
<div class="row">
  <div id="loading" class="center">
    <?php echo op_image_tag('ajax-loader.gif');?>
  </div>
</div>
<div class="row">
  <button class="span12 btn small hide" id="loadmore"><?php echo __('More'); ?></button>
</div>

