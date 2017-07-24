<?php
namespace app\admin\model;
use think\Db;
use think\Model;
class Article extends Model {

    /**
     * 根据page参数显示记录每页10条
     * @param $page
     * @return array
     */
    public static function ShowArticlesByPage($page){
        $startPage = ($page-1)*10;
        $result = Db::table('articles')
            ->order('id','desc')
            ->limit($startPage,10)
            ->select();
        return $result;
    }


    /**
     * 获取文章总数
     * @return int|string
     */
    public static function GetTotal()
    {
        $num = Db::table('articles')
            ->count();
        return $num;
    }

    /**
     * 添加新文章，向articles表中添加记录
     * @param $title
     * @param $date
     * @param $sourceName
     * @param $sourceNum
     * @param $url
     * @param $thumbnails
     * @param $type
     * @param $content
     * @return int|string
     */
    public static function addArticle($title,$date,$sourceName,$sourceNum,$url,$thumbnails,$type,$content){

        $data =
            [   'title' => $title,
                'date' => $date,
                'sourceName' => $sourceName,
                'sourceNum' => $sourceNum,
                'url' => $url,
                'thumbnails' => $thumbnails,
                'type' => $type,
                'content' => $content,
            ];
        $result = Db::table('articles')
            ->insert($data);
        return $result;
    }

    /**
     * 根据文章id获得这篇文章的thumbnails字段
     * @param $id
     * @return array
     */
    public static function  getArticleById($id){

        $result = Db::table('articles')
            ->where('id' ,$id)
            ->select();
        return $result;
    }

    /**
     * 根据id删除文章
     * @param $id
     * @return int
     */
    public static function delArticle($id){

        $bool = Db::table('articles')
            ->where('id',$id)
            ->delete();
        return $bool;

    }

    /**
     * 根据文章id获得该文章的缩略图路径
     * @param $id
     * @return false|\PDOStatement|string|\think\Collection
     */
    public static function GetThumbnailsPath($id){
        $result = Db::table('articles')
            ->field('thumbnails')
            ->where('id',$id)
            ->select();
        return $result;
    }

    /**
     * @param $id
     * @param $content
     * @return int|string
     */
    public static function updateContentById($id,$content){
        $result = Db::table('articles')
            ->where('id',$id)
            ->update(['content'=>$content]);
        return $result;
    }
}