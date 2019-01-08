<?php

namespace app\index\model;

use think\Model;

class TopicTag extends Model
{
    public function topic()
    {
        return $this->belongsTo('Topic', 'topic_id');
    }

    public static function getHotTags($num)
    {
        $topicTag = new self();
        return $topicTag->alias('tag')
            ->join('tp_topic topic', 'topic.id=tag.topic_id')
            ->field('tag.tag_id, count(tag.topic_id) as topicNum')
            ->where(['is_delete' => 0])
            ->group('tag_id')
            ->order('topicNum', 'DESC')
            ->limit($num)
            ->select();
    }

//    public static function getHotTags($num)
//    {
//        $topicTag = new TopicTag();
//        return $topicTag
//            ->field('tag_id, count(topic_id) as topicNum')
//            ->group('tag_id')
//            ->limit($num)
//            ->select();
//    }

    public static function getTagTopicIds($page, $limitNum, $tagId)
    {
        return self::where('tag_id', $tagId)
            ->page($page, $limitNum)
            ->column('topic_id');
    }

    public static function getTagTopicByTopicId($topicId)
    {
        return self::where(['topic_id' => $topicId])->select();
    }

    public static function isExists($tagId, $topicId)
    {
        return self::where([
            'tag_id' => $tagId,
            'topic_id' => $topicId
        ])->find();
    }

    public function tag()
    {
        return $this->belongsTo('Tag', 'tag_id');
    }
}
