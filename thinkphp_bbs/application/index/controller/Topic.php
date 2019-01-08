<?php

namespace app\index\controller;

use app\index\model\Topic as TopicModel;
use app\index\model\Tag as TagModel;
use app\index\model\TopicTag as TopicTagModel;
use app\index\model\Praise as PraiseModel;
use app\index\model\Reply as ReplyModel;
use think\Cache;

class Topic extends \think\Controller
{
    public function index()
    {
        $getData = input('get.');
        $page = isset($getData['page']) ? $getData['page'] : 1;
        $tagId = isset($getData['tag']) ? $getData['tag'] : '';
        $field = isset($getData['field']) ? $getData['field'] : 'created_at';
        $order = isset($getData['order']) ? $getData['order'] : 'desc';
        $sortInfo = ['field' => $field, 'order' => $order];
        $pageInfo = TopicModel::getPageInfo($page, config('limitNum'));
        $cacheName = 'index' . $page . $field . $order;
        if (cache($cacheName)) {
            $topics = cache($cacheName);
        } else {
            $topics = TopicModel::getTopics($pageInfo['page'], config('limitNum'), $sortInfo);
            cache($cacheName, $topics, 300, 'topicListCache');
        }

        $this->assign([
            'topics' => $topics,
            'user' => session('user'),
            'page' => $pageInfo['page'],
            'pageNum' => $pageInfo['pageNum'],
            'showPages' => $pageInfo['showPages'],
            'hotTags' => TopicTagModel::getHotTags(config('hotTagNum')),
            'field' => $field,
            'order' => $order,
            'tagId' => $tagId,
        ]);
        echo $this->fetch('index');
    }

    public function search()
    {
        $getData = input('get.');
        $page = isset($getData['page']) ? $getData['page'] : 1;
        $field = isset($getData['field']) ? $getData['field'] : '';
        $order = isset($getData['order']) ? $getData['order'] : '';
        $sortInfo = ['field' => $field, 'order' => $order];
        $keyword = isset($getData['keyword']) ? $getData['keyword'] : '';
        $pageInfo = TopicModel::getSearchPageInfo($page, config('limitNum'), $keyword);
        $cacheName = 'index' . $keyword . $page . $field . $order;
        if (cache($cacheName)) {
            $topics = cache($cacheName);
        } else {
            $topics = TopicModel::search($pageInfo['page'], config('limitNum'), $keyword, $sortInfo);
            cache($cacheName, $topics, 300, 'topicListCache');
        }
        $this->assign([
            'topics' => $topics,
            'user' => session('user'),
            'page' => $pageInfo['page'],
            'keyword' => $keyword,
            'pageNum' => $pageInfo['pageNum'],
            'showPages' => $pageInfo['showPages'],
            'hotTags' => TopicTagModel::getHotTags(config('hotTagNum')),
            'field' => $field,
            'order' => $order,
        ]);
        echo $this->fetch('index');
    }

    public function tag()
    {
        $getData = input('get.');
        $page = isset($getData['page']) ? $getData['page'] : 1;
        $tagId = isset($getData['tag']) ? $getData['tag'] : '';
        $field = isset($getData['field']) ? $getData['field'] : '';
        $order = isset($getData['order']) ? $getData['order'] : '';
        $sortInfo = ['field' => $field, 'order' => $order];
        $count = TopicTagModel::hasWhere('Topic', ['is_delete' => 0])->where(['tag_id' => $tagId])->count();
        $pageInfo = TopicModel::getTagPageInfo($page, config('limitNum'), $count);
        $topicIds = TopicTagModel::getTagTopicIds($pageInfo['page'], config('limitNum'), $tagId);
        $cacheName = 'index' . $tagId . $page . $field . $order;
        if (cache($cacheName)) {
            $topics = cache($cacheName);
        } else {
            $topics = TopicModel::getTagTopics($topicIds, $sortInfo);
            cache($cacheName, $topics, 300, 'topicListCache');
        }

        $this->assign([
            'topics' => $topics,
            'user' => session('user'),
            'page' => $pageInfo['page'],
            'tagId' => $tagId,
            'pageNum' => $pageInfo['pageNum'],
            'showPages' => $pageInfo['showPages'],
            'hotTags' => TopicTagModel::getHotTags(config('hotTagNum')),
            'field' => $field,
            'order' => $order,
        ]);
        echo $this->fetch('index');
    }

    public function newTopic()
    {
        if (request()->isPost()) {
            $postData = input('post.');
            $user = session('user');
            $topicId = input('get.topicId');
            if (!$topicId) {
                $topic = new TopicModel();
            } else {
                $topic = TopicModel::find($topicId);
            }
            $topic->title = $postData['title'];
            $topic->category_id = $postData['category_id'];
            $topic->content = $postData['content'];
            $topic->user_id = $user->id;
            $topic->created_at = intval(microtime(true));
            $topic->save();
            // 标签处理
            $tags = $postData['tags'];
            TopicTagModel::where(['topic_id' => $topic->id])->delete();
            foreach ($tags as $tag) {
                if (is_numeric($tag)) {
                    $this->createTopicTag($tag, $topic->id);
                    continue;
                }
                $newTag = $this->createTag($tag);
                $this->createTopicTag($newTag->id, $topic->id);
            }
            $message = $topicId ? '编辑帖子成功！' : '发表帖子成功！';
            if (!$topicId) {
                $topicId = $topic->id;
            }
            Cache::clear('topicListCache');
//            return $this->success($message, 'topic/detail');
            return $this->success($message, url('topic/detail', ['id' => $topicId]));
        }
        $tags = TagModel::all();
        $this->assign([
            'user' => session('user'),
            'category' => config('category'),
            'tags' => $tags
        ]);
        echo $this->fetch('new_topic');
    }

    //////////////////////////////////
    public function editTopic()
    {
        $user = session('user');
        $topicId = input('get.id');
        $topic = TopicModel::getTopic($topicId);
        if (!isset($user) || ($user->id !== $topic->user_id)) {
            $this->error('您没有登录或者没有权限编辑该帖子！');
        }
        $topicTags = TopicTagModel::where('topic_id', $topicId)->select();
        $selectTags = [];
        foreach ($topicTags as $topicTag) {
            $selectTags[] = $topicTag->tag_id;
        }
        $tags = TagModel::all();
        $this->assign([
            'user' => $user,
            'topic' => $topic,
            'category' => config('category'),
            'tags' => $tags,
            'selectTags' => $selectTags,
        ]);
        echo $this->fetch('edit_topic');
    }

    public function detail()
    {
        $topicId = input('get.id');
        $topic = TopicModel::getTopic($topicId);
        $this->assign([
            'user' => session('user'),
            'topic' => $topic,
            'replies' => ReplyModel::where(['topic_id' => $topicId])->order('created_at','DESC')->select(),
            'topicTags' => TopicTagModel::getTagTopicByTopicId($topic->id),
            'categoryNames' => getCategoryNames($topic->category_id),
        ]);
        echo $this->fetch('detail');
    }

    public function praise()
    {
        $user = session('user');
        if (!$user) {
            return;
        }
        $topicId = input('get.topicId');
        $praise = PraiseModel::get(['topic_id' => $topicId, 'user_id' => $user->id]);
        if ($praise) {
            $praise->delete();
        } else {
            $praise = new PraiseModel([
                'topic_id' => $topicId,
                'user_id' => $user->id,
                'created_at' => intval(microtime(true)),
            ]);
            $praise->save();
        }
    }

    public function editPost()
    {
        $topicId = input('get.topicId');
        $topicTags = TopicTagModel::where(['topic_id' => $topicId])->select();
        $selectTags = [];
        foreach ($topicTags as $topicTag) {
            $selectTags[] = $topicTag->tag_id;
        }
        $this->assign([
            'user' => session('user'),
            'topic' => TopicModel::get($topicId),
            'category' => config('category'),
            'tags' => TagModel::all(),
            'selectTags' => $selectTags,
        ]);
        echo $this->fetch('edit_topic');
    }

    private function createTopicTag($tagId, $topicId)
    {
        $topicTag = new TopicTagModel();
        $topicTag->tag_id = $tagId;
        $topicTag->topic_id = $topicId;
        $topicTag->save();
    }

    private function createTag($tagName)
    {
        $newTag = new TagModel();
        $newTag->name = $tagName;
        $newTag->save();
        return $newTag;
    }
}
